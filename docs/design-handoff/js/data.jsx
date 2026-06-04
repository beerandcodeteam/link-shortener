/* global React */
const { useState, useEffect, useRef } = React;

const DOMAIN = "snp.li";

/* ---------------- Icons (stroke, Apple-like) ---------------- */
function Icon({ name, size = 20, stroke = 1.7, style }) {
  const p = { width: size, height: size, viewBox: "0 0 24 24", fill: "none",
    stroke: "currentColor", strokeWidth: stroke, strokeLinecap: "round", strokeLinejoin: "round", style };
  const paths = {
    link: <><path d="M9 15l6-6"/><path d="M11 6l1-1a4.5 4.5 0 0 1 6.4 6.4l-1 1"/><path d="M13 18l-1 1A4.5 4.5 0 0 1 5.6 12.6l1-1"/></>,
    arrow: <><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></>,
    copy: <><rect x="9" y="9" width="11" height="11" rx="2.4"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></>,
    check: <path d="M5 12.5l4.2 4.2L19 7"/>,
    plus: <><path d="M12 5v14"/><path d="M5 12h14"/></>,
    trash: <><path d="M4 7h16"/><path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/><path d="M6 7l1 13a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-13"/></>,
    chart: <><path d="M4 19V5"/><path d="M4 19h16"/><rect x="7" y="11" width="3" height="5" rx="0.6" fill="currentColor" stroke="none"/><rect x="12" y="7" width="3" height="9" rx="0.6" fill="currentColor" stroke="none"/><rect x="17" y="13" width="3" height="3" rx="0.6" fill="currentColor" stroke="none"/></>,
    cursor: <><path d="M5 3l14 7-6 1.6L9.6 17z"/></>,
    eye: <><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="2.6"/></>,
    power: <><path d="M12 4v8"/><path d="M7.5 7.5a7 7 0 1 0 9 0"/></>,
    chevron: <path d="M9 6l6 6-6 6"/>,
    back: <path d="M15 6l-6 6 6 6"/>,
    x: <><path d="M6 6l12 12"/><path d="M18 6L6 18"/></>,
    globe: <><circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a14 14 0 0 1 0 18 14 14 0 0 1 0-18z"/></>,
    clock: <><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/></>,
    qr: <><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><path d="M14 14h2v2M20 14v0M16 18v2h-2M20 18v2"/></>,
    bolt: <path d="M13 3L5 13h6l-1 8 8-10h-6z"/>,
    shield: <><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/></>,
    spark: <path d="M12 3l1.8 5.4L19 10l-5.2 1.6L12 17l-1.8-5.4L5 10l5.2-1.6z"/>,
    sort: <><path d="M8 4v16M8 20l-3-3M8 4l3 3"/></>,
    device: <><rect x="6" y="3" width="12" height="18" rx="2.4"/><path d="M11 18h2"/></>,
    desktop: <><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M9 20h6M12 16v4"/></>,
  };
  return <svg {...p}>{paths[name] || null}</svg>;
}

/* ---------------- Logo ---------------- */
function Logo({ size = 19 }) {
  return (
    <span className="logo" style={{ fontSize: size }}>
      <span className="logo-mark" style={{ width: size * 1.35, height: size * 1.35 }}>
        <svg width={size * 1.35} height={size * 1.35} viewBox="0 0 28 28" fill="none">
          <rect width="28" height="28" rx="7.5" fill="#1d1d1f"/>
          <path d="M11 17l6-6" stroke="#fff" strokeWidth="1.9" strokeLinecap="round"/>
          <path d="M12.6 8.4l1-1a3.4 3.4 0 0 1 4.8 4.8l-1 1" stroke="#fff" strokeWidth="1.9" strokeLinecap="round"/>
          <path d="M15.4 19.6l-1 1a3.4 3.4 0 0 1-4.8-4.8l1-1" stroke="#fff" strokeWidth="1.9" strokeLinecap="round"/>
        </svg>
      </span>
      Snip
    </span>
  );
}

/* ---------------- Helpers ---------------- */
const CODE_CHARS = "abcdefghijkmnpqrstuvwxyz23456789";
function genCode(len = 6) {
  let s = "";
  for (let i = 0; i < len; i++) s += CODE_CHARS[Math.floor(Math.random() * CODE_CHARS.length)];
  return s;
}
const RESERVED = ["login","register","dashboard","api","admin","about","pricing","help","settings","terms","privacy"];

