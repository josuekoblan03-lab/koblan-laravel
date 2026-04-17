<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Dashboard — KOBLAN' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .dash-layout { display: flex; min-height: 100vh; background: var(--dark-400); }
    .dash-sidebar {
      width: 260px; flex-shrink: 0; background: var(--dark-500); border-right: 1px solid var(--glass-border);
      display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0;
      z-index: 100; transition: var(--t-med);
    }
    .dash-sidebar-header { padding: 1.5rem; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 0.75rem; }
    .dash-user-info .name { font-weight: 700; font-size: 0.9rem; display: block; }
    .dash-user-info .role { font-size: 0.75rem; color: var(--gold-400); }
    .dash-nav { flex: 1; padding: 1.5rem 1rem; overflow-y: auto; }
    .dash-nav-group { margin-bottom: 2rem; }
    .dash-nav-group-title {
      font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em;
      color: var(--gray-500); margin-bottom: 0.75rem; padding: 0 0.5rem;
    }
    .dash-nav a {
      display: flex; align-items: center; gap: 0.875rem; padding: 0.75rem 0.875rem; border-radius: 12px;
      color: var(--gray-300); text-decoration: none; font-size: 0.875rem; font-weight: 500;
      transition: var(--t-fast); margin-bottom: 0.25rem; position: relative;
    }
    .dash-nav a:hover, .dash-nav a.active { background: var(--glass-1); color: var(--gold-300); }
    .dash-nav a.active { background: linear-gradient(135deg, rgba(255,215,0,0.12), rgba(255,215,0,0.06)); border: 1px solid rgba(255,215,0,0.2); }
    .dash-nav a .nav-icon {
      width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
      background: var(--dark-50); font-size: 0.875rem; flex-shrink: 0; transition: var(--t-fast);
    }
    .dash-nav a:hover .nav-icon, .dash-nav a.active .nav-icon { background: var(--glass-2); }
    .nav-badge { margin-left: auto; background: var(--gold-300); color: var(--dark-500); border-radius: 9999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 700; }
    .dash-sidebar-footer { padding: 1rem; border-top: 1px solid var(--glass-border); }
    .dash-main { flex: 1; margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh; width: calc(100% - 260px); max-width: calc(100% - 260px); }
    .dash-topbar {
      padding: 1rem 2rem; background: rgba(10,10,10,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border);
      display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50;
    }
    .dash-page-title { font-family: var(--font-display); font-size: 1.5rem; font-weight: 800; }
    .dash-content { flex: 1; padding: 2rem; }
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
    .kpi-card { padding: 1.5rem; background: var(--dark-100); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); position: relative; overflow: hidden; }
    .kpi-card::before { content: ''; position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: radial-gradient(circle, var(--kpi-color, var(--gold-300)) 0%, transparent 70%); }
    .kpi-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 1rem; background: var(--kpi-bg, rgba(255,215,0,0.1)); color: var(--kpi-color, var(--gold-300)); }
    .kpi-value { font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: var(--kpi-color, var(--gold-300)); display: block; line-height: 1; margin-bottom: 0.25rem; }
    .kpi-label { font-size: 0.8rem; color: var(--gray-500); }
    @media (max-width: 1024px) {
      .dash-sidebar { transform: translateX(-100%); }
      .dash-sidebar.open { transform: translateX(0); }
      .dash-main { margin-left: 0; width: 100%; max-width: 100%; }
    }
  </style>
