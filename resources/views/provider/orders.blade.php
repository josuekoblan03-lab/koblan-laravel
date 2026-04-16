@extends('layouts.dashboard')

@section('title', 'Commandes Reçues — KOBLAN')

@section('content')
<div class="orders-container">
  <!-- ====== THREE.JS ADVANCED BACKGROUND ====== -->
  <canvas id="provider-3d-canvas"></canvas>

  <div class="orders-header gs-reveal">
    <div>
      <h1 class="orders-title">Gestion des Commandes</h1>
      <p style="color:var(--gray-400); font-size:1.1rem; margin-top:0.5rem;">
        Examinez les demandes, acceptez-les pour validation, et lancez vos travaux.
      </p>
    </div>
    <div class="orders-stats">
        <div class="stat-box">
            <span class="stat-value">{{ $orders->count() }}</span>
            <span class="stat-label">Total Reçues</span>
        </div>
        <div class="stat-box">
            <span class="stat-value" style="color:#eab308;">
              {{ $orders->where('status', 'pending')->count() }}
            </span>
            <span class="stat-label">À Valider</span>
        </div>
    </div>
  </div>

  @if(session('success'))
  <div class="gs-reveal" style="background:rgba(16, 185, 129, 0.2);border:1px solid rgba(16, 185, 129, 0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">
    {{ session('success') }}
  </div>
  @endif

  <div class="orders-list">
    @if($orders->isEmpty())
      <div class="empty-orders gs-reveal">
         <div class="empty-icon">📋</div>
         <h2>Aucune commande reçue</h2>
         <p>Optimisez vos services pour attirer plus de clients.</p>
      </div>
    @else
      @foreach($orders as $idx => $c)
      @php 
          $statusColors = [
              'pending' => ['#eab308', 'Nouvelle demande', 'fa-bell'],
              'accepted' => ['#3b82f6', 'En attente paiement client', 'fa-hourglass-half'],
              'in_progress' => ['#a855f7', 'Payé & En cours', 'fa-spinner fa-spin'],
              'completed' => ['#10b981', 'Terminée', 'fa-check-circle'],
              'cancelled' => ['#ef4444', 'Annulée / Refusée', 'fa-times-circle']
          ];
          $st = $statusColors[$c->status] ?? $statusColors['pending'];
          $p = $c->prestations->first();
          $p_title = $p ? $p->title : 'Service personnalisé';
      @endphp
      <div class="order-card gs-item" style="--delay: {{ $idx * 0.1 }}s">
        <!-- Glow effect based on status -->
        <div class="order-glow" style="background: {{ $st[0] }};"></div>
        
        <div class="order-content">
           <div class="order-client-info">
             <div class="client-avatar">
               @if($c->client->avatar)
                 <img src="{{ $c->client->getAvatarUrl() }}" alt="">
               @else
                 <span class="avatar-letter">{{ $c->client->getInitials() }}</span>
               @endif
             </div>
             <div class="client-details">
                <h3>{{ $c->client->name }}</h3>
                <span><i class="fas fa-map-marker-alt"></i> {{ $c->neighborhood->name ?? 'Non spécifié' }}</span>
                @if($c->client->phone)
                   <span><i class="fas fa-phone-alt"></i> {{ $c->client->phone }}</span>
                @endif
             </div>
           </div>
           
           <div class="order-details">
             <div class="order-meta">
                <span class="order-id">#CMD-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="order-date"><i class="far fa-calendar-alt"></i> {{ $c->created_at->format('d M Y à H:i') }}</span>
             </div>
             <h3 class="order-service-title">{{ $p_title }}</h3>
             <div class="order-status-badge" style="color: {{ $st[0] }};">
                <i class="fas {{ $st[2] }}"></i> {{ $st[1] }}
             </div>
           </div>

           <div class="order-actions-zone">
             <div class="order-price">
                {{ number_format($c->total_amount, 0, ',', ' ') }} <span style="font-size:1rem;color:var(--gray-500);">FCFA</span>
             </div>
             
             <!-- ACTONS CONTROL -->
             <div class="action-buttons">
                <!-- TODO: implement receipt route -->
                <a href="#" target="_blank" class="btn-icon" style="background:rgba(255,255,255,0.1); border-color:var(--gray-600);" title="Télécharger le reçu / facture">
                   <i class="fas fa-file-invoice"></i>
                </a>

                @if($c->status === 'pending')
                    <form action="{{ route('prestataire.orders.status', $c->id) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn-action accept" title="Accepter la commande">
                            <i class="fas fa-check"></i> Accepter
                        </button>
                    </form>
                    <form action="{{ route('prestataire.orders.status', $c->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cette commande ?');">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn-action refuse" title="Refuser">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                @elseif($c->status === 'accepted')
                    <form action="{{ route('prestataire.orders.status', $c->id) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="btn-action" style="background: #A855F7;" title="Démarrer le travail">
                            <i class="fas fa-spinner"></i> Démarrer
                        </button>
                    </form>
                @elseif($c->status === 'in_progress')
                    <form action="{{ route('prestataire.orders.status', $c->id) }}" method="POST" style="display:inline;">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn-action finish" title="Marquer comme Terminée">
                            <i class="fas fa-flag-checkered"></i> Terminer le travail
                        </button>
                    </form>
                @endif
                
                <a href="{{ url('/client/messages?with='.$c->client_id) }}" class="btn-icon msg-icon" style="background:#3b82f6;color:#fff;" title="Contacter le client">
                   <i class="fas fa-comment-dots"></i>
                </a>
             </div>
           </div>
        </div>
      </div>
      @endforeach
    @endif
    
    @if ($orders->hasPages())
    <div style="margin-top:2rem;">
      {{ $orders->links('pagination::bootstrap-4') }}
    </div>
    @endif
  </div>
