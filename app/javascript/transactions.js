setElementHeights('transactions-controller');

function createHTMLforTransactionButton(transaction) {
    html_string =
        `
    <div class="transactions-button-container">
    `;

    if (transaction.is_reviewed) {
        html_string +=
            `
            <label class="transactions-button transactions-button-delete hoverable" id="delete-` + String(transaction.movie.id) + `" onclick="deleteReview(this)">Delete</label>
            <a class="transactions-button transactions-button-edit hoverable" href="/review?movie_id=` + String(transaction.movie.id) + `">Edit</a>
        `;
    } else if (Date.parse(transaction.screening.show_time) <= Date.now()) {
        html_string +=
            `
            <a class="transactions-button transactions-button-add" href="/review?movie_id=` + String(transaction.movie.id) + `">Add Review</a>
        `;
    }

    html_string +=
        `
    </div>
    `;

    return html_string;
}

function createHTMLforTransaction(transaction) {
    html_string =
        `
    <div class="transactions-item">
        <img class="transactions-img" src="` + getImageURLMovieDB() + transaction.movie.poster_path + `" alt="img"/>
        <div class="transactions-description">
            <h2 class>` + transaction.movie.title + `</h2>
            <b class="blue-text">Schedule:</b>` + transaction.screening.show_time + `
        </div>
        ` + createHTMLforTransactionButton(transaction) + `
    </div>
    <hr>
    `;

    return html_string;
}

function getTransactionsCallback(response) {
    response = JSON.parse(response);
    if (response.response_code == 200) {
        transactions = response.data;
        for (i = 0; i < transactions.length; i++) {
            document.getElementById('transactions').insertAdjacentHTML('beforeend', createHTMLforTransaction(transactions[i]));
        }
    }
}

function getTransactions() {
    sendRequest('GET', getAPIDomain() + '/transactions/get?username=' + getCookie('USERNAME'), null, getTransactionsCallback);
}

function createDeleteReviewObject(movie_id) {
    return {
        username: getCookie('USERNAME'),
        movie_id: movie_id
    }
}

function deleteReviewCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        location.reload();
    }
}

function deleteReview(movieElement) {
    payload = createPayload(createDeleteReviewObject(movieElement.id.replace('delete-', '')));
    sendRequest('POST', getAPIDomain() + '/movies/remove_review', payload, deleteReviewCallback);
}

getTransactions();