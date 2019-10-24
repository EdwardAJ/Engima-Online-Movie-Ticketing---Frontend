function setUsernameText() {
    document.getElementById('username_text').innerHTML = getCookie('USERNAME');
}

function createHTMLforMovie(movie) {
    html_string =
        `
    <a href="/detail?movie_id=` + String(movie.id) + `">
        <div class="movie-item hoverable">
            <img class="movie-image" src="` + movie.movie_picture_url + `"/>
            <p class="movie-title">` + movie.title + `</p>
            <div class="star-rating">
                <img class="star-icon" src="../img/movies/star.png"/>` + movie.score + `
            </div>
        </div>
    </a>
    `;

    return html_string;
}

function showCurrentMovies(response) {
    response = JSON.parse(response);
    if (response.response_code == 200) {
        movies = response.data;
        for (i = 0; i < movies.length; i++) {
            document.getElementById('movie_grid').insertAdjacentHTML('beforeend', createHTMLforMovie(movies[i]));
        }
    }
}

function getCurrentMovies() {
    sendRequest('GET', getAPIDomain() + '/movies/current', null, showCurrentMovies);
}

setElementHeights();

setUsernameText();
getCurrentMovies();