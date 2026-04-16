@extends('layouts.app')

@section('title', 'Créer une Publication — Blog KOBLAN')

@section('content')
<section class="page-header" style="padding-bottom:1rem;">
  <div class="section-container" style="position:relative;z-index:2;">
    <div class="section-tag reveal">✏️ Nouvelle publication</div>
    <h1 class="page-header-title reveal" data-delay="0.1">
      Créer un <span class="text-gold">Article</span>
    </h1>
    <p class="page-header-sub reveal" data-delay="0.2">Partagez vos conseils, astuces et expériences avec la communauté KOBLAN.</p>
  </div>
</section>

<section style="padding:3rem 0 6rem;">
  <div class="section-container" style="max-width:800px;">

    @if($errors->any())
    <div class="alert alert-error reveal" style="margin-bottom:2rem;">
      <i class="fas fa-exclamation-circle"></i>
      <div>
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul style="margin-top:0.5rem;padding-left:1.5rem;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif

    <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data" class="glass-card reveal" style="padding:2.5rem;">
      @csrf

      {{-- Titre --}}
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label class="form-label" style="display:block;margin-bottom:0.5rem;">
          <i class="fas fa-heading text-gold-plain"></i> Titre de l'article <span style="color:var(--error)">*</span>
        </label>
        <input type="text" name="titre" class="form-control" placeholder="Ex: Comment bien entretenir sa maison ?" 
               value="{{ old('titre') }}" style="width:100%;font-size:1rem;">
      </div>

      {{-- Catégorie --}}
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label class="form-label" style="display:block;margin-bottom:0.5rem;">
          <i class="fas fa-tag text-gold-plain"></i> Catégorie <span style="color:var(--error)">*</span>
        </label>
        <select name="categorie" class="form-control" style="width:100%;">
          @foreach(['Astuces', 'Tendances', 'Conseils', 'Sécurité', 'Cuisine', 'Nature', 'Tech', 'Général'] as $cat)
            <option value="{{ $cat }}" {{ old('categorie') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>

      {{-- Contenu --}}
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label class="form-label" style="display:block;margin-bottom:0.5rem;">
          <i class="fas fa-align-left text-gold-plain"></i> Contenu <span style="color:var(--error)">*</span>
        </label>
        <textarea name="contenu" class="form-control" rows="10" placeholder="Rédigez votre article ici... (minimum 20 caractères)" 
                  style="width:100%;resize:vertical;min-height:250px;line-height:1.7;">{{ old('contenu') }}</textarea>
        <div style="font-size:0.8rem;color:var(--gray-500);margin-top:0.4rem;">
          <i class="fas fa-info-circle"></i> Minimum 20 caractères requis.
        </div>
      </div>

      {{-- Média --}}
      <div class="form-group" style="margin-bottom:2rem;">
        <label class="form-label" style="display:block;margin-bottom:0.5rem;">
          <i class="fas fa-image text-gold-plain"></i> Image ou Vidéo (optionnel)
        </label>
        <div id="mediaDropzone" onclick="document.getElementById('mediaInput').click()"
             style="border:2px dashed var(--glass-border);border-radius:12px;padding:2.5rem;text-align:center;cursor:pointer;transition:all 0.3s;background:var(--dark-100);"
             onmouseover="this.style.borderColor='var(--gold-400)';this.style.background='rgba(255,215,0,0.05)'"
             onmouseout="this.style.borderColor='var(--glass-border)';this.style.background='var(--dark-100)'">
          <i class="fas fa-cloud-upload-alt" style="font-size:2.5rem;color:var(--gold-400);margin-bottom:1rem;display:block;"></i>
          <div style="color:var(--gray-300);margin-bottom:0.5rem;font-weight:600;">Glissez-déposez ou cliquez pour choisir</div>
          <div style="font-size:0.8rem;color:var(--gray-500);">JPG, PNG, GIF, MP4 — Max 20 Mo</div>
          <div id="mediaPreviewName" style="margin-top:0.75rem;font-size:0.85rem;color:var(--gold-300);display:none;"></div>
        </div>
        <input type="file" name="media" id="mediaInput" 
               accept="image/jpg,image/jpeg,image/png,image/gif,video/mp4,video/webm"
               style="display:none;"
               onchange="previewMedia(this)">
        <div id="mediaPreview" style="margin-top:1rem;display:none;">
          <img id="mediaPreviewImg" style="max-width:100%;max-height:200px;border-radius:8px;object-fit:cover;">
        </div>
      </div>

      {{-- Actions --}}
      <div style="display:flex;gap:1rem;justify-content:flex-end;flex-wrap:wrap;">
        <a href="{{ route('blog') }}" class="btn btn-dark">
          <i class="fas fa-times"></i> Annuler
        </a>
        <button type="submit" class="btn btn-gold btn-lg">
          <i class="fas fa-paper-plane"></i> Publier l'article
        </button>
      </div>
    </form>
  </div>
</section>
@endsection

@section('scripts')
<script>
function previewMedia(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const nameEl = document.getElementById('mediaPreviewName');
    const previewEl = document.getElementById('mediaPreview');
    const previewImg = document.getElementById('mediaPreviewImg');
    
    nameEl.textContent = '✅ ' + file.name;
    nameEl.style.display = 'block';
    
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        previewImg.src = e.target.result;
        previewEl.style.display = 'block';
      };
      reader.readAsDataURL(file);
    } else {
      previewEl.style.display = 'none';
    }
  }
}

// Drag & drop support
const dropzone = document.getElementById('mediaDropzone');
const input = document.getElementById('mediaInput');

dropzone.addEventListener('dragover', e => {
  e.preventDefault();
  dropzone.style.borderColor = 'var(--gold-400)';
  dropzone.style.background = 'rgba(255,215,0,0.08)';
});
dropzone.addEventListener('dragleave', () => {
  dropzone.style.borderColor = 'var(--glass-border)';
  dropzone.style.background = 'var(--dark-100)';
});
dropzone.addEventListener('drop', e => {
  e.preventDefault();
  input.files = e.dataTransfer.files;
  previewMedia(input);
  dropzone.style.borderColor = 'var(--glass-border)';
  dropzone.style.background = 'var(--dark-100)';
});
</script>
@endsection
