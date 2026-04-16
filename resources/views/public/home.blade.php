@extends('layouts.app')

@section('content')
<!-- ============================================================
     1. HERO IMMERSIF (Three.js Spécifique)
     ============================================================ -->
<section class="hero" id="hero-section">
  <canvas id="hero-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>
  <canvas id="character-canvas" style="position:absolute;top:0;right:0;width:50%;height:100vh;z-index:2;pointer-events:none;display:block;"></canvas>
  <div style="position:absolute;inset:0;background:radial-gradient(circle at 30% 50%, rgba(255,215,0,0.06) 0%, transparent 60%);z-index:1;pointer-events:none;"></div>

  <div class="section-container" style="position:relative;z-index:3;width:100%;">
    <div class="hero-content">
      <div class="hero-badge reveal" data-delay="0">
        <i class="fas fa-map-marker-alt" style="font-size:0.7rem;"></i> Plateforme N°1 en Côte d'Ivoire 🇨🇮
      </div>

      <h1 class="hero-title">
        <span class="line"><span class="word text-gold" id="heroWord1">Trouvez</span></span>
        <span class="line"><span class="word" id="heroWord2">Le</span> <span class="word" id="heroWord3">Pro</span></span>
        <span class="line"><span class="word" id="heroWord4">Qu'il</span> <span class="word" id="heroWord5">Vous</span></span>
        <span class="line"><span class="word text-gold" id="heroWord6">Faut</span></span>
      </h1>

      <p class="hero-subtitle reveal" data-delay="0.6">
        KOBLAN connecte <strong style="color:var(--gold-300);">les meilleurs professionnels</strong> de la Côte d'Ivoire avec ceux qui en ont besoin. Simplicité, sécurité et qualité.
      </p>

      <div class="search-hero reveal" data-delay="0.8" style="margin-bottom:1.5rem;">
        <i class="fas fa-search" style="color:var(--gray-500);margin-right:0.5rem;font-size:1.2rem;"></i>
        <input type="text" id="heroSearch" placeholder="Que recherchez-vous ? (ex: plomberie, coiffeuse...)" autocomplete="off">
        <button class="btn btn-gold" onclick="doHeroSearch()" style="padding:0.75rem 1.5rem;">Rechercher</button>
      </div>

      <div class="reveal" data-delay="0.9" style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:2rem;">
        <span style="font-size:0.75rem;color:var(--gray-500);align-self:center;">Tendances :</span>
        @foreach (['Coiffure', 'Plomberie', 'Ménage', 'Électricité'] as $tag)
        <a href="{{ route('services.index', ['q' => $tag]) }}" class="btn btn-dark btn-sm" style="font-size:0.7rem;">{{ $tag }}</a>
        @endforeach
      </div>

      <div class="hero-actions reveal" data-delay="1.1">
        <a href="{{ route('services.index') }}" class="btn btn-gold btn-lg"><i class="fas fa-compass"></i> Explorer</a>
        @guest
        <a href="{{ route('register.prestataire') }}" class="btn btn-outline-gold btn-lg">Devenir Prestataire <i class="fas fa-arrow-right"></i></a>
        @endguest
      </div>
    </div>
  </div>

  <div style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);z-index:3;text-align:center;animation:float 2s infinite;">
    <div style="font-size:0.6rem;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.2em;margin-bottom:0.5rem;">Défiler</div>
    <i class="fas fa-chevron-down" style="color:var(--gold-300);"></i>
  </div>
</section>

<!-- ============================================================
     2. STATS 3D
     ============================================================ -->
<section class="stats-section section-with-canvas" style="padding:4rem 0;">
  <div class="section-canvas" id="canvas-stats" data-three="icosahedron" data-color="0xFFD700"></div>
  <div class="section-container section-content-z">
    <div class="stats-grid glass-card" style="padding:2rem;">
      <div class="stat-item reveal" data-delay="0">
        <span class="stat-number" data-count="{{ $stats['prestataires'] }}">0</span>
        <span class="stat-label">Pros Vérifiés</span>
      </div>
      <div class="stat-item reveal" data-delay="0.1">
        <span class="stat-number" data-count="{{ $stats['clients'] }}">0</span>
        <span class="stat-label">Clients Satisfaits</span>
      </div>
      <div class="stat-item reveal" data-delay="0.2">
        <span class="stat-number" data-count="{{ $stats['services'] }}">0</span>
        <span class="stat-label">Services Dispos</span>
      </div>
      <div class="stat-item reveal" data-delay="0.3">
        <span class="stat-number" data-count="{{ $stats['commandes'] }}">0</span>
        <span class="stat-label">Missions Réussies</span>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     3. CATÉGORIES INTERACTIVES
     ============================================================ -->
