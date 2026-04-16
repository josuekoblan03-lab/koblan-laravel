@extends('layouts.dashboard')

@section('content')
<div class="db-wrap">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-tags" style="color:#FFD700;"></i> Gestion des <span style="color:#FFD700;">Catégories</span>
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">{{ $categories->count() }} catégorie(s) configurée(s)</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

@if(session('success'))
<div style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('success') }}</div>
@endif

{{-- FORMULAIRE CRÉATION --}}
<div style="background:var(--dark-100);border:1px solid rgba(255,215,0,0.2);border-radius:16px;padding:2rem;margin-bottom:2rem;">
    <h2 style="font-family:var(--font-alt);font-size:1rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fas fa-plus-circle" style="color:#FFD700;"></i> Nouvelle Catégorie
    </h2>
    <form id="catForm" method="POST" action="{{ route('admin.categories.store') }}" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
        @csrf
        <div>
            <label style="display:block;font-size:0.8rem;color:var(--gray-400);margin-bottom:0.4rem;">Nom de la catégorie *</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Coiffure & Beauté" required>
        </div>
        <div>
            <label style="display:block;font-size:0.8rem;color:var(--gray-400);margin-bottom:0.4rem;">Icône FontAwesome</label>
            <input type="text" name="icon" class="form-control" placeholder="fas fa-cut" value="fas fa-briefcase">
        </div>
        <div>
            <label style="display:block;font-size:0.8rem;color:var(--gray-400);margin-bottom:0.4rem;">Couleur</label>
            <div style="display:flex;gap:0.5rem;align-items:center;">
                <input type="color" name="color" id="colorPicker" value="#FFD700" style="width:44px;height:44px;border:none;border-radius:8px;background:none;cursor:pointer;padding:0;">
                <input type="text" id="colorText" value="#FFD700" class="form-control" style="flex:1;" readonly>
            </div>
        </div>
        <button type="submit" class="btn btn-gold" style="height:44px;white-space:nowrap;">
            <i class="fas fa-plus"></i> Créer
        </button>
    </form>
    <div style="margin-top:1rem;">
        <label style="display:block;font-size:0.8rem;color:var(--gray-400);margin-bottom:0.4rem;">Description courte (optionnel)</label>
        <input type="text" name="description" class="form-control" placeholder="Ex: Coiffure, maquillage, soins beauté..." form="catForm">
    </div>
</div>

{{-- LISTE DES CATÉGORIES --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.25rem;">
    @foreach($categories as $cat)
    @php
        $couleur = $cat->color ?: '#FFD700';
        $iconClass = $cat->icon ?: 'fas fa-briefcase';
    @endphp
    <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.5rem;position:relative;overflow:hidden;transition:0.2s;"
         onmouseover="this.style.borderColor='{{ $couleur }}44'" onmouseout="this.style.borderColor='var(--glass-border)'">
        {{-- Background glow --}}
        <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:radial-gradient(circle,{{ $couleur }}22,transparent);pointer-events:none;"></div>

        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;gap:0.85rem;">
                <div style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,{{ $couleur }}22,{{ $couleur }}11);border:1px solid {{ $couleur }}33;display:flex;align-items:center;justify-content:center;color:{{ $couleur }};font-size:1.2rem;flex-shrink:0;">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#fff;font-size:0.95rem;display:flex;align-items:center;gap:0.5rem;">
                        {{ $cat->name }}
                        <a href="javascript:void(0)" onclick="let nvc = prompt('Modifier :', '{{ addslashes($cat->name) }}'); if(nvc !== null && nvc.trim() !== ''){ document.getElementById('input-edit-cat-{{ $cat->id }}').value = nvc.trim(); document.getElementById('form-edit-cat-{{ $cat->id }}').submit(); }" style="color:#FFD700;opacity:0.5;font-size:0.8rem;transition:0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.5'"><i class="fas fa-pencil-alt"></i></a>
                    </div>
                    <div style="font-size:0.72rem;color:var(--gray-600);margin-top:0.1rem;">{{ $iconClass }}</div>
                </div>
            </div>

            {{-- Form hidden modifier nom --}}
            <form method="POST" action="{{ route('admin.categories.update', $cat->id) }}" style="display:none;" id="form-edit-cat-{{ $cat->id }}">
                @csrf @method('PUT')
                <input type="hidden" name="name" id="input-edit-cat-{{ $cat->id }}" value="">
                <input type="hidden" name="icon" value="{{ $cat->icon }}">
                <input type="hidden" name="color" value="{{ $cat->color }}">
            </form>

            {{-- Bouton supprimer --}}
            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                    style="background:none;border:none;color:var(--gray-600);cursor:pointer;padding:0.3rem;border-radius:6px;transition:0.2s;"
                    onmouseover="this.style.color='#EF4444';this.style.background='#EF444411'"
                    onmouseout="this.style.color='var(--gray-600)';this.style.background='none'"
                    onclick="return confirm('Supprimer la catégorie \"{{ $cat->name }}\" ?')">
                    <i class="fas fa-trash" style="font-size:0.85rem;"></i>
                </button>
            </form>
        </div>

        <p style="font-size:0.8rem;color:var(--gray-500);margin-bottom:1rem;line-height:1.5;">
            {{ $cat->description ? Str::limit($cat->description, 80) : 'Aucune description.' }}
        </p>

        <div style="display:flex;gap:1rem;font-size:0.78rem;border-top:1px solid rgba(255,255,255,0.05);padding-top:0.75rem;">
            <span style="color:var(--gray-500);">
                <i class="fas fa-briefcase" style="color:{{ $couleur }};"></i>
                {{ $cat->prestations_count ?? 0 }} prestation(s)
            </span>
            <span style="background:{{ $couleur }}22;color:{{ $couleur }};padding:0.1rem 0.4rem;border-radius:4px;font-size:0.7rem;font-weight:700;margin-left:auto;">
                {{ $couleur }}
            </span>
        </div>
    </div>
    @endforeach
</div>

@if($categories->isEmpty())
<div style="text-align:center;padding:4rem;color:var(--gray-500);">
    <i class="fas fa-tags" style="font-size:3rem;opacity:0.2;display:block;margin-bottom:1rem;"></i>
    Aucune catégorie créée. Commencez par en ajouter une.
</div>
@endif

</div>
@endsection

@section('scripts')
<script>
document.getElementById('colorPicker')?.addEventListener('input', function() {
    document.getElementById('colorText').value = this.value;
});
</script>
@endsection
