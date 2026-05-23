<?php
/**
 * Template helper functions.
 *
 * @package Bharat_Bulletin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bharat_bulletin_top_fallback() {
	echo '<ul class="network-links"><li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li><li><a href="' . esc_url( home_url( '/category/business/' ) ) . '">Business</a></li><li><a href="' . esc_url( home_url( '/category/sports/' ) ) . '">Sports</a></li><li><a href="' . esc_url( home_url( '/category/lifestyle/' ) ) . '">Lifestyle</a></li></ul>';
}

function bharat_bulletin_primary_fallback() {
	echo '<ul class="main-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">होम</a></li><li><a href="' . esc_url( home_url( '/category/bihar/' ) ) . '">बिहार</a></li><li><a href="' . esc_url( home_url( '/category/crime/' ) ) . '">क्राइम</a></li><li><a href="' . esc_url( home_url( '/category/politics/' ) ) . '">राजनीति</a></li><li><a href="' . esc_url( home_url( '/category/districts/' ) ) . '">जिले</a></li><li><a href="' . esc_url( home_url( '/category/education/' ) ) . '">शिक्षा</a></li><li><a href="' . esc_url( home_url( '/category/jobs/' ) ) . '">नौकरी</a></li><li><a href="' . esc_url( home_url( '/category/weather/' ) ) . '">मौसम</a></li><li><a href="' . esc_url( home_url( '/category/video/' ) ) . '">वीडियो</a></li></ul>';
}

function bharat_bulletin_footer_fallback() {
	echo '<ul><li><a href="' . esc_url( home_url( '/category/bihar/' ) ) . '">बिहार</a></li><li><a href="' . esc_url( home_url( '/category/crime/' ) ) . '">क्राइम</a></li><li><a href="' . esc_url( home_url( '/category/districts/' ) ) . '">जिले</a></li><li><a href="' . esc_url( home_url( '/category/jobs/' ) ) . '">नौकरी</a></li></ul>';
}

function bharat_bulletin_card( $args = array() ) {
	$defaults = array(
		'title'    => get_the_title(),
		'excerpt'  => get_the_excerpt(),
		'url'      => get_permalink(),
		'image'    => '',
		'badge'    => '',
		'featured' => false,
		'index'    => 1,
	);
	$args = wp_parse_args( $args, $defaults );
	$image = $args['image'] ? $args['image'] : bharat_bulletin_sample_image( $args['index'] );
	?>
	<article class="story-card<?php echo $args['featured'] ? ' featured' : ''; ?>">
		<a class="story-media" href="<?php echo esc_url( $args['url'] ); ?>">
			<img src="<?php echo esc_url( $image ); ?>" alt="">
			<?php if ( $args['badge'] ) : ?>
				<span class="media-badge"><?php echo esc_html( $args['badge'] ); ?></span>
			<?php endif; ?>
		</a>
		<div class="story-body">
			<?php if ( $args['featured'] ) : ?>
				<h1><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a></h1>
			<?php else : ?>
				<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['title'] ); ?></a></h3>
			<?php endif; ?>
			<?php if ( $args['excerpt'] ) : ?>
				<p class="summary"><?php echo esc_html( wp_trim_words( $args['excerpt'], 22 ) ); ?></p>
			<?php endif; ?>
			<div class="meta"><span><?php esc_html_e( 'अपडेटेड अभी', 'bharat-bulletin' ); ?></span><span>•</span><span><?php esc_html_e( '3 मिनट पढ़ें', 'bharat-bulletin' ); ?></span></div>
		</div>
	</article>
	<?php
}
