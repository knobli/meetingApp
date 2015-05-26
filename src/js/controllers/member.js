(function(){
    var app = angular.module('MeetingApp.controllers.Member', ['ui.select']);
  	
  	app.directive("memberList", ['$http', function($http) {
      return {
        restrict: 'E',
        templateUrl: "member-list.html",
        scope: {
            ngModel: '='
        },
        require: 'ngModel',
        controller: function(){
                        $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
				    	var memberController = this;
				    	this.members = [];
				    	$http.post(getAPIUrl() + '/member.php' , $.param({memberId: getUserId()})).
                            success(function(data){
				    		    memberController.members = data;
				    	    });
        },
        controllerAs: "memberCtrl"
      };
    }]);
    
})();
