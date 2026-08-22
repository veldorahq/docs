<?php
// Veldora UI — Accordion Component
// Props: id, title, open (bool, default false)
$accId  = $id ?? 'vui-acc-' . uniqid();
$title  = $title ?? 'Accordion Title';
$isOpen = !empty($open);
?>
<div id="<?= htmlspecialchars($accId) ?>" class="vui-accordion <?= $isOpen ? 'vui-accordion-open' : '' ?>">
    <button
        type="button"
        class="vui-accordion-header"
        aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
        onclick="(function(btn){
            var item = document.getElementById('<?= htmlspecialchars($accId) ?>');
            var open = item.classList.toggle('vui-accordion-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        })(this)"
    >
        <span class="vui-accordion-title"><?= htmlspecialchars($title) ?></span>
        <svg class="vui-accordion-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>
    <div class="vui-accordion-body" role="region">
        <div class="vui-accordion-inner">
            <?= $slot ?? '' ?>
        </div>
    </div>
</div>
