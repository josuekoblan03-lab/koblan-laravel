@extends('layouts.dashboard')

@section('content')
<div class="db-wrap">
  <div class="db-glass-panel db-reveal" style="max-width:800px;margin:0 auto;width:100%;">
    <div class="db-panel-header">
      <div class="db-panel-title"><i class="fas fa-edit text-gold-plain"></i> Modifier la Prestation</div>
    </div>

    @if ($errors->any())
      <div style="background:rgba(239, 68, 68, 0.2);border:1px solid rgba(239, 68, 68, 0.5);color:#ef4444;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">
        <ul style="margin:0;padding-left:1.5rem;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('prestataire.services.update', $prest->id) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-400);margin-bottom:0.5rem;">Catégorie & Service Cible <span style="color:#ef4444;">*</span></label>
        <select name="service_type_id" class="form-control" required style="background:rgba(0,0,0,0.3);">
          @php $currentCat = null; @endphp
          @foreach($services as $s)
            @php $currentCatName = $s->category ? $s->category->name : 'Sans Catégorie'; @endphp
            @if($currentCatName !== $currentCat)
              @if($currentCat !== null) </optgroup> @endif
              <optgroup label="{{ $currentCatName }}">
              @php $currentCat = $currentCatName; @endphp
            @endif
            <option value="{{ $s->id }}" {{ (old('service_type_id', $prest->service_type_id) == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
          @if($currentCat !== null) </optgroup> @endif
        </select>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-400);margin-bottom:0.5rem;">Titre de votre prestation <span style="color:#ef4444;">*</span></label>
        <input type="text" name="title" class="form-control" style="background:rgba(0,0,0,0.3);" value="{{ old('title', $prest->title) }}" required>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-400);margin-bottom:0.5rem;">Prix (FCFA) <span style="color:#ef4444;">*</span></label>
        <input type="number" name="price" class="form-control" style="background:rgba(0,0,0,0.3);" min="0" value="{{ old('price', $prest->price) }}" required>
      </div>

      <div class="form-group" style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gray-400);margin-bottom:0.5rem;">Description détaillée</label>
        <textarea name="description" class="form-control" rows="5" style="background:rgba(0,0,0,0.3);">{{ old('description', $prest->description) }}</textarea>
      </div>

      @if($medias->count() > 0)
      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;color:var(--gold-300);margin-bottom:0.5rem;font-weight:700;">Médias actuels</label>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          @foreach($medias as $m)
          <div style="position:relative;width:100px;height:100px;border-radius:8px;overflow:hidden;border:1px solid rgba(255,255,255,0.1);">
            <img src="{{ asset('storage/'.$m->media_url) }}" style="width:100%;height:100%;object-fit:cover;">
            @if($m->is_main)
            <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(255,215,0,0.8);color:#000;font-size:0.6rem;text-align:center;font-weight:bold;padding:2px;">Principal</div>
            @endif
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <div class="form-group" style="margin-bottom:2rem;background:rgba(0,0,0,0.2);padding:1.5rem;border-radius:12px;border:1px dashed rgba(255,255,255,0.2);">
        <label style="display:block;font-size:0.85rem;color:var(--gold-300);margin-bottom:0.5rem;font-weight:700;"><i class="fas fa-plus"></i> Ajouter de nouveaux médias</label>
        <input type="file" name="medias[]" class="form-control" accept="image/*,video/mp4" multiple style="background:transparent;padding:0;border:none;">
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;">
        <a href="{{ route('prestataire.services.index') }}" class="db-btn-ghost">Annuler</a>
        <button type="submit" class="db-btn-primary"><i class="fas fa-save"></i> Enregistrer les modifications</button>
      </div>
    </form>
  </div>
</div>
@endsection
