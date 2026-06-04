/* global React, window */
const { useState: useStateDash, useEffect: useEffectDash, useRef: useRefDash } = React;

function Dashboard({ user, links, actions, go, openCreate, justCreatedId }) {
  const S = window.Snip;
  const [showCreate, setShowCreate] = useStateDash(false);
  const [sort, setSort] = useStateDash("recent");
  const [query, setQuery] = useStateDash("");
  const [confirmDel, setConfirmDel] = useStateDash(null);

  useEffectDash(() => { if (openCreate) setShowCreate(true); }, [openCreate]);

  const totalClicks = links.reduce((s, l) => s + l.clicks, 0);
  const activeCount = links.filter((l) => l.status === "active").length;

  let view = links.filter((l) =>
    !query || l.title.toLowerCase().includes(query.toLowerCase()) ||
    l.longUrl.toLowerCase().includes(query.toLowerCase()) || l.code.toLowerCase().includes(query.toLowerCase()));
  view = [...view].sort((a, b) =>
    sort === "clicks" ? b.clicks - a.clicks : b.createdAt - a.createdAt);

  return (
    <div className="fade-up wrap" style={{ paddingTop: 40, paddingBottom: 80 }}>
      {/* Header */}
      <div style={{ display: "flex", alignItems: "flex-end", justifyContent: "space-between", flexWrap: "wrap", gap: 16 }}>
        <div>
          <h1 className="h1">Your links</h1>
          <p className="body" style={{ marginTop: 6 }}>Welcome back, {user.name.split(" ")[0]}.</p>
        </div>
        <button className="btn btn-primary" onClick={() => setShowCreate(true)}>
          <S.Icon name="plus" size={17} /> New link
        </button>
      </div>

      {/* Stat cards */}
      <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 16, marginTop: 26 }}>
        <Stat label="Total links" value={S.fmtNum(links.length)} icon="link" />
        <Stat label="Total clicks" value={S.fmtNum(totalClicks)} icon="cursor" />
        <Stat label="Active" value={`${activeCount} of ${links.length}`} icon="bolt" />
      </div>

      {/* Toolbar */}
      <div style={{ display: "flex", alignItems: "center", gap: 12, marginTop: 36, marginBottom: 14 }}>
        <div className="input-group" style={{ flex: 1, maxWidth: 320 }}>
          <span className="input-prefix"><S.Icon name="eye" size={17} /></span>
          <input className="input" value={query} onChange={(e) => setQuery(e.target.value)}
            placeholder="Search links" style={{ fontSize: 14.5, padding: "10px 14px 10px 6px" }} />
        </div>
        <div className="nav-spacer"></div>
        <div style={{ display: "flex", gap: 4, background: "var(--bg-soft)", padding: 3, borderRadius: "var(--r-pill)" }}>
          {[["recent", "Recent"], ["clicks", "Most clicked"]].map(([k, lbl]) => (
            <button key={k} onClick={() => setSort(k)} style={{
              border: "none", background: sort === k ? "#fff" : "transparent", color: sort === k ? "var(--ink)" : "var(--ink-2)",
              fontSize: 13, fontWeight: 500, padding: "6px 14px", borderRadius: "var(--r-pill)", cursor: "pointer",
              boxShadow: sort === k ? "0 1px 3px rgba(0,0,0,.1)" : "none", transition: "all .15s",
            }}>{lbl}</button>
          ))}
        </div>
      </div>

      {/* List */}
      {links.length === 0 ? (
        <EmptyState onCreate={() => setShowCreate(true)} />
      ) : (
        <div className="card" style={{ overflow: "hidden", padding: 0 }}>
          {view.map((l, i) => (
            <LinkRow key={l.id} link={l} first={i === 0} actions={actions} go={go}
              highlight={l.id === justCreatedId} onDelete={() => setConfirmDel(l)} />
          ))}
          {view.length === 0 && (
            <div style={{ padding: "48px", textAlign: "center", color: "var(--ink-3)" }} className="small">No links match "{query}".</div>
          )}
        </div>
      )}

      {showCreate && <CreateModal actions={actions} onClose={() => setShowCreate(false)} go={go} />}
      {confirmDel && (
        <DeleteConfirm link={confirmDel} onClose={() => setConfirmDel(null)}
          onConfirm={() => { actions.remove(confirmDel.id); setConfirmDel(null); }} />
      )}
    </div>
  );
}

