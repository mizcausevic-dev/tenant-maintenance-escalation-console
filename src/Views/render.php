<?php

declare(strict_types=1);

namespace TenantMaintenanceEscalationConsole\Views;

use TenantMaintenanceEscalationConsole\Services\TenantMaintenanceEscalationConsoleService;

function status_class(string $status): string
{
    return match ($status) {
        'critical', 'blocked', 'needs-refresh' => 'critical',
        'watch' => 'watch',
        default => 'good',
    };
}

function shell(string $active, string $title, string $eyebrow, string $hero, string $intro, string $body, array $rightCards): string
{
    $service = new TenantMaintenanceEscalationConsoleService();
    $summary = $service->summary();
    $operatorPosture = htmlspecialchars((string) $summary['operatorPosture'], ENT_QUOTES);
    $leadRecommendation = htmlspecialchars((string) $summary['leadRecommendation'], ENT_QUOTES);
    $rightCardsHtml = render_side_cards($rightCards);
    $nav = render_nav($active);

    $safeTitle = htmlspecialchars($title, ENT_QUOTES);
    $safeEyebrow = htmlspecialchars($eyebrow, ENT_QUOTES);
    $safeHero = htmlspecialchars($hero, ENT_QUOTES);
    $safeIntro = htmlspecialchars($intro, ENT_QUOTES);

    return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$safeTitle}</title>
  <style>
    :root{
      --bg:#070a0f; --panel:#0b1220; --panel2:#0a1426;
      --line:rgba(120,255,170,.18); --line2:rgba(120,255,170,.10);
      --text:#e9f3ff; --muted:rgba(233,243,255,.72); --muted2:rgba(233,243,255,.55);
      --bert:#37ff8b; --bert2:#19c7ff;
      --warn:#ffcc66; --bad:#ff5c7a; --good:#37ff8b; --plum:#b88cff;
      --shadow: 0 18px 60px rgba(0,0,0,.55);
      --mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      --sans: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{
      margin:0; font-family:var(--sans); color:var(--text);
      background:
        radial-gradient(1200px 600px at 20% -10%, rgba(55,255,139,.18), transparent 60%),
        radial-gradient(900px 520px at 90% 0%, rgba(25,199,255,.16), transparent 55%),
        radial-gradient(1000px 600px at 50% 110%, rgba(55,255,139,.10), transparent 60%),
        linear-gradient(180deg, #05070c 0%, #070a0f 35%, #05070c 100%);
    }
    .grid-bg{
      position:fixed; inset:0; pointer-events:none; opacity:.12; z-index:-1;
      background-image:
        linear-gradient(to right, rgba(55,255,139,.14) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(55,255,139,.10) 1px, transparent 1px);
      background-size: 46px 46px;
      mask-image: radial-gradient(900px 600px at 40% 10%, #000 60%, transparent 100%);
    }
    .wrap{max-width:1280px; margin:0 auto; padding:24px 22px 80px}
    .topbar{
      display:flex; justify-content:space-between; align-items:flex-start; gap:14px;
      border-bottom:1px solid var(--line2); padding-bottom:14px; margin-bottom:22px;
      font-family:var(--mono); font-size:11px; letter-spacing:.16em; color:var(--muted);
      text-transform:uppercase;
    }
    .topbar .left{color:var(--bert)}
    .topbar .right{text-align:right; color:var(--muted)}
    .topbar .right div{margin-bottom:4px}
    .herorow{display:grid; grid-template-columns: 1.5fr .9fr; gap:18px}
    @media (max-width:1000px){.herorow{grid-template-columns:1fr}}
    .hero{
      background: linear-gradient(180deg, rgba(11,18,32,.95), rgba(8,14,26,.92));
      border:1px solid var(--line); border-radius:22px; padding:28px 28px 24px;
      box-shadow: var(--shadow); position:relative; overflow:hidden;
      border-top:2px solid var(--bert2);
    }
    .hero h1{ font-size:60px; line-height:.97; margin:0 0 18px; letter-spacing:-.5px; font-weight:800; }
    @media (max-width:700px){.hero h1{font-size:40px}}
    .hero p{color:var(--muted); font-size:15px; line-height:1.55; max-width:700px; margin:0 0 18px}
    .chiprow{display:flex; flex-wrap:wrap; gap:8px}
    .meta-chip{
      font-family:var(--mono); font-size:11px; color:var(--muted);
      padding:7px 12px; border-radius:999px; border:1px solid var(--line);
      background:rgba(6,10,18,.4);
    }
    .side{display:flex; flex-direction:column; gap:14px}
    .bluf{
      border:1px solid var(--warn); border-left:4px solid var(--warn);
      background: linear-gradient(180deg, rgba(255,204,102,.06), rgba(11,18,32,.92));
      border-radius:14px; padding:16px 18px;
    }
    .bluf .lbl, .corr .lbl{font-family:var(--mono); font-size:10px; letter-spacing:.18em; text-transform:uppercase}
    .bluf .lbl{color:var(--warn)} .corr .lbl{color:var(--bert)}
    .bluf p, .corr p{color:var(--muted); font-size:13.5px; line-height:1.55; margin:6px 0 0}
    .corr{
      border:1px solid var(--bert); border-left:4px solid var(--bert);
      background: linear-gradient(180deg, rgba(55,255,139,.06), rgba(11,18,32,.92));
      border-radius:14px; padding:16px 18px;
    }
    .toolchip{
      font-family:var(--mono); font-size:11px; padding:6px 12px; border-radius:999px;
      border:1px solid currentColor; background:transparent; text-decoration:none;
    }
    .tc-claude{color:var(--bert2)} .tc-codex{color:var(--warn)} .tc-gpt{color:var(--bert)} .tc-perplex{color:var(--plum)}
    .section{margin-top:34px}
    .sh{
      display:flex; justify-content:space-between; align-items:baseline; gap:14px;
      padding-bottom:10px; border-bottom:1px solid var(--line2); margin-bottom:14px;
    }
    .sh h2{margin:0; font-size:24px; font-weight:600; letter-spacing:-.2px}
    .sh .note{font-family:var(--mono); font-size:11px; color:var(--muted2); letter-spacing:.16em; text-transform:uppercase}
    .kpis{display:grid; grid-template-columns: repeat(6, 1fr); gap:12px}
    @media (max-width:1100px){.kpis{grid-template-columns: repeat(3, 1fr)}} @media (max-width:640px){.kpis{grid-template-columns: repeat(2, 1fr)}}
    .kpi{
      border:1px solid var(--line); border-radius:14px; padding:14px 14px 12px;
      background: linear-gradient(180deg, rgba(11,18,32,.85), rgba(8,14,26,.65));
    }
    .kpi .v{font-family:var(--mono); font-size:26px; font-weight:600; letter-spacing:-.5px}
    .kpi.amber .v{color:var(--warn)} .kpi.cyan .v{color:var(--bert2)} .kpi.green .v{color:var(--bert)} .kpi.plum .v{color:var(--plum)} .kpi.red .v{color:var(--bad)} .kpi.white .v{color:var(--text)}
    .kpi .lbl{font-family:var(--mono); font-size:10px; letter-spacing:.18em; text-transform:uppercase; color:var(--muted); margin-top:6px}
    .kpi .h{font-size:12px; color:var(--muted); line-height:1.45; margin-top:8px}
    .board{display:grid; grid-template-columns: repeat(2,1fr); gap:14px}
    @media (max-width:1000px){.board{grid-template-columns:1fr}}
    .pcard{
      border:1px solid var(--line); border-radius:16px; padding:18px 20px;
      background: linear-gradient(180deg, rgba(11,18,32,.85), rgba(8,14,26,.65));
    }
    .pcard .ptop{display:flex; justify-content:space-between; align-items:center; margin-bottom:8px}
    .pcard .pnum{font-family:var(--mono); font-size:22px; font-weight:600; color:var(--bert)}
    .pcard .ppri{font-family:var(--mono); font-size:10px; padding:5px 10px; border-radius:999px; border:1px solid var(--line); color:var(--bert); letter-spacing:.14em; background:rgba(55,255,139,.06)}
    .pcard h3{margin:6px 0 8px; font-size:19px; font-weight:600}
    .pcard .pdesc{font-size:13.5px; color:var(--muted); line-height:1.55; margin:0 0 12px}
    .pcard ul.check{list-style:none; padding:0; margin:0}
    .pcard ul.check li{display:grid; grid-template-columns:18px 1fr; gap:10px; padding:6px 0; font-size:13.5px; color:var(--muted); line-height:1.45}
    .pcard ul.check li:before{content:""; width:14px; height:14px; border:1px solid var(--line); border-radius:3px; background:rgba(6,10,18,.4); margin-top:3px}
    .ttbl{
      width:100%; border-collapse:separate; border-spacing:0;
      border:1px solid var(--line); border-radius:14px; overflow:hidden;
    }
    .ttbl th, .ttbl td{padding:13px 14px; text-align:left; font-size:13.5px; vertical-align:top}
    .ttbl thead th{
      font-family:var(--mono); font-size:11px; letter-spacing:.16em; text-transform:uppercase;
      color:var(--muted2); border-bottom:1px solid var(--line); background:rgba(11,18,32,.5);
    }
    .ttbl tbody tr:hover{background:rgba(55,255,139,.03)}
    .ttbl td, .ttbl td *{color:var(--muted)}
    .ttbl b{color:var(--text)}
    .st{font-family:var(--mono); font-size:10px; padding:4px 9px; border-radius:6px; letter-spacing:.1em; text-transform:uppercase; border:1px solid currentColor; display:inline-block}
    .st.good{color:var(--bert)} .st.watch{color:var(--warn)} .st.critical{color:var(--bad)}
    footer{
      margin-top:30px; padding-top:14px; border-top:1px dashed var(--line2);
      display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;
      font-family:var(--mono); font-size:11px; color:var(--muted2); letter-spacing:.08em;
    }
    a{color:var(--bert2); text-decoration:none}
    a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="grid-bg"></div>
  <div class="wrap">
    <div class="topbar">
      <div class="left">KINETIC GAIN · tenant maintenance lane</div>
      <div class="right">
        <div>Property operations + WordPress portal governance</div>
        <div>Maintenance packets · vendor escalation · SLA posture</div>
      </div>
    </div>
    <div class="herorow">
      <section class="hero">
        <div class="chiprow">
          <span class="meta-chip">{$safeEyebrow}</span>
          <span class="meta-chip">CNAME · tenant.kineticgain.com</span>
          <span class="meta-chip">PHP + static Pages bundle</span>
        </div>
        <h1>{$safeHero}</h1>
        <p>{$safeIntro}</p>
        <div class="bluf" style="margin:18px 0 18px;">
          <div class="lbl">Lead recommendation</div>
          <p><strong>Service-safe escalation posture</strong><br>{$leadRecommendation}</p>
        </div>
        <div class="chiprow">
          {$nav}
        </div>
      </section>
      <aside class="side">{$rightCardsHtml}</aside>
    </div>
    <section class="section">
      <div class="sh"><h2>Operator summary</h2><div class="note">tenant promise + vendor accountability</div></div>
      <div class="kpis">
        <div class="kpi green"><div class="v">{$summary['ticketCount']}</div><div class="lbl">Tracked tickets</div><div class="h">Emergency, recurring, compliance, and access-device lanes in one surface.</div></div>
        <div class="kpi cyan"><div class="v">{$summary['healthyCount']}</div><div class="lbl">Healthy lanes</div><div class="h">Aligned to current resident comms, vendor packet, and closure steps.</div></div>
        <div class="kpi amber"><div class="v">{$summary['watchCount']}</div><div class="lbl">Watch lanes</div><div class="h">Need ETA, vendor, or resident-portal repair before the next update cycle.</div></div>
        <div class="kpi red"><div class="v">{$summary['criticalCount']}</div><div class="lbl">Critical tickets</div><div class="h">Unsafe to treat as routine until the full evidence and escalation packet converges.</div></div>
        <div class="kpi plum"><div class="v">{$summary['vendorCount']}</div><div class="lbl">Vendor escalations</div><div class="h">Canonical escalation queue across mitigation, HVAC, plumbing, and certification lanes.</div></div>
        <div class="kpi white"><div class="v mono">{$operatorPosture}</div><div class="lbl">Operator posture</div><div class="h">Maintenance treated like an operating system, not just a ticket inbox.</div></div>
      </div>
    </section>
    {$body}
    <footer>
      <div>tenant-maintenance-escalation-console · AGPL-3.0-or-later · synthetic demonstration data only</div>
      <div>Routes: / · /maintenance-lane · /escalation-queue · /service-posture · /verification · /docs</div>
    </footer>
  </div>
</body>
</html>
HTML;
}

function active_class(string $active, string $href): string
{
    return $active === $href ? 'tc-gpt' : 'tc-claude';
}

function render_nav(string $active): string
{
    $items = [
        '/' => 'Overview',
        '/maintenance-lane' => 'Maintenance Lane',
        '/escalation-queue' => 'Escalation Queue',
        '/service-posture' => 'Service Posture',
        '/verification' => 'Verification',
        '/docs' => 'Docs',
    ];

    $html = '';
    foreach ($items as $href => $label) {
        $class = active_class($active, $href);
        $safeHref = htmlspecialchars($href, ENT_QUOTES);
        $safeLabel = htmlspecialchars($label, ENT_QUOTES);
        $html .= "<a class=\"toolchip {$class}\" href=\"{$safeHref}\">{$safeLabel}</a>";
    }

    return $html;
}

function render_side_cards(array $cards): string
{
    $html = '';
    foreach ($cards as $index => $card) {
        $class = $index === 0 ? 'bluf' : 'corr';
        $label = htmlspecialchars($card['label'], ENT_QUOTES);
        $title = htmlspecialchars($card['title'], ENT_QUOTES);
        $body = htmlspecialchars($card['body'], ENT_QUOTES);
        $html .= <<<HTML
<article class="{$class}">
  <div class="lbl">{$label}</div>
  <p><strong>{$title}</strong><br>{$body}</p>
</article>
HTML;
    }

    return $html;
}

function render_overview(): string
{
    $service = new TenantMaintenanceEscalationConsoleService();
    $lanes = array_slice($service->maintenanceLanes(), 0, 4);

    $cards = '';
    foreach ($lanes as $index => $lane) {
        $indexPlus = $index + 1;
        $ticket = htmlspecialchars((string) $lane['ticket'], ENT_QUOTES);
        $property = htmlspecialchars((string) $lane['property'], ENT_QUOTES);
        $owner = htmlspecialchars((string) $lane['owner'], ENT_QUOTES);
        $proof = htmlspecialchars((string) $lane['proof'], ENT_QUOTES);
        $nextAction = htmlspecialchars((string) $lane['nextAction'], ENT_QUOTES);
        $status = htmlspecialchars((string) $lane['status'], ENT_QUOTES);
        $statusClass = status_class((string) $lane['status']);
        $cards .= <<<HTML
<article class="pcard">
  <div class="ptop"><div class="pnum">T-0{$indexPlus}</div><div class="ppri">{$status}</div></div>
  <h3>{$ticket}</h3>
  <p class="pdesc">{$property} · owner: {$owner}</p>
  <ul class="check">
    <li>{$proof}</li>
    <li><strong>Next action:</strong> {$nextAction}</li>
    <li><strong>Status:</strong> <span class="st {$statusClass}">{$status}</span></li>
  </ul>
</article>
HTML;
    }

    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Overview</h2><div class="note">where tenant trust drifts first</div></div>
  <div class="board">{$cards}</div>
</section>
HTML;

    return shell(
        '/',
        'Tenant Maintenance Escalation Console',
        'tenant maintenance escalation console',
        'Keep tenant maintenance tickets, vendor escalations, and resident updates in the same service lane.',
        'This operator surface makes property maintenance governance explicit: which tickets are safe to close, which vendor packets are stale, and where operations, field maintenance, finance, or compliance still need to repair the escalation path before residents feel the mismatch.',
        $body,
        [
            ['label' => 'Core offer', 'title' => 'Maintenance escalation control plane', 'body' => 'Property tickets, vendor accountability, resident comms, and escalation evidence tied together in one surface.'],
            ['label' => 'Buyer fit', 'title' => 'Property and facilities teams', 'body' => 'For multifamily, condo, campus, and managed-property teams running maintenance operations through fragmented portals.'],
            ['label' => 'Execution style', 'title' => 'Service-safe review routing', 'body' => 'Treat vendor dispatch, resident messaging, and escalation evidence as reviewable release artifacts.'],
        ]
    );
}

function render_maintenance_lane(): string
{
    $service = new TenantMaintenanceEscalationConsoleService();
    $rows = '';
    foreach ($service->maintenanceLanes() as $lane) {
        $ticket = htmlspecialchars((string) $lane['ticket'], ENT_QUOTES);
        $property = htmlspecialchars((string) $lane['property'], ENT_QUOTES);
        $owner = htmlspecialchars((string) $lane['owner'], ENT_QUOTES);
        $package = htmlspecialchars((string) $lane['package'], ENT_QUOTES);
        $risk = htmlspecialchars((string) $lane['risk'], ENT_QUOTES);
        $nextAction = htmlspecialchars((string) $lane['nextAction'], ENT_QUOTES);
        $status = htmlspecialchars((string) $lane['status'], ENT_QUOTES);
        $statusClass = status_class((string) $lane['status']);
        $rows .= <<<HTML
<tr>
  <td><b>{$ticket}</b><br><span style="color:var(--muted2)">{$property}</span></td>
  <td>{$owner}</td>
  <td>{$package}</td>
  <td>{$risk}</td>
  <td>{$nextAction}</td>
  <td><span class="st {$statusClass}">{$status}</span></td>
</tr>
HTML;
    }

    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Maintenance lane</h2><div class="note">ticket-level packet visibility</div></div>
  <div class="tablewrap">
    <table class="ttbl">
      <thead>
        <tr>
          <th>Ticket</th>
          <th>Owner</th>
          <th>Packet</th>
          <th>Risk</th>
          <th>Next action</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>{$rows}</tbody>
    </table>
  </div>
</section>
HTML;

    return shell(
        '/maintenance-lane',
        'Tenant Maintenance Escalation Console · Maintenance lane',
        'maintenance lane',
        'Each property ticket keeps owner, packet, risk, and next action visible.',
        'Property managers and maintenance leads need one lane that keeps resident impact, vendor state, and escalation evidence readable at a glance.',
        $body,
        [
            ['label' => 'Pressure', 'title' => 'SLA + habitability timing', 'body' => 'Pair each ticket with the actual resident or service risk if the escalation packet ships stale.'],
            ['label' => 'Coordination', 'title' => 'Owner-safe packeting', 'body' => 'Tie field ops, resident comms, and vendor dispatch to the same accountable owner.'],
            ['label' => 'Result', 'title' => 'Review-safe maintenance operations', 'body' => 'Keep routine tickets from hiding the red ones that actually threaten service posture.'],
        ]
    );
}

function render_escalation_queue(): string
{
    $service = new TenantMaintenanceEscalationConsoleService();
    $rows = '';
    foreach ($service->escalationQueue() as $item) {
        $vendor = htmlspecialchars((string) $item['vendor'], ENT_QUOTES);
        $category = htmlspecialchars((string) $item['category'], ENT_QUOTES);
        $sla = htmlspecialchars((string) $item['sla'], ENT_QUOTES);
        $blockingIssue = htmlspecialchars((string) $item['blockingIssue'], ENT_QUOTES);
        $owner = htmlspecialchars((string) $item['owner'], ENT_QUOTES);
        $status = htmlspecialchars((string) $item['status'], ENT_QUOTES);
        $statusClass = status_class((string) $item['status']);
        $rows .= <<<HTML
<tr>
  <td><b>{$vendor}</b><br><span style="color:var(--muted2)">{$category}</span></td>
  <td>{$sla}</td>
  <td>{$blockingIssue}</td>
  <td>{$owner}</td>
  <td><span class="st {$statusClass}">{$status}</span></td>
</tr>
HTML;
    }

    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Escalation queue</h2><div class="note">vendor accountability and unblockers</div></div>
  <div class="tablewrap">
    <table class="ttbl">
      <thead>
        <tr>
          <th>Vendor</th>
          <th>SLA</th>
          <th>Blocking issue</th>
          <th>Owner</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>{$rows}</tbody>
    </table>
  </div>
</section>
HTML;

    return shell(
        '/escalation-queue',
        'Tenant Maintenance Escalation Console · Escalation queue',
        'escalation queue',
        'Vendor escalations stay readable before tenant trust, SLA, or insurance posture break.',
        'This route keeps the red queue visible: which dispatches are blocked, where the packet is incomplete, and which owner still needs to resolve the escalation before the next resident update.',
        $body,
        [
            ['label' => 'Vendor posture', 'title' => 'SLA-safe dispatch routing', 'body' => 'Make blocked dispatches visible before the service window quietly slips.'],
            ['label' => 'Packet quality', 'title' => 'Escalation evidence in one place', 'body' => 'Insurance notes, habitability details, and work-order proof should not live in different tools.'],
            ['label' => 'Resident lens', 'title' => 'Message and dispatch alignment', 'body' => 'Keep what the resident hears aligned with what the vendor can actually do next.'],
        ]
    );
}

function render_service_posture(): string
{
    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Service posture</h2><div class="note">how the lane stays tenant-safe</div></div>
  <div class="board">
    <article class="pcard">
      <div class="ptop"><div class="pnum">P-01</div><div class="ppri">tenant promise</div></div>
      <h3>Resident timeline must match the real dispatch state.</h3>
      <p class="pdesc">If the resident portal, vendor ETA, and owner packet drift apart, trust breaks before the ticket closes.</p>
      <ul class="check">
        <li>Keep one canonical update window for resident-facing comms.</li>
        <li>Block status sends when vendor ETA is stale.</li>
        <li>Escalate habitability-related drift immediately.</li>
      </ul>
    </article>
    <article class="pcard">
      <div class="ptop"><div class="pnum">P-02</div><div class="ppri">repeat failure control</div></div>
      <h3>Recurring incidents need one shared escalation history.</h3>
      <p class="pdesc">Repeat plumbing, HVAC, or access failures should not look like isolated tickets when they already form a pattern.</p>
      <ul class="check">
        <li>Merge repeat incidents into one scope-expansion packet.</li>
        <li>Keep relocation or reimbursement notes alongside the work order.</li>
        <li>Make owner accountability explicit before vendor re-dispatch.</li>
      </ul>
    </article>
  </div>
</section>
HTML;

    return shell(
        '/service-posture',
        'Tenant Maintenance Escalation Console · Service posture',
        'service posture',
        'Maintenance governance stays tenant-safe when packet evidence, vendor action, and resident updates converge.',
        'The posture route explains the operating system behind the queue: why recurring failures, habitability notes, and owner accountability all have to stay inside the same review packet.',
        $body,
        [
            ['label' => 'Why this matters', 'title' => 'Tenant-safe service operations', 'body' => 'This is how property teams avoid letting maintenance tickets become resident-trust incidents.'],
            ['label' => 'Monetizable layer', 'title' => 'Embedded review and portal governance', 'body' => 'The same primitive can ladder into property portal templates, maintenance governance packs, and embedded ops reviews.'],
            ['label' => 'Vertical fit', 'title' => 'PropTech and real-estate operations', 'body' => 'The surface is legible to multifamily, condo, and managed-property buyers immediately.'],
        ]
    );
}

function render_verification(): string
{
    $service = new TenantMaintenanceEscalationConsoleService();
    $rows = '';
    foreach ($service->verificationGates() as $gate) {
        $gateTitle = htmlspecialchars((string) $gate['gate'], ENT_QUOTES);
        $detail = htmlspecialchars((string) $gate['detail'], ENT_QUOTES);
        $status = htmlspecialchars((string) $gate['status'], ENT_QUOTES);
        $statusClass = status_class((string) $gate['status']);
        $rows .= <<<HTML
<tr>
  <td><b>{$gateTitle}</b></td>
  <td>{$detail}</td>
  <td><span class="st {$statusClass}">{$status}</span></td>
</tr>
HTML;
    }

    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Verification</h2><div class="note">release gate</div></div>
  <div class="tablewrap">
    <table class="ttbl">
      <thead>
        <tr>
          <th>Gate</th>
          <th>Detail</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>{$rows}</tbody>
    </table>
  </div>
</section>
HTML;

    return shell(
        '/verification',
        'Tenant Maintenance Escalation Console · Verification',
        'verification',
        'The verification route keeps property ops, resident support, field teams, and finance aligned around one decision: is the current maintenance packet safe to progress?',
        'Verification turns maintenance escalation into an explicit gate instead of an informal inbox ritual.',
        $body,
        [
            ['label' => 'Gate 01', 'title' => 'Resident comms match dispatch reality', 'body' => 'Portal copy should not outrun what the vendor or owner can actually deliver.'],
            ['label' => 'Gate 02', 'title' => 'Habitability evidence is packetized', 'body' => 'Emergency details, relocation notes, and approvals should travel together.'],
            ['label' => 'Gate 03', 'title' => 'Repeat incidents are review-safe', 'body' => 'Recurring failures need one accountable history, not disconnected tickets.'],
        ]
    );
}

function render_docs(): string
{
    $body = <<<HTML
<section class="section">
  <div class="sh"><h2>Docs</h2><div class="note">implementation notes</div></div>
  <div class="board">
    <article class="pcard">
      <div class="ptop"><div class="pnum">D-01</div><div class="ppri">primitive</div></div>
      <h3>WordPress + property portal evidence surface</h3>
      <p class="pdesc">The plugin publishes a maintenance snapshot shortcode and REST endpoint so the same packet can surface in both WordPress and the static portfolio proof layer.</p>
    </article>
    <article class="pcard">
      <div class="ptop"><div class="pnum">D-02</div><div class="ppri">delivery</div></div>
      <h3>Static Pages bundle with machine-readable payloads</h3>
      <p class="pdesc">Prerendered routes, JSON payloads, robots, sitemap, screenshots, and the custom domain all ship together for buyer-readable proof.</p>
    </article>
  </div>
</section>
HTML;

    return shell(
        '/docs',
        'Tenant Maintenance Escalation Console · Docs',
        'docs',
        'Operator documentation for the tenant maintenance escalation primitive.',
        'This repo exists to prove that Kinetic Gain can ship a WordPress-adjacent property-ops surface that stays buyer-readable, route-clean, and machine-readable at the same time.',
        $body,
        [
            ['label' => 'Language atlas', 'title' => 'PHP surface with real delivery rails', 'body' => 'This is a true PHP / WordPress operator repo, not a TypeScript stand-in.'],
            ['label' => 'Industry atlas', 'title' => 'PropTech / Real Estate depth', 'body' => 'The maintenance and escalation primitive expands one of the thinner vertical chips materially.'],
            ['label' => 'Commercial path', 'title' => 'Template pack + embedded rollout', 'body' => 'This can ladder into portal kits, maintenance governance templates, and embedded engagement work.'],
        ]
    );
}
