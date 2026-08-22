<?php
// Veldora UI — Table Component
// Props: striped (bool), hover (bool), bordered (bool), compact (bool)
$isStriped  = !empty($striped);
$isHover    = !empty($hover);
$isBordered = !empty($bordered);
$isCompact  = !empty($compact);

$tableClasses = 'vui-table';
if ($isStriped)  $tableClasses .= ' vui-table-striped';
if ($isHover)    $tableClasses .= ' vui-table-hover';
if ($isBordered) $tableClasses .= ' vui-table-bordered';
if ($isCompact)  $tableClasses .= ' vui-table-compact';
?>
<div class="vui-table-responsive">
    <table class="<?= $tableClasses ?>">
        <?= $slot ?? '' ?>
    </table>
</div>
