<?php
/**
 * Single post template.
 *
 * @package Bharat_Bulletin
 */

get_header();
?>
<main class="content-shell">
	<div class="main-content">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'story-card featured' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="story-media"><?php the_post_thumbnail( 'bb-featured' ); ?></div>
				<?php endif; ?>
				<div class="story-body">
					<h1><?php the_title(); ?></h1>
					<div class="meta"><span><?php echo esc_html( get_the_date() ); ?></span><span>•</span><span><?php the_author(); ?></span></div>
					<div class="summary"><?php the_content(); ?></div>
				</div>
			</article>
			<?php
			the_post_navigation();
		endwhile;
		?>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php get_footer(); ?>
