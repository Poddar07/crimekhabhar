<?php
/**
 * Header template.
 *
 * @package Bharat_Bulletin
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site-wrap">
	<header class="masthead">
		<div class="masthead-inner">
			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : ?>
					<?php
					echo wp_get_attachment_image(
						get_theme_mod( 'custom_logo' ),
						'full',
						false,
						array(
							'class' => 'custom-logo',
							'alt'   => get_bloginfo( 'name' ),
						)
					);
					?>
				<?php else : ?>
					<img class="fallback-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" alt="<?php esc_attr_e( 'क्राइम खबर', 'bharat-bulletin' ); ?>">
				<?php endif; ?>
				<span class="brand-text">
					<strong><?php bloginfo( 'name' ); ?></strong>
					<span><?php bloginfo( 'description' ); ?></span>
				</span>
			</a>
			<?php get_search_form(); ?>
			<button class="icon-button menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Toggle menu', 'bharat-bulletin' ); ?>">☰</button>
		</div>
	</header>

	<nav class="main-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'bharat-bulletin' ); ?>">
		<div class="nav-inner">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'main-menu',
					'fallback_cb'    => 'bharat_bulletin_primary_fallback',
				)
			);
			?>
		</div>
	</nav>

	<section class="breaking" aria-label="<?php esc_attr_e( 'Breaking News', 'bharat-bulletin' ); ?>">
		<div class="breaking-inner">
			<strong class="breaking-label"><?php esc_html_e( 'Breaking News', 'bharat-bulletin' ); ?></strong>
			<ul class="ticker-list">
				<li><a href="#">लोकसभा में आज अर्थव्यवस्था पर बड़ी चर्चा</a></li>
				<li><a href="#">मानसून अपडेट: कई राज्यों में तेज बारिश का अलर्ट</a></li>
				<li><a href="#">टीम इंडिया ने सीरीज के लिए नई टीम घोषित की</a></li>
			</ul>
		</div>
	</section>

	<ul class="topic-strip" aria-label="<?php esc_attr_e( 'Trending topics', 'bharat-bulletin' ); ?>">
		<li><a href="<?php echo esc_url( home_url( '/category/bihar/' ) ); ?>">बिहार</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/crime/' ) ); ?>">क्राइम</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/patna/' ) ); ?>">पटना</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/districts/' ) ); ?>">जिले</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/education/' ) ); ?>">शिक्षा</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/jobs/' ) ); ?>">नौकरी</a></li>
		<li><a href="<?php echo esc_url( home_url( '/category/weather/' ) ); ?>">मौसम</a></li>
	</ul>
