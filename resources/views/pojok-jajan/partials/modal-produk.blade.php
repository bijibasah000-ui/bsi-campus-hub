{{--
    resources/views/pojok-jajan/partials/modal-produk.blade.php
    
    PETUNJUK INTEGRASI:
    - Include partial ini di halaman pojok-jajan utama
    - Panggil openModal(produkData) dari JavaScript kartu produk
    - Tambahkan <meta name="csrf-token"> di <head>
--}}

{{-- ============================================================
     MODAL DETAIL PRODUK (dengan payment method + QR gimmick)
     ============================================================ --}}
<div id="modalProduk"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center p-4"
    onclick="handleModalBackdrop(event)">

    <div class="bg-white rounded-3xl w-full max-w-md max-h-[92vh] overflow-y-auto shadow-2xl relative"
        onclick="event.stopPropagation()">

        {{-- Loading skeleton --}}
        <div id="modalSkeleton" class="p-6 space-y-4">
            <div class="w-full h-40 bg-gray-100 rounded-2xl animate-pulse"></div>
            <div class="h-5 bg-gray-100 rounded animate-pulse w-3/4"></div>
            <div class="h-4 bg-gray-100 rounded animate-pulse w-1/2"></div>
        </div>

        {{-- Konten utama --}}
        <div id="modalContent" class="hidden">

            {{-- Gambar produk --}}
            <div class="relative">
                <img id="produkGambar" src="" alt="" class="w-full h-48 object-cover rounded-t-3xl">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-t-3xl"></div>
            </div>

            <div class="p-6 space-y-4">

                {{-- Header info --}}
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 id="produkNama" class="text-xl font-bold text-gray-800"></h2>
                        <p id="produkLapak" class="text-sm text-gray-500 mt-0.5"></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p id="produkHarga" class="text-xl font-bold text-orange-600"></p>
                        <span id="produkStatus"
                            class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-100 text-green-700 mt-1 inline-block">
                            Tersedia
                        </span>
                    </div>
                </div>

                {{-- Rating bintang --}}
                <div class="flex items-center gap-2">
                    <div id="bintangDisplay" class="flex gap-0.5"></div>
                    <span id="ratingText" class="text-sm text-gray-500"></span>
                </div>

                {{-- Deskripsi --}}
                <p id="produkDeskripsi" class="text-gray-600 text-sm leading-relaxed"></p>

                {{-- Info poin --}}
                <div class="bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 flex items-center gap-3">
                    <span class="text-xl">⭐</span>
                    <div>
                        <p class="text-amber-800 font-semibold text-sm">Kamu akan dapat 300 poin per pembelian!</p>
                        <p class="text-amber-600 text-xs">Tukarkan poin di Pojok Reward</p>
                    </div>
                </div>

                {{-- Pesan section — disembunyikan jika milik sendiri --}}
                <div id="sectionPesan">

                    {{-- Warning penjual sendiri --}}
                    <div id="warningSendiri"
                        class="hidden bg-orange-50 border border-orange-200 rounded-2xl px-4 py-3 text-sm text-orange-700">
                        🚫 Kamu tidak dapat membeli produk dari lapakmu sendiri.
                    </div>

                    {{-- Form pesan --}}
                    <div id="formPesan">

                        {{-- Jumlah --}}
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-700">Jumlah Pesanan</span>
                            <div class="flex items-center gap-3">
                                <button onclick="ubahJumlah(-1)"
                                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 transition font-bold text-gray-700 flex items-center justify-center">−</button>
                                <span id="jumlahPesanan" class="text-lg font-semibold text-gray-800 min-w-8 text-center">1</span>
                                <button onclick="ubahJumlah(1)"
                                    class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 transition font-bold text-gray-700 flex items-center justify-center">+</button>
                                <span id="totalPoin" class="text-sm text-orange-500 font-medium">= 300 poin</span>
                            </div>
                        </div>

                        {{-- STEP 1: Pilih metode pembayaran --}}
                        <div id="stepPilihMetode" class="space-y-3">
                            <p class="text-sm font-semibold text-gray-700">Pilih Metode Pembayaran</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="pilihMetode('qris')"
                                    class="metode-btn border-2 border-gray-200 rounded-2xl p-4 text-center transition hover:border-indigo-300 hover:bg-indigo-50/50"
                                    data-metode="qris">
                                    <div class="text-2xl mb-1">📱</div>
                                    <div class="font-semibold text-sm text-gray-700">QRIS</div>
                                    <div class="text-xs text-gray-400">Bayar via QR Code</div>
                                </button>
                                <button onclick="pilihMetode('tunai')"
                                    class="metode-btn border-2 border-gray-200 rounded-2xl p-4 text-center transition hover:border-indigo-300 hover:bg-indigo-50/50"
                                    data-metode="tunai">
                                    <div class="text-2xl mb-1">💵</div>
                                    <div class="font-semibold text-sm text-gray-700">Tunai</div>
                                    <div class="text-xs text-gray-400">Bayar langsung ke penjual</div>
                                </button>
                            </div>

                            {{-- Tombol pesan -- aktif setelah pilih metode --}}
                            <button id="btnPesan" onclick="prosesOrder()"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl py-3.5 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                disabled>
                                🛒 Pesan Sekarang
                            </button>
                        </div>

                        {{-- STEP 2: QR Code gimmick (muncul kalau pilih QRIS) --}}
                        <div id="stepQRIS" class="hidden space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <button onclick="balikKePilihMetode()"
                                    class="text-gray-500 hover:text-gray-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <h3 class="font-bold text-gray-800">Pembayaran QRIS</h3>
                            </div>

                            {{-- QR Code --}}
                            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center">
                                <div class="inline-block bg-white p-3 rounded-xl shadow-sm mb-3">
                                    <canvas id="qrCanvas" width="160" height="160"></canvas>
                                </div>
                                <p class="text-xs text-gray-500 mb-1">Scan QR Code dengan aplikasi m-banking atau e-wallet</p>
                                <p id="qrNominal" class="font-bold text-gray-800 text-lg"></p>
                                <p id="qrPenerima" class="text-xs text-gray-500">BSI Campus Hub — Pojok Jajan</p>
                            </div>

                            {{-- Timer --}}
                            <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-2.5 flex items-center justify-between">
                                <span class="text-sm text-orange-700 font-medium">Batas waktu pembayaran:</span>
                                <span id="qrTimer" class="font-bold text-orange-600 font-mono text-lg">05:00</span>
                            </div>

                            {{-- Info --}}
                            <div class="text-xs text-gray-500 space-y-1 bg-gray-50 rounded-xl p-3">
                                <p>📌 <strong>Nama Merchant:</strong> BSI Campus Hub</p>
                                <p id="qrLapakNama">🏪 <strong>Lapak:</strong> —</p>
                                <p id="qrProdukDetail">📦 <strong>Produk:</strong> —</p>
                            </div>

                            {{-- Tombol konfirmasi (simulasi) --}}
                            <button onclick="konfirmasiQRIS()"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold rounded-2xl py-3.5 transition flex items-center justify-center gap-2">
                                ✅ Saya Sudah Bayar
                            </button>
                        </div>

                        {{-- STEP 3: Konfirmasi tunai --}}
                        <div id="stepTunai" class="hidden space-y-4">
                            <div class="flex items-center gap-3 mb-2">
                                <button onclick="balikKePilihMetode()"
                                    class="text-gray-500 hover:text-gray-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <h3 class="font-bold text-gray-800">Pembayaran Tunai</h3>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-center">
                                <div class="text-4xl mb-3">💵</div>
                                <p class="text-blue-800 font-semibold">Siapkan uang tunai</p>
                                <p id="tunaiNominal" class="text-2xl font-bold text-blue-900 mt-1"></p>
                                <p class="text-blue-600 text-sm mt-2">Bayar langsung kepada penjual saat mengambil pesanan</p>
                            </div>

                            <button onclick="konfirmasiTunai()"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl py-3.5 transition">
                                ✅ Konfirmasi & Buat Pesanan
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Rating Section (hanya jika ada order selesai belum di-rating) --}}
                <div id="sectionRating" class="hidden border-t border-gray-100 pt-4 space-y-3">
                    <h3 class="font-semibold text-gray-700 flex items-center gap-2">
                        ⭐ Beri Rating
                    </h3>

                    {{-- Pilih order untuk di-rating --}}
                    <div id="pilihOrderRating" class="hidden">
                        <label class="text-xs text-gray-500 mb-1 block">Pilih pesanan yang ingin diberi rating:</label>
                        <select id="selectOrder" onchange="handleSelectOrder()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <option value="">— Pilih pesanan —</option>
                        </select>
                    </div>

                    <div id="formRating" class="hidden space-y-3">
                        {{-- Bintang --}}
                        <div class="flex gap-1" id="ratingInput">
                            @for($i = 1; $i <= 5; $i++)
                                <button onclick="setBintang({{ $i }})" data-nilai="{{ $i }}"
                                    class="rating-star text-3xl transition hover:scale-110"
                                    style="color: #d1d5db">☆</button>
                            @endfor
                        </div>

                        {{-- Komentar --}}
                        <textarea id="komentarRating" placeholder="Tulis ulasan (opsional)..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orange-300"
                            rows="3"></textarea>

                        <button onclick="kirimRating()"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-2xl py-3 transition">
                            Kirim Rating
                        </button>
                    </div>

                    <div id="sudahRating" class="hidden text-sm text-green-600 text-center py-2">
                        ✓ Kamu sudah memberi rating untuk pesanan yang dipilih.
                    </div>

                    <div id="belumBeli" class="text-sm text-gray-400 text-center py-2">
                        Rating hanya bisa diberikan setelah pesanan selesai.
                    </div>
                </div>

                {{-- Ulasan Pembeli --}}
                <div class="border-t border-gray-100 pt-4">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        💬 Ulasan Pembeli
                    </h3>
                    <div id="listUlasan">
                        <p class="text-sm text-gray-400 text-center py-3">Belum ada ulasan.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Tombol tutup --}}
        <button onclick="tutupModal()"
            class="absolute top-4 right-4 w-8 h-8 bg-white/90 hover:bg-white rounded-full shadow flex items-center justify-center text-gray-500 hover:text-gray-700 transition z-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Tombol Tutup bawah --}}
        <button onclick="tutupModal()"
            class="w-full text-center text-sm text-gray-400 hover:text-gray-600 py-4 border-t border-gray-100 transition">
            Tutup
        </button>

    </div>
