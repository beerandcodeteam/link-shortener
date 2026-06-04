# Database Schema

Suggested database schema for the **Link Shortener**, expressed in [DBML](https://dbml.dbdiagram.io/).
Targets Laravel 13 / PostgreSQL conventions: `bigIncrements` primary keys, `snake_case` table and column names, plural table names, `created_at` / `updated_at` timestamps, foreign keys named `<singular>_id`.

Per project guidelines, all categorical/enumerable fields (status, device, browser) use **lookup tables** with foreign keys instead of enum or string columns.

---

## DBML

```dbml
//////////////////////////////////////////////////////
// Laravel framework tables
//////////////////////////////////////////////////////

Table users {
  id bigint [pk, increment]
  name varchar [not null]
  email varchar [not null, unique]
  email_verified_at timestamp [null]
  password varchar [not null]
  remember_token varchar [null]
  created_at timestamp [null]
  updated_at timestamp [null]
}

Table password_reset_tokens {
  email varchar [pk]
  token varchar [not null]
  created_at timestamp [null]
}

Table sessions {
  id varchar [pk]
  user_id bigint [ref: > users.id, null]
  ip_address varchar [null]
  user_agent text [null]
  payload longtext [not null]
  last_activity integer [not null]

  Indexes {
    user_id
    last_activity
  }
}

//////////////////////////////////////////////////////
// Lookup tables (replace enums / status strings)
//////////////////////////////////////////////////////

// Link status: active, disabled
Table link_statuses {
  id bigint [pk, increment]
  name varchar [not null]
  slug varchar [not null, unique]
  description varchar [null]
  is_active boolean [not null, default: true]
  created_at timestamp [null]
  updated_at timestamp [null]
}

// Device type for a click: Desktop, Mobile, Tablet, Bot, Unknown
Table device_types {
  id bigint [pk, increment]
  name varchar [not null]
  slug varchar [not null, unique]
  created_at timestamp [null]
  updated_at timestamp [null]
}

// Browser for a click: Chrome, Firefox, Safari, Edge, Other
Table browsers {
  id bigint [pk, increment]
  name varchar [not null]
  slug varchar [not null, unique]
  created_at timestamp [null]
  updated_at timestamp [null]
}

//////////////////////////////////////////////////////
// Core domain tables
//////////////////////////////////////////////////////

// Shortened URLs. Every saved link has an owner (no anonymous URLs).
Table links {
  id bigint [pk, increment]
  user_id bigint [ref: > users.id, not null]
  link_status_id bigint [ref: > link_statuses.id, not null]
  original_url text [not null]
  short_code varchar [not null, unique]   // auto-generated or custom
  click_count bigint [not null, default: 0] // denormalized total for fast dashboard reads
  created_at timestamp [null]
  updated_at timestamp [null]

  Indexes {
    short_code [unique]   // redirect lookup
    user_id               // dashboard "my URLs" listing
    link_status_id
  }
}

// Per-click log. One row per successful redirect.
Table clicks {
  id bigint [pk, increment]
  link_id bigint [ref: > links.id, not null]
  device_type_id bigint [ref: > device_types.id, null]
  browser_id bigint [ref: > browsers.id, null]
  referrer varchar [null]
  ip_hash varchar [null]   // hashed IP, never plain text
  clicked_at timestamp [not null]
  created_at timestamp [null]
  updated_at timestamp [null]

  Indexes {
    link_id
    clicked_at
  }
}
```

---

## Notes

### Tables

| Table              | Purpose                                                                 |
|--------------------|-------------------------------------------------------------------------|
| `users`            | Account holders. Laravel default auth table.                            |
| `password_reset_tokens` | Supports US-3.3 password reset. Laravel default.                   |
| `sessions`         | Database session driver (optional). Laravel default.                    |
| `link_statuses`    | Lookup for link state (`active`, `disabled`). Replaces a status enum.   |
| `device_types`     | Lookup for click device (`Desktop`, `Mobile`, `Tablet`, `Bot`, `Unknown`). |
| `browsers`         | Lookup for click browser (`Chrome`, `Firefox`, `Safari`, `Edge`, `Other`). |
| `links`            | Shortened URLs. Maps to US-1.x, US-4.x.                                 |
| `clicks`           | Per-click log. Maps to US-5.2.                                          |

### Design Decisions

- **No enum / status string columns.** `links.link_status_id`, `clicks.device_type_id`, and `clicks.browser_id` reference lookup tables, seeded via seeders.
- **Ownership.** `links.user_id` is **NOT NULL** — every saved URL belongs to a user. A visitor who starts shortening must register/login before the row is created (US-1.1).
- **Short code uniqueness.** `links.short_code` is unique and indexed; it serves both auto-generated and custom codes (US-1.2, US-1.3). Reserved-word validation is enforced in the application layer.
- **Total clicks (US-5.1).** `links.click_count` is a denormalized counter incremented on each redirect, so the dashboard avoids `COUNT(*)` on `clicks`. The authoritative detail lives in `clicks` (US-5.2).
- **Privacy.** `clicks.ip_hash` stores a hashed IP, never the raw address.
- **Soft delete?** Not used — US-4.5 deletes URLs outright (hard delete), and a deleted short code returns 404. Add `deleted_at` only if soft delete is later required.
- **Country.** Deferred to a future version per the project description; add a `countries` lookup + `clicks.country_id` when needed.

### Relationships

- `users` 1—N `links` (a user owns many links)
- `link_statuses` 1—N `links`
- `links` 1—N `clicks`
- `device_types` 1—N `clicks`
- `browsers` 1—N `clicks`
