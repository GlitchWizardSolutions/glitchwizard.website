## Brand Spinner System Integration Guide

Centralized brand-themed spinners replacing Bootstrap's default `spinner-border` / `spinner-grow` classes.

### Variants
`rainbow_ring`, `border`, `gradient`, `logo_ring`, `pulse_orb`, `dots` (select globally in Branding Settings).

### PHP Helpers
In `private/gws-universal-functions.php`:
* `getBrandSpinnerStyle()` – current global style
* `getBrandSpinnerHTML($style=null, array $opts=[])`
* `echoBrandSpinner()`
* `getBrandSpinnerOverlayHTML()` / `echoBrandSpinnerOverlay()`

Options for `getBrandSpinnerHTML`:
* `label` (ARIA, default "Loading")
* `class` (extra classes on wrapper)
* `logo` (logo path for logo_ring)
* `size` (`sm|md|lg`)

### Overlay
Auto injected once via `admin/protection.php` as `#global-spinner-overlay`.

### JavaScript Helper (`admin/assets/js/brand-spinner.js`)
Global object `BrandSpinner`:
* `show(id?)`, `hide(id?)`, `toggle(id?)`
* `wrapAsync(promiseOrFn)` – show/hide around async
* `buttonLoading(button, true|false, { text, spinnerHTML })`
* `inline({size,label})` – fallback markup generator

Injected globals (set in protection layer):
* `BRAND_SPINNER_STYLE` – active style name
* `BRAND_SPINNER_INLINE_SM` – pre-rendered small spinner HTML

### Replacing Legacy Spinners
1. Search for `spinner-border` / `spinner-grow`.
2. Replace static markup with `<?php echo getBrandSpinnerHTML(null, ['size'=>'sm']); ?>`.
3. For dynamic button states use `BrandSpinner.buttonLoading(btn, true, { text: 'Processing...' });`.

### Inline Example
```php
<?php echo getBrandSpinnerHTML(null, ['size'=>'sm', 'label'=>'Saving']); ?>
```

### Async Example (overlay)
```js
BrandSpinner.wrapAsync(fetch('/admin/api/run-task'))
  .then(r=>r.json())
  .then(data=>{/* handle */});
```

### Accessibility
* Slow 2s animation; respects `prefers-reduced-motion`.
* Always supply a concise ARIA label.

### Logo Ring
Provide a valid logo (SVG preferred). If missing, ring renders without inner image; supply via options: `getBrandSpinnerHTML('logo_ring', ['logo'=>'/assets/branding/logo.svg']);`.

### Future Ideas
* Data attribute auto-binding for forms
* Progress polling integration
* Dark mode contrast tweaks

---
Document v1.0
