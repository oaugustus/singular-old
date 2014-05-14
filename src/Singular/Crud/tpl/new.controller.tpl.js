(function()
{
    var NewController = function($scope, $state, toaster, Store)
    {
        $scope.breadcrumbs = [
            {text: '@Package' },
            {text: '@Module', view: '@package-@module-list'},
            {text: 'Novo'}
        ]

        $scope.record = {};

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

    angular.module('@package.@module').controller('@package.@module.NewController', ['$scope','$state','toaster','@package.@module.Store', NewController]);
}());