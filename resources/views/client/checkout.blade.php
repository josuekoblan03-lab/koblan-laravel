@extends('layouts.app')

@section('title', 'Validation de Commande — KOBLAN')

@section('content')
<div class="checkout-container">
  <div class="checkout-content gs-reveal">
      <div class="checkout-header">
         <a href="{{ route('services.show', $service->id) }}" class="btn-back"><i class="fas fa-arrow-left"></i> Retour au service</a>
         <h1 class="checkout-title">Validation de Commande</h1>
         <p style="color:var(--gray-400); margin-top:0.5rem;">Configurez votre demande locale pour le prestataire.</p>
      </div>

      <div class="checkout-grid">
          
          <!-- LEFT COLUMN: 3D VISUAL & RECAP -->
          <div class="checkout-left-col">
              
              <!-- 3D VISIBILITY BOX -->
              <div class="checkout-3d-box">
                  <canvas id="checkout-3d-canvas"></canvas>
                  <div class="secure-label">
                     <i class="fas fa-shield-alt"></i> Transmission Sécurisée KOBLAN
                  </div>
              </div>

              <!-- RÉCAPITULATIF COMMANDE -->
              <div class="checkout-card">
                 <div class="glow-border"></div>
                 <h2 class="card-title">Détails de la prestation</h2>
                 
                 <div class="service-preview">
                    <div class="service-image-box">
                       <img src="{{ $service->getImageUrl() }}" alt="Service">
                    </div>
                    <div class="service-info-box">
                      <div class="cat-tag"><i class="{{ $service->serviceType->category->icon ?? 'fas fa-box' }}"></i> {{ $service->serviceType->category->name ?? 'Service' }}</div>
                      <h3>{{ $service->title }}</h3>
                      <p>Proposé par : <strong>{{ $service->user->name }}</strong></p>
                    </div>
                 </div>

                 <div class="total-block">
                    <div class="line">
                        <span>Prix du service</span>
                        <span>{{ number_format($service->price, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <hr>
                    <div class="line total">
                        <span>Total estimé</span>
                        <span class="total-price">{{ number_format($service->price, 0, ',', ' ') }} FCFA</span>
                    </div>
                 </div>
              </div>
          </div>

          <!-- RIGHT COLUMN: FORMULAIRE VALIDATION -->
          <div class="checkout-card form-box">
              <h2 class="card-title">Informations d'intervention</h2>
              
              @if ($errors->any())
                  <div class="alert alert-danger" style="color:#ef4444; background:rgba(239,68,68,0.1); padding:1rem; border-radius:8px; margin-bottom:1rem;">
                      <ul style="margin:0; padding-left:1rem;">
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif

              <form action="{{ route('client.bookings.store', $service->id) }}" method="POST" id="checkoutForm">
                  @csrf
                  
                  <div class="form-group">
                      <label class="form-label">Date souhaitée <span style="color:#ef4444;">*</span></label>
                      <input type="date" name="date_intervention" class="field-input" required min="{{ date('Y-m-d') }}">
                  </div>

                  <div class="form-group">
                      <label class="form-label">Location exacte (Quartier) <span style="color:#ef4444;">*</span></label>
                      <div class="custom-select-wrapper">
                          <i class="fas fa-map-marker-alt select-icon"></i>
                          <select name="neighborhood_id" class="field-input select-with-icon" required>
                              <option value="">Où doit se dérouler la prestation ?</option>
                              @foreach($neighborhoods as $q)
                                <option value="{{ $q->id }}">{{ $q->name }} ({{ $q->city->name }})</option>
                              @endforeach
                          </select>
                      </div>
                  </div>

                  <div class="form-group">
                      <label class="form-label">Instructions pour le professionnel</label>
                      <textarea name="instructions" class="field-input" rows="5" placeholder="Ex: Bâtiment A, porte 4, interphone, apporter un escabeau..."></textarea>
                  </div>

                  <button type="submit" class="btn-checkout-submit">
                      <span class="btn-bg"></span>
                      <strong style="position:relative; z-index:2;"><i class="fas fa-paper-plane"></i> Envoyer la demande au prestataire</strong>
                  </button>
                  <p class="disclaimer">
                      En envoyant cette demande, la commande sera placée En Attente. 
                      Vous ne paierez qu'une fois la demande acceptée par le prestataire.
                  </p>
              </form>
          </div>
      </div>
  </div>
</div>

<style>
.checkout-container { padding: 4rem 2rem; padding-top: 8rem; }
.checkout-content { max-width: 1200px; margin: 0 auto; }
.checkout-header { text-align: center; margin-bottom: 3rem; }
.btn-back { display: inline-block; color: var(--gray-400); margin-bottom: 1rem; transition: 0.2s; font-weight: 600; text-decoration: none; }
.btn-back:hover { color: var(--gold-300); transform: translateX(-5px); }
.checkout-title { font-family: var(--font-display); font-size: 3rem; font-weight: 900; background: linear-gradient(135deg, #fff, var(--gold-300)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; }

.checkout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start; }
.checkout-left-col { display: flex; flex-direction: column; gap: 2rem; }

.checkout-3d-box {
    position: relative;
    width: 100%;
    height: 300px;
    background: radial-gradient(circle at center, rgba(30,30,30,0.8), #0a0a0a);
    border: 1px solid rgba(255,215,0,0.2);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: inset 0 0 50px rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}
#checkout-3d-canvas { position: absolute; inset: 0; width: 100%; height: 100%; }
.secure-label { position: absolute; bottom: 15px; background: rgba(0,0,0,0.6); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; color: #10b981; border: 1px solid rgba(16,185,129,0.3); backdrop-filter: blur(5px); }

.checkout-card { position: relative; background: rgba(15,15,15,0.7); backdrop-filter: blur(30px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 2.5rem; box-shadow: 0 40px 100px rgba(0,0,0,0.6); overflow: hidden; }
.form-box { position: sticky; top: 100px; }

.glow-border { position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--gold-300), transparent); }
.card-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem; color: #fff; }

.service-preview { display: flex; gap: 1.5rem; align-items: center; margin-bottom: 2rem; }
.service-image-box { width: 100px; height: 80px; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
.service-image-box img { width: 100%; height: 100%; object-fit: cover; }
.cat-tag { font-size: 0.8rem; color: var(--gold-400); text-transform: uppercase; margin-bottom: 0.3rem; letter-spacing: 1px; font-weight: 700; }
.service-info-box h3 { font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem 0; }
.service-info-box p { font-size: 0.85rem; color: var(--gray-400); margin: 0; }

.total-block { padding: 1.5rem; background: rgba(255,215,0,0.03); border-radius: 16px; border: 1px solid rgba(255,215,0,0.2); }
.total-block .line { display: flex; justify-content: space-between; color: var(--gray-300); font-size: 1.1rem; }
.total-block hr { border: none; border-top: 1px dashed rgba(255,215,0,0.2); margin: 1rem 0; }
.total-block .line.total { font-weight: 900; color: #fff; align-items: center; }
.total-price { font-family: var(--font-display); font-size: 2rem; color: var(--gold-300); }

.form-group { margin-bottom: 1.5rem; }
.form-label { display: block; font-weight: 600; color: var(--gray-300); margin-bottom: 0.5rem; }
.field-input { width: 100%; padding: 1rem 1.2rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: #fff; font-family: inherit; font-size: 1rem; transition: 0.3s; }
.field-input:focus { outline: none; border-color: var(--gold-300); box-shadow: 0 0 15px rgba(255,215,0,0.2); background: rgba(0,0,0,0.5); }
.custom-select-wrapper { position: relative; }
.select-icon { position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: var(--gold-300); pointer-events: none; }
.select-with-icon { padding-left: 3rem; appearance: none; }

.btn-checkout-submit { width: 100%; padding: 1.2rem; background: transparent; border: 1px solid var(--gold-300); border-radius: 12px; color: var(--gold-300); font-family: var(--font-display); font-size: 1.1rem; cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; margin-top: 1rem; }
.btn-checkout-submit .btn-bg { position: absolute; inset: 0; background: var(--gold-300); transform: scaleX(0); transform-origin: left; transition: transform 0.4s cubic-bezier(0.86, 0, 0.07, 1); z-index: 1; }
.btn-checkout-submit:hover { color: #000; box-shadow: 0 10px 30px rgba(255,215,0,0.3); }
.btn-checkout-submit:hover .btn-bg { transform: scaleX(1); }
.disclaimer { text-align: center; font-size: 0.8rem; color: var(--gray-500); margin-top: 1rem; line-height: 1.5; }

@media(max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } .form-box { position: static; } }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('turbo:load', () => {
    if (typeof gsap !== 'undefined') {
        gsap.from(".gs-reveal", { y: 60, opacity: 0, duration: 1.2, ease: "power4.out" });
        gsap.from([".checkout-left-col", ".form-box"], { x: (i) => i === 0 ? -50 : 50, opacity: 0, duration: 1, delay: 0.3, stagger: 0.2, ease: "back.out(1)" });
    }

    const initCheckout3D = () => {
        const canvas = document.getElementById('checkout-3d-canvas');
        if(!canvas || typeof THREE === 'undefined') return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
        camera.position.z = 12;

        const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
        renderer.setSize(canvas.clientWidth, canvas.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);

        const dirLight = new THREE.DirectionalLight(0xFFD700, 2);
        dirLight.position.set(5, 5, 5);
        scene.add(dirLight);
        
        const blueLight = new THREE.PointLight(0x3b82f6, 3, 50);
        blueLight.position.set(-5, -5, 5);
        scene.add(blueLight);
        scene.add(new THREE.AmbientLight(0xffffff, 0.5));

        const group = new THREE.Group();
        
        // Un globe filaire futuriste central
        const geoSphere = new THREE.IcosahedronGeometry(2.5, 1);
        const matSphere = new THREE.MeshPhysicalMaterial({ color: 0x111111, metalness: 1, roughness: 0.2, wireframe: true });
        const sphere = new THREE.Mesh(geoSphere, matSphere);
        group.add(sphere);

        // Des orbites dorées
        const geoRing1 = new THREE.TorusGeometry(3.5, 0.05, 16, 100);
        const matRing = new THREE.MeshBasicMaterial({ color: 0xFFD700 });
        const ring1 = new THREE.Mesh(geoRing1, matRing);
        ring1.rotation.x = Math.PI / 2.5;
        group.add(ring1);

        const geoRing2 = new THREE.TorusGeometry(4.2, 0.02, 16, 100);
        const ring2 = new THREE.Mesh(geoRing2, matRing);
        ring2.rotation.y = Math.PI / 3;
        group.add(ring2);
        
        scene.add(group);

        let mouseX = 0; let mouseY = 0;
        document.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouseX = (e.clientX - rect.left - rect.width/2) * 0.01;
            mouseY = (e.clientY - rect.top - rect.height/2) * 0.01;
        });

        const animate = () => {
            requestAnimationFrame(animate);
            sphere.rotation.x += 0.005;
            sphere.rotation.y += 0.008;
            ring1.rotation.z += 0.01;
            ring2.rotation.z -= 0.005;
            
            group.rotation.y += (mouseX - group.rotation.y) * 0.05;
            group.rotation.x += (mouseY - group.rotation.x) * 0.05;
            
            renderer.render(scene, camera);
        };
        animate();

        window.addEventListener('resize', () => {
            camera.aspect = canvas.clientWidth / canvas.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
        });

        const form = document.getElementById('checkoutForm');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = form.querySelector('button');
            btn.querySelector('strong').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Transmission...';
            btn.querySelector('.btn-bg').style.transform = 'scaleX(1)';
            btn.querySelector('.btn-bg').style.background = '#10b981';
            btn.style.borderColor = '#10b981';
            
            if (typeof gsap !== 'undefined') {
                gsap.to(group.scale, { x: 0, y: 0, z: 0, duration: 1, ease: "back.in(1.5)" });
            }
            
            setTimeout(() => { form.submit(); }, 1200);
        });
    };
    
    setTimeout(initCheckout3D, 100);
});
</script>
@endsection
