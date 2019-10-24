<?php
class Response
{
    public $response_code;
    public $data;

    public function __construct($response_code, $data)
    {
        $this->response_code = $response_code;
        $this->data = $data;
    }
}

class Controller
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function index()
    {
        echo 'You have not implemented index yet!';
    }

    protected function render($response_code, $data)
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $response = new Response($response_code, $data);
        echo json_encode($response);
    }

    protected function render_view($view_name)
    {
        $view_filepath = root().'/web/view/'.$view_name.'_view.php';
        if (file_exists($view_filepath)) {
            require_once($view_filepath);
            $view_name = $view_name.'View';
            $view = new $view_name();
        } else {
            echo 'View not found!';
        }
    }
}