</div>

{{-- ============================================================
     MODAL QR SCAN PAGE (full screen saat QR di-scan)
     ============================================================ --}}
<div id="modalQRScanPage"
    class="fixed inset-0 bg-indigo-950 z-[60] hidden flex items-center justify-center">
    <div class="text-center text-white p-8 max-w-sm w-full">
        <div class="text-6xl mb-4">💳</div>
        <h2 class="text-2xl font-bold mb-2">BSI Campus Hub</h2>
        <p class="text-indigo-300 text-sm mb-6">Detail Pembayaran</p>

        <div class="bg-white/10 rounded-3xl p-6 space-y-3 text-left mb-6">
            <div class="flex justify-between text-sm">
                <span class="text-indigo-300">Merchant</span>
                <span class="font-medium">BSI Campus Hub</span>
            </div>
            <div class="flex justify-between text-sm" id="scanLapak">
                <span class="text-indigo-300">Lapak</span>
                <span class="font-medium">—</span>
            </div>
            <div class="flex justify-between text-sm" id="scanProduk">
                <span class="text-indigo-300">Produk</span>
                <span class="font-medium">—</span>
            </div>
            <div class="flex justify-between text-sm" id="scanJumlah">
                <span class="text-indigo-300">Jumlah</span>
                <span class="font-medium">—</span>
            </div>
            <div class="border-t border-white/20 pt-3 flex justify-between">
                <span class="text-indigo-200 font-semibold">Total Pembayaran</span>
                <span id="scanTotal" class="font-bold text-xl text-green-400">—</span>
            </div>
        </div>

        <p class="text-indigo-400 text-xs">
            Halaman ini muncul ketika QR Code dipindai.<br>
            Selesaikan pembayaran di aplikasi e-wallet Anda.
        </p>
    </div>
