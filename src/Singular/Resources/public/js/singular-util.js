(function()
{
    var singularUtil = angular.module('singular-util', []);

    /**
     * Diretiva de autofocus em elementos html.
     *
     * @uathor Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singularUtil.directive('singAutofocus', function(){
        return {
            restrict: 'A',
            link : function(scope, el, attr){
                $(el).focus();
            }
        }
    });
}());