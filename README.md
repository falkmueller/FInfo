#FInfo PHP Server Monitoring

##Installation

###Clients
- copy Folder "client" to the Server, you want to monitoring
- open client/config.inc.php and set a secret password by "$settings['password']"

###Monitor
- copy Folder "monitor" to your Server, how wants to run the Monitor
- copy monitor/api/data/config.sample.php and rename to config.php
- set Database-Setting in this file
- run http://[url to monitor]/api/install in the browser
- browse http://[url to monitor]
- default Login is user "admin", password "test123"

##Links to uses Scripts
- used PHP Scripts:
    - Dtatabase Class by [joshcam](http://github.com/joshcam/PHP-MySQLi-Database-Class)
    - Server Stats by [linfo](https://github.com/jrgp/linfo)
- used Javascripts:
    - [jQuery](https://jquery.com/)
    - [jQuery Tiny Pub Sub](https://github.com/cowboy/jquery-tiny-pubsub)
    - [Backbone MVC](http://backbonejs.org/)
    - [Underscore Template](http://underscorejs.org/)
    - [moment.js](http://momentjs.com/)
    - [notifyjs](https://notifyjs.com/)
- used Style Framework:
    - [Foundation 6](http://foundation.zurb.com/)
    - [chartist JS](https://gionkunz.github.io/chartist-js/)