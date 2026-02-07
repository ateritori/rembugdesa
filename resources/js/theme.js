(function () {
    const COLOR_KEY = "theme-color";
    const MODE_KEY = "theme-mode";

    const PRESETS = {
        gray: "#374151",
        green: "#166534",
        blue: "#1e40af",
        brown: "#78350f",
    };

    function applyColor(color) {
        document.documentElement.style.setProperty("--primary", color);
    }

    function applyMode(mode) {
        if (mode === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        } else {
            document.documentElement.removeAttribute("data-theme");
        }
    }

    // ===== INIT =====
    const savedColor = localStorage.getItem(COLOR_KEY);
    const savedMode = localStorage.getItem(MODE_KEY);

    // default color = gray
    applyColor(PRESETS.gray);

    if (savedColor) applyColor(savedColor);
    if (savedMode) applyMode(savedMode);

    // ===== GLOBAL API =====
    window.setThemePreset = function (name) {
        if (PRESETS[name]) {
            localStorage.setItem(COLOR_KEY, PRESETS[name]);
            applyColor(PRESETS[name]);

            // close palette after select
            const palette = document.getElementById("theme-palette");
            if (palette) palette.classList.add("hidden");
        }
    };

    window.toggleThemeMode = function () {
        const isDark = document.documentElement.hasAttribute("data-theme");
        const mode = isDark ? "light" : "dark";
        localStorage.setItem(MODE_KEY, mode);
        applyMode(mode);
    };

    window.toggleSidebar = function () {
        document.documentElement.classList.toggle("sidebar-open");
    };

    // ===== AUTO CLOSE PALETTE ON OUTSIDE CLICK =====
    document.addEventListener("click", function (e) {
        const palette = document.getElementById("theme-palette");
        if (!palette) return;

        const toggleBtn = e.target.closest('[title="Theme"]');
        const insidePalette = e.target.closest("#theme-palette");

        if (!insidePalette && !toggleBtn) {
            palette.classList.add("hidden");
        }
    });
})();
