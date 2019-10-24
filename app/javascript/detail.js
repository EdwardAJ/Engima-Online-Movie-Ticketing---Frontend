function createGenreText(movie) {
    genreText = '';
    for (i = 0; i < movie.genres.length; i++) {
        if (i > 0) {
            genreText += ', ';
        }
        genreText += movie.genres[i];
    }

    return genreText;
}

function setMovieDetails(movie) {
    document.getElementById('movie-img').src = movie.movie_picture_url;
    document.getElementById('movie-title').innerHTML = movie.title;
    document.getElementById('movie-genres').innerHTML = createGenreText(movie);
    document.getElementById('movie-duration').innerHTML = movie.duration;
    document.getElementById('movie-release').innerHTML = 'Released date: ' + movie.release_date;
    document.getElementById('movie-rating').innerHTML = movie.score;
    document.getElementById('movie-description-text').innerHTML = movie.description;
}

function getMovieDetailCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        setMovieDetails(response.data);
    }
}

function getMovieDetail() {
    sendRequest('GET', getAPIDomain() + '/movies/get?movie_id=' + getParameterValue(location, 'movie_id'), null, getMovieDetailCallback, false);
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


function setMovieReviews(reviews) {
    for (i = 0; i < reviews.length && i < 3; i++) {
        document.getElementById('reviews-item-container').insertAdjacentHTML('beforeend', createHTMLforReview(reviews[i]));
    }
}

function getMovieReviewsCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        setMovieReviews(response.data);
    }
}

function getMovieReviews() {
    sendRequest('GET', getAPIDomain() + '/movies/reviews?movie_id=' + getParameterValue(location, 'movie_id'), null, getMovieReviewsCallback, false);
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
    sendRequest('GET', getAPIDomain() + '/movies/schedules?movie_id=' + getParameterValue(location, 'movie_id'), null, getMovieScheduleCallback, false);
}

setElementHeights('body-container');

getMovieDetail();
getMovieReviews();
getMovieSchedule();