<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById('applicationsChart');

    if (canvas) {
        const ctx = canvas.getContext('2d');
        Chart.register(ChartDataLabels);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Under Process', 'Cancelled'],
                datasets: [{
                    data: [
                        <?php echo e($pending ?? 0); ?>,
                        <?php echo e($underProcess ?? 0); ?>,
                        <?php echo e($cancelled ?? 0); ?>

                    ],
                    backgroundColor: ['#198754', '#0d6efd', '#6c757d'],
                    borderRadius: 8,
                    barThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end',
                        align: 'right',
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function(value) {
                            return value;
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    <?php if(session('reprint_id')): ?>
        let appointmentId = "<?php echo e(session('reprint_id')); ?>";

        if (confirm("Reference number updated successfully.\n\nDo you want to reprint the label?")) {
            window.open(
                "<?php echo e(route('appointments.printLabel', ':id')); ?>".replace(':id', appointmentId),
                "_blank"
            );
        }
    <?php endif; ?>
});

function openGlobalHistory(id) {
    const modalEl = document.getElementById('globalHistoryModal');
    const body = document.getElementById('historyModalBody');

    body.innerHTML = `
        <div class="text-center text-muted py-4">
            Loading history...
        </div>
    `;

    fetch(`/appointments/${id}/history`)
        .then(response => response.text())
        .then(html => {
            body.innerHTML = html;
        });

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

function openUnderProcessModal(id) {
    const form = document.getElementById('underProcessForm');
    form.action = `/appointments/${id}/status`;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('underProcessModal')).show();
}

function openProcessCompletedModal(id) {
    const form = document.getElementById('processCompletedForm');
    form.action = `/appointments/${id}/status`;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('processCompletedModal')).show();
}

function openChangeRefModal(id, currentRef) {
    const form = document.getElementById('changeRefForm');
    form.action = `/appointments/${id}/change-ref`;
    document.getElementById('currentRefNo').value = currentRef ?? '';

    bootstrap.Modal.getOrCreateInstance(document.getElementById('changeRefModal')).show();
}

function openChangeEmailModal(id, currentEmail) {
    const form = document.getElementById('changeEmailForm');
    form.action = `/appointments/${id}/change-email`;
    document.getElementById('currentEmail').value = currentEmail ?? '';

    bootstrap.Modal.getOrCreateInstance(document.getElementById('changeEmailModal')).show();
}
</script><?php /**PATH D:\Berlin-Appointments-31\resources\views/dashboard/partials/scripts.blade.php ENDPATH**/ ?>