var contributionTextarea = document.getElementById("texteContribution");
var currentUrl = window.location.href;
var joueurId = extractAdminIdFromUrl(currentUrl);
var roleUser = "joueur";
var btnBrouillon = document.getElementById("btnBrouillon");
var checkmark = document.querySelector(".checkmark");

window.onload = function () {
  // Extraire l'ID de l'administrateur de l'URL
  var popup = document.querySelector(".popupValidate");
  if (popup && popup.classList.contains("show")) {
    resetLocalStorageValues();
  }

  var savedContribution = localStorage.getItem("contributions");
  var id_joueur = localStorage.getItem("id_joueur");
  var role_user = localStorage.getItem("role");

  // Vérifier si les conditions sont remplies pour restaurer les valeurs
  if (id_joueur === joueurId && role_user === roleUser) {
    contributionTextarea.value = savedContribution || "";
  }

  contributionTextarea.addEventListener("input", valueChanged);

  btnBrouillon.addEventListener("click", saveToLocalStorage);
  document
    .getElementById("formId")
    .addEventListener("submit", resetLocalStorageValues);
};

function valueChanged() {
  btnBrouillon.classList.remove("clicked");
  checkmark.style.display = "none";
}

function updateLocalStorage() {
  var contributionText = contributionTextarea.value;

  localStorage.setItem("contributions", contributionText);
  localStorage.setItem("id_joueur", joueurId);
  localStorage.setItem("role", roleUser);
}

function saveToLocalStorage() {
  // Ajoutez cette fonction pour enregistrer les valeurs dans le stockage local

  btnBrouillon.addEventListener("click", function () {
    btnBrouillon.classList.add("clicked");
    checkmark.style.display = "inline-block";
  });
  updateLocalStorage();
}

function resetLocalStorageValues() {
  // Ajoutez cette fonction pour réinitialiser les valeurs du stockage local
  localStorage.setItem("contributions", "");
}

function extractAdminIdFromUrl(url) {
  var match = url.match(/\/joueur\/(\d+)$/);
  if (match && match[1]) {
    return match[1];
  } else {
    console.error("Joueur ID not found in the URL");
    return null;
  }
}