function Stat({ label, value, icon }) {
  const S = window.Snip;
  return (
    <div className="card" style={{ padding: "18px 20px", display: "flex", alignItems: "center", gap: 16 }}>
      <div style={{ width: 42, height: 42, borderRadius: 11, background: "var(--bg-soft)", display: "grid", placeItems: "center", color: "var(--ink-2)" }}>
        <S.Icon name={icon} size={20} />
      </div>
      <div>
        <div style={{ fontSize: 26, fontWeight: 600, letterSpacing: "-0.02em", lineHeight: 1 }}>{value}</div>
        <div className="small" style={{ marginTop: 4, color: "var(--ink-3)" }}>{label}</div>
      </div>
    </div>
  );
}

function LinkRow({ link, first, actions, go, highlight, onDelete }) {
  const S = window.Snip;
  const host = S.hostOf(link.longUrl);
  const shortUrl = `${S.DOMAIN}/${link.code}`;
  const disabled = link.status === "disabled";
  return (
    <div onClick={() => go("detail", { id: link.id })}
      style={{
        display: "flex", alignItems: "center", gap: 16, padding: "16px 20px", cursor: "pointer",
        borderTop: first ? "none" : "1px solid var(--line-soft)",
        background: highlight ? "var(--blue-tint)" : "transparent", transition: "background .25s",
      }}
      onMouseEnter={(e) => { if (!highlight) e.currentTarget.style.background = "var(--bg-softer)"; }}
      onMouseLeave={(e) => { if (!highlight) e.currentTarget.style.background = "transparent"; }}>
      <S.Favicon host={host} size={40} />
      <div style={{ minWidth: 0, flex: 1 }}>
        <div style={{ display: "flex", alignItems: "center", gap: 9 }}>
          <span className="mono" style={{ fontSize: 15, fontWeight: 600, color: disabled ? "var(--ink-3)" : "var(--blue)" }}>{shortUrl}</span>
          {disabled && <S.StatusBadge status="disabled" />}
        </div>
        <div className="small" style={{ marginTop: 3, whiteSpace: "nowrap", overflow: "hidden", textOverflow: "ellipsis", maxWidth: 380 }}>
          {link.title} · {S.prettyUrl(link.longUrl)}
        </div>
      </div>

      <div style={{ width: 96, textAlign: "right", flex: "0 0 auto" }}>
        <div style={{ fontSize: 17, fontWeight: 600, letterSpacing: "-0.01em" }}>{S.fmtNum(link.clicks)}</div>
        <div className="small" style={{ color: "var(--ink-3)", fontSize: 12 }}>clicks</div>
      </div>

      <div style={{ width: 96, flex: "0 0 auto", display: "grid", placeItems: "center" }}>
        <S.Sparkline log={link.clickLog} accent={disabled ? "var(--ink-3)" : "var(--blue)"} />
      </div>

      <div style={{ display: "flex", alignItems: "center", gap: 2, flex: "0 0 auto" }} onClick={(e) => e.stopPropagation()}>
        <S.CopyButton text={"https://" + shortUrl} />
        <button className="btn-icon" title={disabled ? "Enable" : "Disable"} onClick={() => actions.toggle(link.id)}
          style={disabled ? { color: "var(--amber)" } : null}>
          <S.Icon name="power" size={18} />
        </button>
        <button className="btn-icon" title="Delete" onClick={onDelete}
          onMouseEnter={(e) => e.currentTarget.style.color = "var(--red)"}
          onMouseLeave={(e) => e.currentTarget.style.color = ""}>
          <S.Icon name="trash" size={18} />
        </button>
        <button className="btn-icon" title="Details" onClick={() => go("detail", { id: link.id })}>
          <S.Icon name="chevron" size={18} />
        </button>
      </div>
    </div>
  );
}

function EmptyState({ onCreate }) {
  const S = window.Snip;
  return (
    <div className="card" style={{ padding: "72px 32px", textAlign: "center" }}>
      <div style={{ width: 60, height: 60, borderRadius: 16, background: "var(--bg-soft)", display: "grid", placeItems: "center", margin: "0 auto 20px", color: "var(--ink-2)" }}>
        <S.Icon name="link" size={28} />
      </div>
      <h3 className="h2">No links yet</h3>
      <p className="body" style={{ marginTop: 8, maxWidth: 360, marginInline: "auto" }}>Shorten your first URL and it'll show up here with live click tracking.</p>
      <button className="btn btn-primary btn-lg" style={{ marginTop: 24 }} onClick={onCreate}>
        <S.Icon name="plus" size={18} /> Create a link
      </button>
    </div>
  );
}

