setElementHeights('review-container');

function getMovieDetailCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        document.getElementById('movie-title').innerHTML = response.data.title;
        currentMovie = response.data;
    }
}

function getMovieDetail() {
    sendRequest('GET', getAPIDomain() + '/movies/get?movie_id=' + getParameterValue(window.location, 'movie_id'), null, getMovieDetailCallback);
}

function createHTMLforStars() {
    html_string = ``;

    id = 1;
    for (i = 0; i < currentRating; i++) {
        html_string += `<img src="../img/star.png" class="rating-star hoverable" id="star-` + String(id) + `" onclick="setRating(this)"/>`;
        id++;
    }

    for (i = currentRating; i < 10; i++) {
        html_string += `<img src="../img/star-dark.png" class="rating-star hoverable" id="star-` + String(id) + `" onclick="setRating(this)"/>`;
        id++;
    }

    return html_string;
}

function drawStars() {
    document.getElementById('rating-star-container').innerHTML = createHTMLforStars();
}

function setRating(starElement) {
    currentRating = starElement.id.replace('star-', '');
    drawStars();
}

function createReviewObject() {
    if (currentUser == null) {
        getUserData();
    }
    return {
        user_id: currentUser.id,
        movie_id: currentMovie.id,
        score: currentRating,
        content: document.getElementById('input-review-text').value
    }
}

function getUserData() {
    sendRequest(
        'POST',
        getAPIDomain() + '/user/get_user',
        createPayload({
            username: getCookie('USERNAME')
        }),
        function (response) {
            currentUser = JSON.parse(response).data;
        },
        false
    );
}

function addReviewCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        redirect('/transactions');
    } else {
        console.log('Error adding review! ');
        console.log(response.data);
    }
}

function addReview() {
    sendRequest('POST', getAPIDomain() + '/movies/add_review', createPayload(createReviewObject()), addReviewCallback);
}

currentMovie = null;
currentUser = null;
currentRating = 5;

getMovieDetail();
drawStars();