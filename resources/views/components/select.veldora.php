<?php
// Veldora UI — Select Component
// Props: name, label, options (assoc or indexed), selected, placeholder, error, required, disabled, id
$options     = $options     ?? [];
$selected    = $selected    ?? '';
$placeholder = $placeholder ?? 'Select an option';
$required    = $required    ?? false;
$disabled    = $disabled    ?? false;
$error       = $error       ?? null;
$id          = $id          ?? ($name ?? 'select_' . uniqid());
$label       = $label       ?? null;

$selectClass  = 'vui-select' . ($error ? ' vui-input-error' : '');
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

    <select
        id="<?= htmlspecialchars($id) ?>"
        name="<?= htmlspecialchars($name ?? '') ?>"
        class="<?= $selectClass ?>"
        <?= $requiredAttr ?> <?= $disabledAttr ?>
        <?php if ($error): ?>aria-invalid="true"<?php endif; ?>
    >
        <?php if ($placeholder): ?>
            <option value="" disabled <?= $selected === '' ? 'selected' : '' ?>><?= htmlspecialchars($placeholder) ?></option>
        <?php endif; ?>
        <?php foreach ($options as $val => $label): ?>
            <option value="<?= htmlspecialchars((string) $val) ?>" <?= (string) $val === (string) $selected ? 'selected' : '' ?>>
                <?= htmlspecialchars((string) $label) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($error): ?>
        <p class="vui-field-error" role="alert"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
</div>