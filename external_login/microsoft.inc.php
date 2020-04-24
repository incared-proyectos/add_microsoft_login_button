<?php
/**
 * Licence: GPL
 * Please contact CBlue regarding any licences issues.
 * Author: noel@cblue.be
 *  Copyright: CBlue SPRL, 20XX.
 *
 * External login module : FACEBOOK
 *
 * This files provides the facebookConnect()  and facebook_get_url functions
 * Please edit the facebook.conf.php file to adapt it to your fb application parameter
 */
require_once __DIR__.'/../../inc/global.inc.php';
require_once __DIR__.'/microsoft.init.php';
require_once __DIR__.'/functions.inc.php';
$raiz_file = dirname(dirname(dirname(dirname(__FILE__))));
require_once $raiz_file.'/plugin/add_microsoft_login_button/microsoft/vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

/**
 * This function connect to facebook and retrieves the user info
 * If user does not exist in chamilo, it creates it and logs in
 * If user already exists, it updates his info.
 */
function microsoftConnect()
{

    $expectedState  = $_SESSION['auth_microsoft'];
    $providedState = $_GET['state'];

    $authCode = $_GET['code'];

    if (isset($authCode)) {
        // Initialize the OAuth client  
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => $GLOBALS['microsoft_config']['OAUTH_APP_ID'],
          'clientSecret'            => $GLOBALS['microsoft_config']['OAUTH_APP_PASSWORD'],
          'redirectUri'             => $GLOBALS['microsoft_config']['OAUTH_REDIRECT_URI'],
          'urlAuthorize'            => $GLOBALS['microsoft_config']['OAUTH_AUTHORITY'].$GLOBALS['microsoft_config']['OAUTH_AUTHORIZE_ENDPOINT'],
          'urlAccessToken'          => $GLOBALS['microsoft_config']['OAUTH_AUTHORITY'].$GLOBALS['microsoft_config']['OAUTH_TOKEN_ENDPOINT'],
          'urlResourceOwnerDetails' => '',
          'scopes'                  => $GLOBALS['microsoft_config']['OAUTH_SCOPES']
        ]);

        try {
            // Make the token request
            $accessToken = $oauthClient->getAccessToken('authorization_code', [
              'code' => $authCode
            ]);

            $graph = new Graph();
            $graph->setAccessToken($accessToken->getToken());

            $user = $graph->createRequest('GET', '/me')
              ->setReturnType(Model\User::class)
              ->execute();

            echo $user->getUserPrincipalName();

        }catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
          header('Location: '.api_get_path(WEB_PATH));
        }
    }else{
        header('Location: '.api_get_path(WEB_PATH));
    }

    if (!$language) {
        $language = 'en_US';
    }
    $email = $user->getMail() ? $user->getMail() : $user->getUserPrincipalName();

    $u = [
        'firstname' => $user->getGivenName(),
        'lastname' => $user->getSurName(),
        'status' => STUDENT,
        'email' => $email,
        'username' => changeToValidChamiloLogin($email),
        'language' => $language,
        'password' => 'microsoft',
        'auth_source' => 'microsoft',
        'extra' => [],
    ];
    $chamiloUinfo = api_get_user_info_from_email($email);

    $_user['uidReset'] = true;
    $_user['language'] = $language;

    if ($chamiloUinfo === false) {
        // We have to create the user
        $chamilo_uid = external_add_user($u);

        if ($chamilo_uid === false) {
            Display::addFlash(
                Display::return_message(get_lang('UserNotRegistered'), 'error')
            );

            header('Location: '.api_get_path(WEB_PATH));
            exit;
        }

        $_user['user_id'] = $chamilo_uid;
        $_SESSION['_user'] = $_user;

        header('Location: '.api_get_path(WEB_PATH));
        exit();
    }

    // User already exists, update info and login
    $chamilo_uid = $chamiloUinfo['user_id'];
    $u['user_id'] = $chamilo_uid;
    //external_update_user($u);
    $_user['user_id'] = $chamilo_uid;
    $_SESSION['_user'] = $chamiloUinfo;

    header('Location: '.api_get_path(WEB_PATH));
    exit();
}

/**
 * Get facebook login url for the platform.
 *
 * @return string
 */
function microsoftGetLoginUrl()
{
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => $GLOBALS['microsoft_config']['OAUTH_APP_ID'],
      'clientSecret'            => $GLOBALS['microsoft_config']['OAUTH_APP_PASSWORD'],
      'redirectUri'             => $GLOBALS['microsoft_config']['OAUTH_REDIRECT_URI'],
      'urlAuthorize'            => $GLOBALS['microsoft_config']['OAUTH_AUTHORITY'].$GLOBALS['microsoft_config']['OAUTH_AUTHORIZE_ENDPOINT'],
      'urlAccessToken'          => $GLOBALS['microsoft_config']['OAUTH_AUTHORITY'].$GLOBALS['microsoft_config']['OAUTH_TOKEN_ENDPOINT'],
      'urlResourceOwnerDetails' => '',
      'scopes'                  => $GLOBALS['microsoft_config']['OAUTH_SCOPES']
    ]);

    $_SESSION['auth_microsoft'] = $oauthClient->getState();
    $loginUrl = $oauthClient->getAuthorizationUrl();
    return $loginUrl;
}
