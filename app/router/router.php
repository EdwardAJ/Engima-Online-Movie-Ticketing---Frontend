<?php
function root()
{
    return dirname(__DIR__);
}

function Extract_url($url)
{
    $url = substr($url, -1) == '/' ? substr($url, 0, -1) : $url;
    $url_parts = explode('/', $url);
    $url_parts[count($url_parts) - 1] = strpos(end($url_parts), '?') ? substr(end($url_parts), 0, strpos(end($url_parts), '?')) : end($url_parts);
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

function Handle_routing($extracted_url)
{
    ob_start();
    try {
        if (count($extracted_url) > 3) {
            throw new Exception('404');
        }

        $controller_name = $extracted_url[1] != null ? $extracted_url[1] : 'home';
        if ((!Is_API_request($extracted_url[0]) && !Is_Controller_valid($controller_name)) || (Is_API_request($extracted_url[0]) && !Is_API_Controller_valid($controller_name))) {
            throw new Exception('404');
        }
        
        $controller = Create_controller($controller_name, Is_API_request($extracted_url[0]));
        $action_function = end($extracted_url) == $extracted_url[2] ? $extracted_url[2] : 'index';

        if (!Is_Action_valid($controller, $action_function)) {
            throw new Exception('404');
        }

        if (!Is_API_request($extracted_url[0]) && (!isset($_COOKIE['LOGIN_HASH']) || (!Is_Login_Hash_valid($_COOKIE['LOGIN_HASH']))) && $controller_name != 'login' && $controller_name != 'register') {
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
