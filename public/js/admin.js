document.addEventListener('DOMContentLoaded', function() {


    // Modal konfirmasi hapus
var modal       = document.getElementById('deleteModal');
var modalForm   = document.getElementById('modalForm');
var modalCancel = document.getElementById('modalCancel');

if (modal) {
  // Semua tombol hapus
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
      modalForm.action = btn.dataset.action;
      modal.style.display = 'flex';
    });
  });

  // Tutup modal
  modalCancel.addEventListener('click', function() {
    modal.style.display = 'none';
  });

  // Klik backdrop tutup modal
  modal.addEventListener('click', function(e) {
    if (e.target === modal) modal.style.display = 'none';
  });
}


// Tambah stat row (Junior Sentinel)
var addStat = document.getElementById('addStat');
if (addStat) {
  var statCount = document.querySelectorAll('.stat-row').length;
  addStat.addEventListener('click', function() {
    if (statCount >= 3) { alert('Maksimal 3 stat.'); return; }
    var list = document.getElementById('stats-list');
    var row  = document.createElement('div');
    row.className = 'stat-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';
    row.innerHTML =
      '<input type="text" name="stats[' + statCount + '][value]" class="form-input" placeholder="50+" style="flex:1">' +
      '<input type="text" name="stats[' + statCount + '][label]" class="form-input" placeholder="PESERTA TARGET" style="flex:1">' +
      '<button type="button" class="btn-danger stat-remove" style="padding:0 12px;flex-shrink:0">×</button>';
    list.appendChild(row);
    statCount++;
  });

  document.getElementById('stats-list').addEventListener('click', function(e) {
    if (e.target.classList.contains('stat-remove')) {
      var rows = document.querySelectorAll('.stat-row');
      if (rows.length > 1) { e.target.closest('.stat-row').remove(); statCount--; }
      else { e.target.closest('.stat-row').querySelectorAll('input').forEach(function(i){ i.value=''; }); }
    }
  });
}


  // ── Toggle password visibility (login) ──────────────────
  var togglePw = document.getElementById('togglePw');
  var pwInput  = document.getElementById('password');
  if (togglePw && pwInput) {
    togglePw.addEventListener('click', function() {
      pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
    });
  }

  // ── OTP cells (totp-setup) ───────────────────────────────
  var cells  = document.querySelectorAll('.otp-cell');
  var hidden = document.getElementById('totp_hidden');
  var btn    = document.getElementById('otpSubmit');

  if (cells.length > 0) {
    function syncOtp() {
      var val = Array.from(cells).map(function(c) { return c.value; }).join('');
      if (hidden) hidden.value = val;
      if (btn) btn.disabled = val.length < 6;
    }
    cells.forEach(function(cell, i) {
      cell.addEventListener('input', function() {
        cell.value = cell.value.replace(/[^0-9]/g, '').slice(-1);
        syncOtp();
        if (cell.value && i < 5) cells[i + 1].focus();
      });
      cell.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !cell.value && i > 0) cells[i - 1].focus();
      });
    });
    cells[0].addEventListener('paste', function(e) {
      e.preventDefault();
      var p = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      p.slice(0, 6).split('').forEach(function(d, i) { if (cells[i]) cells[i].value = d; });
      syncOtp();
      cells[Math.min(p.length, 5)].focus();
    });
  }

  // ── Copy secret (totp-setup) ─────────────────────────────
  var secretBox = document.getElementById('secretBox');
  if (secretBox) {
    secretBox.addEventListener('click', function() {
      var text = secretBox.textContent.trim().replace(/\s/g, '');
      navigator.clipboard && navigator.clipboard.writeText(text).then(function() {
        secretBox.style.background = '#d1fae5';
        setTimeout(function() { secretBox.style.background = ''; }, 1500);
      });
    });
  }

  // ── Copy backup codes ────────────────────────────────────
  var btnCopy = document.getElementById('btnCopy');
  if (btnCopy) {
    btnCopy.addEventListener('click', function() {
      var codes = Array.from(document.querySelectorAll('.backup-code')).map(function(el) {
        return el.textContent.trim();
      });
      navigator.clipboard && navigator.clipboard.writeText(codes.join('\n')).then(function() {
        btnCopy.textContent = 'Tersalin!';
        setTimeout(function() { btnCopy.textContent = 'Salin Semua Kode'; }, 2000);
      });
    });
  }

});
