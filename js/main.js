// ============================================================
// Tokyo Metro Railway Management System - JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function() {

    // Auto-hide alerts after 4 seconds
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() { alert.remove(); }, 500);
        }, 4000);
    });

    // Confirm before delete actions
    document.querySelectorAll('.btn-danger[data-confirm]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm(this.getAttribute('data-confirm') || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Confirm cancel ticket
    document.querySelectorAll('.cancel-ticket').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to cancel this ticket?')) {
                e.preventDefault();
            }
        });
    });
});
