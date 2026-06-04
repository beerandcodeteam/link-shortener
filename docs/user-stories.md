# Overview

This document contains user stories for the **Link Shortener**, a Laravel application that lets visitors create short URLs and registered users manage their links and track clicks.

**User Types:**

- **Visitor** — Unauthenticated user who can start creating a short URL and follow short links
- **User** — Authenticated account holder who owns and manages short URLs

**Product Decisions (confirmed):**

- A visitor can *start* creating a short URL on the homepage, but to **save** it they are routed through registration/login; the URL is persisted and linked to their account once that flow completes. There are no permanently anonymous URLs.
- **Anyone** may request a **custom short code**, subject to uniqueness validation; otherwise a code is auto-generated.
- **No Admin role** in this version. Only Visitor and User.
- Click tracking stores both a **total count** and a **per-click log** (date/time, referrer, browser, device, IP hash).

---

## 1. Public Shortener

### US-1.1: Start Creating a Short URL (Visitor)

**As a** Visitor
**I want to** paste a long URL on the homepage and start the shortening process
**So that** I can get a short link for my destination

**Acceptance Criteria:**

- [ ] Homepage shows an input field for the long URL
- [ ] URL is validated (well-formed, `http`/`https` scheme, max length)
- [ ] Visitor may optionally request a custom short code
- [ ] On submit, if the visitor is **not authenticated**, the entered URL (and optional custom code) is preserved and the visitor is redirected to register/login
- [ ] After successful registration/login, the short URL is created and linked to the new/returning user's account
- [ ] The generated short link is shown with a copy action

**Expected Result:** Visitor's intended URL survives the auth flow and is saved to their account.

### US-1.2: Auto-Generated Short Code

**As a** User
**I want** the system to generate a unique short code automatically
**So that** I get a working short link without choosing a code

**Acceptance Criteria:**

- [ ] Short code is generated when no custom code is provided
- [ ] Code is unique across all URLs
- [ ] Code uses a URL-safe character set
- [ ] Collisions are retried until a unique code is produced

**Expected Result:** A unique short code is assigned to the URL.

### US-1.3: Custom Short Code

**As a** User
**I want to** choose my own short code (e.g. `/my-link`)
**So that** my short link is memorable and branded

**Acceptance Criteria:**

- [ ] User may enter a desired custom code during creation
- [ ] Code is validated for format (allowed characters, length)
- [ ] Code uniqueness is checked; duplicates are rejected with a clear error
- [ ] Reserved/route-conflicting words are blocked (e.g. `login`, `dashboard`)
- [ ] On success, the custom code is used as the short link

**Expected Result:** User's short link uses their chosen code when available.

---

## 2. Redirection

### US-2.1: Redirect a Short URL

**As a** Visitor
**I want to** open a short URL and be sent to the original destination
**So that** I can reach the intended page

**Acceptance Criteria:**

- [ ] System looks up the short code in the database
- [ ] If the code exists and is **active**, the visitor is redirected to the original URL
- [ ] Redirect uses an appropriate HTTP status (e.g. 302)
- [ ] A click is registered before the redirect completes

**Expected Result:** Visitor reaches the original destination and the click is recorded.

### US-2.2: Handle Missing Short Code

**As a** Visitor
**I want to** see a clear 404 page when a short code does not exist
**So that** I understand the link is invalid

**Acceptance Criteria:**

- [ ] Unknown short code returns an HTTP 404 page
- [ ] No redirect occurs
- [ ] No click is recorded

**Expected Result:** Invalid links produce a 404 instead of a broken redirect.

### US-2.3: Handle Disabled Short Code

**As a** Visitor
**I want to** see an error page when a link is disabled
**So that** I know the link is no longer available

**Acceptance Criteria:**

- [ ] Disabled short code does **not** redirect
- [ ] An error/unavailable page is shown
- [ ] No click is recorded for disabled links

**Expected Result:** Disabled links are blocked from redirection.

---

## 3. Authentication

### US-3.1: User Registration

**As a** Visitor
**I want to** create an account
**So that** I can save and manage my short URLs

**Acceptance Criteria:**

- [ ] Registration form collects: name, email, password, password confirmation
- [ ] Email must be unique
- [ ] Password must meet minimum requirements (8+ characters)
- [ ] If registration was triggered from an in-progress shortening, the pending URL is created after registration
- [ ] User is redirected to the dashboard after registering

**Expected Result:** Account is created and the user reaches their dashboard.

### US-3.2: User Login

**As a** registered User
**I want to** log in with email and password
**So that** I can access my dashboard and links

**Acceptance Criteria:**

- [ ] Login form collects email and password
- [ ] Invalid credentials show an error without revealing which field failed
- [ ] If login was triggered from an in-progress shortening, the pending URL is created after login
- [ ] User is redirected to the dashboard on success

**Expected Result:** User is authenticated and reaches their dashboard.

### US-3.3: Password Reset

**As a** registered User
**I want to** reset my password
**So that** I can regain access to my account

**Acceptance Criteria:**

- [ ] "Forgot password" link is available
- [ ] Reset email is sent to the account email
- [ ] Reset link is valid for a limited time (60 minutes)
- [ ] User can set a new password

**Expected Result:** User regains access securely.

### US-3.4: Logout

**As a** logged-in User
**I want to** log out
**So that** my session is ended on shared devices

**Acceptance Criteria:**

- [ ] Logout action available in the authenticated area
- [ ] Session is invalidated
- [ ] User is redirected to the public homepage

**Expected Result:** User session ends safely.

