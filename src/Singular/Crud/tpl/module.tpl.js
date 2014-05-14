(function()
{
    var pack = {
        view: function(tpl){
            return 'src/@package/@module/views/' + tpl;
        }
    };

    /**
     * Modulo da aplicação
     * @type {config|*|config|config|config|config}
     */
    var module = angular.module("@package.@module",['ui.router'])
        .config(['$stateProvider','$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
            $stateProvider
                .state('@package-@module-list', {
                    url: '/@package/@module',
                    controller: '@package.@module.ListController',
                    templateUrl: pack.view('list.html')
                })
                .state('@package-@module-new', {
                    url: '/@package/@module/novo',
                    controller: '@package.@module.NewController',
                    templateUrl: pack.view('form.html')
                })
                .state('@package-@module-edit', {
                    url: '/@package/@module/edita/:id',
                    controller: '@package.@module.EditController',
                    templateUrl: pack.view('form.html')
                })
        }]);

}());