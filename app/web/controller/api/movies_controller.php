<?php
require_once(dirname(__DIR__).'/controller_base.php');
class MoviesController extends Controller
{
    private function normalize_reviews()
    {
        if (!isset($_GET['movie_id'])) {
            throw new Exception('Parameter movie_id required!');
        }

        return [
            'movie_id' => $_GET['movie_id']
        ];
    }

    public function reviews()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_get();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];
            $reviews = Review::get_by('movie_id', $movie->id);

            $response_data = [];
            foreach ($reviews as $review) {
                $user = User::get_by('id', $review->user_id)[0];
                $data = [
                    'id' => $review->id,
                    'username' => $user->username,
                    'user_picture' => $user->profile_picture_url,
                    'movie_id' => $review->movie_id,
                    'rating' => $review->rating,
                    'content' => $review->content,
                ];

                array_push($response_data, $data);
            }

            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_get()
    {
        if (!isset($_GET['movie_id'])) {
            throw new Exception('Parameter movie_id required!');
        }

        return [
            'movie_id' => $_GET['movie_id']
        ];
    }

    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/movie_genre.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/genre.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_get();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];
            $reviews = Review::get_by('movie_id', $movie->id);

            $score = 0;
            foreach ($reviews as $review) {
                $score += $review->rating;
            }

            $score = count($reviews) > 0 ? $score / count($reviews) : $score;
            
            $movie_genres = [];
            $genres = Movie_Genre::get_by('movie_id', $movie->id);
            foreach ($genres as $genre) {
                $movie_genre = Genre::get_by('id', $genre->genre_id)[0];
                if ($movie_genre == null) {
                    parent::render(401, 'Error while getting genre!');
                    return;
                }
                array_push($movie_genres, $movie_genre->name);
            }

            $response_data = [
                'id' => $movie->id,
                'title' => $movie->title,
                'description' => $movie->description,
                'genres' => $movie_genres,
                'duration' => $movie->duration,
                'movie_picture_url' => $movie->movie_picture_url,
                'release_date' => $movie->release_date,
                'score' => $score
            ];

            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_search()
    {
        if (!isset($_GET['movie_name'])) {
            throw new Exception('Parameter movie_name required!');
        }

        return [
            'movie_name' => $_GET['movie_name']
        ];
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_search();
            } catch (Excaption $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $movies = Movie::like('title', $normalized_params['movie_name'].'%');
            $response_data = [];

            foreach ($movies as $movie) {
                $reviews = Review::get_by('movie_id', $movie->id);

                $score = 0;
                foreach ($reviews as $review) {
                    $score += $review->rating;
                }

                $score = count($reviews) > 0 ? $score / count($reviews) : $score;

                array_push($response_data, [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'description' => $movie->description,
                    'duration' => $movie->duration,
                    'movie_picture_url' => $movie->movie_picture_url,
                    'release_date' => $movie->release_date,
                    'score' => $score
                ]);
            }

            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private static function screening_is_relevant($screening)
    {
        return new DateTime($screening->show_time) >= new DateTime();
    }

    public function current()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/screening.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');

            $screenings = Screening::get_all();
            $movies = [];
            foreach ($screenings as $screening) {
                if (MoviesController::screening_is_relevant($screening)) {
                    $movie = Movie::get_by('id', $screening->movie_id);
                    if (count($movie) == 1) {
                        array_push($movies, $movie[0]);
                    }
                }
            }

            $response_data = [];
            foreach ($movies as $movie) {
                $reviews = Review::get_by('movie_id', $movie->id);

                $score = 0;
                foreach ($reviews as $review) {
                    $score += $review->rating;
                }

                $score = count($reviews) > 0 ? $score / count($reviews) : $score;

                if (!in_array(
                    [
                            'id' => $movie->id,
                            'title' => $movie->title,
                            'description' => $movie->description,
                            'duration' => $movie->duration,
                            'movie_picture_url' => $movie->movie_picture_url,
                            'release_date' => $movie->release_date,
                            'score' => $score
                        ],
                    $response_data
                )
                ) {
                    array_push($response_data, [
                        'id' => $movie->id,
                        'title' => $movie->title,
                        'description' => $movie->description,
                        'duration' => $movie->duration,
                        'movie_picture_url' => $movie->movie_picture_url,
                        'release_date' => $movie->release_date,
                        'score' => $score
                    ]);
                }
            }

            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_schedules()
    {
        if (!isset($_GET['movie_id'])) {
            throw new Exception('Parameter movie_id required!');
        }

        return [
            'movie_id' => $_GET['movie_id']
        ];
    }

    public function schedules()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/screening.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_schedules();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];
            $screenings = Screening::get_by('movie_id', $movie->id);

            $response_data = [];
            foreach ($screenings as $screening) {
                $seat_binary = decbin($screening->seats);
                while (strlen($seat_binary) < 30) {
                    $seat_binary = '0'.$seat_binary;
                }
                $data = [
                    'id' => $screening->id,
                    'movie_id' => $screening->movie_id,
                    'show_time' => $screening->show_time,
                    'price' => $screening->price,
                    'seats' => $seat_binary
                ];

                array_push($response_data, $data);
            }
            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_showing()
    {
        if (!isset($_GET['showing_id'])) {
            throw new Exception('Parameter showing_id required');
        }

        return [
            'showing_id' => $_GET['showing_id']
        ];
    }
    public function showing()
    {
        require_once(dirname(dirname(__DIR__)).'/model/screening.php');
        require_once(dirname(dirname(__DIR__)).'/model/movie.php');
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_showing();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $screening = Screening::get_by('id', $normalized_params['showing_id']);
            if (count($screening) != 1) {
                parent::render(401, 'Screening not found!');
                return;
            }

            $screening = $screening[0];
            $screening->seats = decbin($screening->seats);

            while (strlen($screening->seats) < 30) {
                $screening->seats = '0'.$screening->seats;
            }

            $movie = Movie::get_by('id', $screening->movie_id);
            if (count($movie) != 1) {
                $response_data = [
                    'message' => 'Screening found but invalid!',
                    'screening' => $screening,
                    'movie' => $movie[0]
                ];
                parent::render(401, $response_data);
                return;
            }
            
            $response_data = [
                'message' => 'Screening found!',
                'screening' => $screening,
                'movie' => $movie[0]
            ];
            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_score()
    {
        if (!isset($_GET['movie_id'])) {
            throw new Exception('Parameter movie_id required');
        }

        return [
            'movie_id' => $_GET['movie_id']
        ];
    }

    public function score()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_score();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];
            
            $reviews = Review::get_by('movie_id', $movie->id);
            if (count($reviews) < 1) {
                parent::render(401, 'No reviews yet!');
                return;
            }

            $score = 0;
            foreach ($reviews as $review) {
                $score += $review->rating;
            }

            $score = count($reviews) > 0 ? $score / count($reviews) : $score;

            $response_data = [
                'score' => $score
            ];
            parent::render(200, $response_data);
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_add_review()
    {
        if (!isset($_POST['user_id'])) {
            throw new Exception('Parameter user_id required!');
        }
        if (!isset($_POST['movie_id'])) {
            throw new Exception('Parameter movie_id required!');
        }
        if (!isset($_POST['score'])) {
            throw new Exception('Parameter score required!');
        }
        if (!isset($_POST['content'])) {
            throw new Exception('Parameter content required!');
        }

        return [
            'user_id' => $_POST['user_id'],
            'movie_id' => $_POST['movie_id'],
            'score' => $_POST['score'],
            'content' => $_POST['content']
        ];
    }

    public function add_review()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_add_review();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = User::get_by('id', $normalized_params['user_id']);
            if (count($user) != 1) {
                parent::render(401, 'User not found!');
                return;
            }

            $user = $user[0];

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];

            $review = new Review();
            $review->user_id = $user->id;
            $review->movie_id = $movie->id;
            $review->rating = $normalized_params['score'];
            $review->content = $normalized_params['content'];

            $past_reviews = Review::get_by('user_id', $review->user_id);
            $review_to_delete = null;

            foreach ($past_reviews as $past_review) {
                if ($past_review->user_id == $review->user_id && $past_review->movie_id == $review->movie_id) {
                    $review_to_delete = $past_review;
                    break;
                }
            }

            if ($review_to_delete != null) {
                if (Review::delete('id', $review_to_delete->id) !== true) {
                    parent::render(401, 'Review not edited, some error occured!');
                    return;
                }
            }

            if ($review->save() === true) {
                parent::render(200, 'Review edited successfully!');
            } else {
                parent::render(401, 'Review not saved, some error occured!');
            }
        } else {
            throw new Exception('404');
        }
    }

    private function normalize_remove_review()
    {
        if (!isset($_POST['username'])) {
            throw new Exception('Parameter username required!');
        }
        if (!isset($_POST['movie_id'])) {
            throw new Exception('Parameter movie_id required!');
        }

        return [
            'username' => $_POST['username'],
            'movie_id' => $_POST['movie_id'],
        ];
    }

    public function remove_review()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once(dirname(dirname(__DIR__)).'/model/user.php');
            require_once(dirname(dirname(__DIR__)).'/model/movie.php');
            require_once(dirname(dirname(__DIR__)).'/model/review.php');
            $normalized_params = null;
            try {
                $normalized_params = $this->normalize_remove_review();
            } catch (Exception $e) {
                parent::render(401, $e->getMessage());
                return;
            }

            $user = User::get_by('username', $normalized_params['username']);
            if (count($user) != 1) {
                parent::render(401, 'User not found!');
                return;
            }

            $user = $user[0];

            $movie = Movie::get_by('id', $normalized_params['movie_id']);
            if (count($movie) != 1) {
                parent::render(401, 'Movie not found!');
                return;
            }

            $movie = $movie[0];

            $reviews = Review::get_by('user_id', $user->id);
            ;
            $review_to_delete = null;
            foreach ($reviews as $review) {
                if ($review->movie_id == $movie->id) {
                    $review_to_delete = $review;
                    break;
                }
            }

            if (Review::delete('id', $review_to_delete->id) === true) {
                parent::render(200, 'Review deleted successfully!');
            } else {
                parent::render(401, 'Review not deleted, some error occured!');
            }
        } else {
            throw new Exception('404');
        }
    }
}
