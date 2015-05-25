(function() {
    var app = angular.module('MeetingApp.controllers.Main', []);

    app.controller('MainController', function ($scope) {

    });

    app.filter('myDate', ['$filter', function ($filter) {
        return function (input, format) {
            return $filter('date')(createDate(input), format);
        }
    }]);

    app.filter('memberName', function () {
        return function (input) {
            return input.firstname + ' ' + input.surname;
        }
    });

})();