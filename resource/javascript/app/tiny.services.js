'use strict';

angular.module('tiny.services', []).
    service('ControllerChecker', function($controller){
        return {
            exists: function(controllerName) {
              if(typeof window[controllerName] == 'function') {
                return true;
              }
              try {
                $controller(controllerName);
                return true;
              } catch (error) {
                return !(error instanceof TypeError);
              }
            }
        }
    })