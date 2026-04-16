@extends('layouts.app')

@section('content')

@php
$isProvider = $provider->isPrestataire();
$note = $provider->rating_avg ?? 0;
$nbAvis = $provider->total_reviews ?? 0;
$nbCommandesFinies = $provider->ordersAsPrestataire()->where('status', 'terminee')->count();
$fullName = $provider->name;
$bio = $provider->bio ?? 'Aucune biographie renseignée pour le moment.';
$prestations = $provider->prestations ?? collect();
$avis = $provider->reviewsReceived ?? collect();

function printStarsProfile($note) {
    $full = floor($note);
    $half = ($note - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '';
    for($i=0; $i<$full; $i++) $html .= '<i class="fas fa-star" style="color:#FFD700;"></i>';
    if ($half) $html .= '<i class="fas fa-star-half-alt" style="color:#FFD700;"></i>';
    for($i=0; $i<$empty; $i++) $html .= '<i class="far fa-star" style="color:#555;"></i>';
    return $html;
}
@endphp

<!-- Arrière-plan ludique 3D -->
<canvas id="prof3d" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:-1;opacity:0.2;"></canvas>

<div class="pf-container">
    <!-- En-tête du profil -->
    <header class="pf-head" style="margin-top:80px;">
        <div class="pf-head-cover"></div>
        <div class="pf-head-content">
            <div class="pf-av">
                @if($provider->avatar)
                    <img src="{{ asset('storage/'.$provider->avatar) }}" alt="{{ $fullName }}">
                @else
                    {{ $provider->getInitials() }}
                @endif
                @if($provider->is_verified)
                    <div class="pf-verif" title="Identité vérifiée"><i class="fas fa-check"></i></div>
                @endif
            </div>
            
            <div class="pf-info">
                <h1>{{ $fullName }}</h1>
                @if($provider->city)
                    <div style="color:#aaa; font-size:0.95rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem; text-shadow:0 1px 2px rgba(0,0,0,0.8);">
                        <i class="fas fa-map-marker-alt" style="color:#FFD700;"></i>
                        {{ $provider->city->name }}{{ $provider->neighborhood ? ', '.$provider->neighborhood->name : '' }}
                    </div>
                @endif
                @if($isProvider)
                    <div class="pf-badge"><i class="fas fa-shield-alt"></i> Prestataire PRO</div>
                    
                    <div class="pf-stats-row">
                        <div class="pf-stat">
                            <div class="pf-s-val">{!! printStarsProfile($note) !!} <span>{{ number_format($note, 1) }}</span></div>
                            <div class="pf-s-lib">{{ $nbAvis }} avis clients</div>
                        </div>
                        <div class="pf-stat">
                            <div class="pf-s-val"><i class="fas fa-check-double" style="color:#4ade80;"></i> {{ $nbCommandesFinies }}</div>
                            <div class="pf-s-lib">Missions terminées</div>
                        </div>
                    </div>
                @else
                    <div class="pf-badge" style="color:#bbb;background:rgba(255,255,255,0.05);"><i class="fas fa-user"></i> Client</div>
                @endif
            </div>
            
            <div class="pf-actions">
                @if($isProvider && auth()->check() && auth()->id() != $provider->id)
                    <a href="#" class="pf-btn pf-btn-main">
                        <i class="fas fa-comment"></i> Contacter
                    </a>
                @elseif(!auth()->check())
                    <a href="{{ route('login') }}" class="pf-btn pf-btn-main">
                        <i class="fas fa-sign-in-alt"></i> Connectez-vous pour contacter
                    </a>
                @endif
                <button class="pf-btn pf-btn-sec"><i class="fas fa-share-alt"></i></button>
            </div>
        </div>
    </header>

    <div class="pf-body">
        <!-- Colonne Gauche : Présentation -->
        <aside class="pf-side">
            <div class="pf-card pf-bio-card">
                <h3>À propos</h3>
                <p>{{ $bio }}</p>
                <div class="pf-mem-since">Membre depuis le {{ $provider->created_at->format('d/m/Y') }}</div>
            </div>
        </aside>

        <!-- Colonne Droite : Services & Avis -->
        <main class="pf-main">
            @if($isProvider)
                
                <!-- Services -->
                <section class="pf-section">
                    <h2>Prestations proposées ({{ $prestations->count() }})</h2>
                    
                    @if($prestations->isEmpty())
                        <div class="pf-empty"><i class="fas fa-box-open"></i> <p>Ce prestataire n'a pas encore de services actifs.</p></div>
                    @else
                        <div class="pf-grid">
                            @foreach($prestations as $p)
                                <a href="{{ route('services.show', $p->id) }}" class="pf-srv">
                                    <div class="pf-srv-img">
                                        <img src="{{ $p->getImageUrl() }}" alt="{{ $p->title }}" loading="lazy">
                                        <div class="pf-srv-cat">{{ $p->serviceType->category->name ?? 'Service' }}</div>
                                    </div>
                                    <div class="pf-srv-bot">
                                        <h4>{{ $p->title }}</h4>
                                        <div class="pf-srv-price">{{ number_format($p->price, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- Avis -->
                <section class="pf-section" style="margin-top:3rem;">
                    <h2>Avis des clients ({{ $avis->count() }})</h2>
                    
                    @if($avis->isEmpty())
                        <div class="pf-empty"><i class="far fa-star"></i> <p>Aucun avis pour le moment.</p></div>
                    @else
                        <div class="pf-rev-list">
                            @foreach($avis as $a)
                                <div class="pf-rev">
                                    <div class="pf-rev-av">
                                        @if($a->client && $a->client->avatar)
                                            <img src="{{ asset('storage/'.$a->client->avatar) }}" alt="">
                                        @else
                                            {{ $a->client ? $a->client->getInitials() : '?' }}
                                        @endif
                                    </div>
                                    <div class="pf-rev-content">
                                        <div class="pf-rev-head">
                                            <b>{{ $a->client->name ?? 'Client' }}</b>
                                            <span class="pf-rev-date">{{ $a->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="pf-rev-stars">{!! printStarsProfile($a->rating) !!}</div>
                                        <p class="pf-rev-txt">{{ $a->comment }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            @endif
        </main>
    </div>
</div>

<style>
/* ==============================
   PROFILE PAGE CSS
   ============================== */
.pf-container {
    max-width: 1100px; margin: 2rem auto; padding: 0 1rem;
    position: relative; z-index: 10;
}
.pf-head {
    background: rgba(9, 9, 16, 0.85); backdrop-filter: blur(25px);
    border-radius: 24px; border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    margin-bottom: 2rem;
}
.pf-head-cover {
    height: 160px; background: linear-gradient(135deg, rgba(255,215,0,0.2) 0%, rgba(247,127,0,0.5) 100%);
    position: relative;
}
.pf-head-content {
    display: flex; align-items: flex-end; padding: 0 2rem 2rem;
    position: relative; margin-top: -60px; gap: 2rem;
}
.pf-av {
    width: 140px; height: 140px; border-radius: 50%;
    background: linear-gradient(135deg, #FFD700, #F77F00);
    border: 6px solid #090910; position: relative;
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem; font-weight: 900; color: #000; overflow: hidden;
    flex-shrink: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
.pf-av img { width: 100%; height: 100%; object-fit: cover; }
.pf-verif {
    position: absolute; bottom: 8px; right: 8px; width: 32px; height: 32px;
    background: #22C55E; border: 4px solid #090910; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 0.9rem;
}
.pf-info { flex: 1; padding-bottom: 0.5rem; }
.pf-info h1 {
    font-size: 2.2rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}
.pf-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(255,215,0,0.15); color: #FFD700;
    padding: 0.4rem 0.9rem; border-radius: 99px; font-size: 0.8rem;
    font-weight: 700; margin-bottom: 1.25rem;
}
.pf-stats-row { display: flex; gap: 2.5rem; align-items: center; }
.pf-stat { display: flex; flex-direction: column; gap: 0.2rem; }
.pf-s-val { display: flex; align-items: center; gap: 0.5rem; font-size: 1.1rem; color: #fff; font-weight: 700; }
.pf-s-val span { font-size: 1.3rem; }
.pf-s-lib { font-size: 0.8rem; color: #888; text-transform: uppercase; letter-spacing: 0.05em; }
.pf-actions { display: flex; gap: 1rem; padding-bottom: 0.5rem; }
.pf-btn {
    padding: 0.9rem 1.6rem; border-radius: 14px; font-weight: 700; font-size: 0.95rem;
    display: inline-flex; align-items: center; gap: 0.65rem; cursor: pointer;
    transition: all 0.2s; text-decoration: none; border: none; outline: none;
}
.pf-btn-main {
    background: linear-gradient(135deg, #FFD700, #F77F00); color: #000;
    box-shadow: 0 8px 25px rgba(255,215,0,0.25);
}
.pf-btn-main:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(255,215,0,0.4); color:#000; }
.pf-btn-sec {
    background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1);
}
.pf-btn-sec:hover { background: rgba(255,255,255,0.1); }

.pf-body { display: flex; gap: 2rem; align-items: flex-start; }
.pf-side { width: 320px; flex-shrink: 0; }
.pf-main { flex: 1; min-width: 0; }

.pf-card {
    background: rgba(9, 9, 16, 0.85); backdrop-filter: blur(20px);
    border-radius: 20px; border: 1px solid rgba(255,255,255,0.06);
    padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}
.pf-card h3 { color: #fff; font-size: 1.1rem; margin: 0 0 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
.pf-card p { color: #aaa; font-size: 0.9rem; line-height: 1.6; margin: 0; }
.pf-mem-since { margin-top: 1.5rem; font-size: 0.8rem; color: #555; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); }

.pf-section h2 { color: #fff; font-size: 1.5rem; font-weight: 800; margin: 0 0 1.5rem; }
.pf-empty {
    text-align: center; padding: 4rem 2rem; background: rgba(9,9,16,0.6);
    border-radius: 20px; border: 1px dashed rgba(255,255,255,0.1); color: #666;
}
.pf-empty i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; display:block; }

/* Grid Services */
.pf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem; }
.pf-srv {
    display: block; text-decoration: none; background: rgba(15,15,25,0.8);
    border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);
    overflow: hidden; transition: all 0.25s;
}
.pf-srv:hover { transform: translateY(-6px); box-shadow: 0 15px 40px rgba(0,0,0,0.6); border-color: rgba(255,215,0,0.3); }
.pf-srv-img { height: 140px; position: relative; background: #000; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.pf-srv-img img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; transition: 0.3s; }
.pf-srv:hover .pf-srv-img img { opacity: 1; transform: scale(1.05); }
.pf-srv-noimg { font-size: 3rem; color: rgba(255,215,0,0.2); }
.pf-srv-cat {
    position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px);
    color: #FFD700; font-size: 0.7rem; font-weight: 700; padding: 0.3rem 0.6rem; border-radius: 6px;
}
.pf-srv-bot { padding: 1.25rem; }
.pf-srv-bot h4 { color: #f0f0f0; margin: 0 0 0.75rem; font-size: 0.95rem; font-weight: 600; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.pf-srv-price { font-size: 1.1rem; color: #fff; font-weight: 800; }

/* Liste Avis */
.pf-rev-list { display: flex; flex-direction: column; gap: 1rem; }
.pf-rev {
    display: flex; gap: 1.25rem; padding: 1.5rem; background: rgba(9, 9, 16, 0.85); backdrop-filter: blur(20px);
    border-radius: 18px; border: 1px solid rgba(255,255,255,0.05);
}
.pf-rev-av {
    width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
    background: #333; display: flex; align-items: center; justify-content: center;
    font-weight: 800; color: #bbb; overflow: hidden;
}
.pf-rev-av img { width: 100%; height: 100%; object-fit: cover; }
.pf-rev-content { flex: 1; }
.pf-rev-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem; }
.pf-rev-head b { color: #fff; font-size: 0.95rem; }
.pf-rev-date { color: #555; font-size: 0.75rem; }
.pf-rev-stars { margin-bottom: 0.6rem; font-size: 0.85rem; }
.pf-rev-txt { color: #ccc; font-size: 0.9rem; line-height: 1.6; margin: 0; }

@media(max-width: 900px) {
    .pf-head-content { flex-direction: column; align-items: center; text-align: center; }
    .pf-av { width: 110px; height: 110px; margin-top: -30px; }
    .pf-stats-row { justify-content: center; }
    .pf-body { flex-direction: column; }
    .pf-side { width: 100%; }
}
</style>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
/* 3D Background - Profile Ambient */
(function(){
    const canvas = document.getElementById('prof3d');
    if(!canvas||typeof THREE==='undefined') return;
    const scene = new THREE.Scene();
    const cam = new THREE.PerspectiveCamera(70, window.innerWidth/window.innerHeight, 0.1, 1000);
    cam.position.z = 50;

    const renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:true});
    renderer.setSize(window.innerWidth, window.innerHeight);

    const mat = new THREE.MeshBasicMaterial({ color: 0xFFD700, wireframe: true, transparent: true, opacity: 0.05 });
    const group = new THREE.Group();
    
    for(let i=0; i<3; i++){
        const geo = new THREE.TorusKnotGeometry(10 + i*5, 3, 100, 16);
        const mesh = new THREE.Mesh(geo, mat);
        mesh.rotation.x = Math.random() * Math.PI;
        mesh.rotation.y = Math.random() * Math.PI;
        group.add(mesh);
    }
    scene.add(group);

    function animate(){
        requestAnimationFrame(animate);
        group.rotation.x += 0.001;
        group.rotation.y += 0.002;
        renderer.render(scene, cam);
    }
    animate();

    window.addEventListener('resize', ()=>{
        cam.aspect = window.innerWidth/window.innerHeight;
        cam.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
})();
</script>
@endsection
