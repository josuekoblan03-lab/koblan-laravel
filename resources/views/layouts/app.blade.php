<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ $metaDesc ?? 'KOBLAN - La marketplace de services premium en Côte d\'Ivoire. Coiffure, plomberie, électricité, garde d\'enfants et plus encore.' }}">
  <meta name="keywords" content="services, Côte d'Ivoire, Abidjan, prestataires, coiffure, plomberie, KOBLAN">
  <meta name="author" content="KOBLAN">
  <meta property="og:title" content="{{ $title ?? 'KOBLAN - Services Premium CI' }}">
  <meta property="og:description" content="La marketplace de services N°1 en Côte d'Ivoire">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#FFD700">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'KOBLAN — Services Premium CI' }}</title>

  <!-- Favicon SVG -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Syne:wght@700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body style="background:#0A0A0A!important;color:#E5E5E5!important;">

<!-- GLOBAL THREE.JS CANVAS -->
<canvas id="global-canvas"></canvas>

<!-- PAGE LOADER CINÉMATIQUE -->
<div class="page-loader" id="pageLoader">
  <div style="position:relative;">
    <div class="loader-logo">K</div>
    <div style="position:absolute;top:-10px;right:-10px;width:20px;height:20px;border-radius:50%;background:var(--orange-ci);animation:pulse-glow 1s ease-in-out infinite;"></div>
  </div>
  <div class="loader-bar">
    <div class="loader-bar-fill"></div>
  </div>
  <p style="color:var(--gray-500);font-size:0.75rem;letter-spacing:0.25em;text-transform:uppercase;margin-top:0.5rem;">Koblan · Services Premium CI 🇨🇮</p>
</div>

<!-- MOBILE NAV OVERLAY -->
<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav-close" id="mobileNavClose"><i class="fas fa-times"></i></button>
  <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;opacity:0.05;">
    <div style="position:absolute;top:20%;left:10%;font-size:20rem;font-family:'Syne',sans-serif;font-weight:900;color:var(--gold-300);line-height:1;">K</div>
  </div>
  <nav style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;gap:1.75rem;">
    <a href="{{ route('home') }}">Accueil</a>
    <a href="{{ route('services.index') }}">Services</a>
    <a href="{{ url('/blog') }}">Blog</a>
    <a href="{{ url('/contact') }}">Contact</a>
  </nav>
  @guest
  <div style="display:flex;gap:1rem;margin-top:2rem;position:relative;z-index:2;">
    <a href="{{ route('login') }}" class="btn btn-outline-gold">Connexion</a>
    <a href="{{ route('register') }}" class="btn btn-gold">S'inscrire</a>
  </div>
  @endguest
