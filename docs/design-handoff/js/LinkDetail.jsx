/* global React, window */
const { useState: useStateDetail } = React;

function LinkDetail({ link, actions, go }) {
  const S = window.Snip;
  if (!link) { go("dashboard"); return null; }
  const [confirmDel, setConfirmDel] = useStateDetail(false);
  const host = S.hostOf(link.longUrl);
  const shortUrl = `${S.DOMAIN}/${link.code}`;
  const disabled = link.status === "disabled";

  // breakdowns
  const byKey = (key) => {
    const m = {};
    link.clickLog.forEach((c) => { m[c[key]] = (m[c[key]] || 0) + 1; });
    return Object.entries(m).sort((a, b) => b[1] - a[1]);
  };
  const refs = byKey("referrer").slice(0, 5);
  const devices = byKey("device").slice(0, 5);
  const logTotal = link.clickLog.length;
  const last7 = link.clickLog.filter((c) => Date.now() - c.at.getTime() < 7 * 86400000).length;

  return (
    <div className="fade-up wrap" style={{ paddingTop: 28, paddingBottom: 80, maxWidth: 960 }}>
      {/* Back */}
      <button className="link-btn small" onClick={() => go("dashboard")}
        style={{ display: "inline-flex", alignItems: "center", gap: 4, marginBottom: 22, color: "var(--ink-2)" }}>
        <S.Icon name="back" size={16} /> All links
      </button>

      {/* Header card */}
      <div className="card" style={{ padding: 24 }}>
        <div style={{ display: "flex", alignItems: "flex-start", gap: 18, flexWrap: "wrap" }}>
          <S.Favicon host={host} size={52} radius={13} />
          <div style={{ minWidth: 0, flex: 1 }}>
            <div style={{ display: "flex", alignItems: "center", gap: 10, flexWrap: "wrap" }}>
              <h1 className="h2" style={{ margin: 0 }}>{link.title}</h1>
              <S.StatusBadge status={link.status} />
            </div>
            <a href={link.longUrl} target="_blank" rel="noreferrer" className="small"
              style={{ display: "inline-flex", alignItems: "center", gap: 5, marginTop: 6, color: "var(--ink-2)", wordBreak: "break-all" }}>
              <S.Icon name="globe" size={14} /> {S.prettyUrl(link.longUrl)}
            </a>
          </div>
        </div>

        <div style={{ display: "flex", alignItems: "center", gap: 12, marginTop: 22, flexWrap: "wrap" }}>
          <div style={{ display: "flex", alignItems: "center", gap: 10, background: "var(--bg-soft)", borderRadius: 12, padding: "11px 16px", flex: 1, minWidth: 220 }}>
            <span className="mono" style={{ fontSize: 17, fontWeight: 600, color: disabled ? "var(--ink-3)" : "var(--blue)", flex: 1 }}>{shortUrl}</span>
          </div>
          <S.CopyButton text={"https://" + shortUrl} label="Copy link" variant="primary" />
          <button className="btn btn-soft" onClick={() => actions.toggle(link.id)}>
            <S.Icon name="power" size={16} /> {disabled ? "Enable" : "Disable"}
          </button>
          <button className="btn btn-danger" onClick={() => setConfirmDel(true)}>
            <S.Icon name="trash" size={16} /> Delete
          </button>
        </div>
      </div>

      {/* Stat row */}
      <div style={{ display: "grid", gridTemplateColumns: "repeat(4, 1fr)", gap: 16, marginTop: 18 }}>
        <MiniStat label="Total clicks" value={S.fmtNum(link.clicks)} />
        <MiniStat label="Last 7 days" value={S.fmtNum(last7)} />
        <MiniStat label="Created" value={S.fmtDate(link.createdAt)} small />
        <MiniStat label="Code" value={"/" + link.code} mono />
      </div>

      {/* Chart */}
      <div className="card" style={{ padding: 24, marginTop: 18 }}>
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 18 }}>
          <h3 className="h3">Clicks over time</h3>
          <span className="small" style={{ color: "var(--ink-3)" }}>Last 14 days</span>
        </div>
        <S.BarChart log={link.clickLog} days={14} height={150} accent={disabled ? "var(--ink-3)" : "var(--blue)"} />
      </div>

      {/* Breakdowns */}
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 18, marginTop: 18 }}>
        <Breakdown title="Top referrers" icon="globe" data={refs} total={logTotal} />
        <Breakdown title="Devices" icon="device" data={devices} total={logTotal} />
      </div>

      {/* Recent clicks log */}
      <div className="card" style={{ padding: 0, marginTop: 18, overflow: "hidden" }}>
        <div style={{ padding: "18px 22px", display: "flex", alignItems: "center", justifyContent: "space-between" }}>
          <h3 className="h3">Recent clicks</h3>
          <span className="small" style={{ color: "var(--ink-3)", display: "inline-flex", alignItems: "center", gap: 5 }}>
            <S.Icon name="shield" size={14} /> IPs stored hashed
          </span>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "1.3fr 1fr 1fr 1.4fr", padding: "0 22px 8px", color: "var(--ink-3)", fontSize: 12, fontWeight: 600, letterSpacing: ".02em" }}>
          <span>WHEN</span><span>REFERRER</span><span>DEVICE</span><span>IP HASH</span>
        </div>
        {link.clickLog.slice(0, 8).map((c, i) => (
          <div key={i} style={{ display: "grid", gridTemplateColumns: "1.3fr 1fr 1fr 1.4fr", alignItems: "center",
            padding: "12px 22px", borderTop: "1px solid var(--line-soft)", fontSize: 14 }}>
            <span style={{ color: "var(--ink)" }}>{S.fmtTime(c.at)}</span>
            <span style={{ color: "var(--ink-2)" }}>{c.referrer}</span>
            <span style={{ color: "var(--ink-2)" }}>{c.browser} · {c.device}</span>
            <span className="mono" style={{ color: "var(--ink-3)", fontSize: 12.5 }}>{c.ipHash}</span>
          </div>
        ))}
        {logTotal === 0 && <div className="small" style={{ padding: "32px 22px", color: "var(--ink-3)" }}>No clicks recorded yet.</div>}
      </div>

      {confirmDel && (
        <S.Modal onClose={() => setConfirmDel(false)} width={400}>
          <div style={{ width: 48, height: 48, borderRadius: "50%", background: "var(--red-tint)", color: "var(--red)", display: "grid", placeItems: "center", marginBottom: 16 }}>
            <S.Icon name="trash" size={22} />
          </div>
          <h2 className="h2">Delete this link?</h2>
          <p className="body" style={{ marginTop: 8, fontSize: 15 }}>
            <span className="mono" style={{ color: "var(--ink)", fontWeight: 600 }}>{shortUrl}</span> will stop redirecting and its history is removed permanently.
          </p>
          <div style={{ display: "flex", gap: 10, marginTop: 24 }}>
            <button className="btn btn-soft" style={{ flex: 1 }} onClick={() => setConfirmDel(false)}>Cancel</button>
            <button className="btn btn-lg" style={{ flex: 1, background: "var(--red)", color: "#fff" }}
              onClick={() => { actions.remove(link.id); go("dashboard"); }}>Delete</button>
          </div>
        </S.Modal>
      )}
    </div>
  );
}

