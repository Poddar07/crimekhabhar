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
- `preview.html` is a static SEO-friendly HTML preview for quick review before installing the theme.

## Deploy on Vercel with WordPress

Vercel hosts the HTML frontend. WordPress should be hosted separately, for example on Hostinger, Bluehost, Cloudways, WordPress.com Business, or any PHP hosting.

1. Upload or push this project to GitHub.
2. In Vercel, import the GitHub repository.
3. Keep the framework preset as `Other`.
4. Leave build command empty.
5. Leave output directory empty.
6. Deploy.
7. Open `assets/js/config.js`.
8. Set `wordpressUrl` to your WordPress site URL, for example:

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

The static/Vercel preview validates email addresses and saves subscribers in the browser's `localStorage` under `crimeKhabarSubscribers`.

For production, connect the form to a newsletter provider such as Mailchimp, Brevo, ConvertKit, or a WordPress form plugin endpoint.

## Notes

- Placeholder images use `placehold.co`; replace them with featured images in WordPress posts.
- The homepage automatically uses your latest post as the lead story when posts exist.
- The sidebar supports widgets through the `Homepage Sidebar` widget area.
