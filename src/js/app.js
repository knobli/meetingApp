angular.module('MeetingApp', [
  'ngRoute',
  'mobile-angular-ui',
  'ui.bootstrap',
  'MeetingApp.directives.HelpDirective',
  'MeetingApp.controllers.Main',
  'MeetingApp.controllers.Meeting',
  'MeetingApp.controllers.Member'
])

.config(function($routeProvider) {
  $routeProvider.when('/', {templateUrl:'home.html',  reloadOnSearch: false});
  $routeProvider.when('/add_meeting', {templateUrl:'add_meeting.html',  reloadOnSearch: false});
});