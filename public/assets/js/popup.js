document.addEventListener("DOMContentLoaded", function () {
  const popup = document.querySelector(".popupValidate");
  const popup_texte = document.querySelector(".popup_texte");

  if (popup_texte && popup_texte.textContent.trim() !== "") {
    // Ajoutez la classe "show" pour activer la transition
    popup.classList.add("show");

    // Utilisez setTimeout pour retirer la classe "show" après 3 secondes
    setTimeout(function () {
      popup.classList.remove("show");
      popup = null;
    }, 2000);
  }
});
