(function () {
    const App = {
    
      // les éléments du DOM
      DOM: {
        articles: document.querySelector(".articles"),
        load: document.querySelector("#load"),
        article_buttons: document.querySelector(".articles"),
        article_wrapper: document.querySelector(".article-wrapper"),
        close: document.querySelector(".article-wrapper .close"),
      },
      /**
       * Initialisation de l'application.
       */
      app_init: function () {
        App.app_handlers();
      },
      /**
       * Mise en place des gestionnaires d'évènements.
       */
      app_handlers: function () {
        App.DOM.load.addEventListener("click", App.loadArticles);
        // à compléter
      },
      /**
       * Charger les articles à partir de l'API.
       * @param {object} ev
       */
      loadArticles: (ev) => {
        fetch(
          `${App.URL}?q=maroc&from=2023-09-05&sortBy=popularity&apiKey=${App.API_KEY}`
        )
          .then((response) => response.json())
          .then((data) => {
            App.DOM.load.style.display = "none";
  
            data.articles.forEach((article) => {
              const articleElement = document.createElement("div");
              articleElement.classList.add("article");
  
              const titleElement = document.createElement("h2");
              titleElement.textContent = article.title;
  
              const sourceElement = document.createElement("p");
              sourceElement.textContent = `${article.source.name}`;
  
              const imageElement = document.createElement("img");
              imageElement.src = article.urlToImage;
              imageElement.alt = article.title;
  
              imageElement.setAttribute("loading", "lazy");
  
              const readMoreButton = document.createElement("button");
              readMoreButton.textContent = "En lire plus";
              readMoreButton.addEventListener("click", () => {
                App.viewArticleContent(article);
              });
  
              articleElement.appendChild(titleElement);
              articleElement.appendChild(sourceElement);
              articleElement.appendChild(imageElement);
              articleElement.appendChild(readMoreButton);
  
              App.DOM.articles.appendChild(articleElement);
            });
          })
          .catch((error) => {
            console.error(error);
          });
      },
      /**
       
       * @param {object} article
       */
      viewArticleContent: (article) => {
        App.DOM.articles.style.display = "none";
  
        const fullscreenDiv = document.createElement("div");
        fullscreenDiv.classList.add("fullscreen-article");
  
        const titleElement = document.createElement("h2");
        titleElement.textContent = article.title;
  
        const urlElement = document.createElement("a");
        urlElement.textContent = "Aller voir l'article";
        urlElement.href = article.url;
        urlElement.target = "_blank";
  
        const authorElement = document.createElement("h4");
        authorElement.textContent = article.author;
  
        const descriptionElement = document.createElement("p");
        descriptionElement.textContent = article.description;
  
        fullscreenDiv.appendChild(titleElement);
        fullscreenDiv.appendChild(urlElement);
        fullscreenDiv.appendChild(authorElement);
        fullscreenDiv.appendChild(descriptionElement);
  
        document.body.appendChild(fullscreenDiv);
  
        fullscreenDiv.style.width = "80%";
        fullscreenDiv.style.marginTop = "20px";
        fullscreenDiv.style.marginTop = "20px";
        fullscreenDiv.style.fontFamily = "gabriela";
        fullscreenDiv.style.height = "100%";
        fullscreenDiv.style.marginLeft = "auto";
        fullscreenDiv.style.marginRight = "auto";
        fullscreenDiv.style.display = "flex";
        fullscreenDiv.style.flexDirection = "column";
        fullscreenDiv.style.justifyContent = "space-around";
        urlElement.style.color = "rgb(102, 204, 153)";
        urlElement.style.marginTop = "10px";
        urlElement.style.marginBottom = "10px";
        authorElement.style.marginBottom = "10px";
  
        const closeButton = document.createElement("button");
        closeButton.textContent = "Fermer";
        fullscreenDiv.appendChild(closeButton);
  
        closeButton.addEventListener("click", () => {
          App.DOM.articles.style.display = "grid";
          document.body.removeChild(fullscreenDiv);
        });
      },
    };
    window.addEventListener("DOMContentLoaded", App.app_init);
  })();
  