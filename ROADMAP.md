# SearchRank stabilization roadmap

Target branch: `develop-1.2.4` — 17 October 2026

## Release policy
- Geeklog 2.2.2 minimum for maintained releases; PHP 5.6.40 syntax and PHP 8.1/8.3 validation required, per hostellerie/memorandum/geeklog-runtime-support-policy.md.
- Keep PostgreSQL for now: current Geeklog Core retains pgsql drivers, schema, upgrade scripts and plugin installers. Some independent plugins are MySQL-only, which does not establish Core removal.
- No official release before CI and functional acceptance. Never destroy existing logs during migration without explicit administrator action.

## 1.2.4 — immediate stabilization

### Landed, awaiting testing
- [x] Fix TTL conversion from days to Unix seconds.
- [x] Remove expired hit records before removing only orphan queries (do not erase recent history for an old term).
- [x] Escape the public block search-term HTML title attribute.
- [x] Require POST and Geeklog CSRF token for destructive clear-log operation.

### Blocking release checks
- [ ] Test PostgreSQL and MySQL purge with old-only, new-only, mixed-age, empty, and repeated records; consider table locks, query plan and SQL portability.
- [ ] Confirm Geeklog SEC_createToken / SEC_checkToken and the administration form work on Geeklog 2.2.2 with modern themes.
- [ ] Add strict integer bounds for page/start/limit and config parameters (ttl, maxresults, itemsperpage).
- [ ] Harden query display and URL handling; review all HTML escape contexts.
- [ ] Address possible concurrent duplicate inserts and colliding random hid values; evaluate suitable uniqueness constraints and an idempotent SQL migration.
- [ ] Validate bulk clear under PostgreSQL, including orphan hits; test existing installations and upgrades.
- [ ] Review SQL portability in aggregation, aliases, casting and non-deterministic ordering.
- [ ] Ensure searches do not record sensitive strings publicly; add privacy guardrails.
- [ ] Align installer metadata, plugin.json, compatibility checks, README, translations and release notes with tested minimum runtimes.
- [ ] Preserve existing data; back up before migrations and test rollback.

### Packaging and QA
- [ ] Add GitHub Actions: PHP 5.6/8.1/8.3 lint, PHP compatibility checks, metadata validation.
- [ ] Test clean install, upgrades, activate/deactivate/uninstall, left/right block, statistics, anonymous/admin users.
- [ ] Exercise multiple sites sharing files but using separate databases.
- [ ] Produce versioned installable `dist` artifact; verify archive layout and contents.

## 1.3.0 — administration
- [ ] Navigation consistent with memorandum/plugin-admin-navigation.md and plugin-admin-ux-guidelines.md.
- [ ] In-product getting started, configuration tooltips and responsive tables.
- [ ] Time windows, date filters, safe CSV export and stable sorting.
- [ ] Externalized versioned CSS and theme-independent UI.
- [ ] Explicit confirmation and success/error feedback for destructive operations.

## 1.4.0 — editorial intelligence
- [ ] Evaluate Geeklog search API before counting zero-result searches.
- [ ] New/trending queries and time-window comparisons.
- [ ] Privacy-sensitive query filtering and retention documentation.
- [ ] Read-only query metrics service for Hub; preserve separate responsibilities for Analytics, Search Console and Bing Webmaster.

## PostgreSQL decision
Retain support pending concrete compatibility testing and upstream Core decision. Do not delete `sql/pgsql_*.php` or disable PostgreSQL install paths based solely on independent plugins that use MySQL only.

## Source documents
- https://github.com/hostellerie/memorandum/blob/main/geeklog-runtime-support-policy.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-development-guide.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-admin-ux-guidelines.md
- https://github.com/hostellerie/memorandum/blob/main/seo-measurement-interoperability-guide.md
