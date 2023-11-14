(function () {
  const App = {
    DOM: {
      compteur: document.querySelector("#compteur"),
      titre: document.querySelector("#titre"),
      textarea: document.querySelector("#texteContribution"),
      btnValider: document.querySelector(".button"),
      oeil_barre: document.getElementById("off"),
      oeil: document.getElementById("on"),
      contribution: document.querySelector(".contribution__div--txt"),
      nbContributions: document.getElementById("nbContributions"),
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
      App.textSize();
      App.title_Unique();
      App.period_Unique();
      App.nbContributions();
      // App.periode_cadavre();
    },
    textSize: function () {
      App.DOM.textarea.addEventListener("input", App.isFormValid);
    },

    // periode_cadavre: function () {
    //   App.DOM.dateDebut.addEventListener("change", function () {
    //     checkDate();
    //     App.isFormValid();
    //   });
    // },
    nbContributions: function () {
      App.DOM.nbContributions.addEventListener("input", App.isFormValid);
    },
    title_Unique: function () {
      App.DOM.titre.addEventListener("input", App.isFormValid);
    },
    period_Unique: function () {
      App.DOM.dateDebut.addEventListener("change", App.isFormValid);
      App.DOM.dateFin.addEventListener("change", App.isFormValid);
    },
    isFormValid: function () {
      App.title_Unique();
      App.period_Unique();
      App.textSize();
      App.nbContributions();
      // App.periode_cadavre();
      const titleValidation = checkUniqueTitle(App.DOM.titre.value);
      const periodValidation = checkUniquePeriod();
      const compteurValidation = compteur_contribution();
      const dateValidation = checkDate();
      const nbContributionsValidation = checknbContributions();
      // console.log(titleValidation);
      // console.log(periodValidation);
      // console.log(compteurValidation);
      // console.log(dateValidation);
      // console.log(nbContributionsValidation);
      if (
        titleValidation === "ok" &&
        periodValidation === "ok" &&
        compteurValidation === "ok" &&
        dateValidation === "ok" &&
        nbContributionsValidation === "ok"
      ) {
        console.log("Formulaire valide");
        App.activer_button();
      } else {
        // console.log("Formulaire non valide");
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

  function checkUniqueTitle(newTitle) {
    const titreError = document.getElementById("titreError");
    titreError.textContent = ""; // Réinitialiser le message d'erreur

    // Vérifier si le nouveau titre est déjà présent dans la liste des titres
    if (!newTitle.trim()) {
      return "error";
    } else if (titles.some((titleObj) => titleObj.titre_cadavre === newTitle)) {
      titreError.textContent =
        "Ce titre est déjà utilisé. Veuillez choisir un titre unique.";
      return "error";
    } else {
      return "ok";
    }
  }

  function checkUniquePeriod() {
    const dateDebutValue = App.DOM.dateDebut.value;
    const dateFinValue = App.DOM.dateFin.value;

    const periodeError = document.getElementById("messageErreur");
    periodeError.textContent = "";

    // Vérifier si les champs de date sont vides ou non valides
    if (!isValidDate(dateDebutValue) || !isValidDate(dateFinValue)) {
      return "error";
    }

    // Convertir en objets Date après la vérification de validité
    const dateDebut = new Date(dateDebutValue);
    const dateFin = new Date(dateFinValue);

    for (const periode of periodes) {
      const periodeStart = new Date(periode.start);
      const periodeEnd = new Date(periode.end);

      if (
        (dateDebut >= periodeStart && dateDebut <= periodeEnd) ||
        (dateFin >= periodeStart && dateFin <= periodeEnd) ||
        (dateDebut <= periodeStart && dateFin >= periodeEnd)
      ) {
        periodeError.textContent =
          "La période spécifiée chevauche une période existante.";
        return "error";
      }
    }

    // Si aucune erreur n'est détectée, renvoyer "ok"
    return "ok";
  }

  // Fonction pour vérifier la validité d'une date
  function isValidDate(dateString) {
    const regex = /^\d{4}-\d{2}-\d{2}$/; // Format YYYY-MM-DD
    return dateString.match(regex) !== null;
  }
  function compteur_contribution() {
    App.longueur = App.DOM.textarea.value.length;
    App.DOM.compteur.textContent = App.longueur + " / 280 caractères";

    if (App.longueur < App.minCaracteres) {
      App.DOM.compteur.style.color = "red";
      return "error";
    } else if (App.longueur <= App.maxCaracteres) {
      App.DOM.compteur.style.color = "black";
      return "ok";
    } else {
      App.DOM.compteur.style.color = "red";
      return "error";
    }
  }
  function checknbContributions() {
    const nbContributions = App.DOM.nbContributions.value;
    const ContributionsError = document.getElementById("ContributionsErreur");

    if (!nbContributions || nbContributions <= 1) {
      ContributionsError.textContent =
        "Le nombre de contributions doit être supérieur à 1";
    }
    if (!nbContributions || nbContributions > 1) {
      ContributionsError.textContent = "";
      return "ok";
    }

    return "erreur";
  }
  function checkDate() {
    App.dateDebutValue = new Date(App.DOM.dateDebut.value);
    App.dateFinValue = new Date(App.DOM.dateFin.value);
    App.dateDuJour = new Date();
    const jourDuJour = App.dateDuJour.getDate();
    const moisDuJour = App.dateDuJour.getMonth();
    const anneeDuJour = App.dateDuJour.getFullYear();

    const jourDebut = App.dateDebutValue.getDate();
    const moisDebut = App.dateDebutValue.getMonth();
    const anneeDebut = App.dateDebutValue.getFullYear();

    if (App.dateDebutValue > App.dateFinValue) {
      App.DOM.messageErreur.textContent =
        "La date de début ne peut pas être supérieure à la date de fin.";
      return "error";
    } else if (
      anneeDuJour === anneeDebut &&
      moisDuJour === moisDebut &&
      jourDuJour === jourDebut
    ) {
      return "error";
    } else if (App.dateDebutValue < App.dateDuJour) {
      App.DOM.messageErreur.textContent =
        "La date de début ne peut pas être antérieure à la date du jour.";
      return "error";
    } else {
      return "ok";
    }
  }
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
