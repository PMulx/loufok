// const cacheFirst = async ({ request, fallbackUrl }) => {
//   const responseFromCache = await caches.match(request);
//   if (responseFromCache) {
//     return responseFromCache;
//   }

//   try {
//     const responseFromNetwork = await fetch(request);
//     const cache = await caches.open("cache");
//     await cache.put(request, responseFromNetwork.clone());
//     return responseFromNetwork;
//   } catch (error) {
//     const fallbackResponse = await caches.match(fallbackUrl);
//     if (fallbackResponse) {
//       return fallbackResponse;
//     }
//     return new Response("Network error", {
//       status: 408,
//       headers: { "Content-Type": "text/plan" },
//     });
//   }
// };

// self.addEventListener("fetch", (event) => {
//   if (event.request.url.includes("push-notification")) {
//     event.respondWith(
//       (async () => {
//         try {
//           const responseFromNetwork = await fetch(event.request);
//           const cache = await caches.open("push-notifications-cache");
//           await cache.put(event.request, responseFromNetwork.clone());
//           return responseFromNetwork;
//         } catch (error) {
//           return new Response("Network error", {
//             status: 408,
//             headers: { "Content-Type": "text/plain" },
//           });
//         }
//       })()
//     );
//   } else {
//     event.respondWith(
//       cacheFirst({
//         request: event.request,
//         fallbackUrl: "fallback.html",
//       })
//     );
//   }
// });

// self.addEventListener("push", (event) => {
//   console.log("push event", event);
//   const data = event.data;
//   event.waitUntil(
//     self.registration.showNotification("Hello world", {
//       body: data.text(),
//     })
//   );
// });
