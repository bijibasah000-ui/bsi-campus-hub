<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BSI Campus Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- ============================================================
         DESIGN-ONLY OVERRIDE FOR LOGIN PAGE
         Tidak mengubah struktur form / nama field / route apa pun.
         Style ini hanya berlaku di halaman ini (dokumen HTML terpisah),
         tidak menimpa app.css yang dipakai halaman lain.
       ============================================================ --}}
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            height: auto;
            background: #0a0e1a;
            color: #e0e8f0;
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* Layer 0: Three.js spiral staircase canvas */
        .stair-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            opacity: 0.55;
            pointer-events: none;
        }
        .stair-bg canvas { display: block; }

        /* Fallback layer: pure-CSS spinning staircase + stars.
           Always visible by default. Auto fades out once the real
           Three.js WebGL scene successfully boots (see script below).
           Guarantees there is always an animation, even if the
           Three.js CDN is blocked by a network/firewall/ad-blocker. */
        .fallback-scene {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
            transition: opacity 1s ease;
        }
        .fallback-scene.is-hidden { opacity: 0; }
        .fallback-spiral {
            position: relative;
            width: 620px;
            height: 620px;
            transform-style: preserve-3d;
            animation: fbSpin 16s linear infinite;
        }
        @keyframes fbSpin {
            from { transform: rotateX(62deg) rotateZ(0deg); }
            to   { transform: rotateX(62deg) rotateZ(360deg); }
        }
        .fb-step {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 130px;
            height: 12px;
            border-radius: 4px;
            background: linear-gradient(90deg, rgba(125, 211, 252, 0.8), rgba(125, 211, 252, 0.15));
            box-shadow: 0 0 18px rgba(125, 211, 252, 0.5);
            transform-origin: 0 50%;
            transform: rotate(calc(var(--i) * 22.5deg)) translateX(210px) translateZ(calc(var(--i) * 9px));
        }
        .fb-stars { position: absolute; inset: 0; overflow: hidden; }
        .fb-star {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #7dd3fc;
            opacity: 0.2;
            animation: fbTwinkle 3.2s ease-in-out infinite;
        }
        @keyframes fbTwinkle {
            0%, 100% { opacity: 0.12; }
            50% { opacity: 0.85; }
        }

        /* Layer 1: gradient wash so the card stays readable */
        .bg-gradient-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background:
                radial-gradient(circle at 50% 18%, rgba(125, 211, 252, 0.14), transparent 60%),
                linear-gradient(180deg, rgba(10, 14, 26, 0.25) 0%, rgba(10, 14, 26, 0.88) 65%, #0a0e1a 100%);
        }

        /* Layer 2: actual login content */
        .auth-wrap {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            background: transparent;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            padding: 40px 36px;
            border-radius: 24px;
            background: rgba(15, 21, 36, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(125, 211, 252, 0.16);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.02) inset;
            animation: cardIn 0.7s ease both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(18px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-logo { text-align: center; margin-bottom: 26px; }
        .auth-logo-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            margin: 0 auto 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 45%, #0ea5c4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.35), 0 0 0 1px rgba(125, 211, 252, 0.25);
        }
        .auth-logo-icon svg { width: 28px; height: 28px; fill: #fff; }
        .auth-logo h1 { font-size: 19px; font-weight: 700; color: #fff; letter-spacing: 0.2px; margin: 0; }
        .auth-logo p { font-size: 12.5px; color: #7dd3fc; margin-top: 4px; opacity: 0.85; letter-spacing: 0.3px; }

        .auth-title { font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .auth-sub { font-size: 13px; color: #a0b4c4; margin-bottom: 26px; }

        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 12.5px; font-weight: 600; color: #a0b4c4; display: block; margin-bottom: 6px; letter-spacing: 0.2px; }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 12px;
            font-size: 14px;
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input::placeholder { color: rgba(160, 180, 196, 0.55); }
        .form-input:focus {
            border-color: #7dd3fc;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(125, 211, 252, 0.18);
        }

        .pw-field { position: relative; }
        .pw-field .form-input { padding-right: 42px; }
        .pw-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #7dd3fc;
            opacity: 0.7;
            cursor: pointer;
            padding: 4px;
            line-height: 0;
        }
        .pw-toggle:hover { opacity: 1; }
        .pw-toggle svg { width: 18px; height: 18px; }

        .remember-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .remember-row label { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: #a0b4c4; cursor: pointer; }
        .remember-row input[type="checkbox"] { accent-color: #7dd3fc; width: 14px; height: 14px; }

        .btn-primary {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #06121d;
            cursor: pointer;
            background: linear-gradient(135deg, #7dd3fc 0%, #38bdf8 100%);
            box-shadow: 0 8px 24px rgba(125, 211, 252, 0.3);
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
        }
        .btn-primary:hover { opacity: 1; transform: translateY(-1px); box-shadow: 0 10px 28px rgba(125, 211, 252, 0.42); }
        .btn-primary:active { transform: translateY(0); }

        .notice {
            border-radius: 12px;
            padding: 11px 16px;
            font-size: 12.5px;
            margin-bottom: 16px;
            border: 1px solid;
        }
        .notice.is-error { background: rgba(248, 113, 113, 0.1); border-color: rgba(248, 113, 113, 0.3); color: #fca5a5; }
        .notice.is-success { background: rgba(74, 222, 128, 0.1); border-color: rgba(74, 222, 128, 0.3); color: #86efac; }

        .auth-footer { text-align: center; margin-top: 20px; font-size: 12.5px; color: #7e8fa0; }
        .auth-footer a { color: #7dd3fc; font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        .admin-link-row { margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center; }
        .admin-link-row a {
            font-size: 11px;
            color: #5b6b7c;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .admin-link-row a:hover { color: #7dd3fc; }
        .admin-link-row svg { width: 12px; height: 12px; }

        @media (max-width: 480px) {
            .auth-card { padding: 32px 24px; border-radius: 20px; }
        }
    </style>
</head>
<body>

{{-- Background layer: spiral staircase animation (Three.js) --}}
<div class="stair-bg" id="stairBg" aria-hidden="true">
    {{-- CSS fallback: always visible until/unless the WebGL scene boots --}}
    <div class="fallback-scene" id="fallbackScene">
        <div class="fallback-spiral">
            @for ($i = 0; $i < 16; $i++)
                <span class="fb-step" style="--i:{{ $i }};"></span>
            @endfor
        </div>
        <div class="fb-stars">
            @for ($i = 0; $i < 24; $i++)
                <span class="fb-star" style="left:{{ ($i * 41) % 100 }}%; top:{{ ($i * 67) % 100 }}%; animation-delay:{{ ($i % 8) * 0.4 }}s;"></span>
            @endfor
        </div>
    </div>
</div>
<div class="bg-gradient-overlay" aria-hidden="true"></div>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 3L2 9l10 6 10-6-10-6zM2 15l10 6 10-6M2 9l10 6 10-6"/></svg>
            </div>
            <h1>BSI Campus Hub</h1>
            <p>Universitas BSI</p>
        </div>

        <div class="auth-title">Selamat datang kembali 👋</div>
        <div class="auth-sub">Masuk dengan NIM dan password kamu</div>

        @if ($errors->any())
            <div class="notice is-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="notice is-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">NIM</label>
                <input class="form-input" type="text" name="nim"
                       value="{{ old('nim') }}" placeholder="Contoh: 22410100123" autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="pw-field">
                    <input class="form-input" type="password" name="password" id="passwordInput" placeholder="••••••••">
                    <button type="button" class="pw-toggle" onclick="bchTogglePassword()" aria-label="Tampilkan password">
                        <svg id="bchEyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>
            <button type="submit" class="btn-primary">Masuk</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
        </div>

        {{-- PATCH: Link Login Administrator --}}
        <div class="admin-link-row">
            <a href="{{ route('admin.login') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Login Administrator
            </a>
        </div>
    </div>
</div>

{{-- Three.js spiral staircase background — gagal load pun tidak akan mematahkan form,
     dan fallback CSS di atas akan tetap tampil sebagai animasi pengganti --}}
<script src="https://ajax.googleapis.com/ajax/libs/threejs/r125/three.min.js"></script>
<script>
(function () {
    if (typeof THREE === 'undefined') return; // CDN gagal dimuat (offline/diblokir) -> fallback CSS tetap tampil, form tetap normal

    var container = document.getElementById('stairBg');
    if (!container) return;

    try {
        var width = window.innerWidth;
        var height = window.innerHeight;

        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera(70, width / height, 0.1, 1000);
        camera.position.set(0, 6, 22);

    var renderer = new THREE.WebGLRenderer({
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance'
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    container.appendChild(renderer.domElement);

    // WebGL berhasil dibuat -> sembunyikan fallback CSS secara halus
    var fallbackEl = document.getElementById('fallbackScene');
    if (fallbackEl) { fallbackEl.classList.add('is-hidden'); }

    var ambientLight = new THREE.AmbientLight(0xffffff, 0.45);
    scene.add(ambientLight);

    var spot1 = new THREE.SpotLight(0x7dd3fc, 2.2);
    spot1.position.set(18, 26, 12);
    scene.add(spot1);

    var spot2 = new THREE.SpotLight(0x4f46e5, 1.1);
    spot2.position.set(-16, -8, 14);
    scene.add(spot2);

    // Spiral staircase
    var staircaseGroup = new THREE.Group();
    var stepCount = 50;
    var radius = 6.5;
    var heightStep = 0.5;
    var rotationStep = 0.2;

    var stepGeometry = new THREE.BoxGeometry(4, 0.18, 1.1);
    var stepMaterial = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        shininess: 100,
        specular: 0x7dd3fc,
        transparent: true,
        opacity: 0.85
    });

    var steps = [];
    for (var i = 0; i < stepCount; i++) {
        var step = new THREE.Mesh(stepGeometry, stepMaterial.clone());
        var angle = i * rotationStep;
        step.position.x = Math.cos(angle) * radius;
        step.position.z = Math.sin(angle) * radius;
        step.position.y = i * heightStep - (stepCount * heightStep / 2);
        step.rotation.y = -angle;
        staircaseGroup.add(step);
        steps.push({ mesh: step, angle: angle });
    }
    scene.add(staircaseGroup);

    // Starfield particles
    var particlesCount = 1500;
    var posArray = new Float32Array(particlesCount * 3);
    for (var p = 0; p < particlesCount * 3; p++) {
        posArray[p] = (Math.random() - 0.5) * 70;
    }
    var particlesGeometry = new THREE.BufferGeometry();
    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    var particlesMaterial = new THREE.PointsMaterial({
        size: 0.018,
        color: 0x7dd3fc,
        transparent: true,
        opacity: 0.55,
        blending: THREE.AdditiveBlending
    });
    var particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particlesMesh);

    var mouse = { x: 0, y: 0 };
    window.addEventListener('mousemove', function (e) {
        mouse.x = (e.clientX / window.innerWidth) - 0.5;
        mouse.y = (e.clientY / window.innerHeight) - 0.5;
    });

    var clock = new THREE.Clock();

    function animate() {
        var t = clock.getElapsedTime();

        staircaseGroup.rotation.y = t * 0.12;
        particlesMesh.rotation.y = t * 0.035;

        for (var s = 0; s < steps.length; s++) {
            steps[s].mesh.scale.y = 1 + Math.sin(t * 2 + steps[s].angle) * 0.08;
        }

        var targetCamX = mouse.x * 8;
        var targetCamY = 6 + (mouse.y * 5);
        camera.position.x += (targetCamX - camera.position.x) * 0.04;
        camera.position.y += (targetCamY - camera.position.y) * 0.04;
        camera.lookAt(0, 0, 0);

        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }
    animate();

    window.addEventListener('resize', function () {
        var w = window.innerWidth;
        var h = window.innerHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
    } catch (e) {
        // WebGL gagal jalan (driver/device tidak mendukung, dll) -> fallback CSS tetap tampil
        return;
    }
})();

function bchTogglePassword() {
    var input = document.getElementById('passwordInput');
    if (input) {
        input.type = (input.type === 'password') ? 'text' : 'password';
    }
}
</script>
</body>
</html>
