@extends('layouts.app')

@section('content')
<!-- 1. HERO SERVICES & RECHERCHE 3D -->
<section class="page-header" id="services-hero">
  <div class="section-container" style="position:relative;z-index:2;">
    <h1 class="page-header-title reveal">Trouvez votre <span class="text-gold">Service</span></h1>
    <p class="page-header-sub reveal" data-delay="0.1">Parcourez des milliers de prestations proposées par les meilleurs professionnels de Côte d'Ivoire.</p>
    
    <div class="search-hero reveal" data-delay="0.2" style="max-width:800px;margin:2rem auto 0;background:var(--dark-100);">
      <i class="fas fa-search" style="color:var(--gold-300);"></i>
      <input type="text" id="searchInput" placeholder="Que cherchez-vous ?" value="{{ request('q') }}" onkeypress="if(event.key==='Enter') applyFilters()">
      <button class="btn btn-gold" onclick="applyFilters()">Rechercher</button>
    </div>
  </div>
  <div style="position:absolute;bottom:-50px;right:-50px;font-size:15rem;color:rgba(255,215,0,0.03);pointer-events:none;"><i class="fas fa-tools"></i></div>
</section>

<!-- 2. CATÉGORIES RAPIDES (Tags Visuels) -->
<section style="padding:2rem 0;background:var(--dark-200);border-bottom:1px solid var(--glass-border);">
  <div class="section-container">
    <div class="filter-bar reveal">
      <button class="filter-chip {{ request('category') ? '' : 'active' }}" onclick="document.getElementById('catSelect').value=''; applyFilters()">Tout</button>
      @foreach ($categories->take(8) as $cat)
      <button class="filter-chip {{ request('category') == $cat->id ? 'active' : '' }}" 
              onclick="document.getElementById('catSelect').value='{{ $cat->id }}'; applyFilters()">
        {{ $cat->name }}
      </button>
      @endforeach
    </div>
  </div>
</section>

<style>
  .services-layout {
    display: flex; gap: 2rem; padding-top: 3rem; padding-bottom: 3rem; align-items: flex-start;
  }
  .services-sidebar {
    width: 280px; flex-shrink: 0; position: sticky; top: 90px;
  }
  @media (max-width: 900px) {
    .services-layout { flex-direction: column; }
    .services-sidebar { width: 100%; position: static; }
  }
</style>

