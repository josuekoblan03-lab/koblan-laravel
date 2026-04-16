@extends('layouts.dashboard')

@section('content')
<style>
body { background: #05050a !important; }
.dash-layout { background: transparent !important; }
.dash-main { background: transparent !important; }
.dash-content { padding: 0 !important; background: transparent !important; }
#fav-canvas { position: fixed !important; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; pointer-events: none; }
.favorites-wrapper { position: relative; z-index: 10; width: 100%; min-height: 100vh; color: #fff; }
.fav-container { position: relative; z-index: 10; width: 100%; max-width: 1400px; margin: 0 auto; padding: 3rem 2rem 5rem 2rem; }
.fav-hero { text-align: center; padding: 4rem 1rem 3rem 1rem; margin-bottom: 2rem; }
.fav-hero h1 { font-family: var(--font-display); font-size: 4rem; font-weight: 900; background: linear-gradient(135deg, #FFD700 0%, #ff8c00 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem; line-height: 1.1; }
.fav-hero p { font-size: 1.2rem; color: rgba(255,255,255,0.6); max-width: 600px; margin: 0 auto; }
.fav-mega-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2.5rem; width: 100%; }
.premium-fav-card { background: rgba(10, 10, 15, 0.75); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,215,0,0.1); border-radius: 20px; overflow: hidden; position: relative; transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1); display: flex; flex-direction: column; }
.premium-fav-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 20px 60px rgba(0,0,0,0.7), 0 0 30px rgba(255,215,0,0.08); border-color: rgba(255,215,0,0.3); }
.p-fav-imgbox { position: relative; width: 100%; height: 220px; overflow: hidden; }
.p-fav-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; opacity: 0.9; }
.premium-fav-card:hover .p-fav-img { transform: scale(1.08); opacity: 1; }
.p-fav-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 55%); }
.heart-bubble { position: absolute; top: 1rem; right: 1rem; width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; color: #ef4444; font-size: 1.1rem; cursor: pointer; border: 1px solid rgba(239,68,68,0.3); transition: 0.3s; z-index: 20; pointer-events: auto; }
.heart-bubble:hover { background: #ef4444; color: #fff; transform: scale(1.2); border-color: #ef4444; box-shadow: 0 0 20px rgba(239,68,68,0.5); }
.p-fav-cat { position: absolute; top: 1rem; left: 1rem; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); color: #FFD700; font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.8rem; border-radius: 99px; z-index: 5; }
.p-fav-info { padding: 1.5rem; display: flex; flex-direction: column; flex: 1; position: relative; z-index: 10; }
.p-fav-author { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem; }
.author-box { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; }
.author-name { font-size: 0.85rem; color: rgba(255,255,255,0.6); }
.author-rating { font-size: 0.85rem; color: #FFD700; font-weight: 700; display: flex; align-items: center; gap: 0.25rem; }
.p-fav-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 1.5rem; line-height: 1.4; color: #fff; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.p-fav-bottom { margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); }
.p-fav-price { font-family: var(--font-display); font-size: 1.4rem; font-weight: 800; color: #FFD700; }
.btn-reserver { background: linear-gradient(135deg, #FFD700, #ff8c00); color: #000; padding: 0.65rem 1.2rem; border-radius: 12px; font-weight: 700; text-decoration: none; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.3s; z-index: 20; }
.btn-reserver:hover { box-shadow: 0 6px 20px rgba(255,215,0,0.4); transform: translateY(-2px); color: #000; }
.empty-state { text-align: center; padding: 6rem 2rem; background: rgba(5,5,10,0.8); backdrop-filter: blur(20px); border: 1px dashed rgba(255,255,255,0.1); border-radius: 24px; margin-top: 2rem; }
.fav-dashboard-footer { display: flex; justify-content: space-between; align-items: center; padding: 2rem 0; margin-top: 4rem; border-top: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); font-size: 0.85rem; position: relative; z-index: 10; }
.fav-dashboard-footer a { color: #FFD700; text-decoration: none; }
.dash-sidebar { position: fixed !important; z-index: 100 !important; background: rgba(5, 5, 10, 0.95) !important; backdrop-filter: blur(30px) !important; }
.dash-topbar { background: rgba(5, 5, 10, 0.85) !important; backdrop-filter: blur(20px) !important; }
</style>

<!-- Canvas 3D -->
<canvas id="fav-canvas"></canvas>

<div class="favorites-wrapper">
    <div class="fav-container">

        <!-- HERO -->
        <div class="fav-hero gs-reveal">
            <h1>✦ Vos Pépites</h1>
            <p>Votre collection de services et artisans favoris, gardés précieusement.</p>
        </div>

        @if($favorites->isEmpty())
            <div class="empty-state gs-reveal">
                <i class="fas fa-gem fa-4x" style="color:rgba(255,215,0,0.3); margin-bottom:1.5rem;display:block;"></i>
                <h2 style="font-size:2rem; font-weight:800; margin-bottom:1rem; color:#fff;">Votre coffre-fort est vide !</h2>
                <p style="color:rgba(255,255,255,0.5); margin-bottom:2rem; font-size:1.1rem;">Parcourez notre catalogue et sauvegardez les prestations d'exception.</p>
                <a href="{{ route('services.index') }}" class="btn btn-gold btn-lg" style="padding:1rem 2.5rem; font-size:1.1rem;">Visiter le Catalogue</a>
            </div>
        @else

            <div class="fav-mega-grid">
                @foreach($favorites as $p)
                <div class="premium-fav-card gs-card">
                    <div class="p-fav-imgbox">
                        <a href="{{ route('services.show', $p->id) }}" style="display:block; width:100%; height:100%;">
                            <img src="{{ $p->getImageUrl() }}" class="p-fav-img" alt="{{ $p->title }}">
                            <div class="p-fav-overlay"></div>
                        </a>
                        <div class="p-fav-cat">
                            <i class="{{ $p->serviceType->category->icon ?? 'fas fa-briefcase' }}"></i>
                            {{ $p->serviceType->category->name ?? 'Service' }}
                        </div>
                        <button class="heart-bubble" onclick="removeFromFavorites(event, {{ $p->id }}, this)">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <div class="p-fav-info">
                        <div class="p-fav-author">
                            <a href="{{ route('provider.profile', $p->user_id) }}" class="author-box">
                                <div style="width:28px;height:28px;border-radius:50%;background:#333;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:800;color:#fff;">
                                    {{ $p->user->getInitials() }}
                                </div>
                                <span class="author-name">{{ $p->user->name }}</span>
                            </a>
                            <div class="author-rating"><i class="fas fa-star"></i> {{ number_format($p->user->rating_avg ?? 0, 1) }}</div>
                        </div>

                        <a href="{{ route('services.show', $p->id) }}" style="text-decoration:none; color:inherit;">
                            <h3 class="p-fav-title">{{ $p->title }}</h3>
                        </a>

                        <div class="p-fav-bottom">
                            <span class="p-fav-price">{{ number_format($p->price, 0, ',', ' ') }} F</span>
                            <a href="{{ route('client.checkout', $p->id) }}" class="btn-reserver">
                                Commander <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        @endif

        <!-- Recommandations -->
        @if($recommendations->isNotEmpty())
        <div class="fav-hero gs-reveal" style="margin-top:6rem; padding-bottom:1rem;">
            <h2 style="font-size:2.5rem; font-weight:800; color:#fff;">Tentations Supplémentaires</h2>
            <p>Ces services pourraient aussi vous intéresser.</p>
        </div>

        <div class="fav-mega-grid">
            @foreach($recommendations as $rec)
            <div class="premium-fav-card gs-card" style="opacity:0.75;">
                <div class="p-fav-imgbox" style="height:160px;">
                    <a href="{{ route('services.show', $rec->id) }}" style="display:block; width:100%; height:100%;">
                        <img src="{{ $rec->getImageUrl() }}" class="p-fav-img" alt="{{ $rec->title }}" style="filter:grayscale(50%);">
                        <div class="p-fav-overlay"></div>
                    </a>
                    <div class="p-fav-cat">{{ $rec->serviceType->category->name ?? 'Service' }}</div>
                </div>
                <div class="p-fav-info" style="padding:1rem;">
                    <h3 class="p-fav-title" style="font-size:1rem; margin-bottom:0.5rem;">{{ $rec->title }}</h3>
                    <div class="p-fav-bottom" style="padding-top:0.5rem;">
                        <span class="p-fav-price" style="font-size:1.1rem;">{{ number_format($rec->price, 0, ',', ' ') }} F</span>
                        <a href="{{ route('services.show', $rec->id) }}" style="color:#FFD700; font-weight:700; text-decoration:none;"><i class="fas fa-eye"></i> Voir</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="fav-dashboard-footer">
            <div>© {{ date('Y') }} KOBLAN. Expérience 3D Premium.</div>
            <div>Besoin d'aide ? <a href="{{ route('contact') }}">Support Client</a></div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
(function() {
    function launchFavGalaxy() {
        if (typeof THREE === 'undefined') { setTimeout(launchFavGalaxy, 150); return; }
        const canvas = document.getElementById('fav-canvas');
        if (!canvas) return;
        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setClearColor(0x000000, 0);
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 2000);
        camera.position.set(0, 0, 25);

        // SPIRALE GALACTIQUE (15 000 particules)
        const galaxyCount = 15000;
        const galaxyPos = new Float32Array(galaxyCount * 3);
        const galaxyColors = new Float32Array(galaxyCount * 3);
        const colorCenter = new THREE.Color(0xffffff);
        const colorGold = new THREE.Color(0xFFD700);
        const colorOrange = new THREE.Color(0xF77F00);
        const colorBlue = new THREE.Color(0x4488ff);

        for (let i = 0; i < galaxyCount; i++) {
            const arm = Math.floor(Math.random() * 3);
            const armAngle = (arm / 3) * Math.PI * 2;
            const r = Math.pow(Math.random(), 0.7) * 50;
            const spinAngle = r * 0.4;
            const branchAngle = armAngle + spinAngle;
            const rand = (Math.random() - 0.5) * Math.pow(Math.random(), 3) * 6;
            const randY = (Math.random() - 0.5) * Math.pow(Math.random(), 3) * 2;
            galaxyPos[i*3] = Math.cos(branchAngle)*r + rand;
            galaxyPos[i*3+1] = randY;
            galaxyPos[i*3+2] = Math.sin(branchAngle)*r + rand - 15;
            const mixT = r/50;
            let c;
            if (mixT < 0.3) c = colorCenter.clone().lerp(colorGold, mixT/0.3);
            else if (mixT < 0.7) c = colorGold.clone().lerp(colorOrange, (mixT-0.3)/0.4);
            else c = colorOrange.clone().lerp(colorBlue, (mixT-0.7)/0.3);
            galaxyColors[i*3] = c.r; galaxyColors[i*3+1] = c.g; galaxyColors[i*3+2] = c.b;
        }
        const galaxyGeo = new THREE.BufferGeometry();
        galaxyGeo.setAttribute('position', new THREE.BufferAttribute(galaxyPos, 3));
        galaxyGeo.setAttribute('color', new THREE.BufferAttribute(galaxyColors, 3));
        const galaxyMat = new THREE.PointsMaterial({ size: 0.08, sizeAttenuation: true, vertexColors: true, transparent: true, opacity: 0.75, depthWrite: false, blending: THREE.AdditiveBlending });
        const galaxy = new THREE.Points(galaxyGeo, galaxyMat);
        galaxy.rotation.x = Math.PI * 0.2;
        scene.add(galaxy);

        // Géométries flottantes
        const floaters = [];
        const geoTypes = [
            () => new THREE.IcosahedronGeometry(0.6, 1),
            () => new THREE.OctahedronGeometry(0.5),
            () => new THREE.TorusKnotGeometry(0.4, 0.12, 80, 12),
            () => new THREE.TetrahedronGeometry(0.5),
            () => new THREE.TorusGeometry(0.5, 0.15, 16, 40),
        ];
        for (let i = 0; i < 18; i++) {
            const geoFn = geoTypes[i % geoTypes.length];
            const geo = geoFn();
            const isWireframe = Math.random() > 0.4;
            const mat = new THREE.MeshStandardMaterial({ color: Math.random()>0.6?0xFFD700:(Math.random()>0.5?0xF77F00:0x4466FF), metalness:0.9, roughness:0.1, wireframe:isWireframe, transparent:true, opacity:isWireframe?0.12:0.35 });
            const mesh = new THREE.Mesh(geo, mat);
            mesh.position.set((Math.random()-0.5)*50, (Math.random()-0.5)*35, (Math.random()-0.5)*20-5);
            mesh.userData.speed = { rx:(Math.random()-0.5)*0.008, ry:(Math.random()-0.5)*0.012, floatOffset:Math.random()*Math.PI*2, floatSpeed:Math.random()*0.008+0.004 };
            scene.add(mesh); floaters.push(mesh);
        }

        // Lumières
        scene.add(new THREE.AmbientLight(0xffffff, 0.2));
        const goldLight = new THREE.PointLight(0xFFD700, 3, 60); goldLight.position.set(10, 10, 5); scene.add(goldLight);
        const orangeLight = new THREE.PointLight(0xF77F00, 2, 40); orangeLight.position.set(-15, -8, 0); scene.add(orangeLight);
        scene.add(new THREE.PointLight(0x4466FF, 1.5, 50));

        let mouseX = 0, mouseY = 0, targetMX = 0, targetMY = 0, scrollY = 0, t = 0;
        document.addEventListener('mousemove', (e) => { targetMX = (e.clientX/window.innerWidth-0.5)*2; targetMY = (e.clientY/window.innerHeight-0.5)*2; }, { passive:true });
        window.addEventListener('scroll', () => { scrollY = window.scrollY; }, { passive:true });
        window.addEventListener('resize', () => { camera.aspect = window.innerWidth/window.innerHeight; camera.updateProjectionMatrix(); renderer.setSize(window.innerWidth, window.innerHeight); });

        function animate() {
            requestAnimationFrame(animate); t += 0.005;
            mouseX += (targetMX - mouseX)*0.04; mouseY += (targetMY - mouseY)*0.04;
            galaxy.rotation.y = t*0.04 + scrollY*0.0002; galaxy.rotation.z = t*0.01;
            camera.position.x += (mouseX*3 - camera.position.x)*0.03;
            camera.position.y += (-mouseY*2 - camera.position.y + scrollY*0.005)*0.03;
            camera.lookAt(0, 0, -10);
            floaters.forEach((f) => { f.rotation.x += f.userData.speed.rx; f.rotation.y += f.userData.speed.ry; f.position.y += Math.sin(t*f.userData.speed.floatSpeed + f.userData.speed.floatOffset)*0.012; });
            goldLight.position.x = Math.cos(t*0.3)*15; goldLight.position.z = Math.sin(t*0.3)*10;
            goldLight.intensity = 2.5 + Math.sin(t*1.2)*0.8;
            orangeLight.position.x = Math.sin(t*0.2)*20; orangeLight.position.y = Math.cos(t*0.25)*10;
            renderer.render(scene, camera);
        }
        animate();
    }
    if (document.readyState==='loading') document.addEventListener('turbo:load', launchFavGalaxy); else launchFavGalaxy();
})();

document.addEventListener('turbo:load', () => {
    if (typeof gsap === 'undefined') return;
    const tl = gsap.timeline();
    tl.fromTo('.gs-reveal', { y:60, opacity:0 }, { y:0, opacity:1, duration:1.2, ease:'power3.out', stagger:0.15 });
    tl.fromTo('.gs-card', { y:80, opacity:0, scale:0.9 }, { y:0, opacity:1, scale:1, duration:0.7, ease:'back.out(1.4)', stagger:0.08 }, '-=0.7');
});

window.removeFromFavorites = async function(e, prestId, btn) {
    e.preventDefault(); e.stopPropagation();
    try {
        await fetch('{{ route("client.favorites.toggle", ":id") }}'.replace(':id', prestId), {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const card = btn.closest('.premium-fav-card');
        if (!card) return;
        if (typeof gsap !== 'undefined') {
            gsap.to(card, { scale:0.8, opacity:0, y:-20, duration:0.4, ease:'power2.in', onComplete: () => { card.remove(); if (!document.querySelector('.premium-fav-card')) location.reload(); } });
        } else { card.remove(); if (!document.querySelector('.premium-fav-card')) location.reload(); }
    } catch(err) { console.error('Erreur suppression favori :', err); }
};
</script>
@endsection
