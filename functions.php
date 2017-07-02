<?php
/**
 * Use a child theme for customization (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes).
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */
if(!defined('WP_AMP_THEME_VER')) {
	define('WP_AMP_THEME_VER', '0.2.2' );
}

$wp_amp_defaults_scripts = array(
	'amp-main' => 'https://cdn.ampproject.org/v0.js',
	'amp-form' => 'https://cdn.ampproject.org/v0/amp-form-0.1.js',
	'amp-sidebar' => 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js',
	//'amp-audio' => 'https://cdn.ampproject.org/v0/amp-audio-0.1.js',
	//'amp-analytics' => 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js',
	//'amp-accordion' => 'https://cdn.ampproject.org/v0/amp-accordion-0.1.js',
	//'amp-carousel' => 'https://cdn.ampproject.org/v0/amp-carousel-0.1.js',
	//'amp-install-serviceworker' => 'https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js',
);
$wp_amp_theme_extra_scripts = '';
$wp_amp_theme_extra_style_before = '';
$wp_amp_theme_extra_style_after = '';

if ( ! isset( $content_width ) ) {
	$content_width = 660;
}


if ( is_admin() ) {
	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.
} else {
	header('AMP-Access-Control-Allow-Source-Origin:'.site_url());
}


include_once get_template_directory() . '/theme-customize.php';


/**
 * Enqueue scripts
 * @since 0.2.0
 */
function wp_amp_theme_scripts() {
	global $wp_amp_defaults_scripts;
	$query_args = array(
		'family' => 'Roboto:100,300,400'
	);
	wp_register_style('amp_theme_google_fonts', add_query_arg( $query_args, "https://fonts.googleapis.com/css"));
    wp_enqueue_style('amp_theme_google_fonts');
	wp_enqueue_style('amp-accelerated-mobile-pages-style', get_stylesheet_uri() );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if (is_singular() && (comments_open() || get_comments_number())) {
		if (empty($wp_amp_defaults_scripts['amp-mustache'])) {
			$wp_amp_defaults_scripts['amp-mustache'] =  'https://cdn.ampproject.org/v0/amp-mustache-0.1.js';
		}
	}
	foreach ($wp_amp_defaults_scripts as $name => $src) {
		wp_register_script($name, $src, array(), null, false); 
    	wp_enqueue_script($name); 
	}
	

}
add_action( 'wp_enqueue_scripts', 'wp_amp_theme_scripts', 10 );

if ( ! function_exists( 'wp_amp_theme_setup' ) ) :

function wp_amp_theme_setup() {


	load_theme_textdomain( 'amp-accelerated-mobile-pages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'header-left' => esc_attr__( 'Header Left', 'amp-accelerated-mobile-pages' ),
		'footer'  => esc_attr__( 'Footer', 'amp-accelerated-mobile-pages' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	
	add_theme_support( 'custom-logo', array(
		'height'      => 248,
		'width'       => 248,
		'flex-height' => true,
	) );
	
	/**
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'editor-style.css') );


	$color_scheme  = amp_theme_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );
	/**
	 * Filter AMP - Accelerated Mobile Pages custom-background support arguments.
	 * @since 0.1.1
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *     @type string $default-color     		Default color of the background.
	 *     @type string $default-attachment     Default attachment of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters('amp_theme_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
		'wp-head-callback' => 'amp_theme_background_style',
	) ) );

	/**
	 * Filter AMP - Accelerated Mobile Pages custom-header support arguments.
	 * @since 0.1.1
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *     @type string $default_text_color     Default color of the header text.
	 *     @type int    $width                  Width in pixels of the custom header image. Default 954.
	 *     @type int    $height                 Height in pixels of the custom header image. Default 1300.
	 *     @type string $wp-head-callback       Callback function used to styles the header image and text
	 *                                          displayed on the blog.
	 * }
	 */
	$default_text_color = trim( $color_scheme[4], '#');
	add_theme_support( 'custom-header', apply_filters( 'amp_theme_custom_header_args', array(
		'default-text-color'     => $default_text_color,
		'width'                  => 900,
		'height'                 => 80,
		'flex-width'             => true,
		'wp-head-callback'       => 'amp_theme_header_style',
	) ) );


	
}
endif; // wp_amp_theme_setup
add_action( 'after_setup_theme', 'wp_amp_theme_setup' );

/**
 * Register widget area.
 *
 * @since 0.1
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function wp_amp_widgets_init() {
	register_sidebar( array(
		'name'          => esc_attr__( 'Sidebar Area', 'amp-accelerated-mobile-pages' ),
		'id'            => 'sidebar_amp',
		'description'   => esc_attr__( 'Add widgets here to appear in your sidebar.', 'amp-accelerated-mobile-pages' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_attr__( 'Header Right', 'amp-accelerated-mobile-pages' ),
		'id'            => 'header_rigth_amp',
		'description'   => esc_attr__( 'Add widgets here to appear in your header.', 'amp-accelerated-mobile-pages' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );
}
add_action( 'widgets_init', 'wp_amp_widgets_init' );


if (empty($wp_filesystem)) {
	require_once (ABSPATH . '/wp-admin/includes/file.php');
	WP_Filesystem();
}

require get_template_directory() . '/includes/template_tags.php';
require get_template_directory() . '/includes/compatibilities.php';
require get_template_directory() . '/includes/converter.php';
require get_template_directory() . '/includes/theme_amp.php';
require get_template_directory() . '/includes/tools.php';

if( !class_exists( 'AMP_Content' ) ) {
	
	require get_template_directory() . '/includes/amp-libs/utils/class-amp-dom-utils.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-base-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-base-embed-handler.php';


	require get_template_directory() . '/includes/amp-libs/utils/class-amp-html-utils.php';
	require get_template_directory() . '/includes/amp-libs/utils/class-amp-string-utils.php';

	require get_template_directory() . '/includes/amp-libs/class-amp-content.php';

	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-style-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-blacklist-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-img-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-video-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-iframe-sanitizer.php';
	require get_template_directory() . '/includes/amp-libs/sanitizers/class-amp-audio-sanitizer.php';

	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-twitter-embed.php';
	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-youtube-embed.php';
	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-instagram-embed.php';
	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-vine-embed.php';
	require get_template_directory() . '/includes/amp-libs/embeds/class-amp-facebook-embed.php';

	require get_template_directory() . '/includes/amp-libs/utils/class-amp-image-dimension-extractor.php';


}
?>