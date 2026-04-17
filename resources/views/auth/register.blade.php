@extends('layouts.auth')

@section('content')
<div class="auth-box reveal" style="background:var(--glass-bg);backdrop-filter:blur(20px);padding:3rem;border-radius:var(--radius-lg);border:1px solid var(--glass-border);box-shadow:0 10px 50px rgba(0,0,0,0.5);">
  <div class="auth-header" style="text-align:center;margin-bottom:2rem;">
    <div style="width:60px;height:60px;background:var(--dark-100);border-radius:20px;margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;border:2px solid var(--gold-300);transform:rotate(45deg);box-shadow:0 0 20px rgba(255,215,0,0.2);">
      <i class="fas fa-rocket" style="font-size:1.5rem;color:var(--gold-300);transform:rotate(-45deg);"></i>
    </div>
    <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:0.5rem;">
      Rejoignez <span class="text-gold">L'Élite</span> 🇨🇮
    </h1>
    <p style="color:var(--gray-400);font-size:0.85rem;">Misez sur l'excellence pour vos services.</p>
  </div>

  @if($errors->any())
  <div class="alert alert-error" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#EF4444;border-radius:8px;padding:1rem;margin-bottom:1.5rem;font-size:0.85rem;">
    <i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i> Veuillez corriger les erreurs.
    <ul style="margin-top:0.5rem;padding-left:1.5rem;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <!-- Étapes Magiques -->
  <div style="display:flex;align-items:center;margin-bottom:2rem;position:relative;" id="stepsIndicator">
    <div style="position:absolute;top:50%;left:0;right:0;height:2px;background:var(--dark-50);z-index:1;"></div>
    <div id="stepProgressLine" style="position:absolute;top:50%;left:0;width:0%;height:2px;background:var(--gold-300);z-index:2;transition:width 0.4s ease;"></div>
    
    <div class="step-dot active" data-step="1" style="width:24px;height:24px;border-radius:50%;background:var(--gold-300);color:var(--dark-500);display:flex;justify-content:center;align-items:center;font-size:0.7rem;font-weight:800;z-index:3;transition:all 0.4s;">1</div>
    <div style="flex:1;"></div>
    <div class="step-dot" data-step="2" style="width:24px;height:24px;border-radius:50%;background:var(--dark-100);border:2px solid var(--dark-50);color:var(--gray-500);display:flex;justify-content:center;align-items:center;font-size:0.7rem;font-weight:800;z-index:3;transition:all 0.4s;">2</div>
    <div style="flex:1;"></div>
    <div class="step-dot" data-step="3" style="width:24px;height:24px;border-radius:50%;background:var(--dark-100);border:2px solid var(--dark-50);color:var(--gray-500);display:flex;justify-content:center;align-items:center;font-size:0.7rem;font-weight:800;z-index:3;transition:all 0.4s;">3</div>
  </div>

  <form method="POST" action="{{ route('client.register.submit') }}" id="registerForm" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- ═══ ÉTAPE 1 : IDENTITÉ ═══ -->
    <div id="step1" class="step-container">
      <h3 style="font-size:1.1rem;font-weight:700;color:var(--gray-100);margin-bottom:1.5rem;">Informations Personnelles</h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        <div class="form-group" style="margin:0;">
          <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Nom</label>
          <input type="text" name="nom" class="form-control" placeholder="KOUADIO" value="{{ old('nom') }}" required style="background:rgba(0,0,0,0.2);">
        </div>
        <div class="form-group" style="margin:0;">
          <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Prénom</label>
          <input type="text" name="prenom" class="form-control" placeholder="Marc" value="{{ old('prenom') }}" required style="background:rgba(0,0,0,0.2);">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Email</label>
        <div style="position:relative;">
          <i class="fas fa-envelope text-gold-plain" style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);"></i>
          <input type="email" name="email" class="form-control" placeholder="marc@example.ci" value="{{ old('email') }}" required style="background:rgba(0,0,0,0.2);padding-left:3rem;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:2.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Téléphone</label>
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
      <h3 style="font-size:1.1rem;font-weight:700;color:var(--gray-100);margin-bottom:1.5rem;">Localisation & Sécurité</h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
        <div class="form-group" style="margin:0;">
          <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Ville</label>
          <select name="city_id" id="villeSelect" class="form-control" onchange="loadQuartiers(this.value)" required style="background:rgba(0,0,0,0.2);">
            <option value="">Sélectionner</option>
            @foreach ($cities as $city)
              <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Quartier</label>
          <select name="neighborhood_id" id="quartierSelect" class="form-control" required style="background:rgba(0,0,0,0.2);">
             <option value="">Sélectionner</option>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Mot de passe</label>
        <div style="position:relative;">
          <input type="password" name="password" id="regPassword" class="form-control" placeholder="Minimum 8 caractères" required style="background:rgba(0,0,0,0.2);padding-right:3rem;">
          <button type="button" onclick="toggleRegPwd('regPassword','regPwdIcon')" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-500);">
            <i class="fas fa-eye" id="regPwdIcon"></i>
          </button>
        </div>
        <!-- Bar force Mdp -->
        <div style="height:4px;background:rgba(255,255,255,0.05);margin-top:0.5rem;border-radius:2px;overflow:hidden;">
          <div id="pwdStrengthFill" style="height:100%;width:0%;background:transparent;transition:0.3s;"></div>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:2.5rem;">
        <label style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;display:block;">Confirmation</label>
        <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" placeholder="Confirmez le mot de passe" required style="background:rgba(0,0,0,0.2);">
      </div>

      <div style="display:flex;gap:1rem;">
        <button type="button" onclick="goStep(1)" class="btn btn-dark" style="flex:1;justify-content:center;height:50px;border:1px solid var(--glass-border);">
          <i class="fas fa-arrow-left"></i> Retour
        </button>
        <button type="button" onclick="goStep(3)" class="btn btn-gold" style="flex:2;justify-content:center;height:50px;">
          Suivant <i class="fas fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- ═══ ÉTAPE 3 : RÔLE ═══ -->
    <div id="step3" class="step-container" style="display:none;">
      <h3 style="font-size:1.1rem;font-weight:700;color:var(--gray-100);margin-bottom:1.5rem;text-align:center;">Choisissez votre Profil</h3>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
        <label for="roleClient" style="display:block;">
          <input type="radio" name="role_principal" id="roleClient" value="client" {{ old('role_principal', request('role')) !== 'prestataire' ? 'checked' : '' }} style="position:absolute;opacity:0;pointer-events:none;" class="role-radio">
          <div class="role-card" id="roleClientCard" style="padding:1.5rem 1rem;text-align:center;border:2px solid rgba(255,215,0,0.1);border-radius:16px;background:rgba(0,0,0,0.2);transition:all 0.3s;height:100%;">
            <i class="fas fa-user-circle" style="font-size:2.5rem;color:var(--gray-500);margin-bottom:1rem;transition:0.3s;"></i>
            <div style="font-family:var(--font-alt);font-weight:700;font-size:1rem;color:var(--gray-100);margin-bottom:0.5rem;">Je suis Client</div>
            <div style="font-size:0.75rem;color:var(--gray-400);">Je cherche des pros.</div>
          </div>
        </label>
        <label for="rolePrest" style="display:block;">
          <input type="radio" name="role_principal" id="rolePrest" value="prestataire" {{ old('role_principal', request('role')) === 'prestataire' ? 'checked' : '' }} style="position:absolute;opacity:0;pointer-events:none;" class="role-radio">
          <div class="role-card" id="rolePrestCard" style="padding:1.5rem 1rem;text-align:center;border:2px solid rgba(255,215,0,0.1);border-radius:16px;background:rgba(0,0,0,0.2);transition:all 0.3s;height:100%;">
            <i class="fas fa-briefcase" style="font-size:2.5rem;color:var(--gray-500);margin-bottom:1rem;transition:0.3s;"></i>
            <div style="font-family:var(--font-alt);font-weight:700;font-size:1rem;color:var(--gray-100);margin-bottom:0.5rem;">Je suis Pro</div>
            <div style="font-size:0.75rem;color:var(--gray-400);">Je propose mes services.</div>
          </div>
        </label>
      </div>

      <div class="checkbox-gold" style="margin-bottom:1.5rem;font-size:0.85rem;color:var(--gray-300);">
        <input type="checkbox" id="acceptCgu" name="accept_cgu" required>
        <label for="acceptCgu" style="">
          J'accepte les <a href="#" style="color:var(--gold-300);text-decoration:underline;">conditions d'utilisation</a> et la politique de confidentialité.
        </label>
      </div>

      <div style="display:flex;gap:1rem;">
        <button type="button" onclick="goStep(2)" class="btn btn-dark" style="flex:1;justify-content:center;height:50px;border:1px solid var(--glass-border);">
          <i class="fas fa-arrow-left"></i>
        </button>
        <button type="submit" class="btn btn-gold" style="flex:4;justify-content:center;height:50px;font-size:1.1rem;box-shadow:0 5px 20px rgba(255,215,0,0.3);" id="registerBtn">
          <span id="regBtnText">Terminer l'Inscription <i class="fas fa-check"></i></span>
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
  // Masquer tout
  for(let i=1; i<=3; i++) {
    const el = document.getElementById('step'+i);
    if(el) el.style.display = 'none';
  }
  
  // Afficher la cible
  const target = document.getElementById('step'+n);
  if(target) {
    target.style.display = 'block';
    if(typeof gsap !== 'undefined') gsap.from(target, {opacity: 0, x: 30, duration: 0.4, ease: "power2.out"});
  }

  // Mettre à jour les dots et progress bar
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
      if(typeof gsap !== 'undefined') gsap.from(dot, {scale:1.3, duration:0.3});
    } else {
      dot.style.background = 'var(--dark-100)';
      dot.style.borderColor = 'var(--dark-50)';
      dot.style.color = 'var(--gray-500)';
    }
  });

  const progress = document.getElementById('stepProgressLine');
  if(progress) {
    progress.style.width = ((n-1)/2)*100 + '%';
  }
}

