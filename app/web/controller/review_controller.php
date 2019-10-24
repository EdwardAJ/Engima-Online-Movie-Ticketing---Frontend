<?php
require_once('controller_base.php');
class ReviewController extends Controller
{
    public function index()
    {
        parent::render_view('review');
    }
}
