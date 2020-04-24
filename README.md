Add Microsoft login button plugin
===

This plugin adds a button to allow users to log into Chamilo through their Microsoft account.

To display this button on your portal, you have to:
 
* enable the Microsoft authentication setting and configure it

*To begin, head over to the following site and sign in to your Office365 account: https://portal.azure.com/

*In the menu on the left, click “Azure Active Directory”.

*Under the “Manage” tab, click “App registrations”.

*From here take the following steps:

*Click “New Registration”

*Choose any application name e.g “bbb-endpoint”

*Set the Redirect URI to your campus url  (must be https): “https://hostname/”

*Click “Register”

*Once your application has been created, Under the “Overview” tab, copy your “Application (client) ID” into the OAUTH_APP_ID

*Finally, click the “Certificates & secrets” under the “Manage” tab

*From here take the following steps:

*Click “New client secret”

*Choose the “Never” option in the “Expires” option list

*Copy the value of your password into the OAUTH_APP_PASSWORD

* If facebook login is not configured uncomment because plugin depent of this route if facebook login is configured follow to next step.


```
//uncomment this in app/config/auth.conf.php

$facebook_config = array(
    'appId' => 'APPID',
    'secret' => 'secret app',
    'return_url' => api_get_path(WEB_PATH).'?action=fbconnect',
);
```

* Add the next code after facebook uncomment array the App ID and the Secret Key provided by Microsoft inside the app/config/auth.conf.php file


```
$microsoft_config = array(
    'OAUTH_APP_ID' => 'yourappid',
    'OAUTH_APP_PASSWORD' => 'your_app_password',
    'OAUTH_REDIRECT_URI' => api_get_path(WEB_PATH),
    'OAUTH_SCOPES' => 'openid profile offline_access user.read calendars.read',
    'OAUTH_AUTHORITY'=> 'https://login.microsoftonline.com/common',
    'OAUTH_AUTHORIZE_ENDPOINT'=> '/oauth2/v2.0/authorize',
    'OAUTH_TOKEN_ENDPOINT' => '/oauth2/v2.0/token',
    'return_url' => api_get_path(WEB_PATH),
);
```




* set the following line in your app/config/configuration.php 

```
$_configuration['facebook'] = 1;
```

*This plugin has been developed to be added to the login_top or login_bottom region in Chamilo, but you can put it in whichever region you want.


*Copy files of directory plugin external_login  to  /main/auth/external_login 


*Replace facebook.inc.php (Firts backup of this file) and copy microsoft.inc.php, microsoft.init.php


*Plugin was created based in facebook plugin