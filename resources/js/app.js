import './bootstrap';
import Alpine from 'alpinejs';

import Chart from 'chart.js/auto';

window.Alpine = Alpine;
Alpine.start();
window.Chart = Chart;

// Tambah ini untuk force hide kalau perlu
document.addEventListener('alpine:init', () => {
  // Opsional: Log atau custom logic
});