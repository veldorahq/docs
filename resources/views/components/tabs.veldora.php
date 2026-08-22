<?php
// Veldora UI — Tabs Component
// Props: id, tabs (array: key => Label), active (default first key)
$tabId  = $id ?? 'vui-tabs-' . uniqid();
$tabs   = $tabs ?? [];
$active = $active ?? (!empty($tabs) ? array_key_first($tabs) : '');
?>
<div id="<?= htmlspecialchars($tabId) ?>" class="vui-tabs-container">
    <div class="vui-tabs-list" role="tablist" aria-label="Tabs navigation">
        <?php foreach ($tabs as $key => $label): ?>
            <?php $isActive = ($key === $active); ?>
            <button
                type="button"
                role="tab"
                class="vui-tab-btn <?= $isActive ? 'vui-tab-active' : '' ?>"
                id="tab-btn-<?= htmlspecialchars($tabId . '-' . $key) ?>"
                aria-controls="tab-pane-<?= htmlspecialchars($tabId . '-' . $key) ?>"
                aria-selected="<?= $isActive ? 'true' : 'false' ?>"
                onclick="(function(btn){
                    var root = document.getElementById('<?= htmlspecialchars($tabId) ?>');
                    root.querySelectorAll('.vui-tab-btn').forEach(function(b){ b.classList.remove('vui-tab-active'); b.setAttribute('aria-selected','false'); });
                    root.querySelectorAll('.vui-tab-pane').forEach(function(p){ p.classList.remove('vui-tab-pane-active'); });
                    btn.classList.add('vui-tab-active');
                    btn.setAttribute('aria-selected','true');
                    var target = document.getElementById('tab-pane-<?= htmlspecialchars($tabId . '-') ?>' + '<?= htmlspecialchars($key) ?>');
                    if(target) target.classList.add('vui-tab-pane-active');
                })(this)"
            >
                <?= htmlspecialchars($label) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="vui-tabs-content">
        <?= $slot ?? '' ?>
    </div>
</div>