function prettyUrl(url) {
  try {
    const u = new URL(url.startsWith("http") ? url : "https://" + url);
    return u.hostname.replace(/^www\./, "") + (u.pathname === "/" ? "" : u.pathname);
  } catch { return url; }
}
function hostOf(url) {
  try { return new URL(url.startsWith("http") ? url : "https://" + url).hostname.replace(/^www\./, ""); }
  catch { return url; }
}
function faviconColor(host) {
  let h = 0; for (let i = 0; i < host.length; i++) h = (h * 31 + host.charCodeAt(i)) % 360;
  return `hsl(${h} 62% 55%)`;
}
function relTime(d) {
  const diff = (Date.now() - d.getTime()) / 1000;
  if (diff < 60) return "just now";
  if (diff < 3600) return Math.floor(diff / 60) + "m ago";
  if (diff < 86400) return Math.floor(diff / 3600) + "h ago";
  const days = Math.floor(diff / 86400);
  if (days < 7) return days + "d ago";
  return d.toLocaleDateString("en-US", { month: "short", day: "numeric" });
}
function fmtDate(d) { return d.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" }); }
function fmtNum(n) { return n.toLocaleString("en-US"); }
function fmtTime(d) { return d.toLocaleString("en-US", { month: "short", day: "numeric", hour: "numeric", minute: "2-digit" }); }

/* ---------------- Click log generation ---------------- */
const BROWSERS = ["Safari","Chrome","Chrome","Firefox","Edge","Safari","Chrome"];
const DEVICES = ["iPhone","Mac","Mac","Windows","iPad","Android","iPhone"];
const REFERRERS = ["Direct","x.com","Direct","google.com","linkedin.com","instagram.com","Direct","newsletter","github.com","reddit.com"];
function fakeHash() {
  const h = "0123456789abcdef";
  let s = ""; for (let i = 0; i < 16; i++) s += h[Math.floor(Math.random() * 16)];
  return "sha256:" + s + "…";
}
function genClickLog(count, sinceDays) {
  const out = [];
  const now = Date.now();
  for (let i = 0; i < count; i++) {
    const ago = Math.pow(Math.random(), 1.7) * sinceDays * 86400000;
    const at = new Date(now - ago);
    const dev = DEVICES[Math.floor(Math.random() * DEVICES.length)];
    out.push({
      at,
      browser: BROWSERS[Math.floor(Math.random() * BROWSERS.length)],
      device: dev,
      referrer: REFERRERS[Math.floor(Math.random() * REFERRERS.length)],
      ipHash: fakeHash(),
    });
  }
  return out.sort((a, b) => b.at - a.at);
}

let _id = 100;
function makeLink({ longUrl, code, title, clicks, days, status = "active" }) {
  const created = new Date(Date.now() - days * 86400000);
  return {
    id: ++_id,
    longUrl, code,
    title: title || hostOf(longUrl),
    clicks,
    status,
    createdAt: created,
    clickLog: genClickLog(Math.min(clicks, 60), days),
  };
}

function seedLinks() {
  return [
    makeLink({ longUrl: "https://www.figma.com/community/file/1284881935616",  title: "Design handoff kit — Figma", code: "fig24", clicks: 3471, days: 26 }),
    makeLink({ longUrl: "https://open.spotify.com/playlist/37i9dQZF1DXcBWIGoYBM5M", title: "Friday release playlist", code: "fri", clicks: 1980, days: 12 }),
    makeLink({ longUrl: "https://github.com/snip-app/sdk/releases/tag/v2.0.0", title: "SDK v2.0 release notes", code: "sdk2", clicks: 842, days: 6 }),
    makeLink({ longUrl: "https://docs.google.com/document/d/1aZ_q2k9", title: "Q3 launch brief", code: "q3brief", clicks: 366, days: 9 }),
    makeLink({ longUrl: "https://www.eventbrite.com/e/product-meetup-tickets-88213", title: "Product meetup — RSVP", code: "meetup", clicks: 154, days: 3 }),
    makeLink({ longUrl: "https://snip.li/blog/short-links-that-convert", title: "Blog — links that convert", code: "blog7", clicks: 41, days: 1 }),
    makeLink({ longUrl: "https://www.notion.so/Old-careers-page-deprecated", title: "Careers (old)", code: "jobs", clicks: 612, days: 40, status: "disabled" }),
  ];
}

window.Snip = Object.assign(window.Snip || {}, {
  DOMAIN, Icon, Logo,
  genCode, RESERVED, prettyUrl, hostOf, faviconColor,
  relTime, fmtDate, fmtNum, fmtTime, genClickLog, makeLink, seedLinks,
});
