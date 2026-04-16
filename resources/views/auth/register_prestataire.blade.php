@extends('layouts.auth')

@section('content')
<div class="auth-box reveal" style="background:var(--glass-bg);backdrop-filter:blur(20px);padding:3rem;border-radius:var(--radius-lg);border:1px solid var(--glass-border);box-shadow:0 10px 50px rgba(0,0,0,0.5);">
  <div class="auth-header" style="text-align:center;margin-bottom:2rem;">
    <div style="width:60px;height:60px;background:var(--dark-100);border-radius:20px;margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;border:2px solid var(--gold-300);transform:rotate(45deg);box-shadow:0 0 20px rgba(255,215,0,0.2);">
      <i class="fas fa-briefcase" style="font-size:1.5rem;color:var(--gold-300);transform:rotate(-45deg);"></i>
    </div>
    <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:0.5rem;">
      Devenez <span class="text-gold">Prestataire</span> 🇨🇮
    </h1>
    <p style="color:var(--gray-400);font-size:0.85rem;">Développez votre activité avec KOBLAN.</p>
  </div>

  <div style="display:flex;align-items:center;margin-bottom:2rem;position:relative;" id="stepsIndicator">
    <div style="position:absolute;top:50%;left:0;right:0;height:2px;background:var(--dark-50);z-index:1;"></div>
    <div id="stepProgressLine" style="position:absolute;top:50%;left:0;width:0%;height:2px;background:var(--gold-300);z-index:2;transition:width 0.4s ease;"></div>
    
    <div class="step-dot active" data-step="1" style="width:24px;height:24px;border-radius:50%;background:var(--gold-300);color:var(--dark-500);display:flex;justify-content:center;align-items:center;font-size:0.7rem;font-weight:800;z-index:3;transition:all 0.4s;">1</div>
    <div style="flex:1;"></div>
    <div class="step-dot" data-step="2" style="width:24px;height:24px;border-radius:50%;background:var(--dark-100);border:2px solid var(--dark-50);color:var(--gray-500);display:flex;justify-content:center;align-items:center;font-size:0.7rem;font-weight:800;z-index:3;transition:all 0.4s;">2</div>
  </div>

  <form method="POST" action="{{ route('register.prestataire') }}" id="registerForm" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- ═══ ÉTAPE 1 : IDENTITÉ ═══ -->
    <div id="step1" class="step-container">
      <h3 style="font-size:1.1rem;font-weight:700;color:var(--gray-100);margin-bottom:1.5rem;">Votre Identité Profesionnelle</h3>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Nom complet ou raison sociale</label>
        <input type="text" name="name" class="form-control" placeholder="Marc Kouadio" value="{{ old('name') }}" required style="background:rgba(0,0,0,0.2);">
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Email Pro</label>
        <div style="position:relative;">
          <i class="fas fa-envelope text-gold-plain" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);"></i>
          <input type="email" name="email" class="form-control" placeholder="contact@example.ci" value="{{ old('email') }}" required style="background:rgba(0,0,0,0.2);padding-left:3rem;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:2.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Téléphone de Contact</label>
        <div style="display:flex;gap:0.5rem;">
          <select class="form-control" style="width:100px;background:rgba(0,0,0,0.2);">
            <option value="+225">🇨🇮 +225</option>
          </select>
          <input type="tel" name="phone" class="form-control" placeholder="07 00 00 00 00" value="{{ old('phone') }}" required style="background:rgba(0,0,0,0.2);">
        </div>
      </div>

      <button type="button" onclick="goStep(2)" class="btn btn-gold" style="width:100%;justify-content:center;height:50px;">
        Suivant <i class="fas fa-arrow-right"></i>
      </button>
    </div>

    <!-- ═══ ÉTAPE 2 : LOCALISATION & SECU ═══ -->
    <div id="step2" class="step-container" style="display:none;">
      <h3 style="font-size:1.1rem;font-weight:700;color:var(--gray-100);margin-bottom:1.5rem;">Sécurité & Localisation</h3>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Ville Opérationnelle</label>
        <select name="city_id" class="form-control" required style="background:rgba(0,0,0,0.2);">
          <option value="">Sélectionner votre ville</option>
          @foreach($cities as $city)
            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Mot de passe</label>
        <div style="position:relative;">
          <input type="password" name="password" id="regPassword" class="form-control" placeholder="Minimum 8 caractères" required style="background:rgba(0,0,0,0.2);padding-right:3rem;">
          <button type="button" onclick="toggleRegPwd('regPassword','regPwdIcon')" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-500);">
            <i class="fas fa-eye" id="regPwdIcon"></i>
          </button>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Confirmation</label>
        <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" placeholder="Confirmez le mot de passe" required style="background:rgba(0,0,0,0.2);">
      </div>

      <div class="checkbox-gold" style="margin-bottom:1.5rem;font-size:0.85rem;color:var(--gray-300);">
        <input type="checkbox" id="acceptCgu" name="accept_cgu" required>
        <label for="acceptCgu" style="">
          J'accepte les <a href="#" style="color:var(--gold-300);text-decoration:underline;">conditions d'utilisation Prestataire</a>.
        </label>
      </div>

      <div style="display:flex;gap:1rem;">
        <button type="button" onclick="goStep(1)" class="btn btn-dark" style="flex:1;justify-content:center;height:50px;border:1px solid var(--glass-border);">
          <i class="fas fa-arrow-left"></i>
        </button>
        <button type="submit" class="btn btn-gold" style="flex:4;justify-content:center;height:50px;box-shadow:0 5px 20px rgba(255,215,0,0.3);" id="registerBtn">
          <span id="regBtnText">Créer mon profil Pro <i class="fas fa-check"></i></span>
          <span id="regSpinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Traitement...</span>
        </button>
      </div>
    </div>
  </form>

  <p style="text-align:center;font-size:0.9rem;color:var(--gray-400);margin-top:2rem;">
    Déjà inscrit ? <a href="{{ route('login') }}" style="color:var(--gold-300);font-weight:700;margin-left:0.25rem;">Connectez-vous</a>
  </p>
