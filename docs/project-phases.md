# Project Phases — Link Shortener

Implementation plan broken into numbered phases and sub-phases. Each numbered item is a task an AI agent can implement in isolation. References: [user-stories.md](user-stories.md), [project-description.md](project-description.md), [database-schema.md](database-schema.md), [design-handoff](design-handoff/).

**Legend:** `[x]` done · `[ ]` pending · `US-x.x` traces to a user story.

**Conventions (all phases):**
- Laravel 13, PHP 8.5, Livewire 4, Tailwind CSS, Pest 4, PostgreSQL.
- Livewire 4 best practices: single-file or multi-file components under `app/Livewire`, server-side state, `wire:model.live` only where reactivity is needed, validation via component rules or Form Objects, authorization inside actions.
- Lookup tables instead of enum columns (see schema). Reference by `_id` FK.
- Tests are **Pest feature tests** unless noted. Run with `php artisan test --compact`.
- Run `vendor/bin/pint --dirty --format agent` after PHP changes.

---

## Phase 0: Project Baseline (already scaffolded)

- [x] **0.1** Laravel 13 skeleton installed (`composer.json`: framework ^13.8).
- [x] **0.2** Livewire 4 installed (`livewire/livewire ^4.3`).
- [x] **0.3** Pest 4 installed and wired (`tests/Pest.php`, `phpunit.xml`).
- [x] **0.4** Default framework migrations present (`users`, `cache`, `jobs`).
- [x] **0.5** Default `User` model and `resources/views/layouts/app.blade.php` present (to be replaced/extended in later phases).
- [x] **0.6** Confirm PostgreSQL connection in `.env` (`DB_CONNECTION=pgsql`) and `php artisan migrate` runs clean.

---

## Phase 1: Frontend Foundation & Design System

> No automated tests required for this phase (per instruction). Source of truth: `docs/design-handoff/app.css` + `docs/design-handoff/js/ui.jsx` + screenshots.

### Phase 1.1: Design Tokens & Tailwind Setup
- [ ] **1.1.1** Port the Apple-inspired token set from `app.css` `:root` into the Tailwind/theme config (CSS custom properties or `@theme`): colors (`--bg`, `--bg-soft`, `--ink`/`--ink-2`/`--ink-3`, `--line`, `--blue`/`--blue-press`/`--blue-tint`, `--green`, `--amber`, `--red` + tints), radii (pill/lg/md/sm), shadows (card/pop/nav), font stack.
- [ ] **1.1.2** Establish typography utilities (`display`, `h1`–`h3`, `body`, `small`, `eyebrow`, `mono`) and base body styles (antialiasing, letter-spacing).
- [ ] **1.1.3** Verify Vite build (`npm run build`) compiles CSS with no manifest errors.

### Phase 1.2: Base Layouts
- [ ] **1.2.1** Create the **guest layout** for unauthenticated pages (`php artisan livewire:layout`, e.g. `resources/views/components/layouts/guest.blade.php`) — public nav (Log in / Sign up), centered content, footer.
- [ ] **1.2.2** Create the **app layout** for authenticated pages (`resources/views/components/layouts/app.blade.php`) — authenticated nav (Links / Analytics, "New link" CTA, avatar menu with Sign out), main content slot.
- [ ] **1.2.3** Build the sticky **NavBar** Blade/Livewire partial mirroring `ui.jsx` `NavBar` (blur background, logo, contextual links, auth-state-aware actions).
- [ ] **1.2.4** Build the **Toast** notification region (session-flash driven) mirroring `ui.jsx` `Toast`.

### Phase 1.3: Reusable UI Components (Blade components)
- [ ] **1.3.1** **Button** component — variants `primary`, `ghost`, `soft`, `danger`, sizes default/`sm`, pill radius, icon slot, press transform.
- [ ] **1.3.2** **Input** component — text/url/email/password, label, hint, error state, leading/trailing slot.
- [ ] **1.3.3** **Select** component — styled native select with label + error state.
- [ ] **1.3.4** **Checkbox** component — label, checked/error states.
- [ ] **1.3.5** **Radio** / radio-group component — label, options, error state.
- [ ] **1.3.6** **Modal** component mirroring `ui.jsx` `Modal` — scrim, centered card, ESC-to-close, click-outside close, configurable width (used for delete confirmation, create form).
- [ ] **1.3.7** **StatusBadge** component — `active` (green dot) / `disabled` (neutral dot) per `ui.jsx`.
- [ ] **1.3.8** **CopyButton** component — copies text to clipboard via Alpine, shows "Copied" check state (used on dashboard + detail).
- [ ] **1.3.9** **Favicon chip** + **Avatar** (initials) presentational components per `ui.jsx`.
- [ ] **1.3.10** Component preview/gallery route (dev-only) to eyeball all components against screenshots.

