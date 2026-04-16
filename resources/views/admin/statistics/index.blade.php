@extends('layouts.dashboard')

@section('content')
<div class="db-wrap">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-chart-line" style="color:#8B5CF6;"></i> <span style="color:#8B5CF6;">Analytics</span> Plateforme
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">Aperçu des performances KOBLAN sur les 12 derniers mois.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

{{-- CHARTS GRID --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem;">

    {{-- Chart Inscriptions --}}
    <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;">
        <h2 style="font-family:var(--font-alt);font-size:1rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
            <i class="fas fa-user-plus" style="color:#3B82F6;"></i> Inscriptions Mensuelles
        </h2>
        <canvas id="chartRegistrations" height="200"></canvas>
        @if($monthly_registrations->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--gray-600);">
            <i class="fas fa-chart-bar" style="font-size:2rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>
            Aucune donnée d'inscription disponible.
        </div>
        @endif
    </div>

    {{-- Chart Commandes --}}
    <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;">
        <h2 style="font-family:var(--font-alt);font-size:1rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
            <i class="fas fa-receipt" style="color:#22C55E;"></i> Commandes Mensuelles
        </h2>
        <canvas id="chartOrders" height="200"></canvas>
        @if($monthly_orders->isEmpty())
        <div style="text-align:center;padding:3rem;color:var(--gray-600);">
            <i class="fas fa-chart-bar" style="font-size:2rem;display:block;margin-bottom:0.75rem;opacity:0.3;"></i>
            Aucune commande enregistrée.
        </div>
        @endif
    </div>
</div>

{{-- TOP CATÉGORIES --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;margin-bottom:2rem;">
    <h2 style="font-family:var(--font-alt);font-size:1rem;font-weight:700;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fas fa-trophy" style="color:#FFD700;"></i> Top Catégories par Prestations
    </h2>
    @if($top_categories->isNotEmpty())
    @php
        $maxNb = $top_categories->max('prestations_count') ?: 1;
        $colors = ['#FFD700','#F77F00','#3B82F6','#22C55E','#8B5CF6','#EF4444','#06B6D4','#F59E0B'];
    @endphp
    <div style="display:flex;flex-direction:column;gap:0.75rem;">
        @foreach($top_categories as $i => $cat)
        @php $pct = round(($cat->prestations_count / $maxNb) * 100); $col = $colors[$i % count($colors)]; @endphp
        <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;font-size:0.85rem;">
                <span style="color:var(--gray-300);font-weight:600;">
                    <span style="color:{{ $col }};margin-right:0.5rem;">#{{ $i+1 }}</span>
                    {{ $cat->name }}
                </span>
                <span style="color:{{ $col }};font-weight:700;">{{ $cat->prestations_count }} prestations</span>
            </div>
            <div style="height:8px;background:rgba(255,255,255,0.05);border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $col }};border-radius:4px;transition:width 1.5s ease;"></div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:3rem;color:var(--gray-500);">Aucune donnée de catégorie.</div>
    @endif
</div>

{{-- TABLEAUX DONNÉES BRUTES --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;">
    <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;">
        <h3 style="font-family:var(--font-alt);font-size:0.95rem;font-weight:700;margin-bottom:1rem;color:#3B82F6;">
            <i class="fas fa-user-plus"></i> Inscriptions par mois
        </h3>
        @if($monthly_registrations->isNotEmpty())
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
            @foreach($monthly_registrations->take(8) as $r)
            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                <td style="padding:0.6rem 0;color:var(--gray-400);">{{ $r->mois }}</td>
                <td style="padding:0.6rem 0;text-align:right;font-weight:700;color:#3B82F6;">{{ $r->nb }}</td>
            </tr>
            @endforeach
        </table>
        @else
        <p style="color:var(--gray-600);font-size:0.82rem;">Aucune donnée.</p>
        @endif
    </div>

    <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;padding:1.75rem;">
        <h3 style="font-family:var(--font-alt);font-size:0.95rem;font-weight:700;margin-bottom:1rem;color:#22C55E;">
            <i class="fas fa-receipt"></i> Commandes par mois
        </h3>
        @if($monthly_orders->isNotEmpty())
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
            @foreach($monthly_orders->take(8) as $r)
            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                <td style="padding:0.6rem 0;color:var(--gray-400);">{{ $r->mois }}</td>
                <td style="padding:0.6rem 0;text-align:right;font-weight:700;color:#22C55E;">{{ $r->nb }}</td>
            </tr>
            @endforeach
        </table>
        @else
        <p style="color:var(--gray-600);font-size:0.82rem;">Aucune commande.</p>
        @endif
    </div>
</div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('turbo:load', () => {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(10,10,10,0.9)',
                borderColor: 'rgba(255,215,0,0.3)',
                borderWidth: 1,
                titleColor: '#FFD700',
                bodyColor: '#fff',
            }
        },
        scales: {
            x: { ticks: { color: '#6B7280', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.03)' } },
            y: { ticks: { color: '#6B7280', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.05)' }, beginAtZero: true }
        }
    };

    @if($monthly_registrations->isNotEmpty())
    const regData = @json($monthly_registrations->reverse()->values());
    new Chart(document.getElementById('chartRegistrations'), {
        type: 'bar',
        data: {
            labels: regData.map(d => d.mois),
            datasets: [{
                data: regData.map(d => d.nb),
                backgroundColor: 'rgba(59,130,246,0.3)',
                borderColor: '#3B82F6',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(59,130,246,0.6)',
            }]
        },
        options: chartDefaults
    });
    @endif

    @if($monthly_orders->isNotEmpty())
    const ordData = @json($monthly_orders->reverse()->values());
    new Chart(document.getElementById('chartOrders'), {
        type: 'line',
        data: {
            labels: ordData.map(d => d.mois),
            datasets: [{
                data: ordData.map(d => d.nb),
                borderColor: '#22C55E',
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0,0,0,200);
                    g.addColorStop(0,'rgba(34,197,94,0.3)');
                    g.addColorStop(1,'rgba(34,197,94,0)');
                    return g;
                },
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#22C55E',
                pointRadius: 4,
            }]
        },
        options: chartDefaults
    });
    @endif
});
</script>
@endsection
