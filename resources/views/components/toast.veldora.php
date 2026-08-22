<?php
// Veldora UI — Toast Component
// Props: id, variant (success|warning|danger|info), message, duration (ms, default 3500)
$id       = $id       ?? 'vui-toast-' . uniqid();
$variant  = $variant  ?? 'info';
$message  = $message  ?? ($slot ?? '');
$duration = $duration ?? 3500;

$icons    = ['success' => '✓', 'warning' => '⚠', 'danger' => '✕', 'info' => 'ℹ'];
$variants = ['success' => 'vui-toast-success', 'warning' => 'vui-toast-warning', 'danger' => 'vui-toast-danger', 'info' => 'vui-toast-info'];
$class    = 'vui-toast ' . ($variants[$variant] ?? $variants['info']);
$icon     = $icons[$variant] ?? $icons['info'];
?>
<div id="<?= htmlspecialchars($id) ?>" class="<?= $class ?>" role="status" aria-live="polite" aria-atomic="true">
    <span class="vui-toast-icon" aria-hidden="true"><?= $icon ?></span>
    <span class="vui-toast-message"><?= htmlspecialchars($message) ?></span>
    <button type="button" class="vui-toast-close" onclick="document.getElementById('<?= $id ?>').remove()" aria-label="Dismiss">✕</button>
</div>

<script>
(function () {
    var el = document.getElementById('<?= htmlspecialchars($id) ?>');
    if (!el) return;
    setTimeout(function () {
        el.classList.add('vui-toast-fade');
        setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 400);
    }, <?= (int) $duration ?>);
})();
</script>