@extends('layouts.dashboard')

@section('title', 'Nouvelle Prestation — KOBLAN')

@section('content')

@if ($errors->any())
<div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#EF4444;padding:1rem 1.5rem;border-radius:12px;margin-bottom:1.5rem;">
  <ul style="list-style:none;margin:0;padding:0;">
    @foreach ($errors->all() as $error)
      <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:24px;padding:2.5rem;max-width:750px;margin:0 auto;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
  <h2 style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:2rem;display:flex;align-items:center;gap:0.75rem;">
    <i class="fas fa-plus-circle" style="color:#FFD700;"></i> Publier une nouvelle prestation
  </h2>

  <form method="POST" action="{{ route('prestataire.services.store') }}" enctype="multipart/form-data">
    @csrf

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;color:#ccc;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">Titre de la prestation *</label>
      <input type="text" name="title" style="width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.85rem 1rem;color:#fff;font-size:0.95rem;outline:none;transition:all 0.3s;" placeholder="Ex: Tresses africaines à domicile" value="{{ old('title') }}" required onfocus="this.style.borderColor='#FFD700';this.style.boxShadow='0 0 0 3px rgba(255,215,0,0.1)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.boxShadow='none'">
    </div>

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;color:#ccc;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">Service *</label>
      <select name="service_type_id" style="width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.85rem 1rem;color:#fff;font-size:0.95rem;outline:none;transition:all 0.3s;appearance:none;cursor:pointer;" required onfocus="this.style.borderColor='#FFD700'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        <option value="" style="background:#111;">-- Choisir un service --</option>
        @php $lastCat = null; @endphp
        @foreach($services as $s)
          @php $currentCatName = $s->category ? $s->category->name : 'Sans Catégorie'; @endphp
          @if($currentCatName !== $lastCat)
            @if($lastCat !== null) </optgroup> @endif
            <optgroup label="{{ $currentCatName }}" style="background:#1a1a24;color:#FFD700;">
            @php $lastCat = $currentCatName; @endphp
          @endif
          <option value="{{ $s->id }}" style="background:#0a0a0f;color:#fff;" {{ old('service_type_id') == $s->id ? 'selected' : '' }}>
            {{ $s->name }}
          </option>
        @endforeach
        @if($lastCat !== null) </optgroup> @endif
      </select>
    </div>

    <div style="margin-bottom:1.25rem;">
      <label style="display:block;font-size:0.85rem;color:#ccc;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">Description</label>
      <textarea name="description" rows="4" style="width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.85rem 1rem;color:#fff;font-size:0.95rem;outline:none;transition:all 0.3s;resize:vertical;" placeholder="Décrivez votre prestation en détail..." onfocus="this.style.borderColor='#FFD700'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">{{ old('description') }}</textarea>
    </div>

    <div style="margin-bottom:2rem;">
      <label style="display:block;font-size:0.85rem;color:#ccc;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">Prix de base (FCFA) *</label>
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <input type="number" name="price" style="flex:1;max-width:200px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.85rem 1rem;color:#fff;font-size:1.1rem;font-weight:700;outline:none;transition:all 0.3s;" placeholder="Ex: 15000" min="500" step="500" value="{{ old('price') }}" required onfocus="this.style.borderColor='#FFD700'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
        <span style="color:#aaa;font-weight:600;font-size:0.9rem;">FCFA</span>
      </div>
    </div>

    <!-- Zone d'Upload Premium -->
    <div style="margin-bottom:2.5rem;">
      <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.95rem;font-weight:700;color:#fff;margin-bottom:1rem;">
        <i class="fas fa-images" style="color:#FFD700;"></i> Photos / Vidéos
        <span style="font-weight:400;color:#888;font-size:0.8rem;">(jusqu'à 8 fichiers · JPG, PNG, WEBP, MP4)</span>
      </label>

      <div id="dropZone" style="border:2px dashed rgba(255,215,0,0.3);border-radius:20px;padding:3rem 2rem;text-align:center;cursor:pointer;transition:all 0.3s;background:rgba(255,215,0,0.02);position:relative;" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)" onclick="document.getElementById('dummyInput').click()">
        <i class="fas fa-cloud-upload-alt" style="font-size:3.5rem;color:#FFD700;margin-bottom:1.25rem;display:block;"></i>
        <p style="color:#fff;font-size:1.05rem;font-weight:700;margin-bottom:0.4rem;">Glissez vos images ici ou cliquez pour sélectionner</p>
        <p style="color:#777;font-size:0.85rem;margin:0;">Sélectionnez autant de fichiers que vous voulez (Ctrl+A pour tout sélectionner)</p>
        
        <!-- Overlay Drag -->
        <div id="dropOverlay" style="display:none;position:absolute;inset:0;background:rgba(255,215,0,0.1);border-radius:18px;border:2px solid #FFD700;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
          <span style="font-size:1.2rem;font-weight:800;color:#FFD700;">Relâchez pour ajouter !</span>
        </div>
      </div>

      <!-- Inputs cachés -->
      <input type="file" id="dummyInput" multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm" style="display:none;" onchange="addFiles(this.files); this.value='';">
      <input type="file" name="medias[]" id="mediaInput" multiple style="display:none;">

      <!-- Compteur visuel -->
      <div id="fileCounter" style="text-align:center;margin-top:1rem;font-size:0.85rem;color:#aaa;display:none;">
        <span id="fileCountText"></span>
      </div>

      <!-- Grille de prévisualisation -->
      <div id="mediaPreviews" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:1rem;margin-top:1.5rem;"></div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:1.5rem;">
      <a href="{{ route('prestataire.services.index') }}" style="flex:1;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:0.85rem;border-radius:14px;font-weight:700;text-decoration:none;text-align:center;transition:0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">Annuler</a>
      
      <button type="submit" id="submitBtn" style="flex:2;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;border:none;padding:0.85rem;border-radius:14px;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(255,215,0,0.3)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
        <i class="fas fa-paper-plane"></i> Publier la prestation
      </button>
    </div>

  </form>
