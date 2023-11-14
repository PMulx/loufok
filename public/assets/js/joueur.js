(function () {
  const App = {
    DOM: {
      compteur: document.querySelector("#compteur"),
      titre: document.querySelector("#titre"),
      textarea: document.querySelector("#texteContribution"),
      btnValider: document.querySelector(".button"),

      formulaire: document.querySelector("#form"),
    },
    longueur: 0,
    minCaracteres: 50,
    maxCaracteres: 280,

    app_init: function () {
      App.textSize();
    },
    textSize: function () {
      App.DOM.textarea.addEventListener("input", App.isFormValid);
    },

    isFormValid: function () {
      App.textSize();

      const compteurValidation = compteur_contribution();

      if (compteurValidation === "ok") {
        App.activer_button();
      } else {
        App.desactiver_button();
      }
    },
    activer_button: function () {
      App.DOM.btnValider.removeAttribute("disabled");
      App.DOM.btnValider.classList.remove("button__disabled");
      App.DOM.btnValider.classList.add("button__validate");
    },
    desactiver_button: function () {
      App.DOM.btnValider.classList.add("button__disabled");
      App.DOM.btnValider.setAttribute("disabled", "disabled");
      App.DOM.btnValider.classList.remove("button__validate");
    },
  };

  function compteur_contribution() {
    App.longueur = App.DOM.textarea.value.length;
    App.DOM.compteur.textContent = App.longueur + " / 280 caractères";

    if (App.longueur < App.minCaracteres) {
      return "error";
    } else if (App.longueur <= App.maxCaracteres) {
      return "ok";
    } else {
      return "error";
    }
  }

  window.addEventListener("DOMContentLoaded", App.app_init);
})();
