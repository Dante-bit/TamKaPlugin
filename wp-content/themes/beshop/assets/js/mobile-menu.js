/**
 * Mobile Menu JS - Topbar + Slide-in Panel
 * BeShop - Accessible Mobile Navigation
 *
 * Features:
 * - Hamburger toggle with icon swap
 * - Slide-in panel with smooth animation
 * - Overlay with backdrop blur
 * - Dropdown toggles for submenus (injected via JS)
 * - Full keyboard navigation (Tab, Escape, Arrow keys, Home/End)
 * - Focus trap inside open panel
 * - Automatic submenu open on toggle focus (Tab)
 * - Focus + Click conflict prevention
 * - Body scroll lock when panel is open
 * - Resize listener to auto-close on desktop
 *
 * @package BeShop
 */
(function () {
    'use strict';

    /* ===========================================
       Cache DOM elements
       =========================================== */
    const menuBar      = document.getElementById('wsm-menu');
    const openBtn      = document.getElementById('mmenu-btn');
    const closeBtn     = document.getElementById('mmenu-close-btn');
    const panel        = document.getElementById('mobile-menu-panel');
    const overlay      = document.getElementById('mobile-menu-overlay');
    const navContainer = document.getElementById('mobile-navigation');
    const menuList     = document.getElementById('wsm-menu-ul');

    // Bail early if essential elements are missing
    if (!menuBar || !openBtn || !panel || !overlay) {
        return;
    }

    /* ===========================================
       Inject Dropdown Toggle Buttons
       =========================================== */
    function injectDropdownToggles() {
        if (!menuList) return;

        const parents = menuList.querySelectorAll(
            '.menu-item-has-children, .page_item_has_children'
        );

        parents.forEach(function (item) {
            // Skip if toggle already exists
            if (item.querySelector('.submenu-toggle')) return;

            var toggle = document.createElement('button');
            toggle.className = 'submenu-toggle';
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Toggle submenu');
            toggle.innerHTML =
                '<svg class="toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                '<polyline points="6 9 12 15 18 9"></polyline>' +
                '</svg>';

            // Insert toggle after the <a> link
            var link = item.querySelector(':scope > a');
            if (link && link.nextSibling) {
                item.insertBefore(toggle, link.nextSibling);
            } else {
                item.appendChild(toggle);
            }
        });
    }

    /* ===========================================
       Submenu Open / Close Helpers
       =========================================== */
    function openSubmenu(parentLi) {
        parentLi.classList.add('submenu-open');
        var toggle = parentLi.querySelector(':scope > .submenu-toggle');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
        }
    }

    function closeSubmenu(parentLi) {
        parentLi.classList.remove('submenu-open');
        var toggle = parentLi.querySelector(':scope > .submenu-toggle');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
        // Also close any nested open submenus
        var nested = parentLi.querySelectorAll('.submenu-open');
        nested.forEach(function (n) {
            n.classList.remove('submenu-open');
            var t = n.querySelector(':scope > .submenu-toggle');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }

    function closeSiblingSubmenus(currentLi) {
        var parent = currentLi.parentElement;
        if (!parent) return;
        var siblings = parent.querySelectorAll(':scope > .submenu-open');
        siblings.forEach(function (sib) {
            if (sib !== currentLi) {
                closeSubmenu(sib);
            }
        });
    }

    function closeAllSubmenus() {
        if (!menuList) return;
        var openItems = menuList.querySelectorAll('.submenu-open');
        openItems.forEach(function (item) {
            item.classList.remove('submenu-open');
            var t = item.querySelector(':scope > .submenu-toggle');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }

    /* ===========================================
       Dropdown Toggle: Click Handler
       =========================================== */
    function handleToggleClick(e) {
        var toggle = e.target.closest('.submenu-toggle');
        if (!toggle) return;

        e.preventDefault();
        e.stopPropagation();

        var parentLi = toggle.closest(
            '.menu-item-has-children, .page_item_has_children'
        );
        if (!parentLi) return;

        // If focus just opened this submenu, don't close it
        if (parentLi._focusJustOpened) {
            return;
        }

        closeSiblingSubmenus(parentLi);

        if (parentLi.classList.contains('submenu-open')) {
            closeSubmenu(parentLi);
        } else {
            openSubmenu(parentLi);
        }
    }

    /* ===========================================
       Dropdown Toggle: Focus Handler (Tab key)
       =========================================== */
    function handleToggleFocus(e) {
        var toggle = e.target.closest('.submenu-toggle');
        if (!toggle) return;

        var parentLi = toggle.closest(
            '.menu-item-has-children, .page_item_has_children'
        );
        if (!parentLi) return;

        // Close sibling submenus
        closeSiblingSubmenus(parentLi);

        // Auto-open submenu on focus (Tab navigation)
        if (!parentLi.classList.contains('submenu-open')) {
            openSubmenu(parentLi);
            // Set flag to prevent click from closing it
            parentLi._focusJustOpened = true;
            setTimeout(function () {
                parentLi._focusJustOpened = false;
            }, 300);
        }
    }

    /* ===========================================
       Open / Close Panel
       =========================================== */
    function openMenu() {
        panel.classList.add('panel-open');
        overlay.classList.add('active');
        menuBar.classList.add('menu-open');
        document.body.classList.add('mobile-menu-is-open');
        openBtn.setAttribute('aria-expanded', 'true');
        panel.setAttribute('aria-hidden', 'false');

        // Focus the close button after panel opens
        setTimeout(function () {
            if (closeBtn) closeBtn.focus();
        }, 100);
    }

    function closeMenu() {
        panel.classList.remove('panel-open');
        overlay.classList.remove('active');
        menuBar.classList.remove('menu-open');
        document.body.classList.remove('mobile-menu-is-open');
        openBtn.setAttribute('aria-expanded', 'false');
        panel.setAttribute('aria-hidden', 'true');

        // Close all open submenus
        closeAllSubmenus();

        // Return focus to hamburger button
        openBtn.focus();
    }

    /* ===========================================
       Focus Trap
       =========================================== */
    function getFocusableElements() {
        var selectors = [
            'a[href]',
            'button:not([disabled]):not([tabindex="-1"])',
            'input:not([disabled]):not([type="hidden"])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])'
        ].join(', ');

        var elements = panel.querySelectorAll(selectors);
        return Array.from(elements).filter(function (el) {
            return el.offsetParent !== null;
        });
    }

    /* ===========================================
       Keyboard Handler
       =========================================== */
    function handleKeydown(e) {
        if (!panel.classList.contains('panel-open')) return;

        var key = e.key;

        // Escape → close menu
        if (key === 'Escape') {
            e.preventDefault();
            closeMenu();
            return;
        }

        var focusable = getFocusableElements();
        if (focusable.length === 0) return;

        var currentIndex = focusable.indexOf(document.activeElement);
        var firstEl = focusable[0];
        var lastEl = focusable[focusable.length - 1];

        // Tab / Shift+Tab → focus trap
        if (key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstEl || currentIndex <= 0) {
                    e.preventDefault();
                    lastEl.focus();
                }
            } else {
                if (document.activeElement === lastEl || currentIndex >= focusable.length - 1) {
                    e.preventDefault();
                    firstEl.focus();
                }
            }
            return;
        }

        // Arrow keys for menu navigation
        if (key === 'ArrowDown') {
            e.preventDefault();
            if (currentIndex < focusable.length - 1) {
                focusable[currentIndex + 1].focus();
            } else {
                firstEl.focus();
            }
            return;
        }

        if (key === 'ArrowUp') {
            e.preventDefault();
            if (currentIndex > 0) {
                focusable[currentIndex - 1].focus();
            } else {
                lastEl.focus();
            }
            return;
        }

        // Home / End
        if (key === 'Home') {
            e.preventDefault();
            firstEl.focus();
            return;
        }

        if (key === 'End') {
            e.preventDefault();
            lastEl.focus();
            return;
        }
    }

    /* ===========================================
       Event Listeners
       =========================================== */
    // Open button
    openBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (panel.classList.contains('panel-open')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Close button
    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            closeMenu();
        });
    }

    // Overlay click → close
    overlay.addEventListener('click', function () {
        closeMenu();
    });

    // Keyboard events
    document.addEventListener('keydown', handleKeydown);

    // Submenu toggle clicks
    if (navContainer) {
        navContainer.addEventListener('click', handleToggleClick);
        navContainer.addEventListener('focusin', handleToggleFocus);
    }

    // Auto-close on resize to desktop
    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (window.innerWidth > 992 && panel.classList.contains('panel-open')) {
                closeMenu();
            }
        }, 150);
    });

    /* ===========================================
       Initialize
       =========================================== */
    injectDropdownToggles();

})();
