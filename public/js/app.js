// ============================================================
// KOBLAN - App.js Principal
// GSAP Animations + UI Interactions
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // ── 1. PAGE LOADER ──
  const loader = document.getElementById('pageLoader');
  if (loader) {
    const hideLoader = () => {
      try {
        if (typeof gsap !== 'undefined') {
          gsap.to(loader, {
            opacity: 0, duration: 0.6, ease: 'power2.out',
            onComplete: () => { loader.style.display = 'none'; }
          });
        } else {
          loader.style.opacity = '0';
          setTimeout(() => { loader.style.display = 'none'; }, 600);
        }
      } catch (e) {
        loader.style.display = 'none';
      }
    };
    
    window.addEventListener('load', hideLoader);
    setTimeout(() => {
      if (loader.style.display !== 'none') hideLoader();
    }, 2500); // Réduit à 2.5 secondes par précaution
  }

  // ── 2. INITIALISATION GSAP ──
  if (typeof gsap !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger, TextPlugin);
    initGSAPAnimations();
    initHeroText();
  }

  // ── 3. CURSOR CUSTOM ──
  initCustomCursor();

  // ── 4. NAVBAR & MENU MOBILE ──
  initNavbar();
  initMobileMenu();

  // ── 5. STATS COUNTER ──
  initCounters();

  // ── 6. TILT EFFECT ──
  initTilt();

  // ── 7. NOTIFICATIONS ──
  initDropdowns();
  loadNotificationCount();

  // ── 8. SCROLL & ALERTS ──
  initTestimonialsScroll();
  
  const flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(() => {
      gsap.to(flash, { opacity: 0, y: -20, duration: 0.4, onComplete: () => flash.remove() });
    }, 5000);
  }
});

// ============================================================
// ANIMATIONS GSAP GLOBALES
// ============================================================
function initGSAPAnimations() {
  // Révélations de bas en haut
  gsap.utils.toArray('.reveal').forEach((el) => {
    const delay = parseFloat(el.dataset.delay || 0);
    gsap.fromTo(el,
      { opacity: 0, y: 40 },
      { opacity: 1, y: 0, duration: 0.8, delay: delay, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 85%' }
      }
    );
  });

  // Révélations de la gauche
  gsap.utils.toArray('.reveal-left').forEach((el) => {
    const delay = parseFloat(el.dataset.delay || 0);
    gsap.fromTo(el,
      { opacity: 0, x: -50 },
      { opacity: 1, x: 0, duration: 0.8, delay: delay, ease: 'power3.out',
        scrollTrigger: { trigger: el, start: 'top 85%' }
      }
    );
  });

  // Révélations scale (zoom in)
  gsap.utils.toArray('.scale-reveal').forEach((el) => {
    gsap.fromTo(el,
      { opacity: 0, scale: 0.8 },
      { opacity: 1, scale: 1, duration: 0.8, ease: 'back.out(1.5)',
        scrollTrigger: { trigger: el, start: 'top 85%' }
      }
    );
  });
}

function initHeroText() {
  const words = ['heroWord1','heroWord2','heroWord3','heroWord4','heroWord5','heroWord6'];
  const tl = gsap.timeline({ delay: 0.5 });
  words.forEach((id, i) => {
    const el = document.getElementById(id);
    if (el) {
      tl.fromTo(el,
        { y: '110%', opacity: 0 },
        { y: '0%', opacity: 1, duration: 0.8, ease: 'back.out(1.4)' },
        i * 0.1
      );
    }
  });

  // Typing effect sur le placeholder si searchHero existe
  const searchInput = document.getElementById('heroSearch');
  if (searchInput) {
    const phrases = ['coiffeuse à domicile...', 'un plombier urgent...', 'garde d\'enfants...', 'un électricien...', 'ménage complet...'];
    let phraseIndex = 0;
    function typePlaceholder() {
      let phrase = phrases[phraseIndex % phrases.length];
      phraseIndex++;
      let i = 0;
      searchInput.placeholder = '';
      const interval = setInterval(() => {
        searchInput.placeholder = 'Rechercher ' + phrase.substring(0, i++);
        if (i > phrase.length + 1) {
          clearInterval(interval);
          setTimeout(typePlaceholder, 2500);
        }
      }, 60);
    }
    setTimeout(typePlaceholder, 2000);
  }
}

