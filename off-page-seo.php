<?php
/**
 *  Plugin Name: Off Page SEO
 *  Plugin URI: https://offpageseo.io
 *  Description: Provides various tools to help you with the off-page SEO.
 *  Version: 3.0.3
 *  Author: Jakub Glos
 *  Author URI: http://www.nettermedia.cz
 *  License: GPLv3
 *  Text Domain: off-page-seo
 */

/* ACCESSING DIRECTLY */
if (!defined('ABSPATH')) {
    exit;
}

define('OPS_VERSION', '3.0.3');
define('OPS_URL', 'https://offpageseo.io/');
define('OPS_PLUGIN_DIR', __DIR__);
define('OPS_LISTENER_URL', get_admin_url());


if (!defined('OPS_DEBUG')) {
    define('OPS_DEBUG', false);
}

/* REQUIRE CLASSES */

// third part first
if (function_exists('str_get_html') == false) {
    require_once('lib/third-party/simple-html-dom/simple-html-dom.php');
}

// core classes
require_once('lib/ops.php');
require_once('lib/third-party/google/ops-google.php');
require_once('lib/third-party/exporter/export-data.php');
require_once('lib/ops-email.php');
require_once('lib/ops-ranking.php');
require_once('lib/ops-ajax.php');
require_once('lib/ops-listener.php');
require_once('lib/ops-functions.php');
require_once('lib/ops-cron.php');
require_once('lib/ops-reciprocal.php');
require_once('lib/ops-keyword.php');

// admin views
require_once('views/ops-dashboard.php');
require_once('views/ops-backlinks.php');
require_once('views/ops-search-console.php');
require_once('views/ops-tools.php');
require_once('views/ops-premium.php');
require_once('views/ops-settings.php');


/*
 * ACTIVATE
 */
register_activation_hook(__FILE__, 'ops_on_activate');
function ops_on_activate()
{
    require_once('lib/standalone/ops-install.php');
}
