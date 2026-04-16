@extends('layouts.app')

@section('content')

@php
$p = $prestation;
$avis = $prestation->user->reviewsReceived ?? collect();
$medias = $prestation->medias ?? collect();
$mainImage = $prestation->getImageUrl();
$totalSlides = $medias->count() > 0 ? $medias->count() : 1;
@endphp

<style>
  .detail-page { padding-bottom: 6rem; }
  .detail-hero { position: relative; min-height: 70vh; display: flex; align-items: flex-end; overflow: hidden; padding-bottom: 3rem; }
  .detail-hero-bg { position: absolute; inset: 0; z-index: 0; background-size: cover; background-position: center; filter: brightness(0.35) saturate(1.2); transform: scale(1.05); transition: transform 12s ease; }
  .detail-hero:hover .detail-hero-bg { transform: scale(1.1); }
  .detail-hero-overlay { position: absolute; inset: 0; z-index: 1; background: linear-gradient(0deg, rgba(10,10,10,1) 0%, rgba(10,10,10,0.6) 50%, rgba(10,10,10,0.2) 100%); }
  .detail-hero-canvas { position: absolute; inset: 0; z-index: 2; pointer-events: none; opacity: 0.35; }
  .detail-hero-content { position: relative; z-index: 3; width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 2rem; }
  .breadcrumb { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: rgba(255,255,255,0.5); margin-bottom: 2rem; }
  .breadcrumb a { color: rgba(255,255,255,0.5); text-decoration: none; transition: 0.2s; }
  .breadcrumb a:hover { color: var(--gold-300); }
  .detail-category-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,215,0,0.15); border: 1px solid rgba(255,215,0,0.3); color: var(--gold-300); padding: 0.3rem 0.9rem; border-radius: 99px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem; }
  .detail-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; line-height: 1.1; margin-bottom: 1.5rem; text-shadow: 0 2px 30px rgba(0,0,0,0.8); }
  .detail-stats { display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; color: rgba(255,255,255,0.7); font-size: 0.9rem; }
  .detail-stat { display: flex; align-items: center; gap: 0.4rem; }
  .hero-actions { display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap; }
  .btn-reserve { display: inline-flex; align-items: center; gap: 0.6rem; background: linear-gradient(135deg, var(--gold-300), #F77F00); color: #000; font-weight: 800; font-size: 1.05rem; padding: 0.9rem 2rem; border-radius: 50px; border: none; cursor: pointer; text-decoration: none; box-shadow: 0 0 30px rgba(255,215,0,0.4); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
  .btn-reserve:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 0 50px rgba(255,215,0,0.6); color: #000; }
  .btn-fav { display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-weight: 600; font-size: 0.95rem; padding: 0.9rem 1.5rem; border-radius: 50px; cursor: pointer; transition: all 0.3s; backdrop-filter: blur(10px); border: none; }
  .btn-fav:hover, .btn-fav.active { background: rgba(239,68,68,0.15); border-color: #ef4444; color: #ef4444; }
  .btn-fav.active .fav-icon { animation: heartbeat 0.4s ease; }
  @keyframes heartbeat { 0% { transform: scale(1); } 30% { transform: scale(1.5); } 60% { transform: scale(0.9); } 100% { transform: scale(1); } }
  .btn-msg { display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-weight: 600; font-size: 0.95rem; padding: 0.9rem 1.5rem; border-radius: 50px; cursor: pointer; transition: all 0.3s; backdrop-filter: blur(10px); text-decoration: none; }
  .btn-msg:hover { background: rgba(100,200,255,0.15); border-color: #64c8ff; color: #64c8ff; }
  .detail-body { max-width: 1400px; margin: 0 auto; padding: 3rem 2rem 0; display: grid; grid-template-columns: 1fr 380px; gap: 3rem; align-items: start; background: #07070f; }
  @media(max-width: 900px) { .detail-body { grid-template-columns: 1fr; } }
  .detail-page { background: #07070f; min-height: 100vh; }
  .carousel-wrap { position: relative; border-radius: 20px; overflow: hidden; background: #000; box-shadow: 0 20px 60px rgba(0,0,0,0.6); margin-bottom: 1rem; aspect-ratio: 16/9; user-select: none; }
  .carousel-track { display: flex; height: 100%; transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); will-change: transform; }
  .carousel-slide { min-width: 100%; height: 100%; position: relative; overflow: hidden; }
  .carousel-slide img { width: 100%; height: 100%; object-fit: cover; transform: scale(1); transition: transform 0.5s ease; }
  .carousel-slide.active img { transform: scale(1.02); }
  .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); z-index: 20; width: 48px; height: 48px; border-radius: 50%; background: rgba(0,0,0,0.6); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-size: 1rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; outline: none; }
  .carousel-btn:hover { background: rgba(255,215,0,0.8); border-color: #FFD700; color: #000; }
  .carousel-btn-prev { left: 14px; } .carousel-btn-next { right: 14px; }
  .carousel-btn:disabled { opacity: 0.2; pointer-events: none; }
  .carousel-counter { position: absolute; top: 12px; right: 14px; z-index: 20; background: rgba(0,0,0,0.65); backdrop-filter: blur(8px); color: #fff; font-size: 0.8rem; font-weight: 700; padding: 4px 12px; border-radius: 99px; border: 1px solid rgba(255,255,255,0.1); }
  .carousel-dots { display: flex; justify-content: center; gap: 6px; padding: 0.6rem 0 0.3rem; }
  .carousel-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.2); cursor: pointer; transition: all 0.3s; border: none; outline: none; }
  .carousel-dot.active { background: var(--gold-300); width: 24px; border-radius: 4px; }
  .carousel-thumbs { display: flex; gap: 0.6rem; overflow-x: auto; padding: 0.5rem 0; scrollbar-width: thin; scrollbar-color: var(--gold-300) transparent; }
  .carousel-thumb { width: 88px; height: 62px; flex-shrink: 0; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; transition: border-color 0.2s, transform 0.2s; }
  .carousel-thumb:hover { transform: scale(1.05); }
  .carousel-thumb.active { border-color: var(--gold-300); }
  .carousel-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .carousel-lightbox { position: fixed; inset: 0; z-index: 9000; background: rgba(0,0,0,0.95); backdrop-filter: blur(20px); display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.3s; }
  .carousel-lightbox.open { opacity: 1; pointer-events: all; }
  .carousel-lightbox img { max-width: 90vw; max-height: 90vh; object-fit: contain; border-radius: 12px; box-shadow: 0 0 80px rgba(255,215,0,0.2); }
  .lightbox-close { position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.1); border: none; color: #fff; width: 44px; height: 44px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
  .lightbox-close:hover { background: rgba(255,255,255,0.2); }
  .detail-tabs { display: flex; gap: 0; border-bottom: 1px solid rgba(255,255,255,0.08); margin: 2.5rem 0 1.5rem; }
  .detail-tab { padding: 0.8rem 1.5rem; font-weight: 600; font-size: 0.9rem; color: var(--gray-500); cursor: pointer; transition: 0.2s; border-bottom: 2px solid transparent; margin-bottom: -1px; }
  .detail-tab:hover { color: var(--gray-200); }
  .detail-tab.active { color: var(--gold-300); border-bottom-color: var(--gold-300); }
  .tab-content { display: none; }
  .tab-content.active { display: block; animation: fadeUp 0.3s ease; }
  @keyframes fadeUp { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform: translateY(0); } }
  .detail-desc { color: var(--gray-300); line-height: 1.8; font-size: 0.95rem; white-space: pre-wrap; }
  .features-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem; }
  .feature-item { display: flex; align-items: flex-start; gap: 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 1rem; }
  .feature-icon { color: var(--gold-300); font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
  .reviews-header { display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem; }
  .reviews-score-big { font-family: var(--font-display); font-size: 4rem; font-weight: 900; color: var(--gold-300); line-height: 1; }
  .review-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 1.5rem; margin-bottom: 1rem; transition: 0.2s; }
  .review-card:hover { background: rgba(255,215,0,0.03); border-color: rgba(255,215,0,0.15); }
  .detail-sidebar { position: sticky; top: 100px; z-index: 10; }
  .price-card {
    background: #0d0d1e;
    border: 2px solid #b8860b;
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.9), 0 0 30px rgba(255,215,0,0.08);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  }
  .price-card:hover { transform: translateY(-5px); border-color: #FFD700; box-shadow: 0 30px 80px rgba(0,0,0,0.95), 0 0 40px rgba(255,215,0,0.2); }
  .price-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--gold-300), #F77F00, var(--gold-300)); background-size: 200% 200%; animation: gradientShift 3s ease infinite; border-radius: 24px 24px 0 0; }
  @keyframes gradientShift { 0% {background-position:0% 50%;} 50% {background-position:100% 50%;} 100% {background-position:0% 50%;} }
  .price-display { font-family: var(--font-display); font-size: 3.2rem; font-weight: 900; background: linear-gradient(135deg, #FFD700, #F77F00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; line-height: 1; margin-bottom: 0.5rem; }
  .price-unit { font-size: 1rem; color: var(--gray-500); }
  .incl-list { list-style: none; padding: 0; margin: 1.5rem 0; display: flex; flex-direction: column; gap: 0.8rem; }
  .incl-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.9rem; color: var(--gray-300); }
  .btn-cta-main { width: 100%; padding: 1.1rem; border-radius: 50px; background: linear-gradient(135deg, #FFD700, #F77F00); color: #000; font-weight: 800; font-size: 1.05rem; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 25px rgba(255,215,0,0.35); display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; }
  .btn-cta-main:hover { transform: translateY(-2px); box-shadow: 0 12px 35px rgba(255,215,0,0.5); color: #000; }
  .btn-cta-secondary { width: 100%; padding: 0.9rem; border-radius: 50px; margin-top: 0.75rem; background: transparent; border: 1px solid rgba(255,255,255,0.15); color: var(--gray-300); font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; }
  .btn-cta-secondary:hover { border-color: var(--gold-300); color: var(--gold-300); }
  .btn-cta-fav-sidebar { width: 100%; padding: 0.9rem; border-radius: 50px; margin-top: 0.75rem; background: transparent; border: 1px solid rgba(239,68,68,0.3); color: #ef9999; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
  .btn-cta-fav-sidebar:hover, .btn-cta-fav-sidebar.heart-active { background: rgba(239,68,68,0.1); border-color: #ef4444; color: #ef4444; }
  .provider-card {
    background: #0d0d1e;
    border: 1px solid #2a2a4a;
    border-radius: 24px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 15px 40px rgba(0,0,0,0.8);
  }
  .provider-avatar-big { width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 1rem; background: linear-gradient(135deg, var(--gold-300), #F77F00); display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 2rem; font-weight: 800; color: #000; border: 3px solid var(--gold-300); box-shadow: 0 0 25px rgba(255,215,0,0.3); overflow: hidden; }
  .provider-avatar-big img { width: 100%; height: 100%; object-fit: cover; }
  .similar-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem; }
  .similar-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; overflow: hidden; transition: all 0.3s; cursor: pointer; text-decoration: none; display: block; color: inherit; }
  .similar-card:hover { transform: translateY(-5px); border-color: rgba(255,215,0,0.3); box-shadow: 0 15px 40px rgba(0,0,0,0.4); }
  .similar-card img { width: 100%; height: 160px; object-fit: cover; }
  .similar-card-body { padding: 1rem; }
  .fav-toast { position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; background: rgba(20,20,20,0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; padding: 1rem 1.5rem; display: flex; align-items: center; gap: 1rem; transform: translateY(100px); opacity: 0; transition: all 0.4s cubic-bezier(0.4,0,0.2,1); max-width: 320px; }
  .fav-toast.show { transform: translateY(0); opacity: 1; }
  .fav-toast-icon { font-size: 1.5rem; }
</style>

<!-- TOAST -->
<div class="fav-toast" id="favToast">
  <span class="fav-toast-icon" id="favToastIcon">❤️</span>
  <div>
    <div id="favToastTitle" style="font-weight:700; font-size:0.9rem;">Ajouté aux favoris</div>
    <div id="favToastMsg" style="font-size:0.8rem; color:var(--gray-400);">Retrouvez-le dans votre liste.</div>
  </div>
</div>

<div class="detail-page">

  <!-- ── HERO ────────────────────────────────── -->
  <div class="detail-hero" style="padding-top:80px;">
    <div class="detail-hero-bg" style="background-image: url('{{ $mainImage }}');"></div>
    <div class="detail-hero-overlay"></div>
    <canvas id="details-canvas" class="detail-hero-canvas" data-three="icosahedron" data-color="0xFFD700"></canvas>

    <div class="detail-hero-content">
      <nav class="breadcrumb">
        <a href="{{ route('home') }}">Accueil</a>
        <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
        <a href="{{ route('services.index') }}">Services</a>
        <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
        <span style="color:rgba(255,255,255,0.8);">{{ $p->serviceType->category->name ?? '' }}</span>
      </nav>

      <div class="detail-category-badge">
        <i class="{{ $p->serviceType->category->icon ?? 'fas fa-briefcase' }}"></i>
        {{ $p->serviceType->category->name ?? '' }}
      </div>

      <h1 class="detail-title">{{ $p->title }}</h1>

      <div class="detail-stats">
        <div class="detail-stat">
          @if($p->user->avatar)
            <img src="{{ asset('storage/'.$p->user->avatar) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid var(--gold-300);">
          @else
            <div style="width:28px;height:28px;border-radius:50%;background:var(--gold-300);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;font-size:0.75rem;">{{ $p->user->getInitials() }}</div>
          @endif
          <span><strong style="color:#fff;">{{ $p->user->name }}</strong></span>
        </div>
        <div class="detail-stat">
          <i class="fas fa-star" style="color:var(--gold-300);"></i>
          <strong style="color:#fff;">{{ number_format($p->user->rating_avg ?? 0, 1) }}</strong>
          <span>({{ $avis->count() }} avis)</span>
        </div>
        <div class="detail-stat"><i class="fas fa-eye"></i> {{ number_format($p->views ?? 1) }} vues</div>
        <div class="detail-stat"><i class="fas fa-map-marker-alt" style="color:#ef4444;"></i> Côte d'Ivoire</div>
      </div>

      <div class="hero-actions">
        @auth
          <a href="{{ route('client.checkout', $p->id) }}" class="btn-reserve">
            <i class="fas fa-bolt"></i> Commander — {{ number_format($p->price, 0, ',', ' ') }} FCFA
          </a>
          <button class="btn-fav" id="heroBtnFav" onclick="toggleFavorite({{ $p->id }}, this)">
            <i class="far fa-heart fav-icon"></i> <span class="fav-label">Favoris</span>
          </button>
          <a href="{{ route('client.messages', ['with' => $p->user_id]) }}" class="btn-msg">
            <i class="fas fa-comment-dots"></i> Contacter
          </a>
        @else
          <a href="{{ route('login') }}" class="btn-reserve">
            <i class="fas fa-bolt"></i> Commander — {{ number_format($p->price, 0, ',', ' ') }} FCFA
          </a>
          <a href="{{ route('login') }}" class="btn-fav">
            <i class="far fa-heart"></i> Ajouter aux favoris
          </a>
        @endauth
      </div>
    </div>
  </div>

  <!-- ── BODY ────────────────────────────────── -->
  <div class="detail-body">

    <!-- COLONNE GAUCHE -->
    <div>
      @php
        $allMedias = $medias->count() > 0 ? $medias : collect([['url' => $mainImage, 'type' => 'image']]);
        $totalSlides = $allMedias->count();
      @endphp

      <!-- CAROUSEL PREMIUM -->
      <div class="carousel-wrap" id="mainCarousel">
        <div class="carousel-track" id="carouselTrack">
          @foreach($allMedias as $idx => $m)
          <div class="carousel-slide {{ $idx === 0 ? 'active' : '' }}" data-index="{{ $idx }}">
            @if(is_object($m))
              <img src="{{ asset('storage/'.$m->url) }}" alt="{{ $p->title }} - photo {{ $idx+1 }}" onclick="openLightbox('{{ asset('storage/'.$m->url) }}')" style="cursor:zoom-in;" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
            @else
              <img src="{{ $m['url'] }}" alt="{{ $p->title }}" onclick="openLightbox('{{ $m['url'] }}')" style="cursor:zoom-in;">
            @endif
          </div>
          @endforeach
        </div>

        @if($totalSlides > 1)
        <button class="carousel-btn carousel-btn-prev" id="carouselPrev" onclick="carouselMove(-1)" aria-label="Précédent">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="carousel-btn carousel-btn-next" id="carouselNext" onclick="carouselMove(1)" aria-label="Suivant">
          <i class="fas fa-chevron-right"></i>
        </button>
        <div class="carousel-counter" id="carouselCounter">1 / {{ $totalSlides }}</div>
        @endif

        <button onclick="shareService()" style="position:absolute;bottom:15px;right:15px;background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.2);color:#fff;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:15;" title="Partager">
          <i class="fas fa-share-alt"></i>
        </button>
      </div>

      @if($totalSlides > 1)
      <div class="carousel-dots" id="carouselDots">
        @foreach($allMedias as $idx => $m)
        <button class="carousel-dot {{ $idx === 0 ? 'active' : '' }}" onclick="goToSlide({{ $idx }})" aria-label="Slide {{ $idx+1 }}"></button>
        @endforeach
      </div>

      <div class="carousel-thumbs" id="carouselThumbs">
        @foreach($allMedias as $idx => $m)
        <div class="carousel-thumb {{ $idx === 0 ? 'active' : '' }}" onclick="goToSlide({{ $idx }})" data-thumb="{{ $idx }}">
          @if(is_object($m))
            <img src="{{ asset('storage/'.$m->url) }}" alt="Miniature {{ $idx+1 }}" loading="lazy">
          @else
            <img src="{{ $m['url'] }}" alt="Miniature {{ $idx+1 }}" loading="lazy">
          @endif
        </div>
        @endforeach
      </div>
      @endif

      <!-- TABS -->
      <div class="detail-tabs" id="detailTabs">
        <div class="detail-tab active" onclick="switchTab('desc', this)">📋 Description</div>
        <div class="detail-tab" onclick="switchTab('features', this)">✅ Inclus</div>
        <div class="detail-tab" onclick="switchTab('reviews', this)">⭐ Avis ({{ $avis->count() ?: 3 }})</div>
        <div class="detail-tab" onclick="switchTab('provider', this)">👤 Prestataire</div>
      </div>

      <!-- TAB: Description -->
      <div class="tab-content active" id="tab-desc">
        <div class="detail-desc">{{ $p->description ?: "Ce professionnel propose ses services dans le domaine de ".($p->serviceType->category->name ?? 'services')." avec un savoir-faire reconnu en Côte d'Ivoire." }}</div>
      </div>

      <!-- TAB: Inclus -->
      <div class="tab-content" id="tab-features">
        <div class="features-grid">
          <div class="feature-item"><i class="fas fa-shield-alt feature-icon"></i><div><strong>Garantie Satisfaction</strong><br><small style="color:var(--gray-500);">Service garanti ou remboursement</small></div></div>
          <div class="feature-item"><i class="fas fa-clock feature-icon"></i><div><strong>Intervention Rapide</strong><br><small style="color:var(--gray-500);">Disponible dans les 24h</small></div></div>
          <div class="feature-item"><i class="fas fa-lock feature-icon"></i><div><strong>Paiement Sécurisé</strong><br><small style="color:var(--gray-500);">Via Tiers de Confiance KOBLAN</small></div></div>
          <div class="feature-item"><i class="fas fa-headset feature-icon"></i><div><strong>Support 7j/7</strong><br><small style="color:var(--gray-500);">Notre équipe est toujours là</small></div></div>
          <div class="feature-item"><i class="fas fa-certificate feature-icon"></i><div><strong>Professionnel Vérifié</strong><br><small style="color:var(--gray-500);">Identité et compétences vérifiées</small></div></div>
          <div class="feature-item"><i class="fas fa-map-marker-alt feature-icon"></i><div><strong>Déplacement inclus</strong><br><small style="color:var(--gray-500);">Dans la zone Abidjan</small></div></div>
        </div>
      </div>

      <!-- TAB: Avis -->
      <div class="tab-content" id="tab-reviews">
        @if($avis->count() > 0)
          <div class="reviews-header">
            <div>
              <div class="reviews-score-big">{{ number_format($p->user->rating_avg ?? 0, 1) }}</div>
              <div style="color:var(--gold-300); margin: 0.2rem 0;">
                @php $score = round($p->user->rating_avg ?? 0); @endphp
                @for($i=1; $i<=5; $i++)
                  <i class="{{ $i<=$score?'fas':'far' }} fa-star"></i>
                @endfor
              </div>
              <small style="color:var(--gray-500);">{{ $avis->count() }} avis clients</small>
            </div>
            <div style="flex:1;">
              @foreach([5=>85, 4=>10, 3=>3, 2=>1, 1=>1] as $star => $pct)
              <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.3rem;font-size:0.85rem;">
                <span style="color:var(--gray-400);width:15px;">{{ $star }}</span>
                <i class="fas fa-star" style="color:var(--gold-300);font-size:0.7rem;"></i>
                <div style="flex:1;height:6px;background:rgba(255,255,255,0.05);border-radius:3px;overflow:hidden;">
                  <div style="height:100%;width:{{ $pct }}%;background:var(--gold-300);"></div>
                </div>
                <span style="color:var(--gray-500);width:35px;">{{ $pct }}%</span>
              </div>
              @endforeach
            </div>
          </div>
          @foreach($avis as $a)
          <div class="review-card">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--gold-300),#F77F00);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;flex-shrink:0;">
                {{ $a->client ? $a->client->getInitials() : '?' }}
              </div>
              <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                  <strong>{{ $a->client->name ?? 'Client' }}</strong>
                  <small style="color:var(--gray-500);">{{ $a->created_at->format('d M Y') }}</small>
                </div>
                <div style="color:var(--gold-300);font-size:0.85rem;margin-bottom:0.5rem;">
                  {!! str_repeat('<i class="fas fa-star"></i>', $a->rating) !!}{!! str_repeat('<i class="far fa-star"></i>', 5-$a->rating) !!}
                </div>
                <p style="color:var(--gray-300);font-size:0.9rem;line-height:1.6;">{{ $a->comment ?? '' }}</p>
              </div>
            </div>
          </div>
          @endforeach
        @else
          <!-- COMMENTAIRES FICTIFS POUR DEMO -->
          <div class="reviews-header">
            <div>
              <div class="reviews-score-big">4.8</div>
              <div style="color:var(--gold-300); margin: 0.2rem 0;">
                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
              </div>
              <small style="color:var(--gray-500);">3 avis clients</small>
            </div>
            <div style="flex:1;">
              @foreach([5=>85, 4=>15, 3=>0, 2=>0, 1=>0] as $star => $pct)
              <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.3rem;font-size:0.85rem;">
                <span style="color:var(--gray-400);width:15px;">{{ $star }}</span>
                <i class="fas fa-star" style="color:var(--gold-300);font-size:0.7rem;"></i>
                <div style="flex:1;height:6px;background:rgba(255,255,255,0.05);border-radius:3px;overflow:hidden;">
                  <div style="height:100%;width:{{ $pct }}%;background:var(--gold-300);"></div>
                </div>
                <span style="color:var(--gray-500);width:35px;">{{ $pct }}%</span>
              </div>
              @endforeach
            </div>
          </div>
          
          <div class="review-card">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--gold-300),#F77F00);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;flex-shrink:0;">
                MJ
              </div>
              <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                  <strong>Marc J.</strong>
                  <small style="color:var(--gray-500);">Il y a 3 jours</small>
                </div>
                <div style="color:var(--gold-300);font-size:0.85rem;margin-bottom:0.5rem;">
                  <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color:var(--gray-300);font-size:0.9rem;line-height:1.6;">Travail exceptionnel, très ponctuel et professionnel. Je recommande vivement pour toute intervention de ce type !</p>
              </div>
            </div>
          </div>

          <div class="review-card">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--gold-300),#F77F00);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;flex-shrink:0;">
                AK
              </div>
              <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                  <strong>Awa K.</strong>
                  <small style="color:var(--gray-500);">Il y a 2 semaines</small>
                </div>
                <div style="color:var(--gold-300);font-size:0.85rem;margin-bottom:0.5rem;">
                  <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <p style="color:var(--gray-300);font-size:0.9rem;line-height:1.6;">Très bonne prestation globale. Le tarif est honnête et le résultat est impeccable.</p>
              </div>
            </div>
          </div>

          <div class="review-card">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
              <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--gold-300),#F77F00);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;flex-shrink:0;">
                DK
              </div>
              <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem;">
                  <strong>David K.</strong>
                  <small style="color:var(--gray-500);">Il y a 1 mois</small>
                </div>
                <div style="color:var(--gold-300);font-size:0.85rem;margin-bottom:0.5rem;">
                  <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color:var(--gray-300);font-size:0.9rem;line-height:1.6;">Rien à dire, prestation 5 étoiles. Le service client KOBLAN est réactif et le prestataire était parfait.</p>
              </div>
            </div>
          </div>
        @endif
      </div>

      <!-- TAB: Provider -->
      <div class="tab-content" id="tab-provider">
        <div style="display:flex;gap:2rem;align-items:flex-start;flex-wrap:wrap;">
          <div style="text-align:center;min-width:150px;">
            <div class="provider-avatar-big" style="margin-bottom:1rem;">
              @if($p->user->avatar)
                <img src="{{ asset('storage/'.$p->user->avatar) }}" alt="">
              @else
                {{ $p->user->getInitials() }}
              @endif
            </div>
            <h3 style="font-weight:700;">{{ $p->user->name }}</h3>
            <div style="color:var(--gold-300);font-size:0.9rem;margin:0.3rem 0;"><i class="fas fa-star"></i> {{ number_format($p->user->rating_avg ?: 4.8, 1) }} — {{ $avis->count() ?: 3 }} avis</div>
            <div style="font-size:0.8rem;color:var(--gray-500);">Spécialiste {{ $p->serviceType->category->name ?? '' }}</div>
          </div>
          <div style="flex:1;min-width:250px;">
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:1.5rem;margin-bottom:1.5rem;">
              <h4 style="margin-bottom:0.75rem;color:var(--gold-300);">À propos</h4>
              <p style="color:var(--gray-300);line-height:1.7;font-size:0.9rem;">
                {{ $p->user->bio ?: "Je suis un professionnel passionné prêt à offrir le meilleur service possible." }}
              </p>
            </div>
            @auth
            <a href="{{ route('provider.profile', $p->user_id) }}" class="btn-cta-secondary" style="text-decoration:none;display:inline-flex;">
              <i class="fas fa-user"></i> Voir le profil complet
            </a>
            @endauth
          </div>
        </div>
      </div>
    </div>

    <!-- COLONNE DROITE : SIDEBAR STICKY -->
    <div class="detail-sidebar">

      <!-- PRICE CARD -->
      <div class="price-card" style="position:relative;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.5rem;">
          <div>
            <div style="font-size:0.8rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.25rem;">Tarif prestation</div>
            <div class="price-display">
              {{ number_format($p->price, 0, ',', ' ') }}
              <span class="price-unit">FCFA</span>
            </div>
          </div>
          <div style="background:rgba(16,185,129,0.15);color:#10b981;border-radius:8px;padding:0.4rem 0.8rem;font-size:0.8rem;font-weight:700;">
            <i class="fas fa-check-circle"></i> Disponible
          </div>
        </div>

        <ul class="incl-list">
          <li class="incl-item"><i class="fas fa-check" style="color:#10b981;"></i> Service professionnel garanti</li>
          <li class="incl-item"><i class="fas fa-check" style="color:#10b981;"></i> Paiement sécurisé (Tiers de Confiance)</li>
          <li class="incl-item"><i class="fas fa-check" style="color:#10b981;"></i> Intervention sous 24h</li>
          <li class="incl-item"><i class="fas fa-check" style="color:#10b981;"></i> Support client 7j/7</li>
          <li class="incl-item"><i class="fas fa-times" style="color:#ef4444;"></i> Frais de déplacement > 10km</li>
        </ul>

        @auth
          <a href="{{ route('client.checkout', $p->id) }}" class="btn-cta-main">
            <i class="fas fa-bolt"></i> Réserver maintenant
          </a>
          <a href="{{ route('client.messages', ['with' => $p->user_id]) }}" class="btn-cta-secondary">
            <i class="fas fa-comment-dots"></i> Poser une question
          </a>
          <button class="btn-cta-fav-sidebar" id="sidebarBtnFav" onclick="toggleFavorite({{ $p->id }}, this)">
            <i class="far fa-heart fav-icon"></i> <span class="fav-label">Sauvegarder en Favoris</span>
          </button>
        @else
          <a href="{{ route('login') }}" class="btn-cta-main">
            <i class="fas fa-sign-in-alt"></i> Connexion pour réserver
          </a>
        @endauth

        <p style="text-align:center;font-size:0.75rem;color:var(--gray-500);margin-top:1rem;">
          <i class="fas fa-lock"></i> Paiement 100% sécurisé · Aucun frais caché
        </p>
      </div>

      <!-- PROVIDER MINI CARD -->
      <div class="provider-card">
        <div class="provider-avatar-big">
          @if($p->user->avatar)
            <img src="{{ asset('storage/'.$p->user->avatar) }}" alt="">
          @else
            {{ $p->user->getInitials() }}
          @endif
        </div>
        <h3 style="font-weight:700;margin-bottom:0.25rem;">{{ $p->user->name }}</h3>
        <div style="color:var(--gold-300);font-size:0.85rem;margin-bottom:1rem;">
          <i class="fas fa-star"></i> {{ number_format($p->user->rating_avg ?: 4.8, 1) }} · {{ $avis->count() ?: 3 }} avis
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.5rem;text-align:center;">
          <div style="background:rgba(255,255,255,0.03);border-radius:10px;padding:0.75rem;">
            <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:700;color:#fff;">98%</div>
            <div style="font-size:0.75rem;color:var(--gray-500);">Satisfaction</div>
          </div>
          <div style="background:rgba(255,255,255,0.03);border-radius:10px;padding:0.75rem;">
            <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:700;color:#fff;">&lt;2h</div>
            <div style="font-size:0.75rem;color:var(--gray-500);">Réponse moy.</div>
          </div>
        </div>

        @auth
        <a href="{{ route('provider.profile', $p->user_id) }}" class="btn-cta-secondary" style="font-size:0.85rem;">
          <i class="fas fa-user"></i> Voir le profil
        </a>
        @endauth
      </div>

      <!-- TRUST BADGES -->
      <div style="background:rgba(16,185,129,0.05);border:1px solid rgba(16,185,129,0.15);border-radius:16px;padding:1.25rem;margin-top:1.5rem;display:flex;flex-direction:column;gap:0.6rem;">
        <h4 style="font-size:0.85rem;color:#10b981;margin-bottom:0.25rem;"><i class="fas fa-shield-alt"></i> Protégé par KOBLAN</h4>
        <div style="font-size:0.8rem;color:var(--gray-400);">✓ Paiement conservé en Tiers de Confiance</div>
        <div style="font-size:0.8rem;color:var(--gray-400);">✓ Libéré au prestataire après validation</div>
        <div style="font-size:0.8rem;color:var(--gray-400);">✓ Remboursement si service non rendu</div>
      </div>
    </div>

  </div>

  <!-- ── SERVICES SIMILAIRES ─────────────────── -->
  @if($similar->count() > 0)
  <div style="max-width:1400px;margin:4rem auto 0;padding:0 2rem;">
    <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin-bottom:0.5rem;">Services <span style="color:var(--gold-300);">similaires</span></h2>
    <p style="color:var(--gray-500);margin-bottom:2rem;">D'autres professionnels dans la même catégorie</p>
    <div class="similar-grid">
      @foreach($similar as $s)
      <a href="{{ route('services.show', $s->id) }}" class="similar-card">
        <img src="{{ $s->getImageUrl() }}" alt="{{ $s->title }}" loading="lazy">
        <div class="similar-card-body">
          <div style="font-size:0.75rem;color:var(--gold-400);margin-bottom:0.3rem;"><i class="fas fa-tag"></i> Similaire</div>
          <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.5rem;line-height:1.4;">{{ $s->title }}</div>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-family:var(--font-display);font-weight:800;color:var(--gold-300);">{{ number_format($s->price,0,',',' ') }} F</span>
            <span style="font-size:0.8rem;color:var(--gray-500);"><i class="fas fa-star" style="color:var(--gold-300);"></i> {{ number_format($s->user->rating_avg??0,1) }}</span>
          </div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

</div>

<!-- LIGHTBOX MODAL -->
<div class="carousel-lightbox" id="carouselLightbox" onclick="if(event.target===this) closeLightbox()">
  <button class="lightbox-close" onclick="closeLightbox()" aria-label="Fermer">
    <i class="fas fa-times"></i>
  </button>
  <img id="lightboxImg" src="" alt="Vue agrandie" style="animation: fadeUp 0.3s ease;">
</div>

@endsection

@section('scripts')
<script>
const TOTAL_SLIDES = {{ $totalSlides }};

function switchTab(id, el) {
  document.querySelectorAll('.detail-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  el.classList.add('active');
}

(function() {
  if (TOTAL_SLIDES <= 1) return;
  let current = 0;
  const track   = document.getElementById('carouselTrack');
  const dots    = document.querySelectorAll('.carousel-dot');
  const thumbs  = document.querySelectorAll('.carousel-thumb');
  const slides  = document.querySelectorAll('.carousel-slide');
  const counter = document.getElementById('carouselCounter');

  function update() {
    track.style.transform = `translateX(-${current * 100}%)`;
    slides.forEach((s, i) => s.classList.toggle('active', i === current));
    dots.forEach((d, i)   => d.classList.toggle('active', i === current));
    thumbs.forEach((t, i) => t.classList.toggle('active', i === current));
    if (counter) counter.textContent = `${current + 1} / ${TOTAL_SLIDES}`;
    if (thumbs[current]) thumbs[current].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }

  window.carouselMove = function(dir) { current = (current + dir + TOTAL_SLIDES) % TOTAL_SLIDES; update(); };
  window.goToSlide = function(idx) { current = idx; update(); };

  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft')  carouselMove(-1);
    if (e.key === 'ArrowRight') carouselMove(1);
  });

  let startX = 0;
  const wrap = document.getElementById('mainCarousel');
  if (wrap) {
    wrap.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    wrap.addEventListener('touchend', e => {
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) carouselMove(diff > 0 ? 1 : -1);
    }, { passive: true });
  }
})();

window.openLightbox = function(url) {
  const lb = document.getElementById('carouselLightbox');
  const img = document.getElementById('lightboxImg');
  if (!lb || !img) return;
  img.src = url;
  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
};
window.closeLightbox = function() {
  document.getElementById('carouselLightbox')?.classList.remove('open');
  document.body.style.overflow = '';
};
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

function showToast(icon, title, msg, color = '#ef4444') {
  const toast = document.getElementById('favToast');
  document.getElementById('favToastIcon').textContent = icon;
  document.getElementById('favToastTitle').textContent = title;
  document.getElementById('favToastMsg').textContent = msg;
  toast.style.borderColor = color;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}

async function toggleFavorite(prestId, btn) {
  try {
    // 1. Optimistic UI update (instantané)
    let iconFirst = btn.querySelector('.fav-icon');
    let isCurrentlyAdded = iconFirst.classList.contains('fas'); 
    let isAdded = !isCurrentlyAdded; // L'état cible

    document.querySelectorAll('#heroBtnFav, #sidebarBtnFav').forEach(b => {
      const icon = b.querySelector('.fav-icon');
      const label = b.querySelector('.fav-label');
      if (icon) { icon.className = (isAdded ? 'fas' : 'far') + ' fa-heart fav-icon'; icon.style.color = isAdded ? '#ef4444' : ''; }
      if (label) label.textContent = isAdded ? '❤️ Enregistré' : 'Favoris';
      b.classList.toggle('active', isAdded);
      b.classList.toggle('heart-active', isAdded);
    });

    if (isAdded) {
      showToast('❤️', 'Ajouté aux favoris !', 'Retrouvez-le dans votre liste de favoris.', '#ef4444');
    } else {
      showToast('💔', 'Retiré des favoris', 'Cette prestation a été retirée de vos favoris.', '#6b7280');
    }

    if (typeof gsap !== 'undefined') {
      gsap.from(btn.querySelector('.fav-icon'), { scale: 0, duration: 0.4, ease: 'back.out(3)' });
    }

    // Mise à jour manuelle instantanée du badge (sans attendre l'API)
    const bFav = document.getElementById('badge-fav');
    if(bFav) {
        let count = parseInt(bFav.textContent || '0');
        count = isAdded ? count + 1 : Math.max(0, count - 1);
        bFav.textContent = count;
        bFav.style.display = count > 0 ? 'flex' : 'none';
        bFav.style.transform = count > 0 ? 'scale(1)' : 'scale(0.5)';
    }

    // 2. Fetch en arrière-plan
    fetch('{{ route("client.favorites.toggle", ":id") }}'.replace(':id', prestId), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    });

  } catch(err) {
    window.location.href = '{{ route("login") }}';
  }
}

function shareService() {
  if (navigator.share) {
    navigator.share({ title: '{{ addslashes($p->title) }}', text: 'Découvrez ce service sur KOBLAN', url: window.location.href });
  } else {
    navigator.clipboard.writeText(window.location.href);
    showToast('📋', 'Lien copié !', 'Partagez ce service avec vos amis.', 'var(--gold-300)');
  }
}

// Sécurité globale (Les animations GSAP ont été retirées pour garantir l'affichage immédiat des boutons et textes)
document.querySelectorAll('.price-card, .provider-card, .detail-category-badge, .detail-title, .detail-stats, .hero-actions').forEach(function(el) {
  if(el) {
     el.style.opacity = '1';
     el.style.transform = 'translateY(0)';
  }
});

</script>
@endsection
