# Overview

This project is a URL Shortener application with a public area and an authenticated user dashboard.

The public area allows visitors to create short URLs quickly and access existing short links through redirection. When someone visits a shortened URL, the system redirects the user to the original destination and records the click.

Authenticated users can log in to manage their own shortened URLs, view their list of links, track click counts, and monitor basic usage data.

The goal of the project is to provide a simple, clean, and reliable URL shortening service with a modern Laravel stack, clear separation between public and private areas, and a testable architecture.

# Key Concepts

## URL Shortening

Users can submit a long URL and receive a short, unique URL that redirects to the original destination.

Each shortened URL should have:

- Original URL
- Short code (auto-generated, or a custom code requested by the user)
- Owner user (every saved URL belongs to an authenticated user)
- Click count
- Creation date
- Optional status, such as active or disabled

The short code can be auto-generated or chosen by the user as a custom code. Any user can request a custom code, subject to uniqueness validation and reserved-word rules. When no custom code is provided, the system generates a unique one.

## Public Redirection

When a visitor accesses a short URL, the application must:

1. Find the matching short code.
2. Validate if the URL exists and is active.
3. Register the click.
4. Redirect the visitor to the original URL.

If the short code does not exist, the system should return a 404 page.

## Public Shortener Page

The homepage allows visitors to paste a long URL and start generating a shortened link.

A visitor can begin the creation process on the public homepage, but to actually save the short URL they must register or log in. The entered long URL (and optional custom code) is preserved through the authentication flow, and the URL is created and linked to the user's account once registration/login completes. There are no permanently anonymous URLs — every saved URL has an owner.

## Authentication

Users can create an account, log in, and access a private dashboard.

The authenticated area should only show data owned by the current user.

## User Dashboard

The dashboard allows users to:

- View their shortened URLs
- Create new short URLs
- Copy the generated short link
- See the number of clicks per URL
- Delete or disable URLs
- Access basic link details

## Click Tracking

Each redirect increases the total click counter for that URL and also records a per-click log entry.

The first version stores both a total click count and a detailed log for each click, including:

- Click date and time
- Referrer
- Browser
- Device
- IP hash (IP stored hashed, never in plain text)

The IP is stored hashed for privacy. Logging must not block or noticeably delay the redirect. Country may be added in a future version.

# Tech Stack

## Backend

- PHP 8.5
- Laravel 13

## Frontend

- Livewire 4
- Blade
- Tailwind CSS

## Testing

- Pest 4

## Database

- PostgreSQL

## Main Laravel Features

- Routing
- Controllers
- Form Requests
- Eloquent Models
- Migrations
- Policies
- Authentication
- Livewire Components
- Feature Tests with Pest

# Core Workflows

## 1. Create a Short URL from the Public Homepage

1. Visitor opens the homepage.
2. Visitor submits a long URL (optionally requesting a custom code).
3. The system validates the URL and the optional custom code.
4. If the visitor is not authenticated, the system preserves the entered URL and custom code and routes the visitor to register/login.
5. After successful registration/login, the system generates a unique short code (or uses the requested custom code) and stores the URL linked to the user.
6. The system returns the shortened URL to the user.

## 2. Redirect a Short URL

1. Visitor accesses a URL using a short code.
2. The system searches for the short code in the database.
3. If the short code does not exist, the system returns a 404 page.
4. If the short code exists but is disabled, the system returns an error page.
5. If the short code is valid, the system increments the click counter.
6. The system redirects the visitor to the original URL.

## 3. User Registration and Login

1. User creates an account or logs in.
2. The system authenticates the user.
3. The user is redirected to the dashboard.
4. The dashboard displays only the URLs owned by the authenticated user.

## 4. Create a Short URL from the Dashboard

1. Authenticated user opens the dashboard.
2. User submits a long URL (optionally requesting a custom code).
3. The system validates the URL and the optional custom code.
4. The system generates a unique short code, or uses the requested custom code if available.
5. The system stores the URL linked to the authenticated user.
6. The new short URL appears in the user dashboard.

## 5. View User URLs

1. Authenticated user accesses the dashboard.
2. The system loads only URLs owned by the current user.
3. The dashboard displays each URL with its original URL, short URL, click count, status, and creation date.

## 6. Delete or Disable a Short URL

1. Authenticated user selects one of their URLs.
2. User chooses to delete or disable the URL.
3. The system verifies ownership.
4. The system updates or removes the URL.
5. The URL is no longer available for redirection if disabled or deleted.

## 7. Track Click Count

1. Visitor accesses a short URL.
2. The system validates the short code.
3. Before redirecting, the system increments the total click counter and records a per-click log entry (date/time, referrer, browser, device, hashed IP).
4. The updated click count and log become visible in the owner dashboard.