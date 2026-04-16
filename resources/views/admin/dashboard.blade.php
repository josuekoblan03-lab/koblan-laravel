@extends('layouts.dashboard')

@section('content')
<style>
/* ── ADMIN DASHBOARD PREMIUM ───────────────── */
.admin-hero {
    background: linear-gradient(135deg, rgba(34,197,94,0.08), rgba(255,215,0,0.05));
    border: 1px solid rgba(34,197,94,0.2);
    border-radius: 20px;
    padding: 2rem 2.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1.5rem;
    position: relative;
    overflow: hidden;
}
.admin-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -10%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(34,197,94,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.admin-status-dot {
    width: 8px; height: 8px;
    background: #22C55E;
    border-radius: 50%;
    box-shadow: 0 0 8px #22C55E;
    animation: pulse-dot 2s infinite;
    display: inline-block;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.3); }
}

/* KPI GRID */
.admin-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2.5rem;
}
.admin-kpi-card {
    padding: 1.5rem;
    background: var(--dark-100);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: default;
}
.admin-kpi-card:hover {
    transform: translateY(-4px);
    border-color: var(--kpi-accent, var(--gold-300));
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}
.admin-kpi-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: var(--kpi-accent, var(--gold-300));
    opacity: 0.5;
}
.kpi-num {
    font-family: var(--font-display);
    font-size: 2.2rem;
    font-weight: 900;
    color: var(--kpi-accent, var(--gold-300));
    line-height: 1;
    margin: 0.75rem 0 0.25rem;
}
.kpi-lbl { font-size: 0.78rem; color: var(--gray-500); }
.kpi-ico {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    background: rgba(0,0,0,0.3);
    color: var(--kpi-accent, var(--gold-300));
}

/* MAIN GRID */
.admin-main-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 2rem;
}
@media(max-width: 1100px) { .admin-main-grid { grid-template-columns: 1fr; } }

/* SECTION CARD */
.admin-section {
    background: var(--dark-100);
    border: 1px solid var(--glass-border);
    border-radius: 16px;
    padding: 1.75rem;
    margin-bottom: 1.75rem;
}
.admin-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}
.admin-section-title {
    font-family: var(--font-alt);
    font-size: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.admin-section-link {
    font-size: 0.8rem;
    color: var(--gold-400);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    border: 1px solid rgba(255,215,0,0.2);
    transition: 0.2s;
}
.admin-section-link:hover {
    background: rgba(255,215,0,0.08);
    color: var(--gold-300);
    border-color: rgba(255,215,0,0.4);
}

/* VALIDATION ROW */
.validation-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--dark-200);
    border-radius: 12px;
    margin-bottom: 0.75rem;
    transition: 0.2s;
}
.validation-row:hover { background: var(--dark-300); }

/* TABLE */
.admin-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
.admin-table th {
    padding: 0.75rem 0;
    color: var(--gray-500);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    text-align: left;
}
.admin-table td {
    padding: 0.9rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    vertical-align: middle;
}
.admin-table tr:hover td { background: rgba(255,255,255,0.01); }

/* STATUS BADGE */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.6rem;
    border-radius: 99px;
    font-size: 0.7rem;
    font-weight: 700;
}

/* ACTION GRID */
.action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 1rem;
    background: var(--dark-200);
    border: 1px solid var(--glass-border);
    border-radius: 14px;
    text-decoration: none;
    color: var(--gray-300);
    font-size: 0.78rem;
    font-weight: 600;
    transition: all 0.25s ease;
    cursor: pointer;
    text-align: center;
}
.action-btn:hover {
    transform: translateY(-3px);
    border-color: rgba(255,215,0,0.3);
    background: var(--dark-300);
    color: var(--gold-300);
}
.action-btn i { font-size: 1.3rem; }