---

## Phase 2: Database Foundation

> Tests here are lightweight feature/unit tests proving migrations, factories, relationships, and seeders behave.

### Phase 2.1: Lookup Tables, Migrations & Seeders
- [ ] **2.1.1** Migration + model `LinkStatus` (`link_statuses`: name, slug unique, description, is_active). `php artisan make:model LinkStatus -m`.
- [ ] **2.1.2** Migration + model `DeviceType` (`device_types`: name, slug unique).
- [ ] **2.1.3** Migration + model `Browser` (`browsers`: name, slug unique).
- [ ] **2.1.4** Seeder seeding `link_statuses` (`active`, `disabled`), `device_types` (Desktop, Mobile, Tablet, Bot, Unknown), `browsers` (Chrome, Firefox, Safari, Edge, Other); registered in `DatabaseSeeder`.
  - **Tests:** `LookupSeederTest` — seeding creates expected rows with unique slugs; re-running seeder is idempotent (no duplicates).

### Phase 2.2: Links Table & Model
- [ ] **2.2.1** Migration `links` per schema: `user_id` FK (not null), `link_status_id` FK (not null), `original_url` text, `short_code` unique, `click_count` default 0, timestamps; indexes on `short_code` (unique), `user_id`, `link_status_id`.
- [ ] **2.2.2** `Link` model — `belongsTo` User, LinkStatus; `hasMany` Click; fillable/casts; helper scopes (`active()`) and `isActive()` accessor.
- [ ] **2.2.3** `LinkFactory` with states (`active()`, `disabled()`, `forUser()`).
  - **Tests:** `LinkModelTest` — factory creates a link with owner + status; `links()` relation on User returns only that user's links; `active()` scope filters by status; `short_code` unique constraint rejects duplicates.

### Phase 2.3: Clicks Table & Model
- [ ] **2.3.1** Migration `clicks` per schema: `link_id` FK (not null), `device_type_id` FK (null), `browser_id` FK (null), `referrer` (null), `ip_hash` (null), `clicked_at`, timestamps; indexes on `link_id`, `clicked_at`.
- [ ] **2.3.2** `Click` model — `belongsTo` Link, DeviceType, Browser; casts `clicked_at` datetime.
- [ ] **2.3.3** `ClickFactory` with relations + states (recent dates for chart testing).
  - **Tests:** `ClickModelTest` — factory creates click tied to a link; `link->clicks()` returns them ordered; `ip_hash` stores a hash (never raw IP assertion via factory default).

---

## Phase 3: Short Code Generation & URL Validation

### Phase 3.1: Short Code Generator Service (US-1.2)
- [ ] **3.1.1** `ShortCodeGenerator` service — generates URL-safe random code (configurable length, no ambiguous chars), retries on collision against `links.short_code`.
  - **Tests:** `ShortCodeGeneratorTest` (unit) — generated codes match the allowed charset and length; collision with an existing code triggers regeneration to a unique value; generation is unique across many iterations.

### Phase 3.2: URL & Custom Code Validation (US-1.3)
- [ ] **3.2.1** Form Object / validation rules for link creation: `original_url` required, valid URL, `http`/`https` scheme only, max length; `custom_code` optional, allowed charset + length, unique in `links`, not a reserved word.
- [ ] **3.2.2** Reserved-words list (e.g. `login`, `register`, `dashboard`, `links`) + a `ReservedShortCode` validation rule.
  - **Tests:** `LinkValidationTest` — rejects non-URL, rejects non-http(s) scheme, rejects over-long URL; accepts valid custom code; rejects duplicate custom code with a clear error; rejects reserved-word code; accepts empty custom code (auto-generate path).

---

## Phase 4: Public Redirection & Click Tracking

