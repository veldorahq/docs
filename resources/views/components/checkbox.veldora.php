<?php
// Veldora UI — Checkbox Component
// Props: name, label, value, checked, disabled, error, id
$value    = $value    ?? '1';
$checked  = $checked  ?? false;
$disabled = $disabled ?? false;
$error    = $error    ?? null;
$id       = $id       ?? ($name ?? 'checkbox_' . uniqid());
$label    = $label    ?? null;

$checkedAttr  = $checked  ? 'checked'   : '';
$disabledAttr = $disabled ? 'disabled'  : '';
?>
<div class="vui-checkbox-wrap">
    <input
        type="checkbox"
        id="<?= htmlspecialchars($id) ?>"
        name="<?= htmlspecialchars($name ?? '') ?>"
        value="<?= htmlspecialchars($value) ?>"
        class="vui-checkbox"
        <?= $checkedAttr ?> <?= $disabledAttr ?>
    >
    <?php if ($label): ?>
        <label class="vui-checkbox-label" for="<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($label) ?></label>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="vui-field-error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>