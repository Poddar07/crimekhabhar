<?php
/**
 * Main homepage template.
 *
 * @package Bharat_Bulletin
 */

get_header();

$used_post_ids = array();
$featured_post = null;

$featured_posts = bharat_bulletin_category_posts( array( 'breaking_news', 'badi_khabar', 'bihar' ), 1 );

if ( $featured_posts ) {
	$featured_post   = $featured_posts[0];
	$used_post_ids[] = $featured_post->ID;
}

if ( ! $featured_post ) {
	$recent_posts = bharat_bulletin_recent_posts( 1 );

	if ( $recent_posts ) {
		$featured_post   = $recent_posts[0];
		$used_post_ids[] = $featured_post->ID;
	}
}

$mini_posts = bharat_bulletin_recent_posts( 9, $used_post_ids );
$used_post_ids = array_merge( $used_post_ids, wp_list_pluck( $mini_posts, 'ID' ) );

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


		<?php if ( $featured_post || $mini_posts ) : ?>
			<section class="lead-grid">
				<?php
				if ( $featured_post ) :
					bharat_bulletin_card(
						array(
							'title'    => get_the_title( $featured_post ),
							'excerpt'  => get_the_excerpt( $featured_post ),
							'url'      => get_permalink( $featured_post ),
							'image'    => has_post_thumbnail( $featured_post ) ? get_the_post_thumbnail_url( $featured_post, 'bb-featured' ) : '',
							'badge'    => bharat_bulletin_post_badge( $featured_post ),
							'featured' => true,
						)
					);
				endif;
				?>
				<div class="stacked-news">
					<?php foreach ( $mini_posts as $post ) : ?>
						<article class="story-mini">
							<a class="thumb" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
								<img src="<?php echo esc_url( has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'thumbnail' ) : get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" alt="">
							</a>
							<div>
								<h3><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3>
								<div class="meta"><span><?php echo esc_html( bharat_bulletin_post_badge( $post ) ); ?></span></div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
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
						<a class="visual-card" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
							<img src="<?php echo esc_url( has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'bb-card' ) : get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg' ); ?>" alt="">
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