</div>

<script>
/* ================================================
   UPLOAD MULTI-IMAGES PREMIUM (DataTransfer)
================================================ */
const MAX_FILES = 8;
const MAX_SIZE  = 10 * 1024 * 1024; // 10MB

let selectedFiles = new DataTransfer();

function updateInput() {
  const input = document.getElementById('mediaInput');
  input.files = selectedFiles.files;
}

function updateCounter() {
  const count = selectedFiles.files.length;
  const counter = document.getElementById('fileCounter');
  const text    = document.getElementById('fileCountText');
  if (count > 0) {
    counter.style.display = 'block';
    text.innerHTML = `<i class="fas fa-paperclip" style="color:#FFD700;"></i> <strong style="color:#fff;">${count}</strong> fichier(s) sélectionné(s) <span style="margin:0 8px;color:#555;">|</span> La 1ère image sera l'image super en avant`;
  } else {
    counter.style.display = 'none';
  }
}

function addFiles(fileList) {
  for (let file of fileList) {
    if (selectedFiles.files.length >= MAX_FILES) {
      alert(`Maximum ${MAX_FILES} fichiers autorisés.`);
      break;
    }
    if (file.size > MAX_SIZE) {
      alert(`"${file.name}" est trop volumineux (max 10MB).`);
      continue;
    }
    const allowed = ['image/jpeg','image/png','image/webp','image/gif','video/mp4','video/webm'];
    if (!allowed.includes(file.type)) {
      alert(`"${file.name}" n'est pas un format supporté.`);
      continue;
    }
    let duplicate = false;
    for (let existing of selectedFiles.files) {
      if (existing.name === file.name && existing.size === file.size) { duplicate = true; break; }
    }
    if (!duplicate) selectedFiles.items.add(file);
  }
  updateInput();
  updateCounter();
  renderPreviews();
}

function removeFile(index) {
  const newDT = new DataTransfer();
  Array.from(selectedFiles.files).forEach((f, i) => {
    if (i !== index) newDT.items.add(f);
  });
  selectedFiles = newDT;
  updateInput();
  updateCounter();
  renderPreviews();
}

