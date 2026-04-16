@extends('layouts.dashboard')

@section('title', 'Mon Profil — KOBLAN')

@section('content')
@php
$joinDate = $user->created_at ? $user->created_at->format('M Y') : '2026';
$initials = $user->getInitials();
@endphp

<!-- CANVAS 3D -->
<canvas id="prof-canvas" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;opacity:0.6;"></canvas>

<div class="prof-wrap">

@if(session('success'))
<div class="prof-flash prof-flash-success" id="profFlash">
  <i class="fas fa-check-circle"></i> {{ session('success') }}
  <button onclick="document.getElementById('profFlash').remove()" style="background:none;border:none;color:inherit;margin-left:auto;cursor:pointer;"><i class="fas fa-times"></i></button>
</div>
@endif
@if(session('error'))
<div class="prof-flash prof-flash-error" id="profFlash">
  <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
  <button onclick="document.getElementById('profFlash').remove()" style="background:none;border:none;color:inherit;margin-left:auto;cursor:pointer;"><i class="fas fa-times"></i></button>
</div>
@endif

<!-- HERO PROFIL -->
<div class="prof-hero">
  <div class="prof-hero-bg-glow"></div>

  <div class="prof-identity">
    <div class="prof-avatar-ring">
      <div class="prof-avatar" id="profAvatarDisplay">
        @if($user->avatar)
          <img src="{{ asset('storage/'.$user->avatar) }}" alt="Photo" onerror="this.parentNode.innerHTML='<span>{{ $initials }}</span>'">
        @else
          <span>{{ $initials }}</span>
        @endif
      </div>
      <label class="prof-avatar-edit" for="photoInput" title="Changer la photo">
        <i class="fas fa-camera"></i>
      </label>
    </div>

    <div class="prof-identity-info">
      <h1 class="prof-name">{{ $user->name }}</h1>
      <div class="prof-role-badge"><i class="fas fa-circle" style="font-size:0.45rem;color:#22C55E;"></i> Client actif</div>
      <div class="prof-member-since"><i class="fas fa-calendar-alt"></i> Membre depuis {{ $joinDate }}</div>
      @if($user->bio)
      <p class="prof-bio-display">{{ $user->bio }}</p>
      @endif
    </div>
  </div>

  <!-- Stats hero -->
  <div class="prof-hero-stats">
    <div class="prof-hstat">
      <span class="prof-hstat-val">{{ $stats['orders'] ?? 0 }}</span>
      <span class="prof-hstat-lbl"><i class="fas fa-shopping-bag"></i> Commandes</span>
    </div>
    <div class="prof-hstat-sep"></div>
    <div class="prof-hstat">
      <span class="prof-hstat-val">{{ $stats['favorites'] ?? 0 }}</span>
      <span class="prof-hstat-lbl"><i class="fas fa-heart"></i> Favoris</span>
    </div>
    <div class="prof-hstat-sep"></div>
    <div class="prof-hstat">
      <span class="prof-hstat-val">{{ $stats['messages'] ?? 0 }}</span>
      <span class="prof-hstat-lbl"><i class="fas fa-comments"></i> Messages</span>
    </div>
  </div>
</div>