// Init Cards
function updateRoleCards() {
  const clientC = document.getElementById('roleClient').checked;
  const prestC = document.getElementById('rolePrest').checked;
  
  const cCard = document.getElementById('roleClientCard');
  const pCard = document.getElementById('rolePrestCard');
  
  if(cCard) {
    cCard.style.borderColor = clientC ? 'var(--gold-300)' : 'rgba(255,215,0,0.1)';
    cCard.style.background = clientC ? 'rgba(255,215,0,0.05)' : 'rgba(0,0,0,0.2)';
    cCard.querySelector('i').style.color = clientC ? 'var(--gold-300)' : 'var(--gray-500)';
  }
  if(pCard) {
    pCard.style.borderColor = prestC ? 'var(--gold-300)' : 'rgba(255,215,0,0.1)';
    pCard.style.background = prestC ? 'rgba(255,215,0,0.05)' : 'rgba(0,0,0,0.2)';
    pCard.querySelector('i').style.color = prestC ? 'var(--gold-300)' : 'var(--gray-500)';
  }
}
document.querySelectorAll('.role-radio').forEach(cb => cb.addEventListener('change', updateRoleCards));
updateRoleCards();

// Password Strength
document.getElementById('regPassword')?.addEventListener('input', function(e) {
  let val = e.target.value;
  let s = 0;
  if(val.length > 7) s++;
  if(/[A-Z]/.test(val)) s++;
  if(/[0-9]/.test(val)) s++;
  if(/[^A-Za-z0-9]/.test(val)) s++;
  const w = ['0%','25%','50%','75%','100%'];
  const c = ['transparent','#EF4444','#F59E0B','#22C55E','#22C55E'];
  document.getElementById('pwdStrengthFill').style.width = w[s];
  document.getElementById('pwdStrengthFill').style.background = c[s];
});

