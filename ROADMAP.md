# SearchRank 1.3.0 release roadmap

Development integration branch: `develop-1.2.4` (temporary historical name) — next public release: **1.3.0**, no 1.2.4 release.

## Release policy
- Geeklog 2.1.3 minimum declared (historical baseline retained until a newer Core dependency is demonstrated); run regression tests against Geeklog 2.1.3 and 2.2.2. PHP 5.6.40 minimum syntax; validate PHP 5.6, 8.1 and 8.3. The requested ZIP suffix `_2.1.3` now matches installer and plugin.json.
- PostgreSQL support removed by request in 1.2.4; MySQL-compatible Geeklog database only. Upgrading PostgreSQL installations is unsupported; export historical data before changing database engines.
- No official release before CI and functional acceptance. Never destroy existing logs during migration without explicit administrator action.

## 1.3.0 — stabilization carried forward from 1.2.4

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
- [x] Serialize search logging on legacy MyISAM installations using two-table WRITE locks and deterministic hid assignment (no schema migration); PHP lint and live concurrent-load test pending.
- [ ] Stress-test concurrent searches and lock failure behavior; deterministic IDs now take maxima from both query and hit tables to avoid reuse of orphaned hit identifiers. Evaluate InnoDB migration/unique constraints separately.
- [ ] Validate bulk clear under MySQL, including orphan hits; test existing installations and upgrades.
- [ ] Review MySQL aggregation, aliases, casting and non-deterministic ordering.
- [x] Suppress logging of obvious email addresses and long payment-card-like digit sequences (heuristic only; no claim of comprehensive PII detection).
- [ ] Add configurable opt-out/redaction policy and tests for sensitive searches, false positives and multilingual queries.
- [ ] Align installer metadata, plugin.json, compatibility checks, README, translations and release notes with tested minimum runtimes.
- [ ] Preserve existing data; back up before migrations and test rollback.

### Packaging and QA
- [x] Add GitHub Actions PHP 5.6/8.1/8.3 lint and distributable archive (first CI runs pending).
- [ ] Add PHP compatibility/static checks and metadata validation.
- [ ] Test clean install, upgrades, activate/deactivate/uninstall, left/right block, statistics, anonymous/admin users.
- [ ] Exercise multiple sites sharing files but using separate databases.
- [x] Configure CI to produce versioned `dist` archive.
- [x] GitHub Actions run 38992700770 succeeded and committed `dist/searchrank_1.3.0_2.1.3.zip`.
- [x] User reports successful upgrade from 1.2.1 on Geeklog 2.2.2.
- [ ] Retest archive after subsequent fixes and test fresh installation.

## Live acceptance gate (required after CI succeeds)
- [ ] Upgrade the latest `dist/searchrank_1.3.0_2.1.3.zip` on Geeklog 2.2.2 staging (backup DB first).
- [ ] Verify dashboard, Help, Analytics & CSV, 7/28/90/365-day filters, empty-state and CSV encoding.
- [ ] Verify CSS cache-busting after upgrading (hard reload should not be necessary).
- [ ] Perform three site searches, verify counts and use pagination without clearing historical data.
- [ ] Test log-clear only on disposable/staging data after making a backup, then test repeated searches.
- [ ] Report screenshots of admin dashboard and reporting, plus any PHP warnings.

## 1.3.0 — administration
- [x] Add responsive analytics reports with period filters and CSV export on legacy tables (pending installation test).
- [x] Version admin and block stylesheet URLs with deterministic `?v=1.2.4`; no JavaScript assets are currently required.
- [ ] Confirm asset cache invalidation on subsequent releases, including CSS in installer archive.
- [ ] Confirm current Github Actions builds and dist commit after publishing workflow fix.
- [x] Initial admin home: explanatory introduction, query count, direct log/configuration navigation, separate token-protected destructive action and responsive theme-independent CSS.
- [x] Replace legacy oversized log pagination controls with compact, conditionally visible navigation.
- [x] Add first-use admin Help view covering configuration, search log, analytics, retention, privacy and troubleshooting.
- [x] Begin localization of report controls in English and fallback safely for older language packs.
- [ ] Complete all new admin/help/report translations, active navigation and accessibility checks on Geeklog 2.1.3 and 2.2.2.
- [ ] Navigation consistent with memorandum/plugin-admin-navigation.md and plugin-admin-ux-guidelines.md.
- [ ] In-product getting started, configuration tooltips and responsive tables.
- [ ] Time windows, date filters, safe CSV export and stable sorting.
- [ ] Externalized versioned CSS and theme-independent UI.
- [ ] Explicit confirmation and success/error feedback for destructive operations.

## 1.3.0 — editorial intelligence (formerly planned for 1.4.0)
- [x] Add daily aggregates from existing hit timestamps, without database migration (pending functional test).
- [ ] Collect zero-result searches reliably via compatible search-engine callback (current SearchRank hook does not know result counts).
- [x] Add comparison against the preceding equivalent period (pending on-site testing).
- [ ] Add per-expression trend comparisons and charts, taking retention into account.
- [ ] Evaluate Geeklog search API before counting zero-result searches.
- [ ] New/trending queries and time-window comparisons.
- [ ] Privacy-sensitive query filtering and retention documentation.
- [ ] Read-only query metrics service for Hub; preserve separate responsibilities for Analytics, Search Console and Bing Webmaster.

## Versioning and release decision (2026-10-17)
- No SearchRank 1.2.4 official release. All completed 1.2.4 stabilization work and planned 1.3.0/1.4.0 features consolidate into the next **1.3.0** release.
- Existing `searchrank_1.3.0_2.1.3.zip` is a development/test artifact only; retain it until the 1.3.0 metadata, workflow, archive name and migration paths are changed consistently.
- Keep Geeklog 2.1.3 declared minimum unless a verified newer Core API dependency is introduced; test 2.1.3 and 2.2.2 before release.
- The next code phase must update all version sources, GitHub Actions packaging and cache-busting to 1.3.0 together, then test the upgrade path from installed 1.2.1 and test builds of 1.2.4.
- Release gate: working UI, search logging, daily analytics, export, translated strings, privacy safeguards, package verification and live acceptance. Features unsuitable for a safe 1.3.0 release must be explicitly deferred rather than claimed done.

## PostgreSQL decision
PostgreSQL support is deliberately removed in 1.2.4. SQL installation and upgrade files are deleted; installer compatibility check rejects unsupported database types. This does not remove existing PostgreSQL tables on remote installations. Database migrations require separate backup/export and are not automatic.

## Source documents
- https://github.com/hostellerie/memorandum/blob/main/geeklog-runtime-support-policy.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-development-guide.md
- https://github.com/hostellerie/memorandum/blob/main/plugin-admin-ux-guidelines.md
- https://github.com/hostellerie/memorandum/blob/main/seo-measurement-interoperability-guide.md
