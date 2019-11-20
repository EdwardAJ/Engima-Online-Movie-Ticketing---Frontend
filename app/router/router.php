<?php
function root()
{
    return dirname(__DIR__);
}

function Extract_url($url)
{
    $url = substr($url, -1) == '/' ? substr($url, 0, -1) : $url;
    $url_parts = explode('/', $url);
    if (strpos(end($url_parts), '?')) {
        $url_parts[count($url_parts) - 1] = substr(end($url_parts), 0, strpos(end($url_parts), '?'));
    } else {
        $url_parts[count($url_parts) - 1] = end($url_parts);
    }
    return $url_parts;
}

function Not_found()
{
    echo '404 Not Found';
}

function Is_API_request($url)
{
    return substr($url, 0, 4) == 'api.';
}

function Is_Controller_valid($controller_name)
{
    return file_exists(root().'/web/controller/'.strtolower($controller_name).'_controller.php');
}

function Is_API_Controller_valid($controller_name)
{
    return file_exists(root().'/web/controller/api/'.strtolower($controller_name).'_controller.php');
}

function Is_Action_valid($controller, $action_name)
{
    return method_exists($controller, $action_name) && is_callable(array($controller, $action_name));
}

function Create_controller($controller_name, $is_api = false)
{
    if ($is_api) {
        include_once root().'/web/controller/api/'.strtolower($controller_name).'_controller.php';
    } else {
        include_once root().'/web/controller/'.strtolower($controller_name).'_controller.php';
    }
    $controller_class_name = $controller_name.'Controller';
    return new $controller_class_name();
}

function Is_Login_Hash_valid($login_hash)
{
    include_once root().'/web/model/user.php';
    return count(User::get_by('login_hash', $login_hash)) == 1;
}

function Request_Invalid($extracted_url, $controller_name)
{
    if (!Is_API_request($extracted_url[0]) && !Is_Controller_valid($controller_name)) {
        return true;
    }

    if (Is_API_request($extracted_url[0]) && !Is_API_Controller_valid($controller_name)) {
        return true;
    }

    return false;
}

function Not_Outside($controller_name)
{
    return $controller_name != 'login' && $controller_name != 'register';
}

function Should_Redirect_To_Login($extracted_url, $controller_name)
{
    if (!Is_API_request($extracted_url[0]) && (!isset($_COOKIE['LOGIN_HASH']))) {
        return true;
    }

    var_dump(((!Is_Login_Hash_valid($_COOKIE['LOGIN_HASH']))) && Not_Outside($controller_name));
    if (((!Is_Login_Hash_valid($_COOKIE['LOGIN_HASH']))) && Not_Outside($controller_name)) {
        return true;
    }

    return false;
}

function Is_Logged_In()
{
    return isset($_COOKIE['LOGIN_HASH']) && Is_Login_Hash_valid($_COOKIE['LOGIN_HASH']);
}

function Is_From_Login_Or_Register($controller_name)
{
    return $controller_name == 'login' || $controller_name == 'register';
}

function Handle_routing($extracted_url)
{
    ob_start();
    try {
        if (count($extracted_url) > 3) {
            throw new Exception('404');
        }

        $controller_name = $extracted_url[1] != null ? $extracted_url[1] : 'home';
        if (Request_Invalid($extracted_url, $controller_name)) {
            throw new Exception('404');
        }
        
        $controller = Create_controller($controller_name, Is_API_request($extracted_url[0]));
        $action_function = end($extracted_url) == $extracted_url[2] ? $extracted_url[2] : 'index';

        if (!Is_Action_valid($controller, $action_function)) {
            throw new Exception('404');
        }

        if (!Is_API_request($extracted_url[0]) && !Is_Logged_In() && !Is_From_Login_Or_Register($controller_name)) {
            header("Location: /login");
            exit();
        }

        $controller->$action_function();
    } catch (Exception $e) {
        if ($e->getMessage() == '404') {
            not_found();
        } else {
            echo $e->getMessage();
        }
    }
    ob_end_flush();
}

$extracted_url = Extract_url($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
Handle_routing($extracted_url);
