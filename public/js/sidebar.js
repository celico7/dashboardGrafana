class DynamicSidebar {
    constructor(sidebarSelector, mainContentSelector, toggleButtonId, storageKey) {
        this.sidebar = document.querySelector(sidebarSelector);
        this.mainContent = document.querySelector(mainContentSelector);
        this.toggleButton = null;
        this.storageKey = storageKey;
        this.isCollapsed = this.loadState();

        this.init(toggleButtonId);
    }

    init(toggleButtonId) {
        // Créer le bouton toggle
        this.createToggleButton(toggleButtonId);

        // Ajouter les classes nécessaires
        this.sidebar.classList.add('sidebar-dynamic');
        this.mainContent.classList.add('main-content-dynamic');

        // Appliquer l'état initial
        if (this.isCollapsed) {
            this.collapse(false);
        }

        // Ajouter l'event listener
        this.toggleButton.addEventListener('click', () => this.toggle());
    }

    createToggleButton(buttonId) {
        // Créer le bouton
        this.toggleButton = document.createElement('button');
        this.toggleButton.id = buttonId;
        this.toggleButton.className = 'sidebar-toggle-btn';
        this.toggleButton.setAttribute('aria-label', 'Basculer la sidebar');

        this.updateIcon();

        // Ajouter au DOM - dans le main content en haut à gauche
        this.mainContent.insertBefore(this.toggleButton, this.mainContent.firstChild);
    }

    toggle() {
        this.isCollapsed = !this.isCollapsed;

        if (this.isCollapsed) {
            this.collapse(true);
        } else {
            this.expand(true);
        }

        // Sauvegarder l'état
        this.saveState();

        // Changer l'icône
        this.updateIcon();
    }

    setTransitions(enabled) {
        const value = enabled ? '' : 'none';
        this.sidebar.style.transition = value;
        this.mainContent.style.transition = value;
        if (!enabled) {
            this.sidebar.offsetHeight; 
        }
    }

    collapse(animate = true) {
        if (!animate) this.setTransitions(false);

        this.sidebar.classList.add('collapsed');
        this.mainContent.classList.add('expanded');
        this.toggleButton.classList.add('sidebar-collapsed');

        if (!animate) this.setTransitions(true);
    }

    expand(animate = true) {
        if (!animate) this.setTransitions(false);

        this.sidebar.classList.remove('collapsed');
        this.mainContent.classList.remove('expanded');
        this.toggleButton.classList.remove('sidebar-collapsed');

        if (!animate) this.setTransitions(true);
    }

    updateIcon() {
        if (this.isCollapsed) {
            // Icône pour ouvrir (flèche droite)
            this.toggleButton.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            `;
        } else {
            // Icône hamburger
            this.toggleButton.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            `;
        }
    }

    saveState() {
        try {
            localStorage.setItem(this.storageKey, this.isCollapsed ? 'true' : 'false');
        } catch (e) {
            console.warn('Cannot save sidebar state to localStorage', e);
        }
    }

    loadState() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            return saved === 'true';
        } catch (e) {
            console.warn('Cannot load sidebar state from localStorage', e);
            return false;
        }
    }
}

// Initialisation automatique au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Pour la page élève
    if (document.querySelector('.sidebar')) {
        new DynamicSidebar('.sidebar', '.main-content', 'sidebar-toggle-eleve', 'sidebar-state-eleve');
    }

    // Pour la page prof
    if (document.querySelector('.prof-sidebar')) {
        new DynamicSidebar('.prof-sidebar', '.prof-main-content', 'sidebar-toggle-prof', 'sidebar-state-prof');
    }
});
