function isEmailValid(email) {
    return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email);
}

function isUsernameValid(username) {
    return /^[A-Za-z0-9_]*$/.test(username);
}

function isPhoneNumberValid(phoneNumber) {
    return /^[0-9]{9,12}$/.test(phoneNumber) || /^\+[0-9]{2}[0-9]{8,11}$/.test(phoneNumber);
}

function createRegistrationObject() {
    form = document.forms['registration_form'];
    return {
        username: form['username'].value.toLowerCase(),
        email: form['email'].value,
        phone_number: form['phone_number'].value,
        pass: form['pass'].value,
        image: form['image'].files[0]
    }
}

function registrationCallback(response) {
    response = JSON.parse(response);
    if (response.response_code === 200) {
        document.cookie = 'LOGIN_HASH=' + response.data.login_hash;
        document.cookie = 'USERNAME=' + response.data.username;
        window.location.replace('/home');
    } else {
        if (response.data === 'Username aleady exist!') {
            document.getElementById('username_msg').innerHTML = response.data;
        } else if (response.data === 'Email aleady exist!') {
            document.getElementById('email_msg').innerHTML = response.data;
        } else if (response.data === 'Phone number aleady exist!') {
            document.getElementById('phone_number_msg').innerHTML = response.data;
        }
    }
}

function registerAction() {
    cleanErrorMessage();

    registrationObject = createRegistrationObject();
    if (registrationObject.username === '' || !isUsernameValid(registrationObject.username)) {
        document.getElementById('username_msg').innerHTML = 'Invalid username!';
        return;
    }
    if (registrationObject.email === '' || !isEmailValid(registrationObject.email)) {
        document.getElementById('email_msg').innerHTML = 'Invalid email!';
        return;
    }
    if (registrationObject.phone_number === '' || !isPhoneNumberValid(registrationObject.phone_number)) {
        document.getElementById('phone_number_msg').innerHTML = 'Invalid phone number!';
        return;
    }
    if (registrationObject.pass === '') {
        document.getElementById('pass_msg').innerHTML = 'Please input your password!';
        return;
    }
    if (registrationObject.pass !== document.forms['registration_form']['pass_confirm'].value) {
        document.getElementById('pass_confirm_msg').innerHTML = 'Different password!';
        return;
    }

    payload = createPayload(registrationObject);
    sendRequest('POST', getAPIDomain() + '/user/register', payload, registrationCallback);
}