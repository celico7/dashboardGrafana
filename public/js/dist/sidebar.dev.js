"use strict";

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var DynamicSidebar =
/*#__PURE__*/
function () {
  function DynamicSidebar(sidebarSelector, mainContentSelector, toggleButtonId, storageKey) {
    _classCallCheck(this, DynamicSidebar);

    this.sidebar = document.querySelector(sidebarSelector);
    this.mainContent = document.querySelector(mainContentSelector);
    this.toggleButton = null;
    this.storageKey = storageKey;
    this.isCollapsed = this.loadState();
    this.init(toggleButtonId);
  }

  _createClass(DynamicSidebar, [{
    key: "init",
    value: function init(toggleButtonId) {
      var _this = this;

      // Créer le bouton toggle
      this.createToggleButton(toggleButtonId); // Ajouter les classes nécessaires

      this.sidebar.classList.add('sidebar-dynamic');
      this.mainContent.classList.add('main-content-dynamic'); // Appliquer l'état initial

      if (this.isCollapsed) {
        this.collapse(false);
      } // Ajouter l'event listener


      this.toggleButton.addEventListener('click', function () {
        return _this.toggle();
      });
    }
  }, {
    key: "createToggleButton",
    value: function createToggleButton(buttonId) {
      // Créer le bouton
      this.toggleButton = document.createElement('button');
      this.toggleButton.id = buttonId;
      this.toggleButton.className = 'sidebar-toggle-btn';
      this.toggleButton.setAttribute('aria-label', 'Basculer la sidebar');
      this.updateIcon(); // Ajouter au DOM - dans le main content en haut à gauche

      this.mainContent.insertBefore(this.toggleButton, this.mainContent.firstChild);
    }
  }, {
    key: "toggle",
    value: function toggle() {
      this.isCollapsed = !this.isCollapsed;

      if (this.isCollapsed) {
        this.collapse(true);
      } else {
        this.expand(true);
      } // Sauvegarder l'état


      this.saveState(); // Changer l'icône

      this.updateIcon();
    }
  }, {
    key: "setTransitions",
    value: function setTransitions(enabled) {
      var value = enabled ? '' : 'none';
      this.sidebar.style.transition = value;
      this.mainContent.style.transition = value;

      if (!enabled) {
        this.sidebar.offsetHeight;
      }
    }
  }, {
    key: "collapse",
    value: function collapse() {
      var animate = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      if (!animate) this.setTransitions(false);
      this.sidebar.classList.add('collapsed');
      this.mainContent.classList.add('expanded');
      this.toggleButton.classList.add('sidebar-collapsed');
      if (!animate) this.setTransitions(true);
    }
  }, {
    key: "expand",
    value: function expand() {
      var animate = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      if (!animate) this.setTransitions(false);
      this.sidebar.classList.remove('collapsed');
      this.mainContent.classList.remove('expanded');
      this.toggleButton.classList.remove('sidebar-collapsed');
      if (!animate) this.setTransitions(true);
    }
  }, {
    key: "updateIcon",
    value: function updateIcon() {
      if (this.isCollapsed) {
        // Icône pour ouvrir (flèche droite)
        this.toggleButton.innerHTML = "\n                <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">\n                    <polyline points=\"9 18 15 12 9 6\"></polyline>\n                </svg>\n            ";
      } else {
        // Icône hamburger
        this.toggleButton.innerHTML = "\n                <svg viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\">\n                    <line x1=\"3\" y1=\"6\" x2=\"21\" y2=\"6\"></line>\n                    <line x1=\"3\" y1=\"12\" x2=\"21\" y2=\"12\"></line>\n                    <line x1=\"3\" y1=\"18\" x2=\"21\" y2=\"18\"></line>\n                </svg>\n            ";
      }
    }
  }, {
    key: "saveState",
    value: function saveState() {
      try {
        localStorage.setItem(this.storageKey, this.isCollapsed ? 'true' : 'false');
      } catch (e) {
        console.warn('Cannot save sidebar state to localStorage', e);
      }
    }
  }, {
    key: "loadState",
    value: function loadState() {
      try {
        var saved = localStorage.getItem(this.storageKey);
        return saved === 'true';
      } catch (e) {
        console.warn('Cannot load sidebar state from localStorage', e);
        return false;
      }
    }
  }]);

  return DynamicSidebar;
}(); // Initialisation automatique au chargement de la page


document.addEventListener('DOMContentLoaded', function () {
  // Pour la page élève
  if (document.querySelector('.sidebar')) {
    new DynamicSidebar('.sidebar', '.main-content', 'sidebar-toggle-eleve', 'sidebar-state-eleve');
  } // Pour la page prof


  if (document.querySelector('.prof-sidebar')) {
    new DynamicSidebar('.prof-sidebar', '.prof-main-content', 'sidebar-toggle-prof', 'sidebar-state-prof');
  }
});
//# sourceMappingURL=sidebar.dev.js.map
