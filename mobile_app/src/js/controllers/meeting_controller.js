(function(){
    var app = angular.module('MeetingApp.controllers.Meeting', ['MeetingApp.controllers.Member', 'MeetingApp.directives.HelpDirective', 'ui.bootstrap']);

    app.controller('MeetingController', ['$http', '$routeParams', '$route', '$location', function($http, $routeParams, $route, $location) {
        var meetingCtrl = this;
        meetingCtrl.meetings = [];
        $http.get(getAPIUrl() + '/meeting.php?memberId=' + getUserId()).success(function (response) {
            meetingCtrl.meetings = response;
        });
        this.getSigninClass = function(status){
            if(isLoggedIn()) {
                return getMemberStatusCss(status);
            }
        };

        this.meeting = {responsible: getUserId(),
                        mail: 1
        };
        this.getLocation = function(val){
            return $http.get(getAjaxUrl() + '/auto_complete.php?type=ort&term=' + val).
                then(function(response){
                    return response.data.map(function(item){
                        return item.value;
                    });
                });
        };
        var meeting = this.meeting;
        this.submitForm = function(){
            $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            var memberController = this;
            this.members = [];
            $http.post(getAPIUrl() + '/meeting.php' , $.param(meeting)).
                success(function(data){
                    if(data.success === 1){
                        alert("Erfolgreich eingetragen");
                        $location.path('/');
                    } else {
                        alertErrorMessage("Fehler beim Erstellen: ", data.error_message);
                    }
                });
        };

        var meetingId = $routeParams.meetingId;
        if(meetingId !== undefined) {
            $http.get(getAPIUrl() + '/meeting.php?id=' + meetingId + '&memberId=' + getUserId()).success(function (response) {
                meetingCtrl.loadMeeting = response;
            });
        }


        $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
        this.members = [];
        $http.post(getAPIUrl() + '/member.php' , $.param({memberId: getUserId()})).
            success(function(data){
                meetingCtrl.members = data;
            });

        this.signin = function(status){
            var signinPayload = {
                signinObjectId: meetingId,
                memberId: getUserId(),
                status: status,
                comment: "Sign in from demo app"
            };
            $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            $http.post(getAPIUrl() + '/signinEntries.php', $.param(signinPayload)).
                success(function(data){
                    if(data.success  === 1){
                        alert("Erfolgreich eingetragen");
                        $route.reload();
                    } else {
                        alertErrorMessage("Fehler beim Erstellen: ", data.error_message);
                    }
                });
        }
    }]);

})();