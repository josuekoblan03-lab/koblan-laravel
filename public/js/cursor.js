// ============================================================
// KOBLAN - Curseur Magnétique Premium
// ============================================================

const cursorDot = document.getElementById('cursorDot');
const cursorRing = document.getElementById('cursorRing');

let mouseX = -100, mouseY = -100;
let ringX = -100, ringY = -100;
let isHovering = false;

// Suivi de la souris
document.addEventListener('mousemove', (e) => {
  mouseX = e.clientX;
  mouseY = e.clientY;
});

// Animation fluide du curseur
function animateCursor() {
  if (cursorDot) {
    cursorDot.style.left = mouseX + 'px';
    cursorDot.style.top  = mouseY + 'px';
  }

  // Ring suit avec délai (effet magnétique)
  if (cursorRing) {
    ringX += (mouseX - ringX) * 0.12;
    ringY += (mouseY - ringY) * 0.12;
    cursorRing.style.left = ringX + 'px';
    cursorRing.style.top  = ringY + 'px';
  }

  requestAnimationFrame(animateCursor);
}
animateCursor();

// Effet hover sur éléments interactifs
const hoverTargets = document.querySelectorAll('a, button, [data-tilt], .btn, .service-card, .category-card, .provider-card, input, select, textarea');

hoverTargets.forEach(el => {
  el.addEventListener('mouseenter', () => {
    cursorRing?.classList.add('hover');
    cursorDot && (cursorDot.style.transform = 'translate(-50%, -50%) scale(1.5)');
  });
  el.addEventListener('mouseleave', () => {
    cursorRing?.classList.remove('hover');
    cursorDot && (cursorDot.style.transform = 'translate(-50%, -50%) scale(1)');
  });
});

// Cacher le curseur quand il quitte la fenêtre
document.addEventListener('mouseleave', () => {
  if (cursorDot) cursorDot.style.opacity = '0';
  if (cursorRing) cursorRing.style.opacity = '0';
});
document.addEventListener('mouseenter', () => {
  if (cursorDot) cursorDot.style.opacity = '1';
  if (cursorRing) cursorRing.style.opacity = '1';
});
