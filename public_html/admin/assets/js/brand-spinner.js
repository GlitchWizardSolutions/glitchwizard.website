// Brand Spinner JS Helper
// Provides a lightweight API for showing/hiding the global overlay spinner
// and managing inline/button loading states without duplicating markup.
// Relies on the PHP-injected overlay element (id: global-spinner-overlay) and
// global variables BRAND_SPINNER_STYLE and BRAND_SPINNER_INLINE_SM (optional).
(function(w){
  const DEFAULT_OVERLAY_ID = 'global-spinner-overlay';
  function $(id){ return document.getElementById(id); }

  const api = {
    show(id){ const el = $(id||DEFAULT_OVERLAY_ID); if(el){ el.classList.remove('d-none'); el.removeAttribute('aria-hidden'); } },
    hide(id){ const el = $(id||DEFAULT_OVERLAY_ID); if(el){ el.classList.add('d-none'); el.setAttribute('aria-hidden','true'); } },
    toggle(id){ const el=$(id||DEFAULT_OVERLAY_ID); if(!el) return; const hidden=el.classList.toggle('d-none'); hidden?el.setAttribute('aria-hidden','true'):el.removeAttribute('aria-hidden'); },
    wrapAsync(promiseOrFn, id){
      api.show(id);
      try {
        const p = (typeof promiseOrFn === 'function') ? promiseOrFn() : promiseOrFn;
        return Promise.resolve(p).finally(()=>api.hide(id));
      } catch (e) { api.hide(id); throw e; }
    },
    buttonLoading(btn, loading=true, opts={}){
      if(!btn) return;
      // Clear any previous auto revert listeners if toggling off or re-enabling
      if(!loading && btn._autoRevertHandlers){
        btn._autoRevertHandlers.forEach(({target, ev, fn})=> target.removeEventListener(ev, fn));
        btn._autoRevertHandlers = null;
      }
      if(loading){
        if(!btn._origHtml){ btn._origHtml = btn.innerHTML; }
        btn.disabled = true;
        const spinnerHTML = opts.spinnerHTML || w.BRAND_SPINNER_INLINE_SM || '<div class="brand-spinner-rainbow brand-spinner-size-sm" role="status" aria-label="Loading"></div>';
        const text = (opts.text !== undefined) ? opts.text : 'Processing...';
        btn.innerHTML = spinnerHTML + ' ' + text;
        // Auto revert on custom events (first one wins)
        const events = opts.autoRevertEvents || [];
        if(events.length){
          const target = opts.eventTarget || window;
            btn._autoRevertHandlers = [];
            const revert = ()=> api.buttonLoading(btn, false);
            events.forEach(ev=>{
              const fn = ()=>{ revert(); events.forEach(e2=> target.removeEventListener(e2, fn)); };
              target.addEventListener(ev, fn, { once:true });
              btn._autoRevertHandlers.push({target, ev, fn});
            });
        }
      } else {
        if(btn._origHtml){ btn.innerHTML = btn._origHtml; }
        btn.disabled = false;
      }
    },
    // Convenience: bind a button so that when it dispatches startEvent (e.g. click/form submit) it goes loading
    // and auto-reverts on any of endEvents.
    bindAuto(btn, startEvent='click', endEvents=['success','error'], opts={}){
      if(!btn) return;
      btn.addEventListener(startEvent, (e)=>{
        if(opts.preventDefault) e.preventDefault();
        api.buttonLoading(btn, true, { autoRevertEvents:endEvents, eventTarget:opts.eventTarget||window, text:opts.text, spinnerHTML:opts.spinnerHTML });
      });
    },
    inline(opts={}){
      const size = opts.size || 'sm';
      const label = opts.label || 'Loading';
      return '<div class="brand-spinner-rainbow brand-spinner-size-'+size+'" role="status" aria-label="'+label.replace(/"/g,'&quot;')+'"></div>';
    }
  };
  w.BrandSpinner = api;
})(window);
