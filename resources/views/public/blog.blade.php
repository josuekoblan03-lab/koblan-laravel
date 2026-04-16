@extends('layouts.app')

@section('title', 'Blog & Actualités — KOBLAN')
@section('meta_desc', 'Découvrez les astuces, partagez votre expérience et suivez les tendances de KOBLAN Services CI.')

@section('content')
@php
$mockArticles = [
    ['id'=>1,'titre'=>'Comment choisir le meilleur plombier pour vos réparations ?','contenu'=>'Découvrez nos conseils pratiques pour repérer un professionnel de confiance sur la plateforme et éviter les mauvaises surprises. La plomberie est un domaine délicat qui nécessite une expertise réelle.','url_media'=>'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-04-10','vues'=>1247,'nb_likes'=>58,'categorie'=>'Astuces','auteur_prenom'=>'Kouassi','auteur_nom'=>'Dje'],
    ['id'=>2,'titre'=>'Les nouvelles tendances de décoration intérieure en 2026','contenu'=>'Inspiration et idées créatives pour aménager votre intérieur avec des artisans locaux talentueux et des matériaux authentiques du terroir ivoirien.','url_media'=>'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-04-08','vues'=>892,'nb_likes'=>34,'categorie'=>'Tendances','auteur_prenom'=>'Aminata','auteur_nom'=>'Coulibaly'],
    ['id'=>3,'titre'=>'Astuces pour un déménagement réussi et sans stress','contenu'=>'Organisez sereinement votre prochain déménagement avec l\'aide de nos experts en logistique et transport.','url_media'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-04-05','vues'=>654,'nb_likes'=>21,'categorie'=>'Conseils','auteur_prenom'=>'Ibrahim','auteur_nom'=>'Traoré'],
    ['id'=>4,'titre'=>'Pourquoi faire appel à un électricien certifié ?','contenu'=>'La sécurité électrique de votre logement ne doit jamais être négligée. Un électricien certifié garantit des installations conformes aux normes.','url_media'=>'https://images.unsplash.com/photo-1621905252507-b35492d90cb0?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-04-01','vues'=>430,'nb_likes'=>15,'categorie'=>'Sécurité','auteur_prenom'=>'Jean-Marc','auteur_nom'=>'Konan'],
    ['id'=>5,'titre'=>'Comment entretenir votre jardin tout au long de l\'année ?','contenu'=>'Un jardin bien entretenu valorise votre propriété et offre un espace de détente incomparable.','url_media'=>'https://images.unsplash.com/photo-1416879598555-22b311740d7c?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-03-28','vues'=>318,'nb_likes'=>12,'categorie'=>'Nature','auteur_prenom'=>'Fatou','auteur_nom'=>'Diallo'],
    ['id'=>6,'titre'=>'Les meilleures recettes de cuisine ivoirienne','contenu'=>'De l\'attiéké au kedjenou, en passant par le garba, découvrez les plats emblématiques de la cuisine ivoirienne.','url_media'=>'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=800&q=80','type_media'=>'image','date_creation'=>'2026-03-22','vues'=>796,'nb_likes'=>42,'categorie'=>'Cuisine','auteur_prenom'=>'Marie-Louise','auteur_nom'=>'Bah'],
];

// Articles réels de la base de données (passés par BlogController)
$realArticles = isset($articles) ? $articles->map(fn($a) => [
    'id' => $a->id,
    'titre' => $a->titre,
    'contenu' => $a->contenu,
    'url_media' => $a->url_media ? asset('storage/'.$a->url_media) : null,
    'type_media' => $a->type_media,
    'date_creation' => $a->created_at->format('Y-m-d'),
    'vues' => $a->vues,
    'nb_likes' => $a->nb_likes,
    'categorie' => $a->categorie,
    'auteur_prenom' => $a->user->name ?? 'Auteur',
    'auteur_nom' => '',
])->toArray() : [];

// Les vrais articles d'abord, puis les mocks
$allArticles = array_merge($realArticles, $mockArticles);
$featured = $allArticles[0];
$gridArticles = array_slice($allArticles, 1);
@endphp

{{-- 1. BLOG HERO --}}
<section class="page-header" id="blog-hero" style="padding-bottom:1rem;">
  <div class="section-container" style="position:relative;z-index:2;">
    <div class="section-tag reveal">📰 Magazine KOBLAN</div>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
      <div>
        <h1 class="page-header-title reveal" data-delay="0.1">
          Actualités &amp; <span class="text-gold">Conseils</span>
        </h1>
        <p class="page-header-sub reveal" data-delay="0.2">Découvrez les astuces, partagez votre expérience et suivez les tendances.</p>
      </div>
      @auth
        <a href="{{ route('blog.create') }}" class="btn btn-gold reveal" data-delay="0.3">
          <i class="fas fa-pen"></i> Créer une publication
        </a>
      @endauth
    </div>
  </div>
</section>

@if(session('success'))
<div class="section-container" style="padding-top:1rem;">
  <div class="alert alert-success"><i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span></div>
</div>
@endif

{{-- 2. ARTICLE À LA UNE --}}
<section style="padding:2rem 0 4rem;">
  <div class="section-container">
    <a href="{{ url('/blog/detail/' . $featured['id']) }}" class="glass-card reveal holographic" style="display:flex;flex-wrap:wrap;overflow:hidden;text-decoration:none;border:1px solid var(--gold-600);">
      <div style="flex:1;min-width:300px;background:var(--dark-100);min-height:300px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
        @if(!empty($featured['url_media']))
          <img src="{{ $featured['url_media'] }}" alt="À la une" style="width:100%;height:100%;object-fit:cover;">
        @else
          <i class="fas fa-image" style="font-size:5rem;color:var(--gray-600);opacity:0.5;"></i>
        @endif
        <div style="position:absolute;bottom:1rem;left:1rem;background:var(--gold-500);color:var(--dark-500);font-weight:700;font-size:0.75rem;padding:0.25rem 0.75rem;border-radius:10px;">À la une</div>
        <div style="position:absolute;top:1rem;right:1rem;background:rgba(0,0,0,0.6);color:var(--gold-300);font-size:0.75rem;padding:0.25rem 0.75rem;border-radius:10px;font-weight:600;">
          <i class="fas fa-tag"></i> {{ $featured['categorie'] }}
        </div>
      </div>
      <div style="flex:1;min-width:300px;padding:3rem;display:flex;flex-direction:column;justify-content:center;">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;font-size:0.8rem;color:var(--gray-400);">
          <span><i class="fas fa-calendar-alt text-gold-plain"></i> {{ \Carbon\Carbon::parse($featured['date_creation'])->format('d M Y') }}</span>
          <span><i class="fas fa-eye text-gold-plain"></i> {{ number_format($featured['vues']) }} vues</span>
          <span><i class="fas fa-heart" style="color:var(--error);"></i> {{ $featured['nb_likes'] }}</span>
        </div>
        <h2 style="font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--gray-100);margin-bottom:1rem;line-height:1.2;">{{ $featured['titre'] }}</h2>
        <p style="color:var(--gray-300);font-size:1.05rem;line-height:1.7;margin-bottom:2rem;">
          {{ \Str::limit(strip_tags($featured['contenu']), 160) }}
        </p>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
          <div style="display:flex;align-items:center;gap:0.75rem;">
            <div class="user-avatar-nav" style="width:40px;height:40px;">
              <div class="user-initials" style="font-size:0.8rem;">{{ strtoupper(mb_substr($featured['auteur_prenom'], 0, 1) . mb_substr($featured['auteur_nom'], 0, 1)) }}</div>
            </div>
            <div>
              <div style="font-weight:700;font-size:0.85rem;color:var(--gray-100);">{{ $featured['auteur_prenom'] }} {{ mb_substr($featured['auteur_nom'], 0, 1) }}{{ strlen($featured['auteur_nom']) > 0 ? '.' : '' }}</div>
              <div style="font-size:0.75rem;color:var(--gray-500);">Contributeur KOBLAN</div>
            </div>
          </div>
          <div class="btn btn-gold btn-sm" style="border-radius:9999px;">
            Lire l'article <i class="fas fa-arrow-right"></i>
          </div>
        </div>
      </div>
    </a>
  </div>
</section>

{{-- 3. GRILLE D'ARTICLES --}}
<section style="padding:2rem 0 5rem;">
  <div class="section-container">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
      <h2 style="font-family:var(--font-display);font-size:1.75rem;font-weight:800;">Tous les <span class="text-gold">Articles</span></h2>
      <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        @foreach(['Tous', 'Astuces', 'Tendances', 'Conseils', 'Sécurité', 'Cuisine', 'Nature', 'Tech'] as $cat)
        <button class="filter-chip {{ $cat === 'Tous' ? 'active' : '' }}" onclick="filterBlog('{{ $cat }}', this)">{{ $cat }}</button>
        @endforeach
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:2.5rem;" id="blogGrid">
      @foreach ($gridArticles as $i => $article)
      <a href="{{ url('/blog/detail/' . $article['id']) }}" class="glass-card reveal blog-card" data-delay="{{ ($i % 3) * 0.1 }}" data-cat="{{ $article['categorie'] }}" style="display:block;overflow:hidden;text-decoration:none;">
        <div style="height:200px;background:var(--dark-100);display:flex;align-items:center;justify-content:center;border-bottom:1px solid var(--glass-border);position:relative;overflow:hidden;">
          @if(!empty($article['url_media']))
            <img src="{{ $article['url_media'] }}" alt="{{ $article['titre'] }}" style="width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
          @else
            <i class="fas fa-newspaper" style="font-size:3.5rem;color:var(--gray-600);opacity:0.4;"></i>
          @endif
          <div style="position:absolute;top:0.75rem;left:0.75rem;background:rgba(0,0,0,0.75);color:var(--gold-300);font-size:0.7rem;padding:0.2rem 0.6rem;border-radius:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
            {{ $article['categorie'] }}
          </div>
        </div>
        <div style="padding:1.5rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <span style="font-size:0.75rem;color:var(--gray-500);"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($article['date_creation'])->format('d M Y') }}</span>
            <span style="font-size:0.75rem;color:var(--gray-500);"><i class="fas fa-heart" style="color:var(--error);"></i> {{ $article['nb_likes'] }}</span>
          </div>
          <h3 style="font-family:var(--font-alt);font-size:1.15rem;font-weight:700;color:var(--gray-100);margin-bottom:0.75rem;line-height:1.4;">{{ $article['titre'] }}</h3>
          <p style="font-size:0.9rem;color:var(--gray-400);line-height:1.6;margin-bottom:1.5rem;">
            {{ \Str::limit(strip_tags($article['contenu']), 100) }}
          </p>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div style="color:var(--gold-300);font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:0.5rem;">
              Lire l'article <i class="fas fa-arrow-right"></i>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;">
              <div class="user-avatar-nav" style="width:28px;height:28px;">
                <div class="user-initials" style="font-size:0.65rem;">{{ strtoupper(mb_substr($article['auteur_prenom'], 0, 1) . mb_substr($article['auteur_nom'], 0, 1)) }}</div>
              </div>
              <div style="font-size:0.75rem;color:var(--gray-500);">{{ $article['auteur_prenom'] }}</div>
            </div>
          </div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
</section>

{{-- 4. NEWSLETTER --}}
<section style="padding:5rem 0;background:var(--dark-200);border-top:1px solid var(--glass-border);">
  <div class="section-container" style="max-width:600px;text-align:center;">
    <div class="section-tag reveal" style="justify-content:center;">✉️ Newsletter</div>
    <h2 class="section-title reveal" data-delay="0.1">Restez <span class="text-gold">Informé</span></h2>
    <p class="section-desc reveal" data-delay="0.15">Recevez chaque semaine les meilleurs articles directement dans votre boîte mail.</p>
    <div class="reveal" data-delay="0.2" style="display:flex;gap:0.75rem;max-width:450px;margin:0 auto;flex-wrap:wrap;justify-content:center;">
      <input type="email" placeholder="votre@email.com" style="flex:1;min-width:200px;padding:0.75rem 1.25rem;background:var(--dark-100);border:1px solid var(--glass-border);border-radius:9999px;color:var(--gray-100);outline:none;font-size:0.9rem;" onfocus="this.style.borderColor='var(--gold-400)'" onblur="this.style.borderColor='var(--glass-border)'">
      <button class="btn btn-gold" style="border-radius:9999px;">S'abonner</button>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
function filterBlog(cat, btn) {
  document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.blog-card').forEach(card => {
    const visible = cat === 'Tous' || card.dataset.cat === cat;
    card.style.display = visible ? 'block' : 'none';
    if (visible && typeof gsap !== 'undefined') {
      gsap.fromTo(card, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.4 });
    }
  });
}
</script>
@endsection
