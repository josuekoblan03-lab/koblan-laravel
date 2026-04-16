
// ================================================================
// KOBLAN — Hero Character 3D
// Essaye de charger le modèle GLTF Soldier (humain animé réel)
// Fallback : personnage procédural détaillé avec casquette & tenues
// ================================================================

(function waitAndInit() {
  // Attendre que THREE.js soit initialisé (il est chargé à la fin du body)
  if (typeof THREE === 'undefined') {
    return setTimeout(waitAndInit, 100);
  }

  // Si on a THREE mais pas GLTFLoader (chargé séparément)
  if (typeof THREE.GLTFLoader === 'undefined') {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/gh/mrdoob/three.js@r134/examples/js/loaders/GLTFLoader.js';
    s.onload = () => initHeroCharacter();
    document.body.appendChild(s);
  } else {
    initHeroCharacter();
  }
})();

function initHeroCharacter() {
  const canvas = document.getElementById('character-canvas');
  if (!canvas) return;

  const W = Math.round(window.innerWidth * 0.5);
  const H = window.innerHeight;
  canvas.style.cssText = 'position:absolute;top:0;right:0;width:'+W+'px;height:'+H+'px;z-index:5;pointer-events:none;display:block;';

  const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(W, H, false);
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  try { renderer.outputEncoding = THREE.sRGBEncoding; } catch(e){}
  try { renderer.toneMapping = THREE.ACESFilmicToneMapping; renderer.toneMappingExposure = 1.3; } catch(e){}

  const scene  = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(40, W / H, 0.1, 100);
  camera.position.set(0, 1.5, 3.8);
  camera.lookAt(0, 1.0, 0);

  // ── Lumières cinématiques ──
  scene.add(new THREE.AmbientLight(0xfff0cc, 0.7));
  const key = new THREE.DirectionalLight(0xfff5dd, 3.5);
  key.position.set(3, 6, 4); key.castShadow = true;
  key.shadow.mapSize.set(1024, 1024); scene.add(key);
  const fill = new THREE.DirectionalLight(0xaaddff, 1.2);
  fill.position.set(-4, 2, 2); scene.add(fill);
  const gold = new THREE.PointLight(0xFFD700, 5, 14);
  gold.position.set(0, 4, 1.5); scene.add(gold);
  const rim  = new THREE.PointLight(0xF77F00, 3, 10);
  rim.position.set(-2, 2, -1.5); scene.add(rim);
  const flr  = new THREE.PointLight(0xFFD700, 2, 6);
  flr.position.set(0, -0.2, 1); scene.add(flr);

  // ── Sol (ombres) ──
  const gnd = new THREE.Mesh(
    new THREE.PlaneGeometry(8, 8),
    new THREE.ShadowMaterial({ opacity: 0.22 })
  );
  gnd.rotation.x = -Math.PI / 2; gnd.receiveShadow = true; scene.add(gnd);

  // ── Socle holographique ──
  const matGold = new THREE.MeshStandardMaterial({ color:0xFFD700, emissive:0xB8860B, emissiveIntensity:.28, metalness:.95, roughness:.05 });
  const base = new THREE.Mesh(new THREE.CylinderGeometry(.9,.95,.045,64), matGold);
  base.receiveShadow = true; scene.add(base);

  const mkRing = (r, col, op) => {
    const m = new THREE.Mesh(
      new THREE.TorusGeometry(r, .013, 8, 100),
      new THREE.MeshBasicMaterial({ color:col, transparent:true, opacity:op })
    );
    m.rotation.x = Math.PI / 2; scene.add(m); return m;
  };
  const ring1 = mkRing(1.05, 0xFFD700, .55);
  const ring2 = mkRing(1.30, 0xF77F00, .38);
  const ring3 = mkRing(1.60, 0x4488FF, .22);

  const orbG = new THREE.SphereGeometry(.045, 10, 10);
  const orbs = Array.from({length:8}, (_,i) => {
    const mat = new THREE.MeshStandardMaterial({
      color: i%2 ? 0xF77F00 : 0xFFD700,
      emissive: i%2 ? 0xF77F00 : 0xFFD700,
      emissiveIntensity:.5, metalness:.8, roughness:.1
    });
    const o = new THREE.Mesh(orbG, mat);
    o.userData = { angle:(i/8)*Math.PI*2, radius:.88+(i%3)*.18, speed:.007+i*.0012, yOff:(Math.random()-.5)*.25 };
    scene.add(o); return o;
  });

  const pN = 400, pP = new Float32Array(pN*3);
  for (let i=0;i<pN;i++){
    const r=1.0+Math.random()*2.2, th=Math.random()*Math.PI*2, ph=Math.acos(2*Math.random()-1);
    pP[i*3]=r*Math.sin(ph)*Math.cos(th); pP[i*3+1]=r*Math.sin(ph)*Math.sin(th)*.5+1.0; pP[i*3+2]=r*Math.cos(ph)*.4;
  }
  const pGeo=new THREE.BufferGeometry(); pGeo.setAttribute('position',new THREE.BufferAttribute(pP,3));
  const pts=new THREE.Points(pGeo, new THREE.PointsMaterial({ color:0xFFD700,size:.018,transparent:true,opacity:.8,blending:THREE.AdditiveBlending,depthWrite:false }));
  scene.add(pts);

  // ── Loader indicator ──
  const heroSec = document.getElementById('hero-section');
  const loadDiv = document.createElement('div');
  loadDiv.id = 'char-loading';
  loadDiv.style.cssText = 'position:absolute;top:50%;right:25%;transform:translate(50%,-50%);color:rgba(255,215,0,.8);font-size:.8rem;letter-spacing:.1em;text-align:center;pointer-events:none;z-index:6;font-family:sans-serif;';
  loadDiv.innerHTML = '<div style="width:36px;height:36px;border:2px solid rgba(255,215,0,.3);border-top-color:#FFD700;border-radius:50%;animation:spin-slow 1s linear infinite;margin:0 auto .5rem;"></div>Chargement 3D...';
  if (heroSec) heroSec.appendChild(loadDiv);

  // ── GLTF Loader ──
  let mixer, model, clock = new THREE.Clock(), fallbackAnim = null;

  const loader = new THREE.GLTFLoader();
  loader.load(
    'https://threejs.org/examples/models/gltf/Soldier/Soldier.glb',
    function(gltf) {
      document.getElementById('char-loading')?.remove();
      model = gltf.scene;
      const box   = new THREE.Box3().setFromObject(model);
      const size  = box.getSize(new THREE.Vector3());
      const sc    = 1.85 / size.y;
      model.scale.setScalar(sc);
      const ctr   = box.getCenter(new THREE.Vector3());
      model.position.set(-ctr.x*sc, -box.min.y*sc, -ctr.z*sc);
      model.traverse(c => {
        if (!c.isMesh) return;
        c.castShadow = true; c.receiveShadow = true;
      });
      scene.add(model);
      mixer = new THREE.AnimationMixer(model);
      const clips = gltf.animations || [];
      if (clips.length) {
        const clip = THREE.AnimationClip.findByName(clips,'Idle') || clips[0];
        mixer.clipAction(clip).play();
      }
      console.log('[KOBLAN 3D] Modèle GLTF humain chargé ✓');
    },
    null,
    function() {
      document.getElementById('char-loading')?.remove();
      console.info('[KOBLAN 3D] GLTF non dispo → personnage procédural');
      const result = buildProceduralHuman(scene);
      model = result.group;
      fallbackAnim = result.animate;
    }
  );

  let mx=0, my=0;
  document.addEventListener('mousemove', e => {
    mx=(e.clientX/window.innerWidth-.5)*2;
    my=(e.clientY/window.innerHeight-.5)*2;
  }, {passive:true});

  window.addEventListener('resize', () => {
    const nW=Math.round(window.innerWidth*.5), nH=window.innerHeight;
    canvas.style.width=nW+'px'; canvas.style.height=nH+'px';
    camera.aspect=nW/nH; camera.updateProjectionMatrix();
    renderer.setSize(nW,nH,false);
  });

  let t=0;
  (function loop(){
    requestAnimationFrame(loop); t+=.01;
    const dt = clock.getDelta();
    if (mixer) mixer.update(dt);
    if (fallbackAnim) fallbackAnim(t);
    if (model) model.rotation.y += (mx*.45 - model.rotation.y) * .05;

    ring1.scale.setScalar(1+Math.sin(t*1.4)*.025);
    ring2.scale.setScalar(1+Math.sin(t*1.4+1)*.025);
    ring3.rotation.z = t*.06;
    ring1.material.opacity = .45+Math.sin(t*2)*.12;
    ring2.material.opacity = .28+Math.sin(t*2+1)*.08;

    orbs.forEach(o => {
      o.userData.angle += o.userData.speed;
      const a = o.userData.angle;
      o.position.set(Math.cos(a)*o.userData.radius, o.userData.yOff+Math.sin(a*1.3)*.15, Math.sin(a)*o.userData.radius);
      o.material.emissiveIntensity = .4+Math.sin(t*3+a)*.3;
    });

    pts.rotation.y = t*.035;
    gold.intensity = 4.5+Math.sin(t*1.5)*.8;
    gold.position.x = Math.sin(t*.4)*1.5;
    rim.position.x  = Math.cos(t*.3)*2.5;

    camera.position.x += (mx*.3 - camera.position.x)*.035;
    camera.position.y += (-my*.2+1.5 - camera.position.y)*.035;
    camera.lookAt(0, 1, 0);
    renderer.render(scene, camera);
  })();
}