function MiniStat({ label, value, mono, small }) {
  return (
    <div className="card" style={{ padding: "16px 18px" }}>
      <div className="small" style={{ color: "var(--ink-3)", fontSize: 12.5, fontWeight: 500 }}>{label}</div>
      <div className={mono ? "mono" : ""} style={{ fontSize: small ? 17 : 24, fontWeight: 600, letterSpacing: "-0.02em", marginTop: 6 }}>{value}</div>
    </div>
  );
}

function Breakdown({ title, icon, data, total }) {
  const S = window.Snip;
  const max = Math.max(1, ...data.map((d) => d[1]));
  return (
    <div className="card" style={{ padding: 22 }}>
      <h3 className="h3" style={{ display: "flex", alignItems: "center", gap: 8, marginBottom: 16 }}>
        <S.Icon name={icon} size={17} style={{ color: "var(--ink-2)" }} /> {title}
      </h3>
      {data.length === 0 && <p className="small" style={{ color: "var(--ink-3)" }}>No data yet.</p>}
      <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
        {data.map(([k, v]) => (
          <div key={k}>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: 14, marginBottom: 5 }}>
              <span style={{ color: "var(--ink)" }}>{k}</span>
              <span style={{ color: "var(--ink-3)" }}>{v}</span>
            </div>
            <div style={{ height: 6, background: "var(--bg-soft)", borderRadius: 4, overflow: "hidden" }}>
              <div style={{ width: `${(v / max) * 100}%`, height: "100%", background: "var(--blue)", borderRadius: 4, transition: "width .5s cubic-bezier(.2,.8,.2,1)" }} />
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

window.Snip = Object.assign(window.Snip || {}, { LinkDetail });
