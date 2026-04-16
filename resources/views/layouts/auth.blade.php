<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Authentification - KOBLAN' }}</title>
  
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Syne:wght@700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background:var(--dark-500); overflow-x:hidden;">

<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<div class="page-loader" id="pageLoader">
  <div class="loader-logo">K</div>
  <div class="loader-bar"><div class="loader-bar-fill"></div></div>
</div>

<main style="display:flex; min-height:100vh;">
  <div style="flex:1; display:flex; flex-direction:column; padding:2rem; position:relative; z-index:10; background:var(--dark-400); border-right:1px solid var(--glass-border);">
    
    <a href="{{ route('home') }}" class="navbar-logo" style="margin-bottom:auto;">
      <div class="logo-icon">K</div>
      <div>
        <span class="logo-text">KOBLAN</span>
        <span class="logo-sub">Retour à l'accueil</span>
      </div>
    </a>

    @if(session()->has('success') || session()->has('error') || $errors->any())
    @php
        $flashType = session('success') ? 'success' : 'error';
        $flashMsg = session('success') ?? session('error') ?? $errors->first();
        $icon = $flashType === 'success' ? 'check-circle' : 'exclamation-circle';
    @endphp
    <div class="alert alert-{{ $flashType }}" style="margin-top:2rem; margin-bottom:1rem; animation:fade-in 0.5s ease;">
      <i class="fas fa-{{ $icon }}"></i>
      <span>{{ $flashMsg }}</span>
    </div>
    @endif

    <div style="margin:auto 0; width:100%; max-width:480px; align-self:center;" class="reveal">
      @yield('content')
    </div>

    <div style="margin-top:auto; font-size:0.75rem; color:var(--gray-500); display:flex; justify-content:space-between;">
      <span>© {{ date('Y') }} KOBLAN</span>
      <div style="display:flex; gap:1rem;">
        <a href="#" style="color:var(--gray-400);">CGU</a>
        <a href="#" style="color:var(--gray-400);">Confidentialité</a>
      </div>
    </div>
  </div>

  <div style="flex:1; position:relative; overflow:hidden; display:none;" id="authVisualContainer">
    <canvas id="auth-canvas" style="position:absolute;inset:0;width:100%;height:100%;"></canvas>
    
    <div style="position:absolute;inset:0;background:linear-gradient(to right, var(--dark-400) 0%, transparent 20%);pointer-events:none;"></div>
    
    <div style="position:absolute; bottom:10%; right:10%; text-align:right; z-index:2; pointer-events:none;">
      <h2 style="font-family:var(--font-display); font-size:3rem; font-weight:900; color:var(--gold-300); line-height:1.1; margin-bottom:1rem;" class="glow-text">
        Services<br>Premium CI
      </h2>
      <p style="color:var(--gray-200); max-width:300px; margin-left:auto; font-size:1.1rem;">
        Rejoignez la plateforme de mise en relation N°1 en Côte d'Ivoire.
      </p>
    </div>
  </div>
</main>

<style>
@media (min-width: 1024px) {
  #authVisualContainer { display: block !important; }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="{{ asset('js/three-manager.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
