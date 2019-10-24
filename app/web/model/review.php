<?php
require_once('model_base.php');
class Review extends Model
{
    public $id;
    public $user_id;
    public $movie_id;
    public $rating;
    public $content;
}
