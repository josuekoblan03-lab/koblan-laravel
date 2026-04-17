@extends('layouts.dashboard')

@section('title', 'Validation des Prestations — KOBLAN Admin')

@section('content')
<div class="db-wrap">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-clipboard-check" style="color:#FFD700;"></i> Validation des <span style="color:#FFD700;">Prestations</span>
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">Approuvez ou refusez les services soumis par les prestataires</p>
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
$pending  = $prestations->where('status', 'pending')->count();
$active   = $prestations->where('status', 'active')->count();
$rejected = $prestations->where('status', 'rejected')->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['Total', $prestations->count(), '#3B82F6'],
        ['En attente', $pending,  '#F59E0B'],
        ['Approuvés',  $active,   '#22C55E'],
        ['Refusés',    $rejected, '#EF4444'],
    ] as [$lbl, $val, $col])
    <div style="padding:1.25rem;background:var(--dark-100);border:1px solid var(--glass-border);border-radius:14px;border-bottom:3px solid {{ $col }};">
        <div style="font-family:var(--font-display);font-size:2rem;font-weight:900;color:{{ $col }};">{{ $val }}</div>
        <div style="font-size:0.78rem;color:var(--gray-500);">{{ $lbl }}</div>
    </div>
    @endforeach
</div>

{{-- ONGLETS --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;overflow:hidden;">
    <div style="display:flex;border-bottom:1px solid var(--glass-border);background:var(--dark-200);">
        <button onclick="filterTab('all')"      id="tab-all"      class="prest-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gold-300);font-weight:600;cursor:pointer;font-size:0.88rem;border-bottom:2px solid var(--gold-300);">Tous ({{ $prestations->count() }})</button>
        <button onclick="filterTab('pending')"  id="tab-pending"  class="prest-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:0.88rem;">⏳ En attente ({{ $pending }})</button>
        <button onclick="filterTab('active')"   id="tab-active"   class="prest-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:0.88rem;">✅ Approuvés ({{ $active }})</button>
        <button onclick="filterTab('rejected')" id="tab-rejected" class="prest-tab" style="padding:1rem 1.5rem;background:none;border:none;color:var(--gray-400);cursor:pointer;font-size:0.88rem;">❌ Refusés ({{ $rejected }})</button>
    </div>

    @if($prestations->isEmpty())
    <div style="text-align:center;padding:4rem;color:var(--gray-500);">
        <i class="fas fa-box-open" style="font-size:3rem;opacity:0.3;display:block;margin-bottom:1rem;"></i>
        Aucune prestation soumise pour le moment.
    </div>
    @else
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;min-width:900px;">
        <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                <th style="padding:1rem 1.5rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;">Prestation</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Prestataire</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Catégorie</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Prix</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Statut</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Date</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($prestations as $p)
        <tr class="prest-row" data-status="{{ $p->status }}"
            style="border-bottom:1px solid rgba(255,255,255,0.03);transition:0.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.02)'"
            onmouseout="this.style.background='transparent'">
            <td style="padding:1rem 1.5rem;">
                <div style="font-weight:700;color:#fff;">{{ \Str::limit($p->title, 40) }}</div>
                <div style="font-size:0.72rem;color:var(--gray-500);margin-top:0.2rem;">{{ \Str::limit($p->description, 60) }}</div>
            </td>
            <td style="padding:1rem;color:var(--gray-300);font-size:0.82rem;">
                <div style="font-weight:600;">{{ $p->user?->name ?? '—' }}</div>
                <div style="font-size:0.72rem;color:var(--gray-500);">{{ $p->user?->email }}</div>
            </td>
            <td style="padding:1rem;color:var(--gray-400);font-size:0.82rem;">
                {{ $p->serviceType?->category?->name ?? '—' }}<br>
                <span style="color:var(--gray-500);font-size:0.72rem;">{{ $p->serviceType?->name ?? '—' }}</span>
            </td>
            <td style="padding:1rem;text-align:center;">
                <span style="font-family:var(--font-display);font-size:1rem;font-weight:800;color:#FFD700;">{{ number_format($p->price, 0, ',', ' ') }} FCFA</span>
            </td>
            <td style="padding:1rem;text-align:center;">
                @if($p->status === 'pending')
                    <span style="background:#F59E0B22;color:#F59E0B;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">⏳ En attente</span>
                @elseif($p->status === 'active')
                    <span style="background:#22C55E22;color:#22C55E;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">✅ Approuvé</span>
                @elseif($p->status === 'rejected')
                    <span style="background:#EF444422;color:#EF4444;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">❌ Refusé</span>
                @else
                    <span style="background:rgba(100,116,139,0.2);color:#94a3b8;padding:0.25rem 0.75rem;border-radius:99px;font-size:0.72rem;font-weight:700;">{{ $p->status }}</span>
                @endif
            </td>
            <td style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.78rem;">
                {{ $p->created_at->format('d/m/Y') }}
            </td>
            <td style="padding:1rem;text-align:center;">
                <div style="display:flex;justify-content:center;gap:0.4rem;flex-wrap:wrap;">
                    @if($p->status === 'pending' || $p->status === 'rejected')
                    <form method="POST" action="{{ route('admin.prestations.approve', $p->id) }}" style="margin:0;">
                        @csrf @method('PUT')
                        <button type="submit" style="background:#22C55E22;color:#22C55E;border:1px solid #22C55E44;border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;cursor:pointer;"
                            onclick="return confirm('Approuver cette prestation ?')">
                            <i class="fas fa-check"></i> Approuver
                        </button>
                    </form>
                    @endif
                    @if($p->status === 'pending' || $p->status === 'active')
                    <form method="POST" action="{{ route('admin.prestations.reject', $p->id) }}" style="margin:0;">
                        @csrf @method('PUT')
                        <button type="submit" style="background:#EF444422;color:#EF4444;border:1px solid #EF444444;border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;cursor:pointer;"
                            onclick="return confirm('Refuser cette prestation ?')">
                            <i class="fas fa-times"></i> Refuser
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('services.show', $p->id) }}" target="_blank"
                       style="background:rgba(255,255,255,0.05);color:var(--gray-400);border:1px solid var(--glass-border);border-radius:8px;padding:0.35rem 0.7rem;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem;">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>

</div>

@section('scripts')
<script>
function filterTab(status) {
    document.querySelectorAll('.prest-row').forEach(row => {
        row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
    });
    document.querySelectorAll('.prest-tab').forEach(t => {
        t.style.color = 'var(--gray-400)';
        t.style.borderBottom = 'none';
    });
    const active = document.getElementById('tab-' + status);
    if (active) {
        active.style.color = 'var(--gold-300)';
        active.style.borderBottom = '2px solid var(--gold-300)';
    }
}
</script>
@endsection
@endsection
