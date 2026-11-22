<?php
/**
 * Uninstall routine.
 *
 * @package Rowan Edge Cache
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

foreach (array('rec_stats', 'rec_stats_url', 'rec_report_at', 'rec_install_at', 'rec_settings', 'rec_consent', 'rec_consent_secrets', 'rec_report_v', 'rec_touch', 'rec_metrics_due') as $option) {
    delete_option($option);
}

foreach (array('rec_seen', 'rec_due', 'rec_sent', 'rec_since', 'rec_env_check', 'rec_cloud', 'rec_policy') as $key) {
    delete_transient($key);
}

foreach (array('rec_cache_expire', 'rec_cache_preload', 'rec_db_maintenance') as $hook) {
    wp_clear_scheduled_hook($hook);
}

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'rowan-edge-cache_redirects');
delete_option('rec_redirects_db');

$dir = WP_CONTENT_DIR . '/cache/rowan-edge-cache';

foreach ((array) glob($dir . '/*') as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}

if (is_dir($dir)) {
    @rmdir($dir);
}
