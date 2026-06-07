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
	echo '<ul class="main-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">होम</a></li>';
	foreach ( bharat_bulletin_category_tree() as $item ) {
		$url = bharat_bulletin_category_link( $item['key'] );
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $item['label'] ) . '</a>';
		if ( ! empty( $item['children'] ) ) {
			echo '<div class="sub-menu-wrap">';
			bharat_bulletin_render_category_tree( $item['children'] );
			echo '</div>';
		}
		echo '</li>';
	}

	echo '</ul>';
}

function bharat_bulletin_footer_fallback() {
	echo '<ul><li><a href="' . esc_url( bharat_bulletin_category_link( 'bihar' ) ) . '">बिहार</a></li><li><a href="' . esc_url( bharat_bulletin_category_link( 'crime' ) ) . '">क्राइम</a></li><li><a href="' . esc_url( bharat_bulletin_category_link( 'patna' ) ) . '">पटना</a></li><li><a href="' . esc_url( bharat_bulletin_category_link( 'sarkari_naukri' ) ) . '">सरकारी नौकरी</a></li></ul>';
}

function bharat_bulletin_category_map() {
	return array(
		'bihar'                => array( 'label' => 'बिहार', 'slugs' => array( 'bihar' ) ),
		'districts'            => array( 'label' => 'जिले', 'slugs' => array( 'districts', 'jile' ) ),
		'patna'                => array( 'label' => 'पटना', 'slugs' => array( 'patna' ) ),
		'muzaffarpur'          => array( 'label' => 'मुजफ्फरपुर', 'slugs' => array( 'muzaffarpur', 'mujahfarpur', 'mujffarpur' ) ),
		'darbhanga'            => array( 'label' => 'दरभंगा', 'slugs' => array( 'darbhanga' ) ),
		'gaya'                 => array( 'label' => 'गया', 'slugs' => array( 'gaya' ) ),
		'bhagalpur'            => array( 'label' => 'भागलपुर', 'slugs' => array( 'bhagalpur' ) ),
		'crime'                => array( 'label' => 'क्राइम', 'slugs' => array( 'crime' ) ),
		'murder'               => array( 'label' => 'हत्या', 'slugs' => array( 'murder', 'hatya' ) ),
		'loot'                 => array( 'label' => 'लूट', 'slugs' => array( 'loot' ) ),
		'police'               => array( 'label' => 'पुलिस', 'slugs' => array( 'police' ) ),
		'politics'             => array( 'label' => 'राजनीति', 'slugs' => array( 'politics', 'rajniti' ) ),
		'nitish_kumar'         => array( 'label' => 'नीतीश कुमार', 'slugs' => array( 'nitish-kumar' ) ),
		'election'             => array( 'label' => 'चुनाव', 'slugs' => array( 'election', 'chunav' ) ),
		'education'            => array( 'label' => 'शिक्षा', 'slugs' => array( 'education', 'shiksha' ) ),
		'bseb'                 => array( 'label' => 'BSEB', 'slugs' => array( 'bseb' ) ),
		'result'               => array( 'label' => 'रिजल्ट', 'slugs' => array( 'result' ) ),
		'admit_card'           => array( 'label' => 'एडमिट कार्ड', 'slugs' => array( 'admit-card' ) ),
		'sarkari_naukri'       => array( 'label' => 'सरकारी नौकरी', 'slugs' => array( 'sarkari-naukri', 'jobs', 'naukri' ) ),
		'teacher_jobs'         => array( 'label' => 'शिक्षक भर्ती', 'slugs' => array( 'teacher-jobs', 'teacher-recruitment' ) ),
		'police_jobs'          => array( 'label' => 'पुलिस भर्ती', 'slugs' => array( 'police-jobs', 'police-recruitment' ) ),
		'exam_calendar'        => array( 'label' => 'परीक्षा कैलेंडर', 'slugs' => array( 'exam-calendar' ) ),
		'mausam'               => array( 'label' => 'मौसम', 'slugs' => array( 'mausam', 'weather' ) ),
		'badi_khabar'          => array( 'label' => 'बड़ी खबर', 'slugs' => array( 'badi-khabar', 'bihar-ki-badi-khabar', 'bihar-is-badi-khabar' ) ),
		'breaking_news'        => array( 'label' => 'ब्रेकिंग न्यूज़', 'slugs' => array( 'breaking-news', 'breaking-news-2' ) ),
		'bihar_weather'        => array( 'label' => 'बिहार मौसम', 'slugs' => array( 'bihar-weather', 'mausam', 'weather' ) ),
		'bihar_video'          => array( 'label' => 'बिहार वीडियो', 'slugs' => array( 'bihar-video', 'video' ) ),
		'bihar_visual_stories' => array( 'label' => 'बिहार विजुअल स्टोरीज़', 'slugs' => array( 'bihar-visual-stories', 'bihar-visulal-stories', 'visual-stories' ) ),
	);
}

