document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('ownerSidebar');
    const scrim = document.getElementById('sidebarScrim');
    const menuToggle = document.getElementById('menuToggle');

    const closeSidebar = () => {
        sidebar?.classList.remove('open');
        scrim?.classList.remove('visible');
    };

    menuToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        scrim?.classList.toggle('visible');
    });
    scrim?.addEventListener('click', closeSidebar);

    const dialog = document.getElementById('serviceDialog');
    const serviceForm = document.getElementById('serviceForm');
    const openAddButton = document.getElementById('addServiceButton');

    openAddButton?.addEventListener('click', () => {
        serviceForm.reset();
        serviceForm.action = openAddButton.dataset.storeUrl || serviceForm.action;
        document.getElementById('serviceMethod').value = 'POST';
        document.getElementById('serviceDialogTitle').textContent = 'Tambah layanan';
        document.getElementById('serviceDuration').value = '24';
        document.getElementById('serviceActive').checked = true;
        document.getElementById('serviceOriginalPrice').value = '';
        document.getElementById('servicePromoLabel').value = '';
        document.getElementById('servicePromoUntil').value = '';
        dialog.showModal();
    });

    document.querySelectorAll('.edit-service').forEach((button) => {
        button.addEventListener('click', () => {
            serviceForm.action = button.dataset.updateUrl;
            document.getElementById('serviceMethod').value = 'PUT';
            document.getElementById('serviceDialogTitle').textContent = 'Edit layanan';
            document.getElementById('serviceName').value = button.dataset.name;
            document.getElementById('serviceDescription').value = button.dataset.description || '';
            document.getElementById('servicePrice').value = button.dataset.price;
            document.getElementById('serviceDuration').value = button.dataset.duration;
            document.getElementById('serviceActive').checked = button.dataset.active === '1';
            document.getElementById('serviceOriginalPrice').value = button.dataset.originalPrice || '';
            document.getElementById('servicePromoLabel').value = button.dataset.promoLabel || '';
            document.getElementById('servicePromoUntil').value = button.dataset.promoUntil || '';
            dialog.showModal();
        });
    });

    document.querySelectorAll('[data-close-dialog]').forEach((button) => {
        button.addEventListener('click', () => dialog?.close());
    });

    document.querySelectorAll('.delete-confirm').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Yakin ingin menghapus data ini?')) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-owner-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.ownerConfirm)) event.preventDefault();
        });
    });

    document.querySelectorAll('[data-closed-toggle]').forEach((toggle) => {
        const updateTimeInputs = () => {
            const row = toggle.closest('[data-schedule-row]');
            row.querySelectorAll('[data-time-input]').forEach((input) => {
                input.disabled = toggle.checked;
                input.required = !toggle.checked;
            });
        };
        toggle.addEventListener('change', updateTimeInputs);
        updateTimeInputs();
    });
    document.querySelectorAll('[data-weigh-form]').forEach((form) => {
        const weightInput = form.querySelector('[name="weight_kg"]');
        const preview = form.querySelector('[data-weigh-total]');
        const rate = Number(form.dataset.rate || 0);
        const pickupFee = Number(form.dataset.pickupFee || 0);
        const updateWeighTotal = () => {
            const weight = Number(String(weightInput.value).replace(',', '.'));
            preview.textContent = weight > 0
                ? `Rp ${new Intl.NumberFormat('id-ID').format(Math.round((weight * rate + pickupFee) * 100) / 100)}`
                : 'Masukkan berat';
        };
        weightInput.addEventListener('input', updateWeighTotal);
        updateWeighTotal();
    });

    const chatRoot = document.querySelector('[data-chat-root]');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatReplyForm');
    const chatInput = document.getElementById('chatMessageInput');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (chatRoot && chatMessages && chatForm && chatInput) {
        const customerName = chatRoot.dataset.customer;
        const customerId = chatRoot.dataset.userId;
        let lastMessageId = [...chatMessages.querySelectorAll('[data-message-id]')]
            .reduce((latest, element) => Math.max(latest, Number(element.dataset.messageId)), 0);

        const showChatError = (message) => {
            let status = document.getElementById('chatError');
            if (!status) {
                status = document.createElement('p');
                status.id = 'chatError';
                status.className = 'chat-error';
                status.setAttribute('role', 'status');
                chatForm.before(status);
            }
            status.textContent = message;
        };

        const appendMessage = (message) => {
            if (Number(message.id) <= lastMessageId) return;
            const bubble = document.createElement('div');
            bubble.className = `message ${message.sender === 'owner' ? 'message-owner' : 'message-customer'}`;
            bubble.dataset.messageId = message.id;
            const body = document.createElement('p');
            body.textContent = message.message;
            const time = document.createElement('small');
            time.textContent = new Date(message.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            bubble.append(body, time);
            chatMessages.append(bubble);
            lastMessageId = Number(message.id);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };

        const pollMessages = async () => {
            try {
                const url = new URL(chatRoot.dataset.messagesUrl, window.location.href);
                url.searchParams.set('customer', customerName);
                if (customerId) url.searchParams.set('user_id', customerId);
                url.searchParams.set('after', String(lastMessageId));
                const response = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) return;
                const data = await response.json();
                data.messages.forEach(appendMessage);
            } catch (error) {
                showChatError('Koneksi chat terputus. Mencoba menyambungkan kembali...');
            }
        };

        chatMessages.scrollTop = chatMessages.scrollHeight;
        window.setInterval(pollMessages, 4000);

        chatForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            const submitButton = chatForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            try {
                const response = await fetch(chatRoot.dataset.replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ customer_name: customerName, user_id: customerId || null, message }),
                });
                if (!response.ok) throw new Error('Pesan belum terkirim. Coba lagi.');
                appendMessage(await response.json());
                chatInput.value = '';
                showChatError('');
            } catch (error) {
                showChatError(error.message || 'Pesan belum terkirim. Coba lagi.');
            } finally {
                submitButton.disabled = false;
                chatInput.focus();
            }
        });
    }
});