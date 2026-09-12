// Beta Notice Modal Handler
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('beta-overlay');
    if (!overlay) return;

    const closeBtn = document.getElementById('beta-close-btn');
    const skipBtn = document.querySelector('.beta-skip');

    function closeBetaModal() {
        overlay.classList.add('hiding');
        try {
            localStorage.setItem('edvora_beta_dismissed', 'true');
        } catch (e) {
            // Ignore localStorage errors
        }
        setTimeout(function () {
            overlay.style.display = 'none';
        }, 350);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeBetaModal);
    }

    if (skipBtn) {
        skipBtn.addEventListener('click', closeBetaModal);
    }

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeBetaModal();
        }
    });
});
