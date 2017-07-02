<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */

/**
 * Register color schemes for AMP - Accelerated Mobile Pages
 *
 * Can be filtered with {@see 'amp_theme_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Main Background Color.
 * 2. Sidebar Background Color.
 * 3. Box Background Color.
 * 4. Main Text and Link Color.
 * 5. Sidebar Text and Link Color.
 * 6. Meta Box Background Color.
 *
 * @since 0.1.1
 *
 * @return array An associative array of color scheme options.
 */
function amp_theme_get_color_schemes() {
	/**
	 * Filter the color schemes registered for use with AMP - Accelerated Mobile Pages
	 *
	 * The default schemes include 'default', 'dark', 'yellow', 'pink', 'purple', and 'blue'.
	 *
	 * @since 0.1.1
	 *
	 * @param array $schemes {
	 *     Associative array of color schemes data.
	 *
	 *     @type array $slug {
	 *         Associative array of information for setting up the color scheme.
	 *
	 *         @type string $label  Color scheme label.
	 *         @type array  $colors HEX codes for default colors prepended with a hash symbol ('#').
	 *                              Colors are defined in the following order: Main background, sidebar
	 *                              background, box background, main text and link, sidebar text and link,
	 *                              meta box background.
	 *     }
	 * }
	 */
	return apply_filters( 'amp_theme_color_schemes', array(
		'default' => array(
			'label'  => __( 'Default', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#f1f1f1',
				'#ffffff',
				'#ffffff',
				'#333333',
				'#333333',
				'#f7f7f7',
			),
		),
		'dark'    => array(
			'label'  => __( 'Dark', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#111111',
				'#202020',
				'#202020',
				'#bebebe',
				'#bebebe',
				'#1b1b1b',
			),
		),
		'yellow'  => array(
			'label'  => __( 'Yellow', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#f4ca16',
				'#ffdf00',
				'#ffffff',
				'#111111',
				'#111111',
				'#f1f1f1',
			),
		),
		'pink'    => array(
			'label'  => __( 'Pink', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#ffe5d1',
				'#e53b51',
				'#ffffff',
				'#352712',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'purple'  => array(
			'label'  => __( 'Purple', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#674970',
				'#2e2256',
				'#ffffff',
				'#2e2256',
				'#ffffff',
				'#f1f1f1',
			),
		),
		'blue'   => array(
			'label'  => __( 'Blue', 'amp-accelerated-mobile-pages' ),
			'colors' => array(
				'#e9f2f9',
				'#55c3dc',
				'#ffffff',
				'#22313f',
				'#ffffff',
				'#f1f1f1',
			),
		),
	) );
}

if ( ! function_exists( 'amp_theme_get_color_scheme' ) ) :
/**
 * Get the current AMP color scheme.
 * @since 0.1.1
 * @return array An associative array of either the current or default color scheme hex values.
 */
function amp_theme_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = amp_theme_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // amp_theme_get_color_scheme

if ( ! function_exists( 'amp_theme_header_style' ) ) :
/**
 * Styles the header image on the blog.
 * @since 0.1.1
 */
function amp_theme_header_style() {
	global $wp_amp_theme_extra_style_before;
	$header_image = get_header_image();

	// If no custom options for text are set, let's bail.
	if ( empty( $header_image ) && display_header_text() ) {
		return;
	}
	// If we get this far, we have custom styles. Let's do this.
	?>
	<?php
		// Has a Custom Header been added?
		if ( ! empty( $header_image ) ) :
	
		$wp_amp_theme_extra_style_before .= '.header.fixed {
			background-image: url('.$header_image.');
			background-repeat: no-repeat;
			background-position: 50% 50%;
			-webkit-background-size: cover;
			-moz-background-size:    cover;
			-o-background-size:      cover;
			background-size:         cover;
		}';
		endif;
	?>
	<?php
}
endif; // amp_theme_header_style

if ( ! function_exists( 'amp_theme_background_style' ) ) :
/**
 * Styles the background image on the blog.
 * @since 0.1.1
 */
function amp_theme_background_style() {
	global $wp_amp_theme_extra_style_before;
     // $background is the saved custom image, or the default image.
    $background = set_url_scheme( get_background_image() );
 
    // $color is the saved custom color.
    // A default has to be specified in style.css. It will not be printed here.
    $color = get_background_color();
 
    if ( $color === get_theme_support( 'custom-background', 'default-color' ) ) {
        $color = false;
    }
 
    if ( ! $background && ! $color ) {
        if ( is_customize_preview() ) {
            echo '<style type="text/css" id="custom-background-css"></style>';
        }
        return;
    }
 
    $style = $color ? "background-color: #$color;" : '';
 
    if ( $background ) {
        $image = ' background-image: url("' . esc_url_raw( $background ) . '");';
 
        // Background Position.
        $position_x = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
        $position_y = get_theme_mod( 'background_position_y', get_theme_support( 'custom-background', 'default-position-y' ) );
 
        if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
            $position_x = 'left';
        }
 
        if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
            $position_y = 'top';
        }
 
        $position = " background-position: $position_x $position_y;";
 
        // Background Size.
        $size = get_theme_mod( 'background_size', get_theme_support( 'custom-background', 'default-size' ) );
 
        if ( ! in_array( $size, array( 'auto', 'contain', 'cover' ), true ) ) {
            $size = 'auto';
        }
 
        $size = " background-size: $size;";
 
        // Background Repeat.
        $repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
 
        if ( ! in_array( $repeat, array( 'repeat-x', 'repeat-y', 'repeat', 'no-repeat' ), true ) ) {
            $repeat = 'repeat';
        }
 
        $repeat = " background-repeat: $repeat;";
 
        // Background Scroll.
        $attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
 
        if ( 'fixed' !== $attachment ) {
            $attachment = 'scroll';
        }
 
        $attachment = " background-attachment: $attachment;";
 
        $style .= $image . $position . $size . $repeat . $attachment;
    }
    $wp_amp_theme_extra_style_before .= 'body.custom-background { '.trim( $style ).' }';
?>

<?php
}
endif;
?>