(function () {
  const config = window.CRIME_KHABAR_CONFIG || {};
  const wordpressUrl = (config.wordpressUrl || "").replace(/\/$/, "");
  const postsPerPage = config.postsPerPage || 12;
  const categories = [];
  let categoryMap = {};
  let currentRecommendedPost = null;

  init();

  async function init() {
    const loadedCategories = await loadCategories();
    categories.splice(0, categories.length, ...loadedCategories);
    categoryMap = flattenCategories(categories);
    renderPrimaryMenu();
    renderTopicStrip();
    renderCategoryDirectory();
    renderAdSlots();
    renderWeatherTemperature();
  }

  async function loadCategories() {
    if (!wordpressUrl) {
      return config.categories || [];
    }

    try {
      return await fetchCategories();
    } catch (error) {
      console.warn("Failed to load categories from WordPress, falling back to config.", error);
      return config.categories || [];
    }
  }

  async function fetchCategories() {
    const urls = [
      `${wordpressUrl}/wp-json/wp/v2/categories?per_page=100&hide_empty=false`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/categories&per_page=100&hide_empty=false`
    ];
    const terms = await fetchJson(urls);
    return buildCategoryTree(terms);
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
        link: term.link || "",
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

  if (!wordpressUrl || wordpressUrl.includes("example.com")) {
    setStatus("WordPress URL सेट करें, फिर खबरें अपने आप लोड होंगी।");
    return;
  }

  if (!document.querySelector(".main-content")) {
    return;
  }

  function flattenCategories(items, map = {}) {
    items.forEach((item) => {
      map[item.key] = {
        id: item.id,
        label: item.label,
        link: item.link || "",
        slugs: item.slugs || [],
        parent: item.parent || null
      };

      if (item.children) {
        item.children.forEach((child) => {
          child.parent = item.key;
        });
        flattenCategories(item.children, map);
      }
    });

    return map;
  }

  function firstSlug(item) {
    return item && item.slugs && item.slugs.length ? item.slugs[0] : "";
  }

  function categoryUrl(item) {
    const slug = firstSlug(item);
    const localFallback = slug ? `category.html?category=${encodeURIComponent(slug)}` : "category.html";

    if (!item || !item.link) {
      return localFallback;
    }

    try {
      const linkUrl = new URL(item.link);
      const currentOrigin = window.location.origin;

      // Keep category navigation local in local/static setups.
      if (linkUrl.origin !== currentOrigin) {
        return localFallback;
      }

      return `${linkUrl.pathname}${linkUrl.search}${linkUrl.hash}`;
    } catch (error) {
      return localFallback;
    }
  }

  function stripTags(html) {
    const div = document.createElement("div");
    div.innerHTML = html || "";
    return div.textContent.trim();
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text || "";
    return div.innerHTML;
  }

  function escapeAttr(text) {
    return escapeHtml(text).replace(/"/g, "&quot;");
  }

  function setStatus(message) {
    const status = document.querySelector(".filter-status");

    if (status) {
      status.textContent = message;
    }
  }

  function renderCategoryList(items) {
    return `<ul>${items
      .map((item) => {
        const children = item.children && item.children.length ? renderCategoryList(item.children) : "";
        const toggle = item.children && item.children.length ? `<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="Toggle subcategories">▾</button>` : "";
        return `<li><a href="${categoryUrl(item)}">${escapeHtml(item.label)}</a>${toggle}${children}</li>`;
      })
      .join("")}</ul>`;
  }

  function renderPrimaryMenu() {
    const menu = document.querySelector(".main-menu");

    if (!menu) {
      return;
    }

    const primaryItems = categories;
    menu.innerHTML = [
      `<li class="current-menu-item"><a href="index.html">होम</a></li>`,
      ...primaryItems.map((item) => {
        const children = item.children && item.children.length ? `<div class="sub-menu-wrap">${renderCategoryList(item.children)}</div>` : "";
        const toggle = item.children && item.children.length ? `<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="Toggle subcategories">▾</button>` : "";
        return `<li><a href="${categoryUrl(item)}">${escapeHtml(item.label)}</a>${toggle}${children}</li>`;
      })
    ].join("");
  }

  function renderTopicStrip() {
    const strip = document.querySelector(".topic-strip");

    if (!strip) {
      return;
    }

    const quickItems = [
      { label: "Top Stories", href: "#top" },
      { label: "दृश्य कहानियाँ", href: "#visual-stories" },
      { label: "Trending", href: "#top" },
      { label: "Weather", href: "#category-weather" },
      { label: "Newsletter", href: "#newsletter" }
    ];

    strip.innerHTML = quickItems
      .map((item) => `<li><a href="${item.href}">${escapeHtml(item.label)}</a></li>`)
      .join("");
  }

  function renderCategoryDirectory() {
    const directory = document.querySelector("[data-category-tree]");

    if (!directory) {
      return;
    }

    directory.innerHTML = renderCategoryList(categories);
  }

  function renderAdSlots() {
    document.querySelectorAll("[data-ad-slots]").forEach((container) => {
      container.innerHTML = "";
    });
  }

  function renderWeatherTemperature() {
    document.querySelectorAll("[data-weather-temp]").forEach((node) => {
      node.textContent = config.weatherTemperature || "34°C";
    });
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

  function getCategories(post) {
    const terms = post._embedded && post._embedded["wp:term"] && post._embedded["wp:term"][0];

    if (!terms || !terms.length) {
      return { label: "News", slugs: ["news"] };
    }

    return {
      label: terms[0].name,
      slugs: terms.map((term) => term.slug)
    };
  }

  function postSlugs(post) {
    return getCategories(post).slugs.filter(Boolean);
  }

  function matchesAny(post, keys) {
    const slugs = postSlugs(post);
    const accepted = keys.flatMap((key) => (categoryMap[key] && categoryMap[key].slugs) || []);

    return slugs.some((slug) => accepted.includes(slug));
  }

  function biharCategoryKeys() {
    const keys = new Set(["bihar"]);
    let changed = true;

    while (changed) {
      changed = false;
      Object.keys(categoryMap).forEach((key) => {
        const item = categoryMap[key];
        if (item && item.parent && keys.has(item.parent) && !keys.has(key)) {
          keys.add(key);
          changed = true;
        }
      });
    }

    return Array.from(keys);
  }

  function categoryIdsForKeys(keys) {
    return keys
      .map((key) => categoryMap[key] && categoryMap[key].id)
      .filter(Boolean);
  }

  function pickPosts(posts, keys, limit, used) {
    const selected = posts.filter((post) => !used.has(post.id) && matchesAny(post, keys)).slice(0, limit);
    selected.forEach((post) => used.add(post.id));

    if (selected.length < limit) {
      posts
        .filter((post) => !used.has(post.id))
        .slice(0, limit - selected.length)
        .forEach((post) => {
          selected.push(post);
          used.add(post.id);
        });
    }

    return selected;
  }

  function getVideoLink(post) {
    return (post.meta && (post.meta.youtube_url || post.meta.video_url)) || `detail.html?id=${post.id}`;
  }

  async function fetchWordPressPosts(limit) {
    const prettyUrl = `${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=${limit}`;
    const fallbackUrl = `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&_embed=1&per_page=${limit}`;
    const urls = [prettyUrl, fallbackUrl];
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

  async function fetchWordPressPostsByCategoryIds(categoryIds, limit) {
    if (!categoryIds.length) {
      return [];
    }

    const categoryParam = encodeURIComponent(categoryIds.join(","));
    const prettyUrl = `${wordpressUrl}/wp-json/wp/v2/posts?_embed=1&per_page=${limit}&categories=${categoryParam}`;
    const fallbackUrl = `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&_embed=1&per_page=${limit}&categories=${categoryParam}`;
    return fetchJson([prettyUrl, fallbackUrl]);
  }

  async function fetchVisualStories(limit) {
    return fetchWordPressPostsByCategoryIds(
      categoryIdsForKeys(["visual-stories", "bihar-visual-stories", "bihar-visulal-stories"]),
      limit
    ).catch(() => []);
  }

  function postUrl(post) {
    return `detail.html?id=${encodeURIComponent(post.id)}`;
  }

  function renderLead(post) {
    const cats = getCategories(post);
    const image = getFeaturedImage(post, "large");

    return `
      <article class="story-card featured${image ? "" : " no-image"}" data-category="${escapeAttr(cats.slugs.join(" "))}">
        ${
          image
            ? `<a class="story-media" href="${postUrl(post)}">
                ${renderImage(image)}
                <span class="media-badge">${escapeHtml(cats.label)}</span>
              </a>`
            : ""
        }
        <div class="story-body">
          <h1><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></h1>
          <p class="summary">${escapeHtml(stripTags(post.excerpt.rendered).slice(0, 180))}</p>
          <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span></div>
        </div>
      </article>
    `;
  }

  function renderMini(post) {
    const cats = getCategories(post);
    const image = getFeaturedImage(post, "thumbnail");

    return `
      <article class="story-mini${image ? "" : " no-image"}" data-category="${escapeAttr(cats.slugs.join(" "))}">
        ${image ? `<a class="thumb" href="${postUrl(post)}">${renderImage(image)}</a>` : ""}
        <div>
          <div class="category-kicker">${escapeHtml(cats.label)}</div>
          <h3><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></h3>
          <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span></div>
        </div>
      </article>
    `;
  }

  function renderCard(post) {
    const cats = getCategories(post);
    const image = getFeaturedImage(post, "medium_large");

    return `
      <article class="story-card${image ? "" : " no-image"}" data-category="${escapeAttr(cats.slugs.join(" "))}">
        ${
          image
            ? `<a class="story-media" href="${postUrl(post)}">
                ${renderImage(image)}
                <span class="media-badge">${escapeHtml(cats.label)}</span>
              </a>`
            : ""
        }
        <div class="story-body">
          <h3><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></h3>
          <p class="summary">${escapeHtml(stripTags(post.excerpt.rendered).slice(0, 120))}</p>
          <div class="meta"><span>${new Date(post.date).toLocaleDateString("hi-IN")}</span></div>
        </div>
      </article>
    `;
  }
  function renderVisual(post) {
    return `
      <a class="visual-card" href="${postUrl(post)}">
        ${renderImage(getFeaturedImage(post, "medium_large"))}
        <h3>${escapeHtml(stripTags(post.title.rendered))}</h3>
      </a>
    `;
  }

  function renderTrending(posts) {
    const lists = document.querySelectorAll("[data-bihar-trending]");

    if (!lists.length) {
      return;
    }

    const trendingPosts = posts.filter((post) => matchesAny(post, biharCategoryKeys())).slice(0, 4);
    const html = trendingPosts
      .map((post, index) => `<li><span class="rank">${index + 1}</span><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></li>`)
      .join("");

    lists.forEach((list) => {
      list.innerHTML = html;
    });
  }

  function renderEmptyState() {
    document.querySelector(".lead-grid").innerHTML = `<div class="empty-state">अभी कोई खबर नहीं मिली।</div>`;
    const newsGrid = document.querySelector(".news-grid");
    if (newsGrid) {
      newsGrid.innerHTML = "";
    }
    document.querySelector(".visual-strip").innerHTML = "";
  }

  async function loadWordPressPosts() {
    setStatus("WordPress से खबरें लोड हो रही हैं...");

    const posts = await fetchWordPressPosts(Math.max(postsPerPage, 36));

    if (!posts.length) {
      setStatus("WordPress में अभी कोई पोस्ट नहीं मिली।");
      renderEmptyState();
      return;
    }

    const used = new Set();
    const lead = pickPosts(posts, ["breaking-news", "badi-khabar", "bihar"], 1, used)[0];
    const miniPosts = posts.filter((post) => post.id !== lead.id).slice(0, 9);
    miniPosts.forEach((post) => used.add(post.id));
    const visualPosts = await fetchVisualStories(5);

    const leadGrid = document.querySelector(".lead-grid");
    const visualStrip = document.querySelector(".visual-strip");
    const ticker = document.querySelector(".ticker-list");

    if (leadGrid) {
      leadGrid.innerHTML = `${renderLead(lead)}<div class="stacked-news">${miniPosts.map(renderMini).join("")}</div>`;
    }

    if (visualStrip) {
      visualStrip.innerHTML = visualPosts.map(renderVisual).join("");
    }

    if (ticker) {
      ticker.innerHTML = posts
        .slice(0, 12)
        .map((post) => `<li><a href="${postUrl(post)}">${escapeHtml(stripTags(post.title.rendered))}</a></li>`)
        .join("");
    }

    currentRecommendedPost = posts.find((post) => !used.has(post.id)) || posts[0];
    renderAdSlots();
    renderTrending(posts);
    setStatus("");
  }

  loadWordPressPosts().catch(function () {
    setStatus("WordPress API कनेक्ट नहीं हुआ।");
    renderEmptyState();
  });
})();




