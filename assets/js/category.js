(function () {
  const config = window.CRIME_KHABAR_CONFIG || {};
  const wordpressUrl = (config.wordpressUrl || "").replace(/\/$/, "");
  const titleNode = document.getElementById("category-title");
  const statusNode = document.getElementById("category-status");
  const contentNode = document.getElementById("category-content");
  const trendingNode = document.getElementById("category-trending");

  init();

  async function init() {
    if (!wordpressUrl) {
      setStatus("WordPress URL is not configured.");
      return;
    }

    try {
      const categories = await fetchCategories();
      renderMenu(categories);
      renderTopicStrip();

      const slug = getCategorySlugFromUrl();
      if (!slug) {
        setStatus("Category slug missing.");
        return;
      }

      const category = findCategoryBySlug(categories, slug);
      if (!category) {
        setStatus("Category not found.");
        return;
      }

      setTitle(category.name);

      if (hasChildren(category, categories)) {
        await renderParentCategoryView(category, categories);
      } else {
        await renderSubcategoryListView(category);
      }

      await renderTrending(categories);
      setStatus("");
    } catch (error) {
      setStatus("Unable to load category page.");
    }
  }

  function setTitle(text) {
    if (titleNode) {
      titleNode.textContent = text || "Category";
    }
    document.title = `${text || "Category"} - Crime Khabar`;
  }

  function setStatus(text) {
    if (statusNode) {
      statusNode.textContent = text || "";
    }
  }

  function getCategorySlugFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return (params.get("category") || "").trim();
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

  function categoryApiUrls() {
    return [
      `${wordpressUrl}/wp-json/wp/v2/categories?per_page=100&hide_empty=false`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/categories&per_page=100&hide_empty=false`,
    ];
  }

  function postApiUrls(query) {
    return [
      `${wordpressUrl}/wp-json/wp/v2/posts?${query}`,
      `${wordpressUrl}/index.php?rest_route=/wp/v2/posts&${query}`,
    ];
  }

  async function fetchCategories() {
    return fetchJson(categoryApiUrls());
  }

  async function fetchPostsByCategoryId(categoryId, perPage) {
    const query = `_embed=1&categories=${encodeURIComponent(categoryId)}&per_page=${encodeURIComponent(perPage || 5)}`;
    return fetchJson(postApiUrls(query));
  }

  async function fetchPostsByCategoryIds(categoryIds, perPage) {
    const query = `_embed=1&categories=${encodeURIComponent(categoryIds.join(","))}&per_page=${encodeURIComponent(perPage || 5)}`;
    return fetchJson(postApiUrls(query));
  }

  function findCategoryBySlug(categories, slug) {
    return categories.find((category) => category.slug === slug);
  }

  function hasChildren(category, categories) {
    return categories.some((item) => item.parent === category.id);
  }

  function childCategoriesOf(parentCategory, categories) {
    return categories
      .filter((item) => item.parent === parentCategory.id)
      .sort((a, b) => String(a.name).localeCompare(String(b.name)));
  }

  function categoryUrlBySlug(slug) {
    return `category.html?category=${encodeURIComponent(slug)}`;
  }

  function renderMenu(categories) {
    const menu = document.querySelector(".main-menu");
    if (!menu) {
      return;
    }

    const byId = new Map(categories.map((item) => [item.id, { ...item, children: [] }]));
    const roots = [];

    for (const item of byId.values()) {
      if (item.parent && byId.has(item.parent)) {
        byId.get(item.parent).children.push(item);
      } else {
        roots.push(item);
      }
    }

    function renderTree(items) {
      return `<ul>${items
        .sort((a, b) => String(a.name).localeCompare(String(b.name)))
        .map((item) => {
          const children = item.children.length ? renderTree(item.children) : "";
          const toggle = item.children.length
            ? `<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="Toggle subcategories">▾</button>`
            : "";
          return `<li><a href="${categoryUrlBySlug(item.slug)}">${escapeHtml(item.name)}</a>${toggle}${children}</li>`;
        })
        .join("")}</ul>`;
    }

    menu.innerHTML =
      `<li class="current-menu-item"><a href="index.html">होम</a></li>` +
      roots
        .sort((a, b) => String(a.name).localeCompare(String(b.name)))
        .map((item) => {
          const children = item.children.length ? `<div class="sub-menu-wrap">${renderTree(item.children)}</div>` : "";
          const toggle = item.children.length
            ? `<button type="button" class="submenu-toggle" aria-expanded="false" aria-label="Toggle subcategories">▾</button>`
            : "";
          return `<li><a href="${categoryUrlBySlug(item.slug)}">${escapeHtml(item.name)}</a>${toggle}${children}</li>`;
        })
        .join("");
  }

  function renderTopicStrip() {
    const strip = document.querySelector(".topic-strip");
    if (!strip) {
      return;
    }

    const items = [
      { label: "Home", href: "index.html" },
      { label: "Top Stories", href: "index.html#latest-news" },
      { label: "Visual Stories", href: "index.html#visual-stories" },
      { label: "Weather", href: "#category-weather" },
    ];

    strip.innerHTML = items.map((item) => `<li><a href="${item.href}">${escapeHtml(item.label)}</a></li>`).join("");
  }

  async function renderParentCategoryView(parentCategory, categories) {
    const children = childCategoriesOf(parentCategory, categories);
    if (!children.length) {
      await renderSubcategoryListView(parentCategory);
      return;
    }

    const sections = await Promise.all(
      children.map(async (child) => {
        const posts = await fetchPostsByCategoryId(child.id, 5).catch(() => []);
        return `
          <section class="subcategory-block">
            <div class="subcategory-head">
              <h2><a href="${categoryUrlBySlug(child.slug)}">${escapeHtml(child.name)}</a></h2>
            </div>
            ${
              posts.length
                ? `<ol class="category-post-list">
                    ${posts
                      .map(
                        (post) => `
                          <li>
                            <a href="/detail.html?id=${encodeURIComponent(post.id)}">${escapeHtml(stripTags(post.title.rendered))}</a>
                            <span class="meta">${escapeHtml(formatDate(post.date))}</span>
                          </li>
                        `
                      )
                      .join("")}
                  </ol>`
                : `<p class="empty-state">No posts yet in this subcategory.</p>`
            }
          </section>
        `;
      })
    );

    contentNode.innerHTML = `<div class="subcategory-blocks">${sections.join("")}</div>`;
  }

  async function renderSubcategoryListView(category) {
    const posts = await fetchPostsByCategoryId(category.id, 30).catch(() => []);

    if (!posts.length) {
      contentNode.innerHTML = `<p class="empty-state">No posts found in this subcategory.</p>`;
      return;
    }

    contentNode.innerHTML = `
      <ol class="category-post-list category-post-list-full">
        ${posts
          .map(
            (post) => `
              <li>
                <a href="/detail.html?id=${encodeURIComponent(post.id)}">${escapeHtml(stripTags(post.title.rendered))}</a>
                <span class="meta">${escapeHtml(formatDate(post.date))}</span>
              </li>
            `
          )
          .join("")}
      </ol>
    `;
  }

  function descendantCategoryIds(category, categories) {
    const ids = [category.id];
    childCategoriesOf(category, categories).forEach((child) => {
      ids.push(...descendantCategoryIds(child, categories));
    });
    return ids;
  }

  async function renderTrending(categories) {
    if (!trendingNode) {
      return;
    }

    const biharCategory = findCategoryBySlug(categories, "bihar");
    const categoryIds = biharCategory ? descendantCategoryIds(biharCategory, categories) : [];
    const posts = categoryIds.length ? await fetchPostsByCategoryIds(categoryIds, 4).catch(() => []) : [];

    trendingNode.innerHTML = posts
      .slice(0, 4)
      .map(
        (post, index) => `
          <li>
            <span class="rank">${index + 1}</span>
            <a href="/detail.html?id=${encodeURIComponent(post.id)}">${escapeHtml(stripTags(post.title.rendered))}</a>
          </li>
        `
      )
      .join("");
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

  function formatDate(dateText) {
    if (!dateText) {
      return "";
    }
    try {
      return new Date(dateText).toLocaleDateString("hi-IN");
    } catch (error) {
      return "";
    }
  }
})();
