<?php
?>

<div id="accessibility-panel">
    <div class="acc-header">
        <div class="acc-header-left">
            <div class="acc-header-icon">
                <i class="fa-solid fa-universal-access"></i>
            </div>
            <div>
                <div class="acc-header-title">Aksesibilitas</div>
                <div class="acc-header-sub">Sesuaikan tampilan</div>
            </div>
        </div>
        <button id="close-accessibility" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="acc-body">

        <div class="acc-label">Ukuran Teks</div>
        <div class="acc-font-row">
            <button id="font-decrease" class="acc-font-btn" title="Perkecil">
                <i class="fa-solid fa-minus"></i>
            </button>
            <div class="acc-font-val">
                <span id="font-size-value">100</span>
                <small>%</small>
            </div>
            <button id="font-increase" class="acc-font-btn" title="Perbesar">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>

        <div class="acc-label">Tampilan</div>
        <div class="acc-grid">
            <button class="acc-tb" id="high-contrast" data-key="contrast" data-class="high-contrast">
                <i class="fa-solid fa-circle-half-stroke"></i>
                <span>Kontras Tinggi</span>
            </button>
            <button class="acc-tb" id="dark-mode" data-key="darkMode" data-class="dark-mode">
                <i class="fa-solid fa-moon"></i>
                <span>Tema Gelap</span>
            </button>
            <button class="acc-tb" id="underline-links" data-key="underline" data-class="underline-links">
                <i class="fa-solid fa-underline"></i>
                <span>Garis Bawah Link</span>
            </button>
            <button class="acc-tb" id="text-to-speech">
                <i class="fa-solid fa-volume-high"></i>
                <span id="tts-label">Baca Teks</span>
            </button>
        </div>

        <div class="acc-label">Tips Penggunaan</div>
        <div class="acc-tips">
            <div class="acc-tip-item" id="tip-prev">
                <div class="acc-tip-icon">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <div class="acc-tip-text">
                    <div class="acc-tip-title" id="tip-title">Gunakan Kontras Tinggi</div>
                    <div class="acc-tip-desc" id="tip-desc">Aktifkan kontras tinggi jika teks terasa sulit dibaca di layar terang.</div>
                </div>
            </div>
            <div class="acc-tip-nav">
                <button class="acc-tip-btn" id="tip-prev-btn" title="Tips sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <span class="acc-tip-counter"><span id="tip-current">1</span> / <span id="tip-total">5</span></span>
                <button class="acc-tip-btn" id="tip-next-btn" title="Tips berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <hr class="acc-divider">

        <button class="acc-reset-btn" id="reset-accessibility">
            <i class="fa-solid fa-rotate-right"></i>
            Reset Semua Pengaturan
        </button>

    </div>
</div>

<style>
#accessibility-panel {
    position: fixed;
    top: 0;
    right: -320px;
    width: 290px;
    height: 100%;
    background: #ffffff;
    box-shadow: -6px 0 28px rgba(0,0,0,0.13);
    z-index: 9999;
    transition: right 0.3s cubic-bezier(.4,0,.2,1);
    font-family: 'Poppins', sans-serif;
    display: flex;
    flex-direction: column;
}
#accessibility-panel.active { right: 0; }

