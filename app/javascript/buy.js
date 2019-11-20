function createHTMLforSeats(screening) {
    html_string = ``;
    for (i = 0; i < screening.seats.length; i++) {
        if (i + 1 === currentSeat) {
            html_string += `<label class="seat seat-chosen">` + String(i + 1) + `</label>`;
            continue;
        }
        if (screening.seats[i] == '1') {
            html_string += `<label class="seat seat-taken">` + String(i + 1) + `</label>`;
        } else {
            html_string += `<label class="seat seat-free hoverable" onclick=chooseSeat(` + String(i + 1) + `)>` + String(i + 1) + `</label>`;
        }
    }
    return html_string += `<div class="screen">Screen</div>`;
}

function drawSeats(screening) {
    document.getElementById('seat-choosing').innerHTML = createHTMLforSeats(screening);
}

function setMovieDetail(data) {
    document.getElementById('movie-title').innerHTML = data.movie.title;
    document.getElementById('movie-schedule').innerHTML = data.screening.show_time;
}

function getScreeningDataCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        movie = response.data.movie;
        screening = response.data.screening;
        setMovieDetail(response.data);
        drawSeats(response.data.screening);
    }
}

function getScreeningData() {
    sendRequest('GET', getAPIDomain() + '/movies/showing?showing_id=' + getParameterValue(window.location, 'showing_id'), null, getScreeningDataCallback, false);
}

function createHTMLforSummary() {
    html_string =
        `
    <h2>Booking Summary</h2>
    <p class="ticket-name">` + movie.title + `</p>
    <p>` + screening.show_time + `</p>
    <p class="seat-chosen-text"><span class="seat-number">Seat #` + String(currentSeat) + `</span><span class="seat-price">Rp ` + String(screening.price) + `</span></p>
    <div class="buy-button hoverable" onclick="buyTicket()">Buy Ticket</div>
    `;

    return html_string;
}

function drawSummary() {
    document.getElementById('booking-summary').innerHTML = createHTMLforSummary();
}

function chooseSeat(seat) {
    currentSeat = seat;
    drawSeats(screening);
    drawSummary();
}

function createBuyObject() {
    return {
        username: getCookie('USERNAME'),
        showing_id: getParameterValue(window.location, 'showing_id'),
        seat_id: currentSeat
    };
}

function showModal(success, message = null) {
    if (!success) {
        document.getElementById('payment-text').innerHTML = 'Payment Failed';
        document.getElementById('payment-message').innerHTML = message;
    } else {
        document.getElementById('virtual-account-message').innerHTML = 'Pay by transfering to ' + message;
    }
    document.getElementById('modal').style.display = 'inherit';
}

function buyTicketCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        showModal(true, response.data.virtual_account_number);
    } else {
        showModal(false, response.data.message);
    }
}

function buyTicket() {
    payload = createPayload(createBuyObject());
    sendRequest('POST', getAPIDomain() + '/transactions/buy', payload, buyTicketCallback, false);
}

movie = null;
screening = null;
currentSeat = null;
getScreeningData();