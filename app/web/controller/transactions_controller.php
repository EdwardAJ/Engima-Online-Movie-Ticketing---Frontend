<?php
require_once('controller_base.php');
class TransactionsController extends Controller
{
    public function index()
    {
        parent::render_view('transactions');
    }
}
