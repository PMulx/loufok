(function () {
  const App = {
    DOM: {
      compteur: document.querySelector("#compteur"),
      textarea: document.querySelector("#texteContribution"),
    },
    longueur: 0,
    minCaracteres: 50,
    maxCaracteres: 280,

    app_init: function () {
      App.compteur_contribution();
    },
    compteur_contribution: function () {
      App.DOM.textarea.addEventListener("input", function () {
        App.longueur = App.DOM.textarea.value.length;
        App.DOM.compteur.textContent = App.longueur + " / 280 caractères";

        if (App.longueur < App.minCaracteres) {
          App.DOM.compteur.style.color = "red";
        } else if (App.longueur <= App.maxCaracteres) {
          App.DOM.compteur.style.color = "black";
        } else {
          App.DOM.compteur.style.color = "red";
        }
      });
    },
  };
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
