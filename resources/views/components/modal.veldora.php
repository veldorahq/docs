<?php
// Veldora UI — Modal Component
// Props: id (required), title, size (sm|md|lg|xl)
$id    = $id    ?? 'vui-modal-' . uniqid();
$title = $title ?? null;
$size  = $size  ?? 'md';

$sizes = [
    'sm' => 'vui-modal-sm',
    'md' => 'vui-modal-md',
    'lg' => 'vui-modal-lg',
    'xl' => 'vui-modal-xl',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
?>
<div id="<?= htmlspecialchars($id) ?>" class="vui-modal-overlay" role="dialog" aria-modal="true" aria-hidden="true" <?php if ($title): ?>aria-labelledby="<?= $id ?>-title"<?php endif; ?>>
    <div class="vui-modal-container <?= $sizeClass ?>">
        <div class="vui-modal-header">
            <?php if ($title): ?>
                <h2 id="<?= $id ?>-title" class="vui-modal-title"><?= htmlspecialchars($title) ?></h2>
            <?php endif; ?>
            <button type="button" class="vui-modal-close" onclick="document.getElementById('<?= $id ?>').setAttribute('aria-hidden','true')" aria-label="Close">✕</button>
        </div>
        <div class="vui-modal-body">
            <?= $slot ?>
        </div>
        <?php if (isset($footer)): ?>
            <div class="vui-modal-footer"><?= $footer ?></div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('<?= $id ?>');
    if (!modal) return;
    // Close on overlay click
    modal.addEventListener('click', function (e) {
        if (e.target === modal) modal.setAttribute('aria-hidden', 'true');
    });
    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') modal.setAttribute('aria-hidden', 'true');
    });
})();
</script>