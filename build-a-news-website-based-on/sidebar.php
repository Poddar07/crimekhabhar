<?php
/**
 * Sidebar template.
 *
 * @package Bharat_Bulletin
 */

$trending_posts = bharat_bulletin_recent_posts( 5 );
?>
<aside class="sidebar">
	<div data-ad-slots class="recommended-slot"></div>

	<section class="side-widget">
		<h2><?php esc_html_e( 'Trending Bihar News', 'bharat-bulletin' ); ?></h2>
		<?php if ( $trending_posts ) : ?>
			<ol class="ranked-list">
				<?php foreach ( $trending_posts as $index => $post ) : ?>
					<li>
						<span class="rank"><?php echo esc_html( $index + 1 ); ?></span>
						<a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'अभी कोई खबर नहीं मिली।', 'bharat-bulletin' ); ?></p>
		<?php endif; ?>
	</section>

	<section class="side-widget weather-widget">
		<h2><?php esc_html_e( 'बिहार मौसम', 'bharat-bulletin' ); ?></h2>
		<div class="weather-temp" aria-hidden="true"><?php echo esc_html( bharat_bulletin_weather_temp() ); ?></div>
		<div class="weather-carousel" data-weather-carousel aria-label="Weather for top cities"></div>
	</section>

	<?php if ( is_active_sidebar( 'homepage-sidebar' ) ) : ?>
		<?php dynamic_sidebar( 'homepage-sidebar' ); ?>
	<?php endif; ?>

	<section class="side-widget newsletter">
		<h2><?php esc_html_e( 'बिहार न्यूज़ अलर्ट', 'bharat-bulletin' ); ?></h2>
		<form>
			<input type="email" placeholder="<?php esc_attr_e( 'Email address', 'bharat-bulletin' ); ?>">
			<button type="submit"><?php esc_html_e( 'Subscribe', 'bharat-bulletin' ); ?></button>
		</form>
	</section>
</aside>
