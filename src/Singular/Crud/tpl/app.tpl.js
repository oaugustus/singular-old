(function()
{
    var app = angular.module('app', [
        'app.modules'
        // inclua seus módulos adicionais aqui
    ]).controller('MainController', ['$scope', function($scope){
            $scope.menuItems = [
                {text: 'Dashboard', iconCls: 'ion-ios7-monitor-outline', active: true, view: 'view1'},
                {text: 'Submenu', iconCls: 'ion-ios7-monitor-outline', items: [
                    {text: 'Opção 1', iconCls: 'ion-ios7-monitor-outline', view: 'view2'},
                    {text: 'Submenu 2', iconCls: 'ion-ios7-monitor-outline', items: [
                        {text: 'Opção 2.1', iconCls: 'ion-ios7-monitor-outline', view: 'view3'},
                        {text: 'Opção 2.2', iconCls: 'ion-ios7-monitor-outline', view: 'view4'}
                    ]}
                ]}
            ];

            $scope.onExit = function(){
                alert('clicou em sair');
            };

            $scope.onProfile = function(){
                alert('clicou no perfil');
            }

            $scope.profile = {
                name: 'Otávio Fernandes',
                email: 'otavio@netonsolucoes.com.br'
            };

    }]);
}());