</head>
<body>
  <div class="cursor-dot" id="cursorDot"></div>
  <div class="cursor-ring" id="cursorRing"></div>

  @php 
    $user = auth()->user(); 
    $currentPath = request()->path();
    $modeAdmin = request()->routeIs('admin.*');
    $modeProvider = request()->routeIs('prestataire.*');
    $modeClient = request()->routeIs('client.*');
    $dashNotif = $user->unreadNotificationsCount();
  @endphp

  <div class="dash-layout">
    <!-- SIDEBAR -->
    <aside class="dash-sidebar" id="dashSidebar">
      <div class="dash-sidebar-header">
        <div class="user-avatar-nav" style="width:44px;height:44px;flex-shrink:0;">
          @if($user->avatar)
            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}">
          @else
            <div class="user-initials" style="font-size:0.85rem;">{{ $user->getInitials() }}</div>
          @endif
        </div>
        <div class="dash-user-info">
          <span class="name">{{ $user->name }}</span>
          <span class="role">
            @if($user->isAdmin() && $modeAdmin) 👑 Admin
            @elseif($user->isPrestataire() && $modeProvider) 🔧 Prestataire
            @else 👤 Client @endif
          </span>
        </div>
      </div>

      <nav class="dash-nav">
        @if($modeAdmin && $user->isAdmin())
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Administration</div>
          <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-tachometer-alt"></i></div> Vue d'ensemble
          </a>
          <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-users"></i></div> Utilisateurs
          </a>
          <a href="{{ route('admin.providers.index') }}" class="{{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-user-check"></i></div> Prestataires
          </a>
          <a href="{{ route('admin.prestations.index') }}" class="{{ request()->routeIs('admin.prestations.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-clipboard-check"></i></div> Valider Prestations
            @php $pendingCount = \App\Models\Prestation::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
              <span style="margin-left:auto;background:#F59E0B;color:#000;border-radius:99px;padding:0.1rem 0.5rem;font-size:0.68rem;font-weight:700;">{{ $pendingCount }}</span>
            @endif
          </a>
          <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-briefcase"></i></div> Types de services
          </a>
          <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-tags"></i></div> Catégories
          </a>
          <a href="{{ route('admin.statistics') }}" class="{{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-chart-line"></i></div> Statistiques
          </a>
        </div>
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Changer de mode</div>
          <a href="{{ route('client.dashboard') }}"><div class="nav-icon"><i class="fas fa-user"></i></div> Mode Client</a>
        </div>
        @endif

        @if($modeProvider && $user->isPrestataire())
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Prestataire</div>
          <a href="{{ route('prestataire.dashboard') }}" class="{{ request()->routeIs('prestataire.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-home"></i></div> Tableau de bord
          </a>
          <a href="{{ route('prestataire.services.index') }}" class="{{ request()->routeIs('prestataire.services.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-briefcase"></i></div> Mes Prestations
          </a>
          <a href="{{ route('prestataire.orders.index') }}" class="{{ request()->routeIs('prestataire.orders.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-list-check"></i></div> Commandes Reçues
          </a>
          <a href="{{ route('prestataire.profile') }}" class="{{ request()->routeIs('prestataire.profile') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-cog"></i></div> Paramètres
          </a>
        </div>
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Changer de mode</div>
          <a href="{{ route('client.dashboard') }}" style="background:rgba(255,215,0,0.05); border: 1px solid rgba(255,215,0,0.2);">
            <div class="nav-icon" style="background:transparent;color:var(--gold-300);"><i class="fas fa-exchange-alt"></i></div>
            <span style="color:var(--gold-300);font-weight:bold;">Basculer vers Client</span>
          </a>
        </div>
        @endif

        @if($modeClient || (!$modeAdmin && !$modeProvider))
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Client</div>
          <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-home"></i></div> Tableau de bord
          </a>
          <a href="{{ route('client.bookings') }}" class="{{ request()->routeIs('client.bookings') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-shopping-bag"></i></div> Mes Commandes
          </a>
          <a href="{{ route('client.favorites') }}" class="{{ request()->routeIs('client.favorites') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-heart"></i></div> Favoris
          </a>
        </div>
        <div class="dash-nav-group">
          <div class="dash-nav-group-title">{{ $user->isPrestataire() ? 'Changer de mode' : 'Évolution' }}</div>
          @if($user->isPrestataire())
            <a href="{{ route('prestataire.dashboard') }}" style="background:rgba(255,215,0,0.05); border: 1px solid rgba(255,215,0,0.2);">
              <div class="nav-icon" style="background:transparent;color:var(--gold-300);"><i class="fas fa-exchange-alt"></i></div>
              <span style="color:var(--gold-300);font-weight:bold;">Basculer vers Prestataire</span>
            </a>
          @else
            <a href="#" onclick="event.preventDefault(); document.getElementById('upgrade-form').submit();">
              <div class="nav-icon" style="background:rgba(255,215,0,0.1);color:var(--gold-300);"><i class="fas fa-arrow-up"></i></div>
              <span style="color:var(--gold-300);font-weight:bold;">Devenir Prestataire</span>
            </a>
          @endif
        </div>
        @endif

        <form id="upgrade-form" action="{{ route('client.upgrade') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <div class="dash-nav-group">
          <div class="dash-nav-group-title">Général</div>
          <a href="{{ route('services.index') }}"><div class="nav-icon"><i class="fas fa-search"></i></div> Explorer les services</a>
          <a href="{{ route('client.profile') }}" class="{{ request()->routeIs('client.profile') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-user-cog"></i></div> Mon Profil
          </a>
        </div>
      </nav>

      <div class="dash-sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="display:flex;align-items:center;gap:0.875rem;padding:0.75rem 0.875rem;border-radius:12px;color:var(--error);text-decoration:none;font-size:0.875rem;transition:var(--t-fast);width:100%;border:none;background:transparent;cursor:pointer;" 
               onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
              <div class="nav-icon" style="background:rgba(239,68,68,0.1);color:var(--error);"><i class="fas fa-sign-out-alt"></i></div>
              Déconnexion
            </button>
        </form>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">
      <div class="dash-topbar">
        <div style="display:flex;align-items:center;gap:1rem;">
          <button id="sidebarToggle" style="display:none;background:none;border:none;color:var(--gray-300);font-size:1.2rem;" onclick="document.getElementById('dashSidebar').classList.toggle('open')">
            <i class="fas fa-bars"></i>
          </button>
          <div class="dash-page-title">{{ $title ?? 'Dashboard' }}</div>
        </div>

        <div style="display:flex;align-items:center;gap:1rem;">
          <!-- Recherche rapide -->
          <div style="display:flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.04);border:1px solid var(--glass-border);border-radius:9999px;padding:0.4rem 1rem;">
            <i class="fas fa-search" style="color:var(--gray-500);font-size:0.8rem;"></i>
            <input type="text" placeholder="Rechercher..." style="background:none;border:none;outline:none;color:var(--gray-100);font-size:0.8rem;width:150px;"
                   onkeypress="if(event.key==='Enter')window.location.href='{{ route('services.index') }}?q='+encodeURIComponent(this.value)">
          </div>

          <!-- Notifications -->
          <div style="position:relative;">
            <a href="{{ url('/notifications') }}" style="text-decoration:none; color:var(--gray-300);font-size:1rem;width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.04);border:1px solid var(--glass-border);display:flex;align-items:center;justify-content:center;transition:var(--t-fast);"
               onmouseover="this.style.borderColor='var(--gold-300)';this.style.color='var(--gold-300)'" onmouseout="this.style.borderColor='var(--glass-border)';this.style.color='var(--gray-300)'" title="Notifications">
              <i class="fas fa-bell"></i>
            </a>
            @if($dashNotif > 0)
               <span class="notif-badge" style="position:absolute;top:-4px;right:-4px;background:#3b82f6;color:white;font-size:0.6rem;padding:2px 5px;border-radius:10px;font-weight:bold;">{{ $dashNotif }}</span>
            @endif
          </div>

          <!-- Go Home -->
          <a href="{{ route('home') }}" class="btn btn-gold" style="border-radius:9999px; padding:0.4rem 1.2rem; font-size:0.85rem; font-weight:700; display:flex; align-items:center; gap:0.5rem; box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);">
            <i class="fas fa-home" style="font-size:0.9rem;"></i> ACCUEIL
          </a>
        </div>
      </div>

      <!-- Flash messages -->
      @if(session()->has('success') || session()->has('error') || $errors->any())
        @php
            $flashType = session('success') ? 'success' : 'error';
            $flashMsg = session('success') ?? session('error') ?? $errors->first();
            $icon = $flashType === 'success' ? 'check-circle' : 'exclamation-circle';
        @endphp
        <div style="padding:0 2rem;margin-top:1rem;" id="flashMsg">
          <div class="alert alert-{{ $flashType }}">
            <i class="fas fa-{{ $icon }}"></i>
            <span>{{ $flashMsg }}</span>
            <button onclick="document.getElementById('flashMsg').remove()" style="margin-left:auto;background:none;border:none;color:inherit;">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      @endif

      <div class="dash-content">
        @yield('content')
      </div>
    </main>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="{{ asset('js/cursor.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/app-ajax.js') }}"></script>
  <script>
    gsap.registerPlugin(ScrollTrigger);
    if (window.innerWidth <= 1024 && document.getElementById('sidebarToggle')) { document.getElementById('sidebarToggle').style.display = 'block'; }
    gsap.utils.toArray('.kpi-card').forEach((card, i) => { gsap.from(card, { opacity: 0, y: 30, duration: 0.5, delay: i * 0.1, ease: 'power2.out' }); });
    document.addEventListener('click', (e) => {
      const sidebar = document.getElementById('dashSidebar');
      const toggle = document.getElementById('sidebarToggle');
      if (!sidebar.contains(e.target) && !toggle?.contains(e.target)) sidebar.classList.remove('open');
    });
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => gsap.to(flash, { opacity: 0, onComplete: () => flash.remove() }), 5000);
  </script>
  @yield('scripts')
</body>
</html>