.acc-header {
    background: #1565a7;
    padding: 18px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.acc-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.acc-header-icon {
    width: 34px;
    height: 34px;
    background: rgba(255,255,255,0.18);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #fff;
}
.acc-header-title {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    line-height: 1.2;
}
.acc-header-sub {
    color: rgba(255,255,255,0.6);
    font-size: 11px;
    margin-top: 1px;
}
.acc-header > button {
    width: 28px;
    height: 28px;
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.acc-header > button:hover { background: rgba(255,255,255,0.3); }

.acc-body {
    padding: 18px 16px;
    overflow-y: auto;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.acc-label {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    color: #94a3b8;
    margin-bottom: -2px;
}

/* Font control */
.acc-font-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e8edf2;
    border-radius: 10px;
    padding: 8px 12px;
}
.acc-font-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #dde3ec;
    background: #fff;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.15s;
    flex-shrink: 0;
}
.acc-font-btn:hover { background: #1565a7; color: #fff; border-color: #1565a7; }
.acc-font-val {
    flex: 1;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    color: #1565a7;
    line-height: 1;
}
.acc-font-val small { font-size: 11px; font-weight: 400; color: #94a3b8; }

/* Toggle grid */
.acc-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.acc-tb {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 14px 6px;
    background: #f8fafc;
    border: 1.5px solid #e8edf2;
    border-radius: 12px;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 500;
    color: #475569;
    transition: 0.15s;
    line-height: 1.3;
    text-align: center;
}
.acc-tb i { font-size: 18px; color: #64748b; transition: 0.15s; }
.acc-tb:hover { background: #eff6ff; border-color: #93c5fd; color: #1565a7; }
.acc-tb:hover i { color: #1565a7; }
.acc-tb.is-active { background: #1565a7; border-color: #1565a7; color: #fff; }
.acc-tb.is-active i { color: #fff; }
.acc-tb#text-to-speech.is-active { background: #dc2626; border-color: #dc2626; }

/* Tips */
.acc-tips {
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 12px;
}
.acc-tip-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    min-height: 56px;
}
.acc-tip-icon {
    width: 30px;
    height: 30px;
    background: #1565a7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: #fff;
    flex-shrink: 0;
    margin-top: 1px;
}
.acc-tip-title {
    font-size: 12px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 3px;
    line-height: 1.3;
}
.acc-tip-desc {
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
}
.acc-tip-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid #bfdbfe;
}
.acc-tip-btn {
    width: 26px;
    height: 26px;
    background: #fff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    cursor: pointer;
    font-size: 11px;
    color: #1565a7;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.15s;
}
.acc-tip-btn:hover { background: #1565a7; color: #fff; border-color: #1565a7; }
.acc-tip-counter {
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
}

/* Divider */
.acc-divider {
    border: none;
    border-top: 1px solid #f0f4f8;
    margin: 4px 0 0;
}

/* Reset */
.acc-reset-btn {
    width: 100%;
    padding: 12px;
    background: #fff5f5;
    border: 1.5px solid #fecaca;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #dc2626;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    transition: 0.15s;
    margin-top: auto;
}
.acc-reset-btn:hover { background: #fee2e2; border-color: #f87171; }

/* Dark mode overrides */
.dark-mode #accessibility-panel { background: #1a1a2e; }
.dark-mode .acc-font-row,
.dark-mode .acc-tb { background: #252540; border-color: #333358; color: #cbd5e1; }
.dark-mode .acc-font-btn { background: #2e2e50; border-color: #444466; color: #cbd5e1; }
.dark-mode .acc-font-val { color: #7eb8e8; }
.dark-mode .acc-label { color: #555577; }
.dark-mode .acc-tb i { color: #7eb8e8; }
.dark-mode .acc-tips { background: #1e2a3a; border-color: #2d4a6a; }
.dark-mode .acc-tip-title { color: #7eb8e8; }
.dark-mode .acc-tip-desc { color: #94a3b8; }
.dark-mode .acc-tip-nav { border-color: #2d4a6a; }
.dark-mode .acc-tip-btn { background: #252540; border-color: #2d4a6a; color: #7eb8e8; }
.dark-mode .acc-tip-counter { color: #64748b; }
.dark-mode .acc-divider { border-color: #2a2a45; }
.dark-mode .acc-reset-btn { background: #2a1a1a; border-color: #7f2020; color: #f87171; }
.dark-mode .acc-reset-btn:hover { background: #3a1a1a; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const panel    = document.getElementById("accessibility-panel");
    const openBtn  = document.getElementById("accessibility-btn");
    const closeBtn = document.getElementById("close-accessibility");
    const html     = document.documentElement;

    if (openBtn) openBtn.addEventListener("click", (e) => { e.preventDefault(); panel.classList.toggle("active"); });
    closeBtn.addEventListener("click", () => panel.classList.remove("active"));

    // Ukuran teks
    let fontSize = parseInt(localStorage.getItem("fontSize") || 100);
    const applyFontSize = (size) => {
        fontSize = Math.min(200, Math.max(70, size));
        html.style.fontSize = fontSize + "%";
        document.getElementById("font-size-value").innerText = fontSize;
        localStorage.setItem("fontSize", fontSize);
    };
    applyFontSize(fontSize);
    document.getElementById("font-increase").addEventListener("click", () => applyFontSize(fontSize + 10));
    document.getElementById("font-decrease").addEventListener("click", () => applyFontSize(fontSize - 10));

    // Toggle class buttons
    document.querySelectorAll(".acc-tb[data-class]").forEach(btn => {
        const cls = btn.dataset.class;
        const key = btn.dataset.key;
        if (localStorage.getItem(key) === "true") {
            document.body.classList.add(cls);
            btn.classList.add("is-active");
        }
        btn.addEventListener("click", () => {
            document.body.classList.toggle(cls);
            const active = document.body.classList.contains(cls);
            btn.classList.toggle("is-active", active);
            localStorage.setItem(key, active);
        });
    });

    // Text to Speech
    let isReading = false;
    const ttsBtn   = document.getElementById("text-to-speech");
    const ttsLabel = document.getElementById("tts-label");
    ttsBtn.addEventListener("click", function () {
        if (!isReading) {
            const utterance = new SpeechSynthesisUtterance(document.body.innerText);
            utterance.lang = "id-ID";
            utterance.onend = () => {
                isReading = false;
                ttsLabel.innerText = "Baca Teks";
                ttsBtn.classList.remove("is-active");
            };
            speechSynthesis.speak(utterance);
            isReading = true;
            ttsLabel.innerText = "Hentikan";
            ttsBtn.classList.add("is-active");
        } else {
            speechSynthesis.cancel();
            isReading = false;
            ttsLabel.innerText = "Baca Teks";
            ttsBtn.classList.remove("is-active");
        }
    });

    // Tips rotasi
    const tips = [
        {
            icon: "fa-circle-half-stroke",
            title: "Gunakan Kontras Tinggi",
            desc: "Aktifkan kontras tinggi jika teks terasa sulit dibaca di layar terang."
        },
        {
            icon: "fa-moon",
            title: "Coba Tema Gelap",
            desc: "Tema gelap mengurangi kelelahan mata saat membaca di malam hari."
        },
        {
            icon: "fa-text-height",
            title: "Perbesar Ukuran Teks",
            desc: "Naikkan ukuran teks hingga 150% agar lebih nyaman dibaca tanpa alat bantu."
        },
        {
            icon: "fa-volume-high",
            title: "Fitur Baca Teks",
            desc: "Aktifkan 'Baca Teks' agar browser membacakan seluruh konten halaman dengan suara."
        },
        {
            icon: "fa-underline",
            title: "Garis Bawah pada Link",
            desc: "Aktifkan opsi ini agar semua tautan lebih mudah dikenali di halaman."
        }
    ];

    let tipIndex = 0;
    const tipTitle   = document.getElementById("tip-title");
    const tipDesc    = document.getElementById("tip-desc");
    const tipIcon    = document.querySelector(".acc-tip-icon i");
    const tipCurrent = document.getElementById("tip-current");
    document.getElementById("tip-total").innerText = tips.length;

    const showTip = (i) => {
        tipTitle.innerText   = tips[i].title;
        tipDesc.innerText    = tips[i].desc;
        tipIcon.className    = "fa-solid " + tips[i].icon;
        tipCurrent.innerText = i + 1;
    };

    document.getElementById("tip-next-btn").addEventListener("click", () => {
        tipIndex = (tipIndex + 1) % tips.length;
        showTip(tipIndex);
    });
    document.getElementById("tip-prev-btn").addEventListener("click", () => {
        tipIndex = (tipIndex - 1 + tips.length) % tips.length;
        showTip(tipIndex);
    });

    // Auto rotate tips setiap 5 detik
    setInterval(() => {
        tipIndex = (tipIndex + 1) % tips.length;
        showTip(tipIndex);
    }, 5000);

    // Reset semua
    document.getElementById("reset-accessibility").addEventListener("click", () => {
        localStorage.clear();
        speechSynthesis.cancel();
        location.reload();
    });
});
</script>