// ============================================================
// CURSOR MAGNÉTIQUE
// ============================================================
function initCustomCursor() {
  const dot = document.getElementById('cursorDot');
  const ring = document.getElementById('cursorRing');
  if (!dot || !ring) return;

  let mouse = { x: 0, y: 0 };
  let ringPos = { x: 0, y: 0 };
  
  window.addEventListener('mousemove', (e) => {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
    dot.style.transform = `translate(${mouse.x}px, ${mouse.y}px) translate(-50%, -50%)`;
  });

  function renderRing() {
    ringPos.x += (mouse.x - ringPos.x) * 0.15;
    ringPos.y += (mouse.y - ringPos.y) * 0.15;
    ring.style.transform = `translate(${ringPos.x}px, ${ringPos.y}px) translate(-50%, -50%)`;
    requestAnimationFrame(renderRing);
  }
  requestAnimationFrame(renderRing);

  // Hover sur liens et boutons
  document.querySelectorAll('a, button, input, textarea, select, .glass-card, [data-tilt]').forEach(el => {
    el.addEventListener('mouseenter', () => {
      ring.style.width = '52px'; ring.style.height = '52px';
      ring.style.borderColor = 'var(--gold-300)';
      dot.style.transform = `translate(${mouse.x}px, ${mouse.y}px) translate(-50%, -50%) scale(1.5)`;
    });
    el.addEventListener('mouseleave', () => {
      ring.style.width = '36px'; ring.style.height = '36px';
      ring.style.borderColor = 'rgba(255,215,0,0.6)';
    });
  });

  // Magnetic effect for specfic buttons
  document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const rect = btn.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;
      btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    });
    btn.addEventListener('mouseleave', () => {
      btn.style.transform = 'translate(0, 0)';
    });
  });
}

// ============================================================
// NAVBAR ET MENU
// ============================================================
function initNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const current = window.scrollY;
    if (current > 50) navbar.classList.add('scrolled');
    else navbar.classList.remove('scrolled');

    if (current > lastScroll && current > 200) {
      gsap.to(navbar, { y: '-100%', duration: 0.3 });
    } else {
      gsap.to(navbar, { y: '0%', duration: 0.3 });
    }
    lastScroll = current;
  }, { passive: true });
}

function initMobileMenu() {
  const toggle = document.getElementById('menuToggle');
  const mobileNav = document.getElementById('mobileNav');
  const closeBtn = document.getElementById('mobileNavClose');
  if (!toggle || !mobileNav) return;

  function openMenu() {
    mobileNav.classList.add('active');
    document.body.style.overflow = 'hidden';
    gsap.fromTo(mobileNav.querySelectorAll('a'), 
      { y: 30, opacity: 0 }, 
      { y: 0, opacity: 1, duration: 0.4, stagger: 0.05, ease: 'power2.out', delay: 0.1 }
    );
  }
  function closeMenu() {
    mobileNav.classList.remove('active');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', openMenu);
  closeBtn?.addEventListener('click', closeMenu);
}

// ============================================================
// DIVERS (Compteurs, Tilt, Dropdowns)
// ============================================================
function initCounters() {
  document.querySelectorAll('[data-count]').forEach(counter => {
    const target = parseInt(counter.dataset.count);
    let obj = { val: 0 };
    ScrollTrigger.create({
      trigger: counter, start: 'top 85%', once: true,
      onEnter: () => {
        gsap.to(obj, {
          val: target, duration: 2, ease: 'power2.out',
          onUpdate: () => {
            counter.textContent = Math.round(obj.val).toLocaleString('fr-FR') + (target > 100 ? '+' : '');
          }
        });
      }
    });
  });
}

function initTilt() {
  document.querySelectorAll('[data-tilt]').forEach(el => {
    el.addEventListener('mousemove', (e) => {
      const r = el.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      el.style.transform = `perspective(800px) rotateX(${-y * 10}deg) rotateY(${x * 10}deg) scale(1.02)`;
    });
    el.addEventListener('mouseleave', () => {
      el.style.transform = 'perspective(800px) rotateX(0) rotateY(0) scale(1)';
    });
  });
}

function initDropdowns() {
  document.querySelectorAll('.dropdown').forEach(drop => {
    drop.addEventListener('click', (e) => {
      e.stopPropagation();
      document.querySelectorAll('.dropdown').forEach(d => { if(d !== drop) d.classList.remove('active'); });
      drop.classList.toggle('active');
    });
  });
  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
  });
}

function initTestimonialsScroll() {
  const track = document.getElementById('testimonialsTrack');
  if (!track) return;
  let interval = setInterval(scrollTrack, 3000);
  
  function scrollTrack() {
    if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 10) track.scrollTo({ left: 0, behavior: 'smooth' });
    else track.scrollBy({ left: 350, behavior: 'smooth' });
  }

  track.addEventListener('mouseenter', () => clearInterval(interval));
  track.addEventListener('mouseleave', () => { interval = setInterval(scrollTrack, 3000); });
}

async function loadNotificationCount() {
  try {
    const res = await fetch('/koblan/api/notifications?action=count');
    const data = await res.json();
    const b = document.getElementById('notifCount');
    if (b && data.count > 0) {
      b.textContent = data.count > 9 ? '9+' : data.count;
      b.style.display = 'flex';
    }
  } catch (e) {}
}
