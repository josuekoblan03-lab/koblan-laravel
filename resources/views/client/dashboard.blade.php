@extends('layouts.dashboard')

@section('content')
@php
$user = Auth::user();
$heure = (int)date('G');
$salutation = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');
$emoji = $heure < 6 ? '🌙' : ($heure < 12 ? '☀️' : ($heure < 18 ? '🌤️' : '🌙'));
@endphp

<!-- CANVAS THREE.JS — BACKGROUND 3D -->
<canvas id="dash-canvas-bg" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;opacity:0.55;"></canvas>

<div class="db-wrap">

  <!-- HERO SECTION -->
  <div class="db-hero db-reveal">
    <div class="db-hero-left">
      <div class="db-hero-emoji">{{ $emoji }}</div>
      <h1 class="db-hero-title">{{ $salutation }}, <span class="db-gold">{{ $user->name }}</span> !</h1>
      <p class="db-hero-sub">Prêt à trouver l'expert qu'il vous faut ? Explorez nos services ou suivez vos commandes.</p>
      <div class="db-hero-actions">
        <a href="{{ route('services.index') }}" class="db-btn-primary"><i class="fas fa-search"></i> Explorer les services</a>
        <a href="{{ route('client.bookings') }}" class="db-btn-ghost"><i class="fas fa-list"></i> Mes commandes</a>
      </div>
    </div>
    @if(!$user->isPrestataire())
    <div class="db-hero-right db-reveal-r">
      <div class="db-promo-card">
        <div class="db-promo-glow"></div>
        <div class="db-promo-icon"><i class="fas fa-rocket"></i></div>
        <h3>Devenez Prestataire</h3>
        <p>Proposez vos services et gagnez de l'argent sur KOBLAN.</p>
        <a href="#" onclick="event.preventDefault(); document.getElementById('upgrade-form').submit();" class="db-btn-primary db-btn-sm">
          <i class="fas fa-arrow-right"></i> S'inscrire
        </a>
      </div>
    </div>
    @endif
  </div>

  <!-- KPI CARDS -->
  <div class="db-kpi-grid db-reveal">
    <div class="db-kpi-card" style="--kc:#FFD700;">
      <div class="db-kpi-icon" style="background:rgba(255,215,0,0.12);color:#FFD700;"><i class="fas fa-shopping-bag"></i></div>
      <div class="db-kpi-body">
        <span class="db-kpi-val" data-target="{{ $stats['total_orders'] ?? 0 }}">{{ $stats['total_orders'] ?? 0 }}</span>
        <span class="db-kpi-lbl">Commandes totales</span>
      </div>
      <div class="db-kpi-orb" style="background:radial-gradient(circle,rgba(255,215,0,0.15),transparent 70%);"></div>
    </div>

    <div class="db-kpi-card" style="--kc:#F59E0B;">
      <div class="db-kpi-icon" style="background:rgba(245,158,11,0.12);color:#F59E0B;"><i class="fas fa-clock"></i></div>
      <div class="db-kpi-body">
        <span class="db-kpi-val" data-target="{{ $stats['active_orders'] ?? 0 }}">{{ $stats['active_orders'] ?? 0 }}</span>
        <span class="db-kpi-lbl">En cours</span>
      </div>
      <div class="db-kpi-orb" style="background:radial-gradient(circle,rgba(245,158,11,0.15),transparent 70%);"></div>
    </div>

    <div class="db-kpi-card" style="--kc:#EF4444;">
      <div class="db-kpi-icon" style="background:rgba(239,68,68,0.12);color:#EF4444;"><i class="fas fa-heart"></i></div>
      <div class="db-kpi-body">
        <span class="db-kpi-val" data-target="{{ $stats['favorites'] ?? 0 }}">{{ $stats['favorites'] ?? 0 }}</span>
        <span class="db-kpi-lbl">Favoris</span>
      </div>
      <div class="db-kpi-orb" style="background:radial-gradient(circle,rgba(239,68,68,0.15),transparent 70%);"></div>
    </div>

    <div class="db-kpi-card" style="--kc:#22C55E;">
      <div class="db-kpi-icon" style="background:rgba(34,197,94,0.12);color:#22C55E;"><i class="fas fa-wallet"></i></div>
      <div class="db-kpi-body">
        <span class="db-kpi-val">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</span>
        <span class="db-kpi-lbl">Solde FCFA</span>
      </div>
      <div class="db-kpi-orb" style="background:radial-gradient(circle,rgba(34,197,94,0.15),transparent 70%);"></div>
    </div>
  </div>

  <!-- MAIN GRID -->
  <div class="db-main-grid">

    <!-- COLONNE GAUCHE -->
    <div class="db-col-left">

      <!-- Commandes récentes -->
      <div class="db-glass-panel db-reveal">
        <div class="db-panel-header">
          <div class="db-panel-title"><i class="fas fa-receipt"></i> Dernières commandes</div>
          <a href="{{ route('client.bookings') }}" class="db-link-more">Tout voir <i class="fas fa-arrow-right"></i></a>
        </div>

        @if($orders->isNotEmpty())
        <div class="db-orders-table-wrap">
          <table class="db-table">
            <thead>
              <tr>
                <th>Réf.</th>
                <th>Service</th>
                <th>Prestataire</th>
                <th>Montant</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
            @foreach($orders->take(5) as $c)
            @php
              $statusMap = [
                'pending'     =>['#F59E0B','En attente','fa-clock'],
                'accepted'    =>['#3B82F6','Acceptée','fa-thumbs-up'],
                'confirmed'   =>['#3B82F6','Acceptée','fa-thumbs-up'],
                'in_progress' =>['#A855F7','En cours','fa-spinner fa-spin'],
                'completed'   =>['#22C55E','Terminée','fa-check-circle'],
                'cancelled'   =>['#EF4444','Annulée','fa-times-circle'],
              ];
              [$sc,$sl,$si] = $statusMap[$c->status] ?? ['#888','Inconnu','fa-circle'];
              $p_title = $c->prestation ? $c->prestation->title : 'Service Indisponible';
              $prest = $c->prestataire;
              $amount = $c->total_amount ?? $c->amount ?? 0;
            @endphp
            <tr class="db-tr">
              <td class="db-td-ref">#{{ str_pad($c->id,4,'0',STR_PAD_LEFT) }}</td>
              <td class="db-td-main">{{ \Str::limit($p_title, 22) }}</td>
              <td class="db-td-sub">{{ $prest ? \Str::limit($prest->name, 12) : '—' }}</td>
              <td class="db-td-price">{{ number_format($amount, 0, ',', ' ') }} <small>FCFA</small></td>
              <td>
                <span class="db-badge" style="color:{{ $sc }};background:{{ $sc }}18;border-color:{{ $sc }}55;">
                  <i class="fas {{ $si }}"></i> {{ $sl }}
                </span>
              </td>
            </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="db-empty">
          <div class="db-empty-icon">🛍️</div>
          <p>Aucune commande pour le moment</p>
          <a href="{{ route('services.index') }}" class="db-btn-primary db-btn-sm">Découvrir les services</a>
        </div>
        @endif
      </div>

      <!-- Recommandations -->
      <div class="db-glass-panel db-reveal">
        <div class="db-panel-header">
          <div class="db-panel-title"><i class="fas fa-magic"></i> Recommandé pour vous</div>
          <a href="{{ route('services.index') }}" class="db-link-more">Explorer <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="db-reco-grid">
          @if($recommendations->isEmpty())
          <div class="db-empty" style="grid-column:1/-1;padding:2rem 0;">
            <div class="db-empty-icon">⭐</div>
            <p>Les recommandations apparaîtront ici</p>
          </div>
          @else
          @foreach($recommendations->take(4) as $r)
          <a href="{{ route('services.show', $r->id) }}" class="db-reco-card">
            <div class="db-reco-img">
              @if($r->mainMedia)
                <img src="{{ $r->getImageUrl() }}" alt="{{ $r->title }}">
              @else
                <div class="db-reco-placeholder"><i class="fas fa-star"></i></div>
              @endif
            </div>
            <div class="db-reco-body">
              <div class="db-reco-title">{{ \Str::limit($r->title, 28) }}</div>
              <div class="db-reco-meta">
                <span class="db-reco-price">{{ number_format($r->price, 0, ',', ' ') }} FCFA</span>
                <span class="db-reco-stars"><i class="fas fa-star"></i> {{ number_format($r->user->rating_avg ?? 4.5, 1) }}</span>
              </div>
            </div>
          </a>
          @endforeach
          @endif
        </div>
      </div>
    </div>

    <!-- COLONNE DROITE (Sidebar) -->
    <div class="db-col-right">

      <!-- Profil -->
      <div class="db-glass-panel db-panel-profile db-reveal">
        <div class="db-profile-avatar">
          @if($user->avatar)
            <img src="{{ asset('storage/'.$user->avatar) }}" alt="photo">
          @else
            {{ $user->getInitials() }}
          @endif
        </div>
        <div class="db-profile-info">
          <div class="db-profile-name">{{ $user->name }}</div>
          <div class="db-profile-role"><i class="fas fa-circle" style="color:#22C55E;font-size:0.5rem;"></i> Client actif</div>
        </div>
        <div class="db-profile-bar-wrap">
          <div style="display:flex;justify-content:space-between;font-size:0.75rem;color:#aaa;margin-bottom:0.4rem;">
            <span>Profil complété</span><span style="color:#FFD700;font-weight:700;">80%</span>
          </div>
          <div class="db-bar-track"><div class="db-bar-fill" style="width:80%;"></div></div>
        </div>
        <a href="{{ route('client.profile') }}" class="db-btn-ghost db-btn-sm" style="width:100%;justify-content:center;margin-top:0.75rem;">
          <i class="fas fa-user-edit"></i> Modifier mon profil
        </a>
      </div>

      <!-- Wallet -->
      <div class="db-glass-panel db-panel-wallet db-reveal">
        <div class="db-wallet-label"><i class="fas fa-wallet"></i> Mon portefeuille</div>
        <div class="db-wallet-amount">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} <span>FCFA</span></div>
        <div class="db-wallet-actions">
          <button class="db-btn-primary db-btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-plus"></i> Recharger</button>
          <button class="db-btn-ghost db-btn-sm" style="flex:1;justify-content:center;"><i class="fas fa-history"></i> Historique</button>
        </div>
      </div>

      <!-- Favoris rapides -->
      <div class="db-glass-panel db-reveal">
        <div class="db-panel-header">
          <div class="db-panel-title"><i class="fas fa-heart" style="color:#EF4444;"></i> Mes Favoris</div>
          <a href="{{ route('client.favorites') }}" class="db-link-more">Voir tout</a>
        </div>
        @if($favorites->isNotEmpty())
        <div class="db-fav-list">
          @foreach($favorites->take(4) as $f)
          <a href="{{ route('services.show', $f->id) }}" class="db-fav-item">
            <div class="db-fav-thumb">
              @if($f->mainMedia)
                <img src="{{ $f->getImageUrl() }}" alt="">
              @else
                <i class="fas fa-briefcase"></i>
              @endif
            </div>
            <div class="db-fav-info">
              <div class="db-fav-title">{{ \Str::limit($f->title, 22) }}</div>
              <div class="db-fav-cat">{{ $f->serviceType->category->name ?? '' }}</div>
            </div>
            <i class="fas fa-chevron-right" style="color:#555;font-size:0.7rem;"></i>
          </a>
          @endforeach
        </div>
        @else
        <div class="db-empty" style="padding:1.5rem 0;">
          <div class="db-empty-icon">💛</div>
          <p style="font-size:0.82rem;">Aucun favori enregistré</p>
          <a href="{{ route('services.index') }}" class="db-btn-ghost db-btn-sm" style="margin-top:0.5rem;">Explorer</a>
        </div>
        @endif
      </div>

      <!-- Support -->
      <a href="{{ route('contact') }}" class="db-glass-panel db-panel-support db-reveal">
        <div class="db-support-icon"><i class="fas fa-headset"></i></div>
        <div>
          <div class="db-support-title">Besoin d'aide ?</div>
          <div class="db-support-sub">Support disponible 24h/7j</div>
        </div>
        <i class="fas fa-arrow-right" style="margin-left:auto;color:#FFD700;"></i>
      </a>

    </div><!-- /col-right -->
  </div><!-- /main-grid -->
