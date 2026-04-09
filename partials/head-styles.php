<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --navy:   #0a1628; --navy2: #0f2040; --navy3: #162b55;
  --blue:   #2563eb; --blue2: #3b82f6; --blue3: #60a5fa;
  --cyan:   #06b6d4; --white: #f8fafc; --gray:  #94a3b8;
  --border: rgba(59,130,246,.18);
  --sidebar-w: 220px;
}
body {
  background: var(--navy); color: var(--white);
  font-family: 'Segoe UI', system-ui, sans-serif; font-size: .9rem;
  min-height: 100vh;
}

/* ── TOP NAV ── */
.topnav {
  position: sticky; top: 0; z-index: 50;
  background: rgba(10,22,40,.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--border);
  padding: .7rem 1.5rem;
  display: flex; align-items: center; justify-content: space-between;
}
.topnav-brand { display: flex; align-items: center; gap: .6rem; }
.topnav-brand img { height: 32px; object-fit: contain; }
.topnav-brand .brand-name { font-weight: 700; font-size: .95rem; }
.topnav-brand .brand-sub  { font-size: .72rem; color: var(--gray); }
.topnav-right { display: flex; align-items: center; gap: 1rem; }
.topnav-user  { font-size: .82rem; color: var(--gray); }
.topnav-user strong { color: var(--white); }
.topnav-logout { font-size: .8rem; color: var(--gray); text-decoration: none; padding: .3rem .75rem; border: 1px solid var(--border); border-radius: 6px; transition: border-color .2s; }
.topnav-logout:hover { border-color: var(--blue2); color: var(--blue3); }

/* ── LAYOUT ── */
.layout { display: flex; min-height: calc(100vh - 53px); }

/* ── SIDEBAR ── */
.sidebar {
  width: var(--sidebar-w); flex-shrink: 0;
  background: rgba(15,32,64,.6);
  border-right: 1px solid var(--border);
  padding: 1.25rem 0;
  position: sticky; top: 53px; height: calc(100vh - 53px); overflow-y: auto;
}
.sidebar-section { margin-bottom: 1.5rem; }
.sidebar-label {
  font-size: .65rem; font-weight: 700; letter-spacing: .14em;
  color: var(--gray); text-transform: uppercase;
  padding: 0 1rem .5rem;
}
.sidebar a {
  display: flex; align-items: center; gap: .6rem;
  padding: .55rem 1rem; color: var(--gray);
  text-decoration: none; font-size: .875rem;
  border-left: 2px solid transparent;
  transition: all .15s;
}
.sidebar a:hover { color: var(--white); background: rgba(255,255,255,.04); }
.sidebar a.active { color: var(--blue3); border-left-color: var(--blue2); background: rgba(59,130,246,.08); }
.sidebar a .s-icon { font-size: 1rem; width: 20px; text-align: center; }

/* ── MAIN ── */
.main { flex: 1; padding: 2rem 1.75rem; overflow-x: hidden; }
.page-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;
}
.page-header h1 { font-size: 1.5rem; font-weight: 800; }
.page-sub { color: var(--gray); font-size: .875rem; margin-top: .2rem; }

/* ── STATS ── */
.stats-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 1rem; margin-bottom: 1.75rem;
}
.stat-card {
  background: rgba(255,255,255,.03);
  border: 1px solid var(--border); border-radius: 14px;
  padding: 1.1rem 1.25rem; display: flex; align-items: center; gap: .9rem;
}
.stat-icon {
  width: 44px; height: 44px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;
}
.stat-num  { font-size: 1.65rem; font-weight: 800; line-height: 1; }
.stat-label{ font-size: .75rem; color: var(--gray); margin-top: .2rem; }

/* ── SECTION GRID ── */
.section-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 1.25rem; margin-bottom: 1.5rem;
}

/* ── PANEL ── */
.panel {
  background: rgba(255,255,255,.025);
  border: 1px solid var(--border); border-radius: 16px; padding: 1.4rem;
}
.panel-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.1rem;
}
.panel-header h2 { font-size: 1rem; font-weight: 700; }
.panel-link { font-size: .8rem; color: var(--blue3); text-decoration: none; }
.panel-link:hover { text-decoration: underline; }

/* ── DATA TABLE ── */
.data-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
.data-table th {
  text-align: left; padding: .5rem .75rem;
  color: var(--gray); font-weight: 600; font-size: .75rem;
  border-bottom: 1px solid var(--border);
}
.data-table td { padding: .55rem .75rem; border-bottom: 1px solid rgba(255,255,255,.04); }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,.02); }
.tbl-link { color: var(--blue3); text-decoration: none; font-size: .78rem; }
.tbl-link:hover { text-decoration: underline; }
code { background: rgba(255,255,255,.06); padding: .1rem .35rem; border-radius: 4px; font-size: .78rem; }

/* ── BADGE ── */
.badge {
  display: inline-block; padding: .15rem .6rem;
  border-radius: 6px; font-size: .75rem; font-weight: 600;
}