</div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
  <div class="navbar-inner">
    <a href="{{ route('home') }}" class="navbar-logo">
      <div class="logo-icon">K</div>
      <div>
        <span class="logo-text">KOBLAN</span>
        <span class="logo-sub">Services CI</span>
      </div>
    </a>

    <ul class="navbar-links">
      <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Accueil</a></li>
      <li><a href="{{ route('services.index') }}" class="{{ request()->routeIs('services.*') ? 'active' : '' }}">Services</a></li>
      <li><a href="{{ url('/blog') }}">Blog</a></li>
      <li><a href="{{ url('/contact') }}">Contact</a></li>
    </ul>

    <div class="navbar-actions">
      @auth
        @php
            $user = auth()->user();
            $isProvider = $user->isPrestataire();
            $msgPage = url($isProvider ? '/prestataire/messages' : '/client/messages');
            $favPage = route('client.favorites');
            $ordersPage = $isProvider ? route('prestataire.orders.index') : route('client.bookings');

            $favCount = $user->favoris()->count();
            $msgCount = $user->messagesReceived()->unread()->count();
            
            if ($isProvider) {
                $cmdCount = \App\Models\Order::where('status', 'pending')
                    ->where('prestataire_id', $user->id)->count();
            } else {
                $cmdCount = \App\Models\Order::whereIn('status', ['pending', 'in_progress', 'confirmed'])
                    ->where('client_id', $user->id)->count();
            }
            $globalNotif = $user->unreadNotificationsCount();
        @endphp

        <!-- Icon: Favoris -->
        <a href="{{ $favPage }}" class="navbar-icon-btn" title="Mes Favoris" style="position:relative;background:none;border:none;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;color:var(--gray-300);text-decoration:none;transition:all 0.3s;">
          <i class="fas fa-heart" style="font-size:1.1rem;"></i>
          <span id="badge-fav" style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:0.65rem;font-weight:700;display:{{ $favCount > 0 ? 'flex' : 'none' }};align-items:center;justify-content:center;transform: scale({{ $favCount > 0 ? 1 : 0.5 }});transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">{{ $favCount }}</span>
        </a>

        <!-- Icon: Messages -->
        <a href="{{ $msgPage }}" class="navbar-icon-btn" title="Messages" style="position:relative;background:none;border:none;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;color:var(--gray-300);text-decoration:none;transition:all 0.3s;">
          <i class="fas fa-comment-dots" style="font-size:1.1rem;"></i>
          <span id="badge-msg" style="position:absolute;top:-4px;right:-4px;background:var(--gold-300);color:#000;border-radius:50%;width:18px;height:18px;font-size:0.65rem;font-weight:700;display:{{ $msgCount > 0 ? 'flex' : 'none' }};align-items:center;justify-content:center;transform: scale({{ $msgCount > 0 ? 1 : 0.5 }});transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">{{ $msgCount }}</span>
        </a>

        <!-- Icon: Commandes -->
        <a href="{{ $ordersPage }}" class="navbar-icon-btn" title="Mes Commandes" style="position:relative;background:none;border:none;display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;color:var(--gray-300);text-decoration:none;transition:all 0.3s;">
          <i class="fas fa-shopping-bag" style="font-size:1.1rem;"></i>
          <span id="badge-cmd" style="position:absolute;top:-4px;right:-4px;background:#10b981;color:#fff;border-radius:50%;width:18px;height:18px;font-size:0.65rem;font-weight:700;display:{{ $cmdCount > 0 ? 'flex' : 'none' }};align-items:center;justify-content:center;transform: scale({{ $cmdCount > 0 ? 1 : 0.5 }});transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">{{ $cmdCount }}</span>
        </a>

        <!-- Notification Bell -->
        <div class="dropdown" style="position:relative;">
          <a href="{{ url('/notifications') }}" style="background:none;border:none;color:var(--gray-400);font-size:1rem;position:relative;padding:0.5rem;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;" title="Notifications" class="navbar-icon-btn">
            <i class="fas fa-bell"></i>
            <span id="badge-notif" style="position:absolute;top:-2px;right:-2px;background:#3b82f6;color:#fff;border-radius:50%;width:18px;height:18px;font-size:0.65rem;font-weight:700;display:{{ $globalNotif > 0 ? 'flex' : 'none' }};align-items:center;justify-content:center;transform: scale({{ $globalNotif > 0 ? 1 : 0.5 }});transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">{{ $globalNotif }}</span>
          </a>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
          <div class="user-avatar-nav" id="userAvatarNav">
            @if($user->avatar)
              <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}">
            @else
              <div class="user-initials">{{ $user->getInitials() }}</div>
            @endif
          </div>
          <div class="dropdown-menu">
            <div style="padding:0.875rem;border-bottom:1px solid var(--glass-border);margin-bottom:0.5rem;">
              <p style="font-weight:700;font-size:0.875rem;color:var(--gray-100);">{{ $user->name }}</p>
              <p style="font-size:0.75rem;color:var(--gold-400);margin-top:0.2rem;">
                @if($user->isAdmin()) 👑 Administrateur
                @elseif($user->isPrestataire()) 🔧 Prestataire
                @else 👤 Client @endif
              </p>
            </div>
            @if($user->isAdmin())
              <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard Admin</a>
            @endif
            @if($user->isPrestataire())
              <a href="{{ route('prestataire.dashboard') }}"><i class="fas fa-tools"></i> Espace Prestataire</a>
            @endif
            @if($user->isClient())
              <a href="{{ route('client.dashboard') }}"><i class="fas fa-user"></i> Mon Espace</a>
            @endif
            
            <a href="{{ $favPage }}"><i class="fas fa-heart" style="color:#ef4444;"></i> Mes Favoris</a>
            <a href="{{ $msgPage }}"><i class="fas fa-comment-dots" style="color:var(--gold-300);"></i> Messages</a>
            <a href="{{ $ordersPage }}"><i class="fas fa-shopping-bag" style="color:#10b981;"></i> Commandes</a>
            
            @if($user->isPrestataire())
              <a href="{{ route('prestataire.profile') }}"><i class="fas fa-cog"></i> Paramètres</a>
            @else
              <a href="{{ route('client.profile') }}"><i class="fas fa-cog"></i> Paramètres</a>
            @endif
            
            <div class="divider"></div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;width:100%;text-align:left;color:var(--error)!important;padding:0.5rem 1rem;cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
          </div>
        </div>
      @endauth

      @guest
        <a href="{{ route('login') }}" class="btn btn-outline-gold btn-sm hide-on-mobile">Connexion</a>
        <a href="{{ route('register') }}" class="btn btn-gold btn-sm hide-on-mobile">S'inscrire <i class="fas fa-arrow-right"></i></a>
      @endguest

      <button class="menu-toggle" id="menuToggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

