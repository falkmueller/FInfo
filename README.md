#LIinfo PHP Server Monitoring

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