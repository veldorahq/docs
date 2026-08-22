<?php
// Veldora UI — Badge Component
// Props: variant (default|success|warning|danger|info|purple), dot (bool)
$variant = $variant ?? 'default';
$dot     = $dot     ?? false;

$variants = [
    'default' => 'vui-badge vui-badge-default',
    'success' => 'vui-badge vui-badge-success',
    'warning' => 'vui-badge vui-badge-warning',
    'danger'  => 'vui-badge vui-badge-danger',
    'info'    => 'vui-badge vui-badge-info',
    'purple'  => 'vui-badge vui-badge-purple',
];
$class = $variants[$variant] ?? $variants['default'];
?>
<span class="<?= $class ?>">
    <?php if ($dot): ?><span class="vui-badge-dot" aria-hidden="true"></span><?php endif; ?>
    <?= $slot ?>
</span>