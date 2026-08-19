// Course Learning Page - Tab switching and interactivity

document.addEventListener('DOMContentLoaded', function () {
  initTabs();
  initLessonExpand();
  initNoteToggle();
});

// Tab switching
function initTabs() {
  const tabs = document.querySelectorAll('.learning-tab');
  const mobileSelect = document.getElementById('mobile-tab-select');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      activateTab(this.dataset.tab);
    });
  });

  if (mobileSelect) {
    mobileSelect.addEventListener('change', function () {
      activateTab(this.value, true);
    });
  }

  // Activate tab from URL ?tab=chat or #chat
  const params = new URLSearchParams(window.location.search);
  const hash = window.location.hash.replace('#', '');
  const tabFromUrl = params.get('tab') || hash;
  if (tabFromUrl) {
    activateTab(tabFromUrl, true);
  }
}

function activateTab(target, scrollToPanel) {
  const tabs = document.querySelectorAll('.learning-tab');
  const panels = document.querySelectorAll('.tab-panel');
  const mobileSelect = document.getElementById('mobile-tab-select');

  const targetTab = document.querySelector('.learning-tab[data-tab="' + target + '"]');
  const targetPanel = document.getElementById('panel-' + target);

  if (!targetTab || !targetPanel) return;

  tabs.forEach(function (t) { t.classList.remove('active'); });
  panels.forEach(function (p) { p.classList.remove('active'); });

  targetTab.classList.add('active');
  targetPanel.classList.add('active');

  if (mobileSelect && mobileSelect.value !== target) {
    mobileSelect.value = target;
  }

  if (scrollToPanel) {
    const header = document.querySelector('.navbar');
    const headerOffset = header ? header.offsetHeight + 20 : 90;
    const elementPosition = targetPanel.getBoundingClientRect().top + window.scrollY;
    window.scrollTo({ top: elementPosition - headerOffset, behavior: 'smooth' });
  }
}

// Lesson expand/collapse
function initLessonExpand() {
  var toggles = document.querySelectorAll('.lesson-expand-toggle');

  toggles.forEach(function (toggle) {
    toggle.addEventListener('click', function () {
      var lessonId = this.dataset.lesson;
      var content = document.getElementById('lesson-content-' + lessonId);
      var icon = this.querySelector('.expand-icon');

      if (content) {
        content.classList.toggle('show');
        if (icon) {
          icon.classList.toggle('bi-chevron-down');
          icon.classList.toggle('bi-chevron-up');
        }
      }
    });
  });
}

// Note read more / read less
function initNoteToggle() {
  var toggles = document.querySelectorAll('.note-toggle');

  toggles.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var noteId = this.dataset.note;
      var content = document.getElementById('note-content-' + noteId);

      if (content) {
        content.classList.toggle('truncated');
        if (content.classList.contains('truncated')) {
          this.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Read More';
        } else {
          this.innerHTML = '<i class="bi bi-chevron-up me-1"></i>Read Less';
        }
      }
    });
  });
}
