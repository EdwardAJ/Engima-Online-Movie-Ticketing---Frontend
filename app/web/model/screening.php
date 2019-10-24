<?php
require_once('model_base.php');
class Screening extends Model
{
    public $id;
    public $movie_id;
    public $show_time;
    public $price;
    public $seats;
}
