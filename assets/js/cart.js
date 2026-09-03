let cart = JSON.parse(localStorage.getItem('pizzeria_cart')) || [];

function saveCart() {
    localStorage.setItem('pizzeria_cart', JSON.stringify(cart));
    renderCart();
}

function formatGs(value) {
    return 'Gs. ' + Number(value).toLocaleString('es-PY');
}

function addToCart(product) {
    const item = cart.find(p => Number(p.id) === Number(product.id));
    if (item) item.qty += 1;
    else cart.push({ ...product, id: Number(product.id), qty: 1 });
    saveCart();
    const canvas = document.getElementById('cartCanvas');
    if (canvas && window.bootstrap) bootstrap.Offcanvas.getOrCreateInstance(canvas).show();
}

function increaseQty(id) {
    const item = cart.find(p => Number(p.id) === Number(id));
    if (item) item.qty += 1;
    saveCart();
}

function decreaseQty(id) {
    const item = cart.find(p => Number(p.id) === Number(id));
    if (!item) return;
    item.qty -= 1;
    if (item.qty <= 0) cart = cart.filter(p => Number(p.id) !== Number(id));
    saveCart();
}

function removeItem(id) {
    cart = cart.filter(p => Number(p.id) !== Number(id));
    saveCart();
}

function clearCart() {
    cart = [];
    saveCart();
}

function renderCart() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');
    if (!cartItems) return;

    const totalItems = cart.reduce((sum, item) => sum + Number(item.qty), 0);
    const total = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.qty)), 0);

    if (cartCount) cartCount.textContent = totalItems;
    if (cartTotal) cartTotal.textContent = formatGs(total);

    if (cart.length === 0) {
        cartItems.innerHTML = '<div class="empty-cart">Tu carrito está vacío.</div>';
        return;
    }

    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="${item.image}" onerror="this.onerror=null;this.src='assets/img/logo/placeholder.svg';" alt="${item.name}">
            <div class="flex-grow-1">
                <h6>${item.name}</h6>
                <small>${formatGs(item.price)} x ${item.qty}</small>
                <div class="qty-controls mt-2">
                    <button onclick="decreaseQty(${item.id})">-</button>
                    <span>${item.qty}</span>
                    <button onclick="increaseQty(${item.id})">+</button>
                    <button class="remove" onclick="removeItem(${item.id})"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
    `).join('');
}

async function sendOrderToWhatsApp(phone) {
    if (cart.length === 0) {
        alert('Agregá al menos un producto al carrito.');
        return;
    }

    const name = document.getElementById('customerName').value.trim() || 'Cliente';
    const customerPhone = document.getElementById('customerPhone').value.trim() || 'Sin teléfono';
    const delivery = document.getElementById('deliveryType').value;
    const payment = document.getElementById('paymentMethod').value;
    const address = document.getElementById('customerAddress').value.trim() || 'Sin dirección indicada';
    const total = cart.reduce((sum, item) => sum + (Number(item.price) * Number(item.qty)), 0);
    const resultBox = document.getElementById('orderResult');

    let orderId = null;
    try {
        if (resultBox) resultBox.innerHTML = '<span class="text-muted">Guardando pedido...</span>';
        const response = await fetch('guardar_pedido.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cliente: name, telefono: customerPhone, direccion: address, tipo_entrega: delivery, metodo_pago: payment, items: cart })
        });
        const data = await response.json();
        if (!data.ok) throw new Error(data.message || 'No se pudo guardar el pedido.');
        orderId = data.id_pedido;
        if (resultBox) resultBox.innerHTML = `<span class="text-success">Pedido #${orderId} guardado correctamente.</span>`;
    } catch (error) {
        if (resultBox) resultBox.innerHTML = `<span class="text-danger">${error.message}</span>`;
        const continuar = confirm('No se pudo guardar en la base de datos. ¿Deseás enviar igual el pedido por WhatsApp?');
        if (!continuar) return;
    }

    let message = `Hola, quiero realizar este pedido:%0A%0A`;
    if (orderId) message += `Pedido Nro: ${orderId}%0A`;
    message += `Cliente: ${encodeURIComponent(name)}%0A`;
    message += `Teléfono: ${encodeURIComponent(customerPhone)}%0A`;
    message += `Entrega: ${encodeURIComponent(delivery === 'delivery' ? 'Delivery' : 'Retiro del local')}%0A`;
    message += `Método de pago: ${encodeURIComponent(payment)}%0A`;
    message += `Dirección/Referencia: ${encodeURIComponent(address)}%0A%0A`;
    message += `Detalle del pedido:%0A`;

    cart.forEach(item => {
        message += `- ${encodeURIComponent(item.name)} x${item.qty} = ${encodeURIComponent(formatGs(Number(item.price) * Number(item.qty)))}%0A`;
    });

    message += `%0ATotal productos: ${encodeURIComponent(formatGs(total))}%0A`;
    if (delivery === 'delivery') message += `Delivery estimado: ${encodeURIComponent(formatGs(10000))}%0A`;
    message += `%0AQuedo atento/a a la confirmación.`;

    window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
    if (orderId) clearCart();
}

document.addEventListener('DOMContentLoaded', renderCart);
