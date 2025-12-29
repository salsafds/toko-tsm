<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#userForm');
  if (!form) return;

  const submitButton = document.querySelector('#submitButton');
  const inputsSelector = 'input[name], select[name], textarea[name]';

  // Field yang diabaikan saat perbandingan (field server-only / tidak dianggap perubahan)
  const ignoreNames = new Set(['_token', '_method', 'id_user']);

  // Mengambil snapshot nilai form yang sudah dinormalisasi
  function serializeForm() {
    const data = {};
    const elements = form.querySelectorAll(inputsSelector);

    elements.forEach(el => {
      const name = el.name;
      if (!name || ignoreNames.has(name)) return;

      // Radio: ambil nilai yang terpilih saja
      if (el.type === 'radio') {
        if (data[name] !== undefined) return;
        const checked = form.querySelector(`input[name="${name}"]:checked`);
        data[name] = checked ? checked.value : '';
        return;
      }

      // Checkbox: simpan sebagai array nilai yang dicentang
      if (el.type === 'checkbox') {
        data[name] = form.querySelectorAll(`input[name="${name}"]:checked`).length
          ? Array.from(
              form.querySelectorAll(`input[name="${name}"]:checked`)
            ).map(i => i.value)
          : [];
        return;
      }

      // Input / select / textarea biasa
      data[name] = el.value == null ? '' : String(el.value).trim();
    });

    return data;
  }

  // Membandingkan dua snapshot form
  function isEqualSnapshot(a, b) {
    const aKeys = Object.keys(a).sort();
    const bKeys = Object.keys(b).sort();
    if (aKeys.length !== bKeys.length) return false;

    for (let i = 0; i < aKeys.length; i++) {
      if (aKeys[i] !== bKeys[i]) return false;
      const k = aKeys[i];
      const va = a[k];
      const vb = b[k];

      // Bandingkan array dan nilai biasa
      if (Array.isArray(va) || Array.isArray(vb)) {
        const arra = Array.isArray(va) ? va.slice().map(String) : [];
        const arrb = Array.isArray(vb) ? vb.slice().map(String) : [];
        if (arra.length !== arrb.length) return false;

        // Bandingkan tanpa memperhatikan urutan
        arra.sort();
        arrb.sort();
        for (let j = 0; j < arra.length; j++) {
          if (arra[j] !== arrb[j]) return false;
        }
      } else {
        if (String(va) !== String(vb)) return false;
      }
    }

    return true;
  }

  // Snapshot awal (diambil setelah nilai form terisi)
  const initialSnapshot = serializeForm();

  // Mengatur status disabled tombol submit
  function setButtonDisabledState(disabled) {
    if (!submitButton) return;
    submitButton.disabled = disabled;
    submitButton.classList.toggle('opacity-50', disabled);
    submitButton.setAttribute('aria-disabled', disabled ? 'true' : 'false');
  }

  // Mengecek apakah ada perubahan pada form
  function checkChanges() {
    const current = serializeForm();
    const sama = isEqualSnapshot(initialSnapshot, current);
    setButtonDisabledState(sama);
    return !sama;
  }

  // Pasang event listener ke semua field form
  const watched = form.querySelectorAll(inputsSelector);
  watched.forEach(el => {
    if (!el.name || ignoreNames.has(el.name)) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  // Jalankan sekali saat awal
  checkChanges();

  // Cegah submit jika tidak ada perubahan atau ada error validasi
  form.addEventListener('submit', function (e) {

    // Jika tombol disabled (tidak ada perubahan), hentikan submit
    if (submitButton && submitButton.disabled) {
      e.preventDefault();
      alert('Tidak ada perubahan yang perlu disimpan.');
      return false;
    }

    // Validasi client-side: username tidak boleh mengandung spasi
    const usernameInput = document.querySelector('#username_input');
    const username = usernameInput.value.trim();
    const usernameError = document.querySelector('#username_error');

    // Reset pesan error username
    usernameError.textContent = '';
    usernameError.classList.add('hidden');
    usernameInput.classList.remove('border-red-500', 'bg-red-50');

    let hasError = false;

    // Cek spasi pada username
    if (username.includes(' ')) {
      usernameError.textContent = 'Username tidak boleh mengandung spasi.';
      usernameError.classList.remove('hidden');
      usernameInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    // Jika ada error, batalkan submit
    if (hasError) {
      e.preventDefault();
      return false;
    }

    // Jika lolos, submit dilanjutkan ke server
  });
});
</script>
