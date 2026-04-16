@extends('layouts.auth')

@section('content')
<div class="auth-box reveal" style="background:var(--glass-bg);backdrop-filter:blur(20px);padding:3rem;border-radius:var(--radius-lg);border:1px solid var(--glass-border);box-shadow:0 10px 50px rgba(0,0,0,0.5);">
  <div class="auth-header" style="text-align:center;margin-bottom:2rem;">
    <div style="width:60px;height:60px;background:var(--dark-100);border-radius:50%;margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;border:2px solid var(--gold-300);box-shadow:0 0 20px rgba(255,215,0,0.3);">
      <i class="fas fa-lock" style="font-size:1.5rem;color:var(--gold-300);"></i>
    </div>
    <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:800;margin-bottom:0.5rem;letter-spacing:-0.5px;">
      Bon retour ! <span class="text-gold">👋</span>
    </h1>
    <p style="color:var(--gray-400);font-size:0.875rem;">Connectez-vous à votre espace KOBLAN</p>
  </div>

  <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
    @csrf

    <div class="form-group reveal" data-delay="0.1">
      <label class="form-label" for="email" style="font-weight:600;color:var(--gray-300);margin-bottom:0.5rem;display:block;font-size:0.85rem;">
        <i class="fas fa-envelope text-gold-plain" style="margin-right:0.25rem;"></i> Adresse email
      </label>
      <input type="email" id="email" name="email" class="form-control"
             placeholder="votre@email.com" 
             value="{{ old('email') }}" required
             style="background:rgba(0,0,0,0.2) !important;border:1px solid rgba(255,255,255,0.1);height:50px;">
    </div>

    <div class="form-group reveal" data-delay="0.2">
      <label class="form-label" for="password" style="font-weight:600;color:var(--gray-300);margin-bottom:0.5rem;display:block;font-size:0.85rem;">
        <i class="fas fa-key text-gold-plain" style="margin-right:0.25rem;"></i> Mot de passe
      </label>
      <div style="position:relative;">
        <input type="password" id="password" name="password" class="form-control"
               placeholder="**************" required
               style="background:rgba(0,0,0,0.2) !important;border:1px solid rgba(255,255,255,0.1);height:50px;padding-right:3rem;">
        <button type="button" onclick="togglePassword()" 
                style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-500);font-size:1rem;"
                id="togglePwdBtn">
          <i class="fas fa-eye" id="pwdIcon"></i>
        </button>
      </div>
    </div>

    <div class="reveal" data-delay="0.3" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;font-size:0.85rem;">
      <label class="checkbox-gold" style="display:flex;align-items:center;gap:0.5rem;color:var(--gray-300);">
        <input type="checkbox" name="remember" style="accent-color:var(--gold-300);" {{ old('remember') ? 'checked' : '' }}>
        <span style="">Mémoriser ma session</span>
      </label>
      <a href="#" style="color:var(--gold-400);text-decoration:none;transition:var(--t-fast);font-weight:600;">Oublié ?</a>
    </div>

    <button type="submit" class="btn btn-gold reveal" data-delay="0.4" style="width:100%;justify-content:center;font-size:1.05rem;padding:1rem;border-radius:12px;box-shadow:0 5px 15px rgba(255,215,0,0.2);" id="loginBtn">
      <span id="loginBtnText" style="display:flex;align-items:center;gap:0.5rem;"><i class="fas fa-sign-in-alt"></i> Se Connecter</span>
      <span id="loginSpinner" style="display:none;align-items:center;gap:0.5rem;">
        <i class="fas fa-spinner fa-spin"></i> Authentification...
      </span>
    </button>

    <div class="reveal" data-delay="0.5" style="display:flex;align-items:center;gap:1.5rem;margin:2rem 0;">
      <div style="flex:1;height:1px;background:linear-gradient(to right, transparent, var(--glass-border), transparent);"></div>
      <span style="color:var(--gray-500);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;">ou avec</span>
      <div style="flex:1;height:1px;background:linear-gradient(to right, transparent, var(--glass-border), transparent);"></div>
    </div>

    <div class="reveal" data-delay="0.6" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
      <button type="button" class="btn" style="background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);color:var(--gray-100);justify-content:center;height:45px;" onclick="alert('Connexion Google imminente')">
        <i class="fab fa-google" style="color:#EA4335;"></i> Google
      </button>
      <button type="button" class="btn" style="background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);color:var(--gray-100);justify-content:center;height:45px;" onclick="alert('Connexion Facebook imminente')">
        <i class="fab fa-facebook-f" style="color:#1877F2;"></i> Facebook
      </button>
    </div>
  </form>

  <p class="reveal" data-delay="0.7" style="text-align:center;font-size:0.9rem;color:var(--gray-400);">
    Nouveau sur KOBLAN ? 
    <a href="{{ route('register') }}" style="color:var(--gold-300);font-weight:700;text-decoration:none;margin-left:0.25rem;">
      Créez un compte
    </a>
  </p>

  <div class="reveal" data-delay="0.8" style="margin-top:2rem;padding:1rem;background:rgba(255,215,0,0.02);border:1px dashed rgba(255,215,0,0.3);border-radius:12px;text-align:center;">
    <p style="font-size:0.7rem;color:var(--gray-500);margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.1em;">🔒 Accès Démo</p>
    <p style="font-size:0.8rem;color:var(--gold-400);font-family:monospace;">admin@koblan.ci / password</p>
  </div>
</div>

<script>
function togglePassword() {
  const pwd = document.getElementById('password');
  const icon = document.getElementById('pwdIcon');
  if (pwd.type === 'password') {
    pwd.type = 'text';
    icon.className = 'fas fa-eye-slash';
    icon.style.color = 'var(--gold-300)';
  } else {
    pwd.type = 'password';
    icon.className = 'fas fa-eye';
    icon.style.color = '';
  }
}

document.getElementById('loginForm')?.addEventListener('submit', function() {
  document.getElementById('loginBtnText').style.display = 'none';
  document.getElementById('loginSpinner').style.display = 'inline-flex';
  document.getElementById('loginBtn').style.opacity = '0.8';
});

document.querySelectorAll('.form-control').forEach(input => {
  input.addEventListener('focus', () => {
    if(typeof gsap !== 'undefined') gsap.to(input, {borderColor: 'var(--gold-300)', boxShadow: '0 0 10px rgba(255,215,0,0.2)', duration: 0.3});
  });
  input.addEventListener('blur', () => {
    if(typeof gsap !== 'undefined') gsap.to(input, {borderColor: 'rgba(255,255,255,0.1)', boxShadow: 'none', duration: 0.3});
  });
});
</script>
@endsection
