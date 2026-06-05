# Project Phases — Link Shortener (gemma4-optimized)

Same plan as [project-phases.md](project-phases.md), but **broken into many small single-deliverable phases** so a smaller local model (gemma4 / ~12B via Ollama) can implement each one in isolation without losing context.

**How this file differs:**
- Every task is its own `## Phase X.Y` H2 header — `ralph.sh` splits on `## Phase`, so each becomes one independent run.
- One deliverable per phase (one component, one migration, one service). No phase mixes unrelated concerns.
- Tests are inline in the phase that produces them.
- Phase 0 (baseline scaffold) is assumed done and omitted. Start at Phase 1.

**Conventions (all phases):**
- Laravel 13, PHP 8.5, Livewire 4, Tailwind CSS, Pest 4, PostgreSQL.
- All commands via `./vendor/bin/sail`.
- Livewire 4: components under `app/Livewire`, server-side state, `wire:model.live` only where reactivity is needed, authorization inside actions.
- Lookup tables instead of enum columns. Reference by `_id` FK.
- Pest feature tests unless noted. Run `php artisan test --compact`.
- Run `vendor/bin/pint --dirty --format agent` after PHP changes.

> Source of truth for Phase 1 visuals: `docs/design-handoff/app.css` + `docs/design-handoff/js/ui.jsx` + screenshots. Phase 1 needs **no automated tests**.

---

## Phase 1.1: Design Tokens & Tailwind Setup

Port the Apple-inspired token set from `app.css` `:root` into the Tailwind/theme config (CSS custom properties or `@theme`):
- Colors: `--bg`, `--bg-soft`, `--ink`/`--ink-2`/`--ink-3`, `--line`, `--blue`/`--blue-press`/`--blue-tint`, `--green`, `--amber`, `--red` + tints.
- Radii: pill/lg/md/sm. Shadows: card/pop/nav. Font stack.

No tests. (was 1.1.1)

---

## Phase 1.2: Typography & Vite Build

- Establish typography utilities (`display`, `h1`–`h3`, `body`, `small`, `eyebrow`, `mono`) and base body styles (antialiasing, letter-spacing). (was 1.1.2)
- Verify Vite build (`npm run build`) compiles CSS with no manifest errors. (was 1.1.3)

No tests.

---

## Phase 1.3: Guest Layout

Create the **guest layout** for unauthenticated pages (e.g. `resources/views/components/layouts/guest.blade.php`) — public nav (Log in / Sign up), centered content, footer. Mirror `ui.jsx`. (was 1.2.1)

No tests.

---

## Phase 1.4: App Layout

Create the **app layout** for authenticated pages (`resources/views/components/layouts/app.blade.php`) — authenticated nav (Links / Analytics, "New link" CTA, avatar menu with Sign out), main content slot. (was 1.2.2)

No tests.

---

## Phase 1.5: NavBar Partial

Build the sticky **NavBar** Blade/Livewire partial mirroring `ui.jsx` `NavBar` (blur background, logo, contextual links, auth-state-aware actions). Used by both layouts. (was 1.2.3)

No tests.

---

## Phase 1.6: Toast Region

Build the **Toast** notification region (session-flash driven) mirroring `ui.jsx` `Toast`. (was 1.2.4)

No tests.

---

## Phase 1.7: Button Component

**Button** Blade component — variants `primary`, `ghost`, `soft`, `danger`, sizes default/`sm`, pill radius, icon slot, press transform. (was 1.3.1)

No tests.

---

## Phase 1.8: Input Component

**Input** Blade component — text/url/email/password, label, hint, error state, leading/trailing slot. (was 1.3.2)

No tests.

---

## Phase 1.9: Select Component

**Select** Blade component — styled native select with label + error state. (was 1.3.3)

No tests.

---

## Phase 1.10: Checkbox Component

**Checkbox** Blade component — label, checked/error states. (was 1.3.4)

No tests.

---

## Phase 1.11: Radio Component

**Radio** / radio-group Blade component — label, options, error state. (was 1.3.5)

No tests.

---

## Phase 1.12: Modal Component

**Modal** Blade component mirroring `ui.jsx` `Modal` — scrim, centered card, ESC-to-close, click-outside close, configurable width (used for delete confirmation, create form). (was 1.3.6)

No tests.

---

## Phase 1.13: StatusBadge Component

**StatusBadge** Blade component — `active` (green dot) / `disabled` (neutral dot) per `ui.jsx`. (was 1.3.7)

No tests.

---

## Phase 1.14: CopyButton Component

