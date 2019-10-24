<?php
require_once('controller_base.php');
class HomeController extends Controller
{
    public function index()
    {
        parent::render_view('home');
    }
}
