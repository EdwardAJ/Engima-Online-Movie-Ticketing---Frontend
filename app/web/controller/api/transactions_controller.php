<?php
require_once(dirname(__DIR__).'/controller_base.php');
class TransactionsController extends Controller
{
    public function normalize_buy()
    {
        if (!isset($_POST['username'])) {
            throw new Exception('Parameter username required!');
        }
        if (!isset($_POST['showing_id'])) {
            throw new Exception('Parameter showing_id required!');
        }
        if (!isset($_POST['seat_id'])) {
            throw new Exception('Parameter seat_id required!');
        }

        return [
            'username' => $_POST['username'],
            'showing_id' => $_POST['showing_id'],
            'seat_id' => $_POST['seat_id'] - 1
        ];
    }

    public function buy()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(dirname(dirname(__DIR__)).'/model/transaction.php');
            require_once(dirname(dirname(__DIR__)).'/model/screening.php');
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_buy();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = User::get_by('username', $normalized_params['username']);
            if (count($user) != 1) {
                parent::render(401, 'User not found!');
                return;
            }
            $user = $user[0];

            $screening = Screening::get_by('id', $normalized_params['showing_id']);
            if (count($screening) != 1) {
                parent::render(401, 'Screening not found!');
                return;
            }
            $screening = $screening[0];
            $screening->seats = decbin($screening->seats);
            
            while (strlen($screening->seats) < 30) {
                $screening->seats = '0'.$screening->seats;
            }

            if ($screening->seats[$normalized_params['seat_id']] == '1') {
                parent::render(401, 'Seat is already occupied! Failed to buy.');
                return;
            }

            $screening->seats[$normalized_params['seat_id']] = '1';
            $screening->seats = bindec($screening->seats);

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->screening_id = $screening->id;

            $virtual_account_number = $this->create_virtual_account();
            $this->add_transaction($user, $screening, $normalized_params['seat_id'], $virtual_account_number);

            if ($transaction->save() === true && $screening->update() === true) {
                parent::render(200, [
                    'message' => 'Successfully bought!',
                    'virtual_account_number' => $virtual_account_number,
                ]);
            } else {
                parent::render(401, [
                    'message' => 'Buy process failed!',
                ]);
            }
        } else {
            throw new Exception('404');
        }
    }

    private function create_virtual_account()
    {
        // $urlWSBank = getenv('WSBANK_API_URL').':'.getenv('WSBANK_API_PORT').'/wsbank/generate?wsdl';
        $url_WSBank = 'http://'.getenv('HOST_IP').':'.getenv('WSBANK_API_PORT').'/wsbank/generate?wsdl';

        $soap_client = new SoapClient($url_WSBank);
        $result = $soap_client->generateVirtualAccount(['accountNumber' => '13517000']);

        return $result->return;
    }

    private function add_transaction($user, $screening, $seat_id, $virtual_account_number)
    {
        $url_WSTransaksi = 'http://'.getenv('HOST_IP').':'.getenv('WSTRANSAKSI_API_PORT').'/addPendingTransaction';

        $json_content = http_build_query(
            array (
                'user_id' => $user->id,
                'film_id' => $screening->movie_id,
                'screening_id' => $screening->id,
                'seat' => $seat_id,
                'dest_va' => $virtual_account_number,
            )
        );

        $opt = array(
            'http' => array(
                'method' => 'POST',
                'headers' => 'Content-Type: application/json',
                'content' => $json_content,
            )
        );

        $context = stream_context_create($opt);
        $response = file_get_contents($url_WSTransaksi, false, $context);

        return json_decode($response, false);
    }

    private function normalize_get()
    {
        if (!isset($_GET['user_id']) && !isset($_GET['username'])) {
            throw new Exception('Parameter user_id or username required!');
        }

        return [
            'user_id' => $_GET['user_id'],
            'username' => $_GET['username']
        ];
    }

    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/transaction.php');
            require_once(dirname(dirname(__DIR__)).'/model/screening.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_get();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $get_by_param = $normalized_params['user_id'] != null ? 'user_id' : 'user_name';
            $get_by_param_value = $normalized_params['user_id'] != null ? $normalized_params['user_id'] : $normalized_params['username'];
            
            $user = null;
            try {
                if ($get_by_param == 'user_id') {
                    $user = User::get_by('id', $get_by_param_value);
                    if (count($user) != 1) {
                        throw new Exception('User not found!');
                    }
                } else {
                    $user = User::get_by('username', $get_by_param_value);
                    if (count($user) != 1) {
                        throw new Exception('User not found!');
                    }
                }
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }
            
            $user = $user[0];
            $response_data = [];
            // $transactions = Transaction::get_by('user_id', $user->id);
            $dataFromWSTransaksi = $this->get_transactions_from_WSBank($user->id);
            $transactions = $dataFromWSTransaksi->values;

            foreach ($transactions as $transaction) {
                $screening = Screening::get_by('id', $transaction->screening_id)[0];
                if ($screening == null) {
                    parent::render(401, 'Some error occured.');
                    return;
                }
                $movie = $this->get_movie_from_MovieDB($screening->movie_id);
                $reviews = Review::get_by('user_id', $user->id);
                $is_reviewed = false;
                foreach ($reviews as $review) {
                    if ($review->movie_id == $movie->id) {
                        $is_reviewed = true;
                        break;
                    }
                }

                $transaction_data = [
                    'id' => $transaction->id,
                    'movie' => $movie,
                    'screening' => $screening,
                    'is_reviewed' => $is_reviewed,
                    'virtual_account_number' => $transaction->virtual_account_number,
                    'flag' => $transaction->flag->data[0],
                ];

                array_push($response_data, $transaction_data);
            }
            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function get_transactions_from_WSBank($user_id)
    {
        $url_WSTransaksi = 'http://'.getenv('HOST_IP').':'.getenv('WSTRANSAKSI_API_PORT').'/getAllTransactions';

        $requestBody = http_build_query(
            array (
                'user_id' => $user_id,
            )
        );

        $opt = array(
            'http' => array(
                'method' => 'GET',
                'headers' => 'Content-Type: application/json',
                'content' => $requestBody,
            )
        );

        $context = stream_context_create($opt);
        $response = file_get_contents($url_WSTransaksi, false, $context);

        return json_decode($response, false);
    }

    private function get_movie_from_MovieDB($movie_id)
    {
        $url = 'https://api.themoviedb.org/3/movie/'.$movie_id.'?api_key=031993ac58e3af3fa22429862b57c580';

        $opt = array(
            'http' => array(
                'method' => 'GET',
            )
        );

        $context = stream_context_create($opt);
        $response = file_get_contents($url, false, $context);

        return json_decode($response, false);
    }
}