### Phase 4.1: Redirect Route & Controller (US-2.1, US-2.2, US-2.3)
- [ ] **4.1.1** Route `GET /{shortCode}` → `RedirectController` (registered **last** so it doesn't shadow named routes; reserved words protect app routes).
- [ ] **4.1.2** Controller: lookup by `short_code`; active → 302 redirect to `original_url`; missing → 404; disabled → dedicated "link unavailable" page (not a redirect).
  - **Tests:** `RedirectTest` — active code returns 302 to original URL; unknown code returns 404; disabled code returns the unavailable page (non-redirect) and does **not** 302; app routes (`/login`, `/dashboard`) are not captured by the catch-all.

### Phase 4.2: Click Recording (US-5.1, US-5.2)
- [ ] **4.2.1** `RecordClick` action/listener: increment `links.click_count` and insert a `clicks` row resolving `device_type_id`, `browser_id` (user-agent parse → lookup), `referrer`, `ip_hash` (hashed), `clicked_at`.
- [ ] **4.2.2** Dispatch click recording so it does not block the redirect (queued/`afterResponse`), per "logging must not delay redirect".
  - **Tests:** `ClickTrackingTest` — successful redirect increments `click_count` by 1 and creates exactly one `clicks` row with the originating link; `ip_hash` is hashed (not equal to raw IP); referrer/device/browser are persisted from request; **disabled** and **missing** codes create **no** click row and do **not** increment the counter.

---

## Phase 5: Authentication & Pending-URL Bridge

### Phase 5.1: Auth Scaffolding (US-3.1, US-3.2, US-3.4)
- [ ] **5.1.1** Livewire `Register` component (name, email unique, password ≥8 + confirmation) → creates user, logs in, redirects to dashboard.
- [ ] **5.1.2** Livewire `Login` component (email, password; generic error on bad credentials) → authenticates, redirects to dashboard.
- [ ] **5.1.3** Logout action → invalidates session, redirects to public homepage.
- [ ] **5.1.4** Routes + guest/auth middleware; auth pages use the **guest layout**.
  - **Tests:** `RegistrationTest` — valid data creates user + authenticates + redirects to dashboard; duplicate email rejected; password <8 rejected; mismatch rejected. `LoginTest` — valid credentials authenticate + redirect; invalid credentials show generic error and do not authenticate. `LogoutTest` — authenticated user can log out, session invalidated, redirected home.

### Phase 5.2: Password Reset (US-3.3)
- [ ] **5.2.1** "Forgot password" request component + email; reset component setting a new password; token expiry 60 min.
  - **Tests:** `PasswordResetTest` — reset link/email dispatched for known email; valid token lets user set a new password and log in; expired/invalid token rejected.

### Phase 5.3: Pending-URL Bridge (US-1.1)
- [ ] **5.3.1** Public homepage `Shorten` component: validates URL (+ optional custom code); if guest, stash `{original_url, custom_code}` in session and redirect to register/login; if authenticated, create immediately.
- [ ] **5.3.2** After successful register/login, if a pending shorten payload exists in session, create the link for the new/returning user, clear the payload, and surface the resulting short link (with copy action) on the dashboard.
  - **Tests:** `PendingShortenFlowTest` — guest submitting on homepage is redirected to auth with payload stored; completing **registration** creates the link owned by the new user using the stashed URL/custom code; completing **login** does the same for an existing user; payload cleared after creation; authenticated user submitting on homepage creates the link directly without the auth detour.

---

## Phase 6: User Dashboard & Link Management

### Phase 6.1: Authorization Policy (US-6.1)
- [ ] **6.1.1** `LinkPolicy` (`view`, `update`, `delete`) scoping to `user_id`; register policy; apply in all dashboard actions. Redirect route stays public (no policy).
  - **Tests:** `LinkPolicyTest` — owner passes view/update/delete; non-owner is denied (403/404); guest cannot reach dashboard actions.

### Phase 6.2: Link List (US-4.1)
- [ ] **6.2.1** Livewire `Dashboard`/`LinkList` component — paginated list of **only** the current user's links showing original URL, short URL, click count, status badge, creation date; empty state.
  - **Tests:** `LinkListTest` — renders only the authenticated user's links (excludes other users'); shows click count + status + dates; paginates beyond page size; empty state when none.

### Phase 6.3: Create From Dashboard (US-4.2)
- [ ] **6.3.1** Create form (inline or modal) reusing Phase 3 validation + generator; new link appears in list immediately; success toast + copy action.
  - **Tests:** `DashboardCreateLinkTest` — valid submit creates link owned by user and it appears at top of list; auto-generates code when custom omitted; honors valid custom code; surfaces validation errors (duplicate/reserved/invalid URL) without creating.