// ── Personnage procédural premium avec casquette & vêtements ──
function buildProceduralHuman(scene) {
  const G = new THREE.Group(); scene.add(G);

  const Ph = (c,sh,sp) => new THREE.MeshPhongMaterial({color:c, shininess:sh||30, specular:sp||0x111111});
  const Ss = (r,m,s) => new THREE.Mesh(new THREE.SphereGeometry(r,s||20,s||20), m);
  const Cy = (rT,rB,h,s,m) => new THREE.Mesh(new THREE.CylinderGeometry(rT,rB,h,s), m);
  const Bx = (x,y,z,m) => new THREE.Mesh(new THREE.BoxGeometry(x,y,z), m);

  // Matériaux
  const mSk = Ph(0x4A2810, 25);                               // peau foncée
  const mSh = Ph(0x1e293b, 30);                               // veste/chemise
  const mPn = Ph(0x0f172a, 15);                               // pantalon
  const mSn = Ph(0xf8f8f8, 80, 0x888888);                    // sneakers
  const mSo = Ph(0x111111,  5);                               // semelle
  const mGd = new THREE.MeshStandardMaterial({color:0xFFD700, metalness:1, roughness:.04}); // or
  const mCp = Ph(0xFFD700, 130, 0xFFAA00);                   // casquette or
  const mHr = Ph(0x080808,  40);                              // cheveux
  const mBd = Ph(0x060300,  15);                              // barbe

  // Jambes
  const lL=Cy(.135,.115,.92,12,mPn); lL.position.set(-.2,-.15,0); G.add(lL); lL.castShadow=true;
  const lR=Cy(.135,.115,.92,12,mPn); lR.position.set( .2,-.15,0); G.add(lR); lR.castShadow=true;

  // Sneakers
  const snL=Bx(.22,.13,.4,mSn); snL.position.set(-.2,-.67,.06); G.add(snL);
  const snR=snL.clone(); snR.position.set(.2,-.67,.06); G.add(snR);
  const soL=Bx(.23,.04,.41,mSo); soL.position.set(-.2,-.735,.06); G.add(soL);
  const soR=soL.clone(); soR.position.set(.2,-.735,.06); G.add(soR);
  const tnL=Bx(.14,.055,.12,mSn); tnL.position.set(-.2,-.6,.19); G.add(tnL);
  const tnR=tnL.clone(); tnR.position.set(.2,-.6,.19); G.add(tnR);
  const lcL=Bx(.12,.015,.09,mSo); lcL.position.set(-.2,-.565,.2); G.add(lcL);
  const lcR=lcL.clone(); lcR.position.set(.2,-.565,.2); G.add(lcR);

  // Ceinture
  const blt=Cy(.235,.235,.045,16,Ph(0x333333,10)); blt.position.set(0,.09,0); G.add(blt);
  const bck=Bx(.075,.055,.035,mGd); bck.position.set(0,.09,.235); G.add(bck);

  // Torse (veste)
  const trs=Cy(.27,.235,.92,14,mSh); trs.position.set(0,.56,0); G.add(trs); trs.castShadow=true;
  // Revers
  const rvL=Bx(.095,.28,.05,Ph(0x0c1118,10)); rvL.position.set(-.12,.75,.265); rvL.rotation.z=.22; G.add(rvL);
  const rvR=rvL.clone(); rvR.position.x=.12; rvR.rotation.z=-.22; G.add(rvR);
  // Chemise intérieure blanche
  const shi=Bx(.18,.38,.03,Ph(0xffffff,60)); shi.position.set(0,.72,.278); G.add(shi);
  // Badge
  const bdg=Bx(.09,.065,.04,mGd); bdg.position.set(.17,.8,.27); G.add(bdg);
  const bdgT=Bx(.06,.04,.05,Ph(0x000000,5)); bdgT.position.set(.17,.8,.285); G.add(bdgT);

  // Épaules
  const shL=Ss(.185,Ph(0x0f172a,25)); shL.position.set(-.44,.88,0); G.add(shL);
  const shR=shL.clone(); shR.position.set(.44,.88,0); G.add(shR);

  // Bras (groupes animables)
  const aGL=new THREE.Group(); aGL.position.set(-.44,.88,0); G.add(aGL);
  const aGR=new THREE.Group(); aGR.position.set( .44,.88,0); G.add(aGR);
  Cy(.115,.1,.55,10,mSh).then ? null : [Cy(.115,.1,.55,10,mSh), Cy(.115,.1,.55,10,mSh)].forEach((m,i)=>{m.position.set(0,-.35,0); (i===0?aGL:aGR).add(m);});
  const bsL=Cy(.115,.1,.55,10,mSh); bsL.position.set(0,-.35,0); aGL.add(bsL);
  const bsR=bsL.clone(); aGR.add(bsR);
  const baL=Cy(.095,.085,.4,10,mSk); baL.position.set(0,-.75,0); aGL.add(baL);
  const baR=baL.clone(); aGR.add(baR);
  // Mains
  const mhL=Ss(.1,mSk,14); mhL.position.set(0,-.97,0); aGL.add(mhL);
  const mhR=mhL.clone(); aGR.add(mhR);
  // Doigts
  for(let f=0;f<4;f++){
    const fg=Cy(.014,.012,.07,6,mSk); fg.position.set((f-.15)*.045-.015,-.07,.06); fg.rotation.x=.3;
    const fgC=fg.clone();
    mhL.add(fg); mhR.add(fgC);
  }

  // Montre (main gauche)
  const wB=Bx(.065,.065,.028,mGd); wB.position.set(-.115,-.62,.04); aGL.add(wB);
  const wF=Bx(.055,.055,.031,Ph(0x111111,80)); wF.position.set(-.115,-.62,.054); aGL.add(wF);

  // Cou
  const nck=Cy(.105,.115,.2,12,mSk); nck.position.set(0,1.08,0); G.add(nck);

  // Tête
  const hed=Ss(.295,mSk,24); hed.position.set(0,1.44,0); G.add(hed); hed.castShadow=true;
  // Oreilles
  const eaL=Ss(.065,mSk,10); eaL.position.set(-.3,1.44,0); G.add(eaL);
  const eaR=eaL.clone(); eaR.position.x=.3; G.add(eaR);

  // Yeux
  for(let s=-1;s<=1;s+=2){
    const w=Ss(.07,Ph(0xffffff,100),10); w.position.set(s*.095,1.47,.25); G.add(w);
    const p=Ss(.046,new THREE.MeshBasicMaterial({color:0x0a0a0a}),8); p.position.set(s*.095,1.47,.3); G.add(p);
    const h=Ss(.016,new THREE.MeshBasicMaterial({color:0xffffff}),6); h.position.set(s*.084,1.483,.31); G.add(h);
    const br=Bx(.1,.022,.022,Ph(0x050200,20)); br.position.set(s*.092,1.558,.27); br.rotation.z=s*.1; G.add(br);
  }

  // Nez
  const ns=Ss(.038,mSk,8); ns.scale.set(1,1.35,1); ns.position.set(0,1.41,.3); G.add(ns);
  const nsB=Bx(.034,.058,.03,mSk); nsB.position.set(0,1.385,.295); G.add(nsB);

  // Bouche
  const lp=Bx(.12,.028,.03,Ph(0x5C1A0A,10)); lp.position.set(0,1.37,.285); G.add(lp);
  const lb=Bx(.09,.022,.025,Ph(0x5C1A0A,10)); lb.position.set(0,1.34,.283); G.add(lb);

  // Cheveux sous casquette
  const hr=new THREE.Mesh(new THREE.SphereGeometry(.29,20,16,0,Math.PI*2,0,Math.PI*.45),mHr);
  hr.position.set(0,1.44,0); G.add(hr);
  // Barbe légère
  const brd=new THREE.Mesh(new THREE.SphereGeometry(.205,16,12,0,Math.PI*2,Math.PI*.56,Math.PI*.28),mBd);
  brd.position.set(0,1.44,0); G.add(brd);

  // ── CASQUETTE KOBLAN ──
  const capDome=new THREE.Mesh(new THREE.SphereGeometry(.305,24,16,0,Math.PI*2,0,Math.PI*.52),mCp);
  capDome.position.set(0,1.47,0); G.add(capDome);
  // Bandeau
  const capBand=new THREE.Mesh(new THREE.CylinderGeometry(.296,.296,.042,24,1,true,.22,Math.PI*1.56),Ph(0x111111,10));
  capBand.position.set(0,1.41,0); G.add(capBand);
  // Bouton sommet
  const capBtn=Ss(.026,mGd,6); capBtn.position.set(0,1.75,0); G.add(capBtn);
  // Visière
  const brimGrp=new THREE.Group(); brimGrp.position.set(0,1.44,0); G.add(brimGrp);
  const brim=Bx(.32,.045,.2,mCp); brim.position.set(0,.0,.27); brim.rotation.x=-.18; brimGrp.add(brim);
  const brimU=Bx(.31,.02,.18,Ph(0x222200,5)); brimU.position.set(0,-.022,.27); brimU.rotation.x=-.18; brimGrp.add(brimU);
  // Logo K
  const logoPl=Bx(.065,.04,.011,Ph(0x000000,5)); logoPl.position.set(0,.01,.36); brimGrp.add(logoPl);
  const logoK=Bx(.032,.032,.013,mGd); logoK.position.set(0,.01,.365); brimGrp.add(logoK);

  // Chaîne
  const chain=new THREE.Mesh(new THREE.TorusGeometry(.09,.012,8,32),mGd);
  chain.rotation.x=Math.PI/2; chain.position.set(0,.72,.28); G.add(chain);

  // ── Animate function (retourné pour la loop principale) ──
  function animate(t) {
    G.position.y = Math.sin(t*.7)*.04;
    hed.rotation.y = Math.sin(t*.5)*.1;
    capDome.rotation.y = hed.rotation.y;
    brimGrp.rotation.y = hed.rotation.y;
    hr.rotation.y  = hed.rotation.y;
    brd.rotation.y = hed.rotation.y;
    aGL.rotation.z =  Math.sin(t*.65+1.57)*.28 + .22;
    aGR.rotation.z = -Math.sin(t*.65+1.57)*.28 - .22;
  }

  return { group: G, animate };
}