<div class="section-container services-layout">
  
  <!-- 3. SIDEBAR FILTRES AVANCÉS -->
  <aside class="services-sidebar reveal-left">
    <div class="glass-card" style="padding:1.5rem;">
      <h3 style="font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;"><i class="fas fa-sliders-h text-gold-plain"></i> Filtres</h3>
      
      <div class="form-group">
        <label class="form-label">Catégorie</label>
        <select id="catSelect" class="form-control" onchange="applyFilters()">
          <option value="">Toutes catégories</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Ville</label>
        <select id="villeSelect" class="form-control" onchange="onCityChange(this.value)">
          <option value="">Toutes les Villes</option>
          @foreach ($cities as $v)
            <option value="{{ $v->id }}" {{ request('city') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group" id="communeGroup" style="display: {{ request('city') ? 'block' : 'none' }}">
        <label class="form-label">Commune</label>
        <select id="communeSelect" class="form-control" onchange="applyFilters()">
          <option value="">Toutes les Communes</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Trier par</label>
        <select id="sortSelect" class="form-control" onchange="applyFilters()">
          <option value="recent" {{ request('sort', 'recent') === 'recent' ? 'selected' : '' }}>Plus récents</option>
          <option value="prix_asc" {{ request('sort') === 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
          <option value="prix_desc" {{ request('sort') === 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
          <option value="note" {{ request('sort') === 'note' ? 'selected' : '' }}>Mieux notés</option>
          <option value="populaire" {{ request('sort') === 'populaire' ? 'selected' : '' }}>Populaires</option>
        </select>
      </div>

      <!-- 4. FILTRE PRIX -->
      <div class="form-group">
        <label class="form-label">Fourchette de Prix (FCFA)</label>
        <div style="display:flex;gap:0.5rem;align-items:center;">
          <input type="number" id="prixMin" class="form-control" placeholder="Min" value="{{ request('min_price') }}">
          <span style="color:var(--gray-500);">-</span>
          <input type="number" id="prixMax" class="form-control" placeholder="Max" value="{{ request('max_price') }}">
        </div>
      </div>

      <!-- 5. FILTRE NOTE MINIMUM -->
      <div class="form-group">
        <label class="form-label">Note minimum</label>
        <div style="display:flex;gap:0.5rem;">
          @foreach([4, 3, 2] as $n)
          <button class="btn btn-dark btn-sm" style="flex:1;"><i class="fas fa-star text-gold-plain"></i> {{ $n }}+</button>
          @endforeach
        </div>
      </div>

      <button class="btn btn-gold" style="width:100%;justify-content:center;margin-top:1rem;" onclick="applyFilters()">Appliquer</button>
      <button class="btn btn-outline-gold" style="width:100%;justify-content:center;margin-top:0.5rem;" onclick="resetFilters()">Réinitialiser</button>

      <!-- 6. TOP PRESTATAIRES (Encadré Sidebar) -->
      <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--glass-border);">
        <h4 style="font-size:0.85rem;color:var(--gold-400);text-transform:uppercase;margin-bottom:1rem;">Top recommandés</h4>
        @foreach($prestations->take(3) as $tp)
        <a href="{{ route('provider.profile', $tp->user_id) }}" style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;text-decoration:none;">
          <div class="provider-mini-avatar"><div class="provider-mini-initials">{{ $tp->user->getInitials() }}</div></div>
          <div>
            <div style="font-size:0.875rem;font-weight:600;color:var(--gray-100);">{{ $tp->user->name }}</div>
            <div style="font-size:0.7rem;color:var(--gold-300);"><i class="fas fa-star"></i> {{ number_format($tp->user->rating_avg ?? 5, 1) }}</div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
  </aside>

  <!-- 7. GRILLE PRINCIPALE DE SERVICES -->
  <div style="flex:1;min-width:0;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;" class="reveal">
      <h2 style="font-family:var(--font-display);font-size:1.5rem;">Résultats pour <span class="text-gold">"{{ request('q', 'Tout') }}"</span></h2>
      <div style="color:var(--gray-400);font-size:0.9rem;"><strong>{{ $prestations->total() }}</strong> service(s) trouvé(s)</div>
    </div>

    @if ($prestations->count() > 0)
      <div class="services-grid">
        @foreach ($prestations as $i => $p)
        <a href="{{ route('services.show', $p->id) }}" class="service-card reveal" data-delay="{{ ($i % 3) * 0.1 }}">
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
            <div class="service-card-cat"><i class="{{ $p->serviceType->category->icon ?? 'fas fa-briefcase' }}"></i> {{ $p->serviceType->category->name ?? 'Service' }}</div>
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
              <div class="service-price">{{ number_format($p->price, 0, ',', ' ') }} FCFA<small>/prest.</small></div>
              <div class="service-rating"><i class="fas fa-star"></i> {{ number_format($p->user->rating_avg ?? 0, 1) }}</div>
            </div>
          </div>
        </a>
        @endforeach
      </div>

      <!-- 8. PAGINATION -->
      @if ($prestations->hasPages())
      <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem;" class="reveal">
        @for ($i = 1; $i <= min($prestations->lastPage(), 10); $i++)
          <a href="{{ $prestations->url($i) }}" class="btn {{ $i === $prestations->currentPage() ? 'btn-gold' : 'btn-dark' }}" style="min-width:40px;justify-content:center;padding:0.5rem;">{{ $i }}</a>
        @endfor
      </div>
      @endif

    @else
      <!-- 9. ETAT VIDE (NO RESULTS) -->
      <div class="glass-card reveal" style="text-align:center;padding:6rem 2rem;">
        <div style="font-size:5rem;margin-bottom:1.5rem;opacity:0.3;"><i class="fas fa-surprise"></i></div>
        <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:800;margin-bottom:1rem;">Aucun Résultat</h2>
        <p style="color:var(--gray-300);max-width:400px;margin:0 auto 2rem;">Nous n'avons trouvé aucun service correspondant à vos critères actuels.</p>
        <button onclick="resetFilters()" class="btn btn-gold btn-lg"><i class="fas fa-redo"></i> Voir tout</button>
      </div>
    @endif
  </div>

</div>

<!-- 10. SECTION BANNIÈRE PROMO (Bottom) -->
<section style="padding:4rem 0;background:linear-gradient(45deg, var(--dark-400), var(--dark-200));border-top:1px solid var(--glass-border);">
  <div class="section-container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:2rem;">
    <div class="reveal-left">
      <h2 style="font-size:2rem;font-family:var(--font-display);font-weight:800;"><span class="text-gold">Besoin spécifique ?</span> Demandez un devis</h2>
      <p style="color:var(--gray-400);margin-top:0.5rem;">Vous ne trouvez pas votre bonheur ? Postez une demande publique pour nos experts.</p>
    </div>
    <a href="{{ route('contact') }}" class="btn btn-outline-gold btn-xl reveal">Contactez-nous</a>
  </div>
</section>

@endsection

@section('scripts')
<script>
function applyFilters() {
  const params = new URLSearchParams();
  const q = document.getElementById('searchInput')?.value.trim();
  const cat = document.getElementById('catSelect')?.value;
  const ville = document.getElementById('villeSelect')?.value;
  const commune = document.getElementById('communeSelect')?.value;
  const sort = document.getElementById('sortSelect')?.value;
  const prixMin = document.getElementById('prixMin')?.value;
  const prixMax = document.getElementById('prixMax')?.value;

  if (q) params.set('q', q);
  if (cat) params.set('category', cat);
  if (ville) params.set('city', ville);
  if (commune) params.set('neighborhood', commune);
  if (sort) params.set('sort', sort);
  if (prixMin) params.set('min_price', prixMin);
  if (prixMax) params.set('max_price', prixMax);

  window.location.href = '{{ route("services.index") }}?' + params.toString();
}

function onCityChange(cityId) {
  const communeGroup = document.getElementById('communeGroup');
  const communeSelect = document.getElementById('communeSelect');
  if (!cityId) {
    communeGroup.style.display = 'none';
    communeSelect.innerHTML = '<option value="">Toutes les Communes</option>';
    applyFilters();
    return;
  }
  fetch(`/api/neighborhoods/${cityId}`)
    .then(res => res.json())
    .then(data => {
      communeSelect.innerHTML = '<option value="">Toutes les Communes</option>';
      data.forEach(n => {
        communeSelect.innerHTML += `<option value="${n.id}">${n.name}</option>`;
      });
      communeGroup.style.display = data.length > 0 ? 'block' : 'none';
      applyFilters();
    })
    .catch(() => applyFilters());
}

document.addEventListener('turbo:load', function() {
    const cityId = document.getElementById('villeSelect').value;
    const requestedNeighborhood = '{{ request("neighborhood") }}';
    const communeSelect = document.getElementById('communeSelect');
    const communeGroup = document.getElementById('communeGroup');
    
    if(cityId) {
        fetch(`/api/neighborhoods/${cityId}`)
            .then(res => res.json())
            .then(data => {
                communeSelect.innerHTML = '<option value="">Toutes les Communes</option>';
                data.forEach(n => {
                    const sel = (n.id == requestedNeighborhood) ? 'selected' : '';
                    communeSelect.innerHTML += `<option value="${n.id}" ${sel}>${n.name}</option>`;
                });
                communeGroup.style.display = data.length > 0 ? 'block' : 'none';
            });
    }
});
function resetFilters() { window.location.href = '{{ route("services.index") }}'; }

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
