(function()
{
    var EditController = function($scope, $state, toaster, $stateParams, Store)
    {
        $scope.breadcrumbs = [
            {text: '@Package' },
            {text: '@Module', view: '@package-@module-list'},
            {text: 'Editando registro (' + $stateParams.id + ')'}
        ];


        Store.$get($stateParams.id).then(function(rec){
            $scope.record = rec;
        });

        $scope.onCancelClick = function(){
            $state.go('@package-@module-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('@package-@module-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('@package.@module').controller('@package.@module.EditController', ['$scope','$state','toaster','$stateParams','@package.@module.Store', EditController]);
}());