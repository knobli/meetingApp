MemberStatus = {
	IN : 1,
	OUT : 0,
	SPECIAL : 2,
	NONE : 3
}

MemberStatusCss = {
	1 : "signin",
	0 : "signout",
	2 : "signinSpecial"
}

function getMemberStatusCss(memberStatus){
	var cssClass = MemberStatusCss[memberStatus];
	return (cssClass === undefined) ? "" : cssClass;
}

function getBaseUrl(){
    return 'http://localhost/tvdb_wetten';
}

function getAjaxUrl() {
    return getBaseUrl() + '/ajax';
}

function getAPIUrl() {
	return getBaseUrl() + '/controller/json/v0.4';
}

function createDate(phpFormattedDate) {
	var dateRegex = new RegExp("(\\d+)-(\\d+)-(\\d+)\\s+(\\d+):(\\d+):(\\d+)");
	var dateSplits = dateRegex.exec(phpFormattedDate);
	return new Date(dateSplits[1], dateSplits[2] - 1, dateSplits[3], dateSplits[4], dateSplits[5], dateSplits[6]);
}

function getTimeStamp(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();

	var output = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
	return output;
}

function getDateStamp(date) {
	var month = date.getMonth() + 1;
	var day = date.getDate();

	var output = (day < 10 ? '0' : '') + day + '.' + (month < 10 ? '0' : '') + month + '.' + date.getFullYear();
	return output;
}

function getDateTimeStamp(date) {
	return getDateStamp(date) + ' ' + getTimeStamp(date);
}

function getDateString(date) {
	var weekDays = new Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
	var months = new Array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
	var day = date.getDate();

	var output = weekDays[date.getDay()] + ', ' + (day < 10 ? '0' : '') + day + '. ' + months[date.getMonth()] + ' ' + date.getFullYear();
	return output;
}

function getStartEndDate(startDate, endDate) {
	if (startDate.getFullYear() != endDate.getFullYear() || startDate.getMonth() != endDate.getMonth() || startDate.getDate() != endDate.getDate()) {
		endStamp = getDateStamp(endDate) + ' ' + getTimeStamp(endDate);
	} else {
		endStamp = getTimeStamp(endDate);
	}
	var startStamp = getDateStamp(startDate) + ' ' + getTimeStamp(startDate);
	var output = startStamp + ' - ' + endStamp;
	return output;
}

function getUrlParameter(url, sParam) {
	var sPageURL = url.split("?")[1];
	if(sPageURL !== undefined){
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++) {
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam) {
				return sParameterName[1];
			}
		}
	}
	return null;
}

function getParameterByName(name) {
	var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
	return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
}

function alertErrorMessage(msg, errorMessages){
    var errorMessagesAsString = "";
    if($.isArray(errorMessages)){
        $.each( errorMessages, function( key, errorMessage ) {
            errorMessagesAsString += errorMessage.message + " ";
        });
        errorMessagesAsString = errorMessagesAsString.substring(0, errorMessagesAsString.length - 1);
    } else {
        errorMessagesAsString = errorMessages;
    }
    alert(msg + errorMessagesAsString);
}

var userIdKey = "TV_APP_userId";
var usernameKey = "TV_APP_username";

function setLogin(userId, username) {
	window.localStorage.setItem(userIdKey, userId);
	window.localStorage.setItem(usernameKey, username);
}

function removeLogin() {
	localStorage.removeItem(userIdKey);
	localStorage.removeItem(usernameKey);
}

function isLoggedIn() {
	if (getUserId() !== null) {
		return true;
	}
	return false;
}

function getUserId() {
	return window.localStorage.getItem(userIdKey);
}

function getUsername() {
	return window.localStorage.getItem(usernameKey);
}

function isNearBottom(){
	var heightOffset = 100;
	return ($(window).scrollTop()+heightOffset >= $(document).height() - $(window).height());
}