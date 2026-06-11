{{--
    resources/views/pojok-jajan/partials/form-buat-lapak.blade.php
    
    Form buat lapak baru dengan real-time check nama duplikat
--}}

<div id="modalBuatLapak"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-6 space-y-5" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Buka Lapak Baru</h2>
            <button onclick="document.getElementById('modalBuatLapak').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 transition">✕</button>
        </div>

        <form id="formBuatLapak" onsubmit="submitBuatLapak(event)" class="space-y-4">
            @csrf

            {{-- Nama Lapak --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lapak <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="inputNamaLapak" name="nama_toko"
                        placeholder="Contoh: Warung Segar BSI"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 pr-10 transition"
                        oninput="debounceCheckNama(this.value)">
                    <div id="namaCheckIcon" class="absolute right-3 top-3.5 hidden">
                        <svg id="iconOk" class="w-5 h-5 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <svg id="iconError" class="w-5 h-5 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <div id="iconLoading" class="w-5 h-5 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin hidden"></div>
                    </div>
                </div>
                <p id="namaFeedback" class="text-xs mt-1.5 hidden"></p>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                <select name="kategori"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    <option value="makanan">🍔 Makanan</option>
                    <option value="minuman">🥤 Minuman</option>
                    <option value="barang">📦 Barang</option>
                </select>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi (opsional)</label>
                <textarea name="deskripsi" rows="2" placeholder="Ceritakan lapakmu..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
            </div>

            <button type="submit" id="btnBuatLapak"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-2xl py-3.5 transition disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                🏪 Buka Lapak
            </button>
        </form>

    </div>
</div>

<script>
let namaCheckTimeout = null;
let namaValid = false;

function debounceCheckNama(nilai) {
    clearTimeout(namaCheckTimeout);
    const feedback = document.getElementById('namaFeedback');
    const iconOk   = document.getElementById('iconOk');
    const iconErr  = document.getElementById('iconError');
    const iconLoad = document.getElementById('iconLoading');
    const iconBox  = document.getElementById('namaCheckIcon');
    const btnBuat  = document.getElementById('btnBuatLapak');
    const input    = document.getElementById('inputNamaLapak');

    if (!nilai || nilai.trim().length < 3) {
        iconBox.classList.add('hidden');
        feedback.classList.add('hidden');
        namaValid = false;
        btnBuat.disabled = true;
        return;
    }

    // Loading state
    iconBox.classList.remove('hidden');
    iconOk.classList.add('hidden');
    iconErr.classList.add('hidden');
    iconLoad.classList.remove('hidden');
    feedback.classList.add('hidden');

    namaCheckTimeout = setTimeout(async () => {
        try {
            const res = await fetch('/pojok-jajan/check-nama-toko', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nama_toko: nilai.trim() }),
            });
            const data = await res.json();

            iconLoad.classList.add('hidden');
            feedback.classList.remove('hidden');

            if (data.available) {
                iconOk.classList.remove('hidden');
                iconErr.classList.add('hidden');
                feedback.textContent = '✓ ' + data.message;
                feedback.className = 'text-xs mt-1.5 text-green-600';
                input.classList.remove('border-red-300');
                input.classList.add('border-green-300');
                namaValid = true;
                btnBuat.disabled = false;
            } else {
                iconErr.classList.remove('hidden');
                iconOk.classList.add('hidden');
                feedback.textContent = '⚠ ' + data.message;
                feedback.className = 'text-xs mt-1.5 text-red-500';
                input.classList.add('border-red-300');
                input.classList.remove('border-green-300');
                namaValid = false;
                btnBuat.disabled = true;
            }
        } catch (e) {
            iconLoad.classList.add('hidden');
            console.error(e);
        }
    }, 500); // debounce 500ms
}

async function submitBuatLapak(e) {
    e.preventDefault();
    if (!namaValid) {
        alert('Silakan periksa nama lapak terlebih dahulu.');
        return;
    }

    const form = document.getElementById('formBuatLapak');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const res = await fetch('/pojok-jajan/lapak', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        const result = await res.json();

        if (result.success) {
            document.getElementById('modalBuatLapak').classList.add('hidden');
            alert('✓ ' + result.message);
            location.reload();
        } else {
            alert(result.message || 'Gagal membuat lapak.');
        }
    } catch (err) {
        alert('Terjadi kesalahan. Coba lagi.');
    }
}
</script>
