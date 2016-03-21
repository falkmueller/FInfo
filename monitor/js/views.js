app.view.home = Backbone.View.extend({
    el: "#container",
    
    initialize: function() {
    },
    
    render: function(){
        app.library.GetData("test/test");
        
        this.$el.html(app.library.renderTemplate("home.html"));
    },
});

app.view.topbar = Backbone.View.extend({
    el: "#topbar",
    
    initialize: function(params) {
        this.params = params;
        
    },
    
    render: function(){
        this.$el.html(app.library.renderTemplate("topbar.html", {route: this.params}));
        $(document).foundation();
        this.setTime();
    },
    
    setTime: function(){
        var me = this;
        
        me.$("#time").html(moment().format("HH:mm"));
        
        if (this.timeout){
            clearTimeout(me.timeout);
        } else {
            $.publish('timer.minute');
        }
        
        me.timeout = setTimeout(function() {me.setTime();}, 1000 * 60 );
    }
});

app.view.notFound = Backbone.View.extend({
    el: "#container",
    
    initialize: function(params) {
        this.params = params;
    },
    
    render: function(){
        this.$el.html(app.library.renderTemplate("notFound.html", {route: this.params}));
    },
});

app.view.login = Backbone.View.extend({
    el: "#container",
    
     events: {
        'submit form': "login"
    },
    
    initialize: function(params) {
        this.params = params;
    },
    
    render: function(){
        this.$el.html(app.library.renderTemplate("login.html"));
    },
    
    login: function(event){
        var data = $( event.currentTarget ).serializeObject();
        
        var res = app.library.GetData("user/login", data, 'POST');
        if(res.success) {
            app.router.navigate(app.router.buildUrl("home"), true);
        }
        
        return false;
    }
});

app.view.logout = Backbone.View.extend({
    el: "#container",
    
    render: function(){
        app.library.GetData("user/logout");
        app.router.navigate(app.router.buildUrl("login"), true);
    },
});

app.view.user = Backbone.View.extend({
    el: "#container",
    
     events: {
        'submit form': "save"
    },
    
    render: function(){
        this.$el.html(app.library.renderTemplate("user.html"));
    },
    
    save: function(event){
        var data = $( event.currentTarget ).serializeObject();
        var res = app.library.GetData("user/edit", data, 'POST');
        return false;
    }
});