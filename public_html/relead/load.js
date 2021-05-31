function getCookie(name) {

    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ))
    return matches ? decodeURIComponent(matches[1]) : undefined
}

function setCookie(name, value, props) {

    props = props || {}

    var exp = props.expires

    if (typeof exp == "number" && exp) {

        var d = new Date()

        d.setTime(d.getTime() + exp * 1000)

        exp = props.expires = d

    }

    if (exp && exp.toUTCString) { props.expires = exp.toUTCString() }

    value = encodeURIComponent(value)

    var updatedCookie = name + "=" + value

    for (var propName in props) {

        updatedCookie += "; " + propName

        var propValue = props[propName]

        if (propValue !== true) { updatedCookie += "=" + propValue }
    }

    document.cookie = updatedCookie

}

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

let event = new Event("relead_load");

if(typeof relead_param === 'undefined')
    relead_param = findGetParameter("rld_ref");

relead_param_old = getCookie("relead_param");

relead_id = getCookie("relead_id");
if(relead_id && (!relead_param || relead_param == relead_param_old)) {
    console.log("Relead-ID: " + relead_id);
    window.dispatchEvent(event);
} else {
    fetch('https://relead.paulislava.space/relead/load.php?location=' + encodeURI(location.href) + '&param=' + relead_param, {
        method: 'GET',
    }).then(function (response) {
        response.text().then(function (response_text) {
            if (response.status != 200) {
                console.log("Relead-ID get error.")
            } else {
                relead_id = response_text;
                console.log("Relead-ID: " + relead_id);
                setCookie("relead_id", relead_id, { expires: 60 * 60 * 24 * 30, path: "/" });
                setCookie("relead_param", relead_param, { expires: 60 * 60 * 24 * 30, path: "/" });
            }
            window.dispatchEvent(event);
        });
        return response.ok;
    });
}


function rl_event(event_id, end_action = null) {
    
    if(!relead_id) {
        if(end_action !== null) 
            end_action(null);
        
        return false;
    }

    fetch('https://relead.paulislava.space/relead/event.php?referral_id=' + relead_id + '&event_id=' + event_id + '&location=' + encodeURI(location.href), {
        method: 'GET',
    }).then(function (response) {
        response.text().then(function (response_text) {
            if (response.status != 200 || response_text != '3') {
                console.log("Relead-event '" + event_id + "' error.");
                console.log("Error code: " + response_text);
            } else {
                console.log("Relead-event '" + event_id + "' success. ");
            }

            if(end_action !== null) 
                end_action(response_text);
            
        });
        return response.ok;
    });
}