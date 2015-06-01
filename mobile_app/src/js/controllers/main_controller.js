(function() {
    var app = angular.module('MeetingApp.controllers.Main', []);

    app.controller('MainController', function ($scope) {

    });

    app.filter('myDate', ['$filter', function ($filter) {
        return function (input, format) {
            if(input === null || input === undefined){
                return null;
            }
            return $filter('date')(createDate(input), format);
        }
    }]);

    app.filter('beginEndDate', function () {
        return function (input) {
            if(input === null || input === undefined){
                return null;
            }
            return getStartEndDate(createDate(input.startDate.date), createDate(input.endDate.date));
        }
    });

    app.filter('memberName', function () {
        return function (input) {
            if(input === null || input === undefined){
                return null;
            }
            return input.firstname + ' ' + input.surname;
        }
    });

})();