/* HEALTH BAR */
.health-bar-wrap { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
.health-bar { flex: 1; height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden; }
.health-bar-fill { height: 100%; border-radius: 3px; transition: width 1.5s ease; }
</style>

<div class="db-wrap">

{{-- ═══════════════════════════════════════════
     1. HERO ADMIN
═══════════════════════════════════════════ --}}
<div class="admin-hero db-reveal">
    <div>
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#22C55E,#16a34a);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#fff;box-shadow:0 0 20px rgba(34,197,94,0.3);">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h1 style="font-family:var(--font-display);font-size:1.6rem;font-weight:900;color:#fff;margin:0;">
                    Super <span style="color:#22C55E;">Admin</span> — KOBLAN
                </h1>
                <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--gray-400);margin-top:0.25rem;">
                    <span class="admin-status-dot"></span>
                    Systèmes opérationnels · {{ now()->format('d M Y, H:i') }}
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.providers.index') }}" class="btn btn-outline-gold btn-sm">
            <i class="fas fa-user-check"></i> Valider prestataires
            @if($stats['en_attente'] > 0)
            <span style="background:#F59E0B;color:#000;border-radius:99px;padding:1px 7px;font-size:0.7rem;font-weight:800;">{{ $stats['en_attente'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.statistics') }}" class="btn btn-dark btn-sm">
            <i class="fas fa-chart-line"></i> Analytics
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-dark btn-sm">
            <i class="fas fa-tags"></i> Catégories
        </a>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     2. KPIs GLOBAUX
═══════════════════════════════════════════ --}}
<div class="admin-kpi-grid">
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#3B82F6;">
        <div class="kpi-ico"><i class="fas fa-users"></i></div>
        <div class="kpi-num" data-count="{{ $stats['total_users'] }}">{{ $stats['total_users'] }}</div>
        <div class="kpi-lbl">Utilisateurs inscrits</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#FFD700;">
        <div class="kpi-ico"><i class="fas fa-tools"></i></div>
        <div class="kpi-num" data-count="{{ $stats['prestataires'] }}">{{ $stats['prestataires'] }}</div>
        <div class="kpi-lbl">Prestataires inscrits</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#F59E0B;">
        <div class="kpi-ico"><i class="fas fa-user-clock"></i></div>
        <div class="kpi-num" data-count="{{ $stats['en_attente'] }}">{{ $stats['en_attente'] }}</div>
        <div class="kpi-lbl">En attente validation</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#22C55E;">
        <div class="kpi-ico"><i class="fas fa-shopping-cart"></i></div>
        <div class="kpi-num" data-count="{{ $stats['total_commandes'] }}">{{ $stats['total_commandes'] }}</div>
        <div class="kpi-lbl">Commandes totales</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#8B5CF6;">
        <div class="kpi-ico"><i class="fas fa-briefcase"></i></div>
        <div class="kpi-num" data-count="{{ $stats['total_prestations'] }}">{{ $stats['total_prestations'] }}</div>
        <div class="kpi-lbl">Prestations actives</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#EF4444;">
        <div class="kpi-ico"><i class="fas fa-coins"></i></div>
        <div class="kpi-num" style="font-size:1.1rem;">{{ number_format($stats['revenus'], 0, ',', ' ') }} F</div>
        <div class="kpi-lbl">Commissions plateforme</div>
    </div>
    <div class="admin-kpi-card db-reveal" style="--kpi-accent:#06B6D4;">
        <div class="kpi-ico"><i class="fas fa-star"></i></div>
        <div class="kpi-num" data-count="{{ $stats['total_avis'] }}">{{ $stats['total_avis'] }}</div>
        <div class="kpi-lbl">Avis clients</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     3. GRILLE PRINCIPALE
═══════════════════════════════════════════ --}}
<div class="admin-main-grid">

    {{-- COL GAUCHE --}}
    <div>

        {{-- VALIDATIONS EN ATTENTE --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-header">
                <div class="admin-section-title">
                    <i class="fas fa-user-clock" style="color:#F59E0B;"></i>
                    Validations en attente
                    @if($prestataires_attente->count() > 0)
                        <span style="background:#F59E0B22;color:#F59E0B;padding:2px 8px;border-radius:99px;font-size:0.72rem;">{{ $prestataires_attente->count() }}</span>
                    @endif
                </div>
                <a href="{{ route('admin.users.index') }}" class="admin-section-link">
                    Gérer tous <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
                </a>
            </div>

            @if($prestataires_attente->count() > 0)
                @foreach($prestataires_attente as $p)
                <div class="validation-row">
                    <div style="display:flex;align-items:center;gap:0.85rem;">
                        <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#F59E0B,#D97706);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;font-size:0.8rem;flex-shrink:0;">
                            {{ strtoupper(mb_substr($p->name, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:0.9rem;color:#fff;">{{ $p->name }}</div>
                            <div style="font-size:0.73rem;color:var(--gray-500);">
                                <i class="fas fa-envelope" style="color:#F59E0B;font-size:0.65rem;"></i> {{ $p->email }}
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        <form method="POST" action="{{ route('admin.users.update', $p->id) }}" style="margin:0;">
                            @csrf @method('PUT')
                            <input type="hidden" name="is_verified" value="1">
                            <button type="submit" style="background:#22C55E22;color:#22C55E;border:1px solid #22C55E44;border-radius:8px;padding:0.4rem 0.75rem;font-size:0.8rem;cursor:pointer;" onclick="return confirm('Valider le compte de {{ $p->name }} ?')">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $p->id) }}" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:#EF444422;color:#EF4444;border:1px solid #EF444444;border-radius:8px;padding:0.4rem 0.75rem;font-size:0.8rem;cursor:pointer;" onclick="return confirm('Refuser ce prestataire ?')">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align:center;padding:2.5rem 0;color:var(--gray-500);">
                    <i class="fas fa-check-circle" style="font-size:2.5rem;color:#22C55E;opacity:0.5;display:block;margin-bottom:0.75rem;"></i>
                    Aucun prestataire en attente de validation ✔️
                </div>
            @endif
        </div>

        {{-- DERNIÈRES COMMANDES --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-header">
                <div class="admin-section-title">
                    <i class="fas fa-receipt" style="color:#22C55E;"></i> Flux des Commandes
                </div>
                <a href="{{ route('admin.services.index') }}" class="admin-section-link">
                    Voir toutes <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
                </a>
            </div>

            @if($recent_orders->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $statusMap = [
                        'pending'     => ['color'=>'#F59E0B', 'label'=>'En attente',  'bg'=>'#F59E0B22'],
                        'confirmed'   => ['color'=>'#22C55E', 'label'=>'Confirmée',   'bg'=>'#22C55E22'],
                        'in_progress' => ['color'=>'#3B82F6', 'label'=>'En cours',    'bg'=>'#3B82F622'],
                        'completed'   => ['color'=>'#8B5CF6', 'label'=>'Terminée',    'bg'=>'#8B5CF622'],
                        'cancelled'   => ['color'=>'#EF4444', 'label'=>'Annulée',     'bg'=>'#EF444422'],
                    ];
                    @endphp
                    @foreach($recent_orders as $cmd)
                    @php $st = $statusMap[$cmd->status] ?? ['color'=>'#666','label'=>$cmd->status,'bg'=>'#66666622']; @endphp
                    <tr>
                        <td><span style="color:var(--gold-300);font-weight:700;">#{{ str_pad($cmd->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td style="font-weight:600;">{{ $cmd->client?->name ?? '—' }}</td>
                        <td style="color:var(--gray-400);">{{ $cmd->created_at->format('d/m/Y') }}</td>
                        <td style="font-weight:700;color:#fff;">{{ number_format($cmd->amount ?? 0, 0, ',', ' ') }} F</td>
                        <td>
                            <span class="status-badge" style="background:{{ $st['bg'] }};color:{{ $st['color'] }};">{{ $st['label'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Aucune commande pour le moment.</p>
            @endif
        </div>

        {{-- INSCRIPTIONS RÉCENTES --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-header">
                <div class="admin-section-title">
                    <i class="fas fa-user-plus" style="color:#3B82F6;"></i> Inscriptions Récentes
                </div>
                <a href="{{ route('admin.users.index') }}" class="admin-section-link">
                    Gérer <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
                </a>
            </div>

            @if($recent_users->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $u)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--gold-300),#F77F00);display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;font-size:0.65rem;flex-shrink:0;">
                                    {{ strtoupper(mb_substr($u->name, 0, 2)) }}
                                </div>
                                <span style="font-weight:600;">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--gray-400);font-size:0.82rem;">{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="status-badge" style="background:#22C55E22;color:#22C55E;">Admin</span>
                            @elseif($u->role === 'prestataire')
                                <span class="status-badge" style="background:#F59E0B22;color:#F59E0B;">Prestataire</span>
                                @if(!$u->is_verified)
                                    <span class="status-badge" style="background:#EF444422;color:#EF4444;margin-left:4px;">Non validé</span>
                                @endif
                            @else
                                <span class="status-badge" style="background:#3B82F622;color:#3B82F6;">Client</span>
                            @endif
                        </td>
                        <td style="color:var(--gray-500);font-size:0.8rem;">{{ $u->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p style="text-align:center;color:var(--gray-500);padding:2rem 0;">Aucun utilisateur inscrit.</p>
            @endif
        </div>

    </div>

    {{-- COL DROITE --}}
    <div style="display:flex;flex-direction:column;gap:1.75rem;">

        {{-- NAVIGATION RAPIDE --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-title" style="margin-bottom:1.25rem;">
                <i class="fas fa-rocket" style="color:var(--gold-300);"></i> Navigation Admin
            </div>
            <div class="action-grid">
                <a href="{{ route('admin.users.index') }}" class="action-btn">
                    <i class="fas fa-users" style="color:#3B82F6;"></i> Utilisateurs
                </a>
                <a href="{{ route('admin.users.index') }}" class="action-btn">
                    <i class="fas fa-user-check" style="color:#F59E0B;"></i> Prestataires
                </a>
                <a href="{{ route('admin.services.index') }}" class="action-btn">
                    <i class="fas fa-receipt" style="color:#22C55E;"></i> Commandes
                </a>
                <a href="{{ route('admin.categories.index') }}" class="action-btn">
                    <i class="fas fa-tags" style="color:var(--gold-300);"></i> Catégories
                </a>
                <a href="{{ route('admin.statistics') }}" class="action-btn">
                    <i class="fas fa-chart-line" style="color:#8B5CF6;"></i> Analytics
                </a>
                <a href="{{ route('admin.services.index') }}" class="action-btn">
                    <i class="fas fa-briefcase" style="color:#06B6D4;"></i> Services
                </a>
                <a href="{{ route('home') }}" target="_blank" class="action-btn">
                    <i class="fas fa-external-link-alt" style="color:#6B7280;"></i> Voir le site
                </a>
                <a href="{{ route('admin.dashboard') }}" class="action-btn">
                    <i class="fas fa-shield-alt" style="color:#EF4444;"></i> Sécurité
                </a>
            </div>
        </div>

        {{-- SANTÉ DE LA PLATEFORME --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-title" style="margin-bottom:1.5rem;">
                <i class="fas fa-heartbeat" style="color:#22C55E;"></i> Santé de la Plateforme
            </div>

            @php
            $tauxValidation = ($stats['prestataires'] > 0)
                ? round((($stats['prestataires'] - $stats['en_attente']) / $stats['prestataires']) * 100)
                : 100;
            $tauxCommandes = min(100, round(($stats['total_commandes'] / max(1, $stats['total_users'])) * 20));
            @endphp

            <div class="health-bar-wrap">
                <span style="font-size:0.8rem;color:var(--gray-400);min-width:120px;">Validations</span>
                <div class="health-bar"><div class="health-bar-fill" style="width:{{ $tauxValidation }}%;background:#22C55E;"></div></div>
                <span style="font-size:0.78rem;color:#22C55E;font-weight:700;min-width:36px;">{{ $tauxValidation }}%</span>
            </div>
            <div class="health-bar-wrap">
                <span style="font-size:0.8rem;color:var(--gray-400);min-width:120px;">Activité cmds</span>
                <div class="health-bar"><div class="health-bar-fill" style="width:{{ $tauxCommandes }}%;background:#3B82F6;"></div></div>
                <span style="font-size:0.78rem;color:#3B82F6;font-weight:700;min-width:36px;">{{ $tauxCommandes }}%</span>
            </div>
            <div class="health-bar-wrap">
                <span style="font-size:0.8rem;color:var(--gray-400);min-width:120px;">Serveur</span>
                <div class="health-bar"><div class="health-bar-fill" style="width:99%;background:var(--gold-300);"></div></div>
                <span style="font-size:0.78rem;color:var(--gold-300);font-weight:700;min-width:36px;">99%</span>
            </div>

            <div style="border-top:1px solid var(--glass-border);margin-top:1.25rem;padding-top:1rem;display:flex;flex-direction:column;gap:0.6rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                    <span style="color:var(--gray-400);">Base de données</span>
                    <span style="color:#22C55E;font-weight:700;"><span class="admin-status-dot" style="width:6px;height:6px;"></span> Connectée</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                    <span style="color:var(--gray-400);">Uptime</span>
                    <span style="color:#22C55E;font-weight:700;">99.9%</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;">
                    <span style="color:var(--gray-400);">Dernière activité</span>
                    <span style="color:var(--gray-300);font-weight:600;">{{ now()->format('H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- REVENUS PLATEFORME (mini chart) --}}
        <div class="admin-section db-reveal">
            <div class="admin-section-header">
                <div class="admin-section-title">
                    <i class="fas fa-coins" style="color:var(--gold-300);"></i> Revenus Plateforme
                </div>
                <a href="{{ route('admin.statistics') }}" class="admin-section-link">
                    Détails <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i>
                </a>
            </div>
            <div style="text-align:center;margin-bottom:1.25rem;">
                <div style="font-family:var(--font-display);font-size:2rem;font-weight:900;background:linear-gradient(135deg,var(--gold-300),#F77F00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    {{ number_format($stats['revenus'], 0, ',', ' ') }} FCFA
                </div>
                <div style="font-size:0.78rem;color:#22C55E;margin-top:0.25rem;">
                    <i class="fas fa-arrow-trend-up"></i> Commissions totales (10%)
                </div>
            </div>
            {{-- Mini bar chart visuel --}}
            <div style="display:flex;align-items:flex-end;justify-content:center;gap:5px;height:70px;border-bottom:1px solid var(--glass-border);padding:0 0 5px;">
                @php $bars=[30,45,25,60,40,80,55,90,70,100,65,85]; $colors=['rgba(255,215,0,0.2)','rgba(255,215,0,0.3)','rgba(255,215,0,0.2)','rgba(255,215,0,0.45)','rgba(255,215,0,0.3)','rgba(255,215,0,0.6)','rgba(255,215,0,0.4)','rgba(255,215,0,0.8)','rgba(255,215,0,0.5)','rgba(255,215,0,1)','rgba(255,215,0,0.7)','rgba(247,127,0,1)']; @endphp
                @foreach($bars as $i => $h)
                <div style="flex:1;height:{{ $h }}%;background:{{ $colors[$i] }};border-radius:4px 4px 0 0;min-width:8px;"></div>
                @endforeach
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.68rem;color:var(--gray-600);margin-top:0.4rem;">
                <span>Jan</span><span>Fév</span><span>Mar</span><span>Avr</span><span>Mai</span><span>Jun</span>
                <span>Jul</span><span>Aoû</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Déc</span>
            </div>
        </div>

    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('turbo:load', () => {
    // Count-up animation pour les KPI numbers
    document.querySelectorAll('.kpi-num[data-count]').forEach(el => {
        const target = parseInt(el.dataset.count) || 0;
        if (target === 0) return;
        let start = 0;
        const duration = 1200;
        const step = target / (duration / 16);
        const timer = setInterval(() => {
            start += step;
            if (start >= target) { el.textContent = target.toLocaleString('fr'); clearInterval(timer); }
            else { el.textContent = Math.floor(start).toLocaleString('fr'); }
        }, 16);
    });

    // Animate health bars
    document.querySelectorAll('.health-bar-fill').forEach(bar => {
        const w = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => { bar.style.width = w; }, 300);
    });
});
</script>
@endsection
