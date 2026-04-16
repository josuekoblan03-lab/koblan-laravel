@extends('layouts.dashboard')

@section('content')
<div class="db-wrap">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-user-check" style="color:#F59E0B;"></i> Gestion des <span style="color:#F59E0B;">Prestataires</span>
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">{{ $prestataires->count() }} prestataire(s) au total</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

@if(session('success'))
<div style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.5);color:#ef4444;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('error') }}</div>
@endif

{{-- STATS RAPIDES --}}
@php
$total     = $prestataires->count();
$valides   = $prestataires->where('is_verified', true)->count();
$attente   = $prestataires->where('is_verified', false)->count();
$suspendus = $prestataires->where('is_active', false)->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['Total',     $total,     '#3B82F6'],
        ['Validés',   $valides,   '#22C55E'],
        ['En attente',$attente,   '#F59E0B'],
        ['Suspendus', $suspendus, '#EF4444'],
    ] as [$lbl, $val, $col])
    <div style="padding:1.25rem;background:var(--dark-100);border:1px solid var(--glass-border);border-radius:14px;border-bottom:3px solid {{ $col }};">
        <div style="font-family:var(--font-display);font-size:2rem;font-weight:900;color:{{ $col }};">{{ $val }}</div>
        <div style="font-size:0.78rem;color:var(--gray-500);">{{ $lbl }}</div>
    </div>
    @endforeach
</div>

{{-- TABLEAU --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;overflow-x:auto;">
    {{-- Filtre onglets --}}
    <div style="display:flex;border-bottom:1px solid var(--glass-border);background:var(--dark-200);">
        <button onclick="filterProv('all')"    id="tab-all"     class="prov-tab active-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gold-300);font-weight:600;cursor:pointer;font-size:0.88rem;border-bottom:2px solid var(--gold-300);">Tous ({{ $total }})</button>
        <button onclick="filterProv('pending')" id="tab-pending" class="prov-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:0.88rem;">En attente ({{ $attente }})</button>
        <button onclick="filterProv('valid')"   id="tab-valid"   class="prov-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:0.88rem;">Validés ({{ $valides }})</button>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;min-width:900px;">
        <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                <th style="padding:1rem 1.5rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;">Prestataire</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Contact</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Localisation</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Prestations</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Statut</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($prestataires as $p)
        @php
            $rowStatus = $p->is_verified ? 'valid' : 'pending';
        @endphp
        <tr class="prov-row" data-status="{{ $rowStatus }}"
            style="border-bottom:1px solid rgba(255,255,255,0.03);transition:0.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.02)'"
            onmouseout="this.style.background='transparent'">
            <td style="padding:1rem 1.5rem;">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#F59E0B,#D97706);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;font-size:0.75rem;flex-shrink:0;">
                        {{ strtoupper(mb_substr($p->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;color:#fff;">{{ $p->name }}</div>
                        <div style="font-size:0.72rem;color:var(--gray-500);">ID #{{ $p->id }}</div>
                    </div>
                </div>
            </td>
            <td style="padding:1rem;color:var(--gray-400);font-size:0.82rem;">
                <div>{{ $p->email }}</div>
                <div style="margin-top:0.2rem;">{{ $p->phone ?? '—' }}</div>
            </td>
            <td style="padding:1rem;color:var(--gray-400);font-size:0.82rem;">
                <i class="fas fa-map-marker-alt" style="color:#F59E0B;font-size:0.7rem;"></i>
                {{ $p->city?->name ?? '—' }}
            </td>
            <td style="padding:1rem;text-align:center;">
                <span style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;color:#3B82F6;">{{ $p->prestations_count ?? 0 }}</span>
            </td>
            <td style="padding:1rem;text-align:center;">
                @if(!$p->is_active)
                    <span style="background:#EF444422;color:#EF4444;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">Suspendu</span>
                @elseif($p->is_verified)
                    <span style="background:#22C55E22;color:#22C55E;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">✓ Validé</span>
                @else
                    <span style="background:#F59E0B22;color:#F59E0B;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">⏳ En attente</span>
                @endif
            </td>
            <td style="padding:1rem;text-align:center;">
                <div style="display:flex;justify-content:center;gap:0.4rem;flex-wrap:wrap;">
                    @if(!$p->is_verified && $p->is_active)
                    <form method="POST" action="{{ route('admin.providers.validate', $p->id) }}" style="margin:0;" class="ajax-form">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-sm"
                            style="background:#22C55E22;color:#22C55E;border:1px solid #22C55E44;border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;cursor:pointer;"
                            onclick="return confirm('Valider ce prestataire ?')">
                            <i class="fas fa-check"></i> Valider
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.providers.reject', $p->id) }}" style="margin:0;" class="ajax-form">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-sm"
                            style="background:#EF444422;color:#EF4444;border:1px solid #EF444444;border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;cursor:pointer;"
                            onclick="return confirm('Refuser ce prestataire ?')">
                            <i class="fas fa-times"></i> Refuser
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('provider.profile', $p->id) }}" target="_blank"
                       style="background:rgba(255,255,255,0.05);color:var(--gray-400);border:1px solid var(--glass-border);border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($prestataires->isEmpty())
    <div style="text-align:center;padding:4rem;color:var(--gray-500);">Aucun prestataire inscrit.</div>
    @endif
</div>

</div>
@endsection

@section('scripts')
<script>
function filterProv(status) {
    document.querySelectorAll('.prov-row').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
    document.querySelectorAll('.prov-tab').forEach(t => {
        t.style.color = 'var(--gray-400)';
        t.style.borderBottom = 'none';
        t.classList.remove('active-tab');
    });
    const active = document.getElementById('tab-' + status);
    if (active) { 
        active.style.color = 'var(--gold-300)'; 
        active.style.borderBottom = '2px solid var(--gold-300)'; 
        active.classList.add('active-tab');
    }
}
filterProv('all');
</script>
@endsection
