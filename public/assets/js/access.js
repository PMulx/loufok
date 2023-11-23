let dyslexicBtn = document.getElementById("dyslexicCheck");
let seniorBtn = document.getElementById("seniorCheck");
let elementsToStyle = document.querySelectorAll(".access");

let accesmenu = document.getElementById("menu-access");

function savePreferences() {
  // Enregistrer les choix dans le localStorage
  localStorage.setItem("dyslexicChecked", dyslexicBtn.checked);
  localStorage.setItem("seniorChecked", seniorBtn.checked);
}

function loadPreferences() {
  // Charger les choix depuis le localStorage
  const dyslexicChecked = localStorage.getItem("dyslexicChecked");
  const seniorChecked = localStorage.getItem("seniorChecked");

  // Appliquer les choix s'ils existent
  if (dyslexicChecked === "true") {
    dyslexicBtn.checked = true;
    applyDyslexicFont();
  }

  if (seniorChecked === "true") {
    seniorBtn.checked = true;
    applySeniorMode();
  }
}
// Charger les préférences au chargement de la page
window.addEventListener("load", function () {
  loadPreferences();
});

document.getElementById("accessButton").addEventListener("click", function () {
  DMenu();
});

document.getElementById("croix").addEventListener("click", function () {
  DMenu();
});

dyslexicBtn.addEventListener("change", applyDyslexicFont);
seniorBtn.addEventListener("change", applySeniorMode);

function DMenu() {
  accesmenu.classList.toggle("d-none");
}

function applyDyslexicFont() {
  seniorBtn.checked = false;

  if (dyslexicBtn.checked) {
    elementsToStyle.forEach(function (element) {
      element.classList.remove("senior-mode");
      element.classList.add("dyslexic-font");
      element.style.fontSize = "";
    });
  } else {
    elementsToStyle.forEach(function (element) {
      element.classList.remove("dyslexic-font");
    });
  }

  // Enregistrer les préférences dans le localStorage
  savePreferences();
}

function applySeniorMode() {
  dyslexicBtn.checked = false;

  if (seniorBtn.checked) {
    elementsToStyle.forEach(function (element) {
      // Récupérer la taille de police actuelle de l'élément en pixels
      let currentSizePixels = parseFloat(
        window.getComputedStyle(element).fontSize
      );

      // Convertir la taille de police en pixels en "rem"
      let currentSizeRem =
        currentSizePixels /
        parseFloat(getComputedStyle(document.documentElement).fontSize);

      // Ajouter 0.3rem à la taille de police
      let newSizeRem = currentSizeRem + 0.3;

      // Appliquer la nouvelle taille de police à l'élément
      element.style.fontSize = newSizeRem + "rem";

      // Retirer la classe pour le mode dyslexique
      element.classList.remove("dyslexic-font");

      // Ajouter la classe pour le mode senior
      element.classList.add("senior-mode");
    });
  } else {
    elementsToStyle.forEach(function (element) {
      // Rétablir la taille de police de base pour chaque élément
      element.style.fontSize = "";

      // Retirer la classe pour le mode senior
      element.classList.remove("senior-mode");
    });
  }

  // Enregistrer les préférences dans le localStorage
  savePreferences();
}
