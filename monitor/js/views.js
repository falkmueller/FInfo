app.view.home = Backbone.View.extend({
    el: "#container",
    
    initialize: function() {
    },
    
    render: function(){
        var template_data = {};
        template_data.sites = app.library.GetData("sites/get").data;
        
        this.$el.html(app.library.renderTemplate("home.html", template_data));
        
        this.set_stats();
    },
    
    set_stats: function(){
        var me = this;
        this.$("[data-site-id]").each(function(c, elem){
                me.load_stats(parseInt($(elem).attr("data-site-id")));
            });
    },
    
    load_stats: function(id){
        var data = app.library.GetData("stat/ping", {id: id}).data;
        
        if(!data || !data.available){
            //TODO: Fehler im Frontend markieren
            return;
        }
        
        this.$("div[data-site-id=" + id + "] .site_check").html(moment().format("HH:mm"));
        
        if(data.title){
            this.$("div[data-site-id=" + id + "] .site_title").html(data.title);
        } else {
            this.$("div[data-site-id=" + id + "] .site_title").html("keine Angabe");
        }
        
        if(data.icon){
            this.$("div[data-site-id=" + id + "] .site_icon").attr("src", data.icon);
        }
    }
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

app.view.sites = Backbone.View.extend({
    el: "#container",
    
     events: {
        'click a[data-delete]': "delete",
    },
    
    render: function(){
        var template_data = {};
        template_data.sites = app.library.GetData("sites/get").data;       
        this.$el.html(app.library.renderTemplate("sites.html", template_data));
    },
    
    delete: function(event){
        var id = parseInt($(event.currentTarget).attr("data-delete"));
        
        app.library.GetData("sites/delete", {id: id});
        app.router.navigate(app.router.buildUrl("sites"), true);
    }
});

app.view.sites_edit = Backbone.View.extend({
    el: "#container",
    
    initialize: function(route) {
       this.id = 0;
       if(route.params.id){
          this.id = route.params.id; 
       }
    },
    
     events: {
        'submit form': "save"
    },
    
    render: function(){
        var template_data = {};
        
        if(this.id){
            template_data.site = app.library.GetData("sites/get", {id: this.id}).data[0];    
        } else {
            template_data.site = {};
        }
        
        this.$el.html(app.library.renderTemplate("sites_edit.html", template_data));
    },
    
    save: function(event){
        var data = $( event.currentTarget ).serializeObject();
        if(this.id){
            data.id = this.id;
        }
        
        var res = app.library.GetData("sites/save", data, 'POST');
        
        if(res.success){
            app.router.navigate(app.router.buildUrl("sites"), true);
        }
        
        return false;
    }
});