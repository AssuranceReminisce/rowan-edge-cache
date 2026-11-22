# Rowan Edge Cache: Cache & Performance

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759b?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb3?logo=php&logoColor=white)
![License: MIT](https://img.shields.io/badge/license-MIT-3da639)

Most speed plugins sell a dashboard. Rowan Edge Cache writes static HTML, strips the whitespace nobody reads, converts images to modern formats and prunes the tables that grow every day — then shows the counters it recorded while doing it.

## open source wordpress clear cache

That is the query this plugin answers. The sections below follow the same order an administrator works in: cache first, then the output, then the database, then the things that keep a site from getting slow again.

There are a dozen caching plugins and they mostly work. The differences that matter are the ones you notice later: what happens on a form submission, whether the purge follows a scheduled post, and whether the uninstall leaves 40 options in the database. Rowan Edge Cache is written with those three questions in mind.

## Markup and asset optimisation

Optimisation here means leaving the DOM alone and touching only what the browser does not need: whitespace, comments, the emoji shim, the embed script. Inline scripts and styles are untouched. If a page builder already minifies, switch the minifier off and keep the cache.

## WebP and AVIF on upload

Uploads are converted once, at upload time, not on the front end. The conversion writes sibling files, records them against the attachment, and serves them through a filter — so deactivating the plugin returns the site to the original images with no cleanup.

## 404 log and redirects

Any 404 is recorded with its path, referrer and hit count, grouped so a renamed URL shows up once with the count that proves it matters. One click turns an entry into a permanent redirect, and the cache is purged for the source path so the change takes effect immediately.

## Tools tab

Everything the plugin needs to know about its host in one screen, plus backups of the settings as a JSON file. Import validates the shape before writing, so a truncated export cannot leave the plugin in a half-configured state.

## SEO output

No keyword tooling, no content scoring — the parts of SEO that live in the page: sitemaps that validate, social cards with a fallback image, breadcrumb markup you can place with a shortcode, and JSON-LD for the site and for articles. Everything is filterable, so a custom post type is a few lines.

## Automation

If it has to run unattended it belongs in the CLI: cache purge, database cleanup, image conversion and sitemap rebuild all have a command and exit codes that make sense in a pipeline.

## The score tab

One tab sends a snapshot of the runtime — WordPress and PHP version, locale, multisite flag, theme name, plugin count — and gets back a 0–100 score with recommendations. It asks before the first request, keeps the answer for an hour, and the snapshot can be downloaded exactly as it is sent.

## Troubleshooting

Cache misses on a page that should be cached: check the exclusion list, then the cookie — a cart or session cookie bypasses the cache by design. A layout that changes after enabling minification: turn off the CSS/JS group and keep the whitespace collapse. Wrong image format served: the conversion is per attachment, so run the bulk action once.

## Screenshots

![01-dashboard](assets/screenshots/01-dashboard.png)


![02-cache](assets/screenshots/02-cache.png)


![03-optimize](assets/screenshots/03-optimize.png)


![04-database](assets/screenshots/04-database.png)

## Install

**Pick whichever fits your workflow**

| method | command |
|---|---|
| Download | `plugin.zip` from the [release page](https://github.com/AssuranceReminisce/rowan-edge-cache/releases), then upload it through **Plugins → Add New** |
| Git | `git clone https://github.com/AssuranceReminisce/rowan-edge-cache wp-content/plugins/rowan-edge-cache` and activate from the Plugins screen |
| WP-CLI | `wp plugin install https://github.com/AssuranceReminisce/rowan-edge-cache/releases/latest/download/plugin.zip --activate` |

The release asset and the `src/` tree are the same code; the ZIP is built from it.

## Configuration

Configuration lives in one screen with nine tabs. Start on Cache: set the TTL and the exclusions for shop and member pages. Then Optimize: leave minification on unless another plugin is already doing it. Database and SEO are safe at their defaults, and Tools exports the whole configuration as JSON once you are happy.

## Requirements

| | |
|---|---|
| WordPress | 6.0 or newer |
| PHP | 7.4 or newer (8.1+ recommended) |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Multisite | supported, per-site settings |
| WP-CLI | optional, `wp rowan-edge-cache cache purge` |

## Performance notes

Nothing here is a synthetic score. Render time is measured around the output buffer, hits are counted when a static file is served, and the image conversion tab reports the byte difference per attachment. If a number looks wrong, the raw counter is visible on the same screen.

## Frequently asked

**Is the code reviewed?** The source in `src/` is what ships in the release, the build is a zip of that tree, and `php -l` runs on every push. There is no compiled or obfuscated component.

**Does it handle Core Web Vitals?** It does not ship a field-data collector. It fixes the inputs that move LCP, CLS and INP — cached HTML, smaller images, deferred scripts — and leaves measurement to Lighthouse or Search Console.

**How large does the cache get?** Roughly the size of the HTML of your most visited pages — text, not images. The Cache tab reports the file count and the directory size, and you can cap it and purge on the oldest entries.

**Does it support PHP 8.3?** Yes, and 7.4 upwards. The plugin avoids syntax that would pin it to one version, which is why it runs on older shared hosts without a deprecation storm.

## Uninstall

Deactivating removes the cron events and stops caching immediately. Deleting the plugin through the admin runs the uninstall script, which drops the redirect table, empties `wp-content/cache/rowan-edge-cache` and deletes every option carrying the plugin prefix. Backups you created stay in `wp-content/uploads/rowan-edge-cache-backups`.

## License

MIT — see [LICENSE](LICENSE). If something here is wrong, say so in an issue: the README is meant to describe the plugin, not to sell it. Contribution rules are in [CONTRIBUTING.md](CONTRIBUTING.md).