/* ---------------- Create modal ---------------- */
function CreateModal({ actions, onClose, go }) {
  const S = window.Snip;
  const [url, setUrl] = useStateDash("");
  const [custom, setCustom] = useStateDash("");
  const [err, setErr] = useStateDash("");
  const [created, setCreated] = useStateDash(null);

  function submit(e) {
    e.preventDefault();
    const t = url.trim();
    if (!/^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/\S*)?$/i.test(t)) { setErr("Enter a valid URL."); return; }
    if (custom && S.RESERVED.includes(custom.toLowerCase())) { setErr(`"${custom}" is reserved.`); return; }
    if (custom && !/^[\w-]{3,30}$/.test(custom)) { setErr("Code: 3–30 letters, numbers, - or _."); return; }
    if (custom && actions.codeExists(custom)) { setErr(`snp.li/${custom} is taken.`); return; }
    const link = actions.create({ longUrl: t, customCode: custom.trim() });
    setCreated(link);
  }

  if (created) {
    const shortUrl = `${S.DOMAIN}/${created.code}`;
    return (
      <S.Modal onClose={onClose}>
        <div style={{ textAlign: "center" }}>
          <div style={{ width: 54, height: 54, borderRadius: "50%", background: "var(--green-tint)", color: "var(--green)", display: "grid", placeItems: "center", margin: "0 auto 16px" }}>
            <S.Icon name="check" size={28} />
          </div>
          <h2 className="h2">Link created</h2>
          <p className="small" style={{ marginTop: 6 }}>Your short link is live and tracking clicks.</p>
          <div style={{ display: "flex", alignItems: "center", gap: 10, background: "var(--bg-soft)", borderRadius: 12, padding: "14px 16px", marginTop: 20 }}>
            <span className="mono" style={{ fontSize: 17, fontWeight: 600, color: "var(--blue)", flex: 1, textAlign: "left" }}>{shortUrl}</span>
            <S.CopyButton text={"https://" + shortUrl} label="Copy" variant="primary" />
          </div>
          <div style={{ display: "flex", gap: 10, marginTop: 22 }}>
            <button className="btn btn-soft" style={{ flex: 1 }} onClick={onClose}>Done</button>
            <button className="btn btn-primary" style={{ flex: 1 }} onClick={() => { onClose(); go("detail", { id: created.id }); }}>View details</button>
          </div>
        </div>
      </S.Modal>
    );
  }

  return (
    <S.Modal onClose={onClose}>
      <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: 6 }}>
        <h2 className="h2">New short link</h2>
        <button className="btn-icon" onClick={onClose}><S.Icon name="x" size={18} /></button>
      </div>
      <form onSubmit={submit} style={{ marginTop: 14 }}>
        <div className="field">
          <label className="field-label">Destination URL</label>
          <div className="input-group" style={err ? { borderColor: "var(--red)" } : null}>
            <span className="input-prefix"><S.Icon name="link" size={18} /></span>
            <input className="input" autoFocus value={url} onChange={(e) => { setUrl(e.target.value); setErr(""); }}
              placeholder="https://example.com/long/path" />
          </div>
        </div>
        <div className="field" style={{ marginTop: 14 }}>
          <label className="field-label">Custom code <span style={{ color: "var(--ink-3)", fontWeight: 400 }}>· optional</span></label>
          <div className="input-group">
            <span className="input-prefix" style={{ fontSize: 15 }}>{S.DOMAIN}/</span>
            <input className="input" value={custom} onChange={(e) => { setCustom(e.target.value.replace(/\s/g, "")); setErr(""); }}
              placeholder="leave blank to auto-generate" />
          </div>
        </div>
        {err && <div className="err-text" style={{ marginTop: 10 }}>{err}</div>}
        <button type="submit" className="btn btn-primary btn-lg" style={{ width: "100%", marginTop: 20 }}>Create short link</button>
      </form>
    </S.Modal>
  );
}

function DeleteConfirm({ link, onClose, onConfirm }) {
  const S = window.Snip;
  return (
    <S.Modal onClose={onClose} width={400}>
      <div style={{ width: 48, height: 48, borderRadius: "50%", background: "var(--red-tint)", color: "var(--red)", display: "grid", placeItems: "center", marginBottom: 16 }}>
        <S.Icon name="trash" size={22} />
      </div>
      <h2 className="h2">Delete this link?</h2>
      <p className="body" style={{ marginTop: 8, fontSize: 15 }}>
        <span className="mono" style={{ color: "var(--ink)", fontWeight: 600 }}>{S.DOMAIN}/{link.code}</span> will stop redirecting and its {S.fmtNum(link.clicks)} clicks of history will be removed. This can't be undone.
      </p>
      <div style={{ display: "flex", gap: 10, marginTop: 24 }}>
        <button className="btn btn-soft" style={{ flex: 1 }} onClick={onClose}>Cancel</button>
        <button className="btn btn-lg" style={{ flex: 1, background: "var(--red)", color: "#fff" }} onClick={onConfirm}>Delete</button>
      </div>
    </S.Modal>
  );
}

window.Snip = Object.assign(window.Snip || {}, { Dashboard });
