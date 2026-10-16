document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const scrim = document.getElementById('adminSidebarScrim');
    const menuToggle = document.getElementById('adminMenuToggle');

    menuToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        scrim?.classList.toggle('visible');
    });
    scrim?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        scrim?.classList.remove('visible');
    });

    document.querySelectorAll('[data-admin-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.adminConfirm)) event.preventDefault();
        });
    });

    const dialog = document.getElementById('accountDialog');
    const form = document.getElementById('accountForm');
    document.querySelectorAll('.edit-account').forEach((button) => {
        button.addEventListener('click', () => {
            form.action = button.dataset.updateUrl;
            document.getElementById('accountName').value = button.dataset.name;
            document.getElementById('accountEmail').value = button.dataset.email;
            document.getElementById('accountPhone').value = button.dataset.phone || '';
            document.getElementById('accountAddress').value = button.dataset.address || '';
            document.getElementById('accountStatus').value = button.dataset.status;
            dialog.showModal();
        });
    });
    document.querySelectorAll('[data-close-account]').forEach((button) => {
        button.addEventListener('click', () => dialog?.close());
    });
    const serviceDialog = document.getElementById('catalogServiceDialog');
    const serviceForm = document.getElementById('catalogServiceForm');
    const serviceCreateUrl = serviceForm?.action;
    const serviceFields = {
        laundry: document.getElementById('catalogLaundry'),
        name: document.getElementById('catalogServiceName'),
        description: document.getElementById('catalogServiceDescription'),
        price: document.getElementById('catalogServicePrice'),
        duration: document.getElementById('catalogServiceDuration'),
        active: document.getElementById('catalogServiceActive'),
        promoLabel: document.getElementById('catalogServicePromoLabel'),
        originalPrice: document.getElementById('catalogServiceOriginalPrice'),
        promoUntil: document.getElementById('catalogServicePromoUntil'),
    };

    document.getElementById('addCatalogService')?.addEventListener('click', () => {
        serviceForm?.reset();
        if (serviceForm && serviceCreateUrl) serviceForm.action = serviceCreateUrl;
        document.getElementById('catalogServiceMethod').value = 'POST';
        document.getElementById('catalogServiceTitle').textContent = 'Tambah layanan';
        serviceDialog?.showModal();
    });

    document.querySelectorAll('.edit-catalog-service').forEach((button) => {
        button.addEventListener('click', () => {
            serviceForm.action = button.dataset.updateUrl;
            document.getElementById('catalogServiceMethod').value = 'PUT';
            document.getElementById('catalogServiceTitle').textContent = 'Edit layanan';
            serviceFields.laundry.value = button.dataset.laundry;
            serviceFields.name.value = button.dataset.name;
            serviceFields.description.value = button.dataset.description || '';
            serviceFields.price.value = button.dataset.price;
            serviceFields.duration.value = button.dataset.duration;
            serviceFields.active.checked = button.dataset.active === '1';
            serviceFields.promoLabel.value = button.dataset.promoLabel || '';
            serviceFields.originalPrice.value = button.dataset.originalPrice || '';
            serviceFields.promoUntil.value = button.dataset.promoUntil || '';
            serviceDialog.showModal();
        });
    });

    document.querySelectorAll('[data-close-catalog-service]').forEach((button) => {
        button.addEventListener('click', () => serviceDialog?.close());
    });

    const ownerAccountDialog = document.getElementById('ownerAccountDialog');
    document.getElementById('addOwnerAccount')?.addEventListener('click', () => ownerAccountDialog?.showModal());
    document.querySelectorAll('[data-close-owner-account]').forEach((button) => {
        button.addEventListener('click', () => ownerAccountDialog?.close());
    });

    const liveTransactions = document.getElementById('adminLiveTransactions');
    if (liveTransactions) {
        const allowedStatuses = new Set(['pending', 'processing', 'completed', 'cancelled']);
        const refreshTransactions = async () => {
            try {
                const response = await fetch(liveTransactions.dataset.liveUrl, {
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                });
                if (!response.ok) return;
                const payload = await response.json();
                liveTransactions.replaceChildren();

                if (!payload.transactions.length) {
                    const row = document.createElement('tr');
                    const cell = document.createElement('td');
                    cell.colSpan = 6;
                    cell.className = 'empty-cell';
                    cell.textContent = 'Belum ada transaksi.';
                    row.append(cell);
                    liveTransactions.append(row);
                    return;
                }

                payload.transactions.forEach((transaction) => {
                    const row = document.createElement('tr');
                    const values = [transaction.store_name, transaction.customer_name, transaction.service_name];
                    values.forEach((value, index) => {
                        const cell = document.createElement('td');
                        cell.textContent = value || '—';
                        if (index === 0) cell.className = 'cell-strong';
                        row.append(cell);
                    });
                    const amount = document.createElement('td');
                    amount.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(Number(transaction.total_amount || 0))}`;
                    row.append(amount);

                    const statusCell = document.createElement('td');
                    const badge = document.createElement('span');
                    const status = allowedStatuses.has(transaction.status) ? transaction.status : 'pending';
                    badge.className = `badge badge-${status}`;
                    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    statusCell.append(badge);
                    row.append(statusCell);

                    const timeCell = document.createElement('td');
                    timeCell.textContent = new Date(transaction.created_at).toLocaleString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
                    });
                    row.append(timeCell);
                    liveTransactions.append(row);
                });
            } catch (error) {
                // A temporary network error leaves the last loaded rows visible.
            }
        };

        refreshTransactions();
        window.setInterval(refreshTransactions, 15000);
    }
});