**CopyButton** Blade component — copies text to clipboard via Alpine, shows "Copied" check state (used on dashboard + detail). (was 1.3.8)

No tests.

---

## Phase 1.15: Favicon Chip & Avatar

**Favicon chip** + **Avatar** (initials) presentational Blade components per `ui.jsx`. (was 1.3.9)

No tests.

---

## Phase 1.16: Component Gallery Route

Component preview/gallery route (dev-only) to eyeball all components against screenshots. (was 1.3.10)

No tests.

---

## Phase 2.1: LinkStatus Lookup

Migration + model `LinkStatus` (`link_statuses`: name, slug unique, description, is_active). `php artisan make:model LinkStatus -m`. (was 2.1.1)

No standalone tests (covered by Phase 2.4 seeder test).

---

## Phase 2.2: DeviceType Lookup

Migration + model `DeviceType` (`device_types`: name, slug unique). `php artisan make:model DeviceType -m`. (was 2.1.2)

No standalone tests (covered by Phase 2.4).

---

## Phase 2.3: Browser Lookup

Migration + model `Browser` (`browsers`: name, slug unique). `php artisan make:model Browser -m`. (was 2.1.3)

No standalone tests (covered by Phase 2.4).

---

## Phase 2.4: Lookup Seeder

Seeder seeding `link_statuses` (`active`, `disabled`), `device_types` (Desktop, Mobile, Tablet, Bot, Unknown), `browsers` (Chrome, Firefox, Safari, Edge, Other); registered in `DatabaseSeeder`. (was 2.1.4)

**Tests:** `LookupSeederTest` — seeding creates expected rows with unique slugs; re-running seeder is idempotent (no duplicates).

---

## Phase 2.5: Links Migration

Migration `links` per schema: `user_id` FK (not null), `link_status_id` FK (not null), `original_url` text, `short_code` unique, `click_count` default 0, timestamps; indexes on `short_code` (unique), `user_id`, `link_status_id`. (was 2.2.1)

No standalone tests (covered by Phase 2.6).

---

## Phase 2.6: Link Model & Factory

- `Link` model — `belongsTo` User, LinkStatus; `hasMany` Click; fillable/casts; scope `active()` and `isActive()` accessor. (was 2.2.2)
- `LinkFactory` with states (`active()`, `disabled()`, `forUser()`). (was 2.2.3)

**Tests:** `LinkModelTest` — factory creates a link with owner + status; `links()` relation on User returns only that user's links; `active()` scope filters by status; `short_code` unique constraint rejects duplicates.

---

## Phase 2.7: Clicks Migration

Migration `clicks` per schema: `link_id` FK (not null), `device_type_id` FK (null), `browser_id` FK (null), `referrer` (null), `ip_hash` (null), `clicked_at`, timestamps; indexes on `link_id`, `clicked_at`. (was 2.3.1)

No standalone tests (covered by Phase 2.8).

---

## Phase 2.8: Click Model & Factory

- `Click` model — `belongsTo` Link, DeviceType, Browser; casts `clicked_at` datetime. (was 2.3.2)
- `ClickFactory` with relations + states (recent dates for chart testing). (was 2.3.3)

**Tests:** `ClickModelTest` — factory creates click tied to a link; `link->clicks()` returns them ordered; `ip_hash` stores a hash (never raw IP).

---

## Phase 3.1: Short Code Generator (US-1.2)

`ShortCodeGenerator` service — generates URL-safe random code (configurable length, no ambiguous chars), retries on collision against `links.short_code`. (was 3.1.1)

**Tests:** `ShortCodeGeneratorTest` (unit) — generated codes match the allowed charset and length; collision with an existing code triggers regeneration to a unique value; generation is unique across many iterations.

---

## Phase 3.2: Reserved Words Rule

Reserved-words list (e.g. `login`, `register`, `dashboard`, `links`) + a `ReservedShortCode` validation rule. (was 3.2.2)

**Tests:** unit test on the rule — passes a normal code, fails each reserved word.

---

## Phase 3.3: Link Validation (US-1.3)

Form Object / validation rules for link creation: `original_url` required, valid URL, `http`/`https` scheme only, max length; `custom_code` optional, allowed charset + length, unique in `links`, not a reserved word (use Phase 3.2 rule). (was 3.2.1)

**Tests:** `LinkValidationTest` — rejects non-URL, rejects non-http(s) scheme, rejects over-long URL; accepts valid custom code; rejects duplicate custom code with a clear error; rejects reserved-word code; accepts empty custom code (auto-generate path).

