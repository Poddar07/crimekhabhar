# Bharat Bulletin WordPress Theme

A WordPress-ready Hindi news theme inspired by the editorial structure of Aaj Tak: dense navigation, red broadcast-style masthead, breaking ticker, lead story grid, video band, visual stories, sidebar trending list, ad slots, and newsletter widget.

## Install

1. Copy this folder into `wp-content/themes/bharat-bulletin`.
2. In WordPress Admin, open Appearance > Themes and activate **Bharat Bulletin**.
3. Create menus for the `Primary Menu`, `Network Menu`, and `Footer Menu` locations.
4. Set site title, tagline, logo, and homepage settings from the Customizer.

## SEO and WordPress integration

- `header.php` uses `wp_head()` and semantic HTML landmarks.
- `functions.php` outputs meta description, canonical URL, Open Graph, Twitter card, and JSON-LD schema.
- `front-page.php`, `index.php`, `single.php`, and `archive.php` follow the WordPress template hierarchy.
- `index.html` is a WordPress REST API frontend shell for EC2 hosting. It does not include demo news content.

## Category hierarchy

Create these WordPress categories and use the slugs below so posts map into the homepage sections. The menu supports categories, subcategories, and sub-subcategories.

| Level | Category | Preferred slug | Also accepted |
| --- | --- | --- | --- |
| Category | Bihar | `bihar` | |
| Subcategory | Districts | `districts` | `jile` |
| Sub-subcategory | Patna | `patna` | |
| Sub-subcategory | Muzaffarpur | `muzaffarpur` | `mujahfarpur`, `mujffarpur` |
| Sub-subcategory | Darbhanga | `darbhanga` | |
| Sub-subcategory | Gaya | `gaya` | |
| Sub-subcategory | Bhagalpur | `bhagalpur` | |
| Subcategory | Crime | `crime` | |
| Sub-subcategory | Murder | `murder` | `hatya` |
| Sub-subcategory | Loot | `loot` | |
| Sub-subcategory | Police | `police` | |
| Subcategory | Politics | `politics` | `rajniti` |
| Sub-subcategory | Nitish Kumar | `nitish-kumar` | |
| Sub-subcategory | Election | `election` | `chunav` |
| Category | Education | `education` | `shiksha` |
| Subcategory | BSEB | `bseb` | |
| Subcategory | Result | `result` | |
| Subcategory | Admit Card | `admit-card` | |
| Category | Sarkari Naukri | `sarkari-naukri` | `jobs`, `naukri` |
| Subcategory | Teacher Jobs | `teacher-jobs` | `teacher-recruitment` |
| Subcategory | Police Jobs | `police-jobs` | `police-recruitment` |
| Subcategory | Exam Calendar | `exam-calendar` | |
| Category | Weather | `mausam` | `weather`, `bihar-weather` |
| Category | Video | `bihar-video` | `video` |
| Category | Visual Stories | `bihar-visual-stories` | `bihar-visulal-stories`, `visual-stories` |
| Category | Breaking News | `breaking-news` | `breaking-news-2` |
| Category | Badi Khabar | `badi-khabar` | `bihar-ki-badi-khabar`, `bihar-is-badi-khabar` |

Homepage behavior:

- Lead/top story: `breaking-news`, `badi-khabar`, or `bihar`.
- Top stack: `patna`, `crime`, `muzaffarpur`, `bseb`, `sarkari-naukri`, or `mausam`.
- Bihar big-news grid: Bihar and all major Bihar-related sections.
- Video row: `bihar-video`. Add a custom field named `youtube_url` or `video_url` to link directly to YouTube.
- Visual stories row: `bihar-visual-stories`.
- Weather displays only a temperature value. In WordPress, change it from Appearance > Customize > Site Identity > Weather temperature.
- Advertisement slots are grouped as Top Banner, Sidebar, In-feed, and Footer placements.

## Static frontend with WordPress

Host the static files on your EC2 web server and keep WordPress hosted wherever your production site runs.

1. Copy the static files to your EC2 web root, for example `/var/www/html`.
2. Open `assets/js/config.js`.
3. Set `wordpressUrl` to your WordPress site URL, for example:

```js
window.CRIME_KHABAR_CONFIG = {
  wordpressUrl: "https://your-wordpress-site.com",
  postsPerPage: 12
};
```

The frontend reads posts from:

```text
https://your-wordpress-site.com/wp-json/wp/v2/posts?_embed=1
```

Make sure your WordPress site has public posts and the REST API is not blocked by a security plugin.

## Newsletter form

The static preview validates email addresses and saves subscribers in the browser's `localStorage` under `crimeKhabarSubscribers`.

For production, connect the form to a newsletter provider such as Mailchimp, Brevo, ConvertKit, or a WordPress form plugin endpoint.

## Notes

- Static demo stories were removed. Add real posts in WordPress and assign categories for the homepage to populate.
- The homepage automatically uses your latest post as the lead story when posts exist.
- The sidebar supports widgets through the `Homepage Sidebar` widget area.

## TODO

- Create WordPress categories and structure:
  - Bihar (`bihar`)
  - Districts (`districts`)
    - Patna (`patna`)
    - Muzaffarpur (`muzaffarpur`)
    - Darbhanga (`darbhanga`)
    - Gaya (`gaya`)
    - Bhagalpur (`bhagalpur`)
  - Crime (`crime`)
    - Murder (`murder`)
    - Loot (`loot`)
    - Police (`police`)
  - Politics (`politics`)
    - Nitish Kumar (`nitish-kumar`)
    - Election (`election`)
  - Education (`education`)
    - BSEB (`bseb`)
    - Result (`result`)
    - Admit Card (`admit-card`)
  - Sarkari Naukri (`sarkari-naukri`)
    - Teacher Jobs (`teacher-jobs`)
    - Police Jobs (`police-jobs`)
    - Exam Calendar (`exam-calendar`)
  - Weather (`mausam`)
  - Video (`bihar-video`)
  - Visual Stories (`bihar-visual-stories`)
  - Breaking News (`breaking-news`)
  - Badi Khabar (`badi-khabar`)

- For each category and subcategory:
  - add posts in WordPress and assign the correct category slug
  - use subcategories for more specific news sections
  - make sure category slugs match the preferred values to keep the homepage sections working

- Weather update in WordPress:
  - go to `Appearance > Customize`
  - open `Site Identity`
  - update the `Weather temperature` field
  - publish the customizer changes

- If you want dynamic weather data later:
  - replace the customizer temperature with a weather API
  - display the API value in `sidebar.php` and any frontend templates

### Weather API (OpenWeatherMap)

- To show live weather for top Bihar cities, obtain an API key from OpenWeatherMap: https://openweathermap.org/api
- Configure via `assets/js/config.js` (preferred for static previews and to keep a single source):

```js
// assets/js/config.js
window.CRIME_KHABAR_CONFIG = {
  wordpressUrl: "https://your-wordpress-site.com",
  postsPerPage: 12,
  weatherApiKey: "YOUR_OPENWEATHERMAP_KEY",
  weatherCities: ["Patna", "Muzaffarpur", "Darbhanga", "Gaya", "Bhagalpur"],
  weatherUnits: "metric"
};
```

- The theme's weather module reads settings from `assets/js/config.js` exclusively. The carousel will show current temperature and a short description for each city.

Security note: API keys are visible to client-side JavaScript when used this way. For production, consider fetching weather data server-side or proxying the requests through your backend to keep keys secret.
