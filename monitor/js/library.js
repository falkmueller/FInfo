app.library.renderTemplate = function(filename, data) {
    var template = app.library.GetTemplate(filename);
    var template    = _.template(template);
    return  template(data);
}

app.library.GetTemplate = function(filename){
    var template = "";
    $.ajax({
        url: 'templates/' + filename,
            dataType: "text",
            async: false,
            cache: true,
            success: function(data){
                template = data;
            }
         });
    return template;
}

app.library.GetData = function(url, data, method){
    
    if (typeof method === "undefined") {
        method = 'GET';
    }
    
    if (typeof data === "undefined") {
        data = {};
    }
    
    var ReturnValue = "";
    $.ajax({
            url: config.api_url + url,
            method: method,
            data: data,
            async: false,
            cache: false,
            dataType: "json",
            success: function(data){
                ReturnValue = data;
            }
        });

    app.library.handleServerReturnValue(ReturnValue);    

    return ReturnValue;
}

app.library.alert = function(message, type){
    if (typeof type === 'undefined'){
        type = "warn";
    }
    
    $.notify(message, type);
}

app.library.handleServerReturnValue = function(ReturnValue){
    if (ReturnValue.message && ReturnValue.message.length > 0) {
        app.library.alert(ReturnValue.message, (ReturnValue.success ? "success" : "warn"));
    }

    if (ReturnValue.functionCalls && ReturnValue.functionCalls.length > 0) {
        for (i = 0; i < ReturnValue.functionCalls.length; i++) {
            app.library.executeFunctionByName(ReturnValue.functionCalls[i].functionName, window, ReturnValue.functionCalls[i].parameters);
        }
    }
}

app.library.executeFunctionByName = function(functionName, context, args) {
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    return context[func].apply(this, args);
}

app.library.redirect = function(url, wait) {
    if (!wait) { wait = 0; }
    setTimeout(function () {app.router.navigate(url, true); }, wait);
}

app.library.reload =  function(delay) {
    if (!delay) {delay = 0;}
    setTimeout(function(){window.location.reload()}, delay);
}

/*Extension #########################################################*/
$.fn.serializeObject = function()
{
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};