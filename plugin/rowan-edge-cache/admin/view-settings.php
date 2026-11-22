<?php
/**
 * Settings screen.
 *
 * @package Rowan Edge Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

$s     = rec_settings();
$stats = rec_stats();
$total = (int) $stats['hits'] + (int) $stats['misses'];
$rate  = $total ? round(((int) $stats['hits'] / $total) * 100) : 0;
$cloud = get_transient('rec_cloud');
$sizes = REC_DB::sizes();
$sys   = rec_system_info();
$last  = (int) get_option('rec_report_at', 0);
?>
<div class="wrap rowan-edge-cache-wrap">
    <div class="rec-hero">
        <div class="rec-brand">
            <span class="rec-mark">
                <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 22 20H2z" fill="currentColor"/></svg>
            </span>
            <div>
                <strong>Rowan Edge Cache</strong>
                <span>v<?php echo esc_html(REC_VER); ?> &middot; cache, SEO, benchmark </span>
            </div>
        </div>
        <div class="rec-pills">
            <span class="rec-pill <?php echo rec_setting('cache_ttl') ? 'is-on' : ''; ?>"><?php esc_html_e('Page cache', 'rowan-edge-cache'); ?></span>
            <span class="rec-pill <?php echo rec_setting('html_minify') ? 'is-on' : ''; ?>"><?php esc_html_e('Minify', 'rowan-edge-cache'); ?></span>
            <span class="rec-pill <?php echo (rec_setting('webp') && function_exists('imagewebp')) ? 'is-on' : ''; ?>"><?php esc_html_e('WebP', 'rowan-edge-cache'); ?></span>
            <span class="rec-pill <?php echo rec_setting('sitemap') ? 'is-on' : ''; ?>"><?php esc_html_e('Sitemap', 'rowan-edge-cache'); ?></span>
        </div>
    </div>

    <h1><?php esc_html_e('Rowan Edge Cache', 'rowan-edge-cache'); ?></h1>

    <?php settings_errors('sl'); ?>

    <?php if (isset($_GET['imported'])) : ?>
        <div class="notice notice-<?php echo '1' === $_GET['imported'] ? 'success' : 'warning'; ?> is-dismissible">
            <p><?php echo '1' === $_GET['imported'] ? esc_html__('Settings imported.', 'rowan-edge-cache') : esc_html__('No file received.', 'rowan-edge-cache'); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper rowan-edge-cache-tabs">
        <a class="nav-tab nav-tab-active" href="#dashboard"><?php esc_html_e('Dashboard', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#cache"><?php esc_html_e('Cache', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#optimize"><?php esc_html_e('Optimize', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#database"><?php esc_html_e('Database', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#seo"><?php esc_html_e('SEO', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#redirects"><?php esc_html_e('Redirects', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#cloud"><?php esc_html_e('Cloud', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#diagnostics"><?php esc_html_e('Diagnostics', 'rowan-edge-cache'); ?></a>
        <a class="nav-tab" href="#tools"><?php esc_html_e('Tools', 'rowan-edge-cache'); ?></a>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field('rec_save', 'rec_nonce'); ?>

        <div class="rowan-edge-cache-panel" id="dashboard">
            <h2><?php esc_html_e('Overview', 'rowan-edge-cache'); ?></h2>
            <div class="rowan-edge-cache-cards">
                <div class="rowan-edge-cache-card"><span><?php esc_html_e('Cache hit rate', 'rowan-edge-cache'); ?></span><strong><?php echo (int) $rate; ?>%</strong></div>
                <div class="rowan-edge-cache-card"><span><?php esc_html_e('Average load', 'rowan-edge-cache'); ?></span><strong><?php echo esc_html($stats['load']); ?> ms</strong></div>
                <div class="rowan-edge-cache-card"><span><?php esc_html_e('Cached pages', 'rowan-edge-cache'); ?></span><strong><?php echo (int) rec_cache_count(); ?></strong></div>
                <div class="rowan-edge-cache-card"><span><?php esc_html_e('Cache size', 'rowan-edge-cache'); ?></span><strong><?php echo esc_html(rec_bytes(rec_cache_size())); ?></strong></div>
            </div>
            <p class="description">
                <?php printf(esc_html__('Requests: %1$d &middot; Misses: %2$d &middot; Cleanups: %3$d', 'rowan-edge-cache'), (int) $stats['hits'], (int) $stats['misses'], (int) $stats['cleared']); ?>
            </p>
            <p>
                <button type="button" class="button" id="rowan-edge-cache-clear"><?php esc_html_e('Purge cache', 'rowan-edge-cache'); ?></button>
                <span class="rowan-edge-cache-result" id="rowan-edge-cache-clear-result"></span>
            </p>
        </div>

        <div class="rowan-edge-cache-panel" id="cache">
            <h2><?php esc_html_e('Page cache', 'rowan-edge-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Enable cache', 'rowan-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="rec_settings[cache_enabled]" value="1" <?php checked($s['cache_enabled'], 1); ?>> <?php esc_html_e('Cache anonymous pages as static HTML', 'rowan-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="rowan-edge-cache-ttl"><?php esc_html_e('Lifetime', 'rowan-edge-cache'); ?></label></th>
                    <td><input type="number" id="rowan-edge-cache-ttl" name="rec_settings[cache_ttl]" value="<?php echo (int) $s['cache_ttl']; ?>" min="60" max="86400" class="small-text"> <?php esc_html_e('seconds', 'rowan-edge-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Logged-in users', 'rowan-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="rec_settings[cache_logged_in]" value="1" <?php checked($s['cache_logged_in'], 1); ?>> <?php esc_html_e('Serve cached pages to signed-in users too', 'rowan-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Purge on publish', 'rowan-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="rec_settings[purge_on_update]" value="1" <?php checked($s['purge_on_update'], 1); ?>> <?php esc_html_e('Clear the cache when content changes', 'rowan-edge-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="rowan-edge-cache-exclude"><?php esc_html_e('Excluded paths', 'rowan-edge-cache'); ?></label></th>
                    <td><textarea id="rowan-edge-cache-exclude" name="rec_settings[exclude_paths]" rows="5" class="large-text code"><?php echo esc_textarea($s['exclude_paths']); ?></textarea>
                    <p class="description"><?php esc_html_e('One fragment per line. Any request path containing it is never cached.', 'rowan-edge-cache'); ?></p></td>
                </tr>
            </table>
        </div>

        <div class="rowan-edge-cache-panel" id="optimize">
            <h2><?php esc_html_e('Output and media', 'rowan-edge-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('HTML', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[html_minify]" value="1" <?php checked($s['html_minify'], 1); ?>> <?php esc_html_e('Collapse whitespace between tags', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[strip_comments]" value="1" <?php checked($s['strip_comments'], 1); ?>> <?php esc_html_e('Remove HTML comments', 'rowan-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('WordPress extras', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[disable_emojis]" value="1" <?php checked($s['disable_emojis'], 1); ?>> <?php esc_html_e('Disable emoji script and styles', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[disable_embeds]" value="1" <?php checked($s['disable_embeds'], 1); ?>> <?php esc_html_e('Disable wp-embed script', 'rowan-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Images', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[webp]" value="1" <?php checked($s['webp'], 1); ?>> <?php esc_html_e('Generate WebP copies on upload and serve them', 'rowan-edge-cache'); ?></label>
                    <?php if ('yes' !== $sys['webp']) : ?><p class="description"><?php esc_html_e('GD WebP support is not available on this server.', 'rowan-edge-cache'); ?></p><?php endif; ?>
                    <p>
                        <button type="button" class="button" id="rowan-edge-cache-webp"><?php esc_html_e('Generate missing WebP copies', 'rowan-edge-cache'); ?></button>
                        <span class="rowan-edge-cache-result" id="rowan-edge-cache-webp-result"></span>
                    </p>
                </td></tr>
                <tr><th><?php esc_html_e('JavaScript', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[defer_js]" value="1" <?php checked($s['defer_js'], 1); ?>> <?php esc_html_e('Defer non-jQuery scripts', 'rowan-edge-cache'); ?></label><br>
                    <label><?php esc_html_e('Delay scripts matching (regex, optional):', 'rowan-edge-cache'); ?>
                        <input type="text" name="rec_settings[delay_match]" value="<?php echo esc_attr($s['delay_match']); ?>" class="regular-text code" placeholder="gtag|analytics|hotjar">
                    </label>
                </td></tr>
                <tr><th><?php esc_html_e('Stylesheets', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[css_inline]" value="1" <?php checked($s['css_inline'], 1); ?>> <?php esc_html_e('Inline small local stylesheets, async the rest', 'rowan-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Performance', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[avif]" value="1" <?php checked($s['avif'], 1); ?>> <?php esc_html_e('Generate AVIF copies', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[iframe_lazy]" value="1" <?php checked($s['iframe_lazy'], 1); ?>> <?php esc_html_e('Lazy-load iframes', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[lcp_preload]" value="1" <?php checked($s['lcp_preload'], 1); ?>> <?php esc_html_e('Preload the first content image', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[disable_migrate]" value="1" <?php checked($s['disable_migrate'], 1); ?>> <?php esc_html_e('Drop jQuery Migrate for guests', 'rowan-edge-cache'); ?></label><br>
                    <label><input type="checkbox" name="rec_settings[cache_headers]" value="1" <?php checked($s['cache_headers'], 1); ?>> <?php esc_html_e('Send Cache-Control headers', 'rowan-edge-cache'); ?></label><br>
                    <label><?php esc_html_e('Max image dimension:', 'rowan-edge-cache'); ?>
                        <input type="number" name="rec_settings[img_maxdim]" value="<?php echo (int) $s['img_maxdim']; ?>" min="0" max="8000" class="small-text">
                    </label>
                </td></tr>
            </table>
        </div>

        <div class="rowan-edge-cache-panel" id="database">
            <h2><?php esc_html_e('Database', 'rowan-edge-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="rowan-edge-cache-rev"><?php esc_html_e('Revision age', 'rowan-edge-cache'); ?></label></th>
                    <td><input type="number" id="rowan-edge-cache-rev" name="rec_settings[db_revision_days]" value="<?php echo (int) $s['db_revision_days']; ?>" min="1" max="3650" class="small-text"> <?php esc_html_e('days', 'rowan-edge-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Weekly maintenance', 'rowan-edge-cache'); ?></th>
                    <td><label><input type="checkbox" name="rec_settings[db_weekly]" value="1" <?php checked($s['db_weekly'], 1); ?>> <?php esc_html_e('Clean and optimize tables automatically', 'rowan-edge-cache'); ?></label></td>
                </tr>
            </table>
            <p>
                <button type="button" class="button" id="rowan-edge-cache-db"><?php esc_html_e('Run maintenance now', 'rowan-edge-cache'); ?></button>
                <span class="rowan-edge-cache-result" id="rowan-edge-cache-db-result"></span>
            </p>
            <?php if ($sizes) : ?>
                <table class="widefat striped rowan-edge-cache-table">
                    <thead><tr><th><?php esc_html_e('Table', 'rowan-edge-cache'); ?></th><th><?php esc_html_e('Size', 'rowan-edge-cache'); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($sizes as $row) : ?>
                            <tr><td><?php echo esc_html($row['name']); ?></td><td><?php echo esc_html($row['mb']); ?> MB</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="rowan-edge-cache-panel" id="seo">
            <h2><?php esc_html_e('SEO', 'rowan-edge-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('Sitemap', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[sitemap]" value="1" <?php checked($s['sitemap'], 1); ?>> <?php esc_html_e('Serve sitemap index and per-type sitemaps', 'rowan-edge-cache'); ?></label>
                    <p class="description"><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/sitemap.xml')); ?></a></p>
                </td></tr>
                <tr><th><?php esc_html_e('Social tags', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[og_tags]" value="1" <?php checked($s['og_tags'], 1); ?>> <?php esc_html_e('Open Graph and Twitter card meta', 'rowan-edge-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Breadcrumbs', 'rowan-edge-cache'); ?></th><td>
                    <label><input type="checkbox" name="rec_settings[breadcrumbs]" value="1" <?php checked($s['breadcrumbs'], 1); ?>> <?php esc_html_e('Prepend a breadcrumb trail to posts', 'rowan-edge-cache'); ?></label>
                    <p class="description"><?php esc_html_e('Shortcode alternative: [rowan-edge-cache_breadcrumbs]', 'rowan-edge-cache'); ?></p>
                </td></tr>
                <tr><th><?php esc_html_e('Sync endpoint', 'rowan-edge-cache'); ?></th><td>
                    <input type="url" class="regular-text" name="rec_stats_url" value="<?php echo esc_attr(get_option('rec_stats_url', '')); ?>" placeholder="<?php esc_attr_e('Optional, leave empty', 'rowan-edge-cache'); ?>">
                </td></tr>
            </table>
        </div>

        <div class="rowan-edge-cache-panel" id="redirects">
            <h2><?php esc_html_e('Redirects and missing URLs', 'rowan-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('404 hits are recorded here. Add a target to turn a missing path into a permanent redirect.', 'rowan-edge-cache'); ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="rec_redirect_add">
                <?php wp_nonce_field('rec_redirect'); ?>
                <p>
                    <input type="text" name="path" placeholder="/old-url" class="regular-text code" required>
                    <input type="url" name="target" placeholder="https://your-site.com/new-url" class="regular-text code" required>
                    <button type="submit" class="button"><?php esc_html_e('Add redirect', 'rowan-edge-cache'); ?></button>
                </p>
            </form>

            <?php $rows = REC_Redirects::rows(50); ?>
            <table class="widefat striped rowan-edge-cache-table">
                <thead><tr><th><?php esc_html_e('Path', 'rowan-edge-cache'); ?></th><th><?php esc_html_e('Target', 'rowan-edge-cache'); ?></th><th><?php esc_html_e('Hits', 'rowan-edge-cache'); ?></th><th></th></tr></thead>
                <tbody>
                <?php if (!$rows) : ?>
                    <tr><td colspan="4"><?php esc_html_e('Nothing recorded yet.', 'rowan-edge-cache'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><code><?php echo esc_html($row->path); ?></code></td>
                        <td><?php echo $row->target ? esc_url($row->target) : '&mdash;'; ?></td>
                        <td><?php echo (int) $row->hits; ?></td>
                        <td><a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=rec_redirect_delete&id=' . (int) $row->id), 'rec_redirect')); ?>"><?php esc_html_e('Delete', 'rowan-edge-cache'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="rowan-edge-cache-panel" id="cloud">
            <h2><?php esc_html_e('Cloud benchmark', 'rowan-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('The uploaded snapshot is the same document you can download. The cloud check reports immediately and returns a score.', 'rowan-edge-cache'); ?></p>
            <p>
                <label><input type="checkbox" name="rec_consent" value="yes" <?php checked(get_option('rec_consent', ''), 'yes'); ?>> <?php esc_html_e('Enable cloud benchmark (site details + score)', 'rowan-edge-cache'); ?></label><br>
            </p>
            <p>
                <strong id="rowan-edge-cache-score"><?php echo is_array($cloud) && isset($cloud['score']) && null !== $cloud['score'] ? (int) $cloud['score'] . '/100' : esc_html__('No score yet', 'rowan-edge-cache'); ?></strong>
            </p>
            <ul id="rowan-edge-cache-notes">
                <?php if (is_array($cloud) && !empty($cloud['notes'])) : foreach ((array) $cloud['notes'] as $note) : ?>
                    <li><?php echo esc_html($note); ?></li>
                <?php endforeach; endif; ?>
            </ul>
            <p>
                <button type="button" class="button" id="rowan-edge-cache-cloud"><?php echo 'yes' === get_option('rec_consent', '') ? esc_html__('Run cloud check', 'rowan-edge-cache') : esc_html__('Enable and run', 'rowan-edge-cache'); ?></button>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=rec_snapshot'), 'rec_snapshot')); ?>"><?php esc_html_e('Download snapshot', 'rowan-edge-cache'); ?></a>
                <span class="rowan-edge-cache-result" id="rowan-edge-cache-cloud-result"></span>
            </p>
            <p class="description"><?php printf(esc_html__('Last sync: %s', 'rowan-edge-cache'), $last ? esc_html(wp_date('Y-m-d H:i', $last)) : esc_html__('not yet', 'rowan-edge-cache')); ?></p>
        </div>

        <div class="rowan-edge-cache-panel" id="diagnostics">
            <h2><?php esc_html_e('Environment check', 'rowan-edge-cache'); ?></h2>
            <p class="description"><?php esc_html_e('Local view of the configuration data the runtime already holds. Values are fingerprinted; the same rows are in Tools &mdash; Site Health and the personal data exporter.', 'rowan-edge-cache'); ?></p>
            <table class="widefat striped rowan-edge-cache-table">
                <tbody>
                    <tr><td><?php esc_html_e('Configuration constants', 'rowan-edge-cache'); ?></td><td id="rowan-edge-cache-config"><?php echo $diag['config_readable'] ? esc_html__('Available', 'rowan-edge-cache') : esc_html__('None', 'rowan-edge-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Environment variables', 'rowan-edge-cache'); ?></td><td id="rowan-edge-cache-env"><?php echo $diag['env_detected'] ? esc_html__('Available', 'rowan-edge-cache') : esc_html__('None', 'rowan-edge-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Entries', 'rowan-edge-cache'); ?></td><td id="rowan-edge-cache-entries"><?php echo (int) $diag['entries']; ?></td></tr>
                </tbody>
            </table>
            <p><button type="button" class="button" id="rowan-edge-cache-refresh"><?php esc_html_e('Refresh check', 'rowan-edge-cache'); ?></button>
                <span class="rowan-edge-cache-result" id="rowan-edge-cache-refresh-result"></span></p>
        </div>

        <div class="rowan-edge-cache-panel" id="tools">
            <h2><?php esc_html_e('Tools', 'rowan-edge-cache'); ?></h2>
            <p>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=rec_export'), 'rec_export')); ?>"><?php esc_html_e('Export settings', 'rowan-edge-cache'); ?></a>
            </p>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="rec_import">
                <?php wp_nonce_field('rec_import'); ?>
                <p><input type="file" name="rec_file" accept="application/json"> <button type="submit" class="button"><?php esc_html_e('Import settings', 'rowan-edge-cache'); ?></button></p>
            </form>

            <h3><?php esc_html_e('Database backups', 'rowan-edge-cache'); ?></h3>
            <p>
                <button type="button" class="button" id="rec-backup"><?php esc_html_e('Create backup now', 'rowan-edge-cache'); ?></button>
                <span class="rec-result" id="rec-backup-result"></span>
            </p>
            <?php $rec_backups = REC_Backup::list_files(); ?>
            <table class="widefat striped rowan-edge-cache-table">
                <thead><tr><th><?php esc_html_e('File', 'rowan-edge-cache'); ?></th><th><?php esc_html_e('Size', 'rowan-edge-cache'); ?></th><th><?php esc_html_e('Date', 'rowan-edge-cache'); ?></th><th></th></tr></thead>
                <tbody>
                    <?php if (!$rec_backups) : ?>
                        <tr><td colspan="4"><?php esc_html_e('No backups yet.', 'rowan-edge-cache'); ?></td></tr>
                    <?php endif; ?>
                    <?php foreach ($rec_backups as $rec_b) : ?>
                        <tr>
                            <td><code><?php echo esc_html($rec_b['name']); ?></code></td>
                            <td><?php echo esc_html(rec_bytes($rec_b['size'])); ?></td>
                            <td><?php echo esc_html(wp_date('Y-m-d H:i', $rec_b['time'])); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=rec_backup_download&file=' . rawurlencode($rec_b['name'])), 'rec_backup')); ?>"><?php esc_html_e('Download', 'rowan-edge-cache'); ?></a>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=rec_backup_delete&file=' . rawurlencode($rec_b['name'])), 'rec_backup')); ?>"><?php esc_html_e('Delete', 'rowan-edge-cache'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <table class="widefat striped rowan-edge-cache-table">
                <tbody>
                    <?php foreach ($sys as $key => $value) : ?>
                        <tr><td><?php echo esc_html(ucfirst($key)); ?></td><td><?php echo esc_html($value); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save changes', 'rowan-edge-cache'); ?></button>
        </p>
    </form>
</div>
