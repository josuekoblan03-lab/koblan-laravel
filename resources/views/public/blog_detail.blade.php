@extends('layouts.app')

@section('title', $article['titre'] . ' — Blog KOBLAN')

@section('content')

{{-- STYLES blog-content --}}
<style>
.blog-content h1, .blog-content h2, .blog-content h3 { color: var(--gold-300); margin-top: 2rem; margin-bottom: 1rem; font-family: var(--font-display); }
.blog-content p { margin-bottom: 1rem; }
.blog-content blockquote { border-left: 4px solid var(--gold-500); padding-left: 1rem; color: var(--gray-400); font-style: italic; background: var(--dark-100); padding: 1rem; border-radius: 0 8px 8px 0; }
.blog-content ul, .blog-content ol { padding-left: 2rem; margin-bottom: 1rem; }
.blog-content li { margin-bottom: 0.5rem; }
.blog-content a { color: var(--gold-300); text-decoration: underline; }
.blog-content img { max-width: 100%; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
</style>

{{-- 1. PAGE HEADER — CENTRÉ avec image en fond transparent --}}
<section class="page-header" style="padding-bottom:1rem; position:relative; min-height:40vh; display:flex; align-items:center; justify-content:center; text-align:center;">
  @if(!empty($article['url_media']) && $article['type_media'] === 'image')
    <div style="position:absolute; inset:0; background: url('{{ $article['url_media'] }}') center/cover no-repeat; opacity:0.15; z-index:0;"></div>
  @endif

  <div class="section-container" style="position:relative; z-index:2; max-width:900px; margin:0 auto;">
    {{-- Retour --}}
    <div style="margin-bottom:1.5rem;">
      <a href="{{ route('blog') }}" style="color:var(--gold-300);text-decoration:none;font-size:0.85rem;opacity:0.8;">
        <i class="fas fa-arrow-left"></i> Retour au Blog
      </a>
    </div>

    {{-- Date & Vues --}}
    <div style="display:inline-block; font-size:0.75rem; color:var(--gold-400); text-transform:uppercase; letter-spacing:0.1em; font-weight:700; margin-bottom:1rem; padding:0.25rem 0.75rem; background:rgba(255,215,0,0.1); border-radius:10px;">
      <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($article['date_creation'])->format('d M Y') }}
      &nbsp;|&nbsp;
      <i class="fas fa-eye"></i> {{ number_format($article['vues']) }} vues
    </div>

    {{-- Titre --}}
    <h1 style="font-family:var(--font-display); font-size:2.5rem; font-weight:800; color:var(--gray-100); line-height:1.2; margin-bottom:2rem;">{{ $article['titre'] }}</h1>

    {{-- Auteur --}}
    <div style="display:flex; align-items:center; justify-content:center; gap:1rem;">
      <div class="user-avatar-nav" style="width:50px;height:50px;">
        <div class="user-initials" style="font-size:1.1rem;">{{ strtoupper(mb_substr($article['auteur_prenom'], 0, 1) . mb_substr($article['auteur_nom'], 0, 1)) }}</div>
      </div>
      <div style="text-align:left;">
        <div style="font-weight:700; font-size:1rem; color:var(--gray-100);">{{ $article['auteur_prenom'] }} {{ $article['auteur_nom'] }}</div>
        <div style="font-size:0.8rem; color:var(--gold-400);">
          <i class="fas fa-pen"></i> Contributeur KOBLAN
        </div>
      </div>
    </div>
  </div>
</section>