---

## Phase 4.1: Redirect Route & Controller (US-2.1, US-2.2, US-2.3)

- Route `GET /{shortCode}` → `RedirectController` (registered **last** so it doesn't shadow named routes). (was 4.1.1)
- Controller: lookup by `short_code`; active → 302 redirect to `original_url`; missing → 404; disabled → dedicated "link unavailable" page (not a redirect). (was 4.1.2)

**Tests:** `RedirectTest` — active code returns 302 to original URL; unknown code returns 404; disabled code returns the unavailable page (non-redirect) and does **not** 302; app routes (`/login`, `/dashboard`) are not captured by the catch-all.

---

## Phase 4.2: Click Recording Action (US-5.1, US-5.2)

`RecordClick` action: increment `links.click_count` and insert a `clicks` row resolving `device_type_id`, `browser_id` (user-agent parse → lookup), `referrer`, `ip_hash` (hashed), `clicked_at`. (was 4.2.1)

**Tests:** `ClickTrackingTest` — successful redirect increments `click_count` by 1 and creates exactly one `clicks` row with the originating link; `ip_hash` is hashed (not equal to raw IP); referrer/device/browser are persisted from request; **disabled** and **missing** codes create **no** click row and do **not** increment the counter.

---

## Phase 4.3: Non-Blocking Dispatch

Dispatch click recording so it does not block the redirect (queued or `afterResponse`), per "logging must not delay redirect". Wire it into the redirect path from Phase 4.1/4.2. (was 4.2.2)

**Tests:** extend `ClickTrackingTest` — assert the redirect response is returned and the click is still recorded after the response/queue runs.

---

## Phase 5.1: Register Component (US-3.1)

Livewire `Register` component (name, email unique, password ≥8 + confirmation) → creates user, logs in, redirects to dashboard. Use the **guest layout**. (was 5.1.1)

**Tests:** `RegistrationTest` — valid data creates user + authenticates + redirects to dashboard; duplicate email rejected; password <8 rejected; mismatch rejected.

---

## Phase 5.2: Login Component (US-3.2)

Livewire `Login` component (email, password; generic error on bad credentials) → authenticates, redirects to dashboard. Guest layout. (was 5.1.2)

**Tests:** `LoginTest` — valid credentials authenticate + redirect; invalid credentials show generic error and do not authenticate.

---

## Phase 5.3: Logout & Auth Routes (US-3.4)

- Logout action → invalidates session, redirects to public homepage. (was 5.1.3)
- Routes + guest/auth middleware wiring auth pages to the guest layout. (was 5.1.4)

**Tests:** `LogoutTest` — authenticated user can log out, session invalidated, redirected home.

---

## Phase 5.4: Password Reset (US-3.3)

"Forgot password" request component + email; reset component setting a new password; token expiry 60 min. (was 5.2.1)

**Tests:** `PasswordResetTest` — reset link/email dispatched for known email; valid token lets user set a new password and log in; expired/invalid token rejected.

---

## Phase 5.5: Homepage Shorten Component (US-1.1)

Public homepage `Shorten` Livewire component: validates URL (+ optional custom code, reuse Phase 3.3); if guest, stash `{original_url, custom_code}` in session and redirect to register/login; if authenticated, create the link immediately. (was 5.3.1)

**Tests:** `PendingShortenFlowTest` (part 1) — guest submitting on homepage is redirected to auth with payload stored in session; authenticated user submitting creates the link directly without the auth detour.

---

## Phase 5.6: Pending-URL Bridge (US-1.1)

After successful register/login, if a pending shorten payload exists in session, create the link for the user, clear the payload, and surface the resulting short link (with copy action) on the dashboard. (was 5.3.2)

**Tests:** `PendingShortenFlowTest` (part 2) — completing **registration** creates the link owned by the new user using the stashed URL/custom code; completing **login** does the same for an existing user; payload cleared after creation.

---

## Phase 6.1: Link Policy (US-6.1)

`LinkPolicy` (`view`, `update`, `delete`) scoping to `user_id`; register policy; apply in all dashboard actions. Redirect route stays public (no policy). (was 6.1.1)

**Tests:** `LinkPolicyTest` — owner passes view/update/delete; non-owner is denied (403/404); guest cannot reach dashboard actions.

---

## Phase 6.2: Link List (US-4.1)

Livewire `Dashboard`/`LinkList` component — paginated list of **only** the current user's links showing original URL, short URL, click count, status badge, creation date; empty state. (was 6.2.1)

**Tests:** `LinkListTest` — renders only the authenticated user's links (excludes other users'); shows click count + status + dates; paginates beyond page size; empty state when none.

