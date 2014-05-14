(function()
{
    var ListController = function($scope, $state)
    {
        $scope.breadcrumbs = [
            {text: '@Package'},
            {text: '@Module'}
        ]

        $scope.onNovoClick = function(){
            $state.go('@package-@module-new')
        };

    };

    angular.module('@package.@module').controller('@package.@module.ListController', ['$scope', '$state', ListController]);
}());