### Phase 6.4: Copy Short Link (US-4.3)
- [ ] **6.4.1** Wire the `CopyButton` to each list row + detail page copying the full short URL (with domain via `route()`/`get-absolute-url`).
  - **Tests:** `CopyLinkTest` (feature-level) — the rendered short URL is the absolute URL for the code. (Clipboard UX is Alpine; assert the value/markup, not the browser clipboard.)

### Phase 6.5: Disable / Enable (US-4.4)
- [ ] **6.5.1** Toggle action switching `link_status_id` between active/disabled, ownership-checked, list reflects new badge immediately.
  - **Tests:** `ToggleLinkStatusTest` — owner toggles active→disabled and back; disabled link then fails to redirect (integrates Phase 4.1); non-owner toggle denied; status change reflected in component state.

### Phase 6.6: Delete (US-4.5)
- [ ] **6.6.1** Delete action behind a confirmation **Modal**, ownership-checked, hard delete; removed from list; short code then 404s.
  - **Tests:** `DeleteLinkTest` — owner deletes own link → removed from DB + list; deleted code subsequently returns 404 (integrates Phase 4.1); non-owner delete denied; confirmation required (no delete without confirm path).

### Phase 6.7: Link Detail & Click History (US-4.6, US-5.2 view)
- [ ] **6.7.1** Livewire `LinkDetail` component — original URL, short URL, status, created date, total clicks, bar chart of clicks over time, and per-click log table (date/time, referrer, browser, device). Ownership-checked.
  - **Tests:** `LinkDetailTest` — owner sees detail with correct totals and click-log rows; non-owner/guest denied (403/404); click log lists entries tied to that link only; counts match `click_count`.

---

## Phase 7: Cross-Cutting & Hardening

- [ ] **7.1** Custom **404** and **link-unavailable** (disabled) error pages styled with the design system.
  - **Tests:** covered by `RedirectTest`; add `ErrorPageTest` asserting 404 view renders for unknown code.
- [ ] **7.2** Rate-limit public homepage shorten + redirect endpoints (abuse protection).
  - **Tests:** `RateLimitTest` — excessive shorten submissions are throttled (429) after the configured limit.
- [ ] **7.3** Pest **architecture tests** (`arch()`): models extend Eloquent base, no `dd`/`dump`/`ray` left, Livewire components in `app/Livewire`.
- [ ] **7.4** Browser smoke test (Pest 4 browser) across home, auth, dashboard, detail for JS console errors.
- [ ] **7.5** Final `vendor/bin/pint --dirty` pass + full `php artisan test` green.

---

## Traceability Matrix

| User Story | Phase(s) |
|------------|----------|
| US-1.1 Start Creating a Short URL | 5.3 |
| US-1.2 Auto-Generated Short Code | 3.1 |
| US-1.3 Custom Short Code | 3.2 |
| US-2.1 Redirect a Short URL | 4.1 |
| US-2.2 Handle Missing Short Code | 4.1 |
| US-2.3 Handle Disabled Short Code | 4.1, 7.1 |
| US-3.1 User Registration | 5.1 |
| US-3.2 User Login | 5.1 |
| US-3.3 Password Reset | 5.2 |
| US-3.4 Logout | 5.1 |
| US-4.1 View My Short URLs | 6.2 |
| US-4.2 Create Short URL from Dashboard | 6.3 |
| US-4.3 Copy Short Link | 6.4 |
| US-4.4 Disable / Enable a Short URL | 6.5 |
| US-4.5 Delete a Short URL | 6.6 |
| US-4.6 View Link Details | 6.7 |
| US-5.1 Increment Total Click Count | 4.2 |
| US-5.2 Record Per-Click Log | 4.2, 6.7 |
| US-6.1 Enforce URL Ownership | 6.1 |

## Recommended Build Order

1. **Phase 1** — Frontend foundation (unblocks all UI).
2. **Phase 2** — Database foundation (unblocks all domain logic).
3. **Phase 3** — Short code generation + validation (pure, testable, no UI).
4. **Phase 4** — Public redirection + click tracking (delivers core value end-to-end).
5. **Phase 5** — Auth + pending-URL bridge.
6. **Phase 6** — Dashboard + management (depends on 1–5).
7. **Phase 7** — Hardening.
