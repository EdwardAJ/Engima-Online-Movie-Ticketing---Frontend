function isFile(object) {
    if (object == null) {
        return false;
    }
    return object.constructor === File;
}

function createPayload(object) {
    formData = new FormData();
    for (var key in object) {
        if (object[key] == null) continue;
        if (isFile(object[key])) {
            formData.append(key, object[key], object[key].name);
        } else {
            formData.append(key, object[key]);
        }
    }

    return formData;
}

function universalCallback(ajaxRequest, callbackFunction) {
    if (ajaxRequest.readyState === 4 && ajaxRequest.status === 200) {
        callbackFunction(ajaxRequest.response);
    }
}

// Object for header example. Header must be an array!
//
// {
//     header: 'Authorization',
//     value: 'Bearer <>'
// }

function sendRequest(method, url, payload, callbackFunction, async = true, header = null) {
    ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onreadystatechange = function () {
        universalCallback(ajaxRequest, callbackFunction);
    };
    ajaxRequest.open(method, url, async);
    if (header != null) {
        for (var i = 0; i < header.length; i++) {
            ajaxRequest.setRequestHeader(header[i].header, header[i].value);
        }
    }
    ajaxRequest.send(payload);
}