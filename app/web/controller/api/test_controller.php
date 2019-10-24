<?php
require_once(dirname(__DIR__).'/controller_base.php');
class TestController extends Controller
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/movie_genre.php');
            $movie_genres = Movie_Genre::get_all();
            parent::render(200, $movie_genres);
        } else {
            throw new Exception('404');
        }
    }
}
