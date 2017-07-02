<?php
/**
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */

if( !class_exists( 'wp_amp_compatibilities' ) ) {
	class wp_amp_compatibilities {
		public static $get_style = false;
		public static $style_files = array();
		/**
		* Static function hooks
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function hooks() {
			if (!is_user_logged_in()) {
				add_action('wp_enqueue_scripts', array(__CLASS__, 'amp_scripts'), 9999);
				add_action('wp_enqueue_scripts', array(__CLASS__, 'amp_styles'), 9999);
			}
			add_filter('script_loader_tag', array(__CLASS__, 'add_script_attribute'), 10, 2);
			// Compatibility with WP Comments
			add_action('admin_post_wp_amp_comment', array(__CLASS__, 'wp_amp_comment'));
			add_action('admin_post_nopriv_wp_amp_comment', array(__CLASS__, 'wp_amp_comment'));

			if (!is_user_logged_in()) {
				//Others actions
				remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
				remove_action( 'wp_print_styles', 'print_emoji_styles' ); 
			}

			add_filter('wpematico_cache_response_html', array(__CLASS__, 'amp_cache'), 89, 2);
			add_filter('get_search_form', array(__CLASS__, 'get_search_form'), 89, 1);
			add_filter('post_thumbnail_html', array(__CLASS__, 'post_thumbnail_html'), 89, 1);
			
		}
		public static function amp_scripts() {
			global $wp_scripts, $wp_amp_defaults_scripts;
		    $array = array();
		    // Runs through the queue scripts
		    foreach ($wp_scripts->queue as $handle) :
		    	if (!empty($wp_amp_defaults_scripts[$handle])) {
		    		continue;
		    	}
		        $array[] = $handle;
		    endforeach;
		    wp_dequeue_script($array);

		    $array = array();
		    foreach ($wp_scripts->registered as $idenfier => $handle) :
		    	if (!empty($wp_amp_defaults_scripts[$idenfier])) {
		    		continue;
		    	}
		        $array[] = $idenfier;
		    endforeach;
		    self::deregister_script($array);
		    wp_dequeue_script($array);
		   
		}
		/**
		* Static function add_script_attribute
		* @access public
		* @return void
		* @since 0.2.1
		*/
		public static function add_script_attribute($tag, $handle) {
			global $wp_amp_defaults_scripts;
			if (!empty($wp_amp_defaults_scripts[$handle])) {
				$tag = str_replace(" type='text/javascript'", '', $tag );
				$tag = str_replace( ' src', ' async src', $tag );
				if ($handle == 'amp-main') {
					return $tag;
				}
				if ($handle == 'amp-mustache') {
					$tag = str_replace( ' src', " custom-template='".$handle."' src", $tag );
				} else {
					$tag = str_replace( ' src', " custom-element='".$handle."' src", $tag );
				}

			}

		    return $tag;
		}
		

		public static function amp_styles() {
			global $wp_styles;
			
		    foreach( $wp_styles->queue as $style ) {
		      self::$style_files[] =  $wp_styles->registered[$style]->src;
		    }
			
		    foreach ($wp_styles->registered as $handle => $data){

		    	if ('amp_theme_google_fonts' == $handle) {
		    		continue;
		    	}
		    	wp_deregister_style($handle);
				wp_dequeue_style($handle);
			}
		}
		/**
		* Static function get_style_theme
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function get_style_theme() {
			global $wp_filesystem, $wp_amp_theme_extra_style_before, $wp_amp_theme_extra_style_after;
			
			if (!self::$get_style) {
				self::$get_style = true;
			} else {
				return '';
			}
			$content_style = '';
			/* Append content of all styles on custom tag.
			foreach (self::$style_files as $style_src) {
				$content_style .= $wp_filesystem->get_contents($style_src);
			}
			*/
			$default_logo = get_template_directory_uri().'/assets/images/logo-blue.svg';
			$custom_logo_id = get_theme_mod('custom_logo');
			if (!empty($custom_logo_id)) {
				$image = wp_get_attachment_image_src( $custom_logo_id , 'full');
				if (!empty($image)) { 
					$default_logo = $image[0];
				}
			}
			$hamburger_img = get_template_directory_uri().'/assets/images/hamburger.svg';
			$search_img = get_template_directory_uri().'/assets/images/search.svg';
			$close_img = get_template_directory_uri().'/assets/images/close.svg';
			

			$style_path = get_template_directory(). '/style.css';

			$content_style .= $wp_filesystem->get_contents($style_path);
			$content_style = $wp_amp_theme_extra_style_before.$content_style;
			$content_style = $content_style.$wp_amp_theme_extra_style_after;
			$content_style = str_replace('{$custom_logo}', $default_logo, $content_style);
			$content_style = str_replace('{$hamburger_img}', $hamburger_img, $content_style);
			$content_style = str_replace('{$search_img}', $search_img, $content_style);
			$content_style = str_replace('{$close_img}', $close_img, $content_style);
			$content_style = amp_converter::minify_css($content_style);
			echo '<style amp-custom>'.$content_style.'</style>';
		}
		

		public static function amp_cache($response, $url_to_cache) {
			remove_filter('wpematico_cache_response_html', array('wpematico_cache_process', 'cache_response_html'), 100, 2);
			remove_filter('wpematico_cache_response_html','amp_cache', 99, 2);
			return $response;
		}
		public static function comment_form( $args = array(), $post_id = null ) {
			if ( null === $post_id )
				$post_id = get_the_ID();

			// Exit the function when comments for the post are closed.
			if ( ! comments_open( $post_id ) ) {
				/**
				 * Fires after the comment form if comments are closed.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_comments_closed' );

				return;
			}

			$commenter = wp_get_current_commenter();
			$user = wp_get_current_user();
			$user_identity = $user->exists() ? $user->display_name : '';

			$args = wp_parse_args( $args );
			if ( ! isset( $args['format'] ) )
				$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';

			$req      = get_option( 'require_name_email' );
			$aria_req = ( $req ? " aria-required='true'" : '' );
			$html_req = ( $req ? " required='required'" : '' );
			$html5    = 'html5' === $args['format'];
			$fields   =  array(
				'author' => '<p class="comment-form-author">' . '<label for="author">' . esc_attr__( 'Name', 'amp-accelerated-mobile-pages' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
				            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' /></p>',
				'email'  => '<p class="comment-form-email"><label for="email">' . esc_attr__( 'Email', 'amp-accelerated-mobile-pages' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
				            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>',
				'url'    => '<p class="comment-form-url"><label for="url">' . esc_attr__( 'Website', 'amp-accelerated-mobile-pages' ) . '</label> ' .
				            '<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" /></p>',
			);

			$required_text = sprintf( ' ' .
			/* translators: %s: Required input asteric  */
			 esc_attr__('Required fields are marked %s', 'amp-accelerated-mobile-pages'), '<span class="required">*</span>' );

			/**
			 * Filters the default comment form fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $fields The default comment fields.
			 */
			$fields = apply_filters( 'comment_form_default_fields', $fields );
			$defaults = array(
				'fields'               => $fields,
				'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun', 'amp-accelerated-mobile-pages' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea></p>',
				/** This filter is documented in wp-includes/link-template.php */
				'must_log_in'          => '<p class="must-log-in">' . sprintf(
				                              /* translators: %s: login URL */
				                              __( 'You must be <a href="%s">logged in</a> to post a comment.', 'amp-accelerated-mobile-pages' ),
				                              wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) )
				                          ) . '</p>',
				/** This filter is documented in wp-includes/link-template.php */
				'logged_in_as'         => '<p class="logged-in-as">' . sprintf(
				                              /* translators: 1: edit user link, 2: accessibility text, 3: user name, 4: logout URL */
				                              __( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>', 'amp-accelerated-mobile-pages' ),
				                              get_edit_user_link(),
				                              /* translators: %s: user name */
				                              esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.', 'amp-accelerated-mobile-pages' ), $user_identity ) ),
				                              $user_identity,
				                              wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) )
				                          ) . '</p>',
				'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.', 'amp-accelerated-mobile-pages' ) . '</span>'. ( $req ? $required_text : '' ) . '</p>',
				'comment_notes_after'  => '',
				'action'               => admin_url('admin-post.php'),
				'id_form'              => 'commentform',
				'id_submit'            => 'submit',
				'class_form'           => 'comment-form',
				'class_submit'         => 'submit',
				'name_submit'          => 'submit',
				'title_reply'          => __( 'Leave a Reply', 'amp-accelerated-mobile-pages' ),
				 /* translators: %s: Author name */
				'title_reply_to'       => __( 'Leave a Reply to %s', 'amp-accelerated-mobile-pages' ),
				'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
				'title_reply_after'    => '</h3>',
				'cancel_reply_before'  => ' <small>',
				'cancel_reply_after'   => '</small>',
				'cancel_reply_link'    => __( 'Cancel reply', 'amp-accelerated-mobile-pages' ),
				'label_submit'         => __( 'Post Comment', 'amp-accelerated-mobile-pages' ),
				'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
				'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
				'format'               => 'xhtml',
			);

			/**
			 * Filters the comment form default arguments.
			 *
			 * Use {@see 'comment_form_default_fields'} to filter the comment fields.
			 *
			 * @since 3.0.0
			 *
			 * @param array $defaults The default comment form arguments.
			 */
			$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

			// Ensure that the filtered args contain all required default values.
			$args = array_merge( $defaults, $args );

			/**
			 * Fires before the comment form.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_before' );
			?>
			<div id="respond" class="comment-respond">
				<?php
				echo $args['title_reply_before'];

				comment_form_title( $args['title_reply'], $args['title_reply_to'] );

				//echo $args['cancel_reply_before'];

				//cancel_comment_reply_link( $args['cancel_reply_link'] );

				//echo $args['cancel_reply_after'];

				echo $args['title_reply_after'];

				if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) :
					echo $args['must_log_in'];
					/**
					 * Fires after the HTML-formatted 'must log in after' message in the comment form.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_must_log_in_after' );
				else : ?>
					<form action-xhr="<?php echo esc_url( $args['action'] ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>" class="<?php echo esc_attr( $args['class_form'] ); ?>"<?php echo $html5 ? ' novalidate' : ''; ?> target="_top">
						<input type="hidden" name="action" value="wp_amp_comment"/>

						<?php
						/**
						 * Fires at the top of the comment form, inside the form tag.
						 *
						 * @since 3.0.0
						 */
						do_action( 'comment_form_top' );

						if ( is_user_logged_in() ) :
							/**
							 * Filters the 'logged in' message for the comment form for display.
							 *
							 * @since 3.0.0
							 *
							 * @param string $args_logged_in The logged-in-as HTML-formatted message.
							 * @param array  $commenter      An array containing the comment author's
							 *                               username, email, and URL.
							 * @param string $user_identity  If the commenter is a registered user,
							 *                               the display name, blank otherwise.
							 */
							echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );

							/**
							 * Fires after the is_user_logged_in() check in the comment form.
							 *
							 * @since 3.0.0
							 *
							 * @param array  $commenter     An array containing the comment author's
							 *                              username, email, and URL.
							 * @param string $user_identity If the commenter is a registered user,
							 *                              the display name, blank otherwise.
							 */
							do_action( 'comment_form_logged_in_after', $commenter, $user_identity );

						else :

							echo $args['comment_notes_before'];

						endif;

						// Prepare an array of all fields, including the textarea
						$comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];

						/**
						 * Filters the comment form fields, including the textarea.
						 *
						 * @since 4.4.0
						 *
						 * @param array $comment_fields The comment fields.
						 */
						$comment_fields = apply_filters( 'comment_form_fields', $comment_fields );

						// Get an array of field names, excluding the textarea
						$comment_field_keys = array_diff( array_keys( $comment_fields ), array( 'comment' ) );

						// Get the first and the last field name, excluding the textarea
						$first_field = reset( $comment_field_keys );
						$last_field  = end( $comment_field_keys );

						foreach ( $comment_fields as $name => $field ) {

							if ( 'comment' === $name ) {

								/**
								 * Filters the content of the comment textarea field for display.
								 *
								 * @since 3.0.0
								 *
								 * @param string $args_comment_field The content of the comment textarea field.
								 */
								echo apply_filters( 'comment_form_field_comment', $field );

								echo $args['comment_notes_after'];

							} elseif ( ! is_user_logged_in() ) {

								if ( $first_field === $name ) {
									/**
									 * Fires before the comment fields in the comment form, excluding the textarea.
									 *
									 * @since 3.0.0
									 */
									do_action( 'comment_form_before_fields' );
								}

								/**
								 * Filters a comment form field for display.
								 *
								 * The dynamic portion of the filter hook, `$name`, refers to the name
								 * of the comment form field. Such as 'author', 'email', or 'url'.
								 *
								 * @since 3.0.0
								 *
								 * @param string $field The HTML-formatted output of the comment form field.
								 */
								echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";

								if ( $last_field === $name ) {
									/**
									 * Fires after the comment fields in the comment form, excluding the textarea.
									 *
									 * @since 3.0.0
									 */
									do_action( 'comment_form_after_fields' );
								}
							}
						}

						$submit_button = sprintf(
							$args['submit_button'],
							esc_attr( $args['name_submit'] ),
							esc_attr( $args['id_submit'] ),
							esc_attr( $args['class_submit'] ),
							esc_attr( $args['label_submit'] )
						);

						/**
						 * Filters the submit button for the comment form to display.
						 *
						 * @since 4.2.0
						 *
						 * @param string $submit_button HTML markup for the submit button.
						 * @param array  $args          Arguments passed to `comment_form()`.
						 */
						$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

						$submit_field = sprintf(
							$args['submit_field'],
							$submit_button,
							get_comment_id_fields( $post_id )
						);

						/**
						 * Filters the submit field for the comment form to display.
						 *
						 * The submit field includes the submit button, hidden fields for the
						 * comment form, and any wrapper markup.
						 *
						 * @since 4.2.0
						 *
						 * @param string $submit_field HTML markup for the submit field.
						 * @param array  $args         Arguments passed to comment_form().
						 */
						echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

						/**
						 * Fires at the bottom of the comment form, inside the closing </form> tag.
						 *
						 * @since 1.5.0
						 *
						 * @param int $post_id The post ID.
						 */
						do_action( 'comment_form', $post_id );
						?>
						<div submit-success>
						    <template type="amp-mustache">
						      <?php esc_attr_e('Success! Your comment has been added.', 'amp-accelerated-mobile-pages' );?>
						    </template>
						</div>
						<div submit-error>
					    	<template type="amp-mustache">
					      		{{message}} 
					    	</template>
				 		</div>
					</form>
					
				<?php endif; ?>
			</div><!-- #respond -->
			<?php

			/**
			 * Fires after the comment form.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_after' );
		}
		/**
		* Static function wp_amp_comment
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function wp_amp_comment() {
			header('AMP-Access-Control-Allow-Source-Origin:'.site_url());
			header('Access-Control-Expose-Headers: AMP-Redirect-To');
			$comment = wp_handle_comment_submission( wp_unslash( $_POST ) );
			if ( is_wp_error( $comment ) ) {
				$data = intval( $comment->get_error_data() );
				if ( ! empty( $data ) ) {
					status_header(500);
					$json_response = array();
					$json_response['message'] = $comment->get_error_message();
					die(json_encode($json_response));
				} else {
					exit;
				}
			}

			$user = wp_get_current_user();
			do_action( 'set_comment_cookies', $comment, $user );
			$location = empty( $_POST['redirect_to'] ) ? get_comment_link( $comment ) : $_POST['redirect_to'] . '#comment-' . $comment->comment_ID;
			$location = apply_filters( 'comment_post_redirect', $location, $comment );
			header('AMP-Redirect-To:'.$location);
			die('{"sucess": "true"}');

		}
		/**
		* Static function comment_list
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function comment_list($comment, $args, $depth) {

		    if ( 'div' === $args['style'] ) {
		        $tag       = 'div';
		        $add_below = 'comment';
		    } else {
		        $tag       = 'li';
		        $add_below = 'div-comment';
		    }
		    ?>
		    <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
		    <?php 
		    if ( 'div' != $args['style'] ) { 
		    ?>
		        <article id="div-comment-<?php comment_ID() ?>" class="comment-body">
		    <?php 
		    } 
		    ?>
		    <footer class="comment-meta">
		    <div class="comment-author vcard">
		        <?php 
		        if ( $args['avatar_size'] != 0 ) {
		            echo str_replace('img', 'amp-img', get_avatar( $comment, $args['avatar_size'] )).'</amp-img>'; 
		        } 
		        ?>
		        <?php printf( 
		        /* translators: %s: Author name */
		        __( '<b class="fn">%s</b> <span class="says">says:</span>', 'amp-accelerated-mobile-pages' ), get_comment_author_link() ); ?>
		    </div>

		    <?php if ( $comment->comment_approved == '0' ) : ?>
		         <em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'amp-accelerated-mobile-pages' ); ?></em>
		          <br />
		    <?php endif; ?>

		    <div class="comment-metadata"><a rel="nofollow" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
		        <?php
		        /* translators: 1: date, 2: time */
		        printf( esc_attr__('%1$s at %2$s', 'amp-accelerated-mobile-pages'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'amp-accelerated-mobile-pages' ), '  ', '' );
		        ?>
		    </div>
		    </footer>
		    <?php comment_text(); ?>

		    <div class="reply">
		        <?php self::get_comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		    </div>
		    <?php 
		    if ( 'div' != $args['style'] ) {
		    ?>
		    </article>
		    <?php 
		    }
		}
		/**
		* Static function get_comment_reply_link
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function get_comment_reply_link( $args = array(), $comment = null, $post = null ) {
			$defaults = array(
				'add_below'     => 'comment',
				'respond_id'    => 'respond',
				'reply_text'    => esc_attr__( 'Reply', 'amp-accelerated-mobile-pages' ),
				/* translators: Comment reply button text. 1: Comment author name */
				'reply_to_text' => esc_attr__( 'Reply to %s', 'amp-accelerated-mobile-pages' ),
				'login_text'    => esc_attr__( 'Log in to Reply', 'amp-accelerated-mobile-pages' ),
				'max_depth'     => 0,
				'depth'         => 0,
				'before'        => '',
				'after'         => ''
			);

			$args = wp_parse_args( $args, $defaults );

			if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] ) {
				return;
			}

			$comment = get_comment( $comment );

			if ( empty( $post ) ) {
				$post = $comment->comment_post_ID;
			}

			$post = get_post( $post );

			if ( ! comments_open( $post->ID ) ) {
				return false;
			}

			/**
			 * Filters the comment reply link arguments.
			 *
			 * @since 4.1.0
			 *
			 * @param array      $args    Comment reply link arguments. See get_comment_reply_link()
			 *                            for more information on accepted arguments.
			 * @param WP_Comment $comment The object of the comment being replied to.
			 * @param WP_Post    $post    The WP_Post object.
			 */
			$args = apply_filters( 'comment_reply_link_args', $args, $comment, $post );

			if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
				$link = sprintf( '<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
					esc_url( wp_login_url( get_permalink() ) ),
					$args['login_text']
				);
			} else {
				

				$link = sprintf( "<a rel='nofollow' class='comment-reply-link' href='%s'  aria-label='%s'>%s</a>",
					esc_url( add_query_arg( 'replytocom', $comment->comment_ID, get_permalink( $post->ID ) ) ) . "#" . $args['respond_id'],
					esc_attr( sprintf( $args['reply_to_text'], $comment->comment_author ) ),
					$args['reply_text']
				);
			}

			/**
			 * Filters the comment reply link.
			 *
			 * @since 2.7.0
			 *
			 * @param string  $link    The HTML markup for the comment reply link.
			 * @param array   $args    An array of arguments overriding the defaults.
			 * @param object  $comment The object of the comment being replied.
			 * @param WP_Post $post    The WP_Post object.
			 */
			return apply_filters( 'comment_reply_link', $args['before'] . $link . $args['after'], $args, $comment, $post );
		}
		/**
		* Static function deregister_script.1
		* @access public
		* @return void
		* @since 0.1.1
		*/
		public static function deregister_script( $handle ) {
			_wp_scripts_maybe_doing_it_wrong( __FUNCTION__ );
			$current_filter = current_filter();
			if ( 'wp-login.php' === $GLOBALS['pagenow'] && 'login_enqueue_scripts' !== $current_filter ) {
				$no = array(
					'jquery', 'jquery-core', 'jquery-migrate', 'jquery-ui-core', 'jquery-ui-accordion',
					'jquery-ui-autocomplete', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-ui-dialog',
					'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-menu', 'jquery-ui-mouse',
					'jquery-ui-position', 'jquery-ui-progressbar', 'jquery-ui-resizable', 'jquery-ui-selectable',
					'jquery-ui-slider', 'jquery-ui-sortable', 'jquery-ui-spinner', 'jquery-ui-tabs',
					'jquery-ui-tooltip', 'jquery-ui-widget', 'underscore', 'backbone',
				);

				if ( in_array( $handle, $no ) ) {
					_doing_it_wrong( __FUNCTION__, $message, '3.6.0' );
					return;
				}
			}

			wp_scripts()->remove( $handle );
		}
		/**
		* Static function get_search_form
		* @access public
		* @return void
		* @since 0.1
		*/
		public static function get_search_form($form) {
			$form = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" class="searchbar" target="_top" novalidate="">
			  <input class="query user-valid valid" name="s" type="search" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder', 'amp-accelerated-mobile-pages' ) . '"/>
			  <button name="'. esc_attr_x( 'Search', 'submit button', 'amp-accelerated-mobile-pages' ) .'" type="submit"></button>
			</form>';
			return $form;
		}
		/**
		* Static function post_thumbnail_html
		* @access public
		* @return void
		* @since 0.1.1
		*/
		public static function post_thumbnail_html($html) {

			$html = wp_amp::get_amp_content($html);
			return $html;
		}

	}
}
wp_amp_compatibilities::hooks();
?>