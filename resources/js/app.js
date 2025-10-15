import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Tambah ini untuk force hide kalau perlu
document.addEventListener('alpine:init', () => {
  // Opsional: Log atau custom logic
});