var app = {
    view: {},
    library: {},
    
    showOverlay: function(){
        $("#translate_waiting").show();
    },

    hideOverlay: function(){
       $("#translate_waiting").hide();
    },


    start: function () {
        moment.locale("de");
        
        $.publish('app.start');
        
        app.router = new app._router();
        Backbone.history.start();   
        
        var topbar = new app.view.topbar();
        topbar.render();
    }
};
