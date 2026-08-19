(function () {
    const digits = document.querySelectorAll('.otp-digit');
    const hidden = document.getElementById('otpHidden');
    const submitBtn = document.getElementById('submitBtn');
    const resendBtn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');

    digits.forEach((input, idx) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(-1);
            if (input.value) {
                input.classList.add('filled');
                if (idx < digits.length - 1) digits[idx + 1].focus();
            } else {
                input.classList.remove('filled');
            }
            syncHidden();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                digits[idx - 1].focus();
                digits[idx - 1].value = '';
                digits[idx - 1].classList.remove('filled');
                syncHidden();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach((ch, i) => {
                if (digits[i]) {
                    digits[i].value = ch;
                    digits[i].classList.add('filled');
                }
            });
            if (digits[paste.length]) digits[paste.length].focus();
            syncHidden();
        });
    });

    function syncHidden() {
        const code = Array.from(digits).map(d => d.value).join('');
        hidden.value = code;
        submitBtn.disabled = code.length < 6;
    }

    document.getElementById('otpForm').addEventListener('submit', () => {
        const code = Array.from(digits).map(d => d.value).join('');
        hidden.value = code;
    });

    // Countdown timer — 10 minutes
    let total = 10 * 60;
    let resendDelay = 60;
    const timer = setInterval(() => {
        total--;
        if (total <= 0) {
            clearInterval(timer);
            countdown.closest('.otp-timer').innerHTML = '<span class="text-danger">Code expired. Please resend.</span>';
            return;
        }
        const m = String(Math.floor(total / 60)).padStart(2, '0');
        const s = String(total % 60).padStart(2, '0');
        countdown.textContent = `${m}:${s}`;
    }, 1000);

    // Resend enable after 60s
    const resendTimer = setInterval(() => {
        resendDelay--;
        if (resendDelay <= 0) {
            clearInterval(resendTimer);
            resendBtn.disabled = false;
        }
    }, 1000);

    digits[0].focus();
})();
