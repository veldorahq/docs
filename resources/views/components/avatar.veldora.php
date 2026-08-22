<?php
// Veldora UI — Avatar Component
// Props: src, name, size (xs|sm|md|lg|xl), shape (circle|square)
$src   = $src   ?? null;
$name  = $name  ?? '';
$size  = $size  ?? 'md';
$shape = $shape ?? 'circle';

$sizes  = ['xs' => 'vui-avatar-xs', 'sm' => 'vui-avatar-sm', 'md' => 'vui-avatar-md', 'lg' => 'vui-avatar-lg', 'xl' => 'vui-avatar-xl'];
$shapes = ['circle' => 'vui-avatar-circle', 'square' => 'vui-avatar-square'];
$class  = 'vui-avatar ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($shapes[$shape] ?? $shapes['circle']);

// Generate initials from name
$initials = '';
if ($name) {
    $parts = explode(' ', trim($name));
    $initials = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $initials .= strtoupper(substr(end($parts), 0, 1));
}
?>
<span class="<?= $class ?>" aria-label="<?= htmlspecialchars($name) ?>">
    <?php if ($src): ?>
        <img src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($name) ?>" class="vui-avatar-img">
    <?php else: ?>
        <span class="vui-avatar-initials" aria-hidden="true"><?= htmlspecialchars($initials) ?></span>
    <?php endif; ?>
</span>