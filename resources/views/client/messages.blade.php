@extends('layouts.dashboard')

@section('title', 'Messagerie — KOBLAN')

@section('content')
@php
  $myId = Auth::id();
  $conversations = $conversations ?? [];
  $withUid = request('with') ?? 0;
  $first = null;
  
  if ($withUid) {
      foreach($conversations as $c) {
          if((int)$c['id_utilisateur'] === (int)$withUid) { $first = $c; break; }
      }
  }
  if (!$first) { $first = $conversations[0] ?? null; }
  $apiUrl = url('/api/messages'); // URL provisoire pour le JS
@endphp

<canvas id="msg3d" style="position:fixed;top:0;left:0;width:100%;height:100vh;z-index:0;opacity:0.5;pointer-events:none;"></canvas>

<div class="mc-wrap">

  <!-- SIDEBAR -->
  <aside class="mc-sidebar">
    <div class="mc-sb-head">
      <span class="mc-sb-ttl"><i class="fas fa-comment-dots"></i> Messages</span>
      <button class="mc-compose" title="Nouveau"><i class="fas fa-pen"></i></button>
    </div>
    <div class="mc-sb-search">
      <i class="fas fa-search mc-si"></i>
      <input type="text" id="convSearch" placeholder="Rechercher…" oninput="filterConvs(this.value)">
    </div>
    <div class="mc-conv-list" id="convList">
      @if(empty($conversations))
        <div class="mc-empty-side"><i class="fas fa-inbox"></i><p>Aucune conversation</p><small>Commandez un service pour démarrer</small></div>
      @else
        @foreach($conversations as $i => $c)
        @php
          $rawName = ($c['prenom_utilisateur']??'') . ' ' . ($c['nom_utilisateur']??'');
          $ini = strtoupper(mb_substr($c['prenom_utilisateur']??'?',0,1).mb_substr($c['nom_utilisateur']??'',0,1));
          $isActive = ($first && $first['id_utilisateur'] == $c['id_utilisateur']) ? 'active' : '';
        @endphp
        <div class="mc-conv {{ $isActive }}" data-uid="{{ $c['id_utilisateur'] }}" data-name="{{ trim($rawName) }}" data-photo="{{ $c['photo_profil'] ?? '' }}" data-ini="{{ $ini }}" onclick="openConv(this)">
          <div class="mc-av">
            @if(!empty($c['photo_profil']))
              <img src="{{ $c['photo_profil'] }}" alt="">
            @else
              {{ $ini }}
            @endif
            <span class="mc-dot"></span>
          </div>
          <div class="mc-cv-body">
            <div class="mc-cv-row">
              <b class="mc-cv-name">{{ trim($rawName) }}</b>
              <span class="mc-cv-time">{{ !empty($c['last_date']) ? date('H:i', strtotime($c['last_date'])) : '' }}</span>
            </div>
            <div class="mc-cv-prev">{{ Str::limit($c['last_message'] ?? '', 38) }}</div>
          </div>
        </div>
        @endforeach
      @endif
    </div>
  </aside>

  <!-- ZONE CHAT -->
  <main class="mc-chat" id="chatMain">
    @if($first)

    <!-- Header -->
    <div class="mc-chat-head" id="chatHead">
      <a href="{{ url('/profile?id='.$first['id_utilisateur']) }}" class="mc-head-peer" style="text-decoration:none; cursor:pointer;" title="Voir le profil">
        <div class="mc-head-av" id="headAv">
          @if(!empty($first['photo_profil']))
            <img src="{{ $first['photo_profil'] }}" alt="">
          @else
            {{ strtoupper(mb_substr($first['prenom_utilisateur']??'?',0,1).mb_substr($first['nom_utilisateur']??'',0,1)) }}
          @endif
          <span class="mc-dot"></span>
        </div>
        <div>
          <div class="mc-head-name" id="headName">{{ trim(($first['prenom_utilisateur']??'').' '.($first['nom_utilisateur']??'')) }}</div>
          <div class="mc-head-status"><span class="mc-pulse"></span> En ligne</div>
        </div>
      </a>
      <div class="mc-head-acts">
        <button class="mc-hbtn mc-hbtn-warn" title="Signaler"><i class="fas fa-flag"></i></button>
      </div>
    </div>

    <!-- Bulles -->
    <div class="mc-bubbles" id="bubbles">
      <div class="mc-date-sep"><span>Chargement de la conversation…</span></div>
    </div>

    <!-- Typing -->
    <div class="mc-typing" id="typingBar" style="display:none;">
      <div class="mc-dots"><span></span><span></span><span></span></div>
      <span id="typingName">…</span> écrit…
    </div>

    <!-- Input -->
    <div class="mc-input-bar">
      <button class="mc-ib-btn" title="Fichier"><i class="fas fa-paperclip"></i></button>
      <div class="mc-ib-wrap">
        <textarea id="msgTxt" class="mc-textarea" placeholder="Écrivez votre message…" rows="1" onkeydown="handleKey(event)" oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,130)+'px'"></textarea>
        <button class="mc-emoji-btn" onclick="toggleEmoji(event)"><i class="fas fa-smile"></i></button>
      </div>
      <button class="mc-send-btn" id="sendBtn" onclick="sendMsg()"><i class="fas fa-paper-plane"></i></button>
    </div>

    <!-- Emoji panel -->
    <div class="mc-emoji-pnl" id="emojiPnl" style="display:none;">
      @foreach(['😊','😂','🔥','💯','👍','🙏','❤️','✅','❌','😎','🤝','💪','🎉','📞','🏠','⭐','💰','🔒','📷','✌️'] as $em)
      <span onclick="addEmoji('{{ $em }}')">{{ $em }}</span>
      @endforeach
    </div>

    @else
    <div class="mc-no-chat">
      <div class="mc-nc-icon">💬</div>
      <h2>Démarrez une conversation</h2>
      <p>Commandez un service pour échanger avec un prestataire. Les messages apparaîtront ici.</p>
      <a href="{{ route('services.index') }}" class="mc-nc-btn"><i class="fas fa-search"></i> Explorer</a>
    </div>
    @endif
  </main>

