function createLoginObject() {
    form = document.forms['login_form'];
    return {
        email: form['email'].value,
        pass: form['pass'].value,
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