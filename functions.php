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
	wp_enqueue_script( 'bharat-bulletin-config', get_template_directory_uri() . '/assets/js/config.js', array(), wp_get_theme()->get( 'Version' ), true );
	wp_enqueue_script( 'bharat-bulletin-main', get_template_directory_uri() . '/assets/js/main.js', array(), wp_get_theme()->get( 'Version' ), true );
	wp_localize_script(
		'bharat-bulletin-main',
		'bharatBulletinSettings',
		array(
			'newsletterEndpoint' => esc_url_raw( rest_url( 'bharat-bulletin/v1/subscribe' ) ),
			'nonce'              => wp_create_nonce( 'wp_rest' ),
			// Weather settings exposed to client-side scripts (optional)
			'weatherApiKey'      => get_theme_mod( 'bharat_bulletin_weather_api_key', '' ),
			'weatherCities'      => array( 'Patna', 'Muzaffarpur', 'Darbhanga', 'Gaya', 'Bhagalpur' ),
			'weatherUnits'       => 'metric',
			'weatherEndpoint'    => esc_url_raw( rest_url( 'bharat-bulletin/v1/weather' ) ),
		)
	);

	// Enqueue weather script
	wp_enqueue_script( 'bharat-bulletin-weather', get_template_directory_uri() . '/assets/js/weather.js', array(), wp_get_theme()->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'bharat_bulletin_scripts' );

function bharat_bulletin_detail_url( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return home_url( '/' );
	}

	return add_query_arg(
		'id',
		absint( $post->ID ),
		home_url( '/detail.html' )
	);
}

function bharat_bulletin_post_detail_permalink( $permalink, $post ) {
	$post = get_post( $post );

	if ( $post && 'post' === $post->post_type ) {
		return bharat_bulletin_detail_url( $post );
	}

	return $permalink;
}
add_filter( 'post_link', 'bharat_bulletin_post_detail_permalink', 10, 2 );
add_filter( 'post_type_link', 'bharat_bulletin_post_detail_permalink', 10, 2 );

function bharat_bulletin_add_detail_rewrite_rule() {
	add_rewrite_rule( '^detail\.html$', 'index.php?bharat_bulletin_detail=1', 'top' );
}
add_action( 'init', 'bharat_bulletin_add_detail_rewrite_rule' );

function bharat_bulletin_detail_query_vars( $vars ) {
	$vars[] = 'bharat_bulletin_detail';
	return $vars;
}
add_filter( 'query_vars', 'bharat_bulletin_detail_query_vars' );

function bharat_bulletin_render_detail_template() {
	if ( get_query_var( 'bharat_bulletin_detail' ) ) {
		include get_template_directory() . '/detail.html';
		exit;
	}
}
add_action( 'template_redirect', 'bharat_bulletin_render_detail_template' );

function bharat_bulletin_flush_rewrite_rules() {
	bharat_bulletin_add_detail_rewrite_rule();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'bharat_bulletin_flush_rewrite_rules' );

function bharat_bulletin_newsletter_subscribers() {
	$subscribers = get_option( 'bharat_bulletin_newsletter_subscribers', array() );

	if ( ! is_array( $subscribers ) ) {
		$subscribers = array();
	}

	return array_values( array_unique( array_filter( array_map( 'sanitize_email', $subscribers ) ) ) );
}

function bharat_bulletin_add_newsletter_subscriber( $email ) {
	$email = sanitize_email( $email );

	if ( ! is_email( $email ) ) {
		return false;
	}

	$subscribers = bharat_bulletin_newsletter_subscribers();

	if ( in_array( $email, $subscribers, true ) ) {
		return true;
	}

	$subscribers[] = $email;
	return update_option( 'bharat_bulletin_newsletter_subscribers', $subscribers );
}

function bharat_bulletin_newsletter_subscribe( $request ) {
	$email = sanitize_email( $request->get_param( 'email' ) );

	if ( ! is_email( $email ) ) {
		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Please enter a valid email address.', 'bharat-bulletin' ),
			),
			400
		);
	}

	bharat_bulletin_add_newsletter_subscriber( $email );

	return new WP_REST_Response(
		array(
			'success'      => true,
			'message'      => __( 'Subscription received.', 'bharat-bulletin' ),
			'subscriber_count' => count( bharat_bulletin_newsletter_subscribers() ),
		),
		200
	);
}

