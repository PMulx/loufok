// Fonction auto-exécutante (IIFE) pour encapsuler le code dans une portée locale
(function () {
  // Objet principal de l'application
  const App = {
    // Éléments du DOM utilisés par l'application
    DOM: {
      compteur: document.querySelector("#compteur"), // Élément d'affichage du compteur
      textarea: document.querySelector("#texteContribution"), // Zone de texte de la contribution
    },
    longueur: 0, // Variable pour stocker la longueur du texte
    minCaracteres: 50, // Longueur minimale autorisée
    maxCaracteres: 280, // Longueur maximale autorisée

    // Fonction d'initialisation de l'application
    app_init: function () {
      App.compteur_contribution(); // Appeler la fonction pour initialiser le compteur
    },

    // Fonction pour mettre à jour le compteur de caractères et gérer les styles
    compteur_contribution: function () {
      // Ajouter un écouteur d'événements pour suivre les modifications de la zone de texte
      App.DOM.textarea.addEventListener("input", function () {
        // Mettre à jour la variable de longueur avec la longueur actuelle du texte
        App.longueur = App.DOM.textarea.value.length;

        // Mettre à jour l'affichage du compteur avec la longueur actuelle et la limite maximale
        App.DOM.compteur.textContent =
          App.longueur + " / " + App.maxCaracteres + " caractères";

        // Modifier la couleur du texte en fonction de la longueur
        if (App.longueur < App.minCaracteres) {
          App.DOM.compteur.style.color = "red"; // Texte en rouge si en dessous de la longueur minimale
        } else if (App.longueur <= App.maxCaracteres) {
          App.DOM.compteur.style.color = "black"; // Texte en noir si dans la plage autorisée
        } else {
          App.DOM.compteur.style.color = "red"; // Texte en rouge si au-dessus de la longueur maximale
        }
      });
    },
  };

  // Événement principal : exécuter l'initialisation lorsque le DOM est prêt
  window.addEventListener("DOMContentLoaded", App.app_init);
})();
