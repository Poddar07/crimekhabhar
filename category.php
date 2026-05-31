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
								<ol class="category-post-list">
									<?php
									while ( $child_query->have_posts() ) :
										$child_query->the_post();
										?>
										<li>
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											<span class="meta"><?php echo esc_html( get_the_date() ); ?></span>
										</li>
									<?php endwhile; ?>
								</ol>
							<?php else : ?>
								<p class="empty-state"><?php esc_html_e( 'No posts yet in this subcategory.', 'bharat-bulletin' ); ?></p>
							<?php endif; ?>
						</section>
						<?php wp_reset_postdata(); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php if ( have_posts() ) : ?>
					<ol class="category-post-list category-post-list-full">
						<?php
						while ( have_posts() ) :
							the_post();
							?>
							<li>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								<span class="meta"><?php echo esc_html( get_the_date() ); ?></span>
							</li>
						<?php endwhile; ?>
					</ol>
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