function bharat_bulletin_register_newsletter_rest_route() {
	register_rest_route(
		'bharat-bulletin/v1',
		'/subscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'bharat_bulletin_newsletter_subscribe',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'bharat_bulletin_register_newsletter_rest_route' );

function bharat_bulletin_newsletter_admin_page() {
	add_theme_page(
		__( 'Newsletter Subscribers', 'bharat-bulletin' ),
		__( 'Newsletter Subscribers', 'bharat-bulletin' ),
		'manage_options',
		'bharat-bulletin-newsletter-subscribers',
		'bharat_bulletin_render_newsletter_admin_page'
	);
}
add_action( 'admin_menu', 'bharat_bulletin_newsletter_admin_page' );

/**
 * Server-side weather proxy for OpenWeatherMap.
 * Returns current weather for one or more cities and caches responses.
 */
function bharat_bulletin_fetch_weather_for_city( $city ) {
	$key = get_theme_mod( 'bharat_bulletin_weather_api_key', '' );

	if ( ! $key ) {
		return new WP_Error( 'no_api_key', __( 'No weather API key configured.', 'bharat-bulletin' ), array( 'status' => 400 ) );
	}

	$transient_key = 'bb_weather_' . sanitize_title( $city );
	$cached = get_transient( $transient_key );

	if ( $cached ) {
		return $cached;
	}

	$url = add_query_arg(
		array(
			'q'     => $city . ',IN',
			'units' => 'metric',
			'appid' => $key,
		),
		'https://api.openweathermap.org/data/2.5/weather'
	);

	$response = wp_remote_get( $url, array( 'timeout' => 10 ) );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( 200 !== intval( $code ) ) {
		return new WP_Error( 'weather_api_error', sprintf( __( 'Weather API returned %d', 'bharat-bulletin' ), $code ), array( 'status' => $code ) );
	}

	$data = json_decode( $body, true );

	if ( ! $data ) {
		return new WP_Error( 'weather_api_decode_error', __( 'Failed to decode weather API response', 'bharat-bulletin' ), array( 'status' => 500 ) );
	}

	// Cache for 10 minutes
	set_transient( $transient_key, $data, 10 * MINUTE_IN_SECONDS );

	return $data;
}

function bharat_bulletin_weather_rest_callback( $request ) {
	$cities_param = $request->get_param( 'cities' );

	if ( empty( $cities_param ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => __( 'No cities specified.', 'bharat-bulletin' ) ), 400 );
	}

	$cities = is_array( $cities_param ) ? $cities_param : explode( ',', $cities_param );
	$cities = array_map( 'trim', $cities );

	$result = array();

	foreach ( $cities as $city ) {
		$data = bharat_bulletin_fetch_weather_for_city( $city );
		if ( is_wp_error( $data ) ) {
			$result[ $city ] = array( 'error' => $data->get_error_message() );
		} else {
			$result[ $city ] = $data;
		}
	}

	return rest_ensure_response( $result );
}

function bharat_bulletin_register_weather_route() {
	register_rest_route(
		'bharat-bulletin/v1',
		'/weather',
		array(
			'methods'             => 'GET',
			'callback'            => 'bharat_bulletin_weather_rest_callback',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'bharat_bulletin_register_weather_route' );

function bharat_bulletin_render_newsletter_admin_page() {
	$subscribers = bharat_bulletin_newsletter_subscribers();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Newsletter Subscribers', 'bharat-bulletin' ); ?></h1>
		<p><?php printf( esc_html__( 'Total subscribers: %d', 'bharat-bulletin' ), count( $subscribers ) ); ?></p>
		<?php if ( empty( $subscribers ) ) : ?>
			<p><?php esc_html_e( 'No subscribers yet.', 'bharat-bulletin' ); ?></p>
		<?php else : ?>
			<table class="widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Email Address', 'bharat-bulletin' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $subscribers as $subscriber ) : ?>
						<tr>
							<td><?php echo esc_html( $subscriber ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}

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
		$image = get_template_directory_uri() . '/assets/images/crime-khabar-logo.jpeg';
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

function bharat_bulletin_register_post_meta() {
	register_post_meta(
		'post',
		'youtube_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => '__return_true',
		)
	);

	register_post_meta(
		'post',
		'video_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => '__return_true',
		)
	);
}
add_action( 'init', 'bharat_bulletin_register_post_meta' );

function bharat_bulletin_customize_register( $wp_customize ) {
	$wp_customize->add_setting(
		'bharat_bulletin_weather_temp',
		array(
			'default'           => '34°C',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'bharat_bulletin_weather_temp',
		array(
			'label'   => __( 'Weather temperature', 'bharat-bulletin' ),
			'section' => 'title_tagline',
			'type'    => 'text',
		)
	);

	// Optional: API key for OpenWeatherMap to show live weather
	$wp_customize->add_setting(
		'bharat_bulletin_weather_api_key',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'bharat_bulletin_weather_api_key',
		array(
			'label'   => __( 'Weather API Key (OpenWeatherMap)', 'bharat-bulletin' ),
			'section' => 'title_tagline',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'bharat_bulletin_customize_register' );
