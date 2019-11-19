function setUsernameText() {
    document.getElementById('username_text').innerHTML = getCookie('USERNAME');
}

function createHTMLforMovie(movie) {
    html_string =
        `
    <a href="/detail?movie_id=` + String(movie.id) + `">
        <div class="movie-item hoverable">
            <img class="movie-image" src="`+ getImageURLMovieDB() + movie.poster_path + `"/>
            <p class="movie-title">` + movie.original_title + `</p>
            <div class="star-rating">
                <img class="star-icon" src="../img/movies/star.png"/>` + movie.vote_average + `
            </div>
        </div>
    </a>
    `;

    return html_string;
}

function showCurrentMovies(response) {
    response = JSON.parse(response);
    movies = response.results;
    for (i = 0; i < movies.length && i < 15; i++) {
        document.getElementById('movie_grid').insertAdjacentHTML('beforeend', createHTMLforMovie(movies[i]));
    }
}

function getCurrentMovies() {
    sendRequest('GET', 'https://api.themoviedb.org/3/movie/now_playing', null, showCurrentMovies, true, getGeneralHeaderMovieDB());
}

setElementHeights();

setUsernameText();
getCurrentMovies();