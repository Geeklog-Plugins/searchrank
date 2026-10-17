# SearchRank stabilization roadmap

Target branch: `develop-1.2.4` — 17 October 2026

## Release policy
- Geeklog 2.2.2 minimum for *supported/tested* releases by current memorandum policy; PHP 5.6.40 syntax and PHP 8.1/8.3 validation. This is a support-policy floor, not proof that SearchRank 1.2.4 technically requires APIs introduced in 2.2.2. Historical SearchRank 1.2.1 declared Geeklog 2.1.3. ZIP filename retains `_2.1.3` at user request but installer still declares 2.2.2.
- PostgreSQL support removed by request in 1.2.4; MySQL-compatible Geeklog database only. Upgrading PostgreSQL installations is unsupported; export historical data before changing database engines.
- No official release before CI and functional acceptance. Never destroy existing logs during migration without explicit administrator action.

## 1.2.4 — immediate stabilization

### Landed, awaiting testing
- [x] Fix undefined `_DB_table_prefix` and scoped configuration globals when Geeklog loads functions.inc during plugin activation.
- [x] User confirms successful upgrade from SearchRank 1.2.1 to 1.2.4 on Geeklog 2.2.2 (2026-10-17).
- [ ] Confirm disable/enable flow and preservation of existing data in separate multisite installations.
- [x] Fix TTL conversion from days to Unix seconds.
- [x] Remove expired hit records before removing only orphan queries (do not erase recent history for an old term).
- [x] Escape the public block search-term HTML title attribute.
- [x] Require POST and Geeklog CSRF token for destructive clear-log operation.

### Blocking release checks
- [ ] Test MySQL purge with old-only, new-only, mixed-age, empty, and repeated records; consider table locks, query plan and SQL portability.
- [ ] Confirm Geeklog SEC_createToken / SEC_checkToken and the administration form work on Geeklog 2.2.2 with modern themes.
- [x] Bound page/start/limit and clamp log page size to 1–200; add stable ranking ordering.
- [ ] Validate remaining config parameters (ttl, maxresults, itemsperpage) on save and use.
- [ ] Harden query display and URL handling; review all HTML escape contexts.
- [ ] Address possible concurrent duplicate inserts and colliding random hid values; evaluate suitable uniqueness constraints and an idempotent SQL migration.
- [ ] Validate bulk clear under MySQL, including orphan hits; test existing installations and upgrades.
- [ ] Review MySQL aggregation, aliases, casting and non-deterministic ordering.
- [ ] Ensure searches do not record sensitive strings publicly; add privacy guardrails.
- [ ] Align installer metadata, plugin.json, compatibility checks, README, translations and release notes with tested minimum runtimes.
- [ ] Preserve existing data; back up before migrations and test rollback.

### Packaging and QA
- [x] Add GitHub Actions PHP 5.6/8.1/8.3 lint and distributable archive (first CI runs pending).
- [ ] Add PHP compatibility/static checks and metadata validation.
- [ ] Test clean install, upgrades, activate/deactivate/uninstall, left/right block, statistics, anonymous/admin users.
- [ ] Exercise multiple sites sharing files but using separate databases.
- [x] Configure CI to produce versioned `dist` archive.
- [x] GitHub Actions run 38992700770 succeeded and committed `dist/searchrank_1.2.4_2.1.3.zip`.
- [x] User reports successful upgrade from 1.2.1 on Geeklog 2.2.2.
- [ ] Retest archive after subsequent fixes and test fresh installation.

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
PostgreSQL support is deliberately removed in 1.2.4. SQL installation and upgrade files are deleted; installer compatibility check rejects unsupported database types. This does not remove existing PostgreSQL tables on remote installations. Database migrations require separate backup/export and are not automatic.

## Source documents
- https://github.com/hostellerie/memorandum/blob/main/geeklog-runtime-support-policy.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-development-guide.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-admin-ux-guidelines.md
- https://github.com/hostellerie/memorandum/blob/main/seo-measurement-interoperability-guide.md
