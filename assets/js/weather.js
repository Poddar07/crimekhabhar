(function () {
  const containerSelector = "[data-weather-carousel]";

  function getSettings() {
    const cfg = window.CRIME_KHABAR_CONFIG || {};
    return {
      apiKey: cfg.weatherApiKey || "",
      cities: cfg.weatherCities || ["Patna", "Muzaffarpur", "Darbhanga", "Gaya", "Bhagalpur"],
      units: cfg.weatherUnits || "metric",
    };
  }

  function fetchCurrent(city, apiKey, units) {
    const q = encodeURIComponent(city + ",IN");
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${q}&units=${encodeURIComponent(units)}&appid=${encodeURIComponent(apiKey)}`;
    return fetch(url).then((r) => (r.ok ? r.json() : Promise.reject(r)));
  }

  function escapeHtml(text) {
    const d = document.createElement("div");
    d.textContent = text || "";
    return d.innerHTML;
  }

  function renderSlide(data) {
    const temp = data.main ? Math.round(data.main.temp) : "-";
    const desc = data.weather && data.weather[0] ? data.weather[0].description : "";
    const icon = data.weather && data.weather[0] ? `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png` : "";

    return `<div class="weather-slide">
      <div class="weather-city">${escapeHtml(data.name)}</div>
      <div class="weather-main">
        <img class="weather-icon" src="${icon}" alt="${escapeHtml(desc)}">
        <div class="weather-reading">${temp}&deg;C</div>
      </div>
      <div class="weather-desc">${escapeHtml(desc)}</div>
    </div>`;
  }

  function buildCarouselHtml(slidesHtml) {
    return `
      <div class="weather-carousel-wrap">
        <button class="weather-prev" aria-label="Previous">&lsaquo;</button>
        <div class="weather-slides">${slidesHtml}</div>
        <button class="weather-next" aria-label="Next">&rsaquo;</button>
      </div>
    `;
  }

  function initCarousel(root) {
    const slides = root.querySelector(".weather-slides");
    const prev = root.querySelector(".weather-prev");
    const next = root.querySelector(".weather-next");
    if (!slides || !prev || !next) {
      return;
    }

    let index = 0;
    const items = slides.children;
    if (!items.length) {
      return;
    }

    function show(i) {
      index = (i + items.length) % items.length;
      Array.from(items).forEach(function (item, itemIndex) {
        item.style.display = itemIndex === index ? "block" : "none";
      });
    }

    prev.addEventListener("click", function () {
      show(index - 1);
    });

    next.addEventListener("click", function () {
      show(index + 1);
    });

    show(0);

    setInterval(function () {
      show(index + 1);
    }, 5000);
  }

  async function renderWeatherForRoot(root) {
    const settings = getSettings();
    const apiKey = settings.apiKey;
    const units = settings.units || "metric";
    const cities = settings.cities || [];
    const serverEndpoint = (window.bharatBulletinSettings && window.bharatBulletinSettings.weatherEndpoint) || null;

    let results = [];

    if (serverEndpoint) {
      try {
        const url = serverEndpoint + "?cities=" + encodeURIComponent(cities.join(","));
        const resp = await fetch(url);
        if (resp.ok) {
          const data = await resp.json();
          results = Object.values(data).filter(function (item) {
            return item && !item.error;
          });
        }
      } catch (e) {
        results = [];
      }
    }

    if (!results.length && apiKey) {
      results = await Promise.all(
        cities.map((city) =>
          fetchCurrent(city, apiKey, units).catch(function () {
            return null;
          })
        )
      );
    }

    const slides = results.filter(Boolean).map(renderSlide).join("");

    if (!slides) {
      root.innerHTML = `<div class="weather-error">Weather data unavailable.</div>`;
      return;
    }

    root.innerHTML = buildCarouselHtml(slides);
    const wrapper = root.querySelector(".weather-carousel-wrap");
    if (wrapper) {
      initCarousel(wrapper);
    }
  }

  async function renderWeather() {
    const roots = document.querySelectorAll(containerSelector);
    if (!roots.length) {
      return;
    }

    await Promise.all(Array.from(roots).map((root) => renderWeatherForRoot(root)));
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", renderWeather);
  } else {
    renderWeather();
  }
})();
