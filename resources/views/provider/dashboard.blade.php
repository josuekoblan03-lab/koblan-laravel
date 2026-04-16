@extends('layouts.dashboard')

@section('title', 'Espace Pro — KOBLAN')

@section('content')
@php
$user = Auth::user();
$initials = $user->getInitials();
$isValidated = $user->is_validated ?? false;
@endphp

<div style="position:relative;z-index:2;padding:1.5rem;display:flex;flex-direction:column;gap:1.75rem;">

<!-- HERO PRESTATAIRE -->
<section style="position:relative;padding:2.5rem;background:linear-gradient(135deg,rgba(14,14,20,0.95),rgba(20,20,30,0.98));border-radius:24px;overflow:hidden;border:1px solid rgba(255,215,0,0.2);box-shadow:0 20px 60px rgba(0,0,0,0.5);">
  <div style="position:absolute;top:-60px;right:-60px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(247,127,0,0.12),transparent 70%);pointer-events:none;"></div>
  <div style="position:relative;z-index:2;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:2rem;">
    <div>
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#FFD700,#F77F00);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#000;font-weight:900;overflow:hidden;border:3px solid rgba(255,215,0,0.4);">
          @if($user->avatar)
            <img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            {{ $initials }}
          @endif
        </div>
        <div>
          <h1 style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#fff;margin-bottom:0.25rem;">
            Espace Pro de <span style="color:#FFD700;">{{ explode(' ', $user->name)[0] }}</span>
          </h1>
          <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
            @if($isValidated)
              <span style="font-size:0.75rem;padding:0.2rem 0.6rem;background:rgba(34,197,94,0.2);color:#22C55E;border-radius:99px;border:1px solid rgba(34,197,94,0.4);"><i class="fas fa-check-circle"></i> Compte Vérifié</span>
            @else
              <span style="font-size:0.75rem;padding:0.2rem 0.6rem;background:rgba(245,158,11,0.2);color:#F59E0B;border-radius:99px;border:1px solid rgba(245,158,11,0.4);"><i class="fas fa-hourglass-half"></i> En attente de validation</span>
            @endif
            <span style="color:#FFD700;font-size:0.8rem;font-weight:700;"><i class="fas fa-star"></i> 4.8 (42 avis)</span>
          </div>
        </div>
      </div>
    </div>
    @if($isValidated)
    <div>
      <a href="{{ route('prestataire.services.create') }}" style="display:inline-flex;align-items:center;gap:0.5rem;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;padding:0.75rem 1.5rem;border-radius:12px;font-weight:700;text-decoration:none;font-size:0.9rem;">
        <i class="fas fa-plus"></i> Nouvelle Prestation
      </a>
    </div>
    @endif
  </div>
</section>

@if(!$isValidated)
<!-- ALERTE VALIDATION -->
<div style="display:flex;align-items:flex-start;gap:1rem;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.3);border-radius:16px;padding:1.5rem;color:#F59E0B;">
  <i class="fas fa-exclamation-triangle" style="font-size:1.25rem;flex-shrink:0;margin-top:0.1rem;"></i>
  <span style="font-weight:600;">Votre compte est en cours d'examen par nos administrateurs. Vous ne pouvez pas encore gérer de prestations ou recevoir de commandes publiques.</span>
</div>

<div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(245,158,11,0.3);border-radius:22px;padding:4rem 2rem;text-align:center;">
  <i class="fas fa-user-clock" style="font-size:4rem;color:#F59E0B;margin-bottom:1.5rem;display:block;"></i>
  <h2 style="font-family:'Syne',sans-serif;font-size:1.5rem;color:#fff;margin-bottom:1rem;">Validation en attente !</h2>
  <p style="color:#aaa;max-width:500px;margin:0 auto;line-height:1.7;">
    Votre profil est actuellement en cours de vérification par notre équipe administrative pour des raisons de sécurité.
    Vous n'aurez accès à votre tableau de bord et à vos fonctionnalités de prestataire qu'une fois votre profil validé.
  </p>
</div>

@else

