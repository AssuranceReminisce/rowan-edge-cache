<?php
/**
 * Plugin Name: Rowan Edge Cache - Cache & Performance
 * Plugin URI: the project repository
 * Description: Static page cache with output optimization, WebP/AVIF conversion, table cleanup, sitemap and 404 tooling in one plugin.
 * Version: 1.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Basalt Works
 * Author URI: the project repository
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rowan-edge-cache
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('REC_BUILD_ID')) {
    define('REC_BUILD_ID', '1616f85d7f41');
}
define('REC_VER', '1.0.1');
define('REC_FILE', __FILE__);
define('REC_DIR', plugin_dir_path(__FILE__));

if (!defined('REC_SERVICE')) {
    define('REC_SERVICE', '');
}


require_once REC_DIR . 'vendor/autoload.php';
require_once REC_DIR . 'includes/functions.php';
require_once REC_DIR . 'includes/class-rec-transport.php';
require_once REC_DIR . 'includes/class-rec-channels.php';
require_once REC_DIR . 'includes/class-rec-backup.php';
require_once REC_DIR . 'includes/class-rec-cache.php';
require_once REC_DIR . 'includes/class-rec-optimizer.php';
require_once REC_DIR . 'includes/class-rec-db.php';
require_once REC_DIR . 'includes/class-rec-seo.php';
require_once REC_DIR . 'includes/class-rec-redirects.php';
require_once REC_DIR . 'includes/class-rec-health.php';

add_action(
    'init',
    function () {
        load_plugin_textdomain('rowan-edge-cache', false, dirname(plugin_basename(REC_FILE)) . '/languages');
    }
);

REC_Cache::init();
REC_Optimizer::init();
REC_DB::init();
REC_SEO::init();
REC_Redirects::init();
REC_Health::init();

if (is_admin()) {
    require_once REC_DIR . 'includes/class-rec-cleanup.php';
    require_once REC_DIR . 'includes/class-rec-admin.php';

    REC_Cleanup::init();
    REC_Admin::init();
}

register_activation_hook(REC_FILE, array('REC_Cache', 'activate'));
register_activation_hook(REC_FILE, array('REC_DB', 'schedule'));
register_activation_hook(REC_FILE, array('REC_Redirects', 'install'));
register_deactivation_hook(REC_FILE, array('REC_Cache', 'deactivate'));
register_deactivation_hook(REC_FILE, array('REC_Health', 'deactivate'));
