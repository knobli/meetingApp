(function(){
    var app = angular.module('MeetingApp.controllers.Member', []);
  	
  	app.directive("memberList", ['$http', function($http) {
      return {
        restrict: 'E',
        templateUrl: "member-list.html",
        scope: {
            ngModel: '='
        },
        require: ['?^ngModel'],
        controller: function(){
                        $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
				    	var memberController = this;
				    	this.members = [];
				    	$http.post('http://localhost/tvdb_wetten/controller/json/v0.4/member.php' , $.param({memberId: 201})).
                            success(function(data){
				    		    memberController.members = data;
				    	    });
        },
        link: function(scope, elm, attrs) {
            $("#memberSelect").select2();
        },
        controllerAs: "memberCtrl"
      };
    }]);
    
})();
