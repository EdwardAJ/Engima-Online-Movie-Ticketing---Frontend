<?php
require_once('controller_base.php');
class LoginController extends Controller
{
    public function index()
    {
        parent::render_view('login');
    }
}
