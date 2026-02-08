import "./bootstrap";
import "../css/app.css";
import { initTheme } from "./theme";

// Jalankan tema secepat mungkin
initTheme();

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