/* ── QUICK LINKS ── */
.quick-links { display: flex; flex-direction: column; gap: .75rem; }
.ql-item {
  display: flex; align-items: center; gap: .9rem;
  padding: .75rem; border-radius: 10px;
  background: rgba(255,255,255,.03); border: 1px solid var(--border);
  text-decoration: none; color: var(--white); transition: border-color .2s;
}
.ql-item:hover { border-color: rgba(59,130,246,.4); }
.ql-icon { font-size: 1.4rem; }
.ql-item div { display: flex; flex-direction: column; }
.ql-item strong { font-size: .875rem; }
.ql-item small  { font-size: .78rem; color: var(--gray); }

/* ── TEAM LIST ── */
.team-list { display: flex; flex-direction: column; gap: .65rem; }
.team-item { display: flex; align-items: center; gap: .75rem; padding: .5rem; }
.team-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--navy3); border: 1.5px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: .9rem; flex-shrink: 0;
}
.team-info { flex: 1; display: flex; flex-direction: column; }
.team-info strong { font-size: .875rem; }
.team-info small  { font-size: .77rem; color: var(--gray); }
.team-days { flex-shrink: 0; }

/* ── LEGAJO MINI ── */
.legajo-mini { margin-bottom: 1rem; }
.lm-row { display: flex; justify-content: space-between; padding: .45rem 0; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .85rem; }
.lm-row span { color: var(--gray); }

/* ── DIAS BARS ── */
.dias-bars { margin-top: .75rem; display: flex; flex-direction: column; gap: .75rem; }
.dias-bar-item {}
.db-label { display: flex; justify-content: space-between; font-size: .8rem; margin-bottom: .3rem; }
.db-label span { color: var(--gray); }
.db-track { height: 7px; background: rgba(255,255,255,.07); border-radius: 4px; }
.db-fill  { height: 100%; border-radius: 4px; transition: width .4s; }

/* ── FINANZAS ── */
.fin-summary { display: flex; gap: 1.5rem; flex-wrap: wrap; }
.fin-item { padding: 1rem; background: rgba(255,255,255,.03); border: 1px solid var(--border); border-radius: 12px; min-width: 140px; }
.fin-num   { font-size: 1.5rem; font-weight: 800; color: var(--blue2); }
.fin-label { font-size: .78rem; color: var(--gray); margin-top: .25rem; }

/* ── FORMS ── */
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.form-group { display: flex; flex-direction: column; gap: .3rem; }
.form-group label { font-size: .8rem; font-weight: 600; color: var(--gray); }
.form-group select, .form-group input {
  background: rgba(255,255,255,.05); border: 1.5px solid var(--border);
  border-radius: 8px; color: var(--white); padding: .55rem .8rem;
  font-size: .875rem; outline: none; transition: border-color .2s;
}
.form-group select:focus, .form-group input:focus { border-color: var(--blue2); }
.form-group select option { background: #0f2040; }

/* ── HISTORIAL TIMELINE ── */
.hist-timeline { display: flex; flex-direction: column; gap: 0; }
.hist-item {
  display: grid; grid-template-columns: 120px 1fr;
  gap: 0 1.25rem; padding: .9rem 0;
  border-bottom: 1px solid rgba(255,255,255,.04);
}
.hist-item:last-child { border-bottom: none; }
.hist-time { font-size: .75rem; color: var(--gray); padding-top: .1rem; }
.hist-body {}
.hist-actor { font-size: .8rem; font-weight: 700; color: var(--blue3); margin-bottom: .2rem; }
.hist-action { font-size: .85rem; }
.hist-badge { margin-top: .3rem; }

/* ── BUTTONS ── */
.btn-primary {
  background: var(--blue); color: #fff;
  padding: .55rem 1.25rem; border-radius: 8px;
  border: none; font-size: .875rem; font-weight: 600;
  cursor: pointer; text-decoration: none; display: inline-block;
  transition: background .2s;
}
.btn-primary:hover { background: #1d4ed8; }
.btn-success {
  background: rgba(34,197,94,.15); color: #4ade80;
  border: 1px solid rgba(34,197,94,.3);
  padding: .5rem 1.1rem; border-radius: 8px;
  font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .2s;
}
.btn-success:hover { background: rgba(34,197,94,.25); }
.btn-danger {
  background: rgba(239,68,68,.12); color: #f87171;
  border: 1px solid rgba(239,68,68,.25);
  padding: .5rem 1.1rem; border-radius: 8px;
  font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .2s;
}
.btn-danger:hover { background: rgba(239,68,68,.22); }
.btn-secondary {
  background: rgba(255,255,255,.06); color: var(--gray);
  border: 1px solid var(--border);
  padding: .5rem 1.1rem; border-radius: 8px;
  font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;
  transition: border-color .2s;
}
.btn-secondary:hover { border-color: var(--blue2); color: var(--white); }

/* ── ALERT ── */
.alert { padding: .75rem 1rem; border-radius: 8px; font-size: .875rem; margin-bottom: 1rem; }
.alert-ok  { background: rgba(34,197,94,.1);  border: 1px solid rgba(34,197,94,.25);  color: #86efac; }
.alert-err { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.25);  color: #fca5a5; }

/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray); }
.empty-state .es-icon { font-size: 2.5rem; margin-bottom: .75rem; }

@media (max-width: 768px) {
  .sidebar { display: none; }
  .section-grid { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
