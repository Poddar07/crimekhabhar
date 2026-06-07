(function () {
  const config = window.CRIME_KHABAR_CONFIG || {};
  const wordpressUrl = (config.wordpressUrl || "").replace(/\/$/, "");
  const themeBase = window.CRIME_KHABAR_ASSET_BASE || "";
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
        `<li class="current-menu-item"><a href="/">होम</a></li>`,
        ...menuCategories.map(renderCategoryMenuItem)
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
    const path = slug ? `category.html?category=${encodeURIComponent(slug)}` : "category.html";
    return themeBase ? `${themeBase}/${path}` : path;
  }

  function renderCategoryMenuItem(item) {
    const children = item.children && item.children.length ? `<div class="sub-menu-wrap">${renderCategoryList(item.children)}</div>` : "";
    const toggle = item.children && item.children.length ? `<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="Toggle subcategories">▾</button>` : "";

    return `<li><a href="${categoryUrl(item)}">${escapeHtml(item.label)}</a>${toggle}${children}</li>`;
  }

  function renderCategoryList(items) {
    return `<ul>${items.map(renderCategoryMenuItem).join("")}</ul>`;
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
          return await response.json();
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
          return await response.json();
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
    return `/detail.html?id=${encodeURIComponent(post.id)}`;
  }

  async function incrementPostViews(postId) {
    if (!wordpressUrl) {
      return;
    }

    try {
      const response = await fetch(`${wordpressUrl}/wp-json/bharat-bulletin/v1/increment-view`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ post_id: postId })
      });

      if (response.ok) {
        const data = await response.json();
        if (data.success) {
          const viewsElement = document.querySelector('.article-views');
          if (viewsElement) {
            viewsElement.textContent = `👁 ${data.views} views`;
          }
        }
      }
    } catch (error) {
      console.warn("Failed to increment post views.", error);
    }
  }

  function shareUrl(post) {
    const url = new URL(window.location.href);
    url.search = `?id=${encodeURIComponent(post.id)}`;
    url.hash = "";
    return url.toString();
  }

  function shareIcon(name) {
    const icons = {
      facebook: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 2-5 5v2H6v4h3v5h4v-5h3.2l.8-4h-4V9c0-.7.3-1 1-1Z"/></svg>',
      x: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h4.8l4.1 5.7L18 4h2.1l-6.2 7 6.7 9H16l-4.6-6.4L5.7 20H3.6l6.8-7.7L4 4Zm3.2 1.6 9.6 12.8h1.8L9 5.6H7.2Z"/></svg>',
      whatsapp: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a8.8 8.8 0 0 0-7.5 13.4L3.4 21l4.7-1.1A8.8 8.8 0 1 0 12 3Zm0 2a6.8 6.8 0 0 1 0 13.6c-1.1 0-2.1-.2-3-.7l-.4-.2-2.4.6.6-2.3-.3-.4A6.8 6.8 0 0 1 12 5Zm-2.3 3.5c-.2 0-.5 0-.7.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.4c.1.2 1.6 2.6 4 3.5 2 .8 2.4.5 2.9.5.4-.1 1.3-.6 1.5-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.2l-.7.9c-.1.2-.3.2-.5.1-.3-.1-1.1-.4-2-1.2-.7-.7-1.2-1.5-1.4-1.8-.1-.2 0-.4.1-.5l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.7-1.7c-.2-.4-.4-.4-.7-.4Z"/></svg>',
      telegram: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 4.6 18 20c-.2 1-.8 1.2-1.6.7l-4.5-3.3-2.2 2.1c-.2.2-.4.4-.9.4l.3-4.6 8.4-7.6c.4-.3-.1-.5-.6-.2L6.6 14 2.1 12.6c-1-.3-1-1 .2-1.4L19.8 4.4c.8-.3 1.5.2 1.2 1.2Z"/></svg>',
      copy: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 7a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3h-1v-2h1a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v1H8V7Zm-4 4a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3v-6Zm3-1a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1H7Z"/></svg>',
    };

    return icons[name] || "";
  }

  function renderArticle(post) {
    const container = document.getElementById("article-container");

    if (!container) {
      return;
    }

    const publishedDate = new Date(post.date).toLocaleDateString("hi-IN");
    const views = post.bb_post_views || 0;

    container.innerHTML = `
      <div class="article-media">
        ${renderImage(getFeaturedImage(post, "large"))}
      </div>
      <div class="story-body">
        <h1>${escapeHtml(stripTags(post.title.rendered))}</h1>
        <div class="meta article-meta">
          <span>${publishedDate}</span>
          <span class="article-views">👁 ${views} views</span>
          <span class="article-share-inline" aria-label="Share this article">
            <a id="share-facebook" class="share-btn share-facebook" href="#" target="_blank" rel="noopener" aria-label="Share on Facebook">${shareIcon("facebook")}</a>
            <a id="share-twitter" class="share-btn share-twitter" href="#" target="_blank" rel="noopener" aria-label="Share on X">${shareIcon("x")}</a>
            <a id="share-whatsapp" class="share-btn share-whatsapp" href="#" target="_blank" rel="noopener" aria-label="Share on WhatsApp">${shareIcon("whatsapp")}</a>
            <a id="share-telegram" class="share-btn share-telegram" href="#" target="_blank" rel="noopener" aria-label="Share on Telegram">${shareIcon("telegram")}</a>
            <button id="share-copy" class="share-btn share-copy" type="button" aria-label="Copy article link">${shareIcon("copy")}</button>
          </span>
        </div>
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
          <article class="story-card related-card">
            <a class="story-media" href="${postUrl(post)}">${renderImage(getFeaturedImage(post, "medium_large"))}</a>
            <div class="story-body">
              <h3><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></h3>
              <p class="summary">${escapeHtml(stripTags(post.excerpt.rendered).slice(0, 140))}</p>
            </div>
          </article>
        `)
        .join("");
    }

    if (readNext) {
      readNext.innerHTML = posts
        .slice(0, 6)
        .map((post) => `
          <article class="read-item">
            <a href="${postUrl(post)}">
              <div class="read-thumb"><img src="${escapeAttr(getFeaturedImage(post, "thumbnail"))}" alt=""></div>
              <div class="read-body">
                <div class="read-title">${escapeHtml(stripTags(post.title.rendered))}</div>
                <div class="read-meta">${escapeHtml(new Date(post.date).toLocaleDateString("hi-IN"))}</div>
              </div>
            </a>
          </article>
        `)
        .join("");
    }
  }

  function setupShare(post) {
    const url = shareUrl(post);
    const title = stripTags(post.title.rendered);

    const fb = document.getElementById("share-facebook");
    const tw = document.getElementById("share-twitter");
    const wa = document.getElementById("share-whatsapp");
    const tg = document.getElementById("share-telegram");
    const cp = document.getElementById("share-copy");

    if (fb) fb.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    if (tw) tw.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
    if (wa) wa.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(`${title} - ${url}`)}`;
    if (tg) tg.href = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
    if (cp) {
      const originalContent = cp.innerHTML;
      cp.onclick = () => {
        navigator.clipboard.writeText(url).then(() => {
          cp.textContent = "Copied";
          setTimeout(() => {
            cp.innerHTML = originalContent;
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
      incrementPostViews(id);
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