@auth
<style>
.navbar-icon-btn:hover { background: rgba(255,255,255,0.08) !important; color: var(--gold-300) !important; transform: translateY(-2px); }
.navbar-icon-btn:hover i { filter: drop-shadow(0 0 6px currentColor); }
</style>
<script>
    document.querySelectorAll('.navbar-icon-btn').forEach(btn => {
      btn.addEventListener('mouseenter', () => {
        if (typeof gsap !== 'undefined') gsap.to(btn.querySelector('i'), { scale: 1.3, duration: 0.2, ease: 'back.out(3)' });
      });
      btn.addEventListener('mouseleave', () => {
        if (typeof gsap !== 'undefined') gsap.to(btn.querySelector('i'), { scale: 1, duration: 0.2 });
      });
    });
</script>
@endauth

<style>
  @media (max-width: 900px) {
    .hide-on-mobile {
      display: none !important;
    }
  }
</style>

<!-- FLASH MESSAGE -->
@if(session()->has('success') || session()->has('error') || session()->has('warning'))
@php
    $flashType = session()->has('success') ? 'success' : (session()->has('error') ? 'error' : 'warning');
    $flashMsg = session('success') ?? (session('error') ?? session('warning'));
    $icon = $flashType === 'success' ? 'check-circle' : ($flashType === 'error' ? 'exclamation-circle' : 'info-circle');
@endphp
<div style="position:fixed;top:90px;right:1.5rem;z-index:500;max-width:380px;animation:slide-up 0.4s ease;" id="flashMsg">
  <div class="alert alert-{{ $flashType }}">
    <i class="fas fa-{{ $icon }}"></i>
    <span>{{ $flashMsg }}</span>
    <button onclick="document.getElementById('flashMsg').remove()" style="margin-left:auto;background:none;border:none;color:inherit;font-size:0.9rem;">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>
@endif

<!-- MAIN CONTENT -->
<main>
    @yield('content')
</main>

<footer class="footer">
  <canvas id="footer-canvas" style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;opacity:0.3;z-index:0;"></canvas>
  <div class="footer-inner" style="position:relative;z-index:1;">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="{{ route('home') }}" class="navbar-logo" style="margin-bottom:1.25rem;display:inline-flex;">
          <div class="logo-icon">K</div>
          <div><span class="logo-text">KOBLAN</span><span class="logo-sub">Services CI</span></div>
        </a>
        <p>La marketplace de services N°1 en Côte d'Ivoire. Connectez-vous avec des professionnels qualifiés et de confiance près de chez vous.</p>
        <div class="social-links" style="margin-top:1.5rem;">
          <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
          <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
        </div>
        <div style="margin-top:1.5rem;">
          <p style="font-size:0.8rem;color:var(--gray-400);margin-bottom:0.5rem;">Newsletter KOBLAN</p>
          <div style="display:flex;gap:0.5rem;">
            <input type="email" placeholder="votre@email.com" class="form-control" style="height:38px;font-size:0.8rem;">
            <button class="btn btn-gold btn-sm">OK</button>
          </div>
        </div>
      </div>
      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <li><a href="{{ route('services.index') }}">Tous Les Services</a></li>
          <li><a href="{{ url('/prestataires') }}">Prestataires</a></li>
          <li><a href="{{ url('/categories') }}">Catégories</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Plateforme</h4>
        <ul>
          <li><a href="{{ url('/about') }}">À Propos</a></li>
          <li><a href="{{ url('/blog') }}">Blog</a></li>
          <li><a href="{{ url('/contact') }}">Contact</a></li>
          <li><a href="{{ route('register.prestataire') }}">Devenir Prestataire</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Légal</h4>
        <ul>
          <li><a href="#">CGU</a></li>
          <li><a href="#">Confidentialité</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© {{ date('Y') }} KOBLAN. Tous droits réservés. 🇨🇮 <strong style="color:var(--gold-400);">Made in Côte d'Ivoire</strong></p>
    </div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
