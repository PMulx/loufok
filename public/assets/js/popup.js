// Attendez que le DOM soit entièrement chargé avant d'exécuter le code
document.addEventListener("DOMContentLoaded", function () {
  // Sélectionnez les éléments du DOM nécessaires
  const popup = document.querySelector(".popupValidate"); // Popup principal
  const popup_texte = document.querySelector(".popup_texte"); // Contenu texte du popup

  // Vérifiez si l'élément du texte du popup existe et s'il n'est pas vide
  if (popup_texte && popup_texte.textContent.trim() !== "") {
    // Ajoutez la classe "show" pour activer la transition de la popup
    popup.classList.add("show");

    // Utilisez setTimeout pour retirer la classe "show" après 3 secondes (2000 millisecondes)
    setTimeout(function () {
      popup.classList.remove("show"); // Retirez la classe "show" pour masquer la popup
      popup = null; // Libérez la référence à l'élément popup pour libérer des ressources
    }, 2000);
  }
});