function toggleRegPwd(inputId, iconId) {
  let input = document.getElementById(inputId);
  let icon = document.getElementById(iconId);
  if(input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash text-gold-plain'; }
  else { input.type = 'password'; icon.className = 'fas fa-eye'; }
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
  if(!document.getElementById('roleClient').checked && !document.getElementById('rolePrest').checked) {
    alert("Veuillez sélectionner au moins un profil.");
    e.preventDefault(); return;
  }
  if(!document.getElementById('acceptCgu').checked) {
    alert("Veuillez accepter les CGU.");
    e.preventDefault(); return;
  }
  document.getElementById('regBtnText').style.display = 'none';
  document.getElementById('regSpinner').style.display = 'inline-block';
  document.getElementById('registerBtn').style.opacity = '0.7';
});

// Dynamic Neighborhood Loading
const citiesData = @json($cities);
function loadQuartiers(id_ville) {
  const select = document.getElementById('quartierSelect');
  select.innerHTML = '<option value="">Sélectionner</option>';
  if (!id_ville) return;
  
  const city = citiesData.find(c => c.id == id_ville);
  if (city && city.neighborhoods) {
    city.neighborhoods.forEach(q => {
      let opt = document.createElement('option');
      opt.value = q.id;
      opt.textContent = q.name;
      select.appendChild(opt);
    });
  }
}

// Prefill neighborhoods if a city is already selected
document.addEventListener("DOMContentLoaded", function() {
    let villeSelect = document.getElementById('villeSelect');
    if(villeSelect && villeSelect.value) {
        loadQuartiers(villeSelect.value);
        let oldNeighborhood = "{{ old('neighborhood_id') }}";
        if(oldNeighborhood) {
            document.getElementById('quartierSelect').value = oldNeighborhood;
        }
    }
});
</script>
@endsection