<script src="{{ asset('js/three-manager.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/app-ajax.js') }}"></script>
<script>
  @auth
  window.updateGlobalBadges = function() {
    fetch('/api/user/badges')
    .then(r => r.json())
    .then(data => {
      const bFav = document.getElementById('badge-fav');
      if(bFav) {
        bFav.textContent = data.favCount;
        bFav.style.display = data.favCount > 0 ? 'flex' : 'none';
        bFav.style.transform = data.favCount > 0 ? 'scale(1)' : 'scale(0.5)';
      }
      
      const bMsg = document.getElementById('badge-msg');
      if(bMsg) {
        bMsg.textContent = data.msgCount;
        bMsg.style.display = data.msgCount > 0 ? 'flex' : 'none';
        bMsg.style.transform = data.msgCount > 0 ? 'scale(1)' : 'scale(0.5)';
      }
      
      const bCmd = document.getElementById('badge-cmd');
      if(bCmd) {
        bCmd.textContent = data.cmdCount;
        bCmd.style.display = data.cmdCount > 0 ? 'flex' : 'none';
        bCmd.style.transform = data.cmdCount > 0 ? 'scale(1)' : 'scale(0.5)';
      }
      
      const bNotif = document.getElementById('badge-notif');
      if(bNotif) {
        bNotif.textContent = data.globalNotif;
        bNotif.style.display = data.globalNotif > 0 ? 'flex' : 'none';
        bNotif.style.transform = data.globalNotif > 0 ? 'scale(1)' : 'scale(0.5)';
      }
    })
    .catch(err => console.error("Badges sync error:", err));
  };

  // Temps réel pour tous les badges de la navbar
  setInterval(window.updateGlobalBadges, 500);
  @endauth
</script>
@yield('scripts')

@auth
<div id="toastContainer" style="position:fixed;bottom:2rem;right:2rem;display:flex;flex-direction:column;gap:1rem;z-index:9999;"></div>
<script>
  let lastNotifCount = {{ $globalNotif ?? 0 }};
  function showToast(titre, message, lien, type) {
    const toast = document.createElement('div');
    toast.style.background = 'var(--dark-100)';
    toast.style.border = '1px solid var(--glass-border)';
    toast.style.borderLeft = `4px solid ${type === 'commande' ? '#22C55E' : (type === 'message' ? '#3B82F6' : '#F59E0B')}`;
    toast.style.borderRadius = '12px';
    toast.style.padding = '1rem 1.5rem';
    toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
    toast.style.width = '300px';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(50px)';
    toast.style.transition = 'all 0.4s ease';
    toast.style.cursor = lien ? 'pointer' : 'default';

    toast.innerHTML = `<h4 style="margin:0 0 0.25rem 0;color:#fff;font-size:0.9rem;">${titre}</h4><p style="margin:0;font-size:0.8rem;color:var(--gray-400);">${message}</p>`;
    
    if (lien) toast.onclick = () => window.location.href = `{{ url('/') }}/${lien}`;
    document.getElementById('toastContainer').appendChild(toast);
    
    requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; });
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; setTimeout(() => toast.remove(), 400); }, 6000);
  }

  function checkNotifications() {
    fetch('{{ url('/api/notifications') }}', {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(r => r.json())
      .then(list => {
          const count = list.length || 0;
          let badge = document.querySelector('.notif-badge');
          if (count > 0) {
              if (!badge) {
                  badge = document.createElement('span');
                  badge.className = 'notif-badge';
                  badge.style.cssText = 'position:absolute;top:-2px;right:-2px;background:#3b82f6;color:#fff;border-radius:50%;width:18px;height:18px;font-size:0.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;';
                  document.querySelector('a[title="Notifications"]').appendChild(badge);
              }
              badge.textContent = count;
              badge.style.display = 'flex';

              if (count > lastNotifCount && list[0]) {
                  const recent = list[0]; 
                  showToast(recent.title, recent.message, recent.link, recent.type);
              }
          } else if (badge) {
              badge.style.display = 'none';
          }
          lastNotifCount = count;
      });
  }
  // Temps réel quasi-instantané (polling toutes les 500 millisecondes)
  setInterval(checkNotifications, 500);
</script>
@endauth

</body>
</html>
