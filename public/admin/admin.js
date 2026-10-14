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
            document.getElementById('accountRole').value = button.dataset.role;
            document.getElementById('accountStatus').value = button.dataset.status;
            dialog.showModal();
        });
    });
    document.querySelectorAll('[data-close-account]').forEach((button) => {
        button.addEventListener('click', () => dialog?.close());
    });
});