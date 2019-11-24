function logoutAction() {
    signOut();
    deleteCookie('LOGIN_HASH');
    deleteCookie('USERNAME');
    location.reload();
}

function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
}

function searchAction() {
    if (event.keyCode == 13) {
        location.replace('/search?movie_name=' + document.forms['search_form']['search'].value);
    }
}

function redirectToHome() {
    location.replace('/home');
}

document.getElementById('search_form').onkeypress = function (e) {
    var key = e.charCode || e.keyCode || 0;
    if (key == 13) {
        e.preventDefault();
    }
};