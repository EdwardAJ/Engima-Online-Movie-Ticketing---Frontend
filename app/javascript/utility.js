function cleanErrorMessage() {
    message_elements = document.getElementsByClassName('error-message');
    for (i = 0; i < message_elements.length; i++) {
        message_elements[i].innerHTML = '';
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

function getAPIDomain() {
    var hostname = window.location.hostname;

    var port = '';
    if (window.location.port != ''){
        port = ':' + window.location.port;
    }
    return 'http://api.' + hostname + port;
}

function getParameterValue(url, parameter) {
    url = new URL(url);
    return url.searchParams.get(parameter);
}

function setElementHeights(lowerElementID = 'movie-container') {
    bodyHeight = window.innerHeight - document.getElementById('navbar').scrollHeight;
    document.getElementById(lowerElementID).setAttribute('style', 'min-height:' + String(bodyHeight) + 'px');
}

function redirect(url) {
    window.location = url;
}

function getGeneralHeaderMovieDB() {
    return [
        {
            header: 'Authorization',
            value: 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIwMzE5OTNhYzU4ZTNhZjNmYTIyNDI5ODYyYjU3YzU4MCIsInN1YiI6IjVkZDM2MTY1YjM5ZTM1MDAxODhjMjAzYSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.DoElzI3F4KFHZ2mHG5T-nwuFK1T7u-T9LfdnsR6m54Y'
        },
        {
            header: 'Content-Type',
            value:'application/json'
        }
    ];
}

function getImageURLMovieDB() {
    return 'https://image.tmdb.org/t/p/w500/';
}