<?php
require_once('model_base.php');
class Movie extends Model
{
    public $id;
    public $title;
    public $description;
    public $duration;
    public $movie_picture_url;
    public $release_date;
}
