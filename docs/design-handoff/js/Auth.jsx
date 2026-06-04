/* global React, window */
const { useState: useStateAuth } = React;

function Auth({ go, draft, onAuth, initialMode }) {
  const S = window.Snip;
  const [mode, setMode] = useStateAuth(initialMode || "register");
  const [name, setName] = useStateAuth("");
  const [email, setEmail] = useStateAuth("");
  const [pw, setPw] = useStateAuth("");
  const [errs, setErrs] = useStateAuth({});
  const [working, setWorking] = useStateAuth(false);

  const hasDraft = !!(draft && draft.longUrl);
  const previewCode = draft.customCode || "••••••";

  function submit(e) {
    e.preventDefault();
    const next = {};
    if (mode === "register" && !name.trim()) next.name = "Tell us your name.";
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) next.email = "Enter a valid email.";
    if (pw.length < 6) next.pw = "At least 6 characters.";
    setErrs(next);
    if (Object.keys(next).length) return;
    setWorking(true);
    setTimeout(() => {
      onAuth({ name: name.trim() || email.split("@")[0].replace(/^\w/, (c) => c.toUpperCase()), email: email.trim() });
    }, 650);
  }

  return (
    <div className="fade-up" style={{ minHeight: "calc(100vh - 52px)", display: "grid", gridTemplateColumns: hasDraft ? "1fr 1fr" : "1fr" }}>
      {/* Left — preserved-link panel */}
      {hasDraft && (
        <div style={{ background: "var(--bg-soft)", padding: "64px 56px", display: "flex", flexDirection: "column", justifyContent: "center" }}>
          <div style={{ maxWidth: 420 }}>
            <div className="eyebrow" style={{ display: "inline-flex", alignItems: "center", gap: 6 }}>
              <S.Icon name="check" size={15} /> Your link is ready
            </div>
            <h2 className="h1" style={{ marginTop: 14 }}>One step left — create your account to save it.</h2>
            <p className="body" style={{ marginTop: 14 }}>We're holding your link. Sign up and it lands in your dashboard instantly.</p>

            <div className="card" style={{ padding: 18, marginTop: 28, boxShadow: "none", border: "1px solid var(--line)" }}>
              <div className="small" style={{ color: "var(--ink-3)", marginBottom: 8, fontWeight: 500 }}>SHORTENS TO</div>
              <div className="mono" style={{ fontSize: 18, fontWeight: 600, color: "var(--blue)" }}>{S.DOMAIN}/{previewCode}</div>
              <hr className="divider" style={{ margin: "14px 0" }} />
              <div className="small" style={{ color: "var(--ink-3)", marginBottom: 6, fontWeight: 500 }}>DESTINATION</div>
              <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                <S.Favicon host={S.hostOf(draft.longUrl)} size={26} radius={7} />
                <span className="small" style={{ color: "var(--ink)", wordBreak: "break-all" }}>{S.prettyUrl(draft.longUrl)}</span>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Right — form */}
      <div style={{ display: "grid", placeItems: "center", padding: "56px 32px" }}>
        <div style={{ width: "100%", maxWidth: 360 }}>
          {!hasDraft && <div style={{ marginBottom: 26 }}><S.Logo size={22} /></div>}
          <h1 className="h1">{mode === "register" ? "Create your account" : "Welcome back"}</h1>
          <p className="body" style={{ marginTop: 8, marginBottom: 28 }}>
            {mode === "register" ? "Free forever for personal links." : "Log in to your dashboard."}
          </p>

          <form onSubmit={submit} style={{ display: "flex", flexDirection: "column", gap: 16 }}>
            {mode === "register" && (
              <div className="field">
                <label className="field-label">Name</label>
                <input className={"input" + (errs.name ? " input-err" : "")} value={name}
                  onChange={(e) => setName(e.target.value)} placeholder="Alex Rivera" />
                {errs.name && <span className="err-text">{errs.name}</span>}
              </div>
            )}
            <div className="field">
              <label className="field-label">Email</label>
              <input className={"input" + (errs.email ? " input-err" : "")} value={email} type="email"
                onChange={(e) => setEmail(e.target.value)} placeholder="you@example.com" />
              {errs.email && <span className="err-text">{errs.email}</span>}
            </div>
            <div className="field">
              <label className="field-label">Password</label>
              <input className={"input" + (errs.pw ? " input-err" : "")} value={pw} type="password"
                onChange={(e) => setPw(e.target.value)} placeholder="••••••••" />
              {errs.pw && <span className="err-text">{errs.pw}</span>}
            </div>
            <button type="submit" className="btn btn-primary btn-lg" disabled={working} style={{ marginTop: 4 }}>
              {working ? <S.Icon name="link" size={18} className="spin" />
                : (hasDraft ? "Create account & save link" : (mode === "register" ? "Create account" : "Log in"))}
            </button>
          </form>

          <p className="small" style={{ textAlign: "center", marginTop: 22 }}>
            {mode === "register" ? "Already have an account? " : "New to Snip? "}
            <button className="link-btn" style={{ fontSize: 14 }}
              onClick={() => { setErrs({}); setMode(mode === "register" ? "login" : "register"); }}>
              {mode === "register" ? "Log in" : "Create one"}
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}

window.Snip = Object.assign(window.Snip || {}, { Auth });