</div>

{{-- ============================================================
     SUCCESS TOAST
     ============================================================ --}}
<div id="toastSuccess"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-2xl shadow-xl text-sm font-medium hidden z-[70] flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    <span id="toastMsg"></span>
</div>

{{-- ============================================================
     JAVASCRIPT
     ============================================================ --}}
<script>
// ---- State ----
let produkAktif = null;
let jumlah = 1;
let metodeAktif = null;
let bintangAktif = 0;
let orderAktif = null;
let qrTimerInterval = null;
let lapakIdUser = @json(auth()->user()?->lapak?->id ?? null); // ID lapak milik user

// ---- Buka modal ----
async function openModal(produk) {
    produkAktif = produk;
    jumlah = 1;
    metodeAktif = null;
    bintangAktif = 0;
    orderAktif = null;

    document.getElementById('modalProduk').classList.remove('hidden');
    document.getElementById('modalSkeleton').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');

    document.body.style.overflow = 'hidden';
    resetStep();

    // Populate data
    document.getElementById('produkGambar').src = produk.gambar || '/images/placeholder-food.png';
    document.getElementById('produkNama').textContent = produk.nama;
    document.getElementById('produkLapak').textContent = '🏪 ' + produk.lapak_nama;
    document.getElementById('produkHarga').textContent = 'Rp ' + produk.harga.toLocaleString('id-ID');
    document.getElementById('produkDeskripsi').textContent = produk.deskripsi || '';
    document.getElementById('jumlahPesanan').textContent = 1;
    document.getElementById('totalPoin').textContent = '= 300 poin';

    // Rating bintang
    renderBintang('bintangDisplay', produk.rating || 0, false);
    document.getElementById('ratingText').textContent =
        produk.rating ? `${produk.rating}/5` : 'Belum ada rating';

    // Cek apakah produk milik lapak sendiri
    const milikSendiri = produk.lapak_id && lapakIdUser && (produk.lapak_id == lapakIdUser);

    if (milikSendiri) {
        document.getElementById('warningSendiri').classList.remove('hidden');
        document.getElementById('formPesan').classList.add('hidden');
    } else {
        document.getElementById('warningSendiri').classList.add('hidden');
        document.getElementById('formPesan').classList.remove('hidden');
    }

    // Cek order belum rating
    await loadOrdersBelumRating(produk.id);

    // Load ulasan
    await loadUlasan(produk.id);

    document.getElementById('modalSkeleton').classList.add('hidden');
    document.getElementById('modalContent').classList.remove('hidden');
}

