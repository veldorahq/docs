<?php
// Veldora UI — Card Component
// Props: title, subtitle, padding (bool, default true)
$title    = $title    ?? null;
$subtitle = $subtitle ?? null;
$padding  = $padding  ?? true;
$class    = 'vui-card' . (!$padding ? ' vui-card-flush' : '');
?>
<div class="<?= $class ?>">
    <?php if ($title || $subtitle): ?>
        <div class="vui-card-header">
            <?php if ($title): ?>
                <h3 class="vui-card-title"><?= htmlspecialchars($title) ?></h3>
            <?php endif; ?>
            <?php if ($subtitle): ?>
                <p class="vui-card-subtitle"><?= htmlspecialchars($subtitle) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="vui-card-body">
        <?= $slot ?>
    </div>

    <?php if (isset($footer)): ?>
        <div class="vui-card-footer"><?= $footer ?></div>
    <?php endif; ?>
</div>