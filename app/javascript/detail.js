function createGenreText(movie) {
    genreText = '';
    for (i = 0; i < movie.genres.length; i++) {
        if (i > 0) {
            genreText += ', ';
        }
        genreText += movie.genres[i].name;
    }

    return genreText;
}

function setMovieDetails(movie) {
    document.getElementById('movie-img').src = getImageURLMovieDB() + movie.poster_path;
    document.getElementById('movie-title').innerHTML = movie.title;
    document.getElementById('movie-genres').innerHTML = createGenreText(movie);
    document.getElementById('movie-duration').innerHTML = '-';
    document.getElementById('movie-release').innerHTML = 'Released date: ' + movie.release_date;
    document.getElementById('movie-rating').innerHTML = movie.vote_average;
    document.getElementById('movie-description-text').innerHTML = movie.overview;
}

function setAverageRating(reviews) {
    var sum = 0;
    var minLength = reviews.length < 3 ? reviews.length : 3;
    if (minLength === 0) {
        minLength = 1;
    }
    for (i = 0; i < reviews.length && i < 3; i++) {
        sum += reviews[i].rating;
    }
    var averageRating = sum / minLength;
    document.getElementById('movie-rating-user').innerHTML = averageRating;
}

function getMovieDetailCallback(response) {
    response = JSON.parse(response);
    setMovieDetails(response);
}

function getMovieDetail() {
    sendRequest('GET', 'https://api.themoviedb.org/3/movie/' + getParameterValue(location, 'movie_id'), null, getMovieDetailCallback, false, getGeneralHeaderMovieDB());
}

function createHTMLforReview(review) {
    html_string =
        `
    <div class="reviews-item">
        <img class="reviews-img" src="` + review.user_picture + `" alt="photo"/>
        <div class="reviews-description">
            <p class="reviews-name">` + review.username + `</p>
            <p class="reviews-rating"><img class="rating-star" src="../img/movies/star.png"/> ` + review.rating + ` / 10</p>
            <p class="reviews-text">` + review.content + `</p>
        </div>
    </div>
    `;

    return html_string;
}

function createHTMLforDetailedReviewMovieDB(review) {
    html_string =
        `
    <div class="reviews-item">
        <div class="reviews-description">
            <p class="reviews-name"><b>` + review.author + `</b></p>
            <p class="reviews-text">` + review.content + `</p>
        </div>
    </div>
    `;

    return html_string;
}


function setMovieReviews(reviews) {
    console.log(reviews);
    for (i = 0; i < reviews.length && i < 3; i++) {
        document.getElementById('reviews-item-container').insertAdjacentHTML('beforeend', createHTMLforReview(reviews[i]));
    }
}

function setDetailedReviewsMovieDB(reviews) {
    console.log("Reviews: ", reviews);
    for (i = 0; i < reviews.length && i < 3; i++) {
        document.getElementById('reviews-item-container-critics').insertAdjacentHTML('beforeend', createHTMLforDetailedReviewMovieDB(reviews[i]));
    }
}

function getMovieReviewsCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        setAverageRating(response.data);
        setMovieReviews(response.data);
    }
}

function getDetailedReviewsMovieDBCallback (response) {
    response  = JSON.parse(response);
    console.log("response: ", response);
    setDetailedReviewsMovieDB(response.results);
}

function getMovieReviews() {
    sendRequest('GET', getAPIDomain() + '/movies/reviews?movie_id=' + getParameterValue(location, 'movie_id'), null, getMovieReviewsCallback, false);
}

function getDetailedReviewsMovieDB () {
    sendRequest('GET', 'https://api.themoviedb.org/3/movie/' + getParameterValue(location, 'movie_id') + '/reviews', null, getDetailedReviewsMovieDBCallback, false, getGeneralHeaderMovieDB());
}


function getEmptySeatNumber(binaryString) {
    while (binaryString.includes('1')) {
        binaryString = binaryString.replace('1', '');
    }
    return binaryString.length;
}

function isScreeningAvailable(schedule) {
    dateTime = Date.parse(schedule.show_time);
    return dateTime >= Date.now() && getEmptySeatNumber(schedule.seats) > 0;
}

function createHTMLforSchedule(schedule) {
    date = schedule.show_time.split(' ');
    time = date[1];
    date = date[0];

    seatNumber = getEmptySeatNumber(schedule.seats);

    html_string =
        `
    <div class="table-row">
        <div class="table-cell">` + date + `</div>
        <div class="table-cell ">` + time + `</div>
        <div class="table-cell seat-number">` + String(seatNumber) + ` seats</div>
    `;

    if (isScreeningAvailable(schedule)) {
        html_string += `<a class="table-cell availability available" href="/buy?showing_id=` + schedule.id + `">Book Now<img class="availability-mark" src="../img/rightmark.jpg"/></a>`;
    } else {
        html_string += `<div class="table-cell availability not-available">Not Available<img class="availability-mark" src="../img/xmark.png"/></div>`;
    }

    html_string += `</div>`;
    return html_string;
}

function setMovieSchedules(schedules) {
    for (i = 0; i < schedules.length; i++) {
        document.getElementById('table').insertAdjacentHTML('beforeend', createHTMLforSchedule(schedules[i]));
    }
}

function getMovieScheduleCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        setMovieSchedules(response.data);
    }
}

function getMovieSchedule() {
    var releaseDate = document.getElementById('movie-release').innerText.substring(15);
    sendRequest('GET', getAPIDomain() + '/movies/schedules?movie_id=' + getParameterValue(location, 'movie_id') + '&release_date=' + releaseDate, null, getMovieScheduleCallback, false);
}

setElementHeights('body-container');

getMovieDetail();
getMovieReviews();
getDetailedReviewsMovieDB();
getMovieSchedule();