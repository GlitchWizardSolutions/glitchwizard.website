<?php
include_once '../assets/includes/main.php';
// Restrict to Developer role only
if(($_SESSION['admin_role'] ?? '') !== 'Developer') {
  echo template_admin_header('Developer SOP (Restricted)','help','developer');
  echo '<div class="alert alert-danger m-4">Access denied: Developer role required.</div>';
  echo template_admin_footer();
  exit;
}
echo template_admin_header('Developer SOP & Reference','help','developer');
?>
<div class="content-header"><h2>Developer Standard Operating Procedures</h2></div>
<div class="card mb-4">
  <div class="card-body">
    <p class="lead mb-3">Authoritative technical reference for this application. Restricted to Developer role (enforce via role checks later if needed).</p>
    <h5 class="mt-4">1. Custom Font Installation & Multi-Font Brand Title</h5>
    <p>Support scenarios where brand name uses alternating fonts (e.g. <code>FirstWord</code> in Font A, <code>Middle</code> in Font B, <code>LastWord</code> back to Font A).</p>
    <ol>
      <li><strong>File Placement:</strong> Place font files in <code>/public_html/assets/fonts/</code>. Keep a naming convention: <code>FontFamilyName-Weight.ext</code>.</li>
      <li><strong>Formats:</strong> Provide at least <code>.woff2</code> (preferred) and optionally <code>.woff</code>. Avoid TTF/OTF in production if possible.</li>
      <li><strong>@font-face Declaration:</strong> Add to <code>/private/gws-universal-branding.css</code> (or future <code>branding/custom-fonts.css</code>):
        <pre><code>@font-face {
  font-family: 'BrandFontA';
  src: url('/assets/fonts/BrandFontA-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}
@font-face {
  font-family: 'BrandFontB';
  src: url('/assets/fonts/BrandFontB-Regular.woff2') format('woff2');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}</code></pre>
      </li>
      <li><strong>Header Markup Strategy:</strong> In the site header template, wrap word segments:
        <pre><code>&lt;h1 class="site-brand" aria-label="Full Brand Name"&gt;
  &lt;span class="bf-a"&gt;FirstWord&lt;/span&gt;
  &lt;span class="bf-b"&gt;Middle&lt;/span&gt;
  &lt;span class="bf-a"&gt;LastWord&lt;/span&gt;
&lt;/h1&gt;</code></pre>
      </li>
      <li><strong>Styling:</strong>
        <pre><code>.site-brand {font-size: clamp(1.8rem, 3vw, 2.6rem); font-weight: 600; line-height:1.1; display:flex; flex-wrap:wrap; gap:.35rem;}
.site-brand .bf-a {font-family: 'BrandFontA', var(--fallback-heading-font, 'Inter'), sans-serif;}
.site-brand .bf-b {font-family: 'BrandFontB', var(--fallback-heading-font, 'Inter'), sans-serif; font-weight:400;}
/* Optional letter styling */
.site-brand .bf-b {color: var(--brand-accent, #6c3fed);}      
</code></pre>
      </li>
      <li><strong>FOUT/FOIT Mitigation:</strong> Use <code>font-display: swap</code> and ensure a visually similar fallback (e.g. Inter).</li>
      <li><strong>Performance:</strong> Only load weights/styles actually used. Combine @font-face declarations into a single delivered CSS file included once.</li>
      <li><strong>Accessibility:</strong> Provide an <code>aria-label</code> if the visual splitting could confuse screen readers (or hide decorative spans with <code>aria-hidden="true"</code> and supply full text in <code>&lt;span class="sr-only"&gt;</code>).</li>
    </ol>
    <h6 class="mt-3">Quick Copy Snippet (Minimal):</h6>
    <pre><code>&lt;h1 class="site-brand"&gt;
  &lt;span class="bf-a"&gt;First&lt;/span&gt; &lt;span class="bf-b"&gt;Middle&lt;/span&gt; &lt;span class="bf-a"&gt;Last&lt;/span&gt;
&lt;/h1&gt;</code></pre>
    <hr>
    <h5 class="mt-4">2. Icon System Roadmap</h5>
    <p>Primary icon set: Bootstrap Icons CDN. Additional sets can be self-hosted. To add a custom SVG icon:</p>
    <ol>
      <li>Place SVG in <code>/public_html/assets/icons/</code>.</li>
      <li>Inline include where semantics matter: <code>&lt;svg role="img" aria-label="Meaning"&gt;...&lt;/svg&gt;</code>.</li>
      <li>Create a utility PHP include for commonly reused inline symbols (future).</li>
    </ol>
    <hr>
    <h5 class="mt-4">3. Content Data Model (Snapshot)</h5>
    <pre><code>content_items(area, slug, title, body, icon, position, active)
- services: per service row
- section/services: section intro (title/body)
- page/{slug}: JSON body with structured parts (e.g. {"heading":"...","intro":"...","body":"..."})
pages_metadata(slug, meta_title, meta_description)</code></pre>
    <p>Editing pipeline uses AJAX endpoints for granular updates. Consider a versioning table later.</p>
    <hr>
    <h5 class="mt-4">4. Deployment Steps (Abbrev)</h5>
    <ol class="small">
      <li>Pull latest main branch.</li>
      <li>Run pending SQL migrations in <code>/private/sql/</code>.</li>
      <li>Verify permissions on <code>/public_html/assets/img/</code> & fonts directory.</li>
      <li>Clear opcode & application caches (if enabled).</li>
      <li>Smoke test: login, hero load, services accordion, About page edit.</li>
    </ol>
    <hr>
    <h5 class="mt-4">5. Security Notes</h5>
    <ul class="small">
      <li>Sanitize HTML output (escape unless explicitly trusted WYSIWYG content).</li>
      <li>All AJAX endpoints should check role & CSRF (CSRF token pending implementation).</li>
      <li>Disallow font uploads via user forms (developer-only SFTP or deploy pipeline).</li>
    </ul>
    <hr>
    <h5 class="mt-4">6. Roadmap Placeholders</h5>
    <ul class="small mb-0">
      <li>Font management UI (developer-only)</li>
      <li>Icons picker (services & pages)</li>
      <li>Content version history & rollback</li>
      <li>Searchable help index (shared across guides)</li>
    </ul>
  </div>
</div>
<div class="text-end text-muted small">Version 0.1 Developer SOP Draft</div>
<?= template_admin_footer(); ?>
