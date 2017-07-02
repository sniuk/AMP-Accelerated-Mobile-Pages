<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */
if( !class_exists( 'wp_amp' ) ) {
	class wp_amp {

		public static $styles_scripts_added = false;
		/**
		* Static function hooks
		* @access public
		* @return void
		* @since 0.1.1
		*/
		public static function hooks() {
			add_filter( 'the_content', array(__CLASS__, 'amp_content' ), 9998, 1);
			add_filter( 'the_content', array(__CLASS__, 'get_post_scripts_styles' ), 9999, 1);
			add_action( 'wp_head', array(__CLASS__, 'head') );
			add_action( 'wp_footer', array(__CLASS__, 'footer') );
		}
		/**
		* Static function head
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function head() {
			wp_amp_compatibilities::get_style_theme();
		}
		/**
		* Static function footer
		* @access public
		* @return void
		* @since 0.2
		*/
		public static function footer() {
			
		}
		/**
		* Static function comment_form
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function comment_form() {
			wp_amp_compatibilities::comment_form();
		}
		/**
		* Static function amp_content
		* @access public
		* @return $html_content String with html of post content.
		* @since 0.2.0
		*/
		public static function amp_content($html_content) {
			global $content_width;
			$html_content = self::get_amp_content($html_content);
			return $html_content;
		}
		/**
		* Static function get_amp_content
		* @access public
		* @return $html_content String with html of post content.
		* @since 0.2.0
		*/
		public static function get_amp_content($html_content) {
			global $content_width;
			if (!self::$styles_scripts_added) { 
				return $html_content;
			}
			$amp_content = new AMP_Content($html_content,
				apply_filters( 'amp_content_embed_handlers', array(
					'AMP_Twitter_Embed_Handler' => array(),
					'AMP_YouTube_Embed_Handler' => array(),
					'AMP_Instagram_Embed_Handler' => array(),
					'AMP_Vine_Embed_Handler' => array(),
					'AMP_Facebook_Embed_Handler' => array(),
				), null ),
				apply_filters( 'amp_content_sanitizers', array(
					 'AMP_Style_Sanitizer' => array(),
					 'AMP_Blacklist_Sanitizer' => array(),
					 'AMP_Img_Sanitizer' => array(),
					 'AMP_Video_Sanitizer' => array(),
					 'AMP_Audio_Sanitizer' => array(),
					 'AMP_Iframe_Sanitizer' => array(
						 'add_placeholder' => true,
					 ),
				), null ),
				array(
					'content_max_width' => $content_width,
				)
			);
			$content_amp_string = $amp_content->get_amp_content();
			if (!empty($content_amp_string)) {
				$html_content = $content_amp_string;
			}
			return $html_content;
		}
		/**
		* Static function get_post_scripts_styles
		* @access public
		* @return $html_content String with html of post content.
		* @since 0.2.0
		*/
		public static function get_post_scripts_styles($html_content) {
			global $content_width, $wp_amp_theme_extra_style_before, $wp_amp_defaults_scripts;
			if (!self::$styles_scripts_added) {
				self::$styles_scripts_added = true;
			} 
			$amp_content = new AMP_Content($html_content,
				apply_filters( 'amp_content_embed_handlers', array(
					'AMP_Twitter_Embed_Handler' => array(),
					'AMP_YouTube_Embed_Handler' => array(),
					'AMP_Instagram_Embed_Handler' => array(),
					'AMP_Vine_Embed_Handler' => array(),
					'AMP_Facebook_Embed_Handler' => array(),
				), null ),
				apply_filters( 'amp_content_sanitizers', array(
					 'AMP_Style_Sanitizer' => array(),
					 'AMP_Blacklist_Sanitizer' => array(),
					 'AMP_Img_Sanitizer' => array(),
					 'AMP_Video_Sanitizer' => array(),
					 'AMP_Audio_Sanitizer' => array(),
					 'AMP_Iframe_Sanitizer' => array(
						 'add_placeholder' => true,
					 ),
				), null ),
				array(
					'content_max_width' => $content_width,
				)
			);
			foreach ($amp_content->get_amp_styles() as $selector => $atributes) {
				$wp_amp_theme_extra_style_before .= $selector.'{';
				foreach ($atributes as $key => $value) {
					$wp_amp_theme_extra_style_before .= $value.';';
				}
				$wp_amp_theme_extra_style_before .= '}';
			}
			foreach ($amp_content->get_amp_scripts() as $name => $src) {
				if (empty($wp_amp_defaults_scripts[$name])) {
					$wp_amp_defaults_scripts[$name] = $src;
				}
			}

			
			return $html_content;
		}
		/**
		* Static function post_scripts_styles
		* @access public
		* @since 0.2.0
		*/
		public static function post_scripts_styles($more_link_text = null, $strip_teaser = false) {
	        $content = get_the_content( $more_link_text, $strip_teaser );
	        $content = apply_filters( 'the_content', $content );
	        return true;
		}
	}
}
wp_amp::hooks();
?>