<!-- CONTENU PRINCIPAL -->
<div class="prof-grid">

  <!-- COLONNE GAUCHE -->
  <div class="prof-col-left">

    <!-- Informations personnelles -->
    <div class="prof-card prof-anim">
      <div class="prof-card-header">
        <div class="prof-card-icon" style="background:rgba(255,215,0,0.12);color:#FFD700;"><i class="fas fa-user"></i></div>
        <div>
          <h2 class="prof-card-title">Informations personnelles</h2>
          <p class="prof-card-sub">Modifiez vos données de profil</p>
        </div>
      </div>

      <form method="POST" enctype="multipart/form-data" action="{{ route('client.profile.update') }}" id="profileForm">
        @csrf
        @method('PUT')
        <input type="file" name="avatar" id="photoInput" accept="image/*" style="display:none;" onchange="previewPhoto(this)">

        <div class="prof-form-row">
          <div class="prof-field">
            <label class="prof-label"><i class="fas fa-user-tag"></i> Nom complet</label>
            <input type="text" name="name" class="prof-input" value="{{ old('name', $user->name) }}" required placeholder="Votre nom">
          </div>
          <div class="prof-field">
            <label class="prof-label"><i class="fas fa-phone"></i> Téléphone</label>
            <input type="tel" name="phone" class="prof-input" value="{{ old('phone', $user->phone) }}" placeholder="+225 XX XX XX XX">
          </div>
        </div>

        <div class="prof-field">
          <label class="prof-label"><i class="fas fa-envelope"></i> Email</label>
          <input type="email" class="prof-input prof-input-disabled" value="{{ $user->email }}" disabled>
        </div>

        <div class="prof-field">
          <label class="prof-label"><i class="fas fa-align-left"></i> Biographie</label>
          <textarea name="bio" class="prof-textarea" rows="4" placeholder="Décrivez-vous en quelques mots...">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <button type="submit" class="prof-btn-save">
          <i class="fas fa-save"></i> Enregistrer les modifications
        </button>
      </form>
    </div>

    <!-- Complétion profil -->
    <div class="prof-card prof-anim prof-completion-card">
      <div class="prof-card-header">
        <div class="prof-card-icon" style="background:rgba(168,85,247,0.12);color:#a855f7;"><i class="fas fa-tasks"></i></div>
        <div>
          <h2 class="prof-card-title">Complétion du profil</h2>
          <p class="prof-card-sub">Complétez votre profil pour une meilleure expérience</p>
        </div>
      </div>
      @php
      $items = [
        ['label'=>'Photo de profil',    'done'=>(bool)$user->avatar,                    'icon'=>'fa-camera'],
        ['label'=>'Nom complet',         'done'=>!empty($user->name),                    'icon'=>'fa-user'],
        ['label'=>'Numéro de téléphone', 'done'=>!empty($user->phone),                   'icon'=>'fa-phone'],
        ['label'=>'Biographie',          'done'=>!empty($user->bio),                     'icon'=>'fa-pen'],
        ['label'=>'Première commande',   'done'=>($stats['orders'] ?? 0) > 0,            'icon'=>'fa-shopping-bag'],
      ];
      $done = collect($items)->where('done', true)->count();
      $pct  = round($done / count($items) * 100);
      @endphp
      <div class="prof-pct-label">
        <span>Complété à <strong>{{ $pct }}%</strong></span>
        <span>{{ $done }}/{{ count($items) }} étapes</span>
      </div>
      <div class="prof-big-bar">
        <div class="prof-big-bar-fill" style="width:{{ $pct }}%;"></div>
      </div>
      <ul class="prof-checklist">
        @foreach($items as $it)
        <li class="prof-check-item {{ $it['done'] ? 'done' : '' }}">
          <div class="prof-check-icon"><i class="fas {{ $it['done'] ? 'fa-check-circle' : 'fa-circle' }}"></i></div>
          <span>{{ $it['label'] }}</span>
          @if(!$it['done'])
          <span class="prof-check-action">→ À compléter</span>
          @endif
        </li>
        @endforeach
      </ul>
    </div>
  </div>

  <!-- COLONNE DROITE -->
  <div class="prof-col-right">

    <!-- Carte membre -->
    <div class="prof-card prof-anim prof-avatar-card">
      <div class="prof-card-header">
        <div class="prof-card-icon" style="background:rgba(34,197,94,0.12);color:#22C55E;"><i class="fas fa-id-card"></i></div>
        <h2 class="prof-card-title">Votre carte membre</h2>
      </div>
      <div class="prof-member-card">
        <div class="prof-mc-glow"></div>
        <div class="prof-mc-logo">KOBLAN</div>
        <div class="prof-mc-avatar">
          @if($user->avatar)
            <img src="{{ asset('storage/'.$user->avatar) }}" alt="" onerror="this.parentNode.textContent='{{ $initials }}'">
          @else
            {{ $initials }}
          @endif
        </div>
        <div class="prof-mc-name">{{ $user->name }}</div>
        <div class="prof-mc-role">👤 Client KOBLAN</div>
        <div class="prof-mc-stats">
          <div><span>{{ $stats['orders'] ?? 0 }}</span><small>Cmdes</small></div>
          <div><span>{{ $stats['favorites'] ?? 0 }}</span><small>Favoris</small></div>
          <div><span>⭐</span><small>Vérifié</small></div>
        </div>
      </div>
    </div>

    <!-- Sécurité -->
    <div class="prof-card prof-anim">
      <div class="prof-card-header">
        <div class="prof-card-icon" style="background:rgba(239,68,68,0.12);color:#EF4444;"><i class="fas fa-shield-alt"></i></div>
        <div>
          <h2 class="prof-card-title">Sécurité</h2>
          <p class="prof-card-sub">Gérez vos accès et votre mot de passe</p>
        </div>
      </div>
      <div class="prof-security-list">
        <div class="prof-sec-item">
          <div class="prof-sec-icon ok"><i class="fas fa-lock"></i></div>
          <div class="prof-sec-info">
            <div class="prof-sec-title">Mot de passe</div>
            <div class="prof-sec-sub">Dernière modification : récemment</div>
          </div>
          <a href="#" class="prof-sec-btn" onclick="document.getElementById('changePwdSection').classList.toggle('open');return false;">Modifier</a>
        </div>

        <div class="prof-sec-item">
          <div class="prof-sec-icon ok"><i class="fas fa-envelope-open-text"></i></div>
          <div class="prof-sec-info">
            <div class="prof-sec-title">Email vérifié</div>
            <div class="prof-sec-sub">{{ $user->email }}</div>
          </div>
          <span class="prof-badge-ok"><i class="fas fa-check"></i> Vérifié</span>
        </div>

        <div class="prof-sec-item">
          <div class="prof-sec-icon warn"><i class="fas fa-mobile-alt"></i></div>
          <div class="prof-sec-info">
            <div class="prof-sec-title">Authentification 2FA</div>
            <div class="prof-sec-sub">Renforcez la sécurité de votre compte</div>
          </div>
          <span class="prof-badge-warn">Bientôt</span>
        </div>
      </div>

      <!-- Formulaire mot de passe -->
      <div class="prof-pwd-section" id="changePwdSection">
        <form method="POST" action="{{ route('client.profile.update') }}" class="prof-pwd-form">
          @csrf
          @method('PUT')
          <input type="hidden" name="change_password" value="1">
          <div class="prof-field">
            <label class="prof-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="prof-input" placeholder="••••••••" minlength="6">
          </div>
          <div class="prof-field">
            <label class="prof-label">Confirmer</label>
            <input type="password" name="password_confirmation" class="prof-input" placeholder="••••••••">
          </div>
          <button type="submit" class="prof-btn-save" style="background:linear-gradient(135deg,#EF4444,#b91c1c);">
            <i class="fas fa-lock"></i> Changer le mot de passe
          </button>
        </form>
      </div>
    </div>

    <!-- Actions rapides -->
    <div class="prof-card prof-anim">
      <div class="prof-card-header">
        <div class="prof-card-icon" style="background:rgba(59,130,246,0.12);color:#3b82f6;"><i class="fas fa-sliders-h"></i></div>
        <h2 class="prof-card-title">Actions rapides</h2>
      </div>
      <div class="prof-quick-list">
        <a href="{{ route('client.bookings') }}" class="prof-quick-item"><i class="fas fa-shopping-bag"></i><span>Mes commandes</span><i class="fas fa-chevron-right prof-qi-arr"></i></a>
        <a href="{{ route('client.favorites') }}" class="prof-quick-item"><i class="fas fa-heart"></i><span>Mes favoris</span><i class="fas fa-chevron-right prof-qi-arr"></i></a>
        <a href="{{ route('client.messages') }}" class="prof-quick-item"><i class="fas fa-comment-dots"></i><span>Messages</span><i class="fas fa-chevron-right prof-qi-arr"></i></a>
        <a href="{{ route('services.index') }}" class="prof-quick-item"><i class="fas fa-search"></i><span>Explorer les services</span><i class="fas fa-chevron-right prof-qi-arr"></i></a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="prof-quick-item prof-qi-danger">
          <i class="fas fa-sign-out-alt"></i><span>Déconnexion</span><i class="fas fa-chevron-right prof-qi-arr"></i>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      </div>
    </div>

  </div>
