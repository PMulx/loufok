const networkFirst = async ({ request, fallbackUrl }) => {
  try {
    const responseFromNetwork = await fetch(request);
    const cache = await caches.open("cache");

    await cache.put(request, responseFromNetwork.clone());

    return responseFromNetwork;
  } catch (error) {
    const responseFromCache = await caches.match(request);

    if (responseFromCache) {
      return responseFromCache;
    }

    const fallbackResponse = await caches.match(fallbackUrl);

    if (fallbackResponse) {
      return fallbackResponse;
    }

    return new Response("Network error", {
      status: 408,
      headers: { "Content-Type": "text/plain" },
    });
  }
};