function renderPreviews() {
  const container = document.getElementById('mediaPreviews');
  container.innerHTML = '';

  Array.from(selectedFiles.files).forEach((file, i) => {
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position:relative;border-radius:14px;overflow:hidden;aspect-ratio:1;background:rgba(0,0,0,0.5);border:2px solid ' + (i===0 ? '#FFD700' : 'rgba(255,255,255,0.1)') + ';';

    if (file.type.startsWith('image/')) {
      const img = document.createElement('img');
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
      img.src = URL.createObjectURL(file);
      img.onload = () => URL.revokeObjectURL(img.src);
      wrapper.appendChild(img);
    } else {
      wrapper.innerHTML = '<div style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:0.4rem;background:#1a1a24;"><i class="fas fa-video" style="font-size:2rem;color:#FFD700;"></i><span style="font-size:0.7rem;color:#aaa;font-weight:600;">Vidéo</span></div>';
    }

    if (i === 0) {
      const badge = document.createElement('div');
      badge.innerHTML = '⭐ Principale';
      badge.style.cssText = 'position:absolute;bottom:0;left:0;right:0;background:rgba(255,215,0,0.9);color:#000;font-size:0.65rem;font-weight:800;text-align:center;padding:4px;';
      wrapper.appendChild(badge);
    }

    const num = document.createElement('div');
    num.textContent = i + 1;
    num.style.cssText = 'position:absolute;top:6px;left:6px;background:rgba(0,0,0,0.8);color:#fff;font-size:0.7rem;font-weight:800;width:20px;height:20px;display:flex;align-items:center;justify-content:center;border-radius:6px;';
    wrapper.appendChild(num);

    const del = document.createElement('button');
    del.type = 'button';
    del.innerHTML = '<i class="fas fa-times"></i>';
    del.style.cssText = 'position:absolute;top:6px;right:6px;background:rgba(239,68,68,0.9);color:#fff;border:none;border-radius:6px;width:24px;height:24px;font-size:0.7rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:0.2s;';
    del.onclick = (e) => { e.stopPropagation(); removeFile(i); };
    del.onmouseover = () => del.style.background = '#EF4444';
    del.onmouseout  = () => del.style.background = 'rgba(239,68,68,0.9)';
    wrapper.appendChild(del);

    container.appendChild(wrapper);
  });

  if (selectedFiles.files.length > 0 && selectedFiles.files.length < MAX_FILES) {
    const addMore = document.createElement('div');
    addMore.style.cssText = 'border:2px dashed rgba(255,215,0,0.3);border-radius:14px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:0.2s;background:rgba(255,215,0,0.02);gap:0.5rem;';
    addMore.innerHTML = '<i class="fas fa-plus" style="font-size:1.8rem;color:#FFD700;"></i><span style="font-size:0.75rem;color:#aaa;font-weight:600;">Ajouter</span>';
    addMore.onclick = () => document.getElementById('dummyInput').click();
    addMore.onmouseover = () => { addMore.style.borderColor = '#FFD700'; addMore.style.background = 'rgba(255,215,0,0.05)'; };
    addMore.onmouseout  = () => { addMore.style.borderColor = 'rgba(255,215,0,0.3)'; addMore.style.background = 'rgba(255,215,0,0.02)'; };
    container.appendChild(addMore);
  }
}

/* Evénements Drag & Drop */
function handleDragOver(e) {
  e.preventDefault();
  document.getElementById('dropZone').style.borderColor = '#FFD700';
  document.getElementById('dropZone').style.background = 'rgba(255,215,0,0.08)';
  document.getElementById('dropOverlay').style.display = 'flex';
}
function handleDragLeave(e) {
  document.getElementById('dropZone').style.borderColor = 'rgba(255,215,0,0.3)';
  document.getElementById('dropZone').style.background = 'rgba(255,215,0,0.02)';
  document.getElementById('dropOverlay').style.display = 'none';
}
function handleDrop(e) {
  e.preventDefault();
  handleDragLeave(e);
  addFiles(e.dataTransfer.files);
}

document.querySelector('form').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication en cours...';
  btn.style.opacity = '0.8';
  btn.style.pointerEvents = 'none';
});
</script>
@endsection
