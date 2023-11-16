// Sélection des éléments du DOM
const emailField = document.getElementById("email");
const passwordField = document.getElementById("mdp");
const btnValider = document.getElementById("btnValider");

// Ajout d'écouteurs d'événements pour les champs email et mot de passe
emailField.addEventListener("input", toggleButtonState);
passwordField.addEventListener("input", toggleButtonState);

// Fonction pour basculer l'état du bouton en fonction de la saisie
function toggleButtonState() {
  // Vérification si les champs email et mot de passe ne sont pas vides après suppression des espaces
  if (emailField.value.trim() !== "" && passwordField.value.trim() !== "") {
    // Activation du bouton
    btnValider.classList.remove("disabled");
    btnValider.removeAttribute("disabled");
    btnValider.classList.add("button__validate");
  } else {
    // Désactivation du bouton
    btnValider.classList.remove("button__validate");
    btnValider.classList.add("disabled");
    btnValider.setAttribute("disabled", "disabled");
  }
}
