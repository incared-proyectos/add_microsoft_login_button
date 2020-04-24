<?php
// Show the microsoft login button
$_template['show_message'] = false;

//if (api_is_anonymous() && api_get_setting('microsoft_login_activate') == 'true') {
if (api_is_anonymous()) {
    if (isset($_GET['authlogin']) ) {
        exit();
    }
    require_once api_get_path(SYS_CODE_PATH)."auth/external_login/microsoft.inc.php";
    $_template['show_message'] = true;
    // the default title
    $button_url = api_get_path(WEB_PLUGIN_PATH)."add_microsoft_login_button/img/microsoft.png";
    $href_link = microsoftGetLoginUrl();
    if (!empty($plugin_info['settings']['add_microsoft_login_button_microsoft_button_url'])) {
        $button_url = api_htmlentities($plugin_info['settings']['add_microsoft_login_button_microsoft_button_url']);
    }
    $_template['microsoft_button_url'] = $button_url;
    $_template['microsoft_href_link'] = $href_link;
}
