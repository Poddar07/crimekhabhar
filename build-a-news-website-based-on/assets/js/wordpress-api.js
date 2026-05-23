(function () {
  const config = window.CRIME_KHABAR_CONFIG || {};
  const wordpressUrl = (config.wordpressUrl || "").replace(/\/$/, "");
  const postsPerPage = config.postsPerPage || 12;

  if (!wordpressUrl || wordpressUrl.includes("example.com")) {
    return;
  }

  const main = document.querySelector(".main-content");
  const status = document.querySelector(".filter-status");

  if (!main) {
    return;
  }

  function stripTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html || "";
    return div.textContent.trim();
  }

  function getFeaturedImage(post, size) {
    const media = post._embedded && post._embedded["wp:featuredmedia"] && post._embedded["wp:featuredmedia"][0];

    if (!media) {
      return "assets/images/crime-khabar-logo.jpeg";
    }

    if (media.media_details && media.media_details.sizes && media.media_details.sizes[size]) {
      return media.media_details.sizes[size].source_url;
    }

    return media.source_url || "assets/images/crime-khabar-logo.jpeg";
  }

  function getCategories(post) {
    const terms = post._embedded && post._embedded["wp:term"] && post._embedded["wp:term"][0];

    if (!terms || !terms.length) {
      return { label: "News", slugs: "news" };
    }

    return {
      label: terms[0].name,
      slugs: terms.map((term) => term.slug).join(" ")
    };
  }

  function renderLead(post) {
    const cats = getCategories(post);

    return `
      <article class="story-card featured" data-category="${cats.slugs}">
        <a class="story-media" href="${post.link}">
          <img src="${getFeaturedImage(post, "large")}" alt="">
          <span class="media-badge">${cats.label}</span>
        </a>
        <div class="story-body">
          <h1><a href="${post.link}">${stripTags(post.title.rendered)}</a></h1>
          <p class="summary">${stripTags(post.excerpt.rendered).slice(0, 180)}</p>
          <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span><span>•</span><span>WordPress</span></div>
        </div>
      </article>
    `;
  }

  function renderMini(post) {
    const cats = getCategories(post);

    return `
      <article class="story-mini" data-category="${cats.slugs}">
        <a class="thumb" href="${post.link}">
          <img src="${getFeaturedImage(post, "thumbnail")}" alt="">
        </a>
        <div>
          <h3><a href="${post.link}">${stripTags(post.title.rendered)}</a></h3>
          <div class="meta"><span>${cats.label}</span><span>•</span><span>ताजा</span></div>
        </div>
      </article>
    `;
  }

  function renderCard(post) {
    const cats = getCategories(post);

    return `
      <article class="story-card" data-category="${cats.slugs}">
        <a class="story-media" href="${post.link}">
          <img src="${getFeaturedImage(post, "medium_large")}" alt="">
          <span class="media-badge">${cats.label}</span>
        </a>
        <div class="story-body">
          <h3><a href="${post.link}">${stripTags(post.title.rendered)}</a></h3>
          <p class="summary">${stripTags(post.excerpt.rendered).slice(0, 120)}</p>
          <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span></div>
        </div>
      </article>
    `;
  }

  async function loadWordPressPosts() {
    if (status) {
      status.textContent = "WordPress से खबरें लोड हो रही हैं...";
    }

    const response = await fetch(`${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=${postsPerPage}`);

    if (!response.ok) {
      throw new Error("WordPress API request failed");
    }

    const posts = await response.json();

    if (!posts.length) {
      if (status) {
        status.textContent = "WordPress में अभी कोई पोस्ट नहीं मिली।";
      }
      return;
    }

    const lead = posts[0];
    const miniPosts = posts.slice(1, 4);
    const gridPosts = posts.slice(4, 10);

    const leadGrid = document.querySelector(".lead-grid");
    const newsGrid = document.querySelector(".news-grid");

    if (leadGrid) {
      leadGrid.innerHTML = `${renderLead(lead)}<div class="stacked-news">${miniPosts.map(renderMini).join("")}</div>`;
    }

    if (newsGrid) {
      newsGrid.innerHTML = gridPosts.map(renderCard).join("");
    }

    if (status) {
      status.textContent = "WordPress से ताजा खबरें दिखाई जा रही हैं";
    }
  }

  loadWordPressPosts().catch(function () {
    if (status) {
      status.textContent = "WordPress API कनेक्ट नहीं हुआ। अभी डेमो खबरें दिखाई जा रही हैं।";
    }
  });
})();
