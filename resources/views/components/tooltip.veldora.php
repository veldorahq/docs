<?php
// Veldora UI — Tooltip Component
// Props: text, position (top|bottom|left|right, default top)
$text = $text ?? '';
$pos  = $position ?? 'top';
?>
<span class="vui-tooltip-wrapper vui-tooltip-<?= htmlspecialchars($pos) ?>">
    <?= $slot ?? '' ?>
    <span class="vui-tooltip-bubble" role="tooltip" aria-hidden="true">
        <?= htmlspecialchars($text) ?>
    </span>
</span>
