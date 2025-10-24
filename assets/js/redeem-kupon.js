const totalUserKupon = totalKuponUser; // This will be set from PHP
let total = 0;

function updateTotal() {
    total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const qty = parseInt(input.value) || 0;
        const kupon = parseInt(input.dataset.kupon);
        total += qty * kupon;
    });

    document.getElementById('totalKupon').textContent = total + ' Kupon';

    const btn = document.getElementById('btnTukar');
    if (total > 0 && total <= totalUserKupon) {
        btn.disabled = false;
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-primary');
    } else {
        btn.disabled = true;
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
    }
}

// Pop-up konfirmasi
document.getElementById('redeemForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (total <= 0) return;

    Swal.fire({
        title: 'Konfirmasi Penukaran',
        html: `Anda akan menukar <strong>${total}</strong> kupon.<br>Apakah Anda yakin ingin melanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tukar Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Diproses...',
                text: 'Menukarkan kupon Anda.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1200
            });
            setTimeout(() => {
                e.target.submit();
            }, 1200);
        }
    });
});