</div>

<style>
/* =========================================
   STYLE WAHOU - COMMANDES PRESTATAIRE
   ========================================= */
body { background: #05050a !important; }
.dash-layout { background: transparent !important; }
.dash-main { background: transparent !important; }
.dash-content { padding: 0 !important; background: transparent !important; }

.orders-container {
  position: relative;
  min-height: 80vh;
  padding: 3rem 2rem 5rem 2rem;
  z-index: 1;
  max-width: 1400px; margin: 0 auto;
}

#provider-3d-canvas {
  position: fixed !important;
  top: 0; left: 0; width: 100vw; height: 100vh;
  z-index: 0;
  pointer-events: none;
  opacity: 0.85;
}

.orders-header {
  position: relative; z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 3rem;
  background: rgba(8,8,12,0.95);
  backdrop-filter: blur(20px);
  padding: 2rem;
  border-radius: 24px;
  border: 1px solid rgba(255,255,255,0.05);
  box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

.orders-title {
  font-family: var(--font-display);
  font-size: 2.8rem;
  font-weight: 900;
  background: linear-gradient(135deg, #fff, var(--gold-300));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin: 0;
}

.orders-stats { display: flex; gap: 1.5rem; }
.stat-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 1rem 1.5rem; text-align: center; min-width: 120px; }
.stat-value { display: block; font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #fff; }
.stat-label { font-size: 0.8rem; color: var(--gray-400); text-transform: uppercase; }

.orders-list { display: flex; flex-direction: column; gap: 1.5rem; position: relative; z-index: 10; }

.empty-orders { text-align: center; padding: 5rem 2rem; background: #0f1015; border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1); }
.empty-orders h2 { font-size: 2rem; color: #fff; margin-bottom: 0.5rem; }
.empty-orders p { color: var(--gray-500); margin-top: 0.5rem; }
.empty-icon { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; color: var(--gray-500); }

.order-card {
  position: relative;
  background: rgba(15, 16, 21, 0.9) !important;
  backdrop-filter: blur(20px);
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.08);
  overflow: hidden;
  transition: 0.4s;
}
.order-card:hover {  transform: translateY(-5px) scale(1.01); border-color: rgba(255,215,0,0.3); box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
.order-glow { position: absolute; top: 0; left: 0; bottom: 0; width: 4px; box-shadow: 0 0 20px currentcolor; }

.order-content { display: flex; padding: 1.5rem; gap: 2rem; align-items: stretch; }

.order-client-info {
    width: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-right: 1px dashed rgba(255,255,255,0.1);
    padding-right: 1.5rem;
    text-align: center;
}
.client-avatar {
    width: 60px; height: 60px; border-radius: 50%; overflow: hidden; margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--gold-300), #F77F00); display: flex; align-items: center; justify-content: center;
}
.client-avatar img { width: 100%; height: 100%; object-fit: cover; }
.avatar-letter { color: #000; font-weight: 900; font-size: 1.5rem; }
.client-details h3 { font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
.client-details span { display: block; font-size: 0.85rem; color: var(--gray-400); margin-bottom: 0.2rem; }

.order-details { flex: 1; display: flex; flex-direction: column; justify-content: center; }
.order-meta { display: flex; gap: 1rem; margin-bottom: 0.5rem; font-size: 0.85rem; font-family: var(--font-mono, monospace); color: var(--gray-400); }
.order-id { color: var(--gold-300); background: rgba(255,215,0,0.1); padding: 2px 8px; border-radius: 6px; }
.order-service-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin: 0 0 1rem 0; }
.order-status-badge { font-size: 0.9rem; font-weight: 600; display: inline-block; padding: 4px 10px; border-radius: 8px; background: rgba(255,255,255,0.05); }

.order-actions-zone {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
  border-left: 1px dashed rgba(255,255,255,0.1);
  padding-left: 2rem;
  min-width: 250px;
}
.order-price { font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #fff; text-align: right; }

.action-buttons { display: flex; gap: 0.5rem; align-items: center; }
.btn-action { 
    padding: 0.6rem 1.2rem; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; transition: 0.2s; color: #fff; display: flex; align-items: center; gap: 0.5rem; 
}
.btn-action.accept { background: #3b82f6; }
.btn-action.accept:hover { background: #2563eb; transform: scale(1.05); }
.btn-action.refuse { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; padding: 0.6rem; }
.btn-action.refuse:hover { background: #ef4444; color: #fff; }
.btn-action.finish { background: #10b981; }
.btn-action.finish:hover { background: #059669; transform: scale(1.05); box-shadow: 0 0 20px rgba(16,185,129,0.4); }

.btn-icon { width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; }
.btn-icon:hover { background: var(--gold-300); color: #000; transform: scale(1.1); }

@media(max-width: 900px) {
  .order-content { flex-direction: column; }
  .order-client-info { width: 100%; border-right: none; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-right: 0; padding-bottom: 1.5rem; }
  .order-actions-zone { border-left: none; padding-left: 0; align-items: flex-start; gap: 1rem; margin-top: 1rem; }
}

/* Fix sidebar opaqueness */
.dash-sidebar { position: fixed !important; z-index: 100 !important; background: rgba(5, 5, 10, 0.95) !important; backdrop-filter: blur(30px) !important; }
.dash-topbar { background: rgba(5, 5, 10, 0.85) !important; backdrop-filter: blur(20px) !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
</style>
@endsection

@section('scripts')
<!-- SCRIPTS GSAP & THREE.JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
document.addEventListener('turbo:load', () => {
    if(typeof gsap !== 'undefined') {
        gsap.from(".gs-reveal", { y: 40, opacity: 0, duration: 1, stagger:0.2, ease:"power3.out", clearProps:"all" });

        document.querySelectorAll('.gs-item').forEach((item) => {
            let delay = parseFloat(item.style.getPropertyValue('--delay')) || 0;
            gsap.from(item, { x: 50, opacity: 0, duration: 0.8, delay: delay + 0.3, ease:"back.out(1.2)", clearProps:"all" });
        });
    }

    // 2. THREE.JS PROVIDER DASHBOARD (Abstract Hexagonal Matrix)
    const initProvider3D = () => {
        const canvas = document.getElementById('provider-3d-canvas');
        if(!canvas || typeof THREE === 'undefined') return;

        const scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x050505, 0.003);

        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 1, 500);
        camera.position.z = 100;
        camera.position.y = 50;
        camera.lookAt(0,0,0);

        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // CREATE HEXAGONAL PRISMS MAP
        const geometry = new THREE.CylinderGeometry(2, 2, 0.5, 6);
        const material = new THREE.MeshPhongMaterial({
            color: 0x1f1f1f,
            emissive: 0x000000,
            specular: 0x555555,
            shininess: 30,
            flatShading: true,
            transparent: true,
            opacity: 0.6
        });

        const group = new THREE.Group();
        const hexSize = 4;
        const width = 30;
        const height = 30;

        for(let x = -width/2; x < width/2; x++) {
            for(let y = -height/2; y < height/2; y++) {
                const hex = new THREE.Mesh(geometry, material.clone());
                // Hexagonal grid displacement
                const posX = x * hexSize * 1.5;
                const posZ = y * hexSize * Math.sqrt(3) + (Math.abs(x) % 2 === 1 ? hexSize * Math.sqrt(3)/2 : 0);
                
                hex.position.set(posX, 0, posZ);
                // Base Y offset random
                hex.userData.baseY = Math.random() * 2;
                hex.position.y = hex.userData.baseY;
                
                // Add randomly gold emissive material
                if(Math.random() > 0.95) {
                    hex.material.emissive.setHex(0xFFD700);
                } else if(Math.random() > 0.90) {
                    hex.material.emissive.setHex(0x3b82f6);
                }

                group.add(hex);
            }
        }
        scene.add(group);

        const light = new THREE.DirectionalLight(0xffffff, 1);
        light.position.set(100, 100, 50);
        scene.add(light);
        scene.add(new THREE.AmbientLight(0x404040));

        let mouseX = 0; let mouseY = 0;
        document.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX - window.innerWidth / 2) * 0.1;
            mouseY = (e.clientY - window.innerHeight / 2) * 0.1;
        });

        // Animation Loop
        let time = 0;
        const animate = () => {
            requestAnimationFrame(animate);
            time += 0.05;

            // Wave effect on hexagons
            group.children.forEach((hex, i) => {
               const dist = Math.sqrt(hex.position.x * hex.position.x + hex.position.z * hex.position.z);
               hex.position.y = hex.userData.baseY + Math.sin(dist * 0.1 - time * 0.5) * 5;
            });

            // Gentle camera movement
            camera.position.x += (mouseX - camera.position.x) * 0.05;
            camera.position.z += (100 + mouseY - camera.position.z) * 0.05;
            camera.lookAt(scene.position);

            renderer.render(scene, camera);
        };
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    };
    
    setTimeout(initProvider3D, 200);
});
</script>
@endsection
