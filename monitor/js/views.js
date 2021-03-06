app.view.home = Backbone.View.extend({
    el: "#container",
    
    events: {
        'click a[data-site-refresh]': "refresh_site"
    },
    
    initialize: function() {
        /*refhesh stats in view after 10 Minutes*/
        if(!app.store.home_minute_handler){
            app.store.home_minute_handler = function(){
                if(app.store.minute_count > 0 && app.store.minute_count % 15 == 0){
                    if(app.router.current.view == "home"){
                        app.router.currentView.set_stats();
                    } else {
                        app.library.GetData("sites/fetchbasics")
                    }
                }
            }
            
            $.subscribe("timer.minute", app.store.home_minute_handler);
        }     
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
        this.ping_site(id)
        this.site_stats(id);
    },
    
    site_stats: function(id){
        var data = app.library.GetData("stat/basics", {id: id}).data;
         
        if(!data || !data.available){
            this.$("div[data-site-id=" + id + "] .site_cpu").html("kann nicht ermittelt werden");
            this.$("div[data-site-id=" + id + "] .site_ram").html("kann nicht ermittelt werden");
            this.$("div[data-site-id=" + id + "] .site_storage").html("kann nicht ermittelt werden");
            return false;
        } 
  
        if(data.load && data.load.now){
            this.$("div[data-site-id=" + id + "] .site_cpu").html(data.load.now + '%');
        } else {
            this.$("div[data-site-id=" + id + "] .site_cpu").html("kann nicht ermittelt werden");            
        }
        
        if(data.ram && data.ram.total && data.ram.free){
            var percent = Math.round((data.ram.total - data.ram.free) / data.ram.total * 100);
            this.$("div[data-site-id=" + id + "] .site_ram").html(percent + '%');
        } else {
            this.$("div[data-site-id=" + id + "] .site_ram").html("kann nicht ermittelt werden");            
        }
        
        if(data.space){
            if (data.space.total && data.space.free){
                var percent = Math.round((data.space.total - data.space.free) / data.space.total * 100) /100;
                this.$("div[data-site-id=" + id + "] .site_storage").html(percent + '%');
            }
            else if (data.space.free){
                this.$("div[data-site-id=" + id + "] .site_storage").html(parseInt(data.space.free / 1024 / 1024) + ' MB frei');
            } else {
                this.$("div[data-site-id=" + id + "] .site_storage").html("kann nicht ermittelt werden");
            }
        } else {
            this.$("div[data-site-id=" + id + "] .site_storage").html("kann nicht ermittelt werden");
        }
    },
    
    ping_site: function(id){
        var data = app.library.GetData("stat/ping", {id: id}).data;
        
        if(!data || !data.available){
            this.$("div[data-site-id=" + id + "]").addClass("home_row_error");
            return false;
        } 
        
        this.$("div[data-site-id=" + id + "]").removeClass("home_row_error");
        this.$("div[data-site-id=" + id + "] .site_check").html(moment().format("HH:mm"));
        
        if(data.title){
            this.$("div[data-site-id=" + id + "] .site_title").html(data.title);
        } else {
            this.$("div[data-site-id=" + id + "] .site_title").html("keine Angabe");
        }
        
        if(data.icon){
            this.$("div[data-site-id=" + id + "] .site_icon").attr("src", data.icon);
        }
        
        return true;
    },
    
    refresh_site: function(event){
        var id = parseInt($(event.currentTarget).attr("data-site-refresh"));
        this.load_stats(id);
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
        
        if(app.store.minute_count === undefined){
            app.store.minute_count = 0;
        } else {
            app.store.minute_count++;
        }
        
        me.$("#time").html(moment().format("HH:mm"));
        
        if (this.timeout){
            clearTimeout(me.timeout);
        } 
        
        $.publish('timer.minute');
        
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

app.view.site = Backbone.View.extend({
    el: "#container",
    
    initialize: function(route) {
       this.id = 0;
       if(route.params.id){
          this.id = route.params.id; 
       }
    },
    
    render: function(){
        var template_data = {};
        template_data.site = app.library.GetData("sites/get", {id: this.id}).data[0];       
        this.$el.html(app.library.renderTemplate("site.html", template_data));
    },

});