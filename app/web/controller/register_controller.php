<?php
require_once('controller_base.php');
class RegisterController extends Controller
{
    public function index()
    {
        parent::render_view('register');
    }
}
