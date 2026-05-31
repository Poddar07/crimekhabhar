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
	<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" type="image/jpeg">
	<link rel="shortcut icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" type="image/jpeg">
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
			<div class="header-status" aria-label="Live updates">
				<span class="status-dot" aria-hidden="true"></span>
				<span>Live Updates</span>
			</div>
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

	<?php bharat_bulletin_breaking_ticker(); ?>

	<?php bharat_bulletin_topic_strip(); ?>