// ---- Rating: load orders yang bisa di-rating ----
async function loadOrdersBelumRating(produkId) {
    try {
        const res = await fetch(`/pojok-jajan/produk/${produkId}/orders-belum-rating`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        const sectionRating = document.getElementById('sectionRating');
        const belumBeli     = document.getElementById('belumBeli');
        const pilihOrder    = document.getElementById('pilihOrderRating');
        const formRating    = document.getElementById('formRating');

        sectionRating.classList.remove('hidden');

        if (data.boleh_rating && data.orders.length > 0) {
            belumBeli.classList.add('hidden');
            pilihOrder.classList.remove('hidden');

            const select = document.getElementById('selectOrder');
            select.innerHTML = '<option value="">— Pilih pesanan —</option>';
            data.orders.forEach(o => {
                const opt = document.createElement('option');
                opt.value = o.id;
                opt.textContent = `Pesanan #${o.id} • ${o.jumlah} pcs • ${new Date(o.created_at).toLocaleDateString('id-ID')}`;
                select.appendChild(opt);
            });
        } else {
            belumBeli.classList.remove('hidden');
            pilihOrder.classList.add('hidden');
            formRating.classList.add('hidden');
        }
    } catch (e) {
        console.log('Rating check error:', e);
    }
}

function handleSelectOrder() {
    const select  = document.getElementById('selectOrder');
    const form    = document.getElementById('formRating');
    orderAktif    = select.value || null;
    form.classList.toggle('hidden', !orderAktif);
    if (orderAktif) {
        setBintang(0); // reset
    }
}

// ---- Load ulasan produk ----
async function loadUlasan(produkId) {
    try {
        const res  = await fetch(`/pojok-jajan/produk/${produkId}/ulasan`);
        const data = await res.json();
        const el   = document.getElementById('listUlasan');

        if (!data.ulasan || data.ulasan.length === 0) {
            el.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Belum ada ulasan.</p>';
            return;
        }

        el.innerHTML = data.ulasan.map(u => `
            <div class="py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center text-xs font-semibold text-indigo-600">
                        ${u.user_name.charAt(0).toUpperCase()}
                    </div>
                    <span class="text-sm font-medium text-gray-700">${u.user_name}</span>
                    <div class="flex gap-0.5 ml-auto">${renderBintangHtml(u.bintang)}</div>
                </div>
                ${u.komentar ? `<p class="text-sm text-gray-500 ml-8">${u.komentar}</p>` : ''}
            </div>
        `).join('');
    } catch (e) {
        console.log('Ulasan load error:', e);
    }
}

// ---- Ubah jumlah ----
function ubahJumlah(delta) {
    jumlah = Math.max(1, jumlah + delta);
    document.getElementById('jumlahPesanan').textContent = jumlah;
    document.getElementById('totalPoin').textContent = `= ${300 * jumlah} poin`;
}

// ---- Pilih metode pembayaran ----
function pilihMetode(metode) {
    metodeAktif = metode;

    document.querySelectorAll('.metode-btn').forEach(btn => {
        btn.classList.remove('border-indigo-500', 'bg-indigo-50');
        btn.classList.add('border-gray-200');
    });

    const btn = document.querySelector(`.metode-btn[data-metode="${metode}"]`);
    btn.classList.add('border-indigo-500', 'bg-indigo-50');
    btn.classList.remove('border-gray-200');

    document.getElementById('btnPesan').disabled = false;
}

// ---- Proses order: tampilkan step sesuai metode ----
async function prosesOrder() {
    if (!metodeAktif) return;

    if (metodeAktif === 'qris') {
        tampilStepQRIS();
    } else {
        tampilStepTunai();
    }
}

// ---- STEP QRIS ----
function tampilStepQRIS() {
    document.getElementById('stepPilihMetode').classList.add('hidden');
    document.getElementById('stepQRIS').classList.remove('hidden');

    const total = produkAktif.harga * jumlah;
    document.getElementById('qrNominal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('qrLapakNama').innerHTML = `🏪 <strong>Lapak:</strong> ${produkAktif.lapak_nama}`;
    document.getElementById('qrProdukDetail').innerHTML = `📦 <strong>Produk:</strong> ${produkAktif.nama} × ${jumlah}`;

    // Gambar QR sederhana pakai canvas (pola grid gimmick)
    buatQRGimmick(total);

    // Timer 5 menit
    startQRTimer();
}

function buatQRGimmick(total) {
    const canvas = document.getElementById('qrCanvas');
    const ctx = canvas.getContext('2d');
    const size = 160;
    const cellSize = 8;
    const cells = size / cellSize;

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, size, size);

    // Seed deterministic dari total agar QR konsisten
    let seed = total;
    function rand() {
        seed = (seed * 9301 + 49297) % 233280;
        return seed / 233280;
    }

    ctx.fillStyle = '#1a1a1a';

    // Pola acak
    for (let r = 0; r < cells; r++) {
        for (let c = 0; c < cells; c++) {
            // Paksa area finder pattern di sudut (ciri khas QR)
            const inFinder = (
                (r < 7 && c < 7) ||   // kiri atas
                (r < 7 && c > cells - 8) ||  // kanan atas
                (r > cells - 8 && c < 7)     // kiri bawah
            );
            if (inFinder) {
                const localR = r < 7 ? r : r - (cells - 7);
                const localC = c < 7 ? c : c > cells - 8 ? c - (cells - 7) : c;
                const inBorder = (localR === 0 || localR === 6 || localC === 0 || localC === 6);
                const inInner = (localR >= 2 && localR <= 4 && localC >= 2 && localC <= 4);
                if (inBorder || inInner) {
                    ctx.fillRect(c * cellSize, r * cellSize, cellSize, cellSize);
                }
            } else if (rand() > 0.5) {
                ctx.fillRect(c * cellSize, r * cellSize, cellSize, cellSize);
            }
        }
    }
}

function startQRTimer() {
    clearInterval(qrTimerInterval);
    let sisa = 5 * 60;
    const el = document.getElementById('qrTimer');

    function update() {
        const m = String(Math.floor(sisa / 60)).padStart(2, '0');
        const s = String(sisa % 60).padStart(2, '0');
        el.textContent = `${m}:${s}`;
        if (sisa <= 0) {
            clearInterval(qrTimerInterval);
            el.textContent = 'Waktu Habis';
            el.classList.add('text-red-600');
        }
        sisa--;
    }
    update();
    qrTimerInterval = setInterval(update, 1000);
}

async function konfirmasiQRIS() {
    clearInterval(qrTimerInterval);
    await kirimOrder();
}

// ---- STEP TUNAI ----
function tampilStepTunai() {
    document.getElementById('stepPilihMetode').classList.add('hidden');
    document.getElementById('stepTunai').classList.remove('hidden');
    const total = produkAktif.harga * jumlah;
    document.getElementById('tunaiNominal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

async function konfirmasiTunai() {
    await kirimOrder();
}

function balikKePilihMetode() {
    clearInterval(qrTimerInterval);
    document.getElementById('stepQRIS').classList.add('hidden');
    document.getElementById('stepTunai').classList.add('hidden');
    document.getElementById('stepPilihMetode').classList.remove('hidden');
}

// ---- Kirim order ke server ----
async function kirimOrder() {
    try {
        const res = await fetch(`/pojok-jajan/produk/${produkAktif.id}/pesan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                jumlah: jumlah,
                metode_pembayaran: metodeAktif,
            }),
        });

        const data = await res.json();

        if (data.success) {
            tutupModal();
            tampilToast(data.message);
        } else {
            alert(data.message || 'Terjadi kesalahan.');
        }
    } catch (e) {
        alert('Gagal menghubungi server. Coba lagi.');
    }
}

// ---- Rating ----
function setBintang(nilai) {
    bintangAktif = nilai;
    document.querySelectorAll('.rating-star').forEach(btn => {
        const n = parseInt(btn.dataset.nilai);
        btn.style.color = n <= nilai ? '#f59e0b' : '#d1d5db';
        btn.textContent = n <= nilai ? '★' : '☆';
    });
}

async function kirimRating() {
    if (!bintangAktif || !orderAktif) {
        alert('Pilih pesanan dan beri bintang terlebih dahulu.');
        return;
    }

    try {
        const res = await fetch(`/pojok-jajan/produk/${produkAktif.id}/rating`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                bintang: bintangAktif,
                komentar: document.getElementById('komentarRating').value,
                order_id: orderAktif,
            }),
        });

        const data = await res.json();

        if (data.success) {
            document.getElementById('formRating').classList.add('hidden');
            document.getElementById('pilihOrderRating').classList.add('hidden');
            document.getElementById('belumBeli').classList.add('hidden');
            document.getElementById('sudahRating').classList.remove('hidden');
            tampilToast(data.message);
            await loadUlasan(produkAktif.id);
        } else {
            alert(data.message);
        }
    } catch (e) {
        alert('Gagal mengirim rating.');
    }
}

// ---- Utils ----
function renderBintang(elId, nilai, interactive = false) {
    const el = document.getElementById(elId);
    el.innerHTML = '';
    for (let i = 1; i <= 5; i++) {
        const span = document.createElement('span');
        span.textContent = i <= Math.round(nilai) ? '★' : '☆';
        span.style.color = i <= Math.round(nilai) ? '#f59e0b' : '#d1d5db';
        span.style.fontSize = '16px';
        el.appendChild(span);
    }
}

function renderBintangHtml(nilai) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += `<span style="color:${i <= nilai ? '#f59e0b' : '#d1d5db'};font-size:12px">${i <= nilai ? '★' : '☆'}</span>`;
    }
    return html;
}

function resetStep() {
    document.getElementById('stepPilihMetode').classList.remove('hidden');
    document.getElementById('stepQRIS').classList.add('hidden');
    document.getElementById('stepTunai').classList.add('hidden');
    document.getElementById('formRating').classList.add('hidden');
    document.getElementById('sudahRating').classList.add('hidden');
    document.querySelectorAll('.metode-btn').forEach(b => {
        b.classList.remove('border-indigo-500', 'bg-indigo-50');
        b.classList.add('border-gray-200');
    });
    if (document.getElementById('btnPesan')) {
        document.getElementById('btnPesan').disabled = true;
    }
}

function tutupModal() {
    clearInterval(qrTimerInterval);
    document.getElementById('modalProduk').classList.add('hidden');
    document.body.style.overflow = '';
}

function handleModalBackdrop(e) {
    if (e.target === document.getElementById('modalProduk')) tutupModal();
}

function tampilToast(msg) {
    const toast = document.getElementById('toastSuccess');
    document.getElementById('toastMsg').textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
}

// ESC key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') tutupModal();
});
</script>
