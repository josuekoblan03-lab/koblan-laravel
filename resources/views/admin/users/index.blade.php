@extends('layouts.dashboard')

@section('content')
<div class="db-wrap">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
    <div>
        <h1 style="font-family:var(--font-display);font-size:1.8rem;font-weight:900;margin-bottom:0.25rem;">
            <i class="fas fa-users" style="color:#3B82F6;"></i> Gestion des <span style="color:#3B82F6;">Utilisateurs</span>
        </h1>
        <p style="color:var(--gray-500);font-size:0.9rem;">{{ $users->total() }} utilisateur(s) trouvé(s)</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

@if(session('success'))
<div style="background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.5);color:#10b981;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:rgba(239,68,68,0.2);border:1px solid rgba(239,68,68,0.5);color:#ef4444;padding:1rem;border-radius:10px;margin-bottom:1.5rem;">{{ session('error') }}</div>
@endif

{{-- BARRE RECHERCHE --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:12px;padding:1rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
    <i class="fas fa-search" style="color:var(--gray-500);"></i>
    <input type="text" id="userSearch" placeholder="Rechercher par nom, email, rôle..." oninput="searchUsers()"
           style="background:none;border:none;outline:none;color:#fff;font-size:0.9rem;flex:1;min-width:0;">
    <span id="userCount" style="font-size:0.8rem;color:var(--gray-500);white-space:nowrap;">{{ $users->total() }} résultats</span>
</div>

{{-- TABLEAU --}}
<div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:16px;overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;min-width:900px;" id="usersTable">
        <thead>
            <tr style="background:var(--dark-200);border-bottom:1px solid rgba(255,255,255,0.06);">
                <th style="padding:1rem 1.5rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;letter-spacing:0.05em;">Utilisateur</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Email</th>
                <th style="padding:1rem;text-align:left;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Localisation</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Rôle</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Statut</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Inscription</th>
                <th style="padding:1rem;text-align:center;color:var(--gray-500);font-size:0.72rem;text-transform:uppercase;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
        <tr class="user-row"
            data-name="{{ strtolower($u->name) }}"
            data-email="{{ strtolower($u->email) }}"
            data-role="{{ $u->role }}"
            style="border-bottom:1px solid rgba(255,255,255,0.03);transition:0.15s;"
            onmouseover="this.style.background='rgba(255,255,255,0.02)'"
            onmouseout="this.style.background='transparent'">

            <td style="padding:0.9rem 1.5rem;">
                <div style="display:flex;align-items:center;gap:0.8rem;">
                    <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;color:#000;font-size:0.72rem;flex-shrink:0;
                        background:{{ $u->role === 'prestataire' ? 'linear-gradient(135deg,#F59E0B,#D97706)' : 'linear-gradient(135deg,#3B82F6,#1D4ED8)' }};">
                        {{ strtoupper(mb_substr($u->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;color:#fff;">{{ $u->name }}</div>
                        <div style="font-size:0.7rem;color:var(--gray-600);">#{{ $u->id }}</div>
                    </div>
                </div>
            </td>
            <td style="padding:0.9rem 1rem;color:var(--gray-400);font-size:0.82rem;">{{ $u->email }}</td>
            <td style="padding:0.9rem 1rem;color:var(--gray-500);font-size:0.8rem;">
                <i class="fas fa-map-marker-alt" style="color:#3B82F6;font-size:0.7rem;"></i>
                {{ $u->city?->name ?? '—' }}
            </td>
            <td style="padding:0.9rem 1rem;text-align:center;">
                @if($u->role === 'admin')
                    <span style="background:#22C55E22;color:#22C55E;padding:0.2rem 0.6rem;border-radius:99px;font-size:0.72rem;font-weight:700;">Admin</span>
                @elseif($u->role === 'prestataire')
                    <span style="background:#F59E0B22;color:#F59E0B;padding:0.2rem 0.6rem;border-radius:99px;font-size:0.72rem;font-weight:700;">Prestataire</span>
                @else
                    <span style="background:#3B82F622;color:#3B82F6;padding:0.2rem 0.6rem;border-radius:99px;font-size:0.72rem;font-weight:700;">Client</span>
                @endif
            </td>
            <td style="padding:0.9rem 1rem;text-align:center;">
                @if($u->is_active)
                    <span style="background:#22C55E22;color:#22C55E;padding:0.2rem 0.6rem;border-radius:99px;font-size:0.72rem;font-weight:700;">
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:#22C55E;margin-right:4px;vertical-align:middle;"></span>Actif
                    </span>
                @else
                    <span style="background:#EF444422;color:#EF4444;padding:0.2rem 0.6rem;border-radius:99px;font-size:0.72rem;font-weight:700;">Suspendu</span>
                @endif
            </td>
            <td style="padding:0.9rem 1rem;text-align:center;color:var(--gray-500);font-size:0.78rem;">
                {{ $u->created_at->format('d/m/Y') }}
            </td>
            <td style="padding:0.9rem 1rem;text-align:center;">
                <div style="display:flex;justify-content:center;gap:0.4rem;">
                    @if($u->is_active)
                    <button type="button"
                        onclick="openSuspendModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')"
                        style="background:#F59E0B22;color:#F59E0B;border:1px solid #F59E0B44;border-radius:8px;padding:0.3rem 0.6rem;font-size:0.75rem;cursor:pointer;"
                        title="Suspendre">
                        <i class="fas fa-ban"></i>
                    </button>
                    @else
                    <form method="POST" action="{{ route('admin.users.update', $u->id) }}" style="margin:0;" class="ajax-form">
                        @csrf @method('PUT')
                        <input type="hidden" name="is_active" value="1">
                        <button type="submit" style="background:#22C55E22;color:#22C55E;border:1px solid #22C55E44;border-radius:8px;padding:0.3rem 0.6rem;font-size:0.75rem;cursor:pointer;" onclick="return confirm('Réactiver ce compte ?')" title="Activer">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                    @endif
                    <button type="button"
                        onclick="openDeleteModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->email) }}')"
                        style="background:#EF444422;color:#EF4444;border:1px solid #EF444444;border-radius:8px;padding:0.3rem 0.7rem;font-size:0.75rem;cursor:pointer;display:inline-flex;align-items:center;gap:0.3rem;font-weight:600;"
                        title="Supprimer définitivement">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($users->isEmpty())
    <div style="text-align:center;padding:4rem;color:var(--gray-500);">Aucun utilisateur trouvé.</div>
    @endif
</div>

@if($users->hasPages())
<div style="margin-top:2rem;">{{ $users->links('pagination::bootstrap-4') }}</div>
@endif

</div>

{{-- MODALE SUPPRESSION --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);justify-content:center;align-items:center;">
    <div style="background:var(--dark-300);border:1px solid #EF444455;border-radius:16px;padding:1.5rem;max-width:400px;width:92%;position:relative;animation:slideUp 0.25s ease;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <div style="width:38px;height:38px;border-radius:50%;background:#EF444422;border:1.5px solid #EF444455;display:flex;align-items:center;justify-content:center;color:#EF4444;font-size:1rem;flex-shrink:0;">
                <i class="fas fa-trash"></i>
            </div>
            <div>
                <h2 style="font-family:var(--font-display);font-size:1.1rem;font-weight:900;color:#fff;margin:0;">Supprimer l'utilisateur ?</h2>
                <p style="color:#EF4444;font-size:0.75rem;margin:0;">Action irréversible</p>
            </div>
            <button onclick="closeDeleteModal()" style="margin-left:auto;background:none;border:none;color:var(--gray-500);cursor:pointer;font-size:1rem;"><i class="fas fa-times"></i></button>
        </div>
        <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:10px;padding:0.75rem 1rem;margin-bottom:0.85rem;display:flex;align-items:center;gap:0.75rem;">
            <div id="modalAvatar" style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#EF4444,#DC2626);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:0.75rem;flex-shrink:0;"></div>
            <div>
                <div id="modalName" style="font-weight:700;color:#fff;font-size:0.9rem;"></div>
                <div id="modalEmail" style="font-size:0.75rem;color:var(--gray-500);"></div>
            </div>
        </div>
        <div style="background:#EF444411;border:1px solid #EF444433;border-radius:8px;padding:0.6rem 0.85rem;margin-bottom:1rem;font-size:0.78rem;color:#F87171;display:flex;align-items:flex-start;gap:0.5rem;">
            <i class="fas fa-exclamation-triangle" style="margin-top:0.1rem;flex-shrink:0;"></i>
            <span>Compte, commandes, prestations et toutes les données liées seront supprimés définitivement.</span>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <button onclick="closeDeleteModal()" style="flex:1;padding:0.65rem;border-radius:10px;background:var(--dark-100);border:1px solid var(--glass-border);color:var(--gray-300);font-weight:600;cursor:pointer;font-size:0.85rem;">Annuler</button>
            <form method="POST" style="flex:1;margin:0;" id="deleteForm" class="ajax-form">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%;padding:0.65rem;border-radius:10px;background:#EF4444;border:none;color:#fff;font-weight:700;cursor:pointer;font-size:0.85rem;display:flex;align-items:center;justify-content:center;gap:0.4rem;">
                    <i class="fas fa-trash" style="font-size:0.8rem;"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>

{{-- MODALE SUSPENSION --}}
<div id="suspendModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);justify-content:center;align-items:center;">
    <div style="background:var(--dark-300);border:1px solid #F59E0B55;border-radius:16px;padding:1.5rem;max-width:400px;width:92%;position:relative;animation:slideUp 0.25s ease;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <div style="width:38px;height:38px;border-radius:50%;background:#F59E0B22;border:1.5px solid #F59E0B55;display:flex;align-items:center;justify-content:center;color:#F59E0B;font-size:1rem;flex-shrink:0;">
                <i class="fas fa-ban"></i>
            </div>
            <div>
                <h2 style="font-family:var(--font-display);font-size:1.1rem;font-weight:900;color:#fff;margin:0;">Suspendre ce compte ?</h2>
                <p style="color:var(--gray-400);font-size:0.75rem;margin:0;">Action réversible</p>
            </div>
            <button onclick="closeSuspendModal()" style="margin-left:auto;background:none;border:none;color:var(--gray-500);cursor:pointer;font-size:1rem;"><i class="fas fa-times"></i></button>
        </div>
        <div style="background:var(--dark-100);border:1px solid var(--glass-border);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1rem;display:flex;align-items:center;gap:0.75rem;">
            <div id="suspendModalAvatar" style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#F59E0B,#D97706);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:0.75rem;flex-shrink:0;"></div>
            <div>
                <div id="suspendModalName" style="font-weight:700;color:#fff;font-size:0.9rem;"></div>
                <div id="suspendModalEmail" style="font-size:0.75rem;color:var(--gray-500);"></div>
            </div>
        </div>
        <div style="display:flex;gap:0.75rem;">
            <button onclick="closeSuspendModal()" style="flex:1;padding:0.65rem;border-radius:10px;background:var(--dark-100);border:1px solid var(--glass-border);color:var(--gray-300);font-weight:600;cursor:pointer;font-size:0.85rem;">Annuler</button>
            <form method="POST" style="flex:1;margin:0;" id="suspendForm" class="ajax-form">
                @csrf @method('PUT')
                <input type="hidden" name="is_active" value="0">
                <button type="submit" style="width:100%;padding:0.65rem;border-radius:10px;background:#F59E0B;border:none;color:#fff;font-weight:700;cursor:pointer;font-size:0.85rem;display:flex;align-items:center;justify-content:center;gap:0.4rem;">
                    <i class="fas fa-ban" style="font-size:0.8rem;"></i> Suspendre
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes slideUp {
    from { opacity:0; transform: translateY(20px) scale(0.97); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
</style>
@endsection

@section('scripts')
<script>
function openDeleteModal(userId, name, email) {
    document.getElementById('deleteForm').action = '{{ url("admin/users") }}/' + userId;
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('modalAvatar').textContent = (name.split(' ')[0]?.[0] ?? '') + (name.split(' ')[1]?.[0] ?? '');
    document.getElementById('deleteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = '';
}
function openSuspendModal(userId, name, email) {
    document.getElementById('suspendForm').action = '{{ url("admin/users") }}/' + userId;
    document.getElementById('suspendModalName').textContent = name;
    document.getElementById('suspendModalEmail').textContent = email;
    document.getElementById('suspendModalAvatar').textContent = (name.split(' ')[0]?.[0] ?? '') + (name.split(' ')[1]?.[0] ?? '');
    document.getElementById('suspendModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeSuspendModal() {
    document.getElementById('suspendModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('deleteModal').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });
document.getElementById('suspendModal').addEventListener('click', function(e) { if (e.target === this) closeSuspendModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDeleteModal(); closeSuspendModal(); } });

function searchUsers() {
    const q = document.getElementById('userSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    let count = 0;
    rows.forEach(row => {
        const match = row.dataset.name.includes(q) || row.dataset.email.includes(q) || row.dataset.role.includes(q);
        row.style.display = match ? '' : 'none';
        if (match) count++;
    });
    document.getElementById('userCount').textContent = count + ' résultat(s)';
}
</script>
@endsection
