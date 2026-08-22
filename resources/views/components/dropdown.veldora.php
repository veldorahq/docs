<?php
// Veldora UI — Dropdown Component
// Props: label, align (left|right), id
$label = $label ?? 'Options';
$align = $align ?? 'left';
$id    = $id    ?? 'vui-dropdown-' . uniqid();

$menuClass = 'vui-dropdown-menu' . ($align === 'right' ? ' vui-dropdown-right' : '');
?>
<div class="vui-dropdown" id="<?= htmlspecialchars($id) ?>">
    <button
        type="button"
        class="vui-dropdown-trigger"
        aria-haspopup="true"
        aria-expanded="false"
        onclick="(function(el){var open=el.getAttribute('aria-expanded')==='true';el.setAttribute('aria-expanded',!open);el.nextElementSibling.classList.toggle('vui-dropdown-open',!open);})(this)"
    >
        <?= htmlspecialchars($label) ?>
        <span class="vui-dropdown-caret" aria-hidden="true">▾</span>
    </button>
    <ul class="<?= $menuClass ?>" role="menu">
        <?= $slot ?>
    </ul>
</div>

<script>
document.addEventListener('click', function(e) {
    var d = document.getElementById('<?= $id ?>');
    if (d && !d.contains(e.target)) {
        var btn = d.querySelector('.vui-dropdown-trigger');
        var menu = d.querySelector('.vui-dropdown-menu');
        if (btn) btn.setAttribute('aria-expanded', 'false');
        if (menu) menu.classList.remove('vui-dropdown-open');
    }
});
</script>