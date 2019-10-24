function setMovieName() {
    document.getElementById('movie_name').innerHTML = getParameterValue(document.location, 'movie_name');
}

function createHTMLforMovie(movie) {
    html_string =
        `
    <div class="search-item-container">
        <div class="search-item-grid">
            <div class="movie-image-result">
                <img class="movie-image" src="` + movie.movie_picture_url + `"/>
            </div>
            <div class="about-movie">
                <p class="movie-title">` + movie.title + `</p>
                <div class="star-rating">
                    <img class="star-icon" src="../img/movies/star.png"/>` + String(movie.score) + `
                </div>
                <p class="movie-description">` + movie.description + `</p>
            </div>
            <div class="details-button">
                <a class="blue-text" href="/detail?movie_id=` + String(movie.id) + `">View details</a>
            </div>		
        </div>
    </div>
    `;

    return html_string;
}

function getPageCount(movie_count) {
    return Math.ceil(movie_count / 5);
}

function createHTMLforFooter(movie_count) {
    html_string =
        `
    <div class="search-footer-button hoverable" id="back-button" onclick="showPage(this, ` + String(Math.max(currentPage - 1, 1)) + `)">
        Back
    </div>
    `;

    for (i = 0; i < getPageCount(movie_count); i++) {
        html_string +=
            `
        <div class="footer-box hoverable" id=page-button-` + String(i + 1) + ` onclick="showPage(this, ` + String(i + 1) + `)">
            ` + String(i + 1) + `
        </div>
        `;
    }

    html_string +=
        `
    <div class="search-footer-button hoverable" id="next-button" onclick="showPage(this, ` + String(Math.min(currentPage + 1, getPageCount(movie_count))) + `)">
        Next
    </div>
    `;

    return html_string;
}

function clearPage() {
    document.getElementById('search_grid').innerHTML = '';
    document.getElementById('search_footer').innerHTML = '';
}

function showPage(pageElement, pageNumber) {
    currentPage = pageNumber;
    clickedButtonID = pageElement.id;
    clearPage();
    document.getElementById('search_footer').innerHTML = createHTMLforFooter(movies.length);
    document.getElementById(clickedButtonID).setAttribute('style', 'color: grey; border-color: grey;');
    document.getElementById('page-button-' + String(currentPage)).setAttribute('style', 'color: grey; border-color: grey;');
    for (i = 0; i < 5; i++) {
        if (movies[i + ((pageNumber - 1) * 5)] == null) {
            break;
        }
        document.getElementById('search_grid').insertAdjacentHTML('beforeend', createHTMLforMovie(movies[i + ((pageNumber - 1) * 5)]));
    }
}

function movieSearchCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        movies = response.data;
        document.getElementById('result_count').innerHTML = movies.length;
        document.getElementById('search_footer').innerHTML = createHTMLforFooter(movies.length);
        showPage(document.getElementById('page-button-1'), 1);
    }
}

function getMovies(movie_name) {
    sendRequest('GET', getAPIDomain() + '/movies/search?movie_name=' + movie_name, null, movieSearchCallback);
}

movies = null;
currentPage = 1;

setElementHeights();

setMovieName();
getMovies(getParameterValue(document.location, 'movie_name'));