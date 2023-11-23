// Récupérer le contenu actuel du textarea
var contributionTextarea = document.getElementById("texteContribution");

window.onload = function () {
  // Extraire l'ID du joueur de l'URL
  var currentUrl = window.location.href;
  var playerId = extractPlayerIdFromUrl(currentUrl);
  var roleUser = "joueur";

  var savedContribution = localStorage.getItem("contributions");
  var player_id = localStorage.getItem("id_joueur");
  var role_user = localStorage.getItem("role");
  if (savedContribution && player_id === playerId && role_user === roleUser) {
    contributionTextarea.value = savedContribution;
  }

  // Écouter les événements de changement dans le textarea
  contributionTextarea.addEventListener("input", function () {
    // Mettre à jour le contenu de localStorage à chaque changement
    var contributionText = contributionTextarea.value;
    localStorage.setItem("contributions", contributionText);
    localStorage.setItem("id_joueur", playerId);
    localStorage.setItem("role", roleUser);
  });
};

// Fonction pour extraire l'ID du joueur de l'URL
function extractPlayerIdFromUrl(url) {
  // Utilisez une expression régulière pour trouver un nombre à la fin de l'URL
  var match = url.match(/\/joueur\/(\d+)$/);

  if (match && match[1]) {
    return match[1];
  } else {
    console.error("Player ID not found in the URL");
    return null;
  }
}
