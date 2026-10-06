<?php
$config = array();
$config['PLUGIN_NAME'] = 'pjStripe';
$config['PLUGIN_MODEL'] = 'pjStripeModel';
$config['PLUGIN_DIR'] = 'app/plugins/' . $config['PLUGIN_NAME'] . '/';
$config['PLUGIN_CONTROLLERS_PATH'] = $config['PLUGIN_DIR'] . 'controllers/';
$config['PLUGIN_MODELS_PATH'] = $config['PLUGIN_DIR'] . 'models/';
$config['PLUGIN_VIEWS_PATH'] = $config['PLUGIN_DIR'] . 'views/';
$config['PLUGIN_COMPONENTS_PATH'] = $config['PLUGIN_CONTROLLERS_PATH'] . 'components/';
$config['PLUGIN_WEB_PATH'] = $config['PLUGIN_DIR'] . 'web/';
$config['PLUGIN_IMG_PATH'] = $config['PLUGIN_WEB_PATH'] . 'img/';
$config['PLUGIN_CSS_PATH'] = $config['PLUGIN_WEB_PATH'] . 'css/';
$config['PLUGIN_JS_PATH'] = $config['PLUGIN_WEB_PATH'] . 'js/';
$config['PLUGIN_LIBS_PATH'] = $config['PLUGIN_WEB_PATH'] . 'libs/';

$config['PLUGIN_ID'] = "315";
$config['PLUGIN_VERSION'] = "1.1";
$config['PLUGIN_BUILD'] = "1.1.0";

// Stripe REST API base URL (only change it for local testing against a mock server)
if (!defined('PJ_STRIPE_API_BASE'))
{
	define('PJ_STRIPE_API_BASE', 'https://api.stripe.com');
}

$registry = pjRegistry::getInstance();
$registry->set($config['PLUGIN_NAME'], $config);
$plugins = $registry->get('plugins');
if (is_null($plugins))
{
	$plugins = array();
}
$plugins[$config['PLUGIN_NAME']] = array('pjStripe');
$registry->set('plugins', $plugins);

unset($config);
unset($plugins);
?>
