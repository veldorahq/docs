<?php
// Veldora UI — Input Component
// Props: name, label, type, value, placeholder, error, helper, required, disabled, id
$type        = $type        ?? 'text';
$value       = $value       ?? '';
$placeholder = $placeholder ?? '';
$required    = $required    ?? false;
$disabled    = $disabled    ?? false;
$error       = $error       ?? null;
$helper      = $helper      ?? null;
$id          = $id          ?? ($name ?? 'input_' . uniqid());
$label       = $label       ?? null;

$inputClass  = 'vui-input' . ($error ? ' vui-input-error' : '');
$requiredAttr = $required ? 'required' : '';
$disabledAttr = $disabled ? 'disabled' : '';
?>
<div class="vui-field">
    <?php if ($label): ?>
        <label class="vui-label" for="<?= htmlspecialchars($id) ?>">
            <?= htmlspecialchars($label) ?>
            <?php if ($required): ?><span class="vui-required" aria-hidden="true">*</span><?php endif; ?>
        </label>
    <?php endif; ?>

    <input
        id="<?= htmlspecialchars($id) ?>"
        type="<?= htmlspecialchars($type) ?>"
        name="<?= htmlspecialchars($name ?? '') ?>"
        value="<?= htmlspecialchars($value) ?>"
        placeholder="<?= htmlspecialchars($placeholder) ?>"
        class="<?= $inputClass ?>"
        <?= $requiredAttr ?> <?= $disabledAttr ?>
        <?php if ($error): ?>aria-invalid="true" aria-describedby="<?= $id ?>-error"<?php endif; ?>
    >

    <?php if ($error): ?>
        <p id="<?= $id ?>-error" class="vui-field-error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($helper): ?>
        <p class="vui-field-helper"><?= htmlspecialchars($helper) ?></p>
    <?php endif; ?>
</div>