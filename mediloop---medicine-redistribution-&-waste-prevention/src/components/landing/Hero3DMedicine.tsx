import React, { useEffect, useRef } from 'react';
import * as THREE from 'three';
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';

interface Hero3DMedicineProps {
  onFindMatchClick?: () => void;
}

export const Hero3DMedicine: React.FC<Hero3DMedicineProps> = ({ onFindMatchClick }) => {
  const mountRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const container = mountRef.current;
    if (!container) return;

    const width = container.clientWidth || 580;
    const height = container.clientHeight || 520;

    // Scene & Camera
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(38, width / height, 0.1, 100);
    camera.position.set(0, 0, 7.8);

    // Renderer
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    container.appendChild(renderer.domElement);

    // Studio Environment lighting
    const pmremGenerator = new THREE.PMREMGenerator(renderer);
    scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
    pmremGenerator.dispose();

    // Studio Lights
    const keyLight = new THREE.DirectionalLight(0xffffff, 2.2);
    keyLight.position.set(4, 5, 5);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 1024;
    keyLight.shadow.mapSize.height = 1024;
    keyLight.shadow.bias = -0.0005;
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0x93c5fd, 1.2);
    fillLight.position.set(-5, 2, 3);
    scene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0x60a5fa, 1.6);
    rimLight.position.set(0, -4, -3);
    scene.add(rimLight);

    const hemiLight = new THREE.HemisphereLight(0xffffff, 0xdbeafe, 0.6);
    scene.add(hemiLight);

    // --- PROCEDURAL 3D MEDICINE MODELS ---
    const medicineGroup = new THREE.Group();
    scene.add(medicineGroup);

    // 1. Primary Big Capsule (Two-Tone Blue & Glassy Pearl White)
    const capsuleGroup = new THREE.Group();
    capsuleGroup.position.set(0.7, 0.4, 0.2);
    capsuleGroup.rotation.set(0.6, 0.4, 0.7);

    // Blue half capsule
    const capsuleTopGeo = new THREE.CylinderGeometry(0.55, 0.55, 0.9, 36, 1, true);
    const topCapGeo = new THREE.SphereGeometry(0.55, 36, 20, 0, Math.PI * 2, 0, Math.PI / 2);
    
    // Glossy Royal Blue Medical Material
    const blueCapMat = new THREE.MeshPhysicalMaterial({
      color: 0x1d4ed8,
      roughness: 0.12,
      metalness: 0.08,
      clearcoat: 1.0,
      clearcoatRoughness: 0.1,
      reflectivity: 0.9,
    });

    const topCylinder = new THREE.Mesh(capsuleTopGeo, blueCapMat);
    topCylinder.position.y = 0.45;
    topCylinder.castShadow = true;

    const topCap = new THREE.Mesh(topCapGeo, blueCapMat);
    topCap.position.y = 0.9;
    topCap.castShadow = true;

    // White / Translucent half capsule
    const bottomCapMat = new THREE.MeshPhysicalMaterial({
      color: 0xf8fafc,
      roughness: 0.18,
      metalness: 0.05,
      clearcoat: 0.9,
      transmission: 0.2, // Subtle translucent gel look
      thickness: 0.5,
    });

    const bottomCylinder = new THREE.Mesh(capsuleTopGeo, bottomCapMat);
    bottomCylinder.position.y = -0.45;
    bottomCylinder.rotation.x = Math.PI;
    bottomCylinder.castShadow = true;

    const bottomCap = new THREE.Mesh(topCapGeo, bottomCapMat);
    bottomCap.position.y = -0.9;
    bottomCap.rotation.x = Math.PI;
    bottomCap.castShadow = true;

    // Joint ring band
    const ringGeo = new THREE.TorusGeometry(0.555, 0.035, 16, 48);
    const ringMat = new THREE.MeshStandardMaterial({
      color: 0x2563eb,
      roughness: 0.2,
      metalness: 0.4,
    });
    const ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI / 2;

    capsuleGroup.add(topCylinder, topCap, bottomCylinder, bottomCap, ring);
    medicineGroup.add(capsuleGroup);

    // 2. Secondary Scored Medical Tablet (Sky Blue / Cyan scored pill)
    const tabletGroup = new THREE.Group();
    tabletGroup.position.set(-1.8, -0.6, -0.2);
    tabletGroup.rotation.set(0.9, -0.5, 0.3);

    const tabletCylinderGeo = new THREE.CylinderGeometry(0.75, 0.75, 0.28, 48);
    const tabletMat = new THREE.MeshPhysicalMaterial({
      color: 0x38bdf8,
      roughness: 0.25,
      metalness: 0.05,
      clearcoat: 0.8,
    });
    const tabletMesh = new THREE.Mesh(tabletCylinderGeo, tabletMat);
    tabletMesh.castShadow = true;

    // Tablet score groove line (engraved center line)
    const grooveGeo = new THREE.BoxGeometry(1.4, 0.04, 0.05);
    const grooveMat = new THREE.MeshStandardMaterial({
      color: 0x0284c7,
      roughness: 0.4,
    });
    const groove = new THREE.Mesh(grooveGeo, grooveMat);
    groove.position.y = 0.142;
    tabletGroup.add(tabletMesh, groove);
    medicineGroup.add(tabletGroup);

    // 3. Third Pill: Orange/Amber Risk Capsule (symbolizing near-expiry batch)
    const riskCapsuleGroup = new THREE.Group();
    riskCapsuleGroup.position.set(1.4, -1.5, 0.5);
    riskCapsuleGroup.rotation.set(-0.4, 0.3, -0.8);
    riskCapsuleGroup.scale.set(0.65, 0.65, 0.65);

    const amberMat = new THREE.MeshPhysicalMaterial({
      color: 0xf97316,
      roughness: 0.15,
      clearcoat: 1.0,
      transmission: 0.25,
    });
    const whiteMat = new THREE.MeshPhysicalMaterial({
      color: 0xffffff,
      roughness: 0.18,
      clearcoat: 0.9,
    });

    const amberTopCyl = new THREE.Mesh(capsuleTopGeo, amberMat);
    amberTopCyl.position.y = 0.45;
    const amberTopCap = new THREE.Mesh(topCapGeo, amberMat);
    amberTopCap.position.y = 0.9;

    const amberBotCyl = new THREE.Mesh(capsuleTopGeo, whiteMat);
    amberBotCyl.position.y = -0.45;
    amberBotCyl.rotation.x = Math.PI;
    const amberBotCap = new THREE.Mesh(topCapGeo, whiteMat);
    amberBotCap.position.y = -0.9;
    amberBotCap.rotation.x = Math.PI;

    riskCapsuleGroup.add(amberTopCyl, amberTopCap, amberBotCyl, amberBotCap);
    medicineGroup.add(riskCapsuleGroup);

    // 4. Floating Micro-spheres (Pill beads / micro particles)
    const particlesCount = 14;
    const particleGeo = new THREE.SphereGeometry(0.08, 16, 16);
    const particleMat = new THREE.MeshPhysicalMaterial({
      color: 0x60a5fa,
      roughness: 0.1,
      clearcoat: 1.0,
      transmission: 0.4,
    });

    const particleMeshes: THREE.Mesh[] = [];
    for (let i = 0; i < particlesCount; i++) {
      const p = new THREE.Mesh(particleGeo, particleMat);
      p.position.set(
        (Math.random() - 0.5) * 5.5,
        (Math.random() - 0.5) * 4.5,
        (Math.random() - 0.5) * 2.5
      );
      const scale = 0.4 + Math.random() * 0.8;
      p.scale.set(scale, scale, scale);
      medicineGroup.add(p);
      particleMeshes.push(p);
    }

    // Contact Soft Shadow Plane
    const shadowGeo = new THREE.PlaneGeometry(6, 6);
    const shadowCanvas = document.createElement('canvas');
    shadowCanvas.width = 128;
    shadowCanvas.height = 128;
    const ctx = shadowCanvas.getContext('2d')!;
    const grad = ctx.createRadialGradient(64, 64, 10, 64, 64, 60);
    grad.addColorStop(0, 'rgba(11, 31, 75, 0.18)');
    grad.addColorStop(1, 'rgba(255, 255, 255, 0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 128, 128);

    const shadowTex = new THREE.CanvasTexture(shadowCanvas);
    const shadowMat = new THREE.MeshBasicMaterial({
      map: shadowTex,
      transparent: true,
      depthWrite: false,
    });
    const shadowPlane = new THREE.Mesh(shadowGeo, shadowMat);
    shadowPlane.rotation.x = -Math.PI / 2;
    shadowPlane.position.y = -2.2;
    scene.add(shadowPlane);

    // Mouse interactive tilt state
    let targetRotX = 0;
    let targetRotY = 0;
    let currentRotX = 0;
    let currentRotY = 0;

    const onPointerMove = (e: MouseEvent) => {
      const rect = container.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
      const y = -(((e.clientY - rect.top) / rect.height) * 2 - 1);
      targetRotY = x * 0.45;
      targetRotX = -y * 0.35;
    };

    window.addEventListener('mousemove', onPointerMove);

    // Animation Loop
    let animationFrameId: number;
    let clock = new THREE.Clock();

    const animate = () => {
      animationFrameId = requestAnimationFrame(animate);
      const elapsedTime = clock.getElapsedTime();
      const delta = clock.getDelta();

      // Smooth damp tilt
      currentRotX = THREE.MathUtils.damp(currentRotX, targetRotX, 4, 0.016);
      currentRotY = THREE.MathUtils.damp(currentRotY, targetRotY, 4, 0.016);

      medicineGroup.rotation.x = currentRotX;
      medicineGroup.rotation.y = currentRotY;

      // Gentle floating physics
      capsuleGroup.position.y = 0.4 + Math.sin(elapsedTime * 1.4) * 0.12;
      capsuleGroup.rotation.z = 0.7 + Math.sin(elapsedTime * 0.8) * 0.08;
      capsuleGroup.rotation.y = 0.4 + Math.cos(elapsedTime * 0.6) * 0.15;

      tabletGroup.position.y = -0.6 + Math.cos(elapsedTime * 1.2) * 0.1;
      tabletGroup.rotation.x = 0.9 + Math.sin(elapsedTime * 1.0) * 0.06;

      riskCapsuleGroup.position.y = -1.5 + Math.sin(elapsedTime * 1.6 + 1) * 0.1;
      riskCapsuleGroup.rotation.y = 0.3 + Math.sin(elapsedTime * 0.9) * 0.2;

      // Subtle particle float
      particleMeshes.forEach((p, idx) => {
        p.position.y += Math.sin(elapsedTime * 1.2 + idx) * 0.002;
      });

      renderer.render(scene, camera);
    };

    animate();

    const handleResize = () => {
      if (!container) return;
      const w = container.clientWidth;
      const h = container.clientHeight;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    };

    window.addEventListener('resize', handleResize);

    return () => {
      cancelAnimationFrame(animationFrameId);
      window.removeEventListener('mousemove', onPointerMove);
      window.removeEventListener('resize', handleResize);
      if (container.contains(renderer.domElement)) {
        container.removeChild(renderer.domElement);
      }
      renderer.dispose();
      capsuleTopGeo.dispose();
      topCapGeo.dispose();
      ringGeo.dispose();
      tabletCylinderGeo.dispose();
      grooveGeo.dispose();
      particleGeo.dispose();
      shadowGeo.dispose();
      shadowTex.dispose();
    };
  }, []);

  return (
    <div className="relative w-full h-[460px] md:h-[540px] flex items-center justify-center select-none overflow-visible">
      {/* Three.js 3D Canvas Mount */}
      <div
        ref={mountRef}
        className="w-full h-full cursor-grab active:cursor-grabbing"
        title="Drag cursor to interactively rotate 3D medicine models"
      />
    </div>
  );
};
