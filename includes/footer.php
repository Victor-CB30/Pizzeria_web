</main>

<footer class="footer-section">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <h5 class="fw-700 mb-1"><?= limpiarTexto($empresa['nombre_empresa'] ?? APP_NAME) ?></h5>
                <p class="mb-1 small"><?= limpiarTexto($empresa['slogan'] ?? '') ?></p>
                <small><?= limpiarTexto($empresa['direccion'] ?? '') ?></small>
            </div>
            <div class="col-md-3">
                <p class="mb-1 small" style="color:rgba(255,255,255,.5);font-weight:600;text-transform:uppercase;letter-spacing:.06em;font-size:.72rem">Horario</p>
                <p class="mb-0 small"><?= limpiarTexto($empresa['horario'] ?? '') ?></p>
            </div>
            <div class="col-md-3 text-md-end">
                <a class="btn btn-whatsapp" target="_blank"
                   href="https://wa.me/<?= limpiarTexto($empresa['telefono'] ?? WHATSAPP_NUMBER) ?>">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);margin:1.5rem 0 1rem">
        <p class="mb-0 small text-center" style="color:rgba(255,255,255,.2)">
            &copy; <?= date('Y') ?> <?= limpiarTexto($empresa['nombre_empresa'] ?? APP_NAME) ?>
        </p>
    </div>
</footer>

<!-- CARRITO OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartCanvas" style="max-width:420px">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold"><i class="bi bi-cart3 me-2"></i>Mi pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="cartItems" class="flex-grow-1"></div>
        <div class="cart-summary mt-3 pt-3 border-top">
            <div class="d-flex justify-content-between fw-bold mb-3">
                <span>Total</span>
                <span id="cartTotal">Gs. 0</span>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-600">Nombre</label>
                <input type="text" id="customerName" class="form-control" placeholder="Ej: Juan Pérez">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-600">Teléfono</label>
                <input type="text" id="customerPhone" class="form-control" placeholder="Ej: 0981 123456">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-600">Método de pago</label>
                <select id="paymentMethod" class="form-select">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia bancaria">Transferencia bancaria</option>
                    <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                    <option value="Tarjeta de débito">Tarjeta de débito</option>
                    <option value="Billetera digital">Billetera digital</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-600">Tipo de entrega</label>
                <select id="deliveryType" class="form-select">
                    <option value="delivery">Delivery</option>
                    <option value="retiro_local">Retiro del local</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-600">Dirección / Referencia</label>
                <textarea id="customerAddress" class="form-control" rows="2" placeholder="Barrio, calle, referencia…"></textarea>
            </div>
            <div id="orderResult" class="small mb-2"></div>
            <button class="btn btn-whatsapp w-100 mb-2"
                    onclick="sendOrderToWhatsApp('<?= limpiarTexto($empresa['telefono'] ?? WHATSAPP_NUMBER) ?>')">
                <i class="bi bi-whatsapp"></i> Enviar pedido por WhatsApp
            </button>
            <button class="btn btn-outline-secondary w-100 btn-sm" onclick="clearCart()">Vaciar carrito</button>
        </div>
    </div>
</div>

<!-- FLOATING WHATSAPP -->
<a class="floating-whatsapp" target="_blank"
   href="https://wa.me/<?= limpiarTexto($empresa['telefono'] ?? WHATSAPP_NUMBER) ?>"
   title="Contactar por WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/cart.js"></script>
</body>
</html>
