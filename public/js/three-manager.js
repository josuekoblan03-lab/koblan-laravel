// ============================================================
// KOBLAN — Three.js Manager PREMIUM v2.0
// Effets 3D cinématiques ultra-professionnels
// Réponse au scroll ET à la souris sur tout le site
// ============================================================

const KoblanThree = (() => {
  const scenes = {};

  function makeRenderer(canvas, alpha = true) {
    const r = new THREE.WebGLRenderer({ canvas, antialias: true, alpha, powerPreference: 'high-performance' });
    r.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    r.setClearColor(0x000000, 0);
    r.shadowMap.enabled = false;
    return r;
  }

  // ── SCÈNE GLOBALE ULTRA-PREMIUM : Galaxie + Aurora + Géométries ──
  function initGlobalParticles() {
    const canvas = document.getElementById('global-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    // Canvas plein écran fixe
    canvas.style.cssText = `
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
      pointer-events: none; z-index: 0;
    `;

    const renderer = makeRenderer(canvas);
    renderer.setSize(window.innerWidth, window.innerHeight);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 2000);
    camera.position.set(0, 0, 25);

    // ─── 1. Galaxie Spirale (15k particules) ───
    const galaxyCount = 15000;
    const galaxyPos = new Float32Array(galaxyCount * 3);
    const galaxyColors = new Float32Array(galaxyCount * 3);
    const galaxySizes = new Float32Array(galaxyCount);

    const colorGold = new THREE.Color(0xFFD700);
    const colorOrange = new THREE.Color(0xF77F00);
    const colorWhite = new THREE.Color(0xffffff);
    const colorBlue = new THREE.Color(0x4488ff);

    for (let i = 0; i < galaxyCount; i++) {
      const arm = Math.floor(Math.random() * 3); // 3 bras de spirale
      const armAngle = (arm / 3) * Math.PI * 2;
      const r = Math.pow(Math.random(), 0.7) * 50;
      const spinAngle = r * 0.4;
      const branchAngle = armAngle + spinAngle;
      const rand = (Math.random() - 0.5) * Math.pow(Math.random(), 3) * 6;
      const randY = (Math.random() - 0.5) * Math.pow(Math.random(), 3) * 2;

      galaxyPos[i * 3]     = Math.cos(branchAngle) * r + rand;
      galaxyPos[i * 3 + 1] = randY;
      galaxyPos[i * 3 + 2] = Math.sin(branchAngle) * r + rand - 15;

      // Couleur mixée selon distance du centre
      const mixT = r / 50;
      const c = mixT < 0.3 ? colorWhite.clone().lerp(colorGold, mixT / 0.3)
                            : mixT < 0.7 ? colorGold.clone().lerp(colorOrange, (mixT - 0.3) / 0.4)
                                         : colorOrange.clone().lerp(colorBlue, (mixT - 0.7) / 0.3);
      galaxyColors[i * 3]     = c.r;
      galaxyColors[i * 3 + 1] = c.g;
      galaxyColors[i * 3 + 2] = c.b;
      galaxySizes[i] = Math.random() * 1.5 + 0.5;
    }

    const galaxyGeo = new THREE.BufferGeometry();
    galaxyGeo.setAttribute('position', new THREE.BufferAttribute(galaxyPos, 3));
    galaxyGeo.setAttribute('color', new THREE.BufferAttribute(galaxyColors, 3));
    galaxyGeo.setAttribute('size', new THREE.BufferAttribute(galaxySizes, 1));

    const galaxyMat = new THREE.PointsMaterial({
      size: 0.08,
      sizeAttenuation: true,
      vertexColors: true,
      transparent: true,
      opacity: 0.55,
      depthWrite: false,
      blending: THREE.AdditiveBlending,
    });

    const galaxy = new THREE.Points(galaxyGeo, galaxyMat);
    galaxy.rotation.x = Math.PI * 0.2;
    scene.add(galaxy);

    // ─── 2. Nébuleuse floue (grandes sphères transparentes) ───
    const nebulaColors = [0xFFD700, 0xF77F00, 0x4444ff, 0xff44aa, 0x00ffaa];
    for (let i = 0; i < 5; i++) {
      const geo = new THREE.SphereGeometry(3 + Math.random() * 4, 8, 8);
      const mat = new THREE.MeshBasicMaterial({
        color: nebulaColors[i],
        transparent: true,
        opacity: 0.008 + Math.random() * 0.015,
        wireframe: false,
      });
      const mesh = new THREE.Mesh(geo, mat);
      mesh.position.set(
        (Math.random() - 0.5) * 50,
        (Math.random() - 0.5) * 20,
        -20 + (Math.random() - 0.5) * 15
      );
      mesh.userData.drift = {
        x: (Math.random() - 0.5) * 0.005,
        y: (Math.random() - 0.5) * 0.003,
        s: Math.random() * 0.003 + 0.001,
        phase: Math.random() * Math.PI * 2,
      };
      scene.add(mesh);
    }

    // ─── 3. Géométries héroïques flottantes ───
    const floaters = [];
    const geoTypes = [
      () => new THREE.IcosahedronGeometry(0.6, 1),
      () => new THREE.OctahedronGeometry(0.5),
      () => new THREE.TorusKnotGeometry(0.4, 0.12, 80, 12),
      () => new THREE.TetrahedronGeometry(0.5),
      () => new THREE.TorusGeometry(0.5, 0.15, 16, 40),
    ];

    for (let i = 0; i < 18; i++) {
      const geoFn = geoTypes[i % geoTypes.length];
      const geo = geoFn();
      const isWireframe = Math.random() > 0.4;
      const mat = new THREE.MeshStandardMaterial({
        color: Math.random() > 0.6 ? 0xFFD700 : (Math.random() > 0.5 ? 0xF77F00 : 0x4466FF),
        metalness: 0.9,
        roughness: 0.1,
        wireframe: isWireframe,
        transparent: true,
        opacity: isWireframe ? 0.12 : 0.35,
      });
      const mesh = new THREE.Mesh(geo, mat);
      mesh.position.set(
        (Math.random() - 0.5) * 50,
        (Math.random() - 0.5) * 35,
        (Math.random() - 0.5) * 20 - 5
      );
      mesh.userData.speed = {
        rx: (Math.random() - 0.5) * 0.008,
        ry: (Math.random() - 0.5) * 0.012,
        floatOffset: Math.random() * Math.PI * 2,
        floatSpeed: Math.random() * 0.008 + 0.004,
      };
      scene.add(mesh);
      floaters.push(mesh);
    }

    // ─── 4. Réseau de lignes (Energy Nodes) ───
    const nodeCount = 80;
    const nodePositions = [];
    for (let i = 0; i < nodeCount; i++) {
      nodePositions.push(new THREE.Vector3(
        (Math.random() - 0.5) * 60,
        (Math.random() - 0.5) * 40,
        (Math.random() - 0.5) * 20 - 10
      ));
    }

    const lineVerts = [];
    const maxDist = 12;
    for (let i = 0; i < nodeCount; i++) {
      for (let j = i + 1; j < nodeCount; j++) {
        const d = nodePositions[i].distanceTo(nodePositions[j]);
        if (d < maxDist) {
          lineVerts.push(nodePositions[i].x, nodePositions[i].y, nodePositions[i].z);
          lineVerts.push(nodePositions[j].x, nodePositions[j].y, nodePositions[j].z);
        }
      }
    }
    const linGeo = new THREE.BufferGeometry();
    linGeo.setAttribute('position', new THREE.BufferAttribute(new Float32Array(lineVerts), 3));
    const linMat = new THREE.LineBasicMaterial({
      color: 0xFFD700,
      transparent: true,
      opacity: 0.06,
      blending: THREE.AdditiveBlending,
    });
    const energyLines = new THREE.LineSegments(linGeo, linMat);
    scene.add(energyLines);

    // ─── 5. Lumières dynamiques ───
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.2);
    scene.add(ambientLight);

    const goldLight = new THREE.PointLight(0xFFD700, 3, 60);
    goldLight.position.set(10, 10, 5);
    scene.add(goldLight);

    const orangeLight = new THREE.PointLight(0xF77F00, 2, 40);
    orangeLight.position.set(-15, -8, 0);
    scene.add(orangeLight);

    const blueLight = new THREE.PointLight(0x4466FF, 1.5, 50);
    blueLight.position.set(0, 15, -10);
    scene.add(blueLight);

    // ─── Animation & Interaction ───
    let mouseX = 0, mouseY = 0;
    let scrollY = 0;
    let targetMouseX = 0, targetMouseY = 0;
    let t = 0;

    document.addEventListener('mousemove', (e) => {
      targetMouseX = (e.clientX / window.innerWidth - 0.5) * 2;
      targetMouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });

    window.addEventListener('scroll', () => {
      scrollY = window.scrollY;
    }, { passive: true });

    window.addEventListener('resize', () => {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    });

    function animate() {
      requestAnimationFrame(animate);
      t += 0.005;

      // Smooth mouse tracking
      mouseX += (targetMouseX - mouseX) * 0.04;
      mouseY += (targetMouseY - mouseY) * 0.04;

      // Galaxy rotation (parallax scroll)
      galaxy.rotation.y = t * 0.04 + scrollY * 0.0002;
      galaxy.rotation.z = t * 0.01;

      // Camera reacts to mouse + scroll
      camera.position.x += (mouseX * 3 - camera.position.x) * 0.03;
      camera.position.y += (-mouseY * 2 - camera.position.y + scrollY * 0.005) * 0.03;
      camera.lookAt(0, 0, -10);

      // Float + rotate geometries
      floaters.forEach((f, i) => {
        f.rotation.x += f.userData.speed.rx;
        f.rotation.y += f.userData.speed.ry;
        f.position.y += Math.sin(t * f.userData.speed.floatSpeed + f.userData.speed.floatOffset) * 0.012;
        // Subtle parallax on scroll
        f.position.z += Math.sin(t * 0.5 + i) * 0.002;
      });

      // Energy lines subtle pulse
      energyLines.material.opacity = 0.05 + Math.sin(t * 2) * 0.03;
      energyLines.rotation.y = t * 0.008;

      // Nebula drift
      scene.children.forEach(child => {
        if (child.userData.drift) {
          const d = child.userData.drift;
          child.position.x += d.x;
          child.position.y += d.y;
          child.material.opacity = (Math.sin(t * d.s + d.phase) + 1) * 0.01 + 0.005;
          // Wrap around
          if (Math.abs(child.position.x) > 35) d.x *= -1;
          if (Math.abs(child.position.y) > 20) d.y *= -1;
        }
      });

      // Dynamic gold light orbit
      goldLight.position.x = Math.cos(t * 0.3) * 15;
      goldLight.position.z = Math.sin(t * 0.3) * 10;
      goldLight.intensity = 2.5 + Math.sin(t * 1.2) * 0.8;

      orangeLight.position.x = Math.sin(t * 0.2) * 20;
      orangeLight.position.y = Math.cos(t * 0.25) * 10;

      renderer.render(scene, camera);
    }

    animate();
    scenes.global = { renderer, scene, camera };
  }

  // ── SCÈNE HERO : Sphère holographique avancée ──
  function initHeroScene() {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    const renderer = makeRenderer(canvas);
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.shadowMap.enabled = false;
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.set(0, 0, 5);

    // Sphère centrale avec texture holographique
    const sphereGeo = new THREE.SphereGeometry(1.5, 128, 128);
    const sphereMat = new THREE.MeshStandardMaterial({
      color: 0xFFD700,
      metalness: 0.98,
      roughness: 0.02,
      emissive: 0xB8860B,
      emissiveIntensity: 0.4,
    });
    const sphere = new THREE.Mesh(sphereGeo, sphereMat);
    sphere.position.set(2.5, 0, -1);
    scene.add(sphere);

    // Multiple wireframes concentriques
    [1.52, 1.7, 1.9].forEach((r, i) => {
      const wfGeo = new THREE.SphereGeometry(r, 16 - i * 3, 16 - i * 3);
      const wfMat = new THREE.MeshBasicMaterial({
        color: i === 0 ? 0xFFD700 : (i === 1 ? 0xF77F00 : 0xffffff),
        wireframe: true,
        transparent: true,
        opacity: 0.1 - i * 0.02
      });
      const wf = new THREE.Mesh(wfGeo, wfMat);
      wf.position.copy(sphere.position);
      wf.userData.rotSpeed = { rx: 0.003 + i * 0.002, ry: 0.005 + i * 0.003 };
      scene.add(wf);
    });

    // Anneaux orbitaux
    const ringConfigs = [
      { r: 2.2, t: 0.018, rot: [Math.PI/3, 0, 0],         color: 0xFFD700, op: 0.5 },
      { r: 2.8, t: 0.012, rot: [Math.PI/5, Math.PI/6, 0], color: 0xF77F00, op: 0.3 },
      { r: 3.5, t: 0.008, rot: [-Math.PI/4, 0, Math.PI/5], color: 0x4466FF, op: 0.2 },
      { r: 4.0, t: 0.006, rot: [Math.PI/2, Math.PI/8, 0], color: 0xffffff, op: 0.1 },
    ];
    const rings = ringConfigs.map(cfg => {
      const geo = new THREE.TorusGeometry(cfg.r, cfg.t, 16, 120);
      const mat = new THREE.MeshBasicMaterial({ color: cfg.color, transparent: true, opacity: cfg.op });
      const mesh = new THREE.Mesh(geo, mat);
      mesh.position.copy(sphere.position);
      mesh.rotation.set(...cfg.rot);
      scene.add(mesh);
      return mesh;
    });

    // Particules orbitales avancées
    const pCount = 600;
    const pPos = new Float32Array(pCount * 3);
    const pColors = new Float32Array(pCount * 3);
    for (let i = 0; i < pCount; i++) {
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(2 * Math.random() - 1);
      const r = 2 + Math.random() * 4;
      pPos[i*3]   = sphere.position.x + r * Math.sin(phi) * Math.cos(theta);
      pPos[i*3+1] = r * Math.sin(phi) * Math.sin(theta) * 0.6;
      pPos[i*3+2] = sphere.position.z + r * Math.cos(phi) * 0.4;
      const c = Math.random() > 0.6 ? colorGoldHex() : colorOrangeHex();
      pColors[i*3] = c[0]; pColors[i*3+1] = c[1]; pColors[i*3+2] = c[2];
    }
    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
    pGeo.setAttribute('color', new THREE.BufferAttribute(pColors, 3));
    const pMat = new THREE.PointsMaterial({
      size: 0.04,
      vertexColors: true,
      transparent: true,
      opacity: 0.8,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // Lumières
    scene.add(new THREE.AmbientLight(0xffffff, 0.4));
    const gLight = new THREE.PointLight(0xFFD700, 6, 15);
    gLight.position.set(5, 3, 2);
    scene.add(gLight);
    scene.add(Object.assign(new THREE.PointLight(0xF77F00, 3, 10), { position: new THREE.Vector3(-3, -2, 1) }));
    scene.add(Object.assign(new THREE.PointLight(0x4466FF, 2, 12), { position: new THREE.Vector3(0, 5, 3) }));

    let mouseX = 0, mouseY = 0;
    document.addEventListener('mousemove', e => {
      mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
      mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });

    window.addEventListener('resize', () => {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    });

    let t = 0;
    function animate() {
      requestAnimationFrame(animate);
      t += 0.008;
      sphere.rotation.x = t * 0.1 + mouseY * 0.3;
      sphere.rotation.y = t * 0.12 + mouseX * 0.3;

      // Wireframes concentriques contra-rotation
      scene.children.filter(c => c.userData.rotSpeed).forEach((wf, i) => {
        wf.rotation.x += wf.userData.rotSpeed.rx * (i % 2 === 0 ? 1 : -1);
        wf.rotation.y += wf.userData.rotSpeed.ry;
      });

      const breathe = 1 + Math.sin(t * 0.7) * 0.035;
      sphere.scale.setScalar(breathe);

      // Anneaux avec vitesses différentes
      rings[0].rotation.x = Math.PI/3 + t * 0.22;
      rings[0].rotation.z = t * 0.1;
      rings[1].rotation.y = t * 0.18;
      rings[1].rotation.z = -t * 0.08;
      rings[2].rotation.x = -Math.PI/4 + t * 0.12;
      rings[2].rotation.y = t * 0.15;
      rings[3].rotation.z = t * 0.06;
      rings[3].rotation.x = t * 0.04;

      particles.rotation.y = t * 0.05;
      particles.rotation.x = Math.sin(t * 0.08) * 0.10;

      gLight.intensity = 5 + Math.sin(t * 1.5) * 1;
      gLight.position.x = Math.cos(t * 0.4) * 6 + 2.5;
      gLight.position.z = Math.sin(t * 0.4) * 4;

      camera.position.x += (mouseX * 0.3 - camera.position.x) * 0.04;
      camera.position.y += (-mouseY * 0.25 - camera.position.y) * 0.04;
      camera.lookAt(0, 0, 0);
      renderer.render(scene, camera);
    }
    animate();
    scenes.hero = { renderer, scene, camera };
  }

  // ── Helpers couleurs ──
  function colorGoldHex() { return [1.0, 0.84, 0.0]; }
  function colorOrangeHex() { return [0.97, 0.49, 0.0]; }

  // ── SCÈNE SECTION : Géométrie thématique interactive ──
  function initSectionScene(canvasId, geometry = 'octahedron', color = 0xFFD700) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof THREE === 'undefined') return;

    const renderer = makeRenderer(canvas);
    const w = canvas.parentElement.offsetWidth;
    const h = canvas.parentElement.offsetHeight || 400;
    renderer.setSize(w, h);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, w / h, 0.1, 100);
    camera.position.z = 4;

    let geo;
    switch(geometry) {
      case 'icosahedron': geo = new THREE.IcosahedronGeometry(1.2, 1); break;
      case 'torus':       geo = new THREE.TorusGeometry(1, 0.4, 32, 80); break;
      case 'torusknot':   geo = new THREE.TorusKnotGeometry(0.8, 0.25, 160, 20); break;
      case 'octahedron':  default: geo = new THREE.OctahedronGeometry(1.2, 0); break;
    }

    const mat = new THREE.MeshStandardMaterial({
      color, metalness: 0.95, roughness: 0.05,
      emissive: color, emissiveIntensity: 0.15,
    });
    const mesh = new THREE.Mesh(geo, mat);
    scene.add(mesh);

    const wfMat = new THREE.MeshBasicMaterial({ color, wireframe: true, transparent: true, opacity: 0.2 });
    const wfMesh = new THREE.Mesh(geo.clone(), wfMat);
    wfMesh.scale.setScalar(1.04);
    scene.add(wfMesh);

    scene.add(new THREE.AmbientLight(0xffffff, 0.3));
    const pl = new THREE.PointLight(color, 4, 12);
    pl.position.set(3, 3, 3);
    scene.add(pl);
    const pl2 = new THREE.PointLight(0xffffff, 1, 8);
    pl2.position.set(-3, -2, 2);
    scene.add(pl2);

    let t = 0, mouse = { x: 0, y: 0 };
    canvas.parentElement.addEventListener('mousemove', e => {
      const rect = canvas.getBoundingClientRect();
      mouse.x = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
      mouse.y = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
    });

    function animate() {
      requestAnimationFrame(animate);
      t += 0.01;
      mesh.rotation.x = t * 0.3 + mouse.y * 0.4;
      mesh.rotation.y = t * 0.4 + mouse.x * 0.4;
      wfMesh.rotation.copy(mesh.rotation);
      const pulse = 1 + Math.sin(t * 2) * 0.03;
      mesh.scale.setScalar(pulse);
      wfMesh.scale.setScalar(pulse * 1.04);
      pl.position.x = Math.cos(t) * 3;
      pl.position.z = Math.sin(t) * 3;
      pl.intensity = 3.5 + Math.sin(t * 2.5) * 1;
      renderer.render(scene, camera);
    }
    animate();
    scenes[canvasId] = { renderer, scene, camera };
  }

  // ── SCÈNE AUTH ──
  function initAuthScene() {
    const canvas = document.getElementById('auth-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    const renderer = makeRenderer(canvas);
    const w = canvas.offsetWidth || window.innerWidth;
    const h = canvas.offsetHeight || window.innerHeight;
    renderer.setSize(w, h);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(70, w / h, 0.1, 1000);
    camera.position.z = 10;

    // Double hélice ADN premium
    for (let strand = 0; strand < 2; strand++) {
      const pts = [];
      const offset = strand * Math.PI;
      for (let i = 0; i <= 200; i++) {
        const t2 = (i / 200) * Math.PI * 8;
        pts.push(new THREE.Vector3(
          Math.cos(t2 + offset) * 3,
          (i / 200) * 22 - 11,
          Math.sin(t2 + offset) * 3
        ));
      }
      const geo = new THREE.BufferGeometry().setFromPoints(pts);
      const mat = new THREE.LineBasicMaterial({
        color: strand === 0 ? 0xFFD700 : 0xF77F00,
        transparent: true,
        opacity: 0.4,
      });
      scene.add(new THREE.Line(geo, mat));
    }

    // Points lumineux ADN
    for (let i = 0; i <= 200; i += 10) {
      const t2 = (i / 200) * Math.PI * 8;
      ['strand0', 'strand1'].forEach((_, s) => {
        const off = s * Math.PI;
        const geo = new THREE.SphereGeometry(0.1);
        const mat = new THREE.MeshBasicMaterial({
          color: s === 0 ? 0xFFD700 : 0xF77F00,
          transparent: true, opacity: 0.8
        });
        const mesh = new THREE.Mesh(geo, mat);
        mesh.position.set(Math.cos(t2 + off) * 3, (i / 200) * 22 - 11, Math.sin(t2 + off) * 3);
        scene.add(mesh);
      });
    }

    // Particules flottantes
    const pCount = 300;
    const pPos = new Float32Array(pCount * 3);
    for (let i = 0; i < pCount; i++) {
      pPos[i*3] = (Math.random() - 0.5) * 20;
      pPos[i*3+1] = (Math.random() - 0.5) * 20;
      pPos[i*3+2] = (Math.random() - 0.5) * 8;
    }
    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
    const pMat = new THREE.PointsMaterial({
      color: 0xFFD700, size: 0.06, transparent: true, opacity: 0.45,
      blending: THREE.AdditiveBlending
    });
    scene.add(new THREE.Points(pGeo, pMat));

    let t = 0, mouseX = 0;
    document.addEventListener('mousemove', e => {
      mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
    }, { passive: true });

    function animate() {
      requestAnimationFrame(animate);
      t += 0.004;
      scene.rotation.y = t * 0.15 + mouseX * 0.3;
      camera.position.y = Math.sin(t * 0.4) * 1;
      renderer.render(scene, camera);
    }
    animate();
    scenes.auth = { renderer, scene, camera };
  }

  // ── SCÈNE PERSONNAGE HERO (canvas droit) ──
  function initCharacterScene() {
    const canvas = document.getElementById('character-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    // Force les dimensions en pixels (important: clientWidth=0 avant layout)
    const W = Math.round(window.innerWidth * 0.5);
    const H = window.innerHeight;
    const DPR = Math.min(window.devicePixelRatio, 2);

    // Forcer le style CSS aussi
    canvas.style.width  = W + 'px';
    canvas.style.height = H + 'px';

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
    renderer.setPixelRatio(DPR);
    renderer.setSize(W, H);
    renderer.setClearColor(0x000000, 0);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, W / H, 0.1, 100);
    camera.position.set(0, 1.2, 6.5);
    camera.lookAt(0, 1, 0);

    // ─── Matériaux ───
    const matGold = new THREE.MeshStandardMaterial({
      color: 0xFFD700, metalness: 0.95, roughness: 0.05,
      emissive: 0xB8860B, emissiveIntensity: 0.3,
    });
    const matDark = new THREE.MeshStandardMaterial({
      color: 0x1a1a1a, metalness: 0.8, roughness: 0.2,
    });
    const matOrange = new THREE.MeshStandardMaterial({
      color: 0xF77F00, metalness: 0.9, roughness: 0.1,
      emissive: 0xF77F00, emissiveIntensity: 0.2,
    });
    const matSkin = new THREE.MeshStandardMaterial({
      color: 0x8B5E3C, metalness: 0.1, roughness: 0.8,
    });
    const matWire = new THREE.MeshBasicMaterial({
      color: 0xFFD700, wireframe: true, transparent: true, opacity: 0.08,
    });

    function addWireOverlay(mesh, parent) {
      const wf = new THREE.Mesh(mesh.geometry.clone(), matWire.clone());
      wf.scale.setScalar(1.012);
      parent.add(wf);
    }

    // ─── Groupe racine du personnage ───
    const character = new THREE.Group();
    scene.add(character);

    // ── Jambes ──
    const legGeoL = new THREE.CylinderGeometry(0.13, 0.11, 1.0, 16);
    const legL = new THREE.Mesh(legGeoL, matDark);
    legL.position.set(-0.22, 0.0, 0);
    legL.castShadow = true;
    character.add(legL);
    addWireOverlay(legL, character);

    const legR = legL.clone();
    legR.position.set(0.22, 0.0, 0);
    character.add(legR);
    addWireOverlay(legR, character);

    // Pieds
    const footGeo = new THREE.BoxGeometry(0.18, 0.08, 0.3);
    const footL = new THREE.Mesh(footGeo, matGold);
    footL.position.set(-0.22, -0.54, 0.05);
    character.add(footL);
    const footR = footL.clone();
    footR.position.set(0.22, -0.54, 0.05);
    character.add(footR);

    // ── Bassin ──
    const hipsGeo = new THREE.CylinderGeometry(0.3, 0.25, 0.22, 16);
    const hips = new THREE.Mesh(hipsGeo, matDark);
    hips.position.set(0, 0.6, 0);
    character.add(hips);

    // ── Torse ──
    const torsoGeo = new THREE.CylinderGeometry(0.22, 0.28, 0.75, 16);
    const torso = new THREE.Mesh(torsoGeo, matDark);
    torso.position.set(0, 1.1, 0);
    torso.castShadow = true;
    character.add(torso);
    addWireOverlay(torso, character);

    // Badge or sur le torse
    const badgeGeo = new THREE.BoxGeometry(0.22, 0.14, 0.04);
    const badge = new THREE.Mesh(badgeGeo, matGold);
    badge.position.set(0, 1.1, 0.26);
    character.add(badge);

    // ── Épaules ──
    const shoulderGeo = new THREE.SphereGeometry(0.16, 16, 16);
    const shoulderL = new THREE.Mesh(shoulderGeo, matOrange);
    shoulderL.position.set(-0.42, 1.42, 0);
    character.add(shoulderL);
    const shoulderR = shoulderL.clone();
    shoulderR.position.set(0.42, 1.42, 0);
    character.add(shoulderR);

    // ── Bras ──
    const armGeo = new THREE.CylinderGeometry(0.09, 0.08, 0.7, 12);

    // Bras gauche (groupe pour pouvoir l'animer)
    const armGroupL = new THREE.Group();
    armGroupL.position.set(-0.42, 1.42, 0);
    const armMeshL = new THREE.Mesh(armGeo, matDark);
    armMeshL.position.set(0, -0.42, 0);
    armGroupL.add(armMeshL);
    character.add(armGroupL);

    const armGroupR = new THREE.Group();
    armGroupR.position.set(0.42, 1.42, 0);
    const armMeshR = new THREE.Mesh(armGeo, matDark);
    armMeshR.position.set(0, -0.42, 0);
    armGroupR.add(armMeshR);
    character.add(armGroupR);

    // Mains
    const handGeo = new THREE.SphereGeometry(0.1, 12, 12);
    const handL = new THREE.Mesh(handGeo, matSkin);
    handL.position.set(-0.42, 0.9, 0);
    character.add(handL);
    const handR = handL.clone();
    handR.position.set(0.42, 0.9, 0);
    character.add(handR);

    // ── Cou ──
    const neckGeo = new THREE.CylinderGeometry(0.1, 0.12, 0.2, 12);
    const neck = new THREE.Mesh(neckGeo, matSkin);
    neck.position.set(0, 1.58, 0);
    character.add(neck);

    // ── Tête ──
    const headGeo = new THREE.SphereGeometry(0.28, 32, 32);
    const head = new THREE.Mesh(headGeo, matSkin);
    head.position.set(0, 1.96, 0);
    head.castShadow = true;
    character.add(head);

    // Casque / cheveux
    const helmetGeo = new THREE.SphereGeometry(0.295, 32, 16, 0, Math.PI*2, 0, Math.PI*0.55);
    const helmet = new THREE.Mesh(helmetGeo, matGold);
    helmet.position.set(0, 1.96, 0);
    character.add(helmet);

    // Visière
    const visorGeo = new THREE.TorusGeometry(0.24, 0.025, 8, 40, Math.PI);
    const visor = new THREE.Mesh(visorGeo, matOrange);
    visor.rotation.x = Math.PI * 0.5;
    visor.position.set(0, 1.9, 0.1);
    character.add(visor);

    // Yeux lumineux
    const eyeGeo = new THREE.SphereGeometry(0.04, 8, 8);
    const eyeMat = new THREE.MeshBasicMaterial({ color: 0xFFE500 });
    const eyeL = new THREE.Mesh(eyeGeo, eyeMat);
    eyeL.position.set(-0.1, 1.96, 0.25);
    character.add(eyeL);
    const eyeR = eyeL.clone();
    eyeR.position.set(0.1, 1.96, 0.25);
    character.add(eyeR);

    // ── Socle / Plateforme ──
    const baseGeo = new THREE.CylinderGeometry(0.8, 0.9, 0.06, 32);
    const base = new THREE.Mesh(baseGeo, matGold);
    base.position.set(0, -0.62, 0);
    base.receiveShadow = true;
    character.add(base);

    // Anneau autour du socle (orbite)
    const ringGeo = new THREE.TorusGeometry(1.0, 0.018, 8, 80);
    const ringMat = new THREE.MeshBasicMaterial({ color: 0xFFD700, transparent: true, opacity: 0.4 });
    const ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI * 0.5;
    ring.position.set(0, -0.62, 0);
    scene.add(ring);

    const ring2 = new THREE.Mesh(
      new THREE.TorusGeometry(1.3, 0.01, 8, 80),
      new THREE.MeshBasicMaterial({ color: 0xF77F00, transparent: true, opacity: 0.25 })
    );
    ring2.rotation.x = Math.PI * 0.5;
    ring2.position.set(0, -0.62, 0);
    scene.add(ring2);

    // Sphères orbitales
    const orbGeo = new THREE.SphereGeometry(0.06, 12, 12);
    const orbs = [];
    for (let i = 0; i < 6; i++) {
      const m = new THREE.Mesh(orbGeo, i % 2 === 0 ? matGold : matOrange);
      m.userData.angle = (i / 6) * Math.PI * 2;
      m.userData.radius = 1.0 + (i % 2) * 0.3;
      m.userData.speed  = 0.008 + i * 0.002;
      m.userData.yOff   = Math.sin(i) * 0.3;
      scene.add(m);
      orbs.push(m);
    }

    // Particules autour du perso
    const pCount = 200;
    const pPos = new Float32Array(pCount * 3);
    for (let i = 0; i < pCount; i++) {
      const r = 1.2 + Math.random() * 1.5;
      const theta = Math.random() * Math.PI * 2;
      const phi   = Math.acos(2 * Math.random() - 1);
      pPos[i*3]   = r * Math.sin(phi) * Math.cos(theta);
      pPos[i*3+1] = r * Math.sin(phi) * Math.sin(theta);
      pPos[i*3+2] = r * Math.cos(phi);
    }
    const pGeo2 = new THREE.BufferGeometry();
    pGeo2.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
    const pMat2 = new THREE.PointsMaterial({
      color: 0xFFD700, size: 0.025, transparent: true, opacity: 0.7,
      blending: THREE.AdditiveBlending, depthWrite: false,
    });
    const particlesMesh = new THREE.Points(pGeo2, pMat2);
    particlesMesh.position.set(0, 0.7, 0);
    scene.add(particlesMesh);

    // ── Lumières ──
    scene.add(new THREE.AmbientLight(0xffffff, 0.35));

    const keyLight = new THREE.DirectionalLight(0xffffff, 1.5);
    keyLight.position.set(3, 5, 4);
    keyLight.castShadow = true;
    scene.add(keyLight);

    const goldSpot = new THREE.PointLight(0xFFD700, 8, 10);
    goldSpot.position.set(0, 4, 2);
    scene.add(goldSpot);

    const rimLight = new THREE.PointLight(0xF77F00, 4, 8);
    rimLight.position.set(-3, 2, -2);
    scene.add(rimLight);

    const fillLight = new THREE.PointLight(0x4466FF, 2, 8);
    fillLight.position.set(2, 0, 3);
    scene.add(fillLight);

    // ── Sol réflecteur (ombre) ──
    const groundGeo = new THREE.PlaneGeometry(10, 10);
    const groundMat = new THREE.ShadowMaterial({ opacity: 0.2 });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -0.65;
    ground.receiveShadow = true;
    scene.add(ground);

    // ── Interaction souris ──
    let mx = 0, my = 0;
    document.addEventListener('mousemove', e => {
      mx = (e.clientX / window.innerWidth  - 0.5) * 2;
      my = (e.clientY / window.innerHeight - 0.5) * 2;
    }, { passive: true });

    // Resize
    window.addEventListener('resize', () => {
      const nW = Math.round(window.innerWidth * 0.5);
      const nH = window.innerHeight;
      canvas.style.width  = nW + 'px';
      canvas.style.height = nH + 'px';
      camera.aspect = nW / nH;
      camera.updateProjectionMatrix();
      renderer.setSize(nW, nH);
    });

    // ── Loop d'animation ──
    let t = 0;
    function animate() {
      requestAnimationFrame(animate);
      t += 0.01;

      // Respiration / flottement
      const breathe = Math.sin(t * 0.8) * 0.04;
      character.position.y = breathe;
      character.rotation.y = mx * 0.25 + Math.sin(t * 0.3) * 0.08;

      // Balancement de la tête
      head.rotation.y   = Math.sin(t * 0.5) * 0.12 + mx * 0.15;
      head.rotation.x   = -my * 0.08;
      helmet.rotation.y = head.rotation.y;
      helmet.rotation.x = head.rotation.x;
      visor.rotation.z  = head.rotation.y;

      // Bras qui se balancent
      armGroupL.rotation.z =  Math.sin(t * 0.7 + Math.PI * 0.5) * 0.25 + 0.15;
      armGroupR.rotation.z = -Math.sin(t * 0.7 + Math.PI * 0.5) * 0.25 - 0.15;
      handL.position.y = 0.9 + Math.sin(t * 0.7 + Math.PI * 0.5) * 0.08;
      handR.position.y = 0.9 + Math.sin(t * 0.7) * 0.08;

      // Yeux qui clignotent
      const blink = Math.sin(t * 8) > 0.98;
      eyeL.visible = !blink;
      eyeR.visible = !blink;

      // Lumière dynamique
      goldSpot.intensity = 7 + Math.sin(t * 1.2) * 1.5;
      goldSpot.position.x = Math.sin(t * 0.4) * 2;

      // Orbes orbitales
      orbs.forEach(orb => {
        orb.userData.angle += orb.userData.speed;
        const a = orb.userData.angle;
        orb.position.set(
          Math.cos(a) * orb.userData.radius,
          -0.62 + orb.userData.yOff + Math.sin(a * 1.5) * 0.2,
          Math.sin(a) * orb.userData.radius
        );
      });

      // Anneaux du socle pulsants
      ring.scale.setScalar(1 + Math.sin(t * 1.5) * 0.03);
      ring2.scale.setScalar(1 + Math.sin(t * 1.5 + Math.PI) * 0.03);
      ring.material.opacity  = 0.3 + Math.sin(t * 2) * 0.1;
      ring2.material.opacity = 0.2 + Math.sin(t * 2 + 1) * 0.08;

      // Particules tournent
      particlesMesh.rotation.y = t * 0.06;

      // Caméra légère réaction souris
      camera.position.x += (mx * 0.6 - camera.position.x) * 0.04;
      camera.position.y += (-my * 0.4 + 1.2 - camera.position.y) * 0.04;
      camera.lookAt(0, 1, 0);

      renderer.render(scene, camera);
    }
    animate();
    scenes.character = { renderer, scene, camera };
  }

  // ── AUTO-INIT ──
  function init() {
    if (typeof THREE === 'undefined') {
      console.warn('[KoblanThree] THREE.js non chargé');
      return;
    }
    initGlobalParticles();
    if (document.getElementById('hero-canvas'))      initHeroScene();
    // character-canvas est géré directement dans home.php pour éviter les conflits de timing
    if (document.getElementById('auth-canvas'))      initAuthScene();
    document.querySelectorAll('[data-three]').forEach(el => {
      const id = el.id;
      const type = el.dataset.three;
      const color = parseInt(el.dataset.color || '0xFFD700');
      if (id) initSectionScene(id, type, color);
    });
  }

  return { init, initSectionScene, scenes };
})();

document.addEventListener('DOMContentLoaded', () => {
  function runInit() {
    if (typeof THREE !== 'undefined') {
      KoblanThree.init();
    } else {
      setTimeout(runInit, 200);
    }
  }
  runInit();
});

