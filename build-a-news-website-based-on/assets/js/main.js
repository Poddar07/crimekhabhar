(function () {
  const toggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".main-nav");
  const searchForm = document.querySelector(".search-form");
  const searchInput = searchForm ? searchForm.querySelector('input[type="search"]') : null;
  const filterLinks = document.querySelectorAll("[data-filter]");
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
    filterLinks.forEach(function (link) {
      link.parentElement.classList.remove("current-menu-item");
    });
  }

  if (toggle && nav) {
    toggle.setAttribute("aria-expanded", "false");

    toggle.addEventListener("click", function () {
      const isMobile = window.matchMedia("(max-width: 640px)").matches;

      if (isMobile) {
        const isOpen = nav.classList.toggle("is-open");
        toggle.setAttribute("aria-expanded", String(isOpen));
      } else {
        const isCollapsed = nav.classList.toggle("is-collapsed");
        toggle.setAttribute("aria-expanded", String(!isCollapsed));
      }
    });
  }

  filterLinks.forEach(function (link) {
    link.addEventListener("click", function (event) {
      const filter = link.dataset.filter;
      const filterableItems = getFilterableItems();

      if (!filter || !filterableItems.length) {
        return;
      }

      event.preventDefault();
      clearActiveLinks();
      link.parentElement.classList.add("current-menu-item");

      filterableItems.forEach(function (item) {
        const categories = (item.dataset.category || "").split(" ");
        item.classList.toggle("is-hidden", filter !== "all" && !categories.includes(filter));
      });

      setStatus(filter === "all" ? "बिहार की सभी खबरें दिखाई जा रही हैं" : "चुनी हुई कैटेगरी की बिहार खबरें दिखाई जा रही हैं");
      document.querySelector(".content-shell").scrollIntoView({ behavior: "smooth", block: "start" });
    });
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

      setStatus(query ? "Search results for: " + searchInput.value.trim() : "बिहार की सभी खबरें दिखाई जा रही हैं");
      document.querySelector(".content-shell").scrollIntoView({ behavior: "smooth", block: "start" });
    });
  }

  newsletterForms.forEach(function (form) {
    const input = form.querySelector('input[type="email"]');
    const button = form.querySelector('button[type="submit"]');
    const message = document.createElement("p");

    message.className = "newsletter-message";
    message.setAttribute("aria-live", "polite");
    form.insertAdjacentElement("afterend", message);

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const email = input ? input.value.trim() : "";
      const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

      message.classList.remove("is-error", "is-success");

      if (!isValidEmail) {
        message.textContent = "कृपया सही email address डालें।";
        message.classList.add("is-error");
        return;
      }

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
        // Browser storage can be unavailable in some preview contexts.
      }

      message.textContent = "धन्यवाद! आपका subscription जुड़ गया है।";
      message.classList.add("is-success");
      form.reset();

      if (button) {
        button.textContent = "Subscribed";
        setTimeout(function () {
          button.textContent = "Subscribe";
        }, 1800);
      }
    });
  });
})();
