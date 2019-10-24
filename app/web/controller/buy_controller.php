<?php
require_once('controller_base.php');
class BuyController extends Controller
{
    public function index()
    {
        parent::render_view('buy');
    }
}
