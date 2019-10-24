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
    var hostname = '';
    var urlSplit = window.location.hostname.split('.');
    for(i = 1; i < urlSplit.length; i++){
        hostname += urlSplit[i];
        if (i < urlSplit.length - 1){
            hostname += '.';
        }
    }
    if (urlSplit.length === 1){
        hostname = urlSplit[0];
    }
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