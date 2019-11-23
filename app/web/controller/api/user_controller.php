<?php
require_once(dirname(__DIR__).'/controller_base.php');
class UserController extends Controller
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(root().'/web/model/user.php');
            $users = User::get_all();
            parent::render(200, $users);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_login()
    {
        if (!isset($_POST['email'])) {
            throw new Exception('Email required!');
        }
        if (!isset($_POST['pass'])) {
            throw new Exception('Password required!');
        }
        return [
            'email' => $_POST['email'],
            'pass' => $_POST['pass']
        ];
    }

    private function normalize_google_login() 
    {
        if (!isset($_POST['username'])) {
            throw new Exception('Unable to get username!');
        }
        if (!isset($_POST['email'])) {
            throw new Exception('Unable to get email!');
        }
        if (!isset($_POST['image'])) {
            throw new Exception('Unable to get image!');
        }
        return [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'pass' => $_POST['pass'],
            'phone' => $_POST['phone'],
            'image' => $_POST['image']
        ];
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(root().'/web/model/user.php');
            $normalized_params = [];

            try {
                $normalized_params = $this->normalize_login();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = User::get_by('email', $normalized_params['email']);
            if (count($user) != 1) {
                parent::render(401, 'User not found!');
                return;
            }

            $user = $user[0];

            if ($user->pass != $normalized_params['pass']) {
                parent::render(401, 'Wrong password!');
                return;
            }
            $user->login_hash = password_hash($user->pass, PASSWORD_DEFAULT);
            if ($user->update() === true) {
                $response_data = [
                    'message' => 'Login success!',
                    'username' => $user->username,
                    'login_hash' => $user->login_hash
                ];
                parent::render(200, $response_data);
            } else {
                parent::render(502, 'Some error occured.');
            }
        } else {
            throw new Exception('404');
        }
    }

    public function googleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(root().'/web/model/user.php');
            $normalized_params = [];

            try {
                $normalized_params = $this->normalize_login();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = User::get_by('email', $normalized_params['email']);
            if (count($user) != 1) {
                // Redirect to auto register a google account
                $user = new User();
                $user->username = $_POST['username'];
                $user->email = $_POST['email'];
                $user->pass = $_POST['pass'];
                $user->phone_number = $_POST['phone_number'];
                $user->profile_picture_url = $_POST['image'];
                $user->login_hash = password_hash($user->pass, PASSWORD_DEFAULT);

                if ($user->save() === true) {
                    $response_data = [
                        'message' => 'Google Account Registered on Engima',
                        'username' => $user->username,
                        'login_hash' => $user->login_hash
                    ];
                    parent::render(200, $response_data);
                    return;
                } else {
                    parent::render(502, 'Not saved. Some error occured.');
                    return;
                }
            }

            // If user have google account registered on Engima
            $user = $user[0];

            $user->login_hash = password_hash($user->pass, PASSWORD_DEFAULT);
            if ($user->update() === true) {
                $response_data = [
                    'message' => 'Google Sign In Success!',
                    'username' => $user->username,
                    'login_hash' => $user->login_hash
                ];
                parent::render(200, $response_data);
            } else {
                parent::render(502, 'Some error occured.');
            }
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_get_user()
    {
        if (!isset($_POST['username']) && !isset($_POST['email']) && !isset($_POST['phone_number'])) {
            throw new Exception('At least either username, email, or phone_number needed!');
        }
        if (isset($_POST['username'])) {
            return ['username' => $_POST['username']];
        }
        if (isset($_POST['email'])) {
            return ['email' => $_POST['email']];
        }
        if (isset($_POST['phone_number'])) {
            return ['phone_number' => $_POST['phone_number']];
        }
    }

    public function get_user()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            $normalized_params = [];

            try {
                $normalized_params = $this->normalize_get_user();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = null;
            if (array_key_exists('username', $normalized_params)) {
                $user = User::get_by('username', $normalized_params['username'])[0];
            } elseif (array_key_exists('email', $normalized_params)) {
                $user = User::get_by('email', $normalized_params['email'])[0];
            } elseif (array_key_exists('phone_number', $normalized_params)) {
                $user = User::get_by('phone_number', $normalized_params['phone_number'])[0];
            }
            parent::render(200, $user);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_register()
    {
        if (!isset($_POST['username'])) {
            throw new Exception('Username needed!');
        } elseif (count(User::get_by('username', $_POST['username'])) > 0) {
            throw new Exception('Username aleady exist!');
        }
        if (!isset($_POST['email'])) {
            throw new Exception('Email needed!');
        } elseif (count(User::get_by('email', $_POST['email'])) > 0) {
            throw new Exception('Email aleady exist!');
        }
        if (!isset($_POST['pass'])) {
            throw new Exception('Password needed!');
        }
        if (!isset($_POST['phone_number'])) {
            throw new Exception('Phone number needed!');
        } elseif (count(User::get_by('phone_number', $_POST['phone_number'])) > 0) {
            throw new Exception('Phone number aleady exist!');
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(dirname(dirname(__DIR__)).'/model/user.php');

            try {
                $this->normalize_register();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = new User();
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->pass = $_POST['pass'];
            $user->phone_number = $_POST['phone_number'];

            if (isset($_FILES['image']['name'])) {
                if (!is_dir(root().'/img/user')) {
                    mkdir(root().'/img/user');
                }
                if (!move_uploaded_file($_FILES['image']['tmp_name'], root().'/img/user/'.$user->username.'.jpg')) {
                    parent::render(502, 'Not saved. Image upload error occured.');
                    return;
                }

                $user->profile_picture_url = 'http://'.getenv('ADDRESS').'/img/user/'.$user->username.'.jpg';
            }

            $user->login_hash = password_hash($user->pass, PASSWORD_DEFAULT);

            if ($user->save() === true) {
                $response_data = [
                    'message' => 'Registered successfully.',
                    'username' => $user->username,
                    'login_hash' => $user->login_hash
                ];
                parent::render(200, $response_data);
            } else {
                parent::render(502, 'Not saved. Some error occured.');
            }
        } else {
            throw new Exception('404');
        }
    }
}
