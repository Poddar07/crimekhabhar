<?php
/**
 * Archive template.
 *
 * @package Bharat_Bulletin
 */

get_header();
?>
<main class="content-shell">
	<div class="main-content">
		<section class="section">
			<div class="section-head">
				<h1 class="section-title"><?php the_archive_title(); ?></h1>
			</div>
			<div class="news-grid">
				<?php
				if ( have_posts() ) :
					$index = 1;
					while ( have_posts() ) :
						the_post();
						bharat_bulletin_card(
							array(
								'title'   => get_the_title(),
								'excerpt' => get_the_excerpt(),
								'url'     => get_permalink(),
								'image'   => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'bb-card' ) : '',
								'index'   => $index,
							)
						);
						$index++;
					endwhile;
				endif;
				?>
			</div>
			<?php the_posts_pagination(); ?>
		</section>
	</div>
	<?php get_sidebar(); ?>
</main>
<?php get_footer(); ?>
