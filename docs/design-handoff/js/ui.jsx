/* global React, window */
const { useState: useStateUI, useEffect: useEffectUI, useRef: useRefUI } = React;
const S = window.Snip;

/* ---------------- Favicon chip ---------------- */
function Favicon({ host, size = 34, radius = 9 }) {
  const color = S.faviconColor(host);
  const letter = (host || "?").replace(/^https?:\/\//, "")[0]?.toUpperCase() || "?";
  return (
    <span style={{
      width: size, height: size, borderRadius: radius, flex: "0 0 auto",
      display: "grid", placeItems: "center", color: "#fff", fontWeight: 600,
      fontSize: size * 0.44, background: color, letterSpacing: 0,
    }}>{letter}</span>
  );
}

/* ---------------- Copy button ---------------- */
function CopyButton({ text, label, onCopied, variant = "soft" }) {
  const [done, setDone] = useStateUI(false);
  function copy(e) {
    e.stopPropagation();
    try { navigator.clipboard?.writeText(text); } catch {}
    setDone(true);
    onCopied && onCopied();
    setTimeout(() => setDone(false), 1400);
  }
  if (label) {
    return (
      <button className={`btn btn-${variant} btn-sm`} onClick={copy}>
        <S.Icon name={done ? "check" : "copy"} size={16} />
        {done ? "Copied" : label}
      </button>
    );
  }
  return (
    <button className="btn-icon" onClick={copy} title="Copy" style={done ? { color: "var(--green)" } : null}>
      <S.Icon name={done ? "check" : "copy"} size={18} />
    </button>
  );
}

/* ---------------- Toast ---------------- */
function Toast({ toast }) {
  if (!toast) return null;
  return (
    <div className="toast-wrap">
      <div className="toast" key={toast.id}>
        <S.Icon name={toast.icon || "check"} size={18} style={{ color: "#4ade80" }} />
        {toast.msg}
      </div>
    </div>
  );
}

/* ---------------- Modal ---------------- */
function Modal({ children, onClose, width }) {
  useEffectUI(() => {
    function esc(e) { if (e.key === "Escape") onClose(); }
    window.addEventListener("keydown", esc);
    return () => window.removeEventListener("keydown", esc);
  }, []);
  return (
    <div className="scrim" onMouseDown={onClose}>
      <div className="modal" style={width ? { maxWidth: width } : null} onMouseDown={(e) => e.stopPropagation()}>
        {children}
      </div>
    </div>
  );
}

/* ---------------- Status badge ---------------- */
function StatusBadge({ status }) {
  if (status === "active") return <span className="badge badge-active"><span className="dot"></span>Active</span>;
  return <span className="badge badge-disabled"><span className="dot"></span>Disabled</span>;
}

/* ---------------- Mini bar chart (clicks over time) ---------------- */
function BarChart({ log, days = 14, height = 120, accent = "var(--blue)" }) {
  const buckets = new Array(days).fill(0);
  const now = Date.now();
  log.forEach((c) => {
    const d = Math.floor((now - c.at.getTime()) / 86400000);
    if (d >= 0 && d < days) buckets[days - 1 - d]++;
  });
  const max = Math.max(1, ...buckets);
  return (
    <div style={{ display: "flex", alignItems: "flex-end", gap: 5, height }}>
      {buckets.map((v, i) => (
        <div key={i} style={{ flex: 1, display: "flex", flexDirection: "column", justifyContent: "flex-end", height: "100%" }}>
          <div title={`${v} clicks`} style={{
            height: `${(v / max) * 100}%`, minHeight: v > 0 ? 4 : 2,
            background: v > 0 ? accent : "var(--line-soft)",
            borderRadius: 4, transition: "height .4s cubic-bezier(.2,.8,.2,1)",
          }} />
        </div>
      ))}
    </div>
  );
}

/* ---------------- Sparkline ---------------- */
function Sparkline({ log, days = 14, width = 96, height = 30, accent = "var(--blue)" }) {
  const buckets = new Array(days).fill(0);
  const now = Date.now();
  log.forEach((c) => {
    const d = Math.floor((now - c.at.getTime()) / 86400000);
    if (d >= 0 && d < days) buckets[days - 1 - d]++;
  });
  const max = Math.max(1, ...buckets);
  const step = width / (days - 1);
  const pts = buckets.map((v, i) => `${i * step},${height - (v / max) * (height - 4) - 2}`).join(" ");
  return (
    <svg width={width} height={height} style={{ display: "block" }}>
      <polyline points={pts} fill="none" stroke={accent} strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/* ---------------- Nav bar ---------------- */
function NavBar({ user, route, go, onLogout }) {
  return (
    <nav className="nav">
      <div className="nav-inner">
        <a onClick={() => go(user ? "dashboard" : "home")} style={{ cursor: "pointer" }}><S.Logo /></a>
        {!user && (
          <div className="nav-links">
            <a className="nav-link" onClick={() => go("home")}>Shorten</a>
            <a className="nav-link" onClick={() => go("home")}>Features</a>
            <a className="nav-link" onClick={() => go("home")}>Pricing</a>
          </div>
        )}
        {user && (
          <div className="nav-links">
            <a className="nav-link" onClick={() => go("dashboard")} style={route === "dashboard" || route === "detail" ? { opacity: 1, fontWeight: 500 } : null}>Links</a>
            <a className="nav-link" onClick={() => go("dashboard")}>Analytics</a>
          </div>
        )}
        <div className="nav-spacer"></div>
        {!user ? (
          <div style={{ display: "flex", alignItems: "center", gap: 8 }}>
            <button className="btn btn-ghost btn-sm" onClick={() => go("auth", { mode: "login" })}>Log in</button>
            <button className="btn btn-primary btn-sm" onClick={() => go("auth", { mode: "register" })}>Sign up</button>
          </div>
        ) : (
          <div style={{ display: "flex", alignItems: "center", gap: 14 }}>
            <button className="btn btn-primary btn-sm" onClick={() => go("dashboard", { create: true })}>
              <S.Icon name="plus" size={16} /> New link
            </button>
            <Avatar user={user} onLogout={onLogout} go={go} />
          </div>
        )}
      </div>
    </nav>
  );
}

function Avatar({ user, onLogout }) {
  const [open, setOpen] = useStateUI(false);
  const ref = useRefUI();
  useEffectUI(() => {
    function out(e) { if (ref.current && !ref.current.contains(e.target)) setOpen(false); }
    window.addEventListener("mousedown", out);
    return () => window.removeEventListener("mousedown", out);
  }, []);
  const initials = user.name.split(" ").map((w) => w[0]).slice(0, 2).join("").toUpperCase();
  return (
    <div ref={ref} style={{ position: "relative" }}>
      <button onClick={() => setOpen((o) => !o)} style={{
        width: 32, height: 32, borderRadius: "50%", border: "none", cursor: "pointer",
        background: "var(--ink)", color: "#fff", fontSize: 12.5, fontWeight: 600, letterSpacing: 0,
      }}>{initials}</button>
      {open && (
        <div style={{
          position: "absolute", right: 0, top: 40, width: 220, background: "#fff",
          borderRadius: 14, boxShadow: "var(--shadow-pop)", border: "1px solid var(--line-soft)",
          padding: 8, zIndex: 100, animation: "modal-in .18s ease",
        }}>
          <div style={{ padding: "8px 10px 10px" }}>
            <div style={{ fontSize: 14, fontWeight: 600 }}>{user.name}</div>
            <div style={{ fontSize: 12.5, color: "var(--ink-3)" }}>{user.email}</div>
          </div>
          <hr className="divider" />
          <button className="menu-item" onClick={onLogout} style={{
            width: "100%", textAlign: "left", border: "none", background: "none",
            padding: "9px 10px", borderRadius: 8, fontSize: 14, color: "var(--ink)", cursor: "pointer",
          }} onMouseEnter={(e)=>e.currentTarget.style.background="var(--bg-soft)"} onMouseLeave={(e)=>e.currentTarget.style.background="none"}>Sign out</button>
        </div>
      )}
    </div>
  );
}

window.Snip = Object.assign(window.Snip || {}, {
  Favicon, CopyButton, Toast, Modal, StatusBadge, BarChart, Sparkline, NavBar, Avatar,
});
