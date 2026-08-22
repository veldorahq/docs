<?php
// Veldora UI — Textarea Component
// Props: name, label, rows, placeholder, error, helper, required, disabled, id
$rows        = $rows        ?? 4;
$placeholder = $placeholder ?? '';
$required    = $required    ?? false;
$disabled    = $disabled    ?? false;
$error       = $error       ?? null;
$helper      = $helper      ?? null;
$id          = $id          ?? ($name ?? 'textarea_' . uniqid());
$label       = $label       ?? null;
$content     = $slot        ?? '';

$areaClass    = 'vui-textarea' . ($error ? ' vui-input-error' : '');
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

    <textarea
        id="<?= htmlspecialchars($id) ?>"
        name="<?= htmlspecialchars($name ?? '') ?>"
        rows="<?= (int) $rows ?>"
        placeholder="<?= htmlspecialchars($placeholder) ?>"
        class="<?= $areaClass ?>"
        <?= $requiredAttr ?> <?= $disabledAttr ?>
        <?php if ($error): ?>aria-invalid="true" aria-describedby="<?= $id ?>-error"<?php endif; ?>
    ><?= htmlspecialchars($content) ?></textarea>

    <?php if ($error): ?>
        <p id="<?= $id ?>-error" class="vui-field-error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($helper): ?>
        <p class="vui-field-helper"><?= htmlspecialchars($helper) ?></p>
    <?php endif; ?>
</div>