---

## Phase 6.3: Create From Dashboard (US-4.2)

Create form (inline or modal) reusing Phase 3 validation + generator; new link appears in list immediately; success toast + copy action. (was 6.3.1)

**Tests:** `DashboardCreateLinkTest` — valid submit creates link owned by user and it appears at top of list; auto-generates code when custom omitted; honors valid custom code; surfaces validation errors (duplicate/reserved/invalid URL) without creating.

---

## Phase 6.4: Copy Short Link (US-4.3)

Wire the `CopyButton` to each list row + detail page copying the full short URL (with domain via `route()`/`get-absolute-url`). (was 6.4.1)

**Tests:** `CopyLinkTest` — the rendered short URL is the absolute URL for the code. (Assert the value/markup, not the browser clipboard.)

---

## Phase 6.5: Disable / Enable (US-4.4)

Toggle action switching `link_status_id` between active/disabled, ownership-checked, list reflects new badge immediately. (was 6.5.1)

**Tests:** `ToggleLinkStatusTest` — owner toggles active→disabled and back; disabled link then fails to redirect (integrates Phase 4.1); non-owner toggle denied; status change reflected in component state.

---

## Phase 6.6: Delete (US-4.5)

Delete action behind a confirmation **Modal**, ownership-checked, hard delete; removed from list; short code then 404s. (was 6.6.1)

**Tests:** `DeleteLinkTest` — owner deletes own link → removed from DB + list; deleted code subsequently returns 404 (integrates Phase 4.1); non-owner delete denied; confirmation required (no delete without confirm path).

---

## Phase 6.7: Link Detail & Click History (US-4.6, US-5.2)

Livewire `LinkDetail` component — original URL, short URL, status, created date, total clicks, bar chart of clicks over time, and per-click log table (date/time, referrer, browser, device). Ownership-checked. (was 6.7.1)

**Tests:** `LinkDetailTest` — owner sees detail with correct totals and click-log rows; non-owner/guest denied (403/404); click log lists entries tied to that link only; counts match `click_count`.

---

## Phase 7.1: Error Pages

Custom **404** and **link-unavailable** (disabled) error pages styled with the design system. (was 7.1)

**Tests:** `ErrorPageTest` — 404 view renders for unknown code.

---

## Phase 7.2: Rate Limiting

Rate-limit public homepage shorten + redirect endpoints (abuse protection). (was 7.2)

**Tests:** `RateLimitTest` — excessive shorten submissions are throttled (429) after the configured limit.

---

## Phase 7.3: Architecture Tests

Pest **architecture tests** (`arch()`): models extend Eloquent base, no `dd`/`dump`/`ray` left, Livewire components in `app/Livewire`. (was 7.3)

**Tests:** the arch tests themselves.

---

## Phase 7.4: Browser Smoke Test

Browser smoke test (Pest 4 browser) across home, auth, dashboard, detail for JS console errors. (was 7.4)

**Tests:** the smoke test itself.

---

## Phase 7.5: Final Pass

Final `vendor/bin/pint --dirty` pass + full `php artisan test` green. (was 7.5)

No new tests — full suite must pass.

---

## Traceability Matrix

| User Story | Phase(s) |
|------------|----------|
| US-1.1 Start Creating a Short URL | 5.5, 5.6 |
| US-1.2 Auto-Generated Short Code | 3.1 |
| US-1.3 Custom Short Code | 3.2, 3.3 |
| US-2.1 Redirect a Short URL | 4.1 |
| US-2.2 Handle Missing Short Code | 4.1 |
| US-2.3 Handle Disabled Short Code | 4.1, 7.1 |
| US-3.1 User Registration | 5.1 |
| US-3.2 User Login | 5.2 |
| US-3.3 Password Reset | 5.4 |
| US-3.4 Logout | 5.3 |
| US-4.1 View My Short URLs | 6.2 |
| US-4.2 Create Short URL from Dashboard | 6.3 |
| US-4.3 Copy Short Link | 6.4 |
| US-4.4 Disable / Enable a Short URL | 6.5 |
| US-4.5 Delete a Short URL | 6.6 |
| US-4.6 View Link Details | 6.7 |
| US-5.1 Increment Total Click Count | 4.2 |
| US-5.2 Record Per-Click Log | 4.2, 6.7 |
| US-6.1 Enforce URL Ownership | 6.1 |
