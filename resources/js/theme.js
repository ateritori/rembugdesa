const COLOR_KEY = "theme-color";
const PRESET_KEY = "theme-preset";
const MODE_KEY = "theme-mode";

const PRESETS = {
    gray: "#374151",
    green: "#166534",
    blue: "#1e40af",
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
        apply(
            localStorage.getItem(COLOR_KEY),
            newMode,
            localStorage.getItem(PRESET_KEY),
        );
    };
};
