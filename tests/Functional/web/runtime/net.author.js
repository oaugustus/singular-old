//net.author module 
(function()
{
    var pack = {
        view: function(tpl){
            return 'src/net/author/views/' + tpl;
        }
    };

    /**
     * Modulo da aplicação
     * @type {config|*|config|config|config|config}
     */
    var module = angular.module("net.author",['ui.router'])
        .config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('net-author-list', {
                    url: '/net/author',
                    controller: 'net.author.ListController',
                    templateUrl: pack.view('list.html')
                })
                .state('net-author-new', {
                    url: '/net/author/novo',
                    controller: 'net.author.NewController',
                    templateUrl: pack.view('form.html')
                })
                .state('net-author-edit', {
                    url: '/net/author/edita/:id',
                    controller: 'net.author.EditController',
                    templateUrl: pack.view('form.html')
                })
        }]);

}());

//net.author resource service
(function()
{

    var Store = function($http, $q)
    {
        var url = './net/author/';

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

    angular.module('net.author').factory('net.author.Store', ['$http','$q', Store]);
}());

//net.author list controller
(function()
{
    var ListController = function($scope, $state)
    {
        $scope.breadcrumbs = [
            {text: 'Net'},
            {text: 'Author'}
        ]

        $scope.onNovoClick = function(){
            $state.go('net-author-new')
        };

    };

    angular.module('net.author').controller('net.author.ListController', ['$scope', '$state', ListController]);
}());

//net.author new controller
(function()
{
    var NewController = function($scope, $state, toaster, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Net' },
            {text: 'Author', view: 'net-author-list'},
            {text: 'Novo'}
        ]

        $scope.record = {};

        $scope.onCancelClick = function(){
            $state.go('net-author-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('net-author-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('net.author').controller('net.author.NewController', ['$scope','$state','toaster','net.author.Store', NewController]);
}());//net.author edit controller
(function()
{
    var EditController = function($scope, $state, toaster, $stateParams, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Net' },
            {text: 'Author', view: 'net-author-list'},
            {text: 'Editando registro (' + $stateParams.id + ')'}
        ];


        Store.$get($stateParams.id).then(function(rec){
            $scope.record = rec;
        });

        $scope.onCancelClick = function(){
            $state.go('net-author-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('net-author-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('net.author').controller('net.author.EditController', ['$scope','$state','toaster','$stateParams','net.author.Store', EditController]);
}());