{{-- 2. CONTENU PRINCIPAL --}}
<section style="padding:4rem 0;">
  <div class="section-container" style="max-width:800px; margin:0 auto;">

    {{-- Image/Vidéo principale --}}
    @if(!empty($article['url_media']))
      <div style="margin-bottom:3rem; border-radius:12px; overflow:hidden; border:1px solid var(--glass-border); box-shadow:0 10px 40px rgba(0,0,0,0.5);">
        @if($article['type_media'] === 'video')
          <video src="{{ $article['url_media'] }}" controls style="width:100%; display:block;"></video>
        @else
          <img src="{{ $article['url_media'] }}" alt="{{ $article['titre'] }}" style="width:100%; display:block;">
        @endif
      </div>
    @endif

    {{-- Contenu de l'article --}}
    <div class="blog-content" style="font-size:1.1rem; color:var(--gray-300); line-height:1.8; margin-bottom:4rem;">
      @foreach(explode("\n", $article['contenu']) as $line)
        @if(str_starts_with(trim($line), '## '))
          <h2>{{ substr(trim($line), 3) }}</h2>
        @elseif(str_starts_with(trim($line), '# '))
          <h3>{{ substr(trim($line), 2) }}</h3>
        @elseif(trim($line) !== '')
          <p>{{ $line }}</p>
        @endif
      @endforeach
    </div>

    {{-- Section Like --}}
    <div style="padding:1.5rem; background:var(--dark-100); border:1px solid var(--glass-border); border-radius:12px; display:flex; align-items:center; justify-content:space-between; margin-bottom:4rem; flex-wrap:wrap; gap:1rem;">
      <div>
        <span style="font-size:1.1rem; font-weight:700; color:var(--gray-100);">Cet article vous a plu ?</span>
        <p style="font-size:0.85rem; color:var(--gray-500); margin-top:0.25rem;">Montrez votre soutien à l'auteur</p>
      </div>
      @auth
        <button id="likeBtn" onclick="toggleLike({{ $article['id'] }})"
                style="background:var(--dark-200); border:1px solid var(--glass-border); padding:0.75rem 1.5rem; border-radius:9999px; color:var(--gray-300); font-size:1.1rem; cursor:pointer; transition:all 0.3s ease; display:flex; align-items:center; gap:0.5rem;">
          <i class="far fa-heart" id="likeIcon"></i>
          <span id="likeCount">{{ $article['nb_likes'] }}</span>
        </button>
      @else
        <a href="{{ route('login') }}"
           style="background:var(--dark-200); border:1px solid var(--glass-border); padding:0.75rem 1.5rem; border-radius:9999px; color:var(--gray-300); font-size:1.1rem; text-decoration:none; display:flex; align-items:center; gap:0.5rem;">
          <i class="far fa-heart"></i>
          <span>{{ $article['nb_likes'] }}</span>
        </a>
      @endauth
    </div>

    {{-- Section Commentaires --}}
    <h3 style="font-family:var(--font-display); font-size:1.5rem; margin-bottom:2rem; border-bottom:1px solid var(--glass-border); padding-bottom:1rem;">
      Commentaires (<span style="color:var(--gold-400);">{{ count($comments ?? []) }}</span>)
    </h3>

    @auth
      <form id="commentForm" style="margin-bottom:3rem; background:var(--dark-100); padding:1.5rem; border-radius:12px; border:1px solid var(--glass-border);">
        <input type="hidden" id="article_id" value="{{ $article['id'] }}">
        <div style="display:flex; gap:1rem;">
          <div class="user-avatar-nav" style="width:40px; height:40px; flex-shrink:0;">
            <div class="user-initials" style="font-size:0.9rem;">{{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</div>
          </div>
          <div style="flex:1;">
            <textarea id="commentContent" class="form-control" placeholder="Ajouter un commentaire constructif..." required style="min-height:80px; margin-bottom:0.75rem; border-radius:8px; width:100%;"></textarea>
            <div style="text-align:right;">
              <button type="button" onclick="submitComment()" class="btn btn-gold btn-sm" id="btnSubmitComment"><i class="fas fa-paper-plane"></i> Publier</button>
            </div>
            <div id="commentError" style="color:var(--error); font-size:0.85rem; margin-top:0.5rem; display:none;"></div>
          </div>
        </div>
      </form>
    @else
      <div style="margin-bottom:3rem; padding:1.5rem; background:rgba(255,215,0,0.05); border:1px dashed var(--gold-600); border-radius:12px; text-align:center;">
        <p style="color:var(--gray-300); margin-bottom:1rem;">Vous devez être connecté pour laisser un commentaire.</p>
        <a href="{{ route('login') }}" class="btn btn-gold btn-sm">Se connecter</a>
      </div>
    @endauth

    {{-- Liste des commentaires --}}
    <div id="commentsList" style="display:flex; flex-direction:column; gap:1.5rem;">
      @if(empty($comments))
        <div style="text-align:center; padding:2rem; color:var(--gray-500); font-style:italic;">
          Aucun commentaire pour l'instant. Soyez le premier !
        </div>
      @else
        @foreach($comments as $com)
        <div style="display:flex; gap:1rem; padding:1rem; background:var(--dark-100); border-radius:12px; border:1px solid var(--glass-border);">
          <div class="user-avatar-nav" style="width:40px; height:40px; flex-shrink:0;">
            <div class="user-initials" style="font-size:0.9rem;">{{ strtoupper(mb_substr($com->user->name ?? 'U', 0, 2)) }}</div>
          </div>
          <div style="flex:1;">
            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
              <div style="font-weight:700; font-size:0.95rem; color:var(--gray-200);">{{ $com->user->name ?? 'Anonyme' }}</div>
              <div style="font-size:0.75rem; color:var(--gray-500);">{{ $com->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div style="font-size:0.95rem; color:var(--gray-300); line-height:1.5;">{!! nl2br(e($com->contenu)) !!}</div>
          </div>
        </div>
        @endforeach
      @endif
    </div>

  </div>
</section>

@endsection

@section('scripts')
<script>
function toggleLike(id) {
  const btn = document.getElementById('likeBtn');
  const icon = document.getElementById('likeIcon');
  const countSpan = document.getElementById('likeCount');

  fetch(`/blog/${id}/like`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(res => res.json())
  .then(data => {
    if (data.action === 'added') {
      btn.style.background = 'rgba(239, 68, 68, 0.1)';
      btn.style.borderColor = 'var(--error)';
      btn.style.color = 'var(--error)';
      icon.classList.replace('far', 'fas');
      countSpan.textContent = parseInt(countSpan.textContent) + 1;
    } else {
      btn.style.background = 'var(--dark-200)';
      btn.style.borderColor = 'var(--glass-border)';
      btn.style.color = 'var(--gray-300)';
      icon.classList.replace('fas', 'far');
      countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
    }
    // Animation
    if (typeof gsap !== 'undefined') gsap.from(icon, { scale: 0.5, duration: 0.3, ease: 'back.out(3)' });
  })
  .catch(err => console.error('Erreur like:', err));
}

function submitComment() {
  const articleId = document.getElementById('article_id').value;
  const content = document.getElementById('commentContent').value;
  const errorDiv = document.getElementById('commentError');
  const btnSubmit = document.getElementById('btnSubmitComment');
  
  if(content.trim().length < 3) {
    errorDiv.textContent = "Le commentaire doit faire au moins 3 caractères.";
    errorDiv.style.display = 'block';
    return;
  }
  
  errorDiv.style.display = 'none';
  const originalText = btnSubmit.innerHTML;
  btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
  btnSubmit.disabled = true;

  fetch(`/blog/${articleId}/comment`, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json', 
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ contenu: content })
  })
  .then(async res => {
    const data = await res.json().catch(() => null);
    if (!res.ok) {
        throw new Error(data?.message || "Erreur lors de l'envoi du commentaire");
    }
    return data;
  })
  .then(data => {
    // Visual injection since it's an AJAX success
    const list = document.getElementById('commentsList');
    
    // Remove "Aucun commentaire" if it exists
    if(list.children.length === 1 && list.children[0].textContent.includes('Aucun commentaire')) {
      list.innerHTML = '';
    }

    const initials = '{{ auth()->check() ? strtoupper(mb_substr(auth()->user()->name, 0, 2)) : "MO" }}';
    const userName = '{{ auth()->check() ? addslashes(auth()->user()->name) : "Moi" }}';

    // Format new comment HTML
    const newCommentHtml = `
      <div style="display:flex; gap:1rem; padding:1rem; background:rgba(255,215,0,0.05); border-radius:12px; border:1px solid var(--gold-600); opacity:0; transform:translateY(-10px);" id="new-comment-${Date.now()}">
        <div class="user-avatar-nav" style="width:40px; height:40px; flex-shrink:0; background:var(--gold-300); color:#000;">
          <div class="user-initials" style="font-size:0.9rem; font-weight:bold;">${initials}</div>
        </div>
        <div style="flex:1;">
          <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.5rem;">
            <div style="font-weight:700; font-size:0.95rem; color:var(--gold-300);">${userName}</div>
            <div style="font-size:0.75rem; color:var(--gray-500);">À l'instant</div>
          </div>
          <div style="font-size:0.95rem; color:var(--gray-300); line-height:1.5;">${data.contenu || content.replace(/\n/g, '<br>')}</div>
        </div>
      </div>
    `;

    // Append visually
    list.insertAdjacentHTML('beforeend', newCommentHtml);
    document.getElementById('commentContent').value = '';
    
    // Animate
    if (typeof gsap !== 'undefined') {
      gsap.to(list.lastElementChild, { opacity:1, y:0, duration:0.5, ease:'power2.out' });
    }
  })
  .catch(err => {
    errorDiv.textContent = err.message || "Erreur de connexion. L'article est peut-être un article de démonstration.";
    errorDiv.style.display = 'block';
  })
  .finally(() => {
    btnSubmit.innerHTML = originalText;
    btnSubmit.disabled = false;
  });
}
</script>
@endsection
