function giveterTracker(e){var t=getUrlVars();var n=t["giveterID"];var r=t["roposoID"];if(n!==undefined){setCookie("giveterID",n,30)}if(r!==undefined){setCookie("roposoID",r,30)}var i=allCookies();var s=i["giveterID"];var o=i["roposoID"];if(s!==undefined){var u=document.createElement("img");u.src="http://www.giveter.com/AffiliateTracker/update"+"?refURL="+encodeURIComponent(window.location.href)+"&giveterID="+s+"&transaction="+e}if(o!==undefined){var u=document.createElement("img");u.src="http://www.roposo.com/AffiliateTracker/update"+"?refURL="+encodeURIComponent(window.location.href)+"&roposoID="+o+"&transaction="+e}}function allCookies(){var e,t,n;e={};if(document.cookie!==""){t=document.cookie.split("; ");for(var r=t.length-1;r>=0;r--){n=t[r].split("=");e[n[0]]=n[1]}}return e}function getUrlVars(){var e={};var t=window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(t,n,r){e[n]=r});return e}function setCookie(e,t,n){var r,i;if(n){r=new Date;r.setTime(r.getTime()+n*24*60*60*1e3);i="; expires = "+r.toGMTString()}else{i=""}document.cookie=e+"="+t+"; path=/"+i}
