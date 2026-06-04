/* global React, ReactDOM, window */
const { useState: useStateApp, useEffect: useEffectApp, useRef: useRefApp } = React;

const ACCENTS = {
  "Apple Blue": { blue: "#0071e3", press: "#0058b0", tint: "#e8f1fd" },
  "Graphite":   { blue: "#1d1d1f", press: "#000000", tint: "#ececef" },
  "Evergreen":  { blue: "#1d9d57", press: "#157544", tint: "#e6f5ec" },
  "Indigo":     { blue: "#5e5ce6", press: "#4744c4", tint: "#ecebfc" },
};
const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
  "accent": "Apple Blue",
  "corners": "Rounded"
}/*EDITMODE-END*/;

function App() {
  const S = window.Snip;
  const [t, setTweak] = useTweaks(TWEAK_DEFAULTS);
  const [route, setRoute] = useStateApp("home");        // home | auth | dashboard | detail
  const [routeParams, setRouteParams] = useStateApp({});
  const [user, setUser] = useStateApp(null);
  const [links, setLinks] = useStateApp([]);
  const [draft, setDraft] = useStateApp({ longUrl: "", customCode: "" });
  const [toast, setToast] = useStateApp(null);
  const [justCreatedId, setJustCreatedId] = useStateApp(null);
  const toastTimer = useRefApp();

  function showToast(msg, icon = "check") {
    const id = Date.now();
    setToast({ id, msg, icon });
    clearTimeout(toastTimer.current);
    toastTimer.current = setTimeout(() => setToast(null), 2200);
  }

  function go(r, params = {}) {
    setRoute(r);
    setRouteParams(params);
    window.scrollTo({ top: 0, behavior: "auto" });
  }

  // ---- auth completion: create the pending link & enter dashboard ----
  function onAuth(u) {
    setUser(u);
    const seeded = S.seedLinks();
    let createdId = null;
    if (draft.longUrl) {
      const code = draft.customCode || S.genCode();
      const fresh = S.makeLink({ longUrl: draft.longUrl, code, clicks: 0, days: 0 });
      fresh.title = S.hostOf(draft.longUrl);
      fresh.clickLog = [];
      seeded.unshift(fresh);
      createdId = fresh.id;
    }
    setLinks(seeded);
    setDraft({ longUrl: "", customCode: "" });
    go("dashboard", createdId ? { create: false } : {});
    if (createdId) {
      setJustCreatedId(createdId);
      showToast("Link saved to your dashboard", "check");
      setTimeout(() => setJustCreatedId(null), 2600);
    }
  }

  function logout() {
    setUser(null);
    setLinks([]);
    go("home");
  }

  // ---- link actions ----
  const actions = {
    codeExists: (code) => links.some((l) => l.code.toLowerCase() === code.toLowerCase()),
    create: ({ longUrl, customCode }) => {
      let code = customCode;
      if (!code) { do { code = S.genCode(); } while (actions.codeExists(code)); }
      const link = S.makeLink({ longUrl, code, clicks: 0, days: 0 });
      link.title = S.hostOf(longUrl);
      link.clickLog = [];
      setLinks((prev) => [link, ...prev]);
      setJustCreatedId(link.id);
      setTimeout(() => setJustCreatedId(null), 2600);
      showToast("Short link created", "check");
      return link;
    },
    toggle: (id) => {
      let nowDisabled = false;
      setLinks((prev) => prev.map((l) => {
        if (l.id !== id) return l;
        nowDisabled = l.status === "active";
        return { ...l, status: l.status === "active" ? "disabled" : "active" };
      }));
      showToast(nowDisabled ? "Link disabled" : "Link enabled", "power");
    },
    remove: (id) => {
      setLinks((prev) => prev.filter((l) => l.id !== id));
      showToast("Link deleted", "trash");
    },
  };

  // ---- apply tweaks to CSS variables ----
  useEffectApp(() => {
    const r = document.documentElement;
    const a = ACCENTS[t.accent] || ACCENTS["Apple Blue"];
    r.style.setProperty("--blue", a.blue);
    r.style.setProperty("--blue-press", a.press);
    r.style.setProperty("--blue-tint", a.tint);
    const corners = t.corners === "Sharp"
      ? { pill: "8px", lg: "6px", md: "5px", sm: "4px" }
      : { pill: "980px", lg: "20px", md: "14px", sm: "10px" };
    r.style.setProperty("--r-pill", corners.pill);
    r.style.setProperty("--r-lg", corners.lg);
    r.style.setProperty("--r-md", corners.md);
    r.style.setProperty("--r-sm", corners.sm);
  }, [t.accent, t.corners]);

  const selected = links.find((l) => l.id === routeParams.id);

  return (
    <>
      {route !== "auth" && <S.NavBar user={user} route={route} go={go} onLogout={logout} />}

      {route === "home" && <S.Home go={go} draft={draft} setDraft={setDraft} links={links} />}
      {route === "auth" && <S.Auth go={go} draft={draft} onAuth={onAuth} initialMode={routeParams.mode} />}
      {route === "dashboard" && user && (
        <S.Dashboard user={user} links={links} actions={actions} go={go}
          openCreate={routeParams.create} justCreatedId={justCreatedId} />
      )}
      {route === "detail" && user && <S.LinkDetail link={selected} actions={actions} go={go} />}

      <S.Toast toast={toast} />

      <TweaksPanel>
        <TweakSection label="Brand accent" />
        <TweakColor label="Accent" value={(ACCENTS[t.accent] || {}).blue}
          options={Object.values(ACCENTS).map((a) => a.blue)}
          onChange={(v) => {
            const name = Object.keys(ACCENTS).find((k) => ACCENTS[k].blue === v) || "Apple Blue";
            setTweak("accent", name);
          }} />
        <TweakSection label="Shape" />
        <TweakRadio label="Corners" value={t.corners} options={["Rounded", "Sharp"]}
          onChange={(v) => setTweak("corners", v)} />
      </TweaksPanel>
    </>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(<App />);
