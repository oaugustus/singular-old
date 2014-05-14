//acme.autor module 
(function()
{
    var pack = {
        view: function(tpl){
            return 'src/acme/autor/views/' + tpl;
        }
    };

    /**
     * Modulo da aplicação
     * @type {config|*|config|config|config|config}
     */
    var module = angular.module("acme.autor",['ui.router'])
        .config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('acme-autor-list', {
                    url: '/acme/autor',
                    controller: 'acme.autor.ListController',
                    templateUrl: pack.view('list.html')
                })
                .state('acme-autor-new', {
                    url: '/acme/autor/novo',
                    controller: 'acme.autor.NewController',
                    templateUrl: pack.view('form.html')
                })
                .state('acme-autor-edit', {
                    url: '/acme/autor/edita/:id',
                    controller: 'acme.autor.EditController',
                    templateUrl: pack.view('form.html')
                })
        }]);

}());

//acme.autor resource service
(function()
{

    var Store = function($http, $q)
    {
        var url = './acme/autor/';

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

    angular.module('acme.autor').factory('acme.autor.Store', ['$http','$q', Store]);
}());

//acme.autor list controller
(function()
{
    var ListController = function($scope, $state)
    {
        $scope.breadcrumbs = [
            {text: 'Acme'},
            {text: 'Autor'}
        ]

        $scope.onNovoClick = function(){
            $state.go('acme-autor-new')
        };

    };

    angular.module('acme.autor').controller('acme.autor.ListController', ['$scope', '$state', ListController]);
}());

//acme.autor new controller
(function()
{
    var NewController = function($scope, $state, toaster, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Acme' },
            {text: 'Autor', view: 'acme-autor-list'},
            {text: 'Novo'}
        ]

        $scope.record = {};

        $scope.onCancelClick = function(){
            $state.go('acme-autor-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('acme-autor-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('acme.autor').controller('acme.autor.NewController', ['$scope','$state','toaster','acme.autor.Store', NewController]);
}());//acme.autor edit controller
(function()
{
    var EditController = function($scope, $state, toaster, $stateParams, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Acme' },
            {text: 'Autor', view: 'acme-autor-list'},
            {text: 'Editando registro (' + $stateParams.id + ')'}
        ];


        Store.$get($stateParams.id).then(function(rec){
            $scope.record = rec;
        });

        $scope.onCancelClick = function(){
            $state.go('acme-autor-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('acme-autor-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('acme.autor').controller('acme.autor.EditController', ['$scope','$state','toaster','$stateParams','acme.autor.Store', EditController]);
}());

