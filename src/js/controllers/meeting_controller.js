(function(){
    var app = angular.module('MeetingApp.controllers.Meeting', ['MeetingApp.controllers.Member', 'MeetingApp.directives.HelpDirective', 'ui.bootstrap']);

    app.controller('MeetingController', ['$http', function($http) {
        var meetingCtrl = this;
        meetingCtrl.meetings = [];
        $http.get('http://localhost/tvdb_wetten/controller/json/v0.4/meeting.php?memberId=201').success(function (response) {
            meetingCtrl.meetings = response;
        });
        this.getSigninClass = function(status){
            if(isLoggedIn()) {
                return getMemberStatusCss(status);
            }
        };

        this.meeting = {responsible: 201,
                        mail: 1,
                        end: '25'
        };
        this.getLocation = function(val){
            return $http.get('http://localhost/tvdb_wetten/ajax/auto_complete.php?type=ort&term=' + val).
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
            $http.post('http://localhost/tvdb_wetten/controller/json/v0.4/meeting.php' , $.param(meeting)).
                success(function(data){
                    if(data.success){
                        alert("Erfolgreich eingetragen");
                    } else {
                        alertErrorMessage("Fehler beim Erstellen: ", data.error_message);
                    }
                });
        };
    }]);

})();