</div><!-- /mc-wrap -->

<style>
/* Override du layout dynamique pour la messagerie */
.dash-content {
  padding: 0 !important;
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
}

/* ==============================
   MESSAGES PAGE — PREMIUM
   ============================== */
.mc-wrap {
  position:relative;z-index:2;
  display:flex;
  width: 100%;flex: 1; /* Remplit l'espace de dash-content */
  box-sizing: border-box;
  background:rgba(6,6,12,0.97);
  /* Pas de bordures car ça touche les bords de l'écran */
}

/* SIDEBAR */
.mc-sidebar {
  width:300px;flex-shrink:0;
  background:rgba(9,9,16,0.99);
  border-right:1px solid rgba(255,255,255,0.06);
  display:flex;flex-direction:column;
}
.mc-sb-head {
  display:flex;align-items:center;justify-content:space-between;
  padding:1.25rem 1.1rem 0.65rem;
}
.mc-sb-ttl {color:#fff;font-weight:800;font-size:1.1rem;display:flex;align-items:center;gap:0.5rem;}
.mc-sb-ttl i{color:#FFD700;}
.mc-compose {
  width:32px;height:32px;border-radius:9px;background:rgba(255,215,0,0.1);
  border:1px solid rgba(255,215,0,0.2);color:#FFD700;cursor:pointer;
  display:flex;align-items:center;justify-content:center;font-size:0.78rem;transition:0.2s;
}
.mc-compose:hover{transform:scale(1.12);background:rgba(255,215,0,0.18);}
.mc-sb-search {position:relative;padding:0.4rem 0.9rem 0.7rem;}
.mc-sb-search input {
  width:100%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);
  border-radius:11px;padding:0.55rem 0.55rem 0.55rem 2rem;color:#f0f0f0;font-size:0.82rem;
  outline:none;box-sizing:border-box;
}
.mc-sb-search input:focus{border-color:rgba(255,215,0,0.3);}
.mc-si{position:absolute;left:1.65rem;top:50%;transform:translateY(-50%);color:#555;font-size:0.75rem;}
.mc-conv-list{flex:1;overflow-y:auto;padding:0.3rem;}
.mc-conv-list::-webkit-scrollbar{width:3px;}
.mc-conv-list::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.07);border-radius:2px;}
.mc-empty-side{text-align:center;padding:3rem 1rem;color:#555;}
.mc-empty-side i{font-size:2.5rem;opacity:0.3;display:block;margin-bottom:0.75rem;}
.mc-empty-side p{font-weight:600;margin:0 0 0.25rem;color:#666;font-size:0.9rem;}
.mc-empty-side small{font-size:0.75rem;}
.mc-conv {
  display:flex;align-items:center;gap:0.8rem;padding:0.8rem;
  border-radius:13px;cursor:pointer;transition:all 0.18s;
  border:1px solid transparent;margin-bottom:0.2rem;
}
.mc-conv:hover{background:rgba(255,255,255,0.04);}
.mc-conv.active{background:rgba(255,215,0,0.07);border-color:rgba(255,215,0,0.14);}
.mc-av{
  width:46px;height:46px;border-radius:50%;position:relative;flex-shrink:0;
  background:linear-gradient(135deg,#FFD700,#F77F00);
  display:flex;align-items:center;justify-content:center;
  font-weight:900;font-size:0.9rem;color:#000;overflow:hidden;
}
.mc-av img{width:100%;height:100%;object-fit:cover;}
.mc-dot {
  position:absolute;bottom:1px;right:1px;
  width:10px;height:10px;border-radius:50%;
  background:#22C55E;border:2px solid #09090f;
}
.mc-cv-body{flex:1;min-width:0;}
.mc-cv-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:0.15rem;}
.mc-cv-name{font-size:0.85rem;font-weight:700;color:#f0f0f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;}
.mc-cv-time{font-size:0.7rem;color:#555;white-space:nowrap;}
.mc-cv-prev{font-size:0.75rem;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* CHAT MAIN */
.mc-chat {
  flex:1;display:flex;flex-direction:column;
  background:rgba(5,5,10,0.98);position:relative;
  min-width:0; min-height:0; overflow:hidden;
}
.mc-chat-head {
  display:flex;align-items:center;justify-content:space-between;
  padding:0.9rem 1.4rem;border-bottom:1px solid rgba(255,255,255,0.06);
  background:rgba(9,9,16,0.97);backdrop-filter:blur(20px);flex-shrink:0;
}
.mc-head-peer{display:flex;align-items:center;gap:1rem;}
.mc-head-av{
  width:44px;height:44px;border-radius:50%;position:relative;
  background:linear-gradient(135deg,#FFD700,#F77F00);
  display:flex;align-items:center;justify-content:center;
  font-weight:900;font-size:0.9rem;color:#000;overflow:hidden;
}
.mc-head-av img{width:100%;height:100%;object-fit:cover;}
.mc-head-name{font-weight:800;font-size:1rem;color:#fff;}
.mc-head-status{font-size:0.7rem;color:#22C55E;display:flex;align-items:center;gap:0.3rem;margin-top:0.15rem;}
.mc-pulse{width:6px;height:6px;border-radius:50%;background:#22C55E;animation:pls 1.5s infinite;}
@keyframes pls{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.5;transform:scale(1.5)}}
.mc-head-acts{display:flex;gap:0.5rem;}
.mc-hbtn{
  width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.04);
  border:1px solid rgba(255,255,255,0.07);color:#999;cursor:pointer;
  display:flex;align-items:center;justify-content:center;font-size:0.85rem;transition:0.2s;
}
.mc-hbtn:hover{background:rgba(255,255,255,0.08);color:#fff;}
.mc-hbtn-warn:hover{background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.25);color:#EF4444;}

/* BULLES */
.mc-bubbles{flex:1;min-height:0;overflow-y:auto;padding:1.5rem;display:flex;flex-direction:column;gap:0.75rem;scroll-behavior:smooth;}
.mc-bubbles::-webkit-scrollbar{width:4px;}
.mc-bubbles::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.05);border-radius:2px;}
.mc-date-sep{text-align:center;margin:0.5rem 0;}
.mc-date-sep span{font-size:0.75rem;font-weight:600;color:#555;background:rgba(255,255,255,0.03);padding:0.3rem 1rem;border-radius:99px;}
.mc-bw{display:flex;flex-direction:column;max-width:72%;}
.mc-bw.me{align-self:flex-end;align-items:flex-end;}
.mc-bw.them{align-self:flex-start;align-items:flex-start;}
.mc-b{
  padding:0.8rem 1.2rem;border-radius:20px;font-size:0.9rem;line-height:1.5;
  animation:bIn 0.28s cubic-bezier(.175,.885,.32,1.275) both;word-break:break-word;
}
@keyframes bIn{from{opacity:0;transform:scale(0.88) translateY(6px)}to{opacity:1;transform:none}}
.mc-b.me{background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;border-bottom-right-radius:4px;box-shadow:0 4px 16px rgba(255,215,0,0.15);font-weight:500;}
.mc-b.them{background:rgba(255,255,255,0.08);color:#f0f0f0;border-bottom-left-radius:4px;border:1px solid rgba(255,255,255,0.06);}
.mc-bt{font-size:0.7rem;color:#555;margin-top:0.3rem;display:flex;align-items:center;gap:0.3rem;}
.mc-bt.me{color:#888;}

/* QR / TYPING */
.mc-typing{padding:0 1.5rem 0.5rem;font-size:0.8rem;color:#666;display:flex;align-items:center;gap:0.5rem;flex-shrink:0;}
.mc-dots{display:flex;gap:3px;}
.mc-dots span{width:5px;height:5px;border-radius:50%;background:#555;animation:tb 1.4s infinite;}
.mc-dots span:nth-child(2){animation-delay:.2s;}
.mc-dots span:nth-child(3){animation-delay:.4s;}
@keyframes tb{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-5px)}}

/* INPUT */
.mc-input-bar{display:flex;align-items:flex-end;gap:0.75rem;padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,0.06);background:rgba(9,9,16,0.97);flex-shrink:0;}
.mc-ib-btn{width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);color:#666;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:0.9rem;transition:0.2s;flex-shrink:0;}
.mc-ib-btn:hover{color:#FFD700;border-color:rgba(255,215,0,0.25);}
.mc-ib-wrap{flex:1;position:relative;}
.mc-textarea{
  width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.15);
  border-radius:16px;padding:1rem 2.8rem 1rem 1.25rem;color:#f0f0f0;
  font-size:0.95rem;font-family:inherit;outline:none;resize:none;
  min-height:55px;max-height:150px;overflow-y:auto;transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box;
}
.mc-textarea:focus{background:rgba(255,255,255,0.08);border-color:rgba(255,215,0,0.5);box-shadow:0 0 0 4px rgba(255,215,0,0.1);}
.mc-emoji-btn{position:absolute;right:0.65rem;bottom:0.75rem;background:none;border:none;color:#555;cursor:pointer;font-size:1.1rem;transition:0.2s;}
.mc-emoji-btn:hover{color:#FFD700;}
.mc-send-btn{
  width:46px;height:46px;border-radius:14px;flex-shrink:0;
  background:linear-gradient(135deg,#FFD700,#F77F00);border:none;color:#000;
  cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;
  transition:transform 0.2s,box-shadow 0.2s;
}
.mc-send-btn:hover{transform:scale(1.08);box-shadow:0 6px 20px rgba(255,215,0,0.4);}
.mc-send-btn:active{transform:scale(0.92);}
.mc-emoji-pnl{
  position:absolute;bottom:80px;right:110px;z-index:20;
  background:rgba(12,12,20,0.99);border:1px solid rgba(255,255,255,0.1);
  border-radius:16px;padding:0.75rem;
  display:grid;grid-template-columns:repeat(5,1fr);gap:0.3rem;
  box-shadow:0 20px 50px rgba(0,0,0,0.5);
}
.mc-emoji-pnl span{font-size:1.3rem;cursor:pointer;padding:0.3rem;text-align:center;border-radius:8px;transition:0.15s;}
.mc-emoji-pnl span:hover{background:rgba(255,255,255,0.07);}

/* NO CHAT */
.mc-no-chat{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem;gap:1rem;}
.mc-nc-icon{font-size:4.5rem;opacity:0.2;}
.mc-no-chat h2{color:#fff;font-size:1.5rem;font-family:'Syne',sans-serif;font-weight:800;margin:0;}
.mc-no-chat p{color:#666;margin:0;max-width:340px;font-size:0.95rem;line-height:1.5;}
.mc-nc-btn{display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.75rem;background:linear-gradient(135deg,#FFD700,#F77F00);color:#000;border-radius:12px;text-decoration:none;font-weight:700;transition:0.2s;}
.mc-nc-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(255,215,0,0.35);}

@media(max-width:700px){.mc-sidebar{width:70px;} .mc-cv-body,.mc-sb-search,.mc-sb-ttl span{display:none;}}
</style>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
<script>
/* ==============================================
   THREE.JS — VISUEL SIMPLE ET JOLI (FLOATING PARTICLES)
   ============================================== */
(function(){
  const canvas = document.getElementById('msg3d');
  if(!canvas||typeof THREE==='undefined') return;

  const scene = new THREE.Scene();
  const cam = new THREE.PerspectiveCamera(60, innerWidth/innerHeight, 0.1, 1000);
  cam.position.z = 100;

  const renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:true});
  renderer.setSize(innerWidth, innerHeight);
  renderer.setPixelRatio(Math.min(devicePixelRatio,2));
  renderer.setClearColor(0x000000, 0);

  // Particles
  const pCount = 300;
  const pGeo = new THREE.BufferGeometry();
  const pPos = new Float32Array(pCount * 3);
  const pVels = [];
  
  for(let i=0; i<pCount; i++){
    pPos[i*3] = (Math.random() - 0.5) * 200;
    pPos[i*3+1] = (Math.random() - 0.5) * 200;
    pPos[i*3+2] = (Math.random() - 0.5) * 200;
    pVels.push({
       x: (Math.random() - 0.5) * 0.1,
       y: (Math.random() - 0.5) * 0.1,
       z: (Math.random() - 0.5) * 0.1
    });
  }
  pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
  
  // Create circular soft texture
  const c = document.createElement('canvas'); c.width = 16; c.height = 16;
  const ctx = c.getContext('2d');
  const grd = ctx.createRadialGradient(8,8,0,8,8,8);
  grd.addColorStop(0, 'rgba(255, 215, 0, 1)'); 
  grd.addColorStop(1, 'rgba(255, 215, 0, 0)');
  ctx.fillStyle = grd; ctx.fillRect(0,0,16,16);
  const tex = new THREE.CanvasTexture(c);

  const pMat = new THREE.PointsMaterial({
    color: 0xFFD700, size: 2.5, map: tex, transparent: true, opacity: 0.6, blending: THREE.AdditiveBlending, depthWrite: false
  });
  
  const particles = new THREE.Points(pGeo, pMat);
  scene.add(particles);

  let mx = 0, my = 0;
  document.addEventListener('mousemove', e => {
    mx = (e.clientX / innerWidth) * 2 - 1;
    my = -(e.clientY / innerHeight) * 2 + 1;
  });

  function animate(){
    requestAnimationFrame(animate);
    
    // Move particles
    const positions = particles.geometry.attributes.position.array;
    for(let i=0; i<pCount; i++) {
        positions[i*3] += pVels[i].x;
        positions[i*3+1] += pVels[i].y;
        positions[i*3+2] += pVels[i].z;
        
        // wrap around
        if (positions[i*3] > 100) positions[i*3] = -100;
        if (positions[i*3] < -100) positions[i*3] = 100;
        if (positions[i*3+1] > 100) positions[i*3+1] = -100;
        if (positions[i*3+1] < -100) positions[i*3+1] = 100;
        if (positions[i*3+2] > 100) positions[i*3+2] = -100;
        if (positions[i*3+2] < -100) positions[i*3+2] = 100;
    }
    particles.geometry.attributes.position.needsUpdate = true;
    
    // Slight cam move
    cam.position.x += (mx * 20 - cam.position.x) * 0.05;
    cam.position.y += (my * 20 - cam.position.y) * 0.05;
    cam.lookAt(scene.position);

    renderer.render(scene, cam);
  }
  animate();

  window.addEventListener('resize', ()=>{
    cam.aspect = innerWidth/innerHeight;
    cam.updateProjectionMatrix();
    renderer.setSize(innerWidth, innerHeight);
  });
})();

/* ==============================================
   CHAT LOGIC — CONNECTED TO REST API
   ============================================== */
const MY_ID  = {{ $myId }};
let activeUid = {{ $first ? $first['id_utilisateur'] : 'null' }};

function loadHistory(uid) {
  const box = document.getElementById('bubbles');
  box.innerHTML = '<div class="mc-date-sep"><span>Chargement de la conversation…</span></div>';
  fetch(`/api/messages/history/${uid}`)
    .then(r => r.json())
    .then(msgs => {
      box.innerHTML = '';
      if(msgs.length === 0) {
        box.innerHTML = '<div class="mc-date-sep"><span>Aucun message. Démarrez la discussion !</span></div>';
      } else {
        msgs.forEach(m => {
          box.appendChild(makeBubble(m.content, m.is_mine, m.created_at));
        });
      }
      box.scrollTop = box.scrollHeight;
    })
    .catch(err => {
      box.innerHTML = '<div class="mc-date-sep" style="color:var(--red-400);"><span>Erreur de chargement.</span></div>';
    });
}

// Charger l'historique initial
if(activeUid) {
  loadHistory(activeUid);
}

function openConv(el){
  const clickUid = parseInt(el.dataset.uid);
  if (activeUid === clickUid) return;

  document.querySelectorAll('.mc-conv').forEach(i=>i.classList.remove('active'));
  el.classList.add('active');
  activeUid = clickUid;
  document.getElementById('headName').textContent = el.dataset.name;
  
  const headAv = document.getElementById('headAv');
  if (el.dataset.photo && el.dataset.photo !== '') {
      headAv.innerHTML = `<img src="${el.dataset.photo}" alt=""> <span class="mc-dot"></span>`;
  } else {
      headAv.innerHTML = el.dataset.ini + ' <span class="mc-dot"></span>';
  }
  
  const peerLink = document.querySelector('.mc-head-peer');
  if(peerLink) peerLink.href = '{{ url("/profile?id=") }}' + activeUid;
  
  window.history.pushState({}, '', '?with=' + activeUid);
  
  // Charger depuis la base de données
  loadHistory(activeUid);
}

function makeBubble(txt, mine, time=null, is_temp=false) {
  const w=document.createElement('div'); w.className=`mc-bw ${mine?'me':'them'}`;
  const b=document.createElement('div'); b.className=`mc-b ${mine?'me':'them'}`;
  b.textContent=txt; w.appendChild(b);
  if(!time) {
      const d=new Date();
      time = d.toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'});
  }
  const t=document.createElement('div'); t.className=`mc-bt ${mine?'me':''}`;
  if(mine) {
      t.innerHTML=`${time} ${is_temp ? '<i class="fas fa-clock" style="font-size:.65rem; opacity:0.5;"></i>' : '<i class="fas fa-check-double" style="font-size:.65rem;"></i>'}`;
  } else {
      t.innerHTML=`${time}`;
  }
  w.appendChild(t); return w;
}

function sendMsg(){
  const txt=document.getElementById('msgTxt').value.trim();
  if(!txt||!activeUid) return;
  const box=document.getElementById('bubbles');
  
  // Affichage immédiat temporaire
  const tmpBubble = makeBubble(txt, true, '...', true);
  box.appendChild(tmpBubble);
  box.scrollTop=box.scrollHeight;
  
  document.getElementById('msgTxt').value='';
  document.getElementById('msgTxt').style.height='auto';
  
  // Envoi à la base de données
  fetch('/api/messages/send', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ receiver_id: activeUid, content: txt })
  })
  .then(r => r.json())
  .then(res => {
     if(res.status === 'success') {
       // Confirmer l'envoi et mettre à jour l'heure
       tmpBubble.querySelector('.mc-bt').innerHTML = `${res.message.created_at} <i class="fas fa-check-double" style="font-size:.65rem; color:#4ade80;"></i>`;
     } else {
       tmpBubble.querySelector('.mc-bt').innerHTML = `<i class="fas fa-exclamation-circle" style="color:#ef4444;"></i> Erreur`;
     }
  })
  .catch(err => {
      tmpBubble.querySelector('.mc-bt').innerHTML = `<i class="fas fa-wifi" style="color:#ef4444;"></i> Hors ligne`;
  });

  const btn=document.getElementById('sendBtn'); btn.style.transform='scale(0.85)';
  setTimeout(()=>btn.style.transform='',200);
}

function handleKey(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMsg();}}
function toggleEmoji(e){e.stopPropagation();const p=document.getElementById('emojiPnl');p.style.display=p.style.display==='none'?'grid':'none';}
function addEmoji(em){document.getElementById('msgTxt').value+=em;document.getElementById('emojiPnl').style.display='none';}
function filterConvs(q){document.querySelectorAll('.mc-conv').forEach(c=>{c.style.display=(c.dataset.name||'').toLowerCase().includes(q.toLowerCase())?'flex':'none';});}

document.addEventListener('click',e=>{
  const p=document.getElementById('emojiPnl');
  if(p&&!p.contains(e.target)&&!e.target.closest('.mc-emoji-btn')) p.style.display='none';
});

// La hauteur est gérée en 100% via CSS Flexbox (.dash-content et .mc-wrap)
</script>
@endsection
