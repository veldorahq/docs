<?php
// Veldora UI — Progress Component
// Props: value (0-100), max (default 100), variant (primary|success|warning|danger), size (sm|md|lg), striped (bool), animated (bool), showLabel (bool)
$val       = (int) ($value ?? 0);
$maxVal    = (int) ($max ?? 100);
$pct       = $maxVal > 0 ? min(100, max(0, round(($val / $maxVal) * 100))) : 0;
$variant   = $variant ?? 'primary';
$size      = $size ?? 'md';
$isStriped = !empty($striped);
$isAnim    = !empty($animated);
$showLbl   = !empty($showLabel);

$barClasses = 'vui-progress-bar vui-progress-' . htmlspecialchars($variant);
if ($isStriped) $barClasses .= ' vui-progress-striped';
if ($isAnim)    $barClasses .= ' vui-progress-animated';
?>
<div class="vui-progress vui-progress-<?= htmlspecialchars($size) ?>" role="progressbar" aria-valuenow="<?= $val ?>" aria-valuemin="0" aria-valuemax="<?= $maxVal ?>" aria-label="<?= $showLbl ? $pct . '%' : 'Progress bar' ?>">
    <div class="<?= $barClasses ?>" style="width: <?= $pct ?>%;">
        <?php if ($showLbl): ?>
            <span class="vui-progress-label"><?= $pct ?>%</span>
        <?php endif; ?>
    </div>
</div>
