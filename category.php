<?php
/**
 * Category template.
 *
 * Parent category:
 * - Shows all subcategories as blocks.
 * - Each subcategory block shows top 5 latest posts.
 *
 * Subcategory:
 * - Shows list-format posts for that subcategory.
 *
 * @package Bharat_Bulletin
 */

get_header();

$current_term = get_queried_object();
$child_terms  = array();

if ( $current_term instanceof WP_Term ) {
	$child_terms = get_categories(
		array(
			'taxonomy'   => 'category',
			'parent'     => $current_term->term_id,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
}

function bharat_bulletin_category_card( $post ) {
	$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'bb-card' ) : '';
	if ( false !== strpos( $image, 'crime-khabar-logo' ) ) {
		$image = '';
	}
	?>
	<article class="category-post-card">
		<a class="category-post-media" href="<?php echo esc_url( get_permalink( $post ) ); ?>">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="">
			<?php else : ?>
				<span class="category-post-placeholder" aria-hidden="true"></span>
			<?php endif; ?>
		</a>
		<div class="category-post-body">
			<h2><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h2>
			<div class="category-post-date"><?php echo esc_html( get_the_date( 'M j Y g:i A', $post ) ); ?></div>
		</div>
	</article>
	<?php
}
?>
<main class="content-shell">
	<div class="main-content">
		<section class="section">
			<div class="section-head">
				<h1 class="section-title"><?php single_cat_title(); ?></h1>
			</div>

			<?php if ( ! empty( $child_terms ) ) : ?>
				<div class="subcategory-blocks">
					<?php foreach ( $child_terms as $child_term ) : ?>
						<?php
						$child_query = new WP_Query(
							array(
								'post_type'           => 'post',
								'post_status'         => 'publish',
								'posts_per_page'      => 5,
								'ignore_sticky_posts' => true,
								'no_found_rows'       => true,
								'cat'                 => intval( $child_term->term_id ),
							)
						);
						?>
						<section class="subcategory-block">
							<div class="subcategory-head">
								<h2><a href="<?php echo esc_url( get_category_link( $child_term ) ); ?>"><?php echo esc_html( $child_term->name ); ?></a></h2>
							</div>

							<?php if ( $child_query->have_posts() ) : ?>
								<div class="category-card-grid">
									<?php
									while ( $child_query->have_posts() ) :
										$child_query->the_post();
										bharat_bulletin_category_card( get_post() );
									<?php endwhile; ?>
								</div>
							<?php else : ?>
								<p class="empty-state"><?php esc_html_e( 'No posts yet in this subcategory.', 'bharat-bulletin' ); ?></p>
							<?php endif; ?>
						</section>
						<?php wp_reset_postdata(); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php if ( have_posts() ) : ?>
					<div class="category-card-grid category-card-grid-full">
						<?php
						while ( have_posts() ) :
							the_post();
							bharat_bulletin_category_card( get_post() );
						<?php endwhile; ?>
					</div>
					<?php the_posts_pagination(); ?>
				<?php else : ?>
					<p class="empty-state"><?php esc_html_e( 'No posts found in this subcategory.', 'bharat-bulletin' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</section>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php get_footer(); ?>
