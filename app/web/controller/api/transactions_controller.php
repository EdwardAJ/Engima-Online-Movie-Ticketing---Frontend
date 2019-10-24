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

            if ($transaction->save() === true && $screening->update() === true) {
                parent::render(200, 'Successfully bought!');
            } else {
                parent::render(401, 'Buy process failed!');
            }
        } else {
            throw new Exception('404');
        }
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
            $transactions = Transaction::get_by('user_id', $user->id);
            foreach ($transactions as $transaction) {
                $screening = Screening::get_by('id', $transaction->screening_id)[0];
                if ($screening == null) {
                    parent::render(401, 'Some error occured.');
                    return;
                }
                $movie = Movie::get_by('id', $screening->movie_id)[0];
                if ($movie == null) {
                    parent::render(401, 'Some error occured.');
                    return;
                }
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
                    'is_reviewed' => $is_reviewed
                ];

                array_push($response_data, $transaction_data);
            }
            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }
}
