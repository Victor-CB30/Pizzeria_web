<?php
$pageTitle = 'Contacto | Pizzería';
require_once 'includes/header.php';
?>
<section class="page-header">
    <div class="container">
        <h1>Contacto</h1>
        <p>Realizá tu pedido o consultá disponibilidad.</p>
    </div>
</section>
<section class="container py-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="info-card"><i class="bi bi-geo-alt"></i><h5>Dirección</h5><p><?= limpiarTexto($empresa['direccion'] ?? '') ?></p></div>
        </div>
        <div class="col-md-4">
            <div class="info-card"><i class="bi bi-clock"></i><h5>Horario</h5><p><?= limpiarTexto($empresa['horario'] ?? '') ?></p></div>
        </div>
        <div class="col-md-4">
            <div class="info-card"><i class="bi bi-whatsapp"></i><h5>WhatsApp</h5><p><?= limpiarTexto($empresa['telefono'] ?? WHATSAPP_NUMBER) ?></p></div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
