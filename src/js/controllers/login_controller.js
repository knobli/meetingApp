(function(){
    var app = angular.module('MeetingApp.controllers.Login', []);

    app.controller('LoginController', ['$http', function($http) {
        var loginCtrl = this;
        this.loggedIn = isLoggedIn();
        this.username = getUsername();
        this.login = {};
        this.submitForm = function(){
            $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
            $http.post('http://localhost/tvdb_wetten/controller/json/v0.4/login.php' , $.param(loginCtrl.login)).
                success(function(data){
                    if(data.success){
                        loginCtrl.loggedIn = true;
                        loginCtrl.username = data.username;
                        setLogin(data.memberId, data.username);
                        alert("Erfolgreich angemeldet");
                    } else {
                        alertErrorMessage("Fehler beim Erstellen: ", data.error_message);
                    }
                });
        };

        this.logout = function(){
            this.loggedIn = false;
            this.username = null;
            removeLogin();
            alert("Erfolgreich abgemeldet");
        };
    }]);

})();