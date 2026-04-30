/**
 * Drifter — Global Smart Effects
 * Page transitions, ripple, counters, scroll progress, smart nav
 */
(function () {
  'use strict';

  /* ── PAGE TRANSITION ─────────────────────────────────────── */
  const overlay = document.createElement('div');
  overlay.id = 'page-overlay';
  overlay.style.cssText = `
    position:fixed;inset:0;background:linear-gradient(135deg,#0a1628,#0f2b5e);
    z-index:99998;pointer-events:none;opacity:0;transition:opacity 0.35s ease;
  `;
  document.body.appendChild(overlay);

  // Fade in on load
  window.addEventListener('load', () => {
    overlay.style.opacity = '0';
    document.body.style.opacity = '1';
  });
  document.body.style.opacity = '0';
  document.body.style.transition = 'opacity 0.4s ease';
  setTimeout(() => { document.body.style.opacity = '1'; }, 50);

  // Fade out on navigation
  document.addEventListener('click', function (e) {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript') ||
        href.startsWith('mailto') || href.startsWith('tel') ||
        a.target === '_blank' || e.ctrlKey || e.metaKey) return;
    e.preventDefault();
    overlay.style.opacity = '1';
    overlay.style.pointerEvents = 'all';
    setTimeout(() => { window.location.href = href; }, 320);
  });

  /* ── SCROLL PROGRESS BAR ─────────────────────────────────── */
  const bar = document.createElement('div');
  bar.id = 'scroll-progress';
  bar.style.cssText = `
    position:fixed;top:0;left:0;height:3px;width:0%;z-index:99999;
    background:linear-gradient(90deg,#f97316,#fb923c,#fbbf24);
    transition:width 0.1s linear;
    box-shadow:0 0 8px rgba(249,115,22,0.60);
  `;
  document.body.appendChild(bar);

  window.addEventListener('scroll', () => {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    bar.style.width = Math.min(scrolled, 100) + '%';
  }, { passive: true });

  /* ── RIPPLE EFFECT ON BUTTONS ────────────────────────────── */
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-primary,.btn-secondary,.btn-outline,.nav-cta,.act-btn,.toggle-btn,.tab-btn,.quick-card');
    if (!btn) return;
    const ripple = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    ripple.style.cssText = `
      position:absolute;border-radius:50%;
      width:${size}px;height:${size}px;
      left:${e.clientX - rect.left - size / 2}px;
      top:${e.clientY - rect.top - size / 2}px;
      background:rgba(255,255,255,0.25);
      transform:scale(0);animation:rippleAnim 0.55s ease-out forwards;
      pointer-events:none;
    `;
    if (getComputedStyle(btn).position === 'static') btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });

  // Inject ripple keyframe
  const style = document.createElement('style');
  style.textContent = `@keyframes rippleAnim{to{transform:scale(2.5);opacity:0;}}`;
  document.head.appendChild(style);

  /* ── COUNTER ANIMATION ───────────────────────────────────── */
  window.animateCounter = function (el, target, prefix = '', suffix = '', duration = 1200) {
    if (!el) return;
    const start = 0;
    const startTime = performance.now();
    const easeOut = t => 1 - Math.pow(1 - t, 3);

    function update(now) {
      const elapsed = now - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const value = Math.round(easeOut(progress) * target);
      el.textContent = prefix + value.toLocaleString('en-IN') + suffix;
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  };

  /* ── SCROLL REVEAL ───────────────────────────────────────── */
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        revealObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

  function observeReveal() {
    document.querySelectorAll('.reveal:not(.visible)').forEach(el => revealObserver.observe(el));
  }
  observeReveal();

  // Re-observe after dynamic content loads
  window.observeReveal = observeReveal;

  /* ── SMART NAVBAR ────────────────────────────────────────── */
  const nav = document.querySelector('.drifter-nav');
  if (nav) {
    let lastScroll = 0;
    let ticking = false;

    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const current = window.scrollY;
          // Hide on scroll down, show on scroll up
          if (current > lastScroll && current > 100) {
            nav.style.transform = 'translateY(-100%)';
          } else {
            nav.style.transform = 'translateY(0)';
          }
          // Darken on scroll
          if (current > 50) {
            nav.style.background = 'rgba(10,22,40,0.99)';
            nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.40)';
          } else {
            nav.style.background = 'rgba(10,22,40,0.97)';
            nav.style.boxShadow = '0 2px 20px rgba(0,0,0,0.25)';
          }
          lastScroll = current;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });

    nav.style.transition = 'transform 0.35s cubic-bezier(0.4,0,0.2,1), background 0.3s, box-shadow 0.3s';
  }

  /* ── BACK TO TOP ─────────────────────────────────────────── */
  const btt = document.createElement('button');
  btt.id = 'back-to-top';
  btt.innerHTML = '<i class="fas fa-arrow-up"></i>';
  btt.style.cssText = `
    position:fixed;bottom:28px;left:28px;z-index:9990;
    width:44px;height:44px;border-radius:50%;border:none;cursor:pointer;
    background:linear-gradient(135deg,#f97316,#fb923c);color:white;
    font-size:1rem;box-shadow:0 4px 16px rgba(249,115,22,0.45);
    opacity:0;transform:translateY(20px);
    transition:opacity 0.3s,transform 0.3s;pointer-events:none;
  `;
  document.body.appendChild(btt);

  window.addEventListener('scroll', () => {
    if (window.scrollY > 400) {
      btt.style.opacity = '1';
      btt.style.transform = 'translateY(0)';
      btt.style.pointerEvents = 'auto';
    } else {
      btt.style.opacity = '0';
      btt.style.transform = 'translateY(20px)';
      btt.style.pointerEvents = 'none';
    }
  }, { passive: true });

  btt.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ── LOADING SPINNER FOR FORMS ───────────────────────────── */
  window.setLoading = function (btn, loading, text = '') {
    if (loading) {
      btn._originalHTML = btn.innerHTML;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (text || 'Loading...');
      btn.disabled = true;
    } else {
      btn.innerHTML = btn._originalHTML || text;
      btn.disabled = false;
    }
  };

  /* ── AUTO-SYNC INDICATOR ─────────────────────────────────── */
  window.createSyncIndicator = function (containerId) {
    const el = document.getElementById(containerId);
    if (!el) return null;
    const ind = document.createElement('div');
    ind.className = 'sync-indicator';
    ind.style.cssText = `
      display:inline-flex;align-items:center;gap:6px;
      font-size:0.75rem;color:#64748b;font-weight:500;
    `;
    ind.innerHTML = `
      <span class="sync-dot" style="width:7px;height:7px;border-radius:50%;background:#22c55e;animation:syncPulse 2s infinite;display:inline-block;"></span>
      <span class="sync-text">Live</span>
    `;
    el.appendChild(ind);

    const dotStyle = document.createElement('style');
    dotStyle.textContent = `@keyframes syncPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(1.4);}}`;
    document.head.appendChild(dotStyle);

    return {
      setSyncing: () => {
        ind.querySelector('.sync-dot').style.background = '#f97316';
        ind.querySelector('.sync-text').textContent = 'Syncing...';
      },
      setLive: (time) => {
        ind.querySelector('.sync-dot').style.background = '#22c55e';
        ind.querySelector('.sync-text').textContent = 'Updated ' + time;
      },
      setError: () => {
        ind.querySelector('.sync-dot').style.background = '#ef4444';
        ind.querySelector('.sync-text').textContent = 'Sync failed';
      }
    };
  };

  /* ── TOAST (global override) ─────────────────────────────── */
  window.showToast = function (msg, type = 'info', dur = 4000) {
    let t = document.getElementById('g-toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'g-toast';
      document.body.appendChild(t);
    }
    const icons = { success: 'check-circle', error: 'times-circle', info: 'info-circle', warning: 'exclamation-triangle' };
    t.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${msg}`;
    t.className = `show ${type}`;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.className = ''; }, dur);
  };

  /* ── CARD TILT EFFECT ────────────────────────────────────── */
  document.querySelectorAll('.v-card,.stat-card,.quick-card,.service-card').forEach(card => {
    card.addEventListener('mousemove', function (e) {
      const rect = this.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width - 0.5;
      const y = (e.clientY - rect.top) / rect.height - 0.5;
      this.style.transform = `translateY(-4px) rotateX(${-y * 6}deg) rotateY(${x * 6}deg)`;
    });
    card.addEventListener('mouseleave', function () {
      this.style.transform = '';
    });
  });

})();
