@extends('layouts.dashboard')

@section('content')
<style>
.service-admin-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1rem;
    border-radius: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    transition: 0.15s;
}
.service-admin-row:hover { background: rgba(255,255,255,0.02); }
</style>

<div class="db-wrap">

{{-- EN-TÊTE --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-briefcase" style="color:#06B6D4;"></i> Gestion des <span style="color:#06B6D4;">Services</span>
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">Types de services disponibles sur la plateforme.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

@if(session('success'))
<div style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('success') }}</div>
@endif

{{-- FORM AJOUT --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;margin-bottom:2rem;">
    <h2 style="font-family:var(--font-alt);font-size:1rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fas fa-plus-circle" style="color:#06B6D4;"></i> Ajouter un service
    </h2>
    <form method="POST" action="{{ route('admin.services.store') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
        @csrf
        <div style="flex:2;min-width:200px;">
            <label style="display:block;font-size:0.82rem;color:var(--gray-400);margin-bottom:0.4rem;">Nom du service</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Coiffure femme à domicile" required>
        </div>
        <div style="flex:1;min-width:180px;">
            <label style="display:block;font-size:0.82rem;color:var(--gray-400);margin-bottom:0.4rem;">Catégorie</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Choisir --</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-gold">
            <i class="fas fa-plus"></i> Ajouter
        </button>
    </form>
</div>

{{-- LISTE PAR CATÉGORIE --}}
@php
$byCategory = [];
foreach ($services_list as $svc) {
    if ($svc->category) {
        $byCategory[$svc->category->name][] = $svc;
    }
}
@endphp

@foreach($byCategory as $catName => $svcs)
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.5rem;margin-bottom:1.5rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h3 style="font-size:0.95rem;font-weight:700;color:var(--gold-300);display:flex;align-items:center;gap:0.5rem;">
            <i class="fas fa-folder-open"></i> {{ $catName }}
        </h3>
        <span style="font-size:0.75rem;color:var(--gray-500);">{{ count($svcs) }} service(s)</span>
    </div>
    @foreach($svcs as $svc)
    <div class="service-admin-row">
        <div style="display:flex;align-items:center;gap:0.75rem;">
            <div style="width:8px;height:8px;border-radius:50%;background:#06B6D4;flex-shrink:0;"></div>
            <span style="font-weight:600;color:var(--gray-200);">{{ $svc->name }}</span>
            <span style="font-size:0.72rem;color:var(--gray-500);">{{ $svc->prestations_count ?? 0 }} prestation(s)</span>
        </div>
        <div style="display:flex;gap:0.5rem;margin:0;">
            {{-- FORMULAIRE MODIFICATION --}}
            <form method="POST" action="{{ route('admin.services.update', $svc->id) }}" style="margin:0;" id="form-edit-{{ $svc->id }}">
                @csrf @method('PUT')
                <input type="hidden" name="name" id="input-edit-{{ $svc->id }}" value="">
                <button type="button" style="background:none;border:none;color:var(--gray-600);cursor:pointer;font-size:0.8rem;padding:0.3rem;"
                    onclick="let nv = prompt('Modifier le nom du service :', '{{ addslashes($svc->name) }}'); if(nv !== null && nv.trim() !== ''){ document.getElementById('input-edit-{{ $svc->id }}').value = nv.trim(); document.getElementById('form-edit-{{ $svc->id }}').submit(); }"
                    title="Modifier le nom">
                    <i class="fas fa-edit"></i>
                </button>
            </form>

            {{-- FORMULAIRE SUPPRESSION --}}
            <form method="POST" action="{{ route('admin.services.destroy', $svc->id) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:var(--gray-600);cursor:pointer;font-size:0.8rem;padding:0.3rem;"
                    onclick="return confirm('Supprimer le service &quot;{{ addslashes($svc->name) }}&quot; ? Attention, les prestations associées seront impactées.')"
                    onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='var(--gray-600)'"
                    title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endforeach

@if(empty($byCategory))
<div style="text-align:center;padding:4rem 0;color:var(--gray-500);">
    <i class="fas fa-briefcase" style="font-size:3rem;opacity:0.2;display:block;margin-bottom:1rem;"></i>
    <p>Aucun service trouvé.</p>
</div>
@endif

</div>
@endsection
