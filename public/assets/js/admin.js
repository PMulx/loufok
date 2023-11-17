(function () {
  // Objet principal de l'application
  const App = {
    // Propriétés DOM : éléments HTML utilisés fréquemment
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
    // Variables d'état et de configuration
    longueur: 0,
    minCaracteres: 50,
    maxCaracteres: 280,
    dateDebutValue: null,
    dateFinValue: null,
    dateDuJour: null,

    // Méthode d'initialisation de l'application
    app_init: function () {
      App.textSize();
      App.title_Unique();
      App.period_Unique();
      App.nbContributions();
    },

    // Gestionnaire d'événement pour la taille du texte
    textSize: function () {
      App.DOM.textarea.addEventListener("input", App.isFormValid);
    },

    // Gestionnaire d'événement pour le nombre de contributions
    nbContributions: function () {
      App.DOM.nbContributions.addEventListener("input", App.isFormValid);
    },

    // Gestionnaire d'événement pour le titre unique
    title_Unique: function () {
      App.DOM.titre.addEventListener("input", App.isFormValid);
    },

    // Gestionnaire d'événement pour la période unique
    period_Unique: function () {
      App.DOM.dateDebut.addEventListener("change", App.isFormValid);
      App.DOM.dateFin.addEventListener("change", App.isFormValid);
    },

    // Méthode de validation globale du formulaire
    isFormValid: function () {
      App.title_Unique();
      App.period_Unique();
      App.textSize();
      App.nbContributions();

      // Validation de chaque aspect du formulaire
      const titleValidation = checkUniqueTitle(App.DOM.titre.value);
      const periodValidation = checkUniquePeriod();
      const compteurValidation = compteur_contribution();
      const dateValidation = checkDate();
      const nbContributionsValidation = checknbContributions();

      // Activer ou désactiver le bouton en fonction des validations
      if (
        titleValidation === "ok" &&
        periodValidation === "ok" &&
        compteurValidation === "ok" &&
        dateValidation === "ok" &&
        nbContributionsValidation === "ok"
      ) {
        App.activer_button();
      } else {
        App.desactiver_button();
      }
    },

    // Méthode pour activer le bouton
    activer_button: function () {
      App.DOM.btnValider.removeAttribute("disabled");
      App.DOM.btnValider.classList.remove("button__disabled");
      App.DOM.btnValider.classList.add("button__validate");
    },

    // Méthode pour désactiver le bouton
    desactiver_button: function () {
      App.DOM.btnValider.classList.add("button__disabled");
      App.DOM.btnValider.setAttribute("disabled", "disabled");
      App.DOM.btnValider.classList.remove("button__validate");
    },
  };

  // Fonction pour vérifier l'unicité du titre
  function checkUniqueTitle(newTitle) {
    const titreError = document.getElementById("titreError");
    titreError.textContent = ""; // Réinitialiser le message d'erreur

    // Normaliser la nouvelle chaîne en minuscules et sans espaces
    const normalizedNewTitle = newTitle.trim().toLowerCase();

    // Vérifier si la nouvelle chaîne est déjà présente dans la liste des titres
    if (!normalizedNewTitle) {
      return "error"; // Titre vide, renvoyer une erreur
    } else if (
      titles.some(
        (titleObj) =>
          titleObj.titre_cadavre.toLowerCase().replace(/\s/g, "") ===
          normalizedNewTitle
      )
    ) {
      titreError.textContent =
        "Ce titre est déjà utilisé. Veuillez choisir un titre unique.";
      return "error"; // Titre non unique, renvoyer une erreur
    } else {
      return "ok"; // Titre unique, pas d'erreur
    }
  }
  // Fonction pour vérifier l'unicité de la période
  function checkUniquePeriod() {
    const dateDebutValue = App.DOM.dateDebut.value;
    const dateFinValue = App.DOM.dateFin.value;

    const periodeError = document.getElementById("messageErreur");
    periodeError.textContent = ""; // Réinitialiser le message d'erreur

    // Vérifier si les champs de date sont vides ou non valides
    if (!isValidDate(dateDebutValue) || !isValidDate(dateFinValue)) {
      return "error"; // Dates invalides, renvoyer une erreur
    }

    // Convertir en objets Date après la vérification de validité
    const dateDebut = new Date(dateDebutValue);
    const dateFin = new Date(dateFinValue);

    // Parcourir les périodes existantes pour vérifier les chevauchements
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
        return "error"; // Chevauchement de période, renvoyer une erreur
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
  // Fonction pour vérifier la longueur du texte
  function compteur_contribution() {
    App.longueur = App.DOM.textarea.value.length;

    if (App.longueur < App.minCaracteres) {
      return "error"; // Longueur insuffisante, renvoyer une erreur
    } else if (App.longueur <= App.maxCaracteres) {
      return "ok"; // Longueur valide, pas d'erreur
    } else {
      return "error"; // Longueur excessive, renvoyer une erreur
    }
  }

  // Fonction pour vérifier le nombre de contributions
  function checknbContributions() {
    const nbContributions = App.DOM.nbContributions.value;
    const ContributionsError = document.getElementById("ContributionsErreur");

    if (nbContributions) {
      if (nbContributions <= 1) {
        ContributionsError.textContent =
          "Le nombre de contributions doit être supérieur à 1";
        return "error"; // Nombre de contributions insuffisant, renvoyer une erreur}
      }
      if (!nbContributions || nbContributions > 1) {
        ContributionsError.textContent = "";
        return "ok"; // Nombre de contributions valide, pas d'erreur
      }
    }

    return "erreur";
  }
  // Fonction pour vérifier la validité des dates
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
      return "error"; // Date de début supérieure à la date de fin, renvoyer une erreur
    } else if (
      anneeDuJour === anneeDebut &&
      moisDuJour === moisDebut &&
      jourDuJour === jourDebut
    ) {
      return "ok"; // Date de début égale à la date du jour, renvoyer une erreur
    } else if (App.dateDebutValue < App.dateDuJour) {
      App.DOM.messageErreur.textContent =
        "La date de début ne peut pas être antérieure à la date du jour.";
      return "error"; // Date de début antérieure à la date du jour, renvoyer une erreur
    } else {
      return "ok"; // Dates valides, pas d'erreur
    }
  }
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