</div>

<script>
function goStep(n) {
  for(let i=1; i<=2; i++) {
    const el = document.getElementById('step'+i);
    if(el) el.style.display = 'none';
  }
  
  const target = document.getElementById('step'+n);
  if(target) {
    target.style.display = 'block';
    if(typeof gsap !== 'undefined') gsap.from(target, {opacity: 0, x: 30, duration: 0.4, ease: "power2.out"});
  }

  const dots = document.querySelectorAll('.step-dot');
  dots.forEach(dot => {
    let stepNum = parseInt(dot.getAttribute('data-step'));
    if(stepNum < n) {
      dot.style.background = 'var(--gold-300)';
      dot.style.borderColor = 'var(--gold-300)';
      dot.style.color = 'var(--dark-500)';
    } else if(stepNum === n) {
      dot.style.background = 'var(--gold-300)';
      dot.style.borderColor = 'var(--gold-300)';
      dot.style.color = 'var(--dark-500)';
    } else {
      dot.style.background = 'var(--dark-100)';
      dot.style.borderColor = 'var(--dark-50)';
      dot.style.color = 'var(--gray-500)';
    }
  });

  const progress = document.getElementById('stepProgressLine');
  if(progress) progress.style.width = ((n-1)/1)*100 + '%';
}

function toggleRegPwd(inputId, iconId) {
  let input = document.getElementById(inputId);
  let icon = document.getElementById(iconId);
  if(input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash text-gold-plain'; }
  else { input.type = 'password'; icon.className = 'fas fa-eye'; }
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
  if(!document.getElementById('acceptCgu').checked) {
    alert("Veuillez accepter les CGU.");
    e.preventDefault(); return;
  }
  document.getElementById('regBtnText').style.display = 'none';
  document.getElementById('regSpinner').style.display = 'inline-block';
  document.getElementById('registerBtn').style.opacity = '0.7';
});
</script>
@endsection
