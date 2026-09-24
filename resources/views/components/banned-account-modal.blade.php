@auth
@if(Auth::user()->status === 'banned')
<!-- Non-Dismissible Banned Account Modal -->
<div class="modal fade" id="bannedAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="bannedAccountModalLabel" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 24px; box-shadow: 0 25px 60px -15px rgba(220, 38, 38, 0.35);">
            
            <!-- Modal Header with Dark/Crimson Hero -->
            <div class="p-4 text-center position-relative text-white" style="background: linear-gradient(135deg, #180909 0%, #3b0d0d 60%, #5c1414 100%);">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background: rgba(239, 68, 68, 0.18); border: 2px solid rgba(239, 68, 68, 0.45); border-radius: 20px; color: #f87171; font-size: 2.2rem; box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);">
                    <i class="bi bi-shield-slash-fill"></i>
                </div>
                <h4 class="fw-bold mb-1 text-white" id="bannedAccountModalLabel">Account Suspended</h4>
                <p class="text-white-50 small mb-0">Your access to the student dashboard has been restricted</p>
            </div>

            <!-- Modal Body -->
            <div class="p-4 p-md-4 bg-white text-dark">
                <!-- User Information Box -->
                <div class="p-3 rounded-3 mb-3 border d-flex align-items-center justify-content-between" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-4 text-muted"></i>
                        <div>
                            <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                    <span class="badge bg-danger px-2.5 py-1.5 rounded-pill fw-bold" style="font-size: 0.72rem;">
                        <i class="bi bi-slash-circle me-1"></i>Suspended
                    </span>
                </div>

                <!-- Explanation Message -->
                <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger rounded-3 p-3 mb-3 small d-flex gap-2 align-items-start">
                    <i class="bi bi-exclamation-octagon-fill fs-5 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <strong>Notice:</strong> Your student account has been suspended by your instructor. You cannot access your courses, class sessions, or dashboard at this time.
                    </div>
                </div>

                <p class="text-muted small mb-0">
                    If you believe this suspension was issued in error, or if you would like to submit an appeal, please contact our support team.
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="p-3 px-4 bg-light border-top d-flex gap-2 justify-content-between align-items-center flex-wrap">
                <a href="{{ route('logout.get') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold small">
                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                </a>
                <a href="{{ route('contact') }}" class="btn btn-danger rounded-pill px-4 py-2 fw-bold small shadow-sm">
                    <i class="bi bi-headset me-1"></i> Contact Support
                </a>
            </div>

        </div>
    </div>
</div>

<style>
    body.banned-modal-open .main-content,
    body.banned-modal-open .dashboard-wrapper,
    body.banned-modal-open header,
    body.banned-modal-open footer {
        filter: blur(8px) grayscale(40%);
        pointer-events: none !important;
        user-select: none !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('bannedAccountModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            var modal = new bootstrap.Modal(modalEl, {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
            document.body.classList.add('banned-modal-open');
        }
    });
</script>
@endif
@endauth
