(function () {
  const config = window.CRIME_KHABAR_CONFIG || {};
  const wordpressUrl = (config.wordpressUrl || "").replace(/\/$/, "");
  let categories = [];
  let recommendedPost = null;

  init();

  async function initShell() {
    categories = await loadCategories();
    renderShell();
  }

  function renderShell() {
    const menu = document.querySelector(".main-menu");
    const menuCategories = categories.length ? categories : config.categories || [];

    if (menu && menuCategories.length) {
      menu.innerHTML = [
        `<li><a href="index.html">होम</a></li>`,
        ...menuCategories.slice(0, 6).map((item) => `<li><a href="${categoryUrl(item)}">${escapeHtml(item.label)}</a></li>`)
      ].join("");
    }

    document.querySelectorAll("[data-weather-temp]").forEach((node) => {
      node.textContent = config.weatherTemperature || "34°C";
    });
  }

  function getIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id");
  }

  function categoryUrl(item) {
    const slug = item && item.slugs && item.slugs.length ? item.slugs[0] : item && item.slug ? item.slug : "";
    return slug ? `category.html?category=${encodeURIComponent(slug)}` : "category.html";
  }

  async function loadCategories() {
    if (!wordpressUrl) {
      return config.categories || [];
    }

    try {
      const urls = [
        `${wordpressUrl}/wp-json/wp/v2/categories?per_page=100&hide_empty=false`,
        `${wordpressUrl}/index.php?rest_route=/wp/v2/categories&per_page=100&hide_empty=false`
      ];
      const terms = await fetchJson(urls);
      return buildCategoryTree(terms);
    } catch (error) {
      console.warn("Failed to load categories from WordPress, falling back to config.", error);
      return config.categories || [];
    }
  }

  function buildCategoryTree(terms) {
    if (!Array.isArray(terms)) {
      return [];
    }

    const lookup = {};
    terms.forEach((term) => {
      lookup[term.id] = {
        id: term.id,
        key: term.slug,
        label: term.name,
        slugs: [term.slug],
        parent: term.parent || 0,
        children: []
      };
    });

    const tree = [];
    terms.forEach((term) => {
      const item = lookup[term.id];
      if (term.parent && lookup[term.parent]) {
        lookup[term.parent].children.push(item);
      } else {
        tree.push(item);
      }
    });

    return tree;
  }

  async function fetchJson(urls) {
    let lastError;

    for (const url of urls) {
      try {
        const response = await fetch(url);
        if (response.ok) {
          return response.json();
        }
        lastError = new Error(`WordPress API request failed: ${response.status}`);
      } catch (error) {
        lastError = error;
      }
    }

    throw lastError || new Error("WordPress API request failed");
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text || "";
    return div.innerHTML;
  }

  function escapeAttr(text) {
    return escapeHtml(text).replace(/"/g, "&quot;");
  }

  function stripTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html || "";
    return div.textContent.trim();
  }

  function getFeaturedImage(post, size) {
    const media = post._embedded && post._embedded["wp:featuredmedia"] && post._embedded["wp:featuredmedia"][0];

    if (!media) {
      return "";
    }

    if (media.media_details && media.media_details.sizes && media.media_details.sizes[size]) {
      return media.media_details.sizes[size].source_url;
    }

    return media.source_url || "";
  }

  function renderImage(src) {
    return src ? `<img src="${escapeAttr(src)}" alt="">` : "";
  }

  function getCategoryIds(post) {
    return Array.isArray(post.categories) ? post.categories : [];
  }

  function renderAdSlot() {
    document.querySelectorAll("[data-ad-slots]").forEach((container) => {
      container.innerHTML = "";
    });
  }

  async function loadRecommendedPost(currentPost) {
    if (!wordpressUrl) {
      return null;
    }

    try {
      const urls = [
        `${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=4&exclude=${currentPost.id}`,
        `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&_embed=1&per_page=4&exclude=${currentPost.id}`
      ];
      const posts = await fetchJson(urls);

      if (Array.isArray(posts) && posts.length) {
        return posts[0];
      }
    } catch (error) {
      console.warn("Failed to load recommended post.", error);
    }

    return null;
  }

  async function fetchJson(urls) {
    let lastError;

    for (const url of urls) {
      try {
        const response = await fetch(url);

        if (response.ok) {
          return response.json();
        }

        lastError = new Error(`WordPress API request failed: ${response.status}`);
      } catch (error) {
        lastError = error;
      }
    }

    throw lastError || new Error("WordPress API request failed");
  }

  function fetchPost(id) {
    return fetchJson([
      `${wordpressUrl}/wp-json/wp/v2/posts/${encodeURIComponent(id)}?_embed=1`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/posts/${encodeURIComponent(id)}&_embed=1`
    ]);
  }

  function fetchRelated(post) {
    const categories = getCategoryIds(post).slice(0, 3).join(",");
    const categoryParam = categories ? `&categories=${categories}` : "";

    return fetchJson([
      `${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=6&exclude=${post.id}${categoryParam}`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&_embed=1&per_page=6&exclude=${post.id}${categoryParam}`
    ]);
  }

  function collectCategoryIdsBySlug(items, slug) {
    for (const item of items) {
      if (item.key === slug) {
        return collectCategoryIds(item);
      }

      const childIds = collectCategoryIdsBySlug(item.children || [], slug);
      if (childIds.length) {
        return childIds;
      }
    }

    return [];
  }

  function collectCategoryIds(item) {
    return [item.id].concat((item.children || []).flatMap(collectCategoryIds));
  }

  function fetchPostsByCategoryIds(categoryIds) {
    const categoryParam = categoryIds.join(",");

    return fetchJson([
      `${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=4&categories=${encodeURIComponent(categoryParam)}`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&_embed=1&per_page=4&categories=${encodeURIComponent(categoryParam)}`
    ]);
  }

  async function renderBiharTrending() {
    const lists = document.querySelectorAll("[data-bihar-trending]");

    if (!lists.length) {
      return;
    }

    const categoryIds = collectCategoryIdsBySlug(categories, "bihar");
    const posts = categoryIds.length ? await fetchPostsByCategoryIds(categoryIds).catch(() => []) : [];
    const html = posts
      .slice(0, 4)
      .map(
        (post, index) => `
          <li>
            <span class="rank">${index + 1}</span>
            <a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a>
          </li>
        `
      )
      .join("");

    lists.forEach((list) => {
      list.innerHTML = html;
    });
  }

  function postUrl(post) {
    return `detail.html?id=${encodeURIComponent(post.id)}`;
  }

  function renderArticle(post) {
    const container = document.getElementById("article-container");

    if (!container) {
      return;
    }

    container.innerHTML = `
      <div class="article-media">
        ${renderImage(getFeaturedImage(post, "large"))}
      </div>
      <div class="story-body">
        <h1>${escapeHtml(stripTags(post.title.rendered))}</h1>
        <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span></div>
        <div class="article-content">${post.content.rendered || ""}</div>
      </div>
    `;

    setupShare(post);
  }

  function renderRelated(posts) {
    const related = document.getElementById("related-articles");
    const readNext = document.getElementById("read-next-list");

    if (related) {
      related.innerHTML = posts
        .slice(0, 3)
        .map((post) => `
          <article class="story-card">
            <a class="story-media" href="${postUrl(post)}">${renderImage(getFeaturedImage(post, "medium_large"))}</a>
            <div class="story-body">
              <h3><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></h3>
              <p class="summary">${escapeHtml(stripTags(post.excerpt.rendered).slice(0, 120))}</p>
            </div>
          </article>
        `)
        .join("");
    }

    if (readNext) {
      readNext.innerHTML = posts
        .slice(0, 6)
        .map((post) => `
          <div class="read-item">
            <a href="${postUrl(post)}">
              <div class="read-thumb"><img src="${escapeAttr(getFeaturedImage(post, "thumbnail"))}" alt=""></div>
              <div class="read-title">${escapeHtml(stripTags(post.title.rendered))}</div>
            </a>
          </div>
        `)
        .join("");
    }
  }

  function setupShare(post) {
    const url = window.location.href;
    const title = stripTags(post.title.rendered);

    const fb = document.getElementById("share-facebook");
    const tw = document.getElementById("share-twitter");
    const wa = document.getElementById("share-whatsapp");
    const tg = document.getElementById("share-telegram");
    const cp = document.getElementById("share-copy");

    if (fb) fb.onclick = () => window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, "fb", "width=600,height=400");
    if (tw) tw.onclick = () => window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`, "tw", "width=600,height=400");
    if (wa) wa.onclick = () => window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(`${title} - ${url}`)}`, "wa");
    if (tg) tg.onclick = () => window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, "tg");
    if (cp) {
      cp.onclick = () => {
        navigator.clipboard.writeText(url).then(() => {
          cp.textContent = "कॉपी हो गया!";
          setTimeout(() => {
            cp.textContent = "Link कॉपी करें";
          }, 1800);
        });
      };
    }
  }

  async function init() {
    const container = document.getElementById("article-container");
    const id = getIdFromURL();

    await initShell();
    await renderBiharTrending();

    if (!wordpressUrl || !id) {
      if (container) {
        container.innerHTML = `<div class="empty-state">खबर नहीं मिली।</div>`;
      }
      return;
    }

    try {
      const post = await fetchPost(id);
      renderArticle(post);
      const related = await fetchRelated(post).catch(() => []);
      renderRelated(related);
      recommendedPost = related[0] || await loadRecommendedPost(post);
      renderAdSlot();
    } catch (error) {
      if (container) {
        container.innerHTML = `<div class="empty-state">WordPress API कनेक्ट नहीं हुआ।</div>`;
      }
    }
  }
})();
