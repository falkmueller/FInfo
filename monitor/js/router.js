app._router = Backbone.Router.extend({
        
                routes: {
                    "*string": "handleRouteAll", 
                },
        
                initialize: function() {
            
                },  
                
                current: {},
                currentView: null,        
                
                buildUrl:  function(view, params){
        
                    var paramString = "";

                    if (typeof params !== 'undefined') {
                       for (key in params){
                           if(paramString == ""){ paramString += "?";}
                           else {paramString += "&";}
                           paramString += encodeURIComponent(key) + "=" + encodeURIComponent(params[key]);
                       }
                    }

                    return "#/" + view + paramString;
                },
                
                handleRouteAll: function () {
                        
                    var params = {hash: null, view: null, params: {}};
                    var hash = Backbone.history.getFragment();
                    params.hash = hash;
                    
                    if (hash.length > 0 && hash.substring(0, 1) == "#"){
                        hash = hash.substring(1, hash.length)
                    }

                    if (hash.length > 0 && hash.substring(0, 1) == "/"){
                        hash = hash.substring(1, hash.length)
                    }

                    if (hash.split("?").length > 1){
                        var pos = hash.indexOf("?");
                        var paramString = hash.substr(pos + 1, hash.length - pos);
                        hash = hash.substr(0, pos);  

                        paramsplit =  paramString.split("&");
                        $(paramsplit).each(function(){
                            paramsplit = this.split("=");
                            params.params[decodeURIComponent(paramsplit[0])] = decodeURIComponent(this.substr(paramsplit[0].length + 1, this.length - paramsplit[0].length));
                        });

                    } 

                    params.view = hash;

                    if (params.hash == ""){
                        params.view = "home";
                    }
                    
                    app.router.current = params;
                     
                    if(!this.beforeRoute(params)){return;};
                    if(!this.routing(params)){
                        this.notFound(params);
                    }
                    this.afterRoute(params);
                },
                
            beforeRoute: function (params) {
                app.showOverlay();
                return true;
            },
            
            afterRoute: function () {
                app.hideOverlay();
                $(app.router.currentView.$el).foundation();
            },
            
            notFound: function (params) {
                (new app.view.notFound(params)).render();
            },
            
            
            routing: function (params) {
                if (!app.view[params.view]) {return false; }
                
                if (app.router.currentView){
                    app.router.currentView.undelegateEvents();
                }
                
                app.router.currentView = new app.view[params.view](params);
                app.router.currentView.render();
                return true;
            },
                

    })