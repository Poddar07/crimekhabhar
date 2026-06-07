<?php
/**
 * Main homepage template.
 *
 * @package Bharat_Bulletin
 */

get_header();

$latest_posts = bharat_bulletin_recent_posts( 5 );
$visual_posts = bharat_bulletin_category_posts( 'bihar_visual_stories', 5 );

function bharat_bulletin_post_badge( $post ) {
	$categories = get_the_category( $post );

	if ( ! empty( $categories ) ) {
		return $categories[0]->name;
	}

	return __( 'ताजा', 'bharat-bulletin' );
}
?>

<main class="content-shell">
	<div class="main-content">


		<?php if ( $latest_posts ) : ?>
			<section class="lead-grid latest-carousel" data-latest-carousel aria-label="<?php esc_attr_e( 'Latest headlines', 'bharat-bulletin' ); ?>">
				<div class="section-head latest-carousel-head">
					<h2 class="section-title"><?php esc_html_e( 'Latest Headlines', 'bharat-bulletin' ); ?></h2>
					<div class="carousel-controls" aria-label="<?php esc_attr_e( 'Latest post controls', 'bharat-bulletin' ); ?>">
						<button class="carousel-btn" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous post', 'bharat-bulletin' ); ?>">&lsaquo;</button>
						<button class="carousel-btn" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Next post', 'bharat-bulletin' ); ?>">&rsaquo;</button>
					</div>
				</div>
				<div class="latest-carousel-viewport">
					<div class="latest-carousel-track">
						<?php foreach ( $latest_posts as $post ) : ?>
							<?php
							$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'bb-featured' ) : '';
							if ( false !== strpos( $image, 'crime-khabar-logo' ) ) {
								$image = '';
							}
							?>
							<article class="story-card featured latest-slide" data-carousel-slide>
								<a class="story-media" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
									<?php if ( $image ) : ?>
										<img src="<?php echo esc_url( $image ); ?>" alt="">
									<?php else : ?>
										<span class="story-placeholder" aria-hidden="true"></span>
									<?php endif; ?>
									<span class="media-badge"><?php echo esc_html( bharat_bulletin_post_badge( $post ) ); ?></span>
								</a>
								<div class="story-body">
									<div class="category-kicker"><?php esc_html_e( 'Headline', 'bharat-bulletin' ); ?></div>
									<h1><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h1>
									<p class="summary"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 28, '...' ) ); ?></p>
									<div class="meta"><span><?php echo esc_html( get_the_date( 'j M Y', $post ) ); ?></span></div>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="carousel-dots" data-carousel-dots aria-label="<?php esc_attr_e( 'Latest post pages', 'bharat-bulletin' ); ?>"></div>
			</section>
		<?php else : ?>
			<div class="empty-state"><?php esc_html_e( 'अभी कोई खबर नहीं मिली।', 'bharat-bulletin' ); ?></div>
		<?php endif; ?>

		<?php if ( $visual_posts ) : ?>
			<section class="section">
				<div class="section-head">
					<h2 class="section-title"><?php esc_html_e( 'विजुअल स्टोरीज़', 'bharat-bulletin' ); ?></h2>
					<a class="section-link" href="<?php echo esc_url( bharat_bulletin_category_link( 'bihar_visual_stories' ) ); ?>"><?php esc_html_e( 'देखें', 'bharat-bulletin' ); ?></a>
				</div>
				<div class="visual-strip">
					<?php foreach ( $visual_posts as $post ) : ?>
						<?php
						$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'bb-card' ) : '';
						if ( false !== strpos( $image, 'crime-khabar-logo' ) ) {
							$image = '';
						}
						?>
						<a class="visual-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="">
							<?php else : ?>
								<span class="visual-placeholder" aria-hidden="true"></span>
							<?php endif; ?>
							<h3><?php echo esc_html( get_the_title( $post ) ); ?></h3>
						</a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
	</div>

	<?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>
