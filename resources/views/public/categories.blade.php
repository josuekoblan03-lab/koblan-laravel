@extends('layouts.app')

@section('content')

@php
$topCategories = $categories->take(3);
$otherCategories = $categories->skip(3);
@endphp

<style>
body { background: #05050a !important; color: #fff; scroll-behavior: smooth; }
.hub-hero { position: relative; padding: 120px 20px 80px; background: radial-gradient(circle at 50% -20%, rgba(255,215,0,0.15) 0%, transparent 60%); text-align: center; border-bottom: 1px solid rgba(255,255,255,0.02); }
.hub-hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPjxyZWN0IHdpZHRoPSI0IiBoZWlnaHQ9IjQiIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSIvPjwvc3ZnPg==') repeat; opacity: 0.3; pointer-events: none; }
.hub-hero-badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: rgba(255,215,0,0.1); border: 1px solid rgba(255,215,0,0.2); border-radius: 99px; color: #FFD700; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem; position: relative; z-index: 10; }
.hub-hero h1 { font-size: 3.8rem; font-family: var(--font-display, 'Syne', sans-serif); font-weight: 900; line-height: 1.1; margin-bottom: 1rem; position: relative; z-index: 10; }
.hub-hero h1 span { background: linear-gradient(135deg, #FFD700, #ff8c00); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.hub-hero p { font-size: 1.1rem; color: rgba(255,255,255,0.6); max-width: 600px; margin: 0 auto 2.5rem; line-height: 1.6; position: relative; z-index: 10; }
.hub-search-box { position: relative; max-width: 500px; margin: 0 auto; z-index: 10; }
.hub-search-input { width: 100%; padding: 18px 24px 18px 56px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 99px; color: #fff; font-size: 1rem; transition: 0.3s; backdrop-filter: blur(10px); }
.hub-search-input:focus { outline: none; background: rgba(255,255,255,0.06); border-color: rgba(255,215,0,0.4); box-shadow: 0 0 30px rgba(255,215,0,0.1); }
.hub-search-icon { position: absolute; left: 22px; top: 50%; transform: translateY(-50%); color: var(--gray-500); font-size: 1.1rem; pointer-events: none; transition: 0.3s; }
.hub-search-input:focus + .hub-search-icon { color: #FFD700; }
.hub-section { padding: 5rem 2rem; position: relative; }
.hub-section-inner { max-width: 1200px; margin: 0 auto; }
.hub-section-title { font-size: 2.2rem; font-family: var(--font-display, 'Syne', sans-serif); font-weight: 800; margin-bottom: 2.5rem; display: flex; align-items: center; gap: 1rem; }
.hub-section-title i { color: #FFD700; background: rgba(255,215,0,0.1); padding: 12px; border-radius: 14px; font-size: 1.4rem; }
.cat-card { background: rgba(15, 15, 20, 0.6); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 2rem; text-align: center; text-decoration: none; display: flex; flex-direction: column; align-items: center; transition: 0.3s ease; cursor: pointer; position: relative; overflow: hidden; }
.cat-card:hover { transform: translateY(-5px); background: rgba(25, 25, 30, 0.8); border-color: rgba(255, 255, 255, 0.1); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
.cat-icon { width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1.25rem; transition: 0.3s; }
.cat-card:hover .cat-icon { transform: scale(1.1); }
.cat-name { font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 0.5rem; }
.cat-stats { font-size: 0.8rem; color: var(--gray-400); margin-bottom: 1rem; }
.cat-tags { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.4rem; margin-top: auto; }
.cat-tag { font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 99px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.05); color: var(--gray-300); }
.top-cats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; }
@media(max-width: 900px) { .top-cats-grid { grid-template-columns: 1fr; } }
.top-card { border: 1px solid rgba(255,215,0,0.15); background: linear-gradient(180deg, rgba(20,20,25,0.8), rgba(10,10,15,0.9)); padding: 2.5rem 2rem; }
.top-card::before { content: ''; position: absolute; top:0; left:0; width:100%; height:4px; background: linear-gradient(90deg, transparent, var(--card-color, #FFD700), transparent); opacity: 0.5; }
.top-card:hover { border-color: rgba(255,215,0,0.4); }
.top-badge { position: absolute; top: 15px; right: 15px; background: rgba(255,215,0,0.1); color: #FFD700; padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
.reg-cats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem; }
.how-it-works { background: linear-gradient(135deg, rgba(8,8,13,1) 0%, rgba(15,15,22,1) 100%); border-top: 1px solid rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.02); }
.steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 3rem; text-align: center; }
@media(max-width: 800px) { .steps-grid { grid-template-columns: 1fr; gap: 2rem; } }
.step-item { position: relative; }
.step-icon { width: 80px; height: 80px; background: rgba(255,215,0,0.05); border: 1px solid rgba(255,215,0,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #FFD700; margin: 0 auto 1.5rem; position: relative; }
.step-num { position: absolute; top: 0; right: -5px; background: #FFD700; color: #000; width: 24px; height: 24px; border-radius: 50%; font-size: 0.8rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }
.step-item h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color:#fff; }
.step-item p { font-size: 0.9rem; color: var(--gray-400); line-height: 1.5; }
.cta-section { padding: 6rem 2rem; text-align: center; }
.cta-box { background: linear-gradient(135deg, rgba(255,215,0,0.1) 0%, rgba(255,140,0,0.05) 100%); border: 1px solid rgba(255,215,0,0.2); border-radius: 24px; padding: 4rem 2rem; max-width: 900px; margin: 0 auto; position: relative; overflow: hidden; }
.cta-box h2 { font-size: 2.5rem; font-family: var(--font-display, 'Syne', sans-serif); margin-bottom: 1rem; color: #fff; }
.cta-box p { font-size: 1.1rem; color: var(--gray-300); max-width: 500px; margin: 0 auto 2rem; }
</style>

<!-- SECTION 1 : HERO DYNAMIQUE -->
<section class="hub-hero">
    <div class="hub-hero-badge">
        <i class="fas fa-compass"></i> Catalogue Premium
    </div>
    <h1 class="reveal" data-delay="0.1">L'Excellence dans chaque <span>Domaine</span></h1>
    <p class="reveal" data-delay="0.2">Recherchez précisément ce dont vous avez besoin parmi des centaines de professionnels vérifiés et qualifiés.</p>
    
    <div class="hub-search-box reveal" data-delay="0.3">
        <input type="text" id="catSearchInput" class="hub-search-input" placeholder="Ex: Plomberie, Coiffure, Électricité..." onkeyup="filterCategories()">
        <i class="fas fa-search hub-search-icon"></i>
    </div>
</section>

<!-- SECTION 2 : TOP CATÉGORIES TENDANCES -->
@if($topCategories->count() > 0)
<section class="hub-section">
    <div class="hub-section-inner">
        <h2 class="hub-section-title reveal"><i class="fas fa-fire"></i> Catégories Populaires</h2>
        <div class="top-cats-grid" id="topGrid">
            @foreach ($topCategories as $i => $cat)
            @php $color = $cat->color ?? '#FFD700'; @endphp
            <a href="{{ route('services.index', ['category' => $cat->id]) }}" class="cat-card top-card reveal cat-item" data-delay="{{ $i * 0.1 }}" style="--card-color: {{ $color }};">
                <div class="top-badge">Tendance</div>
                <div class="cat-icon" style="background:linear-gradient(135deg, {{ $color }}22, {{ $color }}05); color:{{ $color }}; border:1px solid {{ $color }}33; font-size:1.8rem;">
                    <i class="{{ $cat->icon ?? 'fas fa-gem' }}"></i>
                </div>
                <h3 class="cat-name search-target">{{ $cat->name }}</h3>
                <div class="cat-stats">{{ $cat->prestations_count ?? 0 }} prestataires disponibles</div>
                @if($cat->serviceTypes && $cat->serviceTypes->count() > 0)
                <div class="cat-tags">
                    @foreach($cat->serviceTypes->take(4) as $st)
                    <span class="cat-tag search-target-sub">{{ $st->name }}</span>
                    @endforeach
                    @if($cat->serviceTypes->count() > 4)
                    <span class="cat-tag" style="color:{{ $color }};">+{{ $cat->serviceTypes->count() - 4 }}</span>
                    @endif
                </div>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- SECTION 3 : TOUTES NOS CATÉGORIES -->
<section class="hub-section" style="padding-top: 2rem;">
    <div class="hub-section-inner">
        <h2 class="hub-section-title reveal"><i class="fas fa-th-large"></i> Explorez le Reste</h2>
        @if($otherCategories->count() > 0)
        <div class="reg-cats-grid" id="regGrid">
            @foreach ($otherCategories as $i => $cat)
            @php $color = $cat->color ?? '#FFD700'; @endphp
            <a href="{{ route('services.index', ['category' => $cat->id]) }}" class="cat-card reveal cat-item" data-delay="{{ ($i%4) * 0.05 }}" style="padding: 1.5rem;">
                <div class="cat-icon" style="width:48px;height:48px;font-size:1.4rem;background:transparent;color:{{ $color }};margin-bottom:0.75rem;">
                    <i class="{{ $cat->icon ?? 'fas fa-gem' }}"></i>
                </div>
                <h3 class="cat-name search-target" style="font-size:1.1rem;">{{ $cat->name }}</h3>
                <div class="cat-stats" style="font-size:0.75rem;">{{ $cat->prestations_count ?? 0 }} prestataires</div>
                @if($cat->serviceTypes && $cat->serviceTypes->count() > 0)
                <div class="cat-tags">
                    @foreach($cat->serviceTypes->take(3) as $st)
                    <span class="cat-tag search-target-sub" style="font-size:0.65rem;padding:0.15rem 0.4rem;">{{ $st->name }}</span>
                    @endforeach
                    @if($cat->serviceTypes->count() > 3)
                    <span class="cat-tag" style="color:{{ $color }};font-size:0.65rem;padding:0.15rem 0.4rem;">+{{ $cat->serviceTypes->count() - 3 }}</span>
                    @endif
                </div>
                @endif
            </a>
            @endforeach
        </div>
        @else
            @if($topCategories->isEmpty())
            <div style="text-align:center;padding:5rem;background:rgba(255,255,255,0.02);border-radius:24px;border:1px dashed rgba(255,255,255,0.1);" class="reveal">
                <i class="fas fa-satellite-dish fa-4x" style="color:rgba(255,215,0,0.3);margin-bottom:1.5rem;display:block;"></i>
                <h2 style="color:#fff;font-size:1.8rem;margin-bottom:0.5rem;">Aucune Catégorie</h2>
                <p style="color:var(--gray-400);">Notre univers stellaire est en cours de structuration.</p>
            </div>
            @else
            <p style="color:var(--gray-500);">Aucune autre catégorie disponible pour le moment.</p>
            @endif
        @endif
        
        <div id="noResultMsg" style="display:none; text-align:center; padding:3rem; color:var(--gray-500);">
            <i class="fas fa-search-minus fa-3x" style="opacity:0.2; margin-bottom:1rem; display:block;"></i>
            Aucune catégorie ou service ne correspond à votre recherche.
        </div>
    </div>
</section>

<!-- SECTION 4 : COMMENT ÇA MARCHE ? -->
<section class="hub-section how-it-works">
    <div class="hub-section-inner">
        <h2 class="hub-section-title reveal" style="justify-content:center; margin-bottom:4rem;">
            <i class="fas fa-magic" style="background:transparent;border:1px solid rgba(255,215,0,0.2);"></i> Un Processus Simple
        </h2>
        <div class="steps-grid">
            <div class="step-item reveal" data-delay="0.1">
                <div class="step-icon">
                    <i class="fas fa-hand-pointer"></i>
                    <div class="step-num">1</div>
                </div>
                <h3>Choisissez un Domaine</h3>
                <p>Naviguez à travers nos catégories et sélectionnez le service précis dont vous avez besoin aujourd'hui.</p>
            </div>
            <div class="step-item reveal" data-delay="0.2">
                <div class="step-icon">
                    <i class="fas fa-users"></i>
                    <div class="step-num">2</div>
                </div>
                <h3>Trouvez le Prestataire</h3>
                <p>Découvrez les profils vérifiés près de chez vous. Consultez leurs avis, leurs prix et leurs badges de qualité.</p>
            </div>
            <div class="step-item reveal" data-delay="0.3">
                <div class="step-icon">
                    <i class="fas fa-check-double"></i>
                    <div class="step-num">3</div>
                </div>
                <h3>Réservez & Profitez</h3>
                <p>Validez votre commande en ligne de manière sécurisée. Le prestataire intervient chez vous sans tracas.</p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 5 : CALL TO ACTION  -->
<section class="cta-section reveal">
    <div class="cta-box">
        <h2>Vous êtes un expert ?</h2>
        <p>Rejoignez des centaines de professionnels ivoiriens. Inscrivez-vous sur KOBLAN, proposez vos propres services dans ces catégories et boostez vos revenus !</p>
        @guest
            <a href="{{ route('register.prestataire') }}" class="btn btn-gold btn-lg" style="box-shadow:0 10px 25px rgba(255,215,0,0.3);">Créer un compte prestataire</a>
        @else
            @if(!auth()->user()->isPrestataire())
            <a href="{{ route('client.profile') }}" class="btn btn-gold btn-lg" style="box-shadow:0 10px 25px rgba(255,215,0,0.3);">Modifier mon profil en prestataire</a>
            @else
            <a href="{{ route('prestataire.services.create') }}" class="btn btn-gold btn-lg" style="box-shadow:0 10px 25px rgba(255,215,0,0.3);">Publier un nouveau service</a>
            @endif
        @endguest
    </div>
</section>

@endsection

@section('scripts')
<script>
function filterCategories() {
    const query = document.getElementById('catSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.cat-item');
    let visibleCount = 0;

    cards.forEach(card => {
        const title = card.querySelector('.search-target')?.textContent.toLowerCase() ?? '';
        let subMatch = false;
        card.querySelectorAll('.search-target-sub').forEach(sub => {
            if (sub.textContent.toLowerCase().includes(query)) subMatch = true;
        });
        if (title.includes(query) || subMatch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('noResultMsg').style.display = visibleCount === 0 ? 'block' : 'none';
}
</script>
@endsection
