document.addEventListener('DOMContentLoaded', () => {
    const slides = [...document.querySelectorAll('[data-slide]')];
    const dots = [...document.querySelectorAll('[data-carousel-dot]')];
    let activeSlide = 0;

    const showSlide = (index) => {
        if (!slides.length) return;
        activeSlide = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => slide.classList.toggle('current', slideIndex === activeSlide));
        dots.forEach((dot, dotIndex) => dot.classList.toggle('active', dotIndex === activeSlide));
    };

    document.querySelector('[data-carousel-prev]')?.addEventListener('click', () => showSlide(activeSlide - 1));
    document.querySelector('[data-carousel-next]')?.addEventListener('click', () => showSlide(activeSlide + 1));
    dots.forEach((dot) => dot.addEventListener('click', () => showSlide(Number(dot.dataset.carouselDot))));
    if (slides.length > 1) window.setInterval(() => showSlide(activeSlide + 1), 6500);

    const notificationButton = document.getElementById('notificationButton');
    const notificationPopover = document.getElementById('notificationPopover');
    notificationButton?.addEventListener('click', () => {
        const opening = notificationPopover.hidden;
        notificationPopover.hidden = !opening;
        notificationButton.setAttribute('aria-expanded', String(opening));
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.notification-wrap') && notificationPopover && !notificationPopover.hidden) {
            notificationPopover.hidden = true;
            notificationButton?.setAttribute('aria-expanded', 'false');
        }
    });

    const bookingSection = document.getElementById('booking-form');
    const serviceSelect = document.getElementById('serviceSelect');
    document.querySelectorAll('[data-order-service]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!bookingSection || !serviceSelect) return;
            serviceSelect.value = button.dataset.orderService;
            bookingSection.hidden = false;
            bookingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            serviceSelect.focus({ preventScroll: true });
        });
    });

    const requestedService = new URLSearchParams(window.location.search).get('service');
    if (requestedService && bookingSection && serviceSelect) {
        const serviceExists = [...serviceSelect.options].some((option) => option.value === requestedService);
        if (serviceExists) {
            serviceSelect.value = requestedService;
            bookingSection.hidden = false;
            if (window.location.hash === '#booking-form') {
                window.setTimeout(() => bookingSection.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
            }
        }
    }

    const orderForm = document.querySelector('.booking-form');
    if (orderForm && serviceSelect) {
        const ratePreview = orderForm.querySelector('[data-rate-preview]');
        const updateRatePreview = () => {
            const selected = serviceSelect.selectedOptions[0];
            const unitPrice = Number(selected?.dataset.price || 0);
            const delivery = orderForm.querySelector('[name="delivery_method"]:checked');
            const pickupFee = Number(delivery?.dataset.pickupFee || 0);
            const formattedRate = `Rp ${new Intl.NumberFormat('id-ID').format(unitPrice)} / kg`;
            const formattedPickup = ` · + Rp ${new Intl.NumberFormat('id-ID').format(pickupFee)} biaya jemput`;
            if (ratePreview) ratePreview.textContent = formattedRate + (pickupFee ? formattedPickup : '');
        };
        serviceSelect.addEventListener('change', updateRatePreview);
        orderForm
            .querySelectorAll('[name="delivery_method"]')
            .forEach((option) => option.addEventListener('change', updateRatePreview));
        updateRatePreview();
    }

    document.querySelectorAll('[data-payment-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Konfirmasi pembayaran QRIS simulasi? Tidak ada uang nyata yang diproses.'))
                event.preventDefault();
        });
    });

    document.querySelectorAll('[data-show-payment]').forEach((button) => {
        button.addEventListener('click', () => {
            const paymentDetails = button.parentElement.querySelector('[data-payment-details]');
            if (!paymentDetails) return;
            paymentDetails.hidden = false;
            button.hidden = true;
            paymentDetails.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    const userChat = document.querySelector('[data-user-chat]');
    const userChatMessages = document.getElementById('userOrderChatMessages');
    if (userChat && userChatMessages) {
        let lastMessageId = [...userChatMessages.querySelectorAll('[data-message-id]')].reduce(
            (latest, element) => Math.max(latest, Number(element.dataset.messageId)),
            0,
        );
        const appendOwnerMessage = (message) => {
            if (Number(message.id) <= lastMessageId) return;
            userChatMessages.querySelector('[data-chat-empty]')?.remove();
            const bubble = document.createElement('article');
            bubble.className = `order-chat-message ${message.sender === 'owner' ? 'is-owner' : 'is-customer'}`;
            bubble.dataset.messageId = message.id;
            const author = document.createElement('strong');
            author.textContent = message.sender === 'owner' ? userChat.dataset.laundryName : 'Saya';
            const body = document.createElement('p');
            body.textContent = message.message;
            const time = document.createElement('small');
            time.textContent = new Date(message.created_at).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
            });
            bubble.append(author, body, time);
            userChatMessages.append(bubble);
            lastMessageId = Number(message.id);
            userChatMessages.scrollTop = userChatMessages.scrollHeight;
        };
        const pollOwnerMessages = async () => {
            try {
                const url = new URL(userChat.dataset.messagesUrl, window.location.href);
                url.searchParams.set('after', String(lastMessageId));
                const response = await fetch(url, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) return;
                const data = await response.json();
                data.messages.forEach(appendOwnerMessage);
            } catch (error) {
                userChat.dataset.connection = 'offline';
            }
        };
        userChatMessages.scrollTop = userChatMessages.scrollHeight;
        window.setInterval(pollOwnerMessages, 4000);
    }

    document.querySelectorAll('[data-fake-qr]').forEach((canvas) => {
        const context = canvas.getContext('2d');
        if (!context) return;

        const modules = 29;
        const moduleSize = 5;
        let seed = [...canvas.dataset.fakeQr].reduce(
            (value, character) => (value * 31 + character.charCodeAt(0)) >>> 0,
            7,
        );
        const random = () => {
            seed = (seed * 1664525 + 1013904223) >>> 0;
            return seed / 4294967296;
        };
        context.fillStyle = '#fff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#182b22';

        const drawFinder = (left, top) => {
            context.fillRect(left * moduleSize, top * moduleSize, 7 * moduleSize, 7 * moduleSize);
            context.fillStyle = '#fff';
            context.fillRect((left + 1) * moduleSize, (top + 1) * moduleSize, 5 * moduleSize, 5 * moduleSize);
            context.fillStyle = '#182b22';
            context.fillRect((left + 2) * moduleSize, (top + 2) * moduleSize, 3 * moduleSize, 3 * moduleSize);
        };
        const inFinder = (x, y) =>
            [
                [1, 1],
                [modules - 8, 1],
                [1, modules - 8],
            ].some(([left, top]) => x >= left && x < left + 7 && y >= top && y < top + 7);

        for (let y = 1; y < modules - 1; y += 1) {
            for (let x = 1; x < modules - 1; x += 1) {
                if (!inFinder(x, y) && random() > 0.51) {
                    context.fillRect(x * moduleSize, y * moduleSize, moduleSize, moduleSize);
                }
            }
        }
        drawFinder(1, 1);
        drawFinder(modules - 8, 1);
        drawFinder(1, modules - 8);
    });
});