<section id="categories" style="padding:6rem 0;">
  <div class="section-container">
    <div class="section-header">
      <div class="section-tag reveal">🗂️ Explorez nos domaines</div>
      <h2 class="section-title reveal" data-delay="0.1">Catégories de <span class="text-gold">Services</span></h2>
    </div>

    <div class="categories-grid" id="categoriesGrid">
      @foreach ($categories as $i => $cat)
      <a href="{{ route('services.index', ['category' => $cat->id]) }}" class="category-card reveal" data-delay="{{ $i * 0.05 }}" data-tilt>
        <div class="category-icon-wrap" style="background: linear-gradient(135deg, {{ $cat->color ?? '#FFD700' }}22, {{ $cat->color ?? '#FFD700' }}11);">
          <i class="{{ $cat->icon ?? 'fas fa-briefcase' }}" style="color:{{ $cat->color ?? '#FFD700' }};"></i>
        </div>
        <div class="category-name">{{ $cat->name }}</div>
        
        <div style="height:0.5rem;"></div>

        <div class="category-count">{{ $cat->prestations_count }} service(s)</div>
      </a>
      @endforeach
    </div>
    
  </div>
</section>

<!-- ============================================================
     4. COMMENT ÇA MARCHE
     ============================================================ -->
<section class="section-with-canvas" style="padding:6rem 0;background:var(--dark-200);">
  <div class="section-canvas" id="canvas-how" data-three="torusknot" data-color="0xF77F00"></div>
  <div class="section-container section-content-z">
    <div class="section-header">
      <div class="section-tag reveal">⚡ Simple & Efficace</div>
      <h2 class="section-title reveal" data-delay="0.1">Comment Ça <span class="text-gold">Marche</span> ?</h2>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:2rem;">
      @php
      $steps = [
        ['fas fa-search', 'Recherchez', 'Trouvez le pro idéal.', '#FFD700'],
        ['fas fa-calendar-check', 'Réservez', 'Fixez la date et le lieu.', '#F77F00'],
        ['fas fa-tools', 'Profitez', 'Le service est réalisé.', '#FFC857'],
        ['fas fa-star', 'Notez', 'Évaluez votre expérience.', '#FFD700'],
      ];
      @endphp
      @foreach ($steps as $i => [$icon, $title, $desc, $color])
      <div class="glass-card reveal" data-delay="{{ $i * 0.15 }}" style="padding:2rem;text-align:center;">
        <div style="font-family:var(--font-display);font-size:3.5rem;font-weight:900;color:rgba(255,215,0,0.1);margin-bottom:-20px;line-height:1;">0{{ $i+1 }}</div>
        <div style="width:60px;height:60px;border-radius:15px;background:linear-gradient(135deg,{{ $color }}22,{{ $color }}11);margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:{{ $color }};border:1px solid {{ $color }}33;">
          <i class="{{ $icon }}"></i>
        </div>
        <h3 style="font-family:var(--font-alt);font-size:1.2rem;font-weight:700;color:{{ $color }};margin-bottom:0.5rem;">{{ $title }}</h3>
        <p style="color:var(--gray-400);font-size:0.85rem;">{{ $desc }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- ============================================================
     5. SERVICES EN VEDETTE
     ============================================================ -->
<section style="padding:6rem 0;">
  <div class="section-container">
    <div class="section-header">
      <div class="section-tag reveal">⭐ Sélection KOBLAN</div>
      <h2 class="section-title reveal" data-delay="0.1">Services <span class="text-gold">Populaires</span></h2>
    </div>

    <div class="services-grid" id="servicesGrid">
      @foreach ($prestations as $i => $p)
      <a href="{{ route('services.show', $p->id) }}" class="service-card reveal" data-delay="{{ $i * 0.1 }}">
        <div class="service-card-media">
          <img src="{{ $p->getImageUrl() }}" alt="{{ $p->title }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
          @auth
          @php $isFav = auth()->user()->favoris->contains($p->id); @endphp
          <button onclick="toggleFavorite(event, {{ $p->id }}, this);" class="fav-btn" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.6);border:none;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.9rem;color:{{ $isFav ? '#EF4444' : 'var(--gray-300)' }}; transition:all 0.3s; z-index:10;">
            <i class="{{ $isFav ? 'fas' : 'far' }} fa-heart"></i>
          </button>
          @endauth
        </div>
        <div class="service-card-body">
          <div class="service-card-cat"><i class="{{ $p->serviceType->category->icon ?? 'fas fa-briefcase' }}"></i> {{ $p->serviceType->category->name ?? '' }}</div>
          <div class="service-card-title">{{ $p->title }}</div>
          <div class="service-card-provider">
            <div class="provider-mini-avatar"><div class="provider-mini-initials">{{ $p->user->getInitials() }}</div></div>
            <div class="provider-mini-info">
              <span class="name">{{ $p->user->name }}</span>
              @if($p->user->city)
              <div style="font-size:0.75rem;color:var(--gray-400);margin-top:0.2rem;">
                <i class="fas fa-map-marker-alt" style="color:var(--gold-400);"></i> 
                {{ $p->user->city->name }}{{ $p->user->neighborhood ? ', ' . $p->user->neighborhood->name : '' }}
              </div>
              @endif
            </div>
          </div>
          <div class="service-card-footer">
            <div class="service-price">{{ number_format($p->price, 0, ',', ' ') }} FCFA <small>/prest.</small></div>
            <div class="service-rating"><i class="fas fa-star"></i> {{ number_format($p->user->rating_avg ?? 0, 1) }}</div>
          </div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

