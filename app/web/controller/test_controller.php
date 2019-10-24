<?php
require_once('controller_base.php');
class TestController extends Controller
{
    public function index()
    {
        parent::render_view('test');
    }
}
