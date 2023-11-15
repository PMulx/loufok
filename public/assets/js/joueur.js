// Fonction auto-exécutante (IIFE) pour encapsuler le code dans une portée locale
(function () {
  // Objet principal de l'application
  const App = {
    // Éléments du DOM utilisés par l'application
    DOM: {
      compteur: document.querySelector("#compteur"), // Élément d'affichage du compteur
      titre: document.querySelector("#titre"), // Élément du titre (non utilisé dans le code actuel)
      textarea: document.querySelector("#texteContribution"), // Zone de texte de la contribution
      btnValider: document.querySelector(".button"), // Bouton de validation
      formulaire: document.querySelector("#form"), // Formulaire (non utilisé dans le code actuel)
    },
    longueur: 0, // Variable pour stocker la longueur du texte
    minCaracteres: 50, // Longueur minimale autorisée
    maxCaracteres: 280, // Longueur maximale autorisée

    // Fonction d'initialisation de l'application
    app_init: function () {
      App.textSize(); // Appeler la fonction pour initialiser le suivi de la taille du texte
    },

    // Fonction pour mettre en place le suivi de la taille du texte
    textSize: function () {
      App.DOM.textarea.addEventListener("input", App.isFormValid); // Ajouter un écouteur d'événements pour les modifications de la zone de texte
    },

    // Fonction pour valider le formulaire en fonction de la taille du texte
    isFormValid: function () {
      App.textSize(); // Mettre à jour la taille du texte

      const compteurValidation = compteur_contribution(); // Vérifier la taille du texte

      if (compteurValidation === "ok") {
        App.activer_button(); // Activer le bouton si la validation est réussie
      } else {
        App.desactiver_button(); // Désactiver le bouton si la validation échoue
      }
    },

    // Fonction pour activer le bouton de validation
    activer_button: function () {
      App.DOM.btnValider.removeAttribute("disabled"); // Supprimer l'attribut "disabled" du bouton
      App.DOM.btnValider.classList.remove("button__disabled"); // Retirer la classe de style pour un bouton désactivé
      App.DOM.btnValider.classList.add("button__validate"); // Ajouter la classe de style pour un bouton validé
    },

    // Fonction pour désactiver le bouton de validation
    desactiver_button: function () {
      App.DOM.btnValider.classList.add("button__disabled"); // Ajouter la classe de style pour un bouton désactivé
      App.DOM.btnValider.setAttribute("disabled", "disabled"); // Ajouter l'attribut "disabled" au bouton
      App.DOM.btnValider.classList.remove("button__validate"); // Retirer la classe de style pour un bouton validé
    },
  };

  // Fonction pour vérifier la taille du texte et mettre à jour le compteur
  function compteur_contribution() {
    App.longueur = App.DOM.textarea.value.length; // Obtenir la longueur actuelle du texte
    App.DOM.compteur.textContent =
      App.longueur + " / " + App.maxCaracteres + " caractères"; // Mettre à jour l'affichage du compteur

    // Vérifier la plage de la longueur du texte et renvoyer le statut de validation correspondant
    if (App.longueur < App.minCaracteres) {
      return "error";
    } else if (App.longueur <= App.maxCaracteres) {
      return "ok";
    } else {
      return "error";
    }
  }

  // Événement principal : exécuter l'initialisation lorsque le DOM est prêt
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