</div>
</div><!-- /prof-wrap -->

<style>
.prof-wrap{position:relative;z-index:2;padding:1.5rem;display:flex;flex-direction:column;gap:1.75rem;}
.prof-flash{display:flex;align-items:center;gap:0.75rem;padding:1rem 1.5rem;border-radius:14px;font-weight:600;font-size:0.9rem;border:1px solid;}
.prof-flash-success{background:rgba(34,197,94,0.1);border-color:rgba(34,197,94,0.3);color:#22C55E;}
.prof-flash-error{background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.3);color:#EF4444;}
.prof-hero{position:relative;overflow:hidden;background:rgba(8,8,14,0.92);backdrop-filter:blur(32px);border:1px solid rgba(255,215,0,0.14);border-radius:28px;padding:2.5rem;display:flex;flex-wrap:wrap;gap:2rem;align-items:center;box-shadow:0 30px 80px rgba(0,0,0,0.5),inset 0 1px 0 rgba(255,255,255,0.04);}
.prof-hero-bg-glow{position:absolute;top:-80px;right:-80px;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(255,215,0,0.07),transparent 70%);pointer-events:none;}
.prof-identity{display:flex;align-items:center;gap:2rem;flex:1;}
.prof-avatar-ring{position:relative;flex-shrink:0;}
.prof-avatar{width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#FFD700,#F77F00);display:flex;align-items:center;justify-content:center;font-size:2.2rem;font-weight:900;color:#000;overflow:hidden;border:3px solid rgba(255,215,0,0.5);box-shadow:0 0 30px rgba(255,215,0,0.3);}
.prof-avatar img{width:100%;height:100%;object-fit:cover;}
.prof-avatar-edit{position:absolute;bottom:2px;right:2px;width:30px;height:30px;border-radius:50%;background:#FFD700;color:#000;display:flex;align-items:center;justify-content:center;font-size:0.75rem;cursor:pointer;border:2px solid #060608;transition:transform 0.2s;}
.prof-avatar-edit:hover{transform:scale(1.2);}
.prof-name{font-size:1.9rem;font-weight:900;color:#fff;margin:0 0 0.4rem;font-family:'Syne',sans-serif;}
.prof-role-badge{display:inline-flex;align-items:center;gap:0.4rem;background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#22C55E;border-radius:99px;padding:0.2rem 0.75rem;font-size:0.75rem;font-weight:700;margin-bottom:0.4rem;}
.prof-member-since{color:#777;font-size:0.82rem;margin-bottom:0.5rem;}
.prof-bio-display{color:#aaa;font-size:0.85rem;max-width:400px;margin:0;}
.prof-hero-stats{display:flex;align-items:center;gap:1.5rem;margin-left:auto;}
.prof-hstat{text-align:center;}
.prof-hstat-val{display:block;font-size:2rem;font-weight:900;color:#fff;font-family:'Syne',sans-serif;}
.prof-hstat-lbl{font-size:0.75rem;color:#777;display:flex;align-items:center;gap:0.3rem;justify-content:center;}
.prof-hstat-sep{width:1px;height:50px;background:rgba(255,255,255,0.08);}
.prof-grid{display:grid;grid-template-columns:1fr 340px;gap:1.75rem;}
.prof-col-left,.prof-col-right{display:flex;flex-direction:column;gap:1.5rem;}
.prof-card{background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:2rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);}
.prof-anim{animation:pfUp 0.6s ease both;}
@keyframes pfUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
.prof-card-header{display:flex;align-items:center;gap:1rem;margin-bottom:1.75rem;}
.prof-card-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;}
.prof-card-title{font-size:1.1rem;font-weight:800;color:#fff;margin:0;}
.prof-card-sub{font-size:0.78rem;color:#777;margin:0.2rem 0 0;}
.prof-form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;}
.prof-field{display:flex;flex-direction:column;gap:0.4rem;margin-bottom:1rem;}
.prof-label{font-size:0.78rem;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;display:flex;align-items:center;gap:0.35rem;}
.prof-input,.prof-textarea{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.75rem 1rem;color:#f0f0f0;font-size:0.9rem;outline:none;transition:border-color 0.2s,box-shadow 0.2s;width:100%;box-sizing:border-box;font-family:inherit;}
.prof-input:focus,.prof-textarea:focus{border-color:rgba(255,215,0,0.5);box-shadow:0 0 0 3px rgba(255,215,0,0.08);}
.prof-input-disabled{opacity:0.4;cursor:not-allowed;}
.prof-textarea{resize:vertical;min-height:100px;}
.prof-btn-save{display:inline-flex;align-items:center;gap:0.6rem;padding:0.85rem 2rem;border-radius:14px;border:none;cursor:pointer;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;font-weight:800;font-size:0.92rem;font-family:inherit;transition:transform 0.2s,box-shadow 0.2s;}
.prof-btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,215,0,0.4);}
.prof-pct-label{display:flex;justify-content:space-between;font-size:0.8rem;color:#aaa;margin-bottom:0.6rem;}
.prof-big-bar{width:100%;height:8px;background:rgba(255,255,255,0.05);border-radius:4px;overflow:hidden;margin-bottom:1.5rem;}
.prof-big-bar-fill{height:100%;background:linear-gradient(90deg,#FFD700,#a855f7);border-radius:4px;transition:width 1.2s ease;}
.prof-checklist{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.6rem;}
.prof-check-item{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.75rem;border-radius:10px;border:1px solid rgba(255,255,255,0.05);background:rgba(255,255,255,0.02);}
.prof-check-item.done{background:rgba(34,197,94,0.05);border-color:rgba(34,197,94,0.15);}
.prof-check-icon{font-size:1rem;color:#555;flex-shrink:0;}
.prof-check-item.done .prof-check-icon{color:#22C55E;}
.prof-check-item span{flex:1;font-size:0.85rem;color:#ccc;}
.prof-check-action{font-size:0.72rem;color:#F59E0B;white-space:nowrap;}
.prof-member-card{position:relative;overflow:hidden;background:linear-gradient(135deg,rgba(20,20,35,0.98),rgba(12,12,25,0.98));border:1px solid rgba(255,215,0,0.25);border-radius:20px;padding:2rem;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.5);}
.prof-mc-glow{position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(255,215,0,0.15),transparent 70%);}
.prof-mc-logo{font-size:0.65rem;letter-spacing:0.25em;color:#FFD700;text-transform:uppercase;font-weight:900;margin-bottom:1.25rem;opacity:0.8;}
.prof-mc-avatar{width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#FFD700,#F77F00);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:900;color:#000;margin:0 auto 0.75rem;overflow:hidden;border:2px solid rgba(255,215,0,0.5);}
.prof-mc-avatar img{width:100%;height:100%;object-fit:cover;}
.prof-mc-name{font-weight:800;font-size:1.05rem;color:#fff;margin-bottom:0.25rem;}
.prof-mc-role{font-size:0.78rem;color:#aaa;margin-bottom:1.5rem;}
.prof-mc-stats{display:flex;justify-content:center;gap:2rem;border-top:1px solid rgba(255,255,255,0.06);padding-top:1rem;}
.prof-mc-stats div{text-align:center;}
.prof-mc-stats span{display:block;font-size:1.3rem;font-weight:900;color:#FFD700;}
.prof-mc-stats small{font-size:0.68rem;color:#777;text-transform:uppercase;}
.prof-security-list{display:flex;flex-direction:column;gap:0.75rem;}
.prof-sec-item{display:flex;align-items:center;gap:0.85rem;padding:0.75rem;border-radius:12px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);}
.prof-sec-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.9rem;flex-shrink:0;}
.prof-sec-icon.ok{background:rgba(34,197,94,0.1);color:#22C55E;}
.prof-sec-icon.warn{background:rgba(245,158,11,0.1);color:#F59E0B;}
.prof-sec-info{flex:1;}
.prof-sec-title{font-size:0.85rem;font-weight:700;color:#ddd;}
.prof-sec-sub{font-size:0.72rem;color:#777;}
.prof-sec-btn{font-size:0.75rem;color:#FFD700;text-decoration:none;font-weight:700;white-space:nowrap;padding:0.3rem 0.7rem;border:1px solid rgba(255,215,0,0.25);border-radius:8px;}
.prof-sec-btn:hover{background:rgba(255,215,0,0.08);}
.prof-badge-ok{font-size:0.72rem;color:#22C55E;border:1px solid rgba(34,197,94,0.3);padding:0.25rem 0.6rem;border-radius:8px;white-space:nowrap;}
.prof-badge-warn{font-size:0.72rem;color:#F59E0B;border:1px solid rgba(245,158,11,0.3);padding:0.25rem 0.6rem;border-radius:8px;white-space:nowrap;}
.prof-pwd-section{max-height:0;overflow:hidden;transition:max-height 0.5s ease,margin-top 0.3s;margin-top:0;}
.prof-pwd-section.open{max-height:400px;margin-top:1.25rem;}
.prof-pwd-form{display:flex;flex-direction:column;gap:0.75rem;padding:1.25rem;background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.15);border-radius:14px;}
.prof-quick-list{display:flex;flex-direction:column;gap:0.4rem;}
.prof-quick-item{display:flex;align-items:center;gap:0.85rem;padding:0.85rem 1rem;border-radius:12px;text-decoration:none;color:#ddd;font-size:0.88rem;font-weight:600;border:1px solid transparent;transition:all 0.2s;}
.prof-quick-item i:first-child{width:20px;text-align:center;color:#FFD700;}
.prof-quick-item span{flex:1;}
.prof-qi-arr{font-size:0.7rem;color:#555;}
.prof-quick-item:hover{background:rgba(255,255,255,0.04);border-color:rgba(255,255,255,0.07);}
.prof-qi-danger i:first-child{color:#EF4444;}
.prof-qi-danger:hover{background:rgba(239,68,68,0.06);border-color:rgba(239,68,68,0.15);}
@media(max-width:1024px){.prof-grid{grid-template-columns:1fr;}}
@media(max-width:640px){.prof-hero{flex-direction:column;}.prof-hero-stats{margin-left:0;}.prof-identity{flex-direction:column;text-align:center;}.prof-form-row{grid-template-columns:1fr;}}
</style>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
(function(){
  const canvas = document.getElementById('prof-canvas');
  if(!canvas||typeof THREE==='undefined') return;
  const scene = new THREE.Scene();
  const cam   = new THREE.PerspectiveCamera(60, innerWidth/innerHeight, 0.1, 1000);
  cam.position.set(0, 0, 60);
  const renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:true});
  renderer.setSize(innerWidth, innerHeight);
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));

  scene.add(new THREE.AmbientLight(0xffffff, 0.2));
  const L1=new THREE.PointLight(0xFFD700,3,200); L1.position.set(40,40,30); scene.add(L1);
  const L2=new THREE.PointLight(0xa855f7,2,150); L2.position.set(-40,-20,20); scene.add(L2);
  const L3=new THREE.PointLight(0x22C55E,1.5,150); L3.position.set(0,-40,10); scene.add(L3);

  const objs=[];
  function add(geo,color,x,y,z,wire=true){
    const m=new THREE.Mesh(geo,new THREE.MeshPhongMaterial({color,wireframe:wire,transparent:true,opacity:wire?0.15:0.08,emissive:color,emissiveIntensity:0.3}));
    m.position.set(x,y,z);
    m.userData={rx:(Math.random()-.5)*0.007,ry:(Math.random()-.5)*0.01,fs:Math.random()*0.012+0.006,ft:0};
    scene.add(m); objs.push(m);
  }

  // DNA HELIX
  for(let i=0;i<30;i++){
    const angle=i*0.45, y=i*1.2-18, r=6+Math.sin(angle)*1.5;
    const m=new THREE.Mesh(new THREE.TorusGeometry(0.8,0.08,6,16),
      new THREE.MeshPhongMaterial({color:i%2===0?0xFFD700:0xa855f7,transparent:true,opacity:0.5,emissive:i%2===0?0xFFD700:0xa855f7,emissiveIntensity:0.6}));
    m.position.set(Math.cos(angle)*r, y, Math.sin(angle)*r-20);
    m.rotation.set(Math.random(),Math.random(),Math.random());
    m.userData={rx:0.008,ry:0.005,fs:0.004,ft:i*0.3};
    scene.add(m); objs.push(m);
  }

  add(new THREE.TorusKnotGeometry(10,0.35,150,10),0xFFD700,0,0,-35);
  add(new THREE.TorusGeometry(30,0.2,6,100),0xffffff,0,0,-40);
  add(new THREE.IcosahedronGeometry(6,1),0x22C55E,-28,16,-10);
  add(new THREE.IcosahedronGeometry(4,1),0x3b82f6,28,-16,-8);
  add(new THREE.OctahedronGeometry(5,0),0xa855f7,22,18,-5);

  const pN=2000,pP=new Float32Array(pN*3),pC=new Float32Array(pN*3);
  const pal=[new THREE.Color(0xFFD700),new THREE.Color(0xa855f7),new THREE.Color(0x22C55E),new THREE.Color(0x3b82f6)];
  for(let i=0;i<pN;i++){
    pP[i*3]=(Math.random()-.5)*200; pP[i*3+1]=(Math.random()-.5)*200; pP[i*3+2]=(Math.random()-.5)*120;
    const c=pal[Math.floor(Math.random()*4)]; pC[i*3]=c.r; pC[i*3+1]=c.g; pC[i*3+2]=c.b;
  }
  const pGeo=new THREE.BufferGeometry();
  pGeo.setAttribute('position',new THREE.BufferAttribute(pP,3));
  pGeo.setAttribute('color',new THREE.BufferAttribute(pC,3));
  scene.add(new THREE.Points(pGeo,new THREE.PointsMaterial({size:0.8,vertexColors:true,transparent:true,opacity:0.6,blending:THREE.AdditiveBlending,depthWrite:false})));

  let mx=0,my=0,t=0;
  document.addEventListener('mousemove',e=>{mx=(e.clientX/innerWidth-.5)*2;my=(e.clientY/innerHeight-.5)*2;});

  (function animate(){
    requestAnimationFrame(animate); t+=0.01;
    objs.forEach(o=>{o.rotation.x+=o.userData.rx;o.rotation.y+=o.userData.ry;o.userData.ft+=o.userData.fs;o.position.y+=Math.sin(o.userData.ft)*0.012;});
    L1.intensity=2+Math.sin(t*1.3)*1; L2.intensity=1.5+Math.sin(t*0.9)*0.7; L3.intensity=1+Math.sin(t*1.1)*0.5;
    cam.position.x+=(mx*10-cam.position.x)*0.025;
    cam.position.y+=(-my*6-cam.position.y)*0.025;
    cam.lookAt(0,0,0);
    const pa=pGeo.attributes.position.array;
    for(let i=1;i<pN*3;i+=3){pa[i]-=0.05;if(pa[i]<-100)pa[i]=100;}
    pGeo.attributes.position.needsUpdate=true;
    renderer.render(scene,cam);
  })();

  window.addEventListener('resize',()=>{cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix();renderer.setSize(innerWidth,innerHeight);});
})();

function previewPhoto(input){
  if(!input.files||!input.files[0]) return;
  const r=new FileReader();
  r.onload=e=>{
    const av=document.getElementById('profAvatarDisplay');
    av.innerHTML=`<img src="${e.target.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover;">`;
    document.getElementById('profileForm').submit();
  };
  r.readAsDataURL(input.files[0]);
}
</script>
@endsection