function bharat_bulletin_category_tree() {
	$terms = get_categories(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	$lookup = array();

	foreach ( $terms as $term ) {
		$lookup[ $term->term_id ] = array(
			'key'      => $term->slug,
			'label'    => $term->name,
			'slugs'    => array( $term->slug ),
			'children' => array(),
		);
	}

	$tree = array();

	foreach ( $terms as $term ) {
		if ( $term->parent && isset( $lookup[ $term->parent ] ) ) {
			$lookup[ $term->parent ]['children'][] = &$lookup[ $term->term_id ];
		} else {
			$tree[] = &$lookup[ $term->term_id ];
		}
	}

	return $tree;
}

function bharat_bulletin_render_category_tree( $items = array() ) {
	if ( empty( $items ) ) {
		return;
	}
	?>
	<ul>
		<?php foreach ( $items as $item ) : ?>
			<?php $url = bharat_bulletin_category_link( $item['key'] ); ?>
			<li>
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
				<?php
				if ( ! empty( $item['children'] ) ) {
					bharat_bulletin_render_category_tree( $item['children'] );
				}
				?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function bharat_bulletin_ad_categories() {
	return array(
		'top'     => 'Top Banner Advertisement',
		'sidebar' => 'Sidebar Advertisement',
		'in_feed' => 'In-feed Advertisement',
		'footer'  => 'Footer Advertisement',
	);
}

function bharat_bulletin_ad_slot( $key = 'sidebar' ) {
	$items = bharat_bulletin_ad_categories();
	$label = isset( $items[ $key ] ) ? $items[ $key ] : __( 'Advertisement', 'bharat-bulletin' );
	?>
	<div class="ad-box" data-ad-category="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></div>
	<?php
}

function bharat_bulletin_weather_temp() {
	return get_theme_mod( 'bharat_bulletin_weather_temp', '34°C' );
}

function bharat_bulletin_category_slugs( $keys ) {
	$map  = bharat_bulletin_category_map();
	$keys = (array) $keys;
	$slugs = array();

	foreach ( $keys as $key ) {
		if ( isset( $map[ $key ] ) ) {
			$slugs = array_merge( $slugs, $map[ $key ]['slugs'] );
			continue;
		}

		$term = get_term_by( 'slug', $key, 'category' );

		if ( $term && ! is_wp_error( $term ) ) {
			$slugs[] = $term->slug;
		}
	}

	return array_values( array_unique( $slugs ) );
}

function bharat_bulletin_category_link( $key ) {
	$slugs = bharat_bulletin_category_slugs( $key );

	if ( empty( $slugs ) ) {
		return home_url( '/' );
	}

	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );

		if ( $term && ! is_wp_error( $term ) ) {
			$url = get_category_link( $term );

			if ( $url ) {
				return $url;
			}
		}
	}

	return home_url( '/category/' . $slugs[0] . '/' );
}

function bharat_bulletin_topic_categories() {
	$items = array();
	$terms = get_categories(
		array(
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 5,
			'hide_empty' => true,
		)
	);

	foreach ( $terms as $term ) {
		$items[] = array(
			'label' => $term->name,
			'url'   => get_category_link( $term ),
		);
	}

	return $items;
}

function bharat_bulletin_topic_strip() {
	$items = bharat_bulletin_topic_categories();

	if ( empty( $items ) ) {
		return;
	}
	?>
	<ul class="topic-strip" aria-label="<?php esc_attr_e( 'Trending topics', 'bharat-bulletin' ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function bharat_bulletin_breaking_posts( $count = 20 ) {
	$posts = bharat_bulletin_category_posts( 'breaking_news', $count );

	if ( count( $posts ) < $count ) {
		$existing_ids = wp_list_pluck( $posts, 'ID' );
		$posts = array_merge( $posts, bharat_bulletin_recent_posts( $count - count( $posts ), $existing_ids ) );
	}

	return $posts;
}

function bharat_bulletin_breaking_ticker() {
	$posts = bharat_bulletin_breaking_posts( 20 );
	?>
	<section class="breaking" aria-label="<?php esc_attr_e( 'Breaking News', 'bharat-bulletin' ); ?>">
		<div class="breaking-inner">
			<strong class="breaking-label"><?php esc_html_e( 'Breaking News', 'bharat-bulletin' ); ?></strong>
			<div class="ticker-viewport">
				<ul class="ticker-list">
					<?php if ( $posts ) : ?>
						<?php foreach ( $posts as $post ) : ?>
						<?php $detail_url = get_home_url() . '/wp-content/themes/crimekhabhar/detail.html?id=' . get_the_ID( $post ); ?>
						<li><a href="<?php echo esc_url( $detail_url ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></li>
						<?php endforeach; ?>
					<?php else : ?>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'ताजा खबरें जल्द अपडेट होंगी', 'bharat-bulletin' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</section>
	<?php
}

function bharat_bulletin_category_posts( $keys, $count = 4, $exclude = array() ) {
	$slugs = bharat_bulletin_category_slugs( $keys );

	if ( empty( $slugs ) ) {
		return array();
	}

	return get_posts(
		array(
			'posts_per_page'      => $count,
			'post_status'         => 'publish',
			'post__not_in'        => array_map( 'absint', $exclude ),
			'ignore_sticky_posts' => true,
			'tax_query'           => array(
				array(
					'taxonomy'         => 'category',
					'field'            => 'slug',
					'terms'            => $slugs,
					'include_children' => true,
					'operator'         => 'IN',
				),
			),
		)
	);
}

function bharat_bulletin_recent_posts( $count = 4, $exclude = array() ) {
	return get_posts(
		array(
			'posts_per_page'      => $count,
			'post_status'         => 'publish',
			'post__not_in'        => array_map( 'absint', $exclude ),
			'ignore_sticky_posts' => true,
		)
	);
}

function bharat_bulletin_video_link( $post ) {
	$custom_url = get_post_meta( $post->ID, 'youtube_url', true );

	if ( ! $custom_url ) {
		$custom_url = get_post_meta( $post->ID, 'video_url', true );
	}

	return $custom_url ? $custom_url : get_permalink( $post );
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
	$image = $args['image'] ? $args['image'] : '';
	if ( false !== strpos( $image, 'crime-khabar-logo' ) ) {
		$image = '';
	}
	?>
	<article class="story-card<?php echo $args['featured'] ? ' featured' : ''; ?>">
		<a class="story-media" href="<?php echo esc_url( $args['url'] ); ?>">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="">
			<?php else : ?>
				<span class="story-placeholder" aria-hidden="true"></span>
			<?php endif; ?>
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