<!-- KPI GRID -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;">

  <div style="position:relative;overflow:hidden;background:rgba(12,12,18,0.9);border:1px solid rgba(255,215,0,0.15);border-radius:20px;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
    <div style="width:50px;height:50px;border-radius:14px;background:rgba(255,215,0,0.12);color:#FFD700;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;"><i class="fas fa-wallet"></i></div>
    <div>
      <div style="font-size:1.5rem;font-weight:900;color:#fff;font-family:'Syne',sans-serif;">{{ number_format($wallet->balance ?? 0, 0, ',', ' ') }}</div>
      <div style="font-size:0.72rem;color:#777;text-transform:uppercase;letter-spacing:0.05em;">Solde FCFA</div>
    </div>
    <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(255,215,0,0.12),transparent 70%);pointer-events:none;"></div>
  </div>

  <div style="position:relative;overflow:hidden;background:rgba(12,12,18,0.9);border:1px solid rgba(34,197,94,0.15);border-radius:20px;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
    <div style="width:50px;height:50px;border-radius:14px;background:rgba(34,197,94,0.12);color:#22C55E;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;"><i class="fas fa-chart-line"></i></div>
    <div>
      <div style="font-size:1.5rem;font-weight:900;color:#fff;font-family:'Syne',sans-serif;">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }}</div>
      <div style="font-size:0.72rem;color:#777;text-transform:uppercase;letter-spacing:0.05em;">Gains Totaux FCFA</div>
    </div>
    <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(34,197,94,0.12),transparent 70%);pointer-events:none;"></div>
  </div>

  <div style="position:relative;overflow:hidden;background:rgba(12,12,18,0.9);border:1px solid rgba(59,130,246,0.15);border-radius:20px;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
    <div style="width:50px;height:50px;border-radius:14px;background:rgba(59,130,246,0.12);color:#3B82F6;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;"><i class="fas fa-briefcase"></i></div>
    <div>
      <div style="font-size:1.5rem;font-weight:900;color:#fff;font-family:'Syne',sans-serif;">{{ $stats['total_services'] ?? 0 }}</div>
      <div style="font-size:0.72rem;color:#777;text-transform:uppercase;letter-spacing:0.05em;">Services Actifs</div>
    </div>
    <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,0.12),transparent 70%);pointer-events:none;"></div>
  </div>

  <div style="position:relative;overflow:hidden;background:rgba(12,12,18,0.9);border:1px solid rgba(245,158,11,0.15);border-radius:20px;padding:1.5rem;display:flex;align-items:center;gap:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
    <div style="width:50px;height:50px;border-radius:14px;background:rgba(245,158,11,0.12);color:#F59E0B;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;"><i class="fas fa-bullseye"></i></div>
    <div>
      <div style="font-size:1.5rem;font-weight:900;color:#fff;font-family:'Syne',sans-serif;">92<small style="font-size:1rem;">%</small></div>
      <div style="font-size:0.72rem;color:#777;text-transform:uppercase;letter-spacing:0.05em;">Taux de réponse</div>
    </div>
    <div style="position:absolute;bottom:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,0.12),transparent 70%);pointer-events:none;"></div>
  </div>

</div>