<!-- ============================================================
     6. TOP PRESTATAIRES
     ============================================================ -->
<section style="padding:6rem 0;background:var(--dark-200);">
  <div class="section-container">
    <div class="section-header">
      <div class="section-tag reveal">👑 L'Élite de KOBLAN</div>
      <h2 class="section-title reveal" data-delay="0.1">Meilleurs <span class="text-gold">Prestataires</span></h2>
    </div>

    <div class="providers-grid">
      @forelse ($topProviders as $i => $p)
      <a href="{{ route('provider.profile', $p->id) }}" class="provider-card reveal" data-delay="{{ $i*0.1 }}">
        <div class="provider-avatar">
          @if($p->avatar)
             <img src="{{ asset('storage/'.$p->avatar) }}" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
          @else
             <div class="provider-avatar-initials" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:1.5rem;font-weight:900;color:var(--dark-500);">{{ $p->getInitials() }}</div>
          @endif
        </div>
        <div class="provider-name">{{ $p->name }}</div>
        <div class="provider-specialty">{{ \Str::limit($p->bio ?? 'L\'Élite KOBLAN', 30) }}</div>
        <div class="provider-stars">
          <i class="fas fa-star" style="color:var(--gold-400);"></i>
          <span style="color:var(--gray-300);font-weight:bold;margin-left:5px;">{{ number_format($p->rating_avg, 1) }}</span>
        </div>
        <div class="provider-reviews">{{ $p->total_reviews }} avis vérifiés</div>
        <div class="btn btn-outline-gold btn-sm" style="width:100%;justify-content:center;">Voir le profil</div>
      </a>
      @empty
        <p style="color:var(--gray-400);text-align:center;grid-column:1/-1;">L'élite est en cours de rassemblement.</p>
      @endforelse
    </div>
  </div>
</section>

<!-- CTA PRESTATAIRES -->
<section style="padding:8rem 0;position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center, rgba(255,215,0,0.08) 0%, transparent 65%);pointer-events:none;"></div>
  <div class="section-container" style="text-align:center;position:relative;z-index:2;">
    <h2 class="section-title reveal" style="font-size:clamp(2.5rem, 5vw, 4rem);">Vous êtes un <span class="text-gold">Professionnel</span> ?</h2>
    <p class="section-desc reveal" style="margin-bottom:3rem;font-size:1.1rem;max-width:700px;margin-left:auto;margin-right:auto;">
      Rejoignez l'élite des prestataires. Augmentez vos revenus, gérez votre emploi du temps et trouvez de nouveaux clients facilement.
    </p>
    <a href="{{ route('register.prestataire') }}" class="btn btn-gold btn-xl reveal"><i class="fas fa-rocket"></i> Commencer maintenant</a>
  </div>
</section>

@endsection

@section('scripts')
<script>
function doHeroSearch() {
  const q = document.getElementById('heroSearch').value.trim();
  if (q) window.location.href = '{{ route('services.index') }}?q=' + encodeURIComponent(q);
  else window.location.href = '{{ route('services.index') }}';
}
document.getElementById('heroSearch')?.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') doHeroSearch();
});
</script>
<script src="{{ asset('js/hero-character.js') }}"></script>
<script>
async function toggleFavorite(e, prestId, btn) {
  if (e) { e.preventDefault(); e.stopPropagation(); }
  try {
    const icon = btn.querySelector('i');
    const isCurrentlyAdded = icon.classList.contains('fas');
    const isAdded = !isCurrentlyAdded;

    // Mise à jour visuelle instantanée
    if (isAdded) {
      icon.className = 'fas fa-heart'; btn.style.color = '#EF4444';
      if(typeof gsap !== 'undefined') gsap.from(icon, { scale: 0, duration: 0.3, ease: 'back.out(2)' });
    } else {
      icon.className = 'far fa-heart'; btn.style.color = 'var(--gray-300)';
    }

    // Mise à jour manuelle instantanée du badge (app.blade.php)
    const bFav = document.getElementById('badge-fav');
    if(bFav) {
        let count = parseInt(bFav.textContent || '0');
        count = isAdded ? count + 1 : Math.max(0, count - 1);
        bFav.textContent = count;
        bFav.style.display = count > 0 ? 'flex' : 'none';
        bFav.style.transform = count > 0 ? 'scale(1)' : 'scale(0.5)';
    }
    
    // Fetch en arrière-plan
    fetch('{{ route("client.favorites.toggle", ":id") }}'.replace(':id', prestId), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    
  } catch(err) { console.error(err); window.location.href = '{{ route("login") }}'; }
}
</script>
@endsection
