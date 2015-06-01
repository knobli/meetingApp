angular.module('MeetingApp', [
  'ngRoute',
  'mobile-angular-ui',
  'ui.bootstrap',
  'MeetingApp.directives.HelpDirective',
  'MeetingApp.controllers.Main',
  'MeetingApp.controllers.Meeting',
  'MeetingApp.controllers.Member',
  'MeetingApp.controllers.Login'
])

.config(function($routeProvider) {
  $routeProvider.when('/', {templateUrl:'home.html',  reloadOnSearch: false});
  $routeProvider.when('/login', {templateUrl:'login.html',  reloadOnSearch: false});
  $routeProvider.when('/add_meeting', {templateUrl:'add_meeting.html',  reloadOnSearch: false});
  $routeProvider.when('/meeting/:meetingId', {templateUrl:'meeting.html',  reloadOnSearch: false});
})

.run( function($rootScope, $location) {

    // register listener to watch route changes
    $rootScope.$on( "$routeChangeStart", function(event, next, current) {
        if ( !isLoggedIn() ) {
            // no logged user, we should be going to #login
            if ( next.templateUrl == "partials/login.html" ) {
                // already going to #login, no redirect needed
            } else {
                // not going to #login, we should redirect now
                $location.path( "/login" );
            }
        }
    });
});