</div><!-- /db-wrap -->

<style>
:root { --db-bg:#060608; --db-card:rgba(14,14,20,0.88); --db-border:rgba(255,255,255,0.07); --db-gold:#FFD700; --db-text:#f0f0f0; --db-muted:#888; --db-radius:20px; }
.db-wrap { position:relative;z-index:2;padding:1.5rem;display:flex;flex-direction:column;gap:1.75rem; }
.db-hero { display:flex;gap:2rem;align-items:center;background:rgba(10,10,16,0.85);backdrop-filter:blur(30px);border:1px solid rgba(255,215,0,0.12);border-radius:28px;padding:2.5rem;box-shadow:0 30px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);position:relative;overflow:hidden; }
.db-hero::before { content:'';position:absolute;top:-60px;left:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(255,215,0,0.08),transparent 70%);border-radius:50%; }
.db-hero-left { flex:1; }
.db-hero-emoji { font-size:2.5rem;margin-bottom:0.75rem; }
.db-hero-title { font-size:2.4rem;font-weight:900;color:#fff;margin:0 0 0.75rem;font-family:'Syne','Space Grotesk',sans-serif;line-height:1.15; }
.db-gold { color:#FFD700; }
.db-hero-sub { color:#aaa;font-size:1rem;margin-bottom:1.5rem;max-width:500px;line-height:1.6; }
.db-hero-actions { display:flex;gap:1rem;flex-wrap:wrap; }
.db-hero-right { flex-shrink:0; }
.db-promo-card { position:relative;overflow:hidden;background:rgba(255,215,0,0.05);border:1px solid rgba(255,215,0,0.25);border-radius:20px;padding:1.75rem;min-width:240px;text-align:center; }
.db-promo-glow { position:absolute;top:-30px;right:-30px;width:100px;height:100px;background:radial-gradient(circle,rgba(255,215,0,0.2),transparent 70%); }
.db-promo-icon { width:56px;height:56px;border-radius:16px;background:rgba(255,215,0,0.15);color:#FFD700;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 1rem; }
.db-promo-card h3 { color:#fff;font-weight:800;font-size:1.1rem;margin:0 0 0.5rem; }
.db-promo-card p  { color:#aaa;font-size:0.82rem;margin:0 0 1.25rem; }
.db-btn-primary { display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.4rem;border-radius:12px;text-decoration:none;font-weight:700;font-size:0.88rem;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;transition:transform 0.2s,box-shadow 0.2s;border:none;cursor:pointer; }
.db-btn-primary:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,215,0,0.35);color:#000; }
.db-btn-ghost { display:inline-flex;align-items:center;gap:0.5rem;padding:0.65rem 1.4rem;border-radius:12px;text-decoration:none;font-weight:600;font-size:0.88rem;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.12);color:#ddd;transition:all 0.2s;cursor:pointer; }
.db-btn-ghost:hover { border-color:#FFD700;color:#FFD700;background:rgba(255,215,0,0.06); }
.db-btn-sm { padding:0.45rem 1rem;font-size:0.8rem;border-radius:10px; }
.db-kpi-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem; }
.db-kpi-card { position:relative;overflow:hidden;background:rgba(12,12,18,0.9);border:1px solid var(--db-border);border-radius:20px;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;transition:transform 0.3s,border-color 0.3s;box-shadow:0 8px 32px rgba(0,0,0,0.4); }
.db-kpi-card:hover { transform:translateY(-4px); }
.db-kpi-icon { width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0; }
.db-kpi-body { flex:1; }
.db-kpi-val { display:block;font-size:2rem;font-weight:900;color:#fff;line-height:1;font-family:'Syne','Space Grotesk',sans-serif; }
.db-kpi-lbl { font-size:0.78rem;color:#777;margin-top:0.25rem;text-transform:uppercase;letter-spacing:0.05em; }
.db-kpi-orb { position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;pointer-events:none; }
.db-main-grid { display:grid;grid-template-columns:1fr 320px;gap:1.75rem; }
.db-col-left  { display:flex;flex-direction:column;gap:1.75rem; }
.db-col-right { display:flex;flex-direction:column;gap:1.25rem; }
.db-glass-panel { background:rgba(12,12,20,0.88);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,0.4); }
.db-panel-header { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem; }
.db-panel-title { color:#fff;font-weight:700;font-size:1rem;display:flex;align-items:center;gap:0.6rem; }
.db-panel-title i { color:#FFD700; }
.db-link-more { font-size:0.8rem;color:#FFD700;text-decoration:none;opacity:0.8; }
.db-link-more:hover { opacity:1; }
.db-orders-table-wrap { overflow-x:auto; }
.db-table { width:100%;border-collapse:collapse;font-size:0.85rem; }
.db-table thead tr { border-bottom:1px solid rgba(255,255,255,0.06); }
.db-table th { padding:0.75rem 0.5rem;color:#555;font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;text-align:left; }
.db-tr { border-bottom:1px solid rgba(255,255,255,0.03);transition:background 0.2s; }
.db-tr:hover { background:rgba(255,255,255,0.03); }
.db-tr td { padding:0.85rem 0.5rem; }
.db-td-ref { color:#FFD700;font-weight:700;font-family:monospace; }
.db-td-main { color:#f0f0f0;font-weight:600; }
.db-td-sub  { color:#777; }
.db-td-price { font-weight:700;color:#fff; }
.db-td-price small { font-size:0.7rem;color:#666; }
.db-badge { display:inline-flex;align-items:center;gap:0.35rem;padding:0.3rem 0.75rem;border-radius:99px;border:1px solid;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;white-space:nowrap; }
.db-reco-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:1rem; }
.db-reco-card { text-decoration:none;border-radius:16px;overflow:hidden;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);transition:transform 0.3s,border-color 0.3s; }
.db-reco-card:hover { transform:translateY(-4px);border-color:rgba(255,215,0,0.25); }
.db-reco-img { height:110px;overflow:hidden;background:rgba(255,255,255,0.04); }
.db-reco-img img { width:100%;height:100%;object-fit:cover;transition:transform 0.5s; }
.db-reco-card:hover .db-reco-img img { transform:scale(1.08); }
.db-reco-placeholder { width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#FFD700;font-size:2rem;opacity:0.4; }
.db-reco-body { padding:0.85rem; }
.db-reco-title { color:#f0f0f0;font-weight:600;font-size:0.85rem;margin-bottom:0.4rem; }
.db-reco-meta { display:flex;justify-content:space-between;align-items:center; }
.db-reco-price { color:#FFD700;font-weight:700;font-size:0.82rem; }
.db-reco-stars { color:#F59E0B;font-size:0.78rem; }
.db-panel-profile { text-align:center; }
.db-profile-avatar { width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#FFD700,#F77F00);display:flex;align-items:center;justify-content:center;color:#000;font-weight:900;font-size:1.4rem;margin:0 auto 0.75rem;overflow:hidden;border:3px solid rgba(255,215,0,0.4); }
.db-profile-avatar img { width:100%;height:100%;object-fit:cover; }
.db-profile-name { color:#fff;font-weight:700;font-size:1rem; }
.db-profile-role { color:#777;font-size:0.78rem;margin-top:0.25rem;display:flex;align-items:center;gap:0.4rem;justify-content:center; }
.db-profile-bar-wrap { margin-top:1rem; }
.db-bar-track { width:100%;height:6px;background:rgba(255,255,255,0.06);border-radius:3px;overflow:hidden; }
.db-bar-fill  { height:100%;background:linear-gradient(90deg,#FFD700,#F77F00);border-radius:3px;transition:width 1s ease; }
.db-panel-wallet { background:linear-gradient(135deg,rgba(20,20,30,0.95),rgba(12,12,18,0.95));border-color:rgba(255,215,0,0.2);text-align:center; }
.db-wallet-label { color:#FFD700;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.5rem; }
.db-wallet-amount { font-size:2.2rem;font-weight:900;color:#fff;margin-bottom:1.25rem;font-family:'Syne',sans-serif; }
.db-wallet-amount span { font-size:0.9rem;color:#777; }
.db-wallet-actions { display:flex;gap:0.75rem; }
.db-fav-list { display:flex;flex-direction:column;gap:0.5rem; }
.db-fav-item { display:flex;align-items:center;gap:0.75rem;padding:0.6rem;border-radius:12px;text-decoration:none;transition:background 0.2s; }
.db-fav-item:hover { background:rgba(255,255,255,0.04); }
.db-fav-thumb { width:38px;height:38px;border-radius:10px;background:rgba(255,215,0,0.1);display:flex;align-items:center;justify-content:center;color:#FFD700;overflow:hidden;flex-shrink:0; }
.db-fav-thumb img { width:100%;height:100%;object-fit:cover; }
.db-fav-info { flex:1; }
.db-fav-title { color:#ddd;font-size:0.85rem;font-weight:600; }
.db-fav-cat   { color:#666;font-size:0.72rem; }
.db-panel-support { display:flex;align-items:center;gap:1rem;text-decoration:none;transition:border-color 0.3s,transform 0.3s;cursor:pointer; }
.db-panel-support:hover { border-color:rgba(255,215,0,0.35);transform:translateY(-2px); }
.db-support-icon { width:44px;height:44px;border-radius:14px;background:rgba(255,215,0,0.1);display:flex;align-items:center;justify-content:center;color:#FFD700;font-size:1.3rem;flex-shrink:0; }
.db-support-title { color:#fff;font-weight:700;font-size:0.9rem; }
.db-support-sub   { color:#777;font-size:0.75rem; }
.db-empty { display:flex;flex-direction:column;align-items:center;padding:3rem 0;gap:0.5rem; }
.db-empty-icon { font-size:3rem;opacity:0.4; }
.db-empty p { color:#777;font-size:0.88rem; }
.db-reveal   { animation:dbFadeUp 0.7s ease both; }
.db-reveal-r { animation:dbFadeRight 0.7s 0.2s ease both; }
@keyframes dbFadeUp    { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:none} }
@keyframes dbFadeRight { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:none} }
@media(max-width:1100px){ .db-kpi-grid{grid-template-columns:repeat(2,1fr);} .db-main-grid{grid-template-columns:1fr;} }
@media(max-width:640px) { .db-kpi-grid{grid-template-columns:1fr 1fr;} .db-hero{flex-direction:column;} .db-reco-grid{grid-template-columns:1fr;} }
</style>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
(function(){
  const canvas = document.getElementById('dash-canvas-bg');
  if(!canvas||typeof THREE==='undefined') return;
  const W=window.innerWidth, H=window.innerHeight;
  const scene=new THREE.Scene();
  const camera=new THREE.PerspectiveCamera(65,W/H,0.1,1000);
  camera.position.set(0,0,50);
  const renderer=new THREE.WebGLRenderer({canvas,antialias:true,alpha:true});
  renderer.setSize(W,H);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio,2));

  scene.add(new THREE.AmbientLight(0xffffff,0.3));
  const pl1=new THREE.PointLight(0xFFD700,2,200); pl1.position.set(30,30,30); scene.add(pl1);
  const pl2=new THREE.PointLight(0xF77F00,1.5,200); pl2.position.set(-30,-20,20); scene.add(pl2);
  const bl=new THREE.PointLight(0x3b82f6,1,150); bl.position.set(0,-30,10); scene.add(bl);

  const pCount=2500, pGeo=new THREE.BufferGeometry();
  const pPos=new Float32Array(pCount*3), pCol=new Float32Array(pCount*3);
  const palette=[new THREE.Color(0xFFD700),new THREE.Color(0xF77F00),new THREE.Color(0x3b82f6),new THREE.Color(0xa855f7)];
  for(let i=0;i<pCount;i++){
    pPos[i*3]=(Math.random()-0.5)*200; pPos[i*3+1]=(Math.random()-0.5)*200; pPos[i*3+2]=(Math.random()-0.5)*150;
    const pc=palette[Math.floor(Math.random()*4)];
    pCol[i*3]=pc.r; pCol[i*3+1]=pc.g; pCol[i*3+2]=pc.b;
  }
  pGeo.setAttribute('position',new THREE.BufferAttribute(pPos,3));
  pGeo.setAttribute('color',new THREE.BufferAttribute(pCol,3));
  scene.add(new THREE.Points(pGeo,new THREE.PointsMaterial({size:0.9,vertexColors:true,transparent:true,opacity:0.7,blending:THREE.AdditiveBlending,depthWrite:false})));

  const meshes=[];
  function addMesh(geo,color,x,y,z){
    const mat=new THREE.MeshPhongMaterial({color,wireframe:true,transparent:true,opacity:0.18,emissive:color,emissiveIntensity:0.2});
    const m=new THREE.Mesh(geo,mat);
    m.position.set(x,y,z);
    m.userData={rx:(Math.random()-0.5)*0.006,ry:(Math.random()-0.5)*0.008,rz:(Math.random()-0.5)*0.004};
    scene.add(m); meshes.push(m);
  }
  addMesh(new THREE.TorusGeometry(18,0.4,12,80),0xFFD700,0,0,-20);
  addMesh(new THREE.TorusGeometry(10,0.3,8,60),0xF77F00,20,10,-10);
  addMesh(new THREE.IcosahedronGeometry(7,1),0xFFD700,-25,15,-15);
  addMesh(new THREE.IcosahedronGeometry(5,1),0x3b82f6,25,-18,-10);
  addMesh(new THREE.OctahedronGeometry(6,0),0xF77F00,10,20,-8);
  addMesh(new THREE.OctahedronGeometry(3,0),0x22C55E,-10,-8,5);
  addMesh(new THREE.TorusKnotGeometry(8,0.25,100,8),0xFFD700,-5,5,-30);
  addMesh(new THREE.TorusGeometry(35,0.25,8,120),0xffffff,0,0,-40);

  let mx=0,my=0;
  document.addEventListener('mousemove',e=>{mx=(e.clientX/window.innerWidth-0.5)*2;my=(e.clientY/window.innerHeight-0.5)*2;});

  let t=0;
  function animate(){
    requestAnimationFrame(animate); t+=0.008;
    meshes.forEach(m=>{m.rotation.x+=m.userData.rx;m.rotation.y+=m.userData.ry;m.rotation.z+=m.userData.rz;});
    pl1.intensity=1.5+Math.sin(t*1.2)*0.8; pl2.intensity=1.0+Math.sin(t*0.8+1)*0.5; bl.intensity=0.8+Math.sin(t*1.5)*0.4;
    camera.position.x+=(mx*8-camera.position.x)*0.03;
    camera.position.y+=(-my*5-camera.position.y)*0.03;
    camera.lookAt(0,0,0);
    const pa=pGeo.attributes.position.array;
    for(let i=1;i<pCount*3;i+=3){pa[i]-=0.04;if(pa[i]<-100)pa[i]=100;}
    pGeo.attributes.position.needsUpdate=true;
    renderer.render(scene,camera);
  }
  animate();

  window.addEventListener('resize',()=>{camera.aspect=window.innerWidth/window.innerHeight;camera.updateProjectionMatrix();renderer.setSize(window.innerWidth,window.innerHeight);});

  document.querySelectorAll('.db-kpi-val[data-target]').forEach(el=>{
    const target=parseInt(el.dataset.target)||0;
    if(target===0) return;
    let current=0;
    const step=Math.ceil(target/40);
    const timer=setInterval(()=>{current=Math.min(current+step,target);el.textContent=current.toLocaleString('fr-FR');if(current>=target)clearInterval(timer);},40);
  });
})();
</script>
@endsection
