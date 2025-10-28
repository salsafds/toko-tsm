@extends('layouts.appmaster')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data User</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-user._form', [
      'action' => route('master.data-user.update', $user->id_user),
      'method' => 'PUT',
      'user' => $user,
      'roles' => $roles,
      'jabatans' => $jabatans,
      'pendidikans' => $pendidikans,
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#userForm');
  if (!form) return;

  const submitButton = document.querySelector('#submitButton');
  const inputsSelector = 'input[name], select[name], textarea[name]';

  // Fields we want to ignore when comparing (server-only, readonly or fields we don't consider)
  const ignoreNames = new Set(['_token', '_method', 'id_user']);

  // collect a normalized snapshot of form values
  function serializeForm() {
    const data = {};
    const elements = form.querySelectorAll(inputsSelector);
    elements.forEach(el => {
      const name = el.name;
      if (!name || ignoreNames.has(name)) return;

      // radios: group by name, use checked value (only once)
      if (el.type === 'radio') {
        if (data[name] !== undefined) return; // already handled by checked radio
        const checked = form.querySelector(`input[name="${name}"]:checked`);
        data[name] = checked ? checked.value : '';
        return;
      }

      // checkboxes: store array of checked values
      if (el.type === 'checkbox') {
        data[name] = form.querySelectorAll(`input[name="${name}"]:checked`).length
          ? Array.from(form.querySelectorAll(`input[name="${name}"]:checked`)).map(i => i.value)
          : [];
        return;
      }

      // normal inputs/select/textarea
      data[name] = el.value == null ? '' : String(el.value).trim();
    });
    return data;
  }

  function isEqualSnapshot(a, b) {
    const aKeys = Object.keys(a).sort();
    const bKeys = Object.keys(b).sort();
    if (aKeys.length !== bKeys.length) return false;
    for (let i = 0; i < aKeys.length; i++) {
      if (aKeys[i] !== bKeys[i]) return false;
      const k = aKeys[i];
      const va = a[k];
      const vb = b[k];
      // compare arrays and primitives
      if (Array.isArray(va) || Array.isArray(vb)) {
        const arra = Array.isArray(va) ? va.slice().map(String) : [];
        const arrb = Array.isArray(vb) ? vb.slice().map(String) : [];
        if (arra.length !== arrb.length) return false;
        // compare order-insensitive
        arra.sort(); arrb.sort();
        for (let j=0;j<arra.length;j++) if (arra[j] !== arrb[j]) return false;
      } else {
        if (String(va) !== String(vb)) return false;
      }
    }
    return true;
  }

  // initial snapshot (take AFTER the DOM filled values)
  const initialSnapshot = serializeForm();

  // Ensure button initial state: disabled if no change
  function setButtonDisabledState(disabled) {
    if (!submitButton) return;
    submitButton.disabled = disabled;
    submitButton.classList.toggle('opacity-50', disabled);
    // optionally make it non-clickable via aria
    submitButton.setAttribute('aria-disabled', disabled ? 'true' : 'false');
  }

  // checkChanges: compare current snapshot to initial
  function checkChanges() {
    const current = serializeForm();

    // Important: If password field exists, treat empty string as "no change".
    // But initialSnapshot likely had '' for password; our serialize already returns trimmed values.
    const same = isEqualSnapshot(initialSnapshot, current);
    setButtonDisabledState(same);
    return !same; // returns true if changed
  }

  // attach listeners to all relevant form controls
  const watched = form.querySelectorAll(inputsSelector);
  watched.forEach(el => {
    // ignore fields we don't care about
    if (!el.name || ignoreNames.has(el.name)) return;
    // for selects and inputs catch both events
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  // run once to set correct initial state
  checkChanges();

  // Prevent form submit if nothing changed (safety)
  form.addEventListener('submit', function (e) {
    if (submitButton && submitButton.disabled) {
      e.preventDefault();
      // Optionally show a message
      alert('Tidak ada perubahan yang perlu disimpan.');
      return false;
    }
    // otherwise allow submit to proceed (server-side validation runs)
  });
});
</script>


@endsection
