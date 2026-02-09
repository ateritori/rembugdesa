const COLOR_KEY = "theme-color";
const PRESET_KEY = "theme-preset";
const MODE_KEY = "theme-mode";

// Nilai HEX di sini disamakan dengan warna di app.css
const PRESETS = {
    gray: "#4b5563",
    green: "#15803d",
    blue: "#2563eb",
    brown: "#78350f",
};

export const initTheme = () => {
    const root = document.documentElement;

    // Ambil data dari storage
    const savedColor = localStorage.getItem(COLOR_KEY) || PRESETS.blue;
    const savedPreset = localStorage.getItem(PRESET_KEY) || "blue";
    const savedMode = localStorage.getItem(MODE_KEY) || "light";

    // Fungsi Apply (Internal)
    const apply = (color, mode, preset) => {
        // Mengubah variabel --primary agar sinkron ke seluruh CSS
        root.style.setProperty("--primary", color);
        root.setAttribute("data-theme-preset", preset);

        if (mode === "dark") {
            root.setAttribute("data-theme", "dark");
            root.classList.add("dark");
        } else {
            root.removeAttribute("data-theme");
            root.classList.remove("dark");
        }
    };

    // Jalankan sekali saat load
    apply(savedColor, savedMode, savedPreset);

    // Daftarkan ke Window supaya tombol di HTML bisa manggil
    window.setThemePreset = (name) => {
        if (PRESETS[name]) {
            localStorage.setItem(COLOR_KEY, PRESETS[name]);
            localStorage.setItem(PRESET_KEY, name);
            apply(
                PRESETS[name],
                localStorage.getItem(MODE_KEY) || "light",
                name,
            );
        }
    };

    window.toggleThemeMode = () => {
        const isDark = root.classList.contains("dark");
        const newMode = isDark ? "light" : "dark";
        localStorage.setItem(MODE_KEY, newMode);

        // Mengambil warna terakhir agar tidak reset ke biru saat toggle mode
        const currentColor = localStorage.getItem(COLOR_KEY) || PRESETS.blue;
        const currentPreset = localStorage.getItem(PRESET_KEY) || "blue";

        apply(currentColor, newMode, currentPreset);
    };
};
