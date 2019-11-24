function createLoginObject() {
    form = document.forms['login_form'];
    return {
        email: form['email'].value,
        pass: form['pass'].value,
    }
}

function createGoogleLoginObject(userUsername, userEmail, userImage) {
    form = document.forms['login_form'];
    // Created dummy password and phone number
    // Because google basic sign in doesn't provide those
    return {
        username : userUsername,
        email: userEmail,
        pass: "google",
        phone_number: "+6282200000000",
        image: userImage
    }
}

function loginCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        document.cookie = 'LOGIN_HASH=' + response.data.login_hash;
        document.cookie = 'USERNAME=' + response.data.username;
        window.location.replace('/home');
    } else {
        if (response.data === 'User not found!') {
            document.getElementById('email_msg').innerHTML = response.data;
        } else if (response.data === 'Wrong password!') {
            document.getElementById('pass_msg').innerHTML = response.data;
        } 
    }
}

function loginAction() {
    cleanErrorMessage();
    payload = createPayload(createLoginObject());
    sendRequest('POST', getAPIDomain() + '/user/login', payload, loginCallback);
}

function googleLoginAction(googleUser) {
    var profile = googleUser.getBasicProfile();
    // Using first name given on profile, full name can be too long
    var userName = profile.getGivenName();
    var userEmail = profile.getEmail();
    var userImage = profile.getImageUrl();
    googlePayload = createPayload(createGoogleLoginObject(userName, userEmail, userImage));
    sendRequest('POST', getAPIDomain() + '/user/googleLogin', googlePayload, loginCallback);
}