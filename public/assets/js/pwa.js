async function registerServiceWorker() {
  if ("serviceWorker" in navigator) {
    try {
      const registration = await navigator.serviceWorker.register("/sw.js");
      let subscription = await registration.pushManager.getSubscription();
      console.log(JSON.stringify(subscription));

      if (!subscription) {
        subscription = await registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey:
            "BEy-mjvWHugUZsD9Gs6u3Bd4p-GoxY6VRKjf1kvJ9nOE2-8hdKXE8rnKboMsfEDPM9QK_R4cjtMZ55ea0Q6rIsY",
        });
      }

      // Envoyez la clé d'abonnement (subscription) au serveur pour le stockage
      console.log("Abonnement réussi:", subscription);
    } catch (error) {
      console.error("Erreur lors de l'abonnement au service worker:", error);
    }
  }
}

registerServiceWorker();

let installPrompt;
const pwaInstallButton = document.getElementById("pwaInstall");

window.addEventListener("beforeinstallprompt", (event) => {
  event.preventDefault();
  installPrompt = event;
  pwaInstallButton.classList.remove("hidden");
});

pwaInstallButton.addEventListener("click", async () => {
  if (!installPrompt) return;

  const result = await installPrompt.prompt();

  Notification.requestPermission().then((result) => {
    if (result === "granted") {
      console.log("Notifications granted");
    } else {
      console.log("Notifications refusées");
    }
  });

  installPrompt = null;
  pwaInstallButton.classList.add("hidden");
});

window.addEventListener("offline", (e) => {
  new Notification("Vous êtes hors ligne", {
    title: "Hors ligne",
    message: "Non accessible",
  });
});

// Récupérer le contenu du paragraphe
const notificationsElement = document.getElementById("notif");
const notificationsContent = notificationsElement.textContent.trim();

// Vérifier s'il y a du contenu dans le paragraphe
if (notificationsContent) {
  // Afficher la notification
  new Notification("Nouvelle notification", {
    body: notificationsContent,
  });
}
