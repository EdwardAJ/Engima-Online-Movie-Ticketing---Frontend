<?php
require_once('controller_base.php');
class DetailController extends Controller
{
    public function index()
    {
        parent::render_view('detail');
    }
}
