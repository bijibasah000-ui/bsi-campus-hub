/* ============================================================
   BSI Campus Hub — konseling.js
   AI Chat via Gemini API (backend Laravel)
   ============================================================ */

(function () {
    var input = document.getElementById("chatInput");
    var box = document.getElementById("chatMessages");
    var btn = document.getElementById("chatSend");
    var typing = document.getElementById("typingIndicator");
    if (!input || !box || !btn) return;

    // Simpan riwayat percakapan untuk konteks AI (dikirim ke backend)
    var history = [];
    var isSending = false;

    /* ── Tambahkan bubble pesan ke UI ── */
    function appendMsg(text, type) {
        var isUser = type === "user";
        var avStyle = isUser
            ? "background:#4F46E5; color:white;"
            : "background:linear-gradient(135deg,#4F46E5,#7C3AED); color:white;";
        var avContent = isUser ? "Kamu" : "🧑‍💼";

        var div = document.createElement("div");
        div.className = "msg " + type;
        div.innerHTML =
            '<div class="msg-av" style="' +
            avStyle +
            " width:30px;height:30px;border-radius:50%;" +
            'display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;">' +
            avContent +
            "</div>" +
            '<div class="bubble">' +
            escapeHtml(text) +
            "</div>";

        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
    }

    /* ── Escape HTML untuk keamanan ── */
    function escapeHtml(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    /* ── Tampilkan / sembunyikan typing indicator ── */
    function showTyping(show) {
        if (typing) {
            typing.style.display = show ? "block" : "none";
            if (show) box.scrollTop = box.scrollHeight;
        }
    }

    /* ── Set tombol kirim aktif/nonaktif ── */
    function setLoading(loading) {
        isSending = loading;
        btn.disabled = loading;
        input.disabled = loading;
        btn.style.opacity = loading ? "0.6" : "1";
    }

    /* ── Kirim pesan ── */
    function send() {
        var msg = input.value.trim();
        if (!msg || isSending) return;

        // Tampilkan pesan user
        appendMsg(msg, "user");
        input.value = "";

        // Simpan ke history
        history.push({ role: "user", text: msg });

        // Tampilkan animasi mengetik
        showTyping(true);
        setLoading(true);

        // Kirim ke backend Laravel → Gemini API
        fetch(CHAT_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
            body: JSON.stringify({
                message: msg,
                history: history.slice(-10), // kirim max 10 pesan terakhir sebagai konteks
            }),
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
                return res.json();
            })
            .then(function (result) {
                showTyping(false);
                setLoading(false);

                var reply =
                    result.data.reply ||
                    "Maaf, aku tidak bisa memproses pesanmu. Coba lagi ya 😊";
                appendMsg(reply, "bot");

                // Simpan balasan AI ke history
                history.push({ role: "model", text: reply });

                // Batasi history agar tidak terlalu panjang
                if (history.length > 20) history = history.slice(-20);
            })
            .catch(function (err) {
                showTyping(false);
                setLoading(false);
                appendMsg("Error: " + err.message, "bot");
                console.error("Chat error:", err);
            });
    }

    /* ── Event listeners ── */
    btn.addEventListener("click", send);
    input.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            send();
        }
    });
})();