---

## 4. User Dashboard & Link Management

### US-4.1: View My Short URLs

**As a** User
**I want to** see a list of my short URLs
**So that** I can manage and monitor them

**Acceptance Criteria:**

- [ ] Dashboard loads **only** URLs owned by the current user
- [ ] Each row shows: original URL, short URL, click count, status, creation date
- [ ] List is paginated for large sets
- [ ] Empty state shown when the user has no URLs

**Expected Result:** User sees only their own links with key details.

### US-4.2: Create Short URL from Dashboard

**As a** User
**I want to** create a new short URL from my dashboard
**So that** I can quickly add links to my account

**Acceptance Criteria:**

- [ ] Dashboard provides a creation form (long URL + optional custom code)
- [ ] URL and custom code are validated (see US-1.3)
- [ ] New URL is stored linked to the authenticated user
- [ ] New short URL appears in the list immediately

**Expected Result:** A new owned short URL is created and visible.

### US-4.3: Copy Short Link

**As a** User
**I want to** copy a short link with one action
**So that** I can paste and share it easily

**Acceptance Criteria:**

- [ ] Copy control is available per URL
- [ ] Full short URL (with domain) is copied to clipboard
- [ ] Visual confirmation is shown on copy

**Expected Result:** Short link is copied to the clipboard.

### US-4.4: Disable / Enable a Short URL

**As a** User
**I want to** disable or re-enable one of my short URLs
**So that** I can stop or resume redirection without deleting it

**Acceptance Criteria:**

- [ ] User can toggle a URL's status (active/disabled)
- [ ] Ownership is verified before the change
- [ ] Disabled URLs stop redirecting (see US-2.3)
- [ ] Status change is reflected immediately in the list

**Expected Result:** Redirection behavior follows the URL's status.

### US-4.5: Delete a Short URL

**As a** User
**I want to** delete one of my short URLs
**So that** I can remove links I no longer need

**Acceptance Criteria:**

- [ ] Delete action is available per URL
- [ ] Confirmation is required before deletion
- [ ] Ownership is verified before deletion
- [ ] Deleted URL no longer redirects (returns 404)
- [ ] URL is removed from the user's list

**Expected Result:** The URL is removed and its short code stops working.

### US-4.6: View Link Details

**As a** User
**I want to** open a detail view for one of my URLs
**So that** I can review its data and click history

**Acceptance Criteria:**

- [ ] Detail page shows original URL, short URL, status, creation date, total clicks
- [ ] Ownership is verified before showing the page
- [ ] Detail page lists per-click log entries (see US-5.2)

**Expected Result:** User sees full information for a single owned URL.

---

## 5. Click Tracking

### US-5.1: Increment Total Click Count

**As a** User
**I want** each redirect to increment the click counter on my URL
**So that** I can see how many times my link was used

**Acceptance Criteria:**

- [ ] Counter increments on every successful redirect
- [ ] Disabled/missing codes do not increment the counter
- [ ] Updated count is visible in the dashboard and detail view

**Expected Result:** Total clicks accurately reflect successful redirects.

### US-5.2: Record Per-Click Log

**As a** User
**I want** each click to be logged with details
**So that** I can analyze how my links are used over time

**Acceptance Criteria:**

- [ ] Each click stores: date/time, referrer, browser, device, IP hash
- [ ] IP is stored hashed, not in plain text (privacy)
- [ ] Log entries are linked to the originating short URL
- [ ] Log entries are visible in the URL detail view
- [ ] Logging does not block or noticeably delay the redirect

**Expected Result:** Each click produces a privacy-respecting log entry tied to the URL.

---

## 6. Authorization & Data Isolation

### US-6.1: Enforce URL Ownership

**As a** User
**I want** only my own URLs to be visible and editable by me
**So that** my data stays private and secure

**Acceptance Criteria:**

- [ ] Dashboard, detail, edit, disable, and delete actions are scoped to the owner
- [ ] Accessing another user's URL action returns 403/404
- [ ] Authorization is enforced via Laravel Policies
- [ ] Public redirection works regardless of owner (links are public to follow)

**Expected Result:** Users can only manage URLs they own.

---

## Appendix: User Story Status

| ID     | Story                          | Priority | Status  |
|--------|--------------------------------|----------|---------|
| US-1.1 | Start Creating a Short URL     | High     | Pending |
| US-1.2 | Auto-Generated Short Code      | High     | Pending |
| US-1.3 | Custom Short Code              | High     | Pending |
| US-2.1 | Redirect a Short URL           | High     | Pending |
| US-2.2 | Handle Missing Short Code      | High     | Pending |
| US-2.3 | Handle Disabled Short Code     | Medium   | Pending |
| US-3.1 | User Registration              | High     | Pending |
| US-3.2 | User Login                     | High     | Pending |
| US-3.3 | Password Reset                 | Medium   | Pending |
| US-3.4 | Logout                         | Low      | Pending |
| US-4.1 | View My Short URLs             | High     | Pending |
| US-4.2 | Create Short URL from Dashboard| High     | Pending |
| US-4.3 | Copy Short Link                | Medium   | Pending |
| US-4.4 | Disable / Enable a Short URL   | Medium   | Pending |
| US-4.5 | Delete a Short URL             | High     | Pending |
| US-4.6 | View Link Details              | Medium   | Pending |
| US-5.1 | Increment Total Click Count    | High     | Pending |
| US-5.2 | Record Per-Click Log           | Medium   | Pending |
| US-6.1 | Enforce URL Ownership          | High     | Pending |
