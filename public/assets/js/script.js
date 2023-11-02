(function () {
  const App = {
    DOM: {
      compteur: document.querySelector("#compteur"),
      textarea: document.querySelector("#texteContribution"),
      btnValider: document.querySelector(".button"),
      oeil_barre: document.getElementById("off"),
      oeil: document.getElementById("on"),
      contribution: document.querySelector(".contribution__div--txt"),
      dateDebut: document.getElementById("dateDebut"),
      dateFin: document.getElementById("dateFin"),
      messageErreur: document.getElementById("messageErreur"),
      formulaire: document.querySelector("#form"),
    },
    longueur: 0,
    minCaracteres: 50,
    maxCaracteres: 280,
    dateDebutValue: null,
    dateFinValue: null,
    dateDuJour: null,
    app_init: function () {
      App.compteur_contribution();
      App.periode_cadavre();
      App.isFormValid();
      App.contribution_visible();
      App.contribution_invisible();
    },
    compteur_contribution: function () {
      App.DOM.textarea.addEventListener("input", function () {
        App.longueur = App.DOM.textarea.value.length;
        App.DOM.compteur.textContent = App.longueur + " / 280 caractères";

        if (App.longueur < App.minCaracteres) {
          App.DOM.compteur.style.color = "red";
          App.desactiver_button();
        } else if (App.longueur <= App.maxCaracteres) {
          App.DOM.compteur.style.color = "black";
        } else {
          App.DOM.compteur.style.color = "red";
          App.desactiver_button();
        }
        App.isFormValid();
      });
    },
    periode_cadavre: function () {
      App.DOM.dateDebut.addEventListener("change", function () {
        App.dateDebutValue = new Date(App.DOM.dateDebut.value);
        App.dateFinValue = new Date(App.DOM.dateFin.value);
        App.dateDuJour = new Date();
        // Extraction du jour, du mois et de l'année
        const jourDuJour = App.dateDuJour.getDate();
        const moisDuJour = App.dateDuJour.getMonth();
        const anneeDuJour = App.dateDuJour.getFullYear();

        const jourDebut = App.dateDebutValue.getDate();
        const moisDebut = App.dateDebutValue.getMonth();
        const anneeDebut = App.dateDebutValue.getFullYear();

        if (App.dateDebutValue > App.dateFinValue) {
          App.DOM.messageErreur.textContent =
            "La date de début ne peut pas être supérieure à la date de fin.";
        } else if (
          anneeDuJour === anneeDebut &&
          moisDuJour === moisDebut &&
          jourDuJour === jourDebut
        ) {
          App.activer_button();
          App.DOM.messageErreur.textContent = "";
        } else if (App.dateDebutValue < App.dateDuJour) {
          App.DOM.messageErreur.textContent =
            "La date de début ne peut pas être antérieure à la date du jour.";
        } else {
          App.DOM.messageErreur.textContent = "";
        }

        App.isFormValid();
      });

      App.DOM.dateFin.addEventListener("change", function () {
        App.dateDebutValue = new Date(App.DOM.dateDebut.value);
        App.dateFinValue = new Date(App.DOM.dateFin.value);
        App.dateDuJour = new Date();
        App.isFormValid();
        if (App.dateDebutValue > App.dateFinValue) {
          App.DOM.messageErreur.textContent =
            "La date de début ne peut pas être supérieure à la date de fin.";
        } else if (App.dateDebutValue < App.dateDuJour) {
          App.DOM.messageErreur.textContent =
            "La date de début ne peut pas être antérieure à la date du jour.";
        } else {
          App.DOM.messageErreur.textContent = "";
        }
      });
    },
    isFormValid: function () {
      const inputs = App.DOM.formulaire.querySelectorAll("input");
      const textarea = App.DOM.textarea;
      let tousLesChampsRemplis = true;
      // Extraction du jour, du mois et de l'année
      const jourDuJour = App.dateDuJour.getDate();
      const moisDuJour = App.dateDuJour.getMonth();
      const anneeDuJour = App.dateDuJour.getFullYear();

      const jourDebut = App.dateDebutValue.getDate();
      const moisDebut = App.dateDebutValue.getMonth();
      const anneeDebut = App.dateDebutValue.getFullYear();

      inputs.forEach(function (input) {
        if (input.value.trim() === "") {
          tousLesChampsRemplis = false;
          return;
        }
      });

      if (textarea.value.trim() === "") {
        tousLesChampsRemplis = false;
      }

      const isDateValid =
        anneeDuJour === anneeDebut &&
        moisDuJour === moisDebut &&
        jourDuJour === jourDebut;
      App.dateDebutValue <= App.dateFinValue &&
        App.dateDebutValue > App.dateDuJour;

      if (
        tousLesChampsRemplis &&
        App.longueur >= App.minCaracteres &&
        App.longueur <= App.maxCaracteres &&
        isDateValid
      ) {
        App.activer_button();
        App.DOM.messageErreur.textContent = "";
      } else {
        App.desactiver_button();
      }
      console.log(App.dateDebutValue);
      console.log(App.dateFinValue);
      console.log(App.dateDuJour);
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
    contribution_visible: function () {
      App.DOM.oeil_barre.addEventListener("click", function () {
        App.DOM.contribution.classList.remove("hidden");
        App.DOM.contribution.classList.add("show");
        App.DOM.oeil_barre.classList.add("hidden");
        App.DOM.oeil.classList.remove("hidden");
      });
    },
    contribution_invisible: function () {
      App.DOM.oeil.addEventListener("click", function () {
        App.DOM.contribution.classList.add("hidden");
        App.DOM.contribution.classList.remove("show");
        App.DOM.oeil_barre.classList.remove("hidden");
        App.DOM.oeil.classList.add("hidden");
      });
    },
  };

  window.addEventListener("DOMContentLoaded", App.app_init);
})();
