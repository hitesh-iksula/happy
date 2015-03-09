/**
 * This script has two jobs.
 *
 *  1. If the user lands on the current page after being redirected by giveter,
 *     the URL will have one string like "giveterID=ABCD1234". This script will
 *     extract this value and set in the cookie valid for next 30 days.
 *
 *  2. If the cookie already has been set for this user, this script sends back
 *     the current URL to a tracker giveter.com.
 */

// Set the user's cookie and enable activity tracking
// 'transactionStatus' should be "true" when a transaction happens by affiliate
function giveterTracker(transactionStatus) {
    var giveterID = getUrlVars()["giveterID"];

    // This is our redirect to the affiliate web page, so set cookie now
    if (giveterID != undefined) {
        setCookie("giveterID", giveterID, 30);
    }

    //
    // Only if the cookie has been set by giveter before, send a dummy request to
    // track the page on which user is now.
    //
    var ourCookie = allCookies()["giveterID"];
    if (ourCookie != undefined) {
        var i = document.createElement("img");
        i.src = "http://www.giveter.com/AffiliateTracker/update" +
                "?refURL=" + window.location.href +
                "&giveterID=" + ourCookie +
                "&transaction=" + transactionStatus;
    }
}

// Extract values from the cookie
function allCookies() {
    var cr, ck, cv;
    cr = [];
    if (document.cookie != '') {
        ck = document.cookie.split('; ');
        for (var i = ck.length - 1; i >= 0; i--) {
            cv = ck[i].split('=');
            cr[cv[0]] = cv[1];
        }
    }
    return cr;
};

// Extract the variable=value pairs from the URL
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
        function(m,key,value) {
            vars[key] = value;
        });
    return vars;
}

// Set the cookie with cname=cvalue that expires in these many days
function setCookie(cname, cvalue, days) {
    var dt, expires;
    if (days) {
        dt = new Date();
        dt.setTime(dt.getTime() + (days*24*60*60*1000));
        expires = "; expires = " + dt.toGMTString();
    }
    else {
        expires = '';
    }

    document.cookie = cname + "=" + cvalue + "; path = /" + expires;
}
