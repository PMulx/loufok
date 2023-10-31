(function () {
  const App = {
    // les éléments du DOM
    DOM: {
      compteur: document.querySelector("#compteur"),
      textarea: document.querySelector("#texteContribution"),
      btnValider: document.querySelector(".button"),
      oeil_barre: document.getElementById("off"),
      oeil: document.getElementById("on"),
      contribution: document.querySelector(".contribution__div--txt"),
    },
    /**
     * Initialisation de l'application.
     */
    app_init: function () {
      App.compteur_contribution();
      App.contribution_visible();
      App.contribution_invisible();
    },
    /**
     * Mise en place des gestionnaires d'évènements.
     */
    compteur_contribution: function () {
      App.DOM.textarea.addEventListener("input", function () {
        const longueur = App.DOM.textarea.value.length;
        App.DOM.compteur.textContent = longueur + " / 280 caractères";

        if (longueur < 50) {
          App.DOM.compteur.style.color = "red";
          App.desactiver_button(); // Désactiver le bouton si le compteur est en dessous de 50
        } else {
          App.DOM.compteur.style.color = "black";
          App.activer_button(); // Activer le bouton si le compteur est à 50 caractères ou plus
        }
      });
    },
    periode_cadavre: function () {
      const minCaracteres = 50;
      const maxCaracteres = 280;
      dateDebut.addEventListener("change", () => {
        const dateDebutValue = new Date(dateDebut.value);
        const dateFinValue = new Date(dateFin.value);

        if (dateDebutValue > dateFinValue) {
          messageErreur.textContent =
            "La date de début ne peut pas être supérieure à la date de fin.";
          btnValider.classList.add("disabled");
          btnValider.setAttribute("disabled", true);
        } else {
          messageErreur.textContent = "";
          if (
            textarea.value.length >= minCaracteres &&
            textarea.value.length <= maxCaracteres
          ) {
            btnValider.classList.remove("disabled");
            btnValider.removeAttribute("disabled");
          }
        }
      });

      dateFin.addEventListener("change", () => {
        const dateDebutValue = new Date(dateDebut.value);
        const dateFinValue = new Date(dateFin.value);

        if (dateDebutValue > dateFinValue) {
          messageErreur.textContent =
            "La date de début ne peut pas être supérieure à la date de fin.";
          btnValider.classList.add("disabled");
          btnValider.setAttribute("disabled", true);
        } else {
          messageErreur.textContent = "";
          if (
            textarea.value.length >= minCaracteres &&
            textarea.value.length <= maxCaracteres
          ) {
            btnValider.classList.remove("disabled");
            btnValider.removeAttribute("disabled");
          }
        }
      });
    },
    activer_button: function () {
      App.DOM.btnValider.removeAttribute("disabled");
      App.DOM.btnValider.classList.remove("button__disabled");
      App.DOM.btnValider.classList.add("button__validate");
    },
    desactiver_button: function () {
      App.DOM.btnValider.classList.add("button__disabled");
      App.DOM.btnValider.setAttribute("disabled", true);
      App.DOM.btnValider.classList.remove("button__validate");
    },
    contribution_visible: function () {
      App.DOM.oeil_barre.addEventListener("click", function () {
        // Basculez entre les classes hidden et show sur l'élément p
        App.DOM.contribution.classList.toggle("hidden");
        App.DOM.contribution.classList.toggle("show");

        // Basculez les classes hidden sur les icônes
        App.DOM.oeil_barre.classList.add("hidden");
        App.DOM.oeil.classList.remove("hidden");
      });
    },
    contribution_invisible: function () {
      App.DOM.oeil.addEventListener("click", function () {
        // Basculez entre les classes hidden et show sur l'élément p
        App.DOM.contribution.classList.add("hidden");
        App.DOM.contribution.classList.remove("show");

        // Basculez les classes hidden sur les icônes
        App.DOM.oeil_barre.classList.remove("hidden");
        App.DOM.oeil.classList.add("hidden");
      });
    },
  };
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