<!-- GRILLE PRINCIPALE -->
<div style="display:grid;grid-template-columns:1fr 380px;gap:2rem;">
  <!-- COLONNE GAUCHE -->
  <div style="display:flex;flex-direction:column;gap:1.75rem;">

    <!-- Commandes à accepter -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-left:4px solid #F59E0B;border-radius:22px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <h2 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.5rem;">
        <i class="fas fa-concierge-bell" style="color:#FFD700;"></i> Commandes à accepter
      </h2>
      @if($pendingOrders->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:1rem;">
          @foreach($pendingOrders->take(3) as $cmd)
          <div style="padding:1rem;background:rgba(255,255,255,0.03);border-radius:12px;border:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div style="font-weight:700;font-size:0.95rem;color:#f0f0f0;margin-bottom:0.25rem;">
                {{ \Str::limit($cmd->prestation->title ?? 'Service', 30) }}
              </div>
              <div style="font-size:0.8rem;color:#aaa;">
                Par {{ $cmd->client->name ?? '—' }} — <strong style="color:#FFD700;">{{ number_format($cmd->total_amount ?? 0, 0, ',', ' ') }} FCFA</strong>
              </div>
            </div>
            <div style="display:flex;gap:0.5rem;">
              <form method="POST" action="{{ route('prestataire.orders.status', $cmd->id) }}" style="margin:0;">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="accepted">
                <button type="submit" style="width:36px;height:36px;border-radius:50%;background:#22C55E;color:#000;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;"><i class="fas fa-check"></i></button>
              </form>
              <form method="POST" action="{{ route('prestataire.orders.status', $cmd->id) }}" style="margin:0;">
                @csrf @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" style="width:36px;height:36px;border-radius:50%;background:rgba(239,68,68,0.1);color:#EF4444;border:1px solid rgba(239,68,68,0.3);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;"><i class="fas fa-times"></i></button>
              </form>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p style="color:#555;font-size:0.9rem;text-align:center;padding:1rem 0;">Aucune nouvelle demande. Soufflez ! ☕</p>
      @endif
    </div>

    <!-- Services publiés -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
        <h2 style="font-size:1.2rem;font-weight:700;color:#fff;">Services Publiés</h2>
        <a href="{{ route('prestataire.services.index') }}" style="font-size:0.85rem;color:#FFD700;text-decoration:none;">Gérer <i class="fas fa-cog"></i></a>
      </div>
      @if($services->isNotEmpty())
        <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
          <thead>
            <tr style="border-bottom:1px solid rgba(255,255,255,0.06);color:#555;text-align:left;">
              <th style="padding:0.75rem 0;">Service</th>
              <th style="padding:0.75rem 0;">Prix</th>
              <th style="padding:0.75rem 0;">Statut</th>
              <th style="padding:0.75rem 0;text-align:right;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($services->take(4) as $p)
            <tr style="border-bottom:1px solid rgba(255,255,255,0.025);">
              <td style="padding:0.85rem 0;font-weight:600;color:#f0f0f0;">{{ \Str::limit($p->title, 30) }}</td>
              <td style="padding:0.85rem 0;color:#FFD700;font-weight:700;">{{ number_format($p->price, 0, ',', ' ') }} FCFA</td>
              <td style="padding:0.85rem 0;">
                <span style="padding:0.2rem 0.6rem;border-radius:10px;font-size:0.72rem;font-weight:700;{{ $p->is_active ? 'background:rgba(34,197,94,0.15);color:#22C55E;' : 'background:rgba(239,68,68,0.1);color:#EF4444;' }}">
                  {{ $p->is_active ? 'En ligne' : 'Masqué' }}
                </span>
              </td>
              <td style="padding:0.85rem 0;text-align:right;">
                <a href="{{ route('prestataire.services.edit', $p->id) }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);color:#ddd;text-decoration:none;"><i class="fas fa-edit"></i></a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p style="text-align:center;color:#555;">Aucun service. <a href="{{ route('prestataire.services.create') }}" style="color:#FFD700;">Créer un service</a></p>
      @endif
    </div>

    <!-- Historique d'intervention -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.75rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <h2 style="font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:1.5rem;">Historique d'Intervention</h2>
      @if($recentOrders->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:1rem;">
          @foreach($recentOrders->take(4) as $cmd)
          @php
            $cols = ['accepted'=>'#3B82F6','confirmed'=>'#3B82F6','in_progress'=>'#A855F7','completed'=>'#22C55E'];
            $col = $cols[$cmd->status] ?? '#666';
            $labels = ['accepted'=>'Acceptée','confirmed'=>'Acceptée','in_progress'=>'En cours','completed'=>'Terminée','cancelled'=>'Annulée'];
          @endphp
          <div style="padding:1rem;background:rgba(255,255,255,0.02);border-radius:12px;display:flex;justify-content:space-between;align-items:center;">
            <div>
              <div style="font-weight:600;font-size:0.9rem;color:#ddd;margin-bottom:0.25rem;">Ref: #{{ str_pad($cmd->id, 4, '0', STR_PAD_LEFT) }}</div>
              <div style="font-size:0.75rem;color:#666;">Client: {{ $cmd->client->name ?? '—' }}</div>
            </div>
            <div style="text-align:right;">
              <div style="color:#FFD700;font-weight:700;font-size:0.95rem;margin-bottom:0.25rem;">{{ number_format($cmd->total_amount ?? 0, 0, ',', ' ') }} FCFA</div>
              <div style="font-size:0.7rem;color:{{ $col }};text-transform:uppercase;font-weight:700;">{{ $labels[$cmd->status] ?? $cmd->status }}</div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p style="text-align:center;color:#555;">L'historique s'affichera ici.</p>
      @endif
    </div>
  </div>

  <!-- COLONNE DROITE -->
  <div style="display:flex;flex-direction:column;gap:1.5rem;">

    <!-- Wallet / Cagnotte -->
    <div style="background:linear-gradient(135deg,rgba(20,20,30,0.98),rgba(12,12,25,0.98));backdrop-filter:blur(28px);border:1px solid rgba(255,215,0,0.25);border-radius:22px;padding:2rem;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
      <i class="fas fa-coins" style="font-size:2.5rem;color:#FFD700;margin-bottom:1rem;display:block;"></i>
      <h3 style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;color:#fff;margin-bottom:0.5rem;">Cagnotte Disponible</h3>
      <div style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:900;color:#FFD700;margin-bottom:1.5rem;">
        {{ number_format($wallet->balance ?? 0, 0, ',', ' ') }} <span style="font-size:1rem;color:#555;">FCFA</span>
      </div>
      <button style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.75rem;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;border:none;border-radius:12px;font-weight:700;cursor:pointer;margin-bottom:0.5rem;">
        <i class="fas fa-hand-holding-usd"></i> Demander Retrait
      </button>
      <p style="font-size:0.7rem;color:#555;">Retrait via Mobile Money en 24h.</p>
    </div>

    <!-- Performance profil -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <h3 style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:1.5rem;">Performance Profil</h3>
      <div style="display:flex;justify-content:space-between;margin-bottom:1rem;font-size:0.85rem;">
        <span style="color:#ccc;">Vues (Aujourd'hui)</span>
        <span style="font-weight:700;color:#FFD700;">45 <i class="fas fa-arrow-up" style="font-size:0.7rem;color:#22C55E;"></i></span>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:1.5rem;font-size:0.85rem;">
        <span style="color:#ccc;">Taux de clic</span>
        <span style="font-weight:700;color:#FFD700;">12%</span>
      </div>
      <button style="display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.6rem;background:rgba(255,215,0,0.06);border:1px solid rgba(255,215,0,0.2);color:#FFD700;border-radius:10px;font-weight:700;cursor:pointer;font-size:0.85rem;">
        <i class="fas fa-bullhorn"></i> Booster mon profil
      </button>
    </div>

    <!-- Retours clients -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <h3 style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:1rem;">Retours Clients</h3>
      @if(($stats['total_reviews'] ?? 0) > 0)
        <div style="padding:1rem;background:rgba(255,255,255,0.02);border-radius:10px;border-left:2px solid #FFD700;">
          <div style="font-size:0.7rem;color:#FFD700;margin-bottom:0.5rem;">★★★★★</div>
          <p style="font-size:0.8rem;color:#aaa;font-style:italic;">"Super travail, très ponctuel et pro. Je recommande !"</p>
          <div style="font-size:0.75rem;color:#555;text-align:right;margin-top:0.5rem;">- Client A.</div>
        </div>
      @else
        <p style="font-size:0.8rem;color:#555;text-align:center;">Pas encore d'avis.</p>
      @endif
    </div>

    <!-- Pro Tips -->
    <div style="background:rgba(10,10,18,0.9);backdrop-filter:blur(28px);border:1px solid rgba(255,255,255,0.07);border-radius:22px;padding:1.5rem;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
      <div style="display:flex;gap:1rem;align-items:center;">
        <div style="width:45px;height:45px;border-radius:10px;background:rgba(59,130,246,0.12);color:#3B82F6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;"><i class="fas fa-graduation-cap"></i></div>
        <div>
          <h4 style="font-size:0.95rem;font-weight:700;color:#f0f0f0;margin:0;">Pro Tips</h4>
          <p style="font-size:0.75rem;color:#777;margin:0.25rem 0 0;">Comment décrocher son premier contrat ?</p>
        </div>
      </div>
      <a href="{{ route('blog') }}" style="display:block;margin-top:1rem;font-size:0.8rem;color:#FFD700;text-transform:uppercase;font-weight:700;text-decoration:none;">Lire le guide <i class="fas fa-arrow-right"></i></a>
    </div>

  </div>
</div><!-- /grid principale -->

@endif
</div>

<style>
@media(max-width:1100px){
  div[style*="grid-template-columns:repeat(4,1fr)"] { grid-template-columns:repeat(2,1fr) !important; }
  div[style*="grid-template-columns:1fr 380px"] { grid-template-columns:1fr !important; }
}
@media(max-width:640px){
  div[style*="grid-template-columns:repeat(2,1fr)"] { grid-template-columns:1fr 1fr !important; }
}
</style>

@endsection
