// Live Attendance – no page reload

(function () {

  function getCsrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  // ── Toast notification ──────────────────────────────────────────────
  function showToast(msg, type) {
    var existing = document.getElementById('att-toast');
    if (existing) existing.remove();

    var toast = document.createElement('div');
    toast.id = 'att-toast';
    toast.style.cssText = [
      'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
      'padding:12px 22px', 'border-radius:10px', 'font-size:.88rem',
      'font-weight:600', 'box-shadow:0 4px 20px rgba(0,0,0,.15)',
      'transition:opacity .3s',
      type === 'success'
        ? 'background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;'
        : 'background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;'
    ].join(';');
    toast.innerHTML = (type === 'success'
      ? '<i class="bi bi-check-circle-fill me-2"></i>'
      : '<i class="bi bi-x-circle-fill me-2"></i>') + msg;

    document.body.appendChild(toast);
    setTimeout(function () {
      toast.style.opacity = '0';
      setTimeout(function () { toast.remove(); }, 300);
    }, 3000);
  }

  // ── Update summary table row ─────────────────────────────────────────
  function updateSummaryRow(s) {
    var row = document.querySelector('[data-summary-user="' + s.user_id + '"]');
    if (!row) return;

    row.querySelector('[data-att-present]').textContent = s.present;
    row.querySelector('[data-att-late]').textContent    = s.late;
    row.querySelector('[data-att-absent]').textContent  = s.absent;

    var rateColor = s.rate >= 80 ? '#10b981' : (s.rate >= 60 ? '#f59e0b' : '#ef4444');
    var fill = row.querySelector('[data-att-fill]');
    if (fill) { fill.style.width = s.rate + '%'; fill.style.background = rateColor; }

    var rateVal = row.querySelector('[data-att-rate]');
    if (rateVal) { rateVal.textContent = s.rate + '%'; rateVal.style.color = rateColor; }

    var statusCell = row.querySelector('[data-att-status]');
    if (statusCell) {
      if (s.rate >= 80) {
        statusCell.innerHTML = '<span class="att-badge att-present"><i class="bi bi-check-circle-fill me-1"></i>Good</span>';
      } else if (s.rate >= 60) {
        statusCell.innerHTML = '<span class="att-badge att-late"><i class="bi bi-exclamation-circle-fill me-1"></i>Warning</span>';
      } else {
        statusCell.innerHTML = '<span class="att-badge att-absent"><i class="bi bi-x-circle-fill me-1"></i>At Risk</span>';
      }
    }
  }

  // ── Update top stat cards ────────────────────────────────────────────
  function updateStatCards(overallRate, riskCount) {
    var avgEl  = document.querySelector('[data-att-overall-rate]');
    var riskEl = document.querySelector('[data-att-risk-count]');
    if (avgEl)  avgEl.textContent  = overallRate + '%';
    if (riskEl) riskEl.textContent = riskCount;
  }

  // ── AJAX form submit ─────────────────────────────────────────────────
  function handleAttSubmit(e) {
    e.preventDefault();
    var form = e.currentTarget;
    var btn  = form.querySelector('button[type=submit]');
    var originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';

    var data = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': getCsrf(),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: data,
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      btn.disabled = false;
      btn.innerHTML = originalHtml;

      if (json.success) {
        showToast(json.message, 'success');

        if (json.summary) {
          json.summary.forEach(updateSummaryRow);
        }
        if (json.overallRate !== undefined) {
          updateStatCards(json.overallRate, json.riskCount);
        }
      } else {
        showToast(json.message || 'Something went wrong.', 'error');
      }
    })
    .catch(function () {
      btn.disabled = false;
      btn.innerHTML = originalHtml;
      showToast('Network error. Please try again.', 'error');
    });
  }

  // ── Bind all attendance forms ────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.att-ajax-form').forEach(function (form) {
      form.addEventListener('submit', handleAttSubmit);
    });
  });

})();
