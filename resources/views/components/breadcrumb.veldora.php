<?php
// Veldora UI — Breadcrumb Component
// Props: items (array of ['label' => string, 'href' => ?string])
$items = $items ?? [];
?>
<nav class="vui-breadcrumb" aria-label="Breadcrumb">
    <ol class="vui-breadcrumb-list">
        <?php foreach ($items as $idx => $item): ?>
            <?php $isLast = ($idx === count($items) - 1); ?>
            <li class="vui-breadcrumb-item <?= $isLast ? 'vui-breadcrumb-active' : '' ?>">
                <?php if (!$isLast && !empty($item['href'])): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>" class="vui-breadcrumb-link"><?= htmlspecialchars($item['label']) ?></a>
                    <svg class="vui-breadcrumb-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                <?php else: ?>
                    <span aria-current="page"><?= htmlspecialchars($item['label']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
