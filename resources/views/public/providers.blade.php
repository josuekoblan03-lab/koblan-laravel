@extends('layouts.app')

@section('content')
<section class="page-header" style="background:var(--dark-200);padding:6rem 0 3rem;">
  <div class="section-container" style="text-align:center;">
    <h1 class="page-header-title reveal">L'Élite des <span class="text-gold">Prestataires</span></h1>
    <p class="page-header-sub reveal" data-delay="0.1">Découvrez les professionnels les mieux notés de KOBLAN et confiez-leur vos projets en toute confiance.</p>
  </div>
</section>

<section style="padding:4rem 0;">
  <div class="section-container">
    <div class="providers-grid">
      @forelse ($providers as $p)
      <a href="{{ route('provider.profile', $p->id) }}" class="provider-card reveal">
        <div class="provider-avatar" style="width:80px;height:80px;margin:0 auto 1rem;border-radius:50%;overflow:hidden;border:2px solid var(--gold-300);">
          @if($p->avatar)
             <img src="{{ asset('storage/'.$p->avatar) }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:cover;">
          @else
             <div style="background:var(--gold-300);width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#000;font-weight:bold;font-size:1.5rem;">{{ $p->getInitials() }}</div>
          @endif
        </div>
        <h3 style="font-weight:700;margin-bottom:0.25rem;">{{ $p->name }}</h3>
        <p style="color:var(--gray-500);font-size:0.85rem;margin-bottom:0.5rem;">{{ \Str::limit($p->bio ?? 'Prestataire', 40) }}</p>
        <div style="color:var(--gold-300);font-size:0.9rem;margin-bottom:1rem;">
          <i class="fas fa-star"></i> {{ number_format($p->rating_avg ?? 0, 1) }} ({{ $p->total_reviews ?? 0 }} avis)
        </div>
        <div class="btn btn-outline-gold btn-sm" style="width:100%;justify-content:center;">Voir Profil</div>
      </a>
      @empty
        <div style="grid-column:1/-1;text-align:center;color:var(--gray-400);padding:3rem;">
          Aucun prestataire trouvé.
        </div>
      @endforelse
    </div>
    
    @if ($providers->hasPages())
      <div style="margin-top:3rem;">
        {{ $providers->links('pagination::bootstrap-4') }}
      </div>
    @endif
  </div>
</section>
@endsection
