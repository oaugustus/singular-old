(function()
{
    var singular = angular.module('singular-form',[]);

    /**
     * Define a diretiva responsável pela criação de um campo simples de texto.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormText', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                        '<label class="control-label">{{label}}&nbsp;</label>' +
                        '<sing-field-validation ></sing-field-validation>' +
                        '<input type="text" class="form-control" />' +
                      '</div>',
            scope: true,
            compile: function(tEl, attrs){
                for (prop in attrs){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('input').attr(prop, attrs[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('input').attr('ng-model', "record." + attrs.name);
                tEl.find('sing-field-validation').attr('field', attrs.name);
                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;

                }
            }

        }
    });

    /**
     * Define a diretiva responsável pela criação de um campo numérico simples.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormNumber', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                '<label class="control-label">{{label}}&nbsp;</label>' +
                '<sing-field-validation ></sing-field-validation>' +
                '<input type="number" class="form-control" />' +
                '</div>',
            scope: true,
            compile: function(tEl, attrs){
                for (prop in attrs){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('input').attr(prop, attrs[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('input').attr('ng-model', "record." + attrs.name);
                tEl.find('sing-field-validation').attr('field', attrs.name);
                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;

                }
            }

        }
    });

    /**
     * Define a diretiva responsável pela criação de um campo numérico do tipo decimal.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormDecimal', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                '<label class="control-label">{{label}}&nbsp;</label>' +
                '<sing-field-validation ></sing-field-validation>' +
                '<input type="text" class="form-control" />' +
                '</div>',
            scope: true,
            compile: function(tEl, attr){
                for (prop in attr){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('input').attr(prop, attr[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('input').attr('ng-model', "record." + attr.name);
                tEl.find('sing-field-validation').attr('field', attr.name);
                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;
                    precision = attrs.precision ? attrs.precision : 2;

                    el.find('input').maskMoney({decimal: ',', thousands: '.', precision: parseInt(precision)});

                }
            }

        }
    });

    /**
     * Define a diretiva responsável pela criação de um campo do tipo data.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormDate', function(){
        return {
            restrict: 'E',
            replace: true,
            //require: '?ngModel',
            template: '<div class="form-group">' +
                            '<label class="control-label">{{label}}&nbsp;</label>' +
                            '<sing-field-validation ></sing-field-validation>' +
                            '<div class="input-group date">' +
                                '<input type="text" class="form-control" />' +
                                '<span class="input-group-addon">' +
                                    '<span class="ion-ios7-calendar-outline icon"></span>' +
                                '</span>' +
                            '</div>' +
                    '</div>',
            scope: true,
            compile: function(tEl, attr){
                for (prop in attr){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('input').attr(prop, attr[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('input').attr('ng-model', "record." + attr.name);
                tEl.find('sing-field-validation').attr('field', attr.name);
                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;
                    precision = attrs.precision ? attrs.precision : 2;

                    var datePicker = el.find('input').datetimepicker({format: 'YYYY-MM-DD', pickTime: false});

                    //datePicker.on('dp.change', function(){
//                        console.log(datePicker.data('DateTimePicker').getDate());
//                    });

                    el.find('.input-group-addon').on('click', function(){
                        datePicker.data('DateTimePicker').show();
                    })

                }
            }

        }
    });



    /**
     * Define a diretiva responsável pela criação de um campo textarea.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormTextarea', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                '<label class="control-label">{{label}}&nbsp;</label>' +
                '<sing-field-validation ></sing-field-validation>' +
                '<textarea type="text" class="form-control" ></textarea>' +
                '</div>',
            scope: true,
            compile: function(tEl, attrs){
                for (prop in attrs){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('textarea').attr(prop, attrs[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('textarea').attr('ng-model', "record." + attrs.name);
                tEl.find('sing-field-validation').attr('field', attrs.name);
                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;

                }
            }

        }
    });

    /**
     * Define a diretiva responsável pela criação de um campo select.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormSelect', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                '<label class="control-label">{{label}}&nbsp;</label>' +
                '<sing-field-validation ></sing-field-validation>' +
                '<select class="form-control" ng-options="value.id as value.value for value in options"></select>' +
                '</div>',
            scope: true,
            compile: function(tEl, attrs){
                for (prop in attrs){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('select').attr(prop, attrs[prop]);
                        tEl.removeAttr(prop);
                    }
                }
                tEl.find('select').attr('ng-model', "record." + attrs.name);
                tEl.find('sing-field-validation').attr('field', attrs.name);
                return function(scope, el, attrs){
                    scope.options = angular.fromJson(attrs.options);
                    scope.label = attrs.label;
                    scope.name = attrs.name;

                }
            }

        }
    });

    /**
     * Define a diretiva responsável pela criação de um campo checkbox.
     *
     * @author Otávio Fernandes <otavio@netonsolucoes.com.br>
     */
    singular.directive('singFormCheckbox', function(){
        return {
            restrict: 'E',
            replace: true,
            template: '<div class="form-group">' +
                        '<div class="checkbox">' +
                            '<label>' +
                                '<input type="checkbox"/> {{label}}' +
                            '</label>' +
                        '</div>' +
                      '</div>',
            scope: true,
            compile: function(tEl, attrs){
                for (prop in attrs){
                    if (prop.charAt(0) != '$' && prop != 'class'){
                        tEl.find('input').attr(prop, attrs[prop]);
                        tEl.removeAttr(prop);
                    }
                }

                //console.log(attrs);
                tEl.find('input').attr('ng-model', "record." + attrs.name);
                tEl.find('input').attr('ng-true-value', attrs.trueValue);
                tEl.find('input').attr('ng-false-value', attrs.falseValue);

                return function(scope, el, attrs){
                    scope.label = attrs.label;
                    scope.name = attrs.name;

                }
            }

        }
    });

    /**
     * Diretiva que automatiza a exibição de mensagens de erro de validação.
     *
     * @constructor
     */
    singular.directive('singFieldValidation', function(){

        function getCustomErrors(attr){
            var errors = [], errorName;
            for (item in attr){

                if (item.search('error') == 0){
                    errorName = item.replace('error','');
                    errors[item] = errorName.charAt(0).toLowerCase() + errorName.substr(1);
                }
            }

            return errors;
        };

        return {
            restrict: 'E',
            scope: true,
            compile: function(element, attr){
                var formName = 'sForm',//element.closest('singApp').first().attr('formName'),
                    min = attr.min ? attr.min : 'Muito curto',
                    max = attr.max ? attr.max : 'Muito grande',
                    required = attr.required ? attr.required : 'Obrigatório!',
                    url = attr.url ? attr.url : 'URL inválida!',
                    ok = attr.ok ? attr.ok : 'OK',
                    mask = attr.mask ? attr.mask : 'Formato inválido!',
                    email = attr.email ? attr.email : 'Email inválido!',
                    field = formName+ '.' + attr.field,
//                    field = attr.field,
                    ngClass = '\'label-danger\': ' + field + '.$invalid && '+ field + '.$dirty'+
                        ', \'label-success\': ' + field + '.$valid'+
                        ', \'label-default\': ' + field + '.$dirty || '+field + '.$invalid';
//                console.log(field + ' : ' + field.length);
                message = '<span ng-show="' + field + '.$error.required">' + required + '</span>'+
                    '<span ng-show="'+field+'.$valid">'+ ok +'</span>'+
                    '<span ng-show="'+field+'.$error.url && !'+field+'.$error.required">'+url+'</span>'+
                    '<span ng-show="'+field+'.$error.email && !'+field+'.$error.required">'+email+'</span>'+
                    '<span ng-show="'+field+'.$error.minlength && !'+field+'.$error.required">'+min+'!</span>'+
                    '<span ng-show="'+field+'.$error.mask && !'+field+'.$error.required">'+mask+'!</span>'+
                    '<span ng-show="'+field+'.$error.maxlength && !'+field+'.$error.required">' +max+'!</span>',
                    customErrors = getCustomErrors(attr.$attr);

                for(custom in customErrors){
                    customName = customErrors[custom];
                    message += '<span ng-show="'+field+'.$error.'+customName+'">'+attr[custom]+'</span>';
                }

                var tpl = '<span class="label" ng-class="{' + ngClass + '}">' + message +'</span>';
                element.append(tpl);
            }
        }
    });

}());