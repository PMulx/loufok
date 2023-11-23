async function registerServiceWorker() {
  if ("serviceWorker" in navigator) {
    try {
      const registration = await navigator.serviceWorker.register("/sw.js");
      let subscription = await registration.pushManager.getSubscription();

      if (!subscription) {
        const publicKey =
          "BEy-mjvWHugUZsD9Gs6u3Bd4p-GoxY6VRKjf1kvJ9nOE2-8hdKXE8rnKboMsfEDPM9QK_R4cjtMZ55ea0Q6rIsY";
        try {
          subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicKey),
          });

          // Envoyez la clé d'abonnement (subscription) au serveur pour le stockage
          sendSubscriptionToServer(subscription);
        } catch (error) {
          console.error("Erreur lors de l'abonnement push:", error);
        }
      }
    } catch (error) {
      console.error("Erreur lors de l'inscription du service worker:", error);
    }
  }
}

function urlBase64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");

  const rawData = atob(base64);
  const buffer = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    buffer[i] = rawData.charCodeAt(i);
  }

  return buffer;
}

function sendSubscriptionToServer(subscription) {
  // Envoyez la clé d'abonnement (subscription) au serveur pour le stockage
  // Utilisez une requête AJAX ou tout autre moyen pour envoyer la clé au serveur
  fetch("/subscribe", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(subscription),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(
          "Erreur lors de l'envoi de la clé d'abonnement au serveur."
        );
      }
    })
    .catch((error) => {
      console.error(
        "Erreur lors de l'envoi de la clé d'abonnement au serveur:",
        error
      );
    });
}

// Le reste du code pour la gestion de l'installation et des clics sur les notifications reste inchangé

registerServiceWorker();

let installPrompt;
const pwaInstallButton = document.getElementById("pwaInstall");

if (pwaInstallButton) {
  window.addEventListener("beforeinstallprompt", (event) => {
    event.preventDefault();
    installPrompt = event;
    pwaInstallButton.classList.remove("hidden");
  });
}

if (pwaInstallButton) {
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
}
