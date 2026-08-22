<?php
// Veldora UI — Radio Component
// Props: name, label, value, checked, disabled, id
$checked  = $checked  ?? false;
$disabled = $disabled ?? false;
$id       = $id       ?? ($name ?? 'radio') . '_' . ($value ?? uniqid());
$label    = $label    ?? null;

$checkedAttr  = $checked  ? 'checked'  : '';
$disabledAttr = $disabled ? 'disabled' : '';
?>
<div class="vui-radio-wrap">
    <input
        type="radio"
        id="<?= htmlspecialchars($id) ?>"
        name="<?= htmlspecialchars($name ?? '') ?>"
        value="<?= htmlspecialchars($value ?? '') ?>"
        class="vui-radio"
        <?= $checkedAttr ?> <?= $disabledAttr ?>
    >
    <?php if ($label): ?>
        <label class="vui-radio-label" for="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($label) ?></label>
    <?php endif; ?>
</div>