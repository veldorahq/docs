/* ─────────────────────────────────────────────
   Veldora Docs & UI Showcase — app.js
   Core scripts: navigation, search, code copying,
   dropdown toggles, modal handling, and toast alerts.
   ─────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar active state ───────────────────
    const path = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a').forEach(a => {
        if (a.getAttribute('href') === path) {
            a.closest('li')?.classList.add('active');
        }
    });

    // ── Scroll active sidebar item into view ───
    const activeItem = document.querySelector('.sidebar-nav li.active');
    if (activeItem) {
        activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    // ── Keyboard shortcut: / for search ───────
    const searchInput = document.getElementById('docs-search');
    if (searchInput) {
        document.addEventListener('keydown', e => {
            if (e.key === '/' && document.activeElement !== searchInput) {
                e.preventDefault();
                searchInput.focus();
            }
            if (e.key === 'Escape') searchInput.blur();
        });

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            document.querySelectorAll('.sidebar-nav li').forEach(li => {
                const text = li.textContent.toLowerCase();
                li.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });
    }

    // ── Prevent accidental page jumps on demo links inside .comp-preview ──
    document.querySelectorAll('.comp-preview a').forEach(a => {
        a.addEventListener('click', e => {
            const href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) {
                e.preventDefault();
            }
        });
    });

    // ── Prevent demo form submission inside previews ────────
    document.querySelectorAll('.comp-preview form, .comp-preview-panel form').forEach(f => {
        f.addEventListener('submit', e => e.preventDefault());
    });

    // ── Close dropdowns on outside click ───────────────
    document.addEventListener('click', e => {
        if (!e.target.closest('.vui-dropdown') && !e.target.closest('.dropdown')) {
            document.querySelectorAll('.vui-dropdown-menu, .dropdown-menu').forEach(m => {
                m.classList.remove('vui-dropdown-open');
                m.classList.add('hidden');
            });
            document.querySelectorAll('.vui-dropdown-trigger, #dd-trigger').forEach(btn => {
                btn.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // ── Close modal on Escape ──────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop').forEach(m => {
                m.style.display = 'none';
            });
            document.querySelectorAll('.vui-modal-overlay').forEach(m => {
                m.setAttribute('aria-hidden', 'true');
            });
        }
    });

    // ── Keyboard navigation in docs ────────────────────
    document.addEventListener('keydown', e => {
        if (document.activeElement?.tagName === 'INPUT' || document.activeElement?.tagName === 'TEXTAREA') return;
        if (e.key === 'ArrowLeft') {
            document.getElementById('doc-nav-prev')?.click();
        }
        if (e.key === 'ArrowRight') {
            document.getElementById('doc-nav-next')?.click();
        }
    });

    // ── Highlight all code on page load ────────────────
    highlightCodeBlocks();
});

// ────────────────────────────────────────────────────────────────────────────
// Syntax Highlighting Helper
// ────────────────────────────────────────────────────────────────────────────
function highlightCodeBlocks(container) {
    if (typeof Prism === 'undefined') return;
    const scope = container || document;
    scope.querySelectorAll('pre code, code[class*="language-"]').forEach(el => {
        Prism.highlightElement(el);
    });
}

// ────────────────────────────────────────────────────────────────────────────
// Global Dropdown Toggle
// ────────────────────────────────────────────────────────────────────────────
window.toggleDropdown = function(btn) {
    const dropdown = btn.closest('.vui-dropdown, .dropdown');
    if (!dropdown) return;

    const menu = dropdown.querySelector('.vui-dropdown-menu, .dropdown-menu');
    if (!menu) return;

    const isOpen = menu.classList.contains('vui-dropdown-open') && !menu.classList.contains('hidden');

    // Close all other dropdowns first
    document.querySelectorAll('.vui-dropdown-menu, .dropdown-menu').forEach(m => {
        m.classList.remove('vui-dropdown-open');
        m.classList.add('hidden');
    });
    document.querySelectorAll('.vui-dropdown-trigger, #dd-trigger').forEach(b => {
        b.setAttribute('aria-expanded', 'false');
    });

    if (!isOpen) {
        menu.classList.add('vui-dropdown-open');
        menu.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
    }
};

// ────────────────────────────────────────────────────────────────────────────
// Global Toast System
// ────────────────────────────────────────────────────────────────────────────
window.showToast = function(message, type = 'default') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';

    const icon = type === 'error'
        ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`
        : `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;

    toast.innerHTML = `${icon}<span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 350);
    }, 2500);
};

// ────────────────────────────────────────────────────────────────────────────
// Copy Code from any wrapper (Hero, CTA Terminal, Docs, Showcase)
// ────────────────────────────────────────────────────────────────────────────
window.copyCode = function(btn) {
    // Look for code container in priority order
    const container = btn.closest('.hero-code, .cta-terminal, .code-block-wrapper, .comp-code, .component-section, section');
    let code = container ? container.querySelector('pre code, code') : null;

    if (!code) {
        // Look at sibling or nearby pre
        const toolbar = btn.closest('.hero-code-toolbar, .cta-terminal-bar, .code-block-header') || btn.parentElement;
        if (toolbar && toolbar.nextElementSibling) {
            code = toolbar.nextElementSibling.querySelector('code') || toolbar.nextElementSibling;
        }
    }

    if (!code) {
        window.showToast('No code found to copy', 'error');
        return;
    }

    const textToCopy = code.innerText.trim();

    function setSuccessState() {
        btn.classList.add('copied');
        const origHTML = btn.innerHTML;
        btn.innerHTML = `
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span style="color:#22c55e;font-weight:600;">Copied!</span>`;

        window.showToast('Copied to clipboard!');

        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = origHTML;
        }, 2200);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textToCopy)
            .then(setSuccessState)
            .catch(() => fallbackCopy(textToCopy, setSuccessState));
    } else {
        fallbackCopy(textToCopy, setSuccessState);
    }
};

function fallbackCopy(text, onSuccess) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    try {
        const successful = document.execCommand('copy');
        if (successful && onSuccess) {
            onSuccess();
        } else {
            window.showToast('Failed to copy', 'error');
        }
    } catch (err) {
        window.showToast('Failed to copy', 'error');
    } finally {
        textarea.remove();
    }
}

// ────────────────────────────────────────────────────────────────────────────
// Switch Component View Tab (Preview vs Code)
// ────────────────────────────────────────────────────────────────────────────
window.switchCompTab = function(id, tab) {
    const box = document.getElementById('box-' + id) || document.getElementById(id);
    if (!box) return;

    const tabPrev = box.querySelector('.tab-btn-preview');
    const tabCode = box.querySelector('.tab-btn-code');
    const panelPrev = box.querySelector('.comp-preview-panel');
    const panelCode = box.querySelector('.comp-code-panel');

    if (tab === 'preview') {
        if (tabPrev) {
            tabPrev.classList.add('active');
            tabPrev.setAttribute('aria-selected', 'true');
        }
        if (tabCode) {
            tabCode.classList.remove('active');
            tabCode.setAttribute('aria-selected', 'false');
        }
        if (panelPrev) panelPrev.classList.remove('hidden');
        if (panelCode) panelCode.classList.add('hidden');
    } else {
        if (tabCode) {
            tabCode.classList.add('active');
            tabCode.setAttribute('aria-selected', 'true');
        }
        if (tabPrev) {
            tabPrev.classList.remove('active');
            tabPrev.setAttribute('aria-selected', 'false');
        }
        if (panelCode) {
            panelCode.classList.remove('hidden');
            if (window.Prism) {
                Prism.highlightAllUnder(panelCode);
            }
        }
        if (panelPrev) panelPrev.classList.add('hidden');
    }
};

// ────────────────────────────────────────────────────────────────────────────
// Copy code from inside a component showcase card
// ────────────────────────────────────────────────────────────────────────────
window.copyCompCode = function(id, btn) {
    const box = document.getElementById('box-' + id) || document.getElementById(id);
    const code = box ? box.querySelector('.comp-code-panel code, code') : null;

    if (!code) {
        window.showToast('No code to copy', 'error');
        return;
    }

    const textToCopy = code.innerText.trim();

    function setSuccess() {
        btn.classList.add('copied');
        const origHTML = btn.innerHTML;
        btn.innerHTML = `
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span style="color:#22c55e;font-weight:600;">Copied!</span>`;

        window.showToast('Template code copied!');

        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = origHTML;
        }, 2200);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textToCopy).then(setSuccess).catch(() => fallbackCopy(textToCopy, setSuccess));
    } else {
        fallbackCopy(textToCopy, setSuccess);
    }
};

// ────────────────────────────────────────────────────────────────────────────
// Copy CLI command from header badge
// ────────────────────────────────────────────────────────────────────────────
window.copyCliBadge = function(btn, command) {
    if (!command) return;

    function setSuccess() {
        btn.classList.add('copied');
        const origHTML = btn.innerHTML;
        btn.innerHTML = `
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span style="color:#22c55e;font-weight:600;">Copied: ${command}</span>`;

        window.showToast(`Command copied: ${command}`);

        setTimeout(() => {
            btn.classList.remove('copied');
            btn.innerHTML = origHTML;
        }, 2200);
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(command).then(setSuccess).catch(() => fallbackCopy(command, setSuccess));
    } else {
        fallbackCopy(command, setSuccess);
    }
};

// ────────────────────────────────────────────────────────────────────────────
// Interactive DataTable Client-Side Sort & Search
// ────────────────────────────────────────────────────────────────────────────
window.vuiDtSort = function(wrapId, colIdx, thElem) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;

    const tbody = wrap.querySelector('tbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr'));
    const isAsc = thElem.getAttribute('data-sort-dir') !== 'asc';

    // Reset all headers
    wrap.querySelectorAll('th').forEach(th => {
        th.removeAttribute('data-sort-dir');
        th.classList.remove('vui-sort-active');
        const icon = th.querySelector('.vui-sort-icon');
        if (icon) {
            icon.style.transform = '';
            icon.style.opacity = '.4';
        }
    });

    thElem.setAttribute('data-sort-dir', isAsc ? 'asc' : 'desc');
    thElem.classList.add('vui-sort-active');
    const sortIcon = thElem.querySelector('.vui-sort-icon');
    if (sortIcon) {
        sortIcon.style.transform = isAsc ? 'rotate(180deg)' : 'rotate(0deg)';
        sortIcon.style.opacity = '1';
        sortIcon.style.color = 'var(--accent, #7c6ef5)';
    }

    rows.sort((a, b) => {
        const aVal = (a.cells[colIdx]?.innerText || '').trim();
        const bVal = (b.cells[colIdx]?.innerText || '').trim();
        return isAsc 
            ? aVal.localeCompare(bVal, undefined, { numeric: true }) 
            : bVal.localeCompare(aVal, undefined, { numeric: true });
    });

    tbody.innerHTML = '';
    rows.forEach(r => tbody.appendChild(r));
    if (window.showToast) {
        window.showToast(`Sorted by ${(thElem.querySelector('span')?.textContent || 'column').trim()} (${isAsc ? 'ascending' : 'descending'})`);
    }
};

window.vuiDtSearch = function(wrapId, query) {
    const wrap = document.getElementById(wrapId);
    if (!wrap) return;

    const q = (query || '').toLowerCase().trim();
    const rows = wrap.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const matches = !q || text.includes(q);
        row.style.display = matches ? '' : 'none';
        if (matches) visibleCount++;
    });

    const countElem = wrap.querySelector('#' + wrapId + '-count') || wrap.querySelector('.vui-datatable-count');
    if (countElem) {
        countElem.textContent = `Showing ${visibleCount} member${visibleCount === 1 ? '' : 's'}`;
    }
};
