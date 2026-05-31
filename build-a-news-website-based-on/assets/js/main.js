(function () {
  const toggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".main-nav");
  const searchForm = document.querySelector(".search-form");
  const searchInput = searchForm ? searchForm.querySelector('input[type="search"]') : null;
  const status = document.querySelector(".filter-status");
  const newsletterForms = document.querySelectorAll(".newsletter form");

  function getFilterableItems() {
    return document.querySelectorAll("[data-category]");
  }

  function setStatus(message) {
    if (status) {
      status.textContent = message;
    }
  }

  function clearActiveLinks() {
    document.querySelectorAll("[data-filter]").forEach(function (link) {
      if (link.parentElement) {
        link.parentElement.classList.remove("current-menu-item");
      }
    });
  }

  function setCategoryInUrl(filter) {
    const url = new URL(window.location.href);

    if (!filter || filter === "all") {
      url.searchParams.delete("category");
    } else {
      url.searchParams.set("category", filter);
    }

    window.history.replaceState({}, "", url.toString());
  }

  function applyCategoryFilter(filter, shouldScroll) {
    const filterableItems = getFilterableItems();

    if (!filter || !filterableItems.length) {
      return;
    }

    clearActiveLinks();

    const activeLink =
      document.querySelector(`[data-filter="${filter}"]`) || document.querySelector('[data-filter="all"]');

    if (activeLink && activeLink.parentElement) {
      activeLink.parentElement.classList.add("current-menu-item");
    }

    filterableItems.forEach(function (item) {
      const categories = (item.dataset.category || "").split(" ");
      item.classList.toggle("is-hidden", filter !== "all" && !categories.includes(filter));
    });

    setStatus(filter === "all" ? "Showing all news." : "Showing selected category news.");
    setCategoryInUrl(filter);

    if (shouldScroll) {
      const shell = document.querySelector(".content-shell");
      if (shell) {
        shell.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }
  }

  function setupSubmenuToggleButtons() {
    const menu = document.querySelector(".main-menu");

    if (!menu) {
      return;
    }

    menu.querySelectorAll("li").forEach(function (item) {
      const submenu = item.querySelector(".sub-menu-wrap, .sub-menu");
      if (!submenu || item.querySelector(".submenu-toggle")) {
        return;
      }

      const button = document.createElement("button");
      button.type = "button";
      button.className = "submenu-toggle";
      button.setAttribute("aria-expanded", "false");
      button.setAttribute("aria-label", "Toggle subcategories");
      button.innerHTML = "▾";

      const anchor = item.querySelector("a");
      if (anchor) {
        anchor.after(button);
      } else {
        item.prepend(button);
      }
    });
  }

  const mainMenu = document.querySelector(".main-menu");

  if (mainMenu && window.MutationObserver) {
    const observer = new MutationObserver(setupSubmenuToggleButtons);
    observer.observe(mainMenu, { childList: true, subtree: true });
  }

  setupSubmenuToggleButtons();

  if (toggle && nav) {
    toggle.setAttribute("aria-expanded", "false");

    toggle.addEventListener("click", function () {
      const isMobile = window.matchMedia("(max-width: 640px)").matches;

      if (isMobile) {
        const isOpen = nav.classList.toggle("is-open");
        document.body.classList.toggle("drawer-open", isOpen);
        toggle.setAttribute("aria-expanded", String(isOpen));
      } else {
        const isCollapsed = nav.classList.toggle("is-collapsed");
        toggle.setAttribute("aria-expanded", String(!isCollapsed));
      }
    });

    document.addEventListener("click", function (event) {
      const clickInsideNav = event.target.closest(".main-nav");
      const clickToggle = event.target.closest(".menu-toggle");

      if (nav.classList.contains("is-open") && !clickInsideNav && !clickToggle) {
        nav.classList.remove("is-open");
        document.body.classList.remove("drawer-open");
        toggle.setAttribute("aria-expanded", "false");
      }
    });

    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape" && nav.classList.contains("is-open")) {
        nav.classList.remove("is-open");
        document.body.classList.remove("drawer-open");
        toggle.setAttribute("aria-expanded", "false");
      }
    });

    nav.addEventListener("click", function (event) {
      const toggleButton = event.target.closest(".submenu-toggle");

      if (!toggleButton) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      const listItem = toggleButton.closest("li");
      if (!listItem) {
        return;
      }

      const expanded = listItem.classList.toggle("is-expanded");
      toggleButton.setAttribute("aria-expanded", String(expanded));
    });
  }

  document.addEventListener("click", function (event) {
    const link = event.target.closest("[data-filter]");

    if (!link) {
      return;
    }

    const filter = link.dataset.filter;
    if (!filter) {
      return;
    }

    event.preventDefault();
    applyCategoryFilter(filter, true);
  });

  if (searchForm && searchInput && !searchForm.getAttribute("action")) {
    searchForm.addEventListener("submit", function (event) {
      event.preventDefault();

      const query = searchInput.value.trim().toLowerCase();
      const filterableItems = getFilterableItems();
      clearActiveLinks();

      filterableItems.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.classList.toggle("is-hidden", query.length > 0 && !text.includes(query));
      });

      setStatus(query ? "Search results for: " + searchInput.value.trim() : "Showing all news.");
      const shell = document.querySelector(".content-shell");
      if (shell) {
        shell.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  }

  newsletterForms.forEach(function (form) {
    const input = form.querySelector('input[type="email"]');
    const button = form.querySelector('button[type="submit"]');
    const message = document.createElement("p");

    message.className = "newsletter-message";
    message.setAttribute("aria-live", "polite");
    form.insertAdjacentElement("afterend", message);

    function saveLocally(email) {
      let subscribers = window.crimeKhabarSubscribers || [];

      try {
        subscribers = JSON.parse(window.localStorage.getItem("crimeKhabarSubscribers") || "[]");
      } catch (error) {
        subscribers = window.crimeKhabarSubscribers || [];
      }

      if (!subscribers.includes(email)) {
        subscribers.push(email);
      }

      window.crimeKhabarSubscribers = subscribers;

      try {
        window.localStorage.setItem("crimeKhabarSubscribers", JSON.stringify(subscribers));
      } catch (error) {
        // Browser storage may be unavailable in some contexts.
      }
    }

    function handleSuccess(serverMessage) {
      message.textContent = serverMessage || "Thanks! Your subscription has been added.";
      message.classList.add("is-success");
      form.reset();

      if (button) {
        button.textContent = "Subscribed";
        setTimeout(function () {
          button.textContent = "Subscribe";
        }, 1800);
      }
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const email = input ? input.value.trim() : "";
      const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

      message.classList.remove("is-error", "is-success");

      if (!isValidEmail) {
        message.textContent = "Please enter a valid email address.";
        message.classList.add("is-error");
        return;
      }

      const endpoint = window.bharatBulletinSettings && window.bharatBulletinSettings.newsletterEndpoint;
      const nonce = window.bharatBulletinSettings && window.bharatBulletinSettings.nonce;

      if (!endpoint) {
        saveLocally(email);
        handleSuccess("Thanks! Subscription saved locally in this browser.");
        return;
      }

      fetch(endpoint, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": nonce || "",
        },
        body: JSON.stringify({ email: email }),
      })
        .then(function (response) {
          return response.json().then(function (data) {
            if (response.ok && data.success) {
              saveLocally(email);
              handleSuccess(data.message || "Thanks! Your subscription has been added.");
            } else {
              saveLocally(email);
              message.textContent = data.message || "Server unavailable. Subscription saved locally.";
              message.classList.add("is-success");
              form.reset();
            }
          });
        })
        .catch(function () {
          saveLocally(email);
          message.textContent = "Network error. Subscription saved locally.";
          message.classList.add("is-success");
          form.reset();
        });
    });
  });

  window.addEventListener("load", function () {
    const url = new URL(window.location.href);
    const categoryFromUrl = (url.searchParams.get("category") || "all").trim();
    applyCategoryFilter(categoryFromUrl || "all", false);
  });
})();
