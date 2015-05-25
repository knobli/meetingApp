/**
 * Created by knobli on 02.03.2015.
 */
(function(){
    var app = angular.module('MeetingApp.directives.HelpDirective', []);

    app.directive("datetimePicker", function() {
        return {
            restrict: 'E',
            templateUrl: "datetime-picker.html",
            scope: {
                ngModel: '=',
                myPlaceholder: '@',
                myLabel: '@'
            },
            require: ['?^ngModel'],
            link: function(scope, element, attrs, ngModelCtrl) {
                $(element).find('.datetime-picker').datetimepicker({
                    format: "dd.mm.yyyy hh:ii",
                    autoclose: true,
                    language: "de",
                    startDate: new Date(),
                    minuteStep: 10
                });
            }
        };
    });
})();