(function()
{
    var singular = angular.module('singular-ui',['ui.router','singular-util','singular-form','ngResource','toaster']);

    /**
     * Diretiva de viewport do navegador.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singViewport', function(){
        return {
            restrict: 'E',
            transclude: true,
            replace: true,
            template: '<section class="container wrapper" ng-transclude></section>'
        }
    });

    /**
     * Controlador responsável pela manipulação do menu de navegação.
     *
     * @author Otávio Fernandes <otavio@neton.com.br>
     */
    singular.controller('SingNavController', ['$scope', function TabsetCtrl($scope) {
            var ctrl = this,
                items = ctrl.items = $scope.items = [], views = [];

            $scope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams){
                if (views[toState.name]){
                    ctrl.select(views[toState.name]);
                }
            });

            ctrl.select = function(selectedItem) {
                angular.forEach(items, function(item, idx) {
                    if (item.active && item !== selectedItem) {
                        item.active = false;
                        item.onDeselect();
                    }
                });
                selectedItem.active = true;
                selectedItem.onSelect();
            };

            ctrl.addItem = function addItem(item) {
                items.push(item);

                if (item.view){
                    views[item.view] = item;
                }
                if (item.length === 1) {
                    item.active = true;
                } else if (item.active) {
                    ctrl.select(item);
                }
            };

            ctrl.removeItem = function removeItem(item) {
                var index = items.indexOf(item);
                //Select a new tab if the tab to be removed is selected
                if (item.active && item.length > 1) {
                    //If this is the last tab, select the previous tab. else, the next tab.
                    var newActiveIndex = index == items.length - 1 ? index - 1 : index + 1;
                    ctrl.select(items[newActiveIndex]);
                }
                items.splice(index, 1);
            };
    }]);

    /**
     * Diretiva de navegação (menu principal na esquerda).
     *
     * @author Otávio Fernandes <otavio@neton.com.br>
     */
    singular.directive('singNav', function(){
        return {
            restrict: 'E',
            transclude: true,
            replace: true,
            controller: 'SingNavController',
            template: '<nav class="side-nav" >' +
                        '<ul ng-transclude></ul>' +
                      '</nav>'

        }
    });

    /**
     * Diretiva de navegação secundária (menu secundário na esquerda).
     *
     * @author Otávio Fernandes <otavio@neton.com.br>
     */
    singular.directive('singNavChild', function(){
        return {
            restrict: 'E',
            transclude: true,
            replace: true,
            scope: {
                title: '@',
                onSelect: '&select',
                onDeselect: '&deselect'
            },
            require: '^singNav',
            controller: function(){

            },
            template: '<li class="side-nav-item cursor-pointer" ng-class="{active: active}">' +
                        '<a>' +
                            '<i class="nav-item-caret"></i>' +
                            '<i class="nav-item-icon icon {{icon}}"></i>' +
                            '{{title}}' +
                        '</a>' +
                        '<ul class="side-nav-child">' +
                            '<li class="side-nav-item-heading cursor-pointer">' +
                                '<a class="side-nav-back">' +
                                    '<i class="nav-item-caret"></i>' +
                                    '{{title}}' +
                                '</a>' +
                            '</li>' +
                            '<div ng-transclude></div>' +
                        '</ul>' +
                      '</li>',
            link: function(scope, el, attrs, ctrl){
                el.find('a').first().on('click', function(){
                    //ctrl.select(scope);
                    el.find('ul').first().addClass('open');
                });

                el.find('.side-nav-back').first().on('click', function(){
                    el.find('ul').first().removeClass('open');
                });

                //ctrl.addItem(scope);
            }

        }
    });


    /**
     * Diretiva do item de navegação (item do menu principal à esquerda).
     *
     * @author Otávio Fernandes <otavio@neton.com.br>
     */
    singular.directive('singNavItem', ['$state',function($state){
        return {
            restrict: 'E',
            require:'^singNav',
            controller: function(){

            },
            transclude: true,
            replace: true,
            template: '<li class="side-nav-item cursor-pointer" ng-class="{active: active}">' +
                           '<a ng-click="gotoState()">' +
                                '<i class="nav-item-icon icon {{icon}}"></i>' +
                                '<span ng-transclude></span>' +
                           '</a>' +
                       '</li>',
            scope: {
                active: '@',
                view: '@',
                onSelect: '&select',
                onDeselect: '&deselect'
            },
            compile: function(elm, attrs, transclude){
                return function postLink(scope, elm, attrs, singNavCtrl) {

                    scope.$watch('active', function(active) {

                        if (active) {
                            singNavCtrl.select(scope);
                            var parent = elm.closest('.side-nav-child').first();
                            if (parent.length == 0){
                                parent = elm.closest('.side-nav').first();
                            }
                            parent.find('.side-nav-child').removeClass('open');
                            elm.parents('.side-nav-child').addClass('open');
                        }
                    });

                    scope.icon = attrs.icon;

                    scope.gotoState = function(){
                        $state.go(scope.view);
                        singNavCtrl.select(scope);
                    };

                    singNavCtrl.addItem(scope);

                    scope.$on('$destroy', function() {
                        singNavCtrl.removeItem(scope);
                    });
                }
            }
        }
    }]);

    /**
     * Diretiva do cabeçalho do menu lateral à esquerda.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singSideHeader', [function(){
        return {
            replace: true,
            restrict: 'E',
            template: '<div class="side-header">' +
                        '<h1 class="brand">' +
                            '<a href="{{link}}">' +
                                '<img ng-show="hasLogo" src="{{logo}}">' +
                                '<span ng-transclude></span>' +
                            '</a>' +
                        '</h1>' +
                      '</div>',
            transclude: true,
            scope: {
                'link': '@',
                'logo': '@'
            },
            link: function(scope, el, attrs){
                if (scope.logo){
                    scope.hasLogo = true;
                } else {
                    scope.hasLogo = false;
                }
            }
        }
    }]);

    /**
     * Diretiva do menu lateral à esquerda.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singSideMenu', ['$templateCache', function($templateCache){
        $templateCache.put('sing-menu-item-tpl.html',
            '<sing-nav-item ng-if="item.view" view="{{item.view}}" icon="{{item.iconCls}}" active="{{item.active}}">{{item.text}}</sing-nav-item>'+
            '<sing-nav-child title="{{item.text}}" ng-if="item.items.length > 0">' +
                '<div ng-repeat="item in item.items" ng-include="\'sing-menu-item-tpl.html\'"></div>' +
            '</sing-nav-child>'
        );
        return {
            replace: true,
            restrict: 'E',
            scope: {
                logo: '@',
                link: '@',
                title: '@',
                menuItems: '='
            },
            template: '<aside class="side-left">' +
                          '<sing-side-header logo="{{logo}}" link="{{link}}"> {{title}}</sing-side-header>' +
                          '<div class="side-body">' +
                            '<sing-nav>' +
                                '<div ng-repeat="item in menuItems" ng-include="\'sing-menu-item-tpl.html\'"></div>' +
                            '</sing-nav>' +
                          '</div>' +
                      '</aside>'
        }
    }]);

    /**
     * Diretiva da área lateral esquerda do Dashboard.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singSideLeft', [function(){
        return {
            replace: true,
            restrict: 'E',
            scope: {
                logo: '@',
                link: '@',
                title: '@',
                menuItems: '='
            },
            template: '<aside class="side-left">' +
                        '<sing-side-menu menu-items="menuItems"></sing-side-menu>' +
                      '</aside>'
        }
    }]);

    /**
     * Diretiva que renderiza a view do roteamento.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singPage', [function(){
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            template: '<section class="content" ng-transclude>' +
                            //'<ui-view></ui-view>' +
                      '</section>',
            controller: function($scope){
                this.toggle = function(){
                    $scope.$el.toggleClass('content-lg');
                }

            },
            link: function(scope, el, attrs){
                scope.$el = el;

                // controla o magic layout da página
                el.find('.magic-layout').each(function(){
                    var $container = $(this),
                        parent = $container.parent(),
                        data_col = $container.attr('data-cols'),
                        viewport = $.viewportW(),
                        cols, masonry;

                    if(typeof data_col === undefined || data_col == '' ){
                        data_col = 2;
                    }

                    if(data_col == '3'){
                        cols = 'ml-col-3';
                    }
                    else if(data_col == '4'){
                        cols = 'ml-col-4';
                    }
                    else{
                        data_col = 2;
                    }

                    if (viewport <= 1280) {
                        if (data_col > 2) {
                            cols = '';
                            data_col = 2;
                        }
                    }

                    // add class for layout col
                    $container.addClass(cols);

                    // initialize masonry width
                    masonry = $container.width() / data_col;

                    $container.isotope({
                        itemSelector : '.magic-element',
                        // disable normal resizing
                        resizable: false,
                        // set columnWidth to a percentage of container width
                        masonry: { columnWidth: masonry }
                    });

                    // update fixed with transition layout
                    setTimeout(function(){
                        masonry = $container.width() / data_col;
                        // initialize Isotope
                        $container.isotope({
                            // set columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });
                    }, 500);

                    // update fixed with transition layout
                    $('.transition-layout').bind("animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd", function(){
                        masonry = $container.width() / data_col;
                        // initialize Isotope
                        $container.isotope({
                            // set columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });
                    })

                    // update initialize if transition is running
                    $("#content-aside").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
                        var masonry = $container.width() / data_col;
                        // initialize Isotope
                        $container.isotope({
                            // set columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });
                    })

                    // update columnWidth on window resize
                    $(window).on('resize', function(){
                        var viewport = $.viewportW();	// detect viewport with verge

                        // if toggle aside in mode medium to small viewport
                        if (viewport <= 1280) {
                            if (data_col > 2) {
                                cols = '';
                                data_col = 2;
                            }
                        }
                        else{
                            // set to original data
                            data_col = $container.attr('data-cols');
                            if(typeof data_col === undefined || data_col == '' ){
                                data_col = 2;
                            }

                            if(data_col == '3'){
                                cols = 'ml-col-3';
                            }
                            else if(data_col == '4'){
                                cols = 'ml-col-4';
                            }
                            else{
                                data_col = 2;
                            }
                        }

                        // update class
                        $container.removeClass('ml-col-3 ml-col-4');
                        $container.addClass(cols);

                        // update masonry
                        masonry = $container.width() / data_col;

                        // update on resize
                        $container.isotope({
                            // update columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });

                        // update columnWith after transition finished
                        $("#content").bind("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function(){
                            masonry = $container.width() / data_col;
                            $container.isotope({
                                // update columnWidth to a percentage of container width
                                masonry: { columnWidth: masonry }
                            });
                        })
                    }); // end window resize

                    // update columnWidth on toggle aside
                    $('#toggle-aside').on('click', function(e){
                        $container.isotope('reLayout');

                    }) // end toggle aside

                    // update columnWidth on toggle aside
                    $('#toggle-content').on('click', function(e){
                        masonry = $container.width() / data_col;
                        $container.isotope({
                            // update columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });
                    }) // end toggle content

                    // update columnWidth on avtive content swipe
                    $('#content[data-swipe="true"]').on('swipe', function(){
                        masonry = $container.width() / data_col;
                        $container.isotope({
                            // update columnWidth to a percentage of container width
                            masonry: { columnWidth: masonry }
                        });
                    })
                })
            }
        }
    }]);

    /**
     * Diretiva que define o botão de alternação da exibição ou não do menu lateral.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singContentToggle', [function(){
        return {
            restrict: 'E',
            replace: true,
            require: '^singPage',
            template: '<div class="header-actions pull-left">' +
                        '<button id="toggle-content" class="btn btn-icon" type="button"><i class="icon ion-navicon-round"></i></button>' +
                      '</div>',
            link: function(scope, el, attrs, viewCtrl){
                el.find('button').on('click', function(){
                    viewCtrl.toggle();
                });
            }
        }
    }]);

    /**
     * Diretiva que controla as ações da toolbar do cabeçalho.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singPageHeaderActions', [function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="header-actions pull-right">' +
                        '<div class="btn-group">' +
                            '<a id="users-setting" class="btn btn-icon data-toggle" data-toggle="dropdown" role="button">' +
                                '<i class="icon {{profile.icon}}"></i>' +
                            '</a>' +
                            '<ul class="dropdown-menu dropdown-extend pull-right" role="menu">' +
                                '<li class="notif-media" >' +
                                    '<a class="notif-item">' +
                                        '<div class="notif-img pull-left" ng-if="profile.name != \'\'">' +
                                            '<img src="{{profile.avatar}}" alt="" class="img-circle" />' +
                                        '</div>' +
                                        '<h3 class="notif-heading"><b>{{profile.name}}</b></h3>' +
                                        '<p class="notif-text">{{profile.email}}</p>' +
                                        '<p class="notif-text" ng-if="profile.aditional != false">{{profile.aditional}}</p>' +
                                    '</a>' +
                                '</li>' +
                                '<li class="dropdown-footer">' +
                                    '<div class="clearfix">' +
                                        '<a ng-click="onExitClick()" class="btn btn-sm btn-default pull-right">Sair</a>' +
                                        '<a ng-click="onProfileClick()" class="btn btn-sm btn-default pull-left">Ver perfil</a>' +
                                    '</div>' +
                                '</li>' +
                            '</ul>' +
                        '</div>' +
                      '</div>',
            scope: {
                'profile': '=',
                'onExitClick': '&',
                'onProfileClick': '&'
            },
            link: function(scope, el, attrs){
                if (!scope.profile.icon){
                    scope.profile.icon = 'ion-person';
                }
            }
        }
    }]);

    /**
     * Diretiva que define o cabeçalho da página.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singPageHeader', [function(){
        return {
            restrict: 'E',
            replace: true,
            scope: {
                title: '@',
                profile: '=?',
                exitClick: '&onExitClick',
                profileClick: '&onProfileClick'
            },
            template: '<header class="content-header">' +
                        '<sing-content-toggle></sing-content-toggle>' +
                        '<h1 class="content-title">{{title}}</h1>' +
                        '<sing-page-header-actions profile="profile" on-exit-click="exitClick()" on-profile-click="profileClick()"></sing-page-header-actions>' +
                      '</header>',
            link: function(scope, el, attr){
                if (!scope.profile){
                    scope.profile = {
                        name: '',
                        email: '',
                        avatar: '',
                        aditional: false
                    }
                }
            }
        }
    }]);

    /**
     * Diretiva que define o módulo de aplicação.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singApp', [function(){
        return {
            restrict: 'E',
            require: '^singPage',
            transclude: true,
            template: '<div class="content-spliter">' +
                        '<section class="content-main" id="content-main">' +
//                            '{{record}}' +
                            '<form name="sForm" novalidate class="content-app fixed-header" ng-class="{\'fixed-header\': fixedHeader}" ng-transclude ng-if="form" ng-submit="onFormSubmit()">' +
                                //'<div class="content-app fixed-header" ng-transclude>' +
                            '</form>' +
                            //'<div >' +
                                '<div ng-if="form == false" class="content-app" ng-class="{\'fixed-header\': fixedHeader}" ng-transclude>' +
                                '</div>' +
                            //'</div>' +
                        '</section>' +
                       '</div>',
            scope: true,
            compile: function(tEl, attrs){
//                var form = tEl.find('form');

                //if (form){
//                    form.attr('name', attrs.formName);
//                }

                return function(scope, el, attrs){

                    if (attrs.fixedHeader == undefined){
                        scope.fixedHeader = true;
                    } else {
                        scope.fixedHeader = attrs.fixedHeader == 'true';
                    }

                    /*if (attrs.formName){
                        scope.formName = attrs.formName;
                    } else {
                        scope.formName = 'form';
                    }*/
                    if (!attrs.form) {
                        scope.form = false;
                    } else {
                        scope.form = attrs.form == 'true';
                    }
                }
            }
        }
    }]);

    /**
     * Diretiva que define a toolbar do módulo de aplicação.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singToolbar', [function(){
        return {
            restrict: 'E',
            require: '^singApp',
            replace: true,
            transclude: true,
            template: '<div class="app-header" ng-transclude></div>'
        }
    }]);

    /**
     * Diretiva que define a toolbar de um formulário.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormToolbar', [function(){
        return {
            restrict: 'E',
            require: '^singApp',
            replace: true,
            template: '<div class="app-header">' +
                            '<div class="pull-left">' +
                                '<ol class="breadcrumb bg-none hide-xs">' +
                                    '<li ng-repeat="item in breadcrumbs" ng-class="{active: $last == true}">' +
                                        '<a ng-if="item.view && !$last" ui-sref="{{item.view}}" ng-class="{active: $last == true}">{{item.text}}</a>' +
                                        '<a ng-if="!item.view && !$last">{{item.text}}</a>' +
                                        '<span ng-if="$last">{{item.text}}</span>' +
                                    '</li>' +
                                '</ol>' +
                            '</div>' +
                            '<div class="pull-right no-padding-right">' +
                                '<button type="submit" class="btn btn-success" ng-disabled="sForm.$invalid">Salvar</button>' +
                                '<button type="button" class="btn btn-link" ng-click="onCancelClick()">Cancelar</button>' +
                            '</div>' +
                      '</div>',
            scope: true/*{
                'breadcrumbs': '=',
                'onSaveClick': '&',
                'onCancelClick': '&'
            }*/
        }
    }]);

    /**
     * Diretiva que define a toolbar de uma lista de dados.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singListToolbar', [function(){
        return {
            restrict: 'E',
            require: '^singApp',
            replace: true,
            template: '<div class="app-header">' +
                        '<div class="pull-left">' +
                            '<ol class="breadcrumb bg-none hide-xs">' +
                                '<li ng-repeat="item in breadcrumbs" ng-class="{active: $last == true}">' +
                                    '<a ng-if="item.view && !$last" ui-sref="{{item.view}}" ng-class="{active: $last == true}">{{item.text}}</a>' +
                                    '<a ng-if="!item.view && !$last">{{item.text}}</a>' +
                                    '<span ng-if="$last">{{item.text}}</span>' +
                                '</li>' +
                            '</ol>' +
                        '</div>' +
                        '<div class="pull-right col-lg-6 no-padding-right">' +
                            '<div class="col-lg-10 pull-left no-padding-right">' +
                                '<sing-advanced-search></sing-advanced-search>' +
                            '</div>' +
                            '<div class="col-lg-2 pull-right no-padding-right">' +
                                '<button type="button" class="btn btn-danger" ng-click="onNewClick()">Novo</button>' +
                            '</div>' +

                        '</div>' +
                      '</div>',
            scope: {
                'breadcrumbs': '=',
                'onNewClick': '&'
            }


        }
    }]);

    /**
     * Diretiva que define o corpo de um módulo aplicação.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singAppBody', [function(){
        return {
            restrict: 'E',
            require: 'ˆsingApp',
            replace: true,
            transclude: true,
            template: '<div class="app-body" ng-transclude></div>'
        }
    }]);

    /**
     * Diretiva que define um campo de busca avançado.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singAdvancedSearch', [function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="input-group no-padding-right">' +
                        '<span class="input-group-btn">' +
                            '<button type="button" class="btn btn-default" data-toggle="dropdown" tabindex="-1">' +
                                '<span class="caret"></span>' +
                            '</button>' +
                        '</span>' +
                        '<input type="text" class="form-control" placeholder="pesquisar...">' +
                        '<span class="input-group-btn">' +
                            '<button class="btn btn-default btn-primary" type="button">'+
                                '<i class="icon ion-search"></i>' +
                            '</button>' +
                        '</span>' +
                      '</div>'

        }
    }]);

    /**
     * Diretiva que define o painel da interface de usuário do sistema.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singPanel', [function(){
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            template:'<div class="panel panel-default magic-element bordered-none">' +
                        '<div class="panel-heading bg-primary text-inverse bordered-none">' +
                            '<div class="panel-icon" ng-if="icon != undefined"><i class="icon {{icon}}"></i></div>' +
                            '<div class="panel-actions">' +
                                /*'<a role="button" data-refresh="#panel-custom2" title="refresh" class="btn btn-sm btn-icon">' +
                                    '<i class="icon ion-refresh text-inverse"></i>' +
                                '</a>' +
                                '<a role="button" data-expand="#panel-custom2" title="expand" class="btn btn-sm btn-icon">' +
                                    '<i class="icon ion-arrow-resize text-inverse"></i>' +
                                '</a>' +*/
                            '</div>' +
                            '<h3 class="panel-title">{{title}}</h3>' +
                        '</div>' +
                        '<div class="panel-body bordered-none" ng-transclude>' +
                        '</div>' +
                        '<div class="panel-footer clearfix">' +
                            '<div class="pull-left" style="margin: 10px 0;">Showing 1 to 5 of 32 entries</div>' +
                            '<div class="pull-right">' +
                            '<ul style="margin: 2px;" class="pagination">' +
                                '<li class="disabled"><a href="#">&laquo;</a></li>' +
                                '<li class="active"><a href="#">1</a></li>' +
                                '<li><a href="#">2</a></li>' +
                                '<li><a href="#">3</a></li>' +
                                '<li><a href="#">4</a></li>' +
                                '<li><a href="#">5</a></li>' +
                                '<li><a href="#">&raquo;</a></li>' +
                            '</ul><!-- /pagination -->' +
                            '</div>' +
                        '</div>' +
                     '</div>',
            scope: {
                icon: '@',
                title: '@'
            },
            link: function(scope, el, attrs){
                // expand panel
                el.find('[data-expand]').on('click', function(e){
                    e.preventDefault();
                    var $this = $(this),
                        panel = $this.attr('data-expand');

                    $(panel).toggleClass('expand');
                });
                // refresh panel
                el.find('[data-refresh]').on('click', function(e){
                    e.preventDefault();
                    var $this = $(this),
                        panel = $this.attr('data-refresh');

                    $(panel).append('<div class="panel-progress"><div class="panel-spinner"></div></div>');

                    callback_panel();
                })

            }

        }
    }]);

    /**
     * Diretiva que define o painel de formulário.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormPanel', function(){
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            template:'<div class="panel panel-default magic-element bordered-none">' +
                        '<div class="panel-heading bg-primary text-inverse bordered-none">' +
                            '<div class="panel-icon" ng-if="icon != undefined"><i class="icon {{icon}}"></i></div>' +
                            '<div class="panel-actions">' +
                            '</div>' +
                            '<h3 class="panel-title">{{title}}</h3>' +
                        '</div>' +
                        '<div class="panel-body bordered-none" ng-transclude>' +
                        '</div>' +
                      '</div>',
            scope: {
                icon: '@',
                title: '@'
            },
            link: function(scope, el, attrs){
                // expand panel
                el.find('[data-expand]').on('click', function(e){
                    e.preventDefault();
                    var $this = $(this),
                        panel = $this.attr('data-expand');

                    $(panel).toggleClass('expand');
                });
                // refresh panel
                el.find('[data-refresh]').on('click', function(e){
                    e.preventDefault();
                    var $this = $(this),
                        panel = $this.attr('data-refresh');

                    $(panel).append('<div class="panel-progress"><div class="panel-spinner"></div></div>');

                    callback_panel();
                })

            }

        }
    });

}());