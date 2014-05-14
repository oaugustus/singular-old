(function()
{

    var Store = function($http, $q)
    {
        var url = './@package/@module/';

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

    angular.module('@package.@module').factory('@package.@module.Store', ['$http','$q', Store]);
}());