var contributionTextarea = document.getElementById("texteContribution");
var titre = document.getElementById("titre");
var nbContributions = document.getElementById("nbContributions");
var dateDebut = document.getElementById("dateDebut");
var dateFin = document.getElementById("dateFin");
var currentUrl = window.location.href;
var adminId = extractAdminIdFromUrl(currentUrl);
var roleUser = "admin";
var btnBrouillon = document.getElementById("btnBrouillon");

window.onload = function () {
  // Extraire l'ID de l'administrateur de l'URL
  var popup = document.querySelector(".popupValidate");
  if (popup && popup.classList.contains("show")) {
    resetLocalStorageValues();
  }

  var savedContribution = localStorage.getItem("contributions");
  var savedTitle = localStorage.getItem("title");
  var savedNbContributions = localStorage.getItem("nbContributions");
  var savedDateDebut = localStorage.getItem("dateDebut");
  var savedDateFin = localStorage.getItem("dateFin");
  var admin_id = localStorage.getItem("id_admin");
  var role_user = localStorage.getItem("role");

  // Vérifier si les conditions sont remplies pour restaurer les valeurs
  if (admin_id === adminId && role_user === roleUser) {
    contributionTextarea.value = savedContribution || "";
    titre.value = savedTitle || "";
    nbContributions.value = savedNbContributions || "";
    dateDebut.value = savedDateDebut || "";
    dateFin.value = savedDateFin || "";
  }

  btnBrouillon.addEventListener("click", saveToLocalStorage);
  // Ajoutez un gestionnaire d'événements pour le bouton "btnBrouillon"
  // qui appelle la fonction saveToLocalStorage lorsque le bouton est cliqué
  document
    .getElementById("formId")
    .addEventListener("submit", resetLocalStorageValues);
};

function updateLocalStorage() {
  var contributionText = contributionTextarea.value;
  var title = titre.value;
  var nbContributionsValue = nbContributions.value;
  var dateDebutValue = dateDebut.value;
  var dateFinValue = dateFin.value;

  localStorage.setItem("contributions", contributionText);
  localStorage.setItem("title", title);
  localStorage.setItem("nbContributions", nbContributionsValue);
  localStorage.setItem("dateDebut", dateDebutValue);
  localStorage.setItem("dateFin", dateFinValue);
  localStorage.setItem("id_admin", adminId);
  localStorage.setItem("role", roleUser);
}

function saveToLocalStorage() {
  // Ajoutez cette fonction pour enregistrer les valeurs dans le stockage local
  updateLocalStorage();
}

function resetLocalStorageValues() {
  // Ajoutez cette fonction pour réinitialiser les valeurs du stockage local
  localStorage.setItem("contributions", "");
  localStorage.setItem("title", "");
  localStorage.setItem("nbContributions", "");
  localStorage.setItem("dateDebut", "");
  localStorage.setItem("dateFin", "");
}

function extractAdminIdFromUrl(url) {
  var match = url.match(/\/administrateur\/(\d+)$/);
  if (match && match[1]) {
    return match[1];
  } else {
    console.error("Admin ID not found in the URL");
    return null;
  }
}
