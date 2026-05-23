<?php
/**
 * Bharat Bulletin theme setup.
 *
 * @package Bharat_Bulletin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/template-tags.php';

function bharat_bulletin_setup() {
	load_theme_textdomain( 'bharat-bulletin', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'bharat-bulletin' ),
			'top'     => __( 'Network Menu', 'bharat-bulletin' ),
			'footer'  => __( 'Footer Menu', 'bharat-bulletin' ),
		)
	);

	add_image_size( 'bb-featured', 760, 428, true );
	add_image_size( 'bb-card', 380, 214, true );
	add_image_size( 'bb-thumb', 160, 120, true );
}
add_action( 'after_setup_theme', 'bharat_bulletin_setup' );

function bharat_bulletin_scripts() {
	wp_enqueue_style( 'bharat-bulletin-fonts', 'https://fonts.googleapis.com/css2?family=Mukta:wght@400;600;700;800&family=Noto+Sans+Devanagari:wght@400;600;700;900&display=swap', array(), null );
	wp_enqueue_style( 'bharat-bulletin-style', get_stylesheet_uri(), array( 'bharat-bulletin-fonts' ), wp_get_theme()->get( 'Version' ) );
	wp_enqueue_script( 'bharat-bulletin-main', get_template_directory_uri() . '/assets/js/main.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'bharat_bulletin_scripts' );

function bharat_bulletin_meta_description() {
	if ( is_singular() ) {
		$description = has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( get_the_content() );
		return wp_trim_words( $description, 28, '' );
	}

	return get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : __( 'Latest Hindi news, breaking updates, videos, business, sports and technology stories.', 'bharat-bulletin' );
}

function bharat_bulletin_seo_meta() {
	$description = bharat_bulletin_meta_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );
	$image       = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}

	if ( ! $image ) {
		$image = bharat_bulletin_sample_image( 1 );
	}
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>">
	<meta property="og:type" content="<?php echo is_singular() ? 'article' : 'website'; ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<script type="application/ld+json">
		<?php
		echo wp_json_encode(
			array(
				'@context'  => 'https://schema.org',
				'@type'     => is_singular() ? 'NewsArticle' : 'NewsMediaOrganization',
				'name'      => get_bloginfo( 'name' ),
				'headline'  => $title,
				'url'       => $url,
				'image'     => $image,
				'publisher' => array(
					'@type' => 'Organization',
					'name'  => get_bloginfo( 'name' ),
				),
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);
		?>
	</script>
	<?php
}
add_action( 'wp_head', 'bharat_bulletin_seo_meta', 5 );

function bharat_bulletin_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Homepage Sidebar', 'bharat-bulletin' ),
			'id'            => 'homepage-sidebar',
			'description'   => __( 'Widgets shown in the right rail of the homepage.', 'bharat-bulletin' ),
			'before_widget' => '<aside id="%1$s" class="side-widget widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'bharat_bulletin_widgets_init' );

function bharat_bulletin_sample_image( $index = 1 ) {
	$images = array(
		'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1200&q=80',
		'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=80',
		'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?auto=format&fit=crop&w=1200&q=80',
		'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
		'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1200&q=80',
		'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
	);

	return $images[ absint( $index ) % count( $images ) ];
}
