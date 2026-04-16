@extends('layouts.dashboard')

@section('content')
<!-- Vanta 3D Background Container -->
<div id="vanta-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0;"></div>

<div class="db-wrap" style="position: relative; z-index: 1;">
  <div class="db-glass-panel db-reveal" style="max-width:800px;margin:0 auto;width:100%; background: rgba(15, 15, 20, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 215, 0, 0.15); box-shadow: 0 15px 35px rgba(0,0,0,0.5);">
    <div class="db-panel-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
      <div class="db-panel-title" style="font-size: 1.8rem; font-weight: 800;"><i class="fas fa-user-tie text-gold-plain"></i> Mon Profil Prestataire</div>
    </div>

    @if(session('success'))
    <div style="background:rgba(16, 185, 129, 0.2);border:1px solid rgba(16, 185, 129, 0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">
      {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('prestataire.profile.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div style="display:flex;align-items:center;gap:2rem;margin-bottom:2rem;flex-wrap:wrap;">
        <div style="width:120px;height:120px;border-radius:50%;overflow:hidden;border:3px solid var(--gold-300);background:var(--dark-100);display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:bold;color:#000; box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);">
          @if ($user->avatar)
            <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            <div style="background:linear-gradient(135deg,var(--gold-300),#F77F00);width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
              {{ $user->getInitials() }}
            </div>
          @endif
        </div>
        <div style="flex:1;">
          <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Photo de profil professionnelle</label>
          <input type="file" name="avatar" class="form-control" accept="image/*" style="background:rgba(0,0,0,0.5);padding:0.5rem;border: 1px solid rgba(255,255,255,0.1);color:#fff;">
          <div style="font-size:0.75rem;color:var(--gray-400);margin-top:0.3rem;">Donnez confiance à vos clients avec une photo claire. (Max: 2MB)</div>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Nom d'affichage ou Entreprise <span style="color:#ef4444;">*</span></label>
        <input type="text" name="name" class="form-control" style="background:rgba(0,0,0,0.5);border: 1px solid rgba(255,255,255,0.1);color:#fff;" value="{{ old('name', $user->name) }}" required>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Email de contact <span style="font-size:0.7rem;color:var(--gray-500);">(Contactez l'admin pour modifier)</span></label>
        <input type="email" class="form-control" style="background:rgba(0,0,0,0.3);opacity:0.6;cursor:not-allowed;border: 1px solid rgba(255,255,255,0.05);color:#fff;" value="{{ $user->email }}" readonly>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Téléphone pro</label>
        <input type="text" name="phone" class="form-control" style="background:rgba(0,0,0,0.5);border: 1px solid rgba(255,255,255,0.1);color:#fff;" value="{{ old('phone', $user->phone) }}">
      </div>

      <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
        <div class="form-group" style="flex:1;min-width:200px;">
          <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Ville principale</label>
          <select name="city_id" class="form-control" style="background:rgba(0,0,0,0.5);border: 1px solid rgba(255,255,255,0.1);color:#fff;">
            <option value="">Sélectionnez...</option>
            @foreach($cities as $c)
              <option value="{{ $c->id }}" {{ old('city_id', $user->city_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="flex:1;min-width:200px;">
          <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Quartier / Zone</label>
          <select name="neighborhood_id" class="form-control" style="background:rgba(0,0,0,0.5);border: 1px solid rgba(255,255,255,0.1);color:#fff;">
            <option value="">Sélectionnez d'abord la ville...</option>
            @if($user->city)
              @foreach($user->city->neighborhoods as $n)
                <option value="{{ $n->id }}" {{ old('neighborhood_id', $user->neighborhood_id) == $n->id ? 'selected' : '' }}>{{ $n->name }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:2.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Bio / Présentation de votre expertise</label>
        <textarea name="bio" class="form-control" style="background:rgba(0,0,0,0.5);min-height:150px;border: 1px solid rgba(255,255,255,0.1);color:#fff;line-height:1.6;" placeholder="Décrivez votre parcours, vos diplômes et pourquoi les clients devraient vous faire confiance...">{{ old('bio', $user->bio) }}</textarea>
      </div>

      <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="db-btn-primary" style="padding:0.8rem 2rem; font-size:1.1rem; box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3); transition: all 0.3s ease;"><i class="fas fa-save" style="margin-right:0.5rem;"></i> Sauvegarder le profil</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.halo.min.js"></script>
<script>
document.addEventListener('turbo:load', function() {
    // Animation d'apparition
    if(typeof gsap !== 'undefined') {
        gsap.fromTo('.db-reveal',
            { y: 50, opacity: 0 },
            { y: 0, opacity: 1, duration: 1, ease: 'power3.out' }
        );
    }

    // Fond 3D HALO très classe et technologique
    var dashboardContent = document.querySelector('.dashboard-content');
    if (dashboardContent) {
        dashboardContent.style.position = 'relative'; 
    }
    
    try {
        VANTA.HALO({
          el: "#vanta-bg",
          mouseControls: true,
          touchControls: true,
          gyroControls: false,
          minHeight: 200.00,
          minWidth: 200.00,
          points: 14.00,
          maxDistance: 22.00,
          spacing: 16.00,
          baseColor: 0x0,
          backgroundColor: 0x0a0a0f,
          amplitudeFactor: 1.5,
          size: 1.2
        });
    } catch(e) { console.log('Vanta INIT err:', e); }
});
</script>
@endsection
