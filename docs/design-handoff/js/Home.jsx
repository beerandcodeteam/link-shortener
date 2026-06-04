/* global React, window */
const { useState: useStateHome, useRef: useRefHome } = React;

function Home({ go, draft, setDraft, links }) {
  const S = window.Snip;
  const [url, setUrl] = useStateHome(draft.longUrl || "");
  const [custom, setCustom] = useStateHome(draft.customCode || "");
  const [showCustom, setShowCustom] = useStateHome(!!draft.customCode);
  const [err, setErr] = useStateHome("");
  const [working, setWorking] = useStateHome(false);

  function validate(v) {
    const t = v.trim();
    if (!t) return "Paste a link to shorten.";
    const re = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/\S*)?$/i;
    if (!re.test(t)) return "That doesn't look like a valid URL.";
    return "";
  }

  function submit(e) {
    e && e.preventDefault();
    const v = validate(url);
    if (v) { setErr(v); return; }
    if (custom && S.RESERVED.includes(custom.toLowerCase())) { setErr(`"${custom}" is a reserved word.`); return; }
    if (custom && !/^[\w-]{3,30}$/.test(custom)) { setErr("Custom code: 3–30 letters, numbers, - or _."); return; }
    setErr("");
    setWorking(true);
    setDraft({ longUrl: url.trim(), customCode: custom.trim() });
    setTimeout(() => go("auth", { mode: "register" }), 500);
  }

  const totalLinks = 12840 + links.length;

  return (
    <div className="fade-up">
      {/* Hero */}
      <section style={{ textAlign: "center", padding: "92px 0 8px" }}>
        <div className="wrap-narrow">
          <div style={{ display: "inline-flex", alignItems: "center", gap: 7, background: "var(--bg-soft)",
            padding: "6px 14px", borderRadius: "var(--r-pill)", fontSize: 13, color: "var(--ink-2)", marginBottom: 26 }}>
            <S.Icon name="bolt" size={14} style={{ color: "var(--blue)" }} />
            Trusted with {S.fmtNum(totalLinks)} short links
          </div>
          <h1 className="display">Make every link<br />short, smart, yours.</h1>
          <p className="body" style={{ fontSize: 21, marginTop: 22, maxWidth: 560, marginInline: "auto" }}>
            Paste a long URL and get a clean short link in a tap — with click tracking,
            custom codes, and a dashboard that keeps it all in one place.
          </p>
        </div>
      </section>

      {/* Shortener card */}
      <section className="wrap-narrow" style={{ marginTop: 38 }}>
        <form className="card" onSubmit={submit} style={{ padding: 22, textAlign: "left" }}>
          <div className="field">
            <div className="input-group" style={err && !custom ? { borderColor: "var(--red)" } : null}>
              <span className="input-prefix"><S.Icon name="link" size={19} /></span>
              <input
                className="input" autoFocus value={url}
                onChange={(e) => { setUrl(e.target.value); setErr(""); }}
                placeholder="Paste a long link, e.g. https://example.com/very/long/path"
                style={{ fontSize: 17 }}
              />
            </div>
          </div>

          {showCustom && (
            <div className="field" style={{ marginTop: 12 }}>
              <div className="input-group">
                <span className="input-prefix" style={{ fontSize: 15, color: "var(--ink-2)" }}>{S.DOMAIN}/</span>
                <input className="input" value={custom}
                  onChange={(e) => { setCustom(e.target.value.replace(/\s/g, "")); setErr(""); }}
                  placeholder="custom-code (optional)" style={{ fontSize: 16 }} />
              </div>
            </div>
          )}

          {err && <div className="err-text" style={{ marginTop: 10 }}>{err}</div>}

          <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginTop: 16, gap: 12 }}>
            <button type="button" className="link-btn small" onClick={() => setShowCustom((s) => !s)}
              style={{ display: "inline-flex", alignItems: "center", gap: 6 }}>
              <S.Icon name={showCustom ? "x" : "plus"} size={15} />
              {showCustom ? "Remove custom code" : "Customize the code"}
            </button>
            <button type="submit" className="btn btn-primary btn-lg" disabled={working} style={{ minWidth: 150 }}>
              {working ? <S.Icon name="link" size={18} className="spin" /> : <>Shorten link <S.Icon name="arrow" size={18} /></>}
            </button>
          </div>
        </form>
        <p className="small" style={{ textAlign: "center", marginTop: 14, color: "var(--ink-3)" }}>
          You'll create a free account to save it — it takes a few seconds and keeps your links forever.
        </p>
      </section>

      {/* Feature strip */}
      <section className="wrap" style={{ marginTop: 96, marginBottom: 40 }}>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: 24 }}>
          {[
            { icon: "spark", t: "Custom codes", d: "Pick a memorable ending like snp.li/launch, or let us generate one." },
            { icon: "chart", t: "Real click analytics", d: "See total clicks, referrers, devices and browsers — updated live." },
            { icon: "shield", t: "Private by design", d: "Visitor IPs are hashed, never stored in the clear. Your links, your data." },
          ].map((f) => (
            <div key={f.t} style={{ padding: "4px 4px" }}>
              <div style={{ width: 44, height: 44, borderRadius: 12, background: "var(--bg-soft)", display: "grid", placeItems: "center", color: "var(--ink)", marginBottom: 16 }}>
                <S.Icon name={f.icon} size={22} />
              </div>
              <h3 className="h3">{f.t}</h3>
              <p className="small" style={{ marginTop: 6 }}>{f.d}</p>
            </div>
          ))}
        </div>
      </section>

      <Footer />
    </div>
  );
}

function Footer() {
  const S = window.Snip;
  return (
    <footer style={{ borderTop: "1px solid var(--line-soft)", marginTop: 40, padding: "28px 0", background: "var(--bg-softer)" }}>
      <div className="wrap" style={{ display: "flex", alignItems: "center", justifyContent: "space-between", flexWrap: "wrap", gap: 12 }}>
        <S.Logo size={16} />
        <p className="small" style={{ color: "var(--ink-3)", margin: 0 }}>A prototype · Snip keeps every link tidy.</p>
      </div>
    </footer>
  );
}

window.Snip = Object.assign(window.Snip || {}, { Home });
