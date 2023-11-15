document.addEventListener("DOMContentLoaded", function () {
  const popup = document.querySelector(".popupValidate");
  const popup_texte = document.querySelector(".popup_texte");

  if (popupMessage) {
    // Affichez la popup ici en utilisant une bibliothèque de votre choix ou créez votre propre logique de popup
    popup_texte.textContent = popupMessage;

    // Ajoutez la classe "show" pour activer la transition
    popup.classList.add("show");

    // Utilisez setTimeout pour retirer la classe "show" après 3 secondes
    setTimeout(function () {
      popup.classList.remove("show");
      popup = null;
    }, 2000);
  }
});
