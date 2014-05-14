//acme.book module 
(function()
{
    var pack = {
        view: function(tpl){
            return 'src/acme/book/views/' + tpl;
        }
    };

    /**
     * Modulo da aplicação
     * @type {config|*|config|config|config|config}
     */
    var module = angular.module("acme.book",['ui.router'])
        .config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('acme-book-list', {
                    url: '/acme/book',
                    controller: 'acme.book.ListController',
                    templateUrl: pack.view('list.html')
                })
                .state('acme-book-new', {
                    url: '/acme/book/novo',
                    controller: 'acme.book.NewController',
                    templateUrl: pack.view('form.html')
                })
                .state('acme-book-edit', {
                    url: '/acme/book/edita/:id',
                    controller: 'acme.book.EditController',
                    templateUrl: pack.view('form.html')
                })
        }]);

}());

//acme.book resource service
(function()
{

    var Store = function($http, $q)
    {
        var url = './acme/book/';

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

    angular.module('acme.book').factory('acme.book.Store', ['$http','$q', Store]);
}());

//acme.book list controller
(function()
{
    var ListController = function($scope, $state)
    {
        $scope.breadcrumbs = [
            {text: 'Acme'},
            {text: 'Book'}
        ]

        $scope.onNovoClick = function(){
            $state.go('acme-book-new')
        };

    };

    angular.module('acme.book').controller('acme.book.ListController', ['$scope', '$state', ListController]);
}());

//acme.book new controller
(function()
{
    var NewController = function($scope, $state, toaster, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Acme' },
            {text: 'Book', view: 'acme-book-list'},
            {text: 'Novo'}
        ]

        $scope.record = {};

        $scope.onCancelClick = function(){
            $state.go('acme-book-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('acme-book-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('acme.book').controller('acme.book.NewController', ['$scope','$state','toaster','acme.book.Store', NewController]);
}());//acme.book edit controller
(function()
{
    var EditController = function($scope, $state, toaster, $stateParams, Store)
    {
        $scope.breadcrumbs = [
            {text: 'Acme' },
            {text: 'Book', view: 'acme-book-list'},
            {text: 'Editando registro (' + $stateParams.id + ')'}
        ];


        Store.$get($stateParams.id).then(function(rec){
            $scope.record = rec;
        });

        $scope.onCancelClick = function(){
            $state.go('acme-book-list');
        }

        $scope.onFormSubmit = function(){
            Store.$save($scope.record).success(function(response){
                toaster.pop('success', 'Salvo', 'Registro ' + response.result + ' salvo com sucesso!');
                $state.go('acme-book-list');
            }).error(function(response){
                toaster.pop('error','Falhou', 'Houve uma falha ao salvar. O registro não foi salvo!');
            });
        }
    };

    angular.module('acme.book').controller('acme.book.EditController', ['$scope','$state','toaster','$stateParams','acme.book.Store', EditController]);
}());

