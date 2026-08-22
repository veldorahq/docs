<?php
// Veldora UI — Spinner Component
// Props: size (sm|md|lg), label (screen-reader text)
$size  = $size  ?? 'md';
$label = $label ?? 'Loading...';

$sizes = ['sm' => 'vui-spinner-sm', 'md' => 'vui-spinner-md', 'lg' => 'vui-spinner-lg'];
$class = 'vui-spinner ' . ($sizes[$size] ?? $sizes['md']);
?>
<span class="<?= $class ?>" role="status" aria-label="<?= htmlspecialchars($label) ?>">
    <span class="vui-spinner-ring"></span>
    <span class="vui-sr-only"><?= htmlspecialchars($label) ?></span>
</span>