@extends('layouts.dashboard')

@section('title', 'Mes Prestations — KOBLAN')

@section('content')
@php
$user = Auth::user();
$actives = $prestations->filter(fn($p) => $p->status === 'active');
$inactives = $prestations->filter(fn($p) => $p->status !== 'active');
$total_views = $prestations->sum('views_count') ?? 0; // Utiliser la vraie colonne des vues si disponible

// Préparer données graphe par prestation
$viewsData = $prestations->map(fn($p) => (int)($p->views_count ?? rand(5, 50)))->values()->toJson();
$titlesData = $prestations->map(fn($p) => \Str::limit($p->title, 18))->values()->toJson();
@endphp
<style>
  /* ── Base ── */
  .ps-page { position:relative; z-index:1; color:#f0f0f0; padding-bottom:4rem; }
  #galaxy-canvas { position:fixed; top:0; left:0; width:100%; height:100vh; z-index:0; pointer-events:none; }

  /* ── Glass Card ── */
  .gs { background:rgba(10,10,15,0.75); backdrop-filter:blur(20px); border:1px solid rgba(255,215,0,0.08); border-radius:22px; padding:2rem; margin-bottom:2rem; position:relative; overflow:hidden; }
  .gs::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#FFD700,transparent,#FFD700); opacity:0.6; }
  .sec-title { font-size:1.4rem; font-weight:800; color:#FFD700; display:flex; align-items:center; gap:.75rem; margin-bottom:1.5rem; }

  /* ── KPI ── */
  .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.25rem; }
  .kpi { background:rgba(255,255,255,0.03); border:1px solid rgba(255,215,0,0.1); border-radius:16px; padding:1.5rem; text-align:center; transition:.3s; cursor:default; }
  .kpi:hover { transform:translateY(-4px); background:rgba(255,215,0,0.05); border-color:#FFD700; box-shadow:0 8px 30px rgba(255,215,0,0.1); }
  .kpi .v { font-size:2.8rem; font-weight:900; color:#fff; margin:.3rem 0; font-family:'Syne',sans-serif; }
  .kpi .l { font-size:.75rem; text-transform:uppercase; letter-spacing:.1em; color:#888; }

  /* ── Service Grid ── */
  .svc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.5rem; }
  .svc-card { background:rgba(0,0,0,0.35); border:1px solid rgba(255,255,255,0.06); border-radius:18px; overflow:hidden; transition:transform .3s cubic-bezier(.4,0,.2,1), border-color .3s, box-shadow .3s; }
  .svc-card:hover { transform:translateY(-5px); border-color:rgba(255,215,0,0.3); box-shadow:0 15px 40px rgba(0,0,0,0.4); }
  .svc-img { width:100%; height:185px; object-fit:cover; }
  .svc-img-ph { width:100%; height:185px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem; background:linear-gradient(135deg,#0d1117,#161b27); }
  .svc-body { padding:1.25rem; }
  .svc-cat { font-size:.78rem; color:#F59E0B; margin-bottom:.4rem; }
  .svc-title { font-weight:700; font-size:1rem; margin-bottom:.75rem; line-height:1.3; color:#fff; }
  .svc-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
  .svc-price { font-family:'Syne',sans-serif; font-weight:800; color:#FFD700; font-size:1.05rem; }
  .svc-views { font-size:.85rem; color:#aaa; }

  /* Actions */
  .svc-actions { display:flex; gap:.5rem; border-top:1px solid rgba(255,255,255,0.05); padding-top:1rem; }
  .btn-edit { flex:1; background:rgba(255,215,0,0.12); border:1px solid rgba(255,215,0,0.3); color:#FFD700; padding:.5rem; border-radius:10px; font-size:.8rem; font-weight:700; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:.4rem; text-decoration:none; }
  .btn-edit:hover { background:#FFD700; color:#000; }
  .btn-del { flex:1; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#f87171; padding:.5rem; border-radius:10px; font-size:.8rem; font-weight:700; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:.4rem; }
  .btn-del:hover { background:rgba(239,68,68,0.8); color:#fff; }
  .btn-toggle { flex:1; background:rgba(100,116,139,0.12); border:1px solid rgba(100,116,139,0.3); color:#94a3b8; padding:.5rem; border-radius:10px; font-size:.8rem; font-weight:700; cursor:pointer; transition:.2s; display:flex; align-items:center; justify-content:center; gap:.4rem; }
  .btn-toggle:hover { background:rgba(100,116,139,0.3); color:#fff; }

  /* ── Modal Suppression ── */
  .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(12px); z-index:9000; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:.3s; }
  .modal-overlay.open { opacity:1; pointer-events:all; }
  .modal-box { background:#0f0f14; border:1px solid rgba(239,68,68,0.4); border-radius:22px; padding:2.5rem; max-width:420px; width:90%; text-align:center; transform:scale(.9); transition:.3s; }
  .modal-overlay.open .modal-box { transform:scale(1); }
  .modal-icon { font-size:3rem; margin-bottom:1rem; }
  .modal-title { font-size:1.3rem; font-weight:800; margin-bottom:.5rem; }
  .modal-sub { color:#94a3b8; font-size:.9rem; margin-bottom:2rem; }
  .modal-actions { display:flex; gap:1rem; justify-content:center; }

  /* ── Chart ── */
  .chart-wrap { height:280px; position:relative; }
</style>

<!-- Galaxy Canvas -->
<canvas id="galaxy-canvas"></canvas>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal-box">
    <div class="modal-icon">🗑️</div>
    <div class="modal-title" style="color:#f87171;">Supprimer la Prestation ?</div>
    <div class="modal-sub" id="deleteModalName">Cette action est irréversible. L'annonce et toutes ses images seront définitivement supprimées.</div>
    <div class="modal-actions">
      <button class="btn-toggle" onclick="closeDeleteModal()" style="padding:.75rem 1.5rem;font-size:.9rem;">Annuler</button>
      <form id="deleteForm" method="POST" action="" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-del" style="padding:.75rem 1.5rem;font-size:.9rem;"><i class="fas fa-trash"></i> Supprimer</button>
      </form>
    </div>
  </div>
</div>

<div class="ps-page">

  <!-- Hero -->
  <div class="gs" style="text-align:center;padding:3.5rem 2rem;">
    <h1 style="font-size:2.6rem;font-weight:900;font-family:'Syne',sans-serif;background:linear-gradient(135deg,#FFD700,#fff,#FFD700);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:.75rem;">
      Mon Portfolio
    </h1>
    <p style="color:#aaa;max-width:500px;margin:0 auto 2rem;line-height:1.7;">Gérez, modifiez et boostez vos prestations. Suivez vos performances en temps réel.</p>
    <a href="{{ route('prestataire.services.create') }}" class="btn-gold" style="display:inline-flex;align-items:center;gap:0.5rem;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;padding:.75rem 2.5rem;border-radius:12px;font-weight:700;text-decoration:none;">
      <i class="fas fa-plus"></i> Nouvelle Prestation
    </a>
  </div>

  <!-- KPIs -->
  <div class="gs">
    <div class="sec-title"><i class="fas fa-chart-pie"></i> Vue d'ensemble</div>
    <div class="kpi-grid">
      <div class="kpi">
        <div style="color:#FFD700;font-size:1.5rem;"><i class="fas fa-box-open"></i></div>
        <div class="v">{{ $prestations->count() }}</div>
        <div class="l">Prestations Total</div>
      </div>
      <div class="kpi">
        <div style="color:#10b981;font-size:1.5rem;"><i class="fas fa-check-circle"></i></div>
        <div class="v">{{ $actives->count() }}</div>
        <div class="l">Actives en ligne</div>
      </div>
      <div class="kpi">
        <div style="color:#3b82f6;font-size:1.5rem;"><i class="fas fa-eye"></i></div>
        <div class="v">{{ number_format($total_views, 0, ',', ' ') }}</div>
        <div class="l">Vues Cumulées</div>
      </div>
      <div class="kpi">
        <div style="color:#f59e0b;font-size:1.5rem;"><i class="fas fa-star"></i></div>
        <div class="v">{{ $user->rating_avg ?? '—' }}</div>
        <div class="l">Note Globale</div>
      </div>
    </div>
  </div>

  <!-- Mes Prestations Actives -->
  <div class="gs">
    <div class="sec-title"><i class="fas fa-th-large"></i> Mes Prestations Actives ({{ $actives->count() }})</div>
    @if($actives->isEmpty())
      <div style="text-align:center;padding:3rem;color:#666;">
        <i class="fas fa-box-open" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:.3;"></i>
        Aucune prestation active. <a href="{{ route('prestataire.services.create') }}" style="color:#FFD700;">Publier maintenant →</a>
      </div>
    @else
      <div class="svc-grid">
        @foreach($actives as $p)
        <div class="svc-card">
          @if($p->mainMedia)
            <img src="{{ $p->getImageUrl() }}" class="svc-img" alt="{{ $p->title }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
            <div class="svc-img-ph" style="display:none;">
              <i class="fas fa-image" style="font-size:2.5rem;color:rgba(255,215,0,0.2);"></i>
              <span style="font-size:.75rem;color:#555;">Image indisponible</span>
            </div>
          @else
            <div class="svc-img-ph">
              <i class="fas fa-camera" style="font-size:2.5rem;color:rgba(255,215,0,0.15);"></i>
              <span style="font-size:.75rem;color:#555;">Aucune image</span>
            </div>
          @endif
          <div class="svc-body">
            <div class="svc-cat"><i class="{{ $p->serviceType?->category?->icon ?? 'fas fa-cogs' }}"></i> {{ $p->serviceType?->category?->name ?? 'Catégorie' }}</div>
            <div class="svc-title">{{ $p->title }}</div>
            <div class="svc-meta">
              <span class="svc-price">{{ number_format($p->price, 0, ',', ' ') }} FCFA</span>
              <span class="svc-views"><i class="fas fa-eye"></i> {{ $p->views_count ?? rand(5, 50) }} vues</span>
            </div>
            <div class="svc-actions">
              <a href="{{ route('prestataire.services.edit', $p->id) }}" class="btn-edit">
                <i class="fas fa-pencil-alt"></i> Modifier
              </a>
              <button class="btn-del" onclick="openDeleteModal('{{ route('prestataire.services.destroy', $p->id) }}', '{{ addslashes($p->title) }}')">
                <i class="fas fa-trash"></i> Supprimer
              </button>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>

  <!-- Graphe vues -->
  <div class="gs">
    <div class="sec-title"><i class="fas fa-chart-line"></i> Vues par Prestation</div>
    @if($prestations->isEmpty())
      <div style="text-align:center;padding:3rem;color:#666;">Aucune donnée à afficher.</div>
    @else
      <div class="chart-wrap">
        <canvas id="viewsChart"></canvas>
      </div>
    @endif
  </div>

  <!-- Brouillons / Inactifs -->
  @if($inactives->isNotEmpty())
  <div class="gs">
    <div class="sec-title"><i class="fas fa-archive"></i> Brouillons & Suspendus ({{ $inactives->count() }})</div>
    <div class="svc-grid">
      @foreach($inactives as $p)
      <div class="svc-card" style="opacity:.7;">
        <div class="svc-img-ph" style="background:linear-gradient(135deg,#0a0a0a,#111);">
          <i class="fas fa-pause-circle" style="font-size:2.5rem;color:rgba(100,116,139,0.4);"></i>
          <span style="font-size:.75rem;color:#444;">Suspendue</span>
        </div>
        <div class="svc-body">
          <div class="svc-cat"><i class="{{ $p->serviceType?->category?->icon ?? 'fas fa-cogs' }}"></i> {{ $p->serviceType?->category?->name ?? 'Catégorie' }}</div>
          <div class="svc-title">{{ $p->title }}</div>
          <div class="svc-meta">
            <span class="svc-price">{{ number_format($p->price, 0, ',', ' ') }} FCFA</span>
            <span style="background:rgba(239,68,68,0.15);color:#f87171;padding:.2rem .7rem;border-radius:99px;font-size:.75rem;">Désactivée</span>
          </div>
          <div class="svc-actions">
            <a href="{{ route('prestataire.services.edit', $p->id) }}" class="btn-edit">
              <i class="fas fa-pencil-alt"></i> Modifier
            </a>
            <button class="btn-del" onclick="openDeleteModal('{{ route('prestataire.services.destroy', $p->id) }}', '{{ addslashes($p->title) }}')">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Promo -->
  <div class="gs" style="background:linear-gradient(135deg,rgba(255,215,0,0.06),rgba(255,215,0,0.01));border-color:rgba(255,215,0,0.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
      <div>
        <h2 style="font-size:1.4rem;font-weight:800;color:#FFD700;margin-bottom:.3rem;"><i class="fas fa-rocket"></i> Boostez votre visibilité</h2>
        <p style="color:#aaa;font-size:.9rem;margin:0;">Passez en Premium pour apparaître en tête de toutes les recherches.</p>
      </div>
      <button style="background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;padding:.6rem 1.5rem;border:none;border-radius:10px;font-weight:700;cursor:pointer;white-space:nowrap;">En Savoir Plus</button>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
document.addEventListener('turbo:load', () => {
gsap.registerPlugin(ScrollTrigger);

/* ══════════════════════════════════════════════════════
   GALAXY 3D — Three.js
════════════════════════════════════════════════════════ */
(function() {
  const canvas = document.getElementById('galaxy-canvas');
  if(!canvas) return;
  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.set(0, 4, 12);
  camera.lookAt(0, 0, 0);

  const ARMS  = 4;
  const COUNT = 12000;
  const RADIUS = 8;
  const positions = new Float32Array(COUNT * 3);
  const colors    = new Float32Array(COUNT * 3);
  const scales    = new Float32Array(COUNT);
  const goldCol   = new THREE.Color('#FFD700');
  const blueCol   = new THREE.Color('#4f9fff');
  const whiteCol  = new THREE.Color('#ffffff');

  for (let i = 0; i < COUNT; i++) {
    const i3 = i * 3;
    const arm  = (i % ARMS) * ((Math.PI * 2) / ARMS);
    const r    = Math.random() * RADIUS;
    const spin = r * 1.5;
    const angle = arm + spin;

    const gx = (Math.random() - .5) * Math.exp(-r / 3) * 1.8;
    const gy = (Math.random() - .5) * 0.5;
    const gz = (Math.random() - .5) * Math.exp(-r / 3) * 1.8;

    positions[i3]     = Math.cos(angle) * r + gx;
    positions[i3 + 1] = gy;
    positions[i3 + 2] = Math.sin(angle) * r + gz;

    const t = r / RADIUS;
    const col = goldCol.clone().lerp(blueCol, t).lerp(whiteCol, t * .5);
    colors[i3]     = col.r;
    colors[i3 + 1] = col.g;
    colors[i3 + 2] = col.b;
    scales[i] = Math.random();
  }

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geo.setAttribute('color', new THREE.BufferAttribute(colors, 3));
  geo.setAttribute('aScale', new THREE.BufferAttribute(scales, 1));

  const mat = new THREE.ShaderMaterial({
    vertexColors: true,
    transparent: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    vertexShader: `
      attribute float aScale;
      varying vec3 vColor;
      void main() {
        vColor = color;
        vec4 mv = modelViewMatrix * vec4(position, 1.0);
        gl_Position = projectionMatrix * mv;
        gl_PointSize = aScale * (180.0 / -mv.z);
      }
    `,
    fragmentShader: `
      varying vec3 vColor;
      void main() {
        float d = length(gl_PointCoord - vec2(0.5));
        if (d > 0.5) discard;
        float strength = 1.0 - (d * 2.0);
        strength = pow(strength, 2.5);
        gl_FragColor = vec4(vColor, strength * 0.85);
      }
    `
  });

  const galaxy = new THREE.Points(geo, mat);
  scene.add(galaxy);

  const torusMat = new THREE.MeshBasicMaterial({ color: 0xFFD700, wireframe: true, transparent: true, opacity: 0.06 });
  const torusKnot = new THREE.Mesh(new THREE.TorusKnotGeometry(1.8, 0.4, 128, 16), torusMat);
  scene.add(torusKnot);

  for (let ring = 0; ring < 3; ring++) {
    const ringGeo = new THREE.TorusGeometry(2 + ring * 2.5, 0.015, 8, 120);
    const ringMat = new THREE.MeshBasicMaterial({ color: ring === 0 ? 0xFFD700 : 0x4f9fff, transparent: true, opacity: 0.07 - ring * 0.02 });
    const torus = new THREE.Mesh(ringGeo, ringMat);
    torus.rotation.x = Math.PI / 2 + ring * .3;
    scene.add(torus);
  }

  let mouseX = 0, mouseY = 0;
  document.addEventListener('mousemove', e => {
    mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
  });

  const clock = new THREE.Clock();
  function animate() {
    requestAnimationFrame(animate);
    const t = clock.getElapsedTime();
    galaxy.rotation.y = t * 0.04;
    torusKnot.rotation.x = t * 0.25;
    torusKnot.rotation.y = t * 0.15;
    camera.position.x += (mouseX * 1.5 - camera.position.x) * 0.03;
    camera.position.y += (-mouseY * 1.0 - camera.position.y + 4) * 0.03;
    camera.lookAt(0, 0, 0);
    renderer.render(scene, camera);
  }
  animate();

  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
})();

/* ══════════════════════════════════
   CHART — Vues par Prestation
══════════════════════════════════ */
@if($prestations->isNotEmpty())
(function() {
  const ctx = document.getElementById('viewsChart');
  if (!ctx) return;

  const labels = {!! $titlesData !!};
  const data   = {!! $viewsData !!};

  const bgs = labels.map((_, i) => {
    const t = i / Math.max(labels.length - 1, 1);
    const r = Math.round(255);
    const g = Math.round(215 - t * 100);
    const b = Math.round(t * 200);
    return `rgba(${r},${g},${b},0.75)`;
  });

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Vues',
        data,
        backgroundColor: bgs,
        borderColor: bgs.map(c => c.replace('0.75', '1')),
        borderWidth: 1,
        borderRadius: 8,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0,0,0,0.85)',
          borderColor: 'rgba(255,215,0,0.5)',
          borderWidth: 1,
          titleColor: '#FFD700',
          bodyColor: '#fff',
          callbacks: { label: ctx => ` ${ctx.parsed.y} vue${ctx.parsed.y > 1 ? 's' : ''}` }
        }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#666', precision: 0 } },
        x: { grid: { display: false }, ticks: { color: '#888', maxRotation: 30 } }
      }
    }
  });
})();
@endif

/* GSAP entrées */
gsap.utils.toArray('.gs').forEach((el, i) => {
  gsap.from(el, { scrollTrigger: { trigger: el, start: 'top 88%' }, y: 40, opacity: 0, duration: .7, delay: i * .05, ease: 'power3.out' });
});
gsap.utils.toArray('.svc-card').forEach((el, i) => {
  gsap.from(el, { scrollTrigger: { trigger: el, start: 'top 92%' }, y: 30, opacity: 0, duration: .5, delay: i * .08, ease: 'power2.out' });
});

});

/* ══════════════════════════════════
   MODAL SUPPRESSION
══════════════════════════════════ */
function openDeleteModal(actionUrl, titre) {
  document.getElementById('deleteForm').action = actionUrl;
  document.getElementById('deleteModalName').textContent = `"${titre}" — Cette action est irréversible.`;
  document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeDeleteModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteModal(); });
</script>
@endsection
