//net.task module 
(function()
{
    var pack = {
        view: function(tpl){
            return 'src/net/task/views/' + tpl;
        }
    };

    /**
     * Modulo da aplicação
     * @type {config|*|config|config|config|config}
     */
    var module = angular.module("net.task",['ui.router'])
        .config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('net-task-list', {
                    url: '/net/task',
                    controller: 'net.task.ListController',
                    templateUrl: pack.view('list.html')
                })
                .state('net-task-new', {
                    url: '/net/task/novo',
                    controller: 'net.task.NewController',
                    templateUrl: pack.view('form.html')
                })
                .state('net-task-edit', {
                    url: '/net/task/edita/:id',
                    controller: 'net.task.EditController',
                    templateUrl: pack.view('form.html')
                })
        }]);

}());

//net.task resource service
(function()
{

    var Store = function($http, $q)
    {
        var url = './net/task/';

        return {
            /**
             * Recupera um registro pelo seu id.
             *
             * @param id
             *
             * @return
             */
            $get : function(id){
                var d = $q.defer();

                $http.get(url+ 'get/'+id).success(function(data){
                    d.resolve(data.result);
                });

                return d.promise;
            },
            $query : function(params){

            },
            $delete : function(id){

            },
            $save : function(params){
                return $http.post(url+'save', params).success(function(response){
                    return response.result;
                });
            }
        }
    }

    angular.module('net.task').factory('net.task.Store', ['$http','$q', Store]);
}());

//net.task list controller
(function()
{
    var ListController = function($scope, $state)
    {
        $scope.breadcrumbs = [
            {text: 'Net'},
            {text: 'Task'}
        ]

        $scope.onNovoClick = function(){
            $state.go('net-task-new')
        };

    };

    angular.module('net.task').controller('net.task.ListController', ['$scope', '$state', ListController]);
}());

//net.task new controller
(function()
{
    var NewController = function($scope, $state, toaster, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Net' },
            {text: 'Task', view: 'net-task-list'},
            {text: 'Novo'}
        ]

        $scope.record = {};

        $scope.onCancelClick = function(){
            $state.go('net-task-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('net-task-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('net.task').controller('net.task.NewController', ['$scope','$state','toaster','net.task.Store', NewController]);
}());//net.task edit controller
(function()
{
    var EditController = function($scope, $state, toaster, $stateParams, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Net' },
            {text: 'Task', view: 'net-task-list'},
            {text: 'Editando registro (' + $stateParams.id + ')'}
        ];


        Store.$get($stateParams.id).then(function(rec){
            $scope.record = rec;
        });

        $scope.onCancelClick = function(){
            $state.go('net-task-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('net-task-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('net.task').controller('net.task.EditController', ['$scope','$state','toaster','$stateParams','net.task.Store', EditController]);
}());

