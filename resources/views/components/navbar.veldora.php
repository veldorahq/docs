<?php
// Veldora UI — Navbar Component
// Props: brand (text or HTML), brandHref, sticky (bool)
$brand     = $brand     ?? '';
$brandHref = $brandHref ?? '/';
$sticky    = $sticky    ?? false;
$class     = 'vui-navbar' . ($sticky ? ' vui-navbar-sticky' : '');
$navId     = 'vui-nav-' . substr(md5($brand), 0, 6);
?>
<nav class="<?= $class ?>" role="navigation" aria-label="Main navigation">
    <div class="vui-navbar-inner">
        <a href="<?= htmlspecialchars($brandHref) ?>" class="vui-navbar-brand">
            <?= $brand ?>
        </a>

        <button
            type="button"
            class="vui-navbar-toggle"
            aria-controls="<?= $navId ?>"
            aria-expanded="false"
            onclick="(function(btn){var open=btn.getAttribute('aria-expanded')==='true';btn.setAttribute('aria-expanded',!open);document.getElementById('<?= $navId ?>').classList.toggle('vui-navbar-open',!open);})(this)"
            aria-label="Toggle navigation"
        >
            <span class="vui-navbar-burger"></span>
        </button>

        <div id="<?= $navId ?>" class="vui-navbar-menu">
            <?= $slot ?>
        </div>
    </div>
</nav>