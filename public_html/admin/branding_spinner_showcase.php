<?php
// Spinner Showcase Page (Admin)
// Integrates with existing admin bootstrap include & auth.

require_once __DIR__ . '/assets/includes/main.php'; // Provides $pdo, auth, role, etc.

// Retrieve branding colors directly (single record assumption id=1)
$stmt = $pdo->prepare('SELECT brand_primary_color, brand_secondary_color, brand_tertiary_color, brand_quaternary_color, brand_accent_color FROM setting_branding_colors WHERE id = 1');
$stmt->execute();
$colors = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Fallback palette if any are null/empty
$primary      = $colors['brand_primary_color']      ?: '#6c2eb6';
$secondary    = $colors['brand_secondary_color']    ?: '#6610f2';
$tertiary     = $colors['brand_tertiary_color']     ?: '#8B4513';
$quaternary   = $colors['brand_quaternary_color']   ?: '#2E8B57';
$accent       = $colors['brand_accent_color']       ?: '#20c997';

// Accessible slow spin duration (2s) and reduced motion support
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Brand Spinner Showcase</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/private/gws-universal-branding.css">
<style>
/* Page-local layout only; all spinner classes now sourced from shared gws-universal-branding.css */
:root { --spinner-duration: 2s; }
@media (prefers-reduced-motion: reduce) { :root { --spinner-duration:0s; } }
body { background:#f8f9fa; }
.h-section { margin-top:2.5rem; }
.spinner-grid { display:grid; gap:2rem; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); }
.spinner-card { background:#fff; border:1px solid #dee2e6; border-radius:.75rem; padding:1.25rem; box-shadow:0 2px 4px rgba(0,0,0,.04); position:relative; }
.spinner-card h5 { font-size:1rem; margin:0 0 .75rem; display:flex; align-items:center; gap:.5rem; }
.spinner-demo { display:flex; align-items:center; justify-content:center; min-height:110px; }
.code-sample { font-family:monospace; background:#212529; color:#e9ecef; padding:.75rem; border-radius:.5rem; font-size:.75rem; overflow:auto; max-height:180px; }
.copy-btn { position:absolute; top:.75rem; right:.75rem; }
</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-shuffle me-2"></i>Brand Spinner Showcase</h1>
    <span class="badge bg-secondary ms-2">Accessible Slow Spin</span>
  </div>
  <p class="text-muted mb-4">Below are multiple spinner variants generated with current brand colors. Motion respects <code>prefers-reduced-motion</code>, and spin speed is slowed for accessibility. Copy any snippet to integrate. Customize duration by overriding <code>--spinner-duration</code>.</p>

  <div class="spinner-grid">
    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner-rainbow">Copy</button>
      <h5><i class="bi bi-rainbow"></i> Rainbow Ring Spinner</h5>
      <div class="spinner-demo"><div class="brand-spinner-rainbow" role="status" aria-label="Loading"></div></div>
      <div class="code-sample" data-src="rainbow">&lt;div class=&quot;brand-spinner-rainbow&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;&lt;/div&gt;</div>
    </div>
    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner">Copy</button>
      <h5><i class="bi bi-circle-half"></i> Default Border Spinner</h5>
      <div class="spinner-demo"><div class="brand-spinner" role="status" aria-label="Loading"></div></div>
      <div class="code-sample" data-src="border">&lt;div class=&quot;brand-spinner&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;&lt;/div&gt;</div>
    </div>

    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner-gradient">Copy</button>
      <h5><i class="bi bi-palette"></i> Conic Gradient Spinner</h5>
      <div class="spinner-demo"><div class="brand-spinner-gradient" role="status" aria-label="Loading"></div></div>
      <div class="code-sample" data-src="gradient">&lt;div class=&quot;brand-spinner-gradient&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;&lt;/div&gt;</div>
    </div>

    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner-logo">Copy</button>
      <h5><i class="bi bi-circle-square"></i> Logo Ring Spinner</h5>
      <div class="spinner-demo">
        <div class="brand-spinner-logo" role="status" aria-label="Loading">
          <img src="/assets/branding/logo.svg" alt="" aria-hidden="true">
        </div>
      </div>
      <div class="code-sample" data-src="logo">&lt;div class=&quot;brand-spinner-logo&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;\n  &lt;img src=&quot;/path/to/logo.svg&quot; alt=&quot;&quot; aria-hidden=&quot;true&quot;&gt;\n&lt;/div&gt;</div>
    </div>

    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner-pulse">Copy</button>
      <h5><i class="bi bi-record-circle"></i> Pulse Orb Spinner</h5>
      <div class="spinner-demo"><div class="brand-spinner-pulse" role="status" aria-label="Loading"></div></div>
      <div class="code-sample" data-src="pulse">&lt;div class=&quot;brand-spinner-pulse&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;&lt;/div&gt;</div>
    </div>

    <div class="spinner-card">
      <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy=".brand-spinner-dots">Copy</button>
      <h5><i class="bi bi-three-dots"></i> Bouncing Dots</h5>
      <div class="spinner-demo"><div class="brand-spinner-dots" role="status" aria-label="Loading"><span></span><span></span><span></span></div></div>
      <div class="code-sample" data-src="dots">&lt;div class=&quot;brand-spinner-dots&quot; role=&quot;status&quot; aria-label=&quot;Loading&quot;&gt;\n  &lt;span&gt;&lt;/span&gt;&lt;span&gt;&lt;/span&gt;&lt;span&gt;&lt;/span&gt;\n&lt;/div&gt;</div>
    </div>
  </div>

  <div class="h-section">
    <h2 class="h5 mt-5">Implementation Notes</h2>
    <ul class="small text-muted">
      <li>Each spinner uses CSS variables populated from the database (fallbacks applied if a color is null).</li>
      <li>Slow spin (2s) chosen for accessibility; adjust with <code>--spinner-duration</code>.</li>
      <li>Respects <code>prefers-reduced-motion</code>: animations stop, providing static representation.</li>
      <li>Logo spinner optional; supply a valid path via settings if you enable it globally.</li>
  <li>All spinner CSS now sourced from the shared <code>gws-universal-branding.css</code>.</li>
    </ul>
  </div>
</div>
<script>
// Copy helper
const map = {
  border: '<div class="brand-spinner" role="status" aria-label="Loading"></div>',
  gradient: '<div class="brand-spinner-gradient" role="status" aria-label="Loading"></div>',
  logo: '<div class="brand-spinner-logo" role="status" aria-label="Loading">\n  <img src="/path/to/logo.svg" alt="" aria-hidden="true">\n</div>',
  pulse: '<div class="brand-spinner-pulse" role="status" aria-label="Loading"></div>',
  dots: '<div class="brand-spinner-dots" role="status" aria-label="Loading"><span></span><span></span><span></span></div>'
  ,rainbow: '<div class="brand-spinner-rainbow" role="status" aria-label="Loading"></div>'
};

document.querySelectorAll('.copy-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const key = btn.nextElementSibling.nextElementSibling.getAttribute('data-src');
    navigator.clipboard.writeText(map[key]).then(() => {
      btn.textContent = 'Copied!';
      setTimeout(()=> btn.textContent='Copy', 1600);
    });
  });
});
</script>
</body>
</html>
