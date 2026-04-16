@extends('layouts.dashboard')

@section('title', 'Mes Commandes — KOBLAN')

@section('content')

<div class="orders-container">
  <!-- THREE.JS BACKGROUND -->
  <canvas id="orders-3d-canvas"></canvas>

  <!-- CONTENU PRINCIPAL -->
  <div class="orders-foreground">

    <!-- HEADER STATS -->
    <div class="orders-header gs-reveal">
      <div>
        <h1 class="orders-title">Mes Commandes</h1>
        <p style="color:#aaa; font-size:1.1rem; margin-top:0.5rem;">
          Suivez et gérez l'ensemble de vos transactions sur la plateforme.
        </p>
      </div>
      <div class="orders-stats">
        <div class="stat-box">
          <span class="stat-value">{{ $orders->total() }}</span>
          <span class="stat-label">Total</span>
        </div>
        <div class="stat-box">
          <span class="stat-value" style="color:#eab308;">
            {{ $orders->getCollection()->where('status','pending')->count() + $orders->getCollection()->where('status','accepted')->count() }}
          </span>
          <span class="stat-label">En attente</span>
        </div>
        <div class="stat-box">
          <span class="stat-value" style="color:#10b981;">
            {{ $orders->getCollection()->where('status','completed')->count() }}
          </span>
          <span class="stat-label">Terminées</span>
        </div>
      </div>
    </div>

    <!-- LISTE DES COMMANDES -->
    <div class="orders-section-wrapper gs-reveal">
      <div class="section-divider">
        <div class="section-divider-line"></div>
        <span class="section-divider-label">
          <i class="fas fa-list-check"></i>
          @php $nb = $orders->total(); @endphp
          {{ $nb > 0 ? $nb . ' commande' . ($nb > 1 ? 's' : '') : 'Aucune commande' }}
        </span>
        <div class="section-divider-line"></div>
      </div>

      <div class="orders-list">
        @if($orders->isEmpty())
          <div style="text-align:center; padding:5rem 2rem;">
            <div style="font-size:4rem; margin-bottom:1rem; opacity:0.5;">📦</div>
            <h2 style="color:#fff;">Aucune commande pour le moment</h2>
            <p style="color:#666; margin-top:0.5rem;">Explorez nos services et passez votre première commande.</p>
            <a href="{{ route('services.index') }}" class="btn-cta-main" style="margin-top:2rem; display:inline-block; padding:1rem 2rem; border-radius:12px; font-weight:bold; background:linear-gradient(135deg,#FFD700,#F77F00); color:#000; text-decoration:none;">Découvrir les services</a>
          </div>
        @else
          @foreach($orders as $idx => $c)
          @php
            $statusMap = [
              'pending'     => ['color'=>'#eab308','bg'=>'rgba(234,179,8,0.12)','border'=>'rgba(234,179,8,0.4)','label'=>'En attente','icon'=>'fa-clock','desc'=>'En attente de validation du prestataire'],
              'accepted'    => ['color'=>'#3b82f6','bg'=>'rgba(59,130,246,0.12)','border'=>'rgba(59,130,246,0.4)','label'=>'Acceptée','icon'=>'fa-thumbs-up','desc'=>'Le prestataire a accepté votre demande'],
              'confirmed'   => ['color'=>'#3b82f6','bg'=>'rgba(59,130,246,0.12)','border'=>'rgba(59,130,246,0.4)','label'=>'Acceptée','icon'=>'fa-thumbs-up','desc'=>'Le prestataire a accepté votre demande'],
              'in_progress' => ['color'=>'#a855f7','bg'=>'rgba(168,85,247,0.12)','border'=>'rgba(168,85,247,0.4)','label'=>'En cours','icon'=>'fa-spinner fa-spin','desc'=>'Le service est en cours de réalisation'],
              'completed'   => ['color'=>'#10b981','bg'=>'rgba(16,185,129,0.12)','border'=>'rgba(16,185,129,0.4)','label'=>'Terminée','icon'=>'fa-check-circle','desc'=>'Service réalisé avec succès'],
              'cancelled'   => ['color'=>'#ef4444','bg'=>'rgba(239,68,68,0.12)','border'=>'rgba(239,68,68,0.4)','label'=>'Annulée','icon'=>'fa-times-circle','desc'=>'Cette commande a été annulée'],
            ];
            $st = $statusMap[$c->status] ?? $statusMap['pending'];
            $prestation = $c->prestation;
            $prest = $c->prestataire;
            $amount = $c->total_amount ?? $c->amount ?? 0;
          @endphp
          <div class="order-card gs-item" style="--delay:{{ $idx * 0.1 }}s;">
            <!-- Barre colorée statut -->
            <div class="order-status-bar" style="background:{{ $st['color'] }};"></div>

            <div class="order-content">
              <!-- Image -->
              <div class="order-image">
                @if($prestation && $prestation->mainMedia)
                  <img src="{{ asset('storage/'.$prestation->mainMedia->media_url) }}" alt="Prestation">
                @else
                  <div style="width:100%;height:100%;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-box" style="font-size:2rem;color:#555;"></i>
                  </div>
                @endif
              </div>

              <!-- Détails -->
              <div class="order-details">
                <div class="order-meta">
                  <span class="order-id">#CMD-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>
                  <span class="order-date"><i class="far fa-calendar-alt"></i> {{ $c->created_at->format('d M Y à H:i') }}</span>
                </div>

                <h3 class="order-service-title">{{ $prestation ? $prestation->title : 'Commande Multi-Services' }}</h3>

                <div class="order-provider">
                  <div class="provider-avatar-micro">
                    @if($prest && $prest->avatar)
                      <img src="{{ asset('storage/'.$prest->avatar) }}" alt="">
                    @else
                      {{ $prest ? $prest->getInitials() : 'P' }}
                    @endif
                  </div>
                  <span>{{ $prest ? \Str::limit($prest->name, 15) : 'Prestataire' }}</span>
                  @if($c->address)
                    <span style="color:#444;margin:0 0.5rem;">•</span>
                    <span style="color:#aaa;"><i class="fas fa-map-marker-alt"></i> {{ $c->address }}</span>
                  @endif
                </div>
              </div>

              <!-- Zone statut + actions -->
              <div class="order-actions-zone">
                <!-- Badge statut -->
                <div class="order-status-badge" style="background:{{ $st['bg'] }};border-color:{{ $st['border'] }};color:{{ $st['color'] }};">
                  <i class="fas {{ $st['icon'] }}"></i>
                  <span>{{ $st['label'] }}</span>
                </div>
                <!-- Description statut -->
                <div class="order-status-desc" style="color:{{ $st['color'] }};">{{ $st['desc'] }}</div>

                <!-- Prix -->
                <div class="order-price">
                  {{ number_format($amount, 0, ',', ' ') }} <span style="font-size:1rem;color:#555;">FCFA</span>
                </div>

                <div style="display:flex;gap:0.5rem;align-items:center;">
                  <a href="{{ route('client.receipt', $c->id) }}" target="_blank" class="btn-icon" style="background:rgba(255,255,255,0.1);border-color:#444;" title="Télécharger le reçu">
                    <i class="fas fa-file-invoice"></i>
                  </a>
                  @if(in_array($c->status, ['accepted','confirmed']))
                    <a href="{{ route('client.checkout', $prestation->id ?? 0) }}" class="btn-pay-now">
                      <i class="fas fa-credit-card"></i> Payer
                    </a>
                  @endif
                  <a href="{{ route('client.messages', ['with' => $prest->id ?? 0]) }}" class="btn-icon msg-icon" title="Contacter le prestataire">
                    <i class="fas fa-comment-dots"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          @endforeach

          <!-- Pagination -->
          @if($orders->hasPages())
          <div style="margin-top:2rem;">
            {{ $orders->links() }}
          </div>
          @endif
        @endif
      </div>
    </div>

  </div><!-- /orders-foreground -->
</div>

<style>
.orders-container { position:relative; min-height:80vh; }
#orders-3d-canvas { position:fixed; top:0; left:0; width:100%; height:100%; z-index:0; pointer-events:none; opacity:0.45; }
.orders-foreground { position:relative; z-index:2; padding:2rem; }
.orders-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; background:rgba(10,10,10,0.82); backdrop-filter:blur(24px); padding:2rem; border-radius:24px; border:1px solid rgba(255,255,255,0.07); box-shadow:0 20px 60px rgba(0,0,0,0.5); }
.orders-title { font-family:'Syne','Space Grotesk',sans-serif; font-size:3rem; font-weight:900; background:linear-gradient(135deg,#fff,#FFD700); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin:0; }
.orders-stats { display:flex; gap:1rem; }
.stat-box { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:1rem 1.5rem; text-align:center; min-width:110px; }
.stat-value { display:block; font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:#fff; }
.stat-label { font-size:0.75rem; color:#aaa; text-transform:uppercase; letter-spacing:1px; }
.orders-section-wrapper { background:rgba(8,8,8,0.85); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.07); border-radius:28px; padding:2rem; box-shadow:0 30px 80px rgba(0,0,0,0.6); margin-top:2rem; }
.section-divider { display:flex; align-items:center; gap:1.5rem; margin-bottom:2rem; }
.section-divider-line { flex:1; height:1px; background:linear-gradient(90deg,transparent,rgba(255,215,0,0.3),transparent); }
.section-divider-label { font-family:'Syne',sans-serif; font-size:0.95rem; font-weight:700; color:#FFD700; white-space:nowrap; display:flex; align-items:center; gap:0.5rem; padding:0.45rem 1.2rem; background:rgba(255,215,0,0.07); border:1px solid rgba(255,215,0,0.22); border-radius:999px; }
.orders-list { display:flex; flex-direction:column; gap:1.25rem; }
.order-card { position:relative; background:rgba(18,18,18,0.95); border-radius:18px; border:1px solid rgba(255,255,255,0.07); overflow:hidden; transition:transform 0.3s,box-shadow 0.3s,border-color 0.3s; }
.order-card:hover { transform:translateY(-4px); border-color:rgba(255,255,255,0.14); box-shadow:0 20px 50px rgba(0,0,0,0.5); }
.order-status-bar { position:absolute; top:0; left:0; bottom:0; width:5px; }
.order-content { display:flex; padding:1.5rem 1.5rem 1.5rem 2rem; gap:1.5rem; align-items:center; }
.order-image { width:130px; height:100px; border-radius:12px; overflow:hidden; flex-shrink:0; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.03); }
.order-image img { width:100%; height:100%; object-fit:cover; transition:transform 0.5s; }
.order-card:hover .order-image img { transform:scale(1.08); }
.order-details { flex:1; display:flex; flex-direction:column; justify-content:center; gap:0.4rem; }
.order-meta { display:flex; gap:1rem; font-size:0.8rem; font-family:monospace; color:#aaa; flex-wrap:wrap; }
.order-id { color:#FFD700; background:rgba(255,215,0,0.1); padding:2px 10px; border-radius:6px; border:1px solid rgba(255,215,0,0.2); font-weight:700; }
.order-date { color:#666; font-size:0.78rem; }
.order-service-title { font-size:1.25rem; font-weight:700; color:#fff; margin:0; line-height:1.3; }
.order-provider { display:flex; align-items:center; font-size:0.82rem; color:#ccc; flex-wrap:wrap; gap:0.25rem; }
.provider-avatar-micro { width:22px; height:22px; border-radius:50%; background:linear-gradient(135deg,#FFD700,#F77F00); display:flex; align-items:center; justify-content:center; color:#000; font-weight:800; font-size:0.55rem; overflow:hidden; margin-right:0.4rem; flex-shrink:0; }
.provider-avatar-micro img { width:100%; height:100%; object-fit:cover; }
.order-actions-zone { display:flex; flex-direction:column; align-items:flex-end; justify-content:space-between; gap:0.6rem; border-left:1px solid rgba(255,255,255,0.06); padding-left:1.5rem; min-width:200px; align-self:stretch; }
.order-status-badge { display:inline-flex; align-items:center; gap:0.5rem; padding:0.45rem 1rem; border-radius:99px; border:1.5px solid; font-size:0.82rem; font-weight:800; letter-spacing:0.04em; text-transform:uppercase; transition:box-shadow 0.3s; }
.order-card:hover .order-status-badge { box-shadow:0 0 18px currentColor; }
.order-status-desc { font-size:0.7rem; text-align:right; opacity:0.8; line-height:1.4; max-width:185px; font-style:italic; }
.order-price { font-family:'Syne',sans-serif; font-size:1.55rem; font-weight:800; color:#fff; text-align:right; white-space:nowrap; }
.btn-pay-now { display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 0.9rem; background:linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff; border-radius:10px; font-size:0.78rem; font-weight:700; text-decoration:none; transition:0.2s; white-space:nowrap; }
.btn-pay-now:hover { transform:scale(1.05); box-shadow:0 4px 20px rgba(59,130,246,0.4); color:#fff; }
.btn-icon { width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:0.2s; text-decoration:none; flex-shrink:0; }
.btn-icon:hover { background:#FFD700; color:#000; transform:scale(1.1); }
.msg-icon:hover { background:#3b82f6 !important; color:#fff !important; }
@media(max-width:768px){
  .orders-header{flex-direction:column;align-items:flex-start;gap:1.5rem;}
  .order-content{flex-direction:column;align-items:flex-start;}
  .order-actions-zone{border-left:none;border-top:1px solid rgba(255,255,255,0.06);padding-left:0;padding-top:1rem;align-items:flex-start;min-width:auto;width:100%;}
  .order-image{width:100%;height:180px;}
}
</style>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
document.addEventListener('turbo:load', () => {
    if(typeof gsap !== 'undefined') {
        gsap.from(".gs-reveal", { y:40, opacity:0, duration:1, stagger:0.2, ease:"power3.out" });
        document.querySelectorAll('.gs-item').forEach(item => {
            let delay = parseFloat(item.style.getPropertyValue('--delay')) || 0;
            gsap.from(item, { x:-50, opacity:0, duration:0.8, delay:delay+0.4, ease:"back.out(1.2)" });
        });
    }

    // THREE.JS PARTICLE TUNNEL
    setTimeout(() => {
        const canvas = document.getElementById('orders-3d-canvas');
        if(!canvas || typeof THREE === 'undefined') return;
        const scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x0a0a0a, 0.002);
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth/window.innerHeight, 1, 1000);
        camera.position.z = 100;
        const renderer = new THREE.WebGLRenderer({ canvas, antialias:true, alpha:true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(window.devicePixelRatio);

        const geometry = new THREE.BufferGeometry();
        const count = 3000;
        const positions = new Float32Array(count * 3);
        const colors = new Float32Array(count * 3);
        const palette = [new THREE.Color(0xFFD700), new THREE.Color(0xF77F00), new THREE.Color(0x3b82f6)];
        for(let i=0; i<count*3; i+=3) {
            const radius = 20 + Math.random()*40;
            const theta = Math.random()*Math.PI*2;
            positions[i] = Math.cos(theta)*radius;
            positions[i+1] = (Math.random()-0.5)*800;
            positions[i+2] = Math.sin(theta)*radius;
            const c = palette[Math.floor(Math.random()*3)];
            colors[i]=c.r; colors[i+1]=c.g; colors[i+2]=c.b;
        }
        geometry.setAttribute('position', new THREE.BufferAttribute(positions,3));
        geometry.setAttribute('color', new THREE.BufferAttribute(colors,3));
        const particles = new THREE.Points(geometry, new THREE.PointsMaterial({size:1.5,vertexColors:true,transparent:true,opacity:0.7,blending:THREE.AdditiveBlending}));
        particles.rotation.x = Math.PI/2;
        scene.add(particles);

        let mouseX=0, mouseY=0, spd=0.5;
        document.addEventListener('mousemove', e => {
            mouseX=(e.clientX-window.innerWidth/2)*0.05;
            mouseY=(e.clientY-window.innerHeight/2)*0.05;
            spd=0.5+(e.clientY/window.innerHeight)*2;
        });

        const animate = () => {
            requestAnimationFrame(animate);
            const pos = particles.geometry.attributes.position.array;
            for(let i=1;i<count*3;i+=3){pos[i]-=spd;if(pos[i]<-400)pos[i]=400;}
            particles.geometry.attributes.position.needsUpdate=true;
            camera.position.x+=(mouseX-camera.position.x)*0.02;
            camera.position.y+=(-mouseY-camera.position.y)*0.02;
            camera.lookAt(scene.position);
            renderer.render(scene,camera);
        };
        animate();

        window.addEventListener('resize',()=>{camera.aspect=window.innerWidth/window.innerHeight;camera.updateProjectionMatrix();renderer.setSize(window.innerWidth,window.innerHeight);});
    }, 200);
});
</script>
@endsection
