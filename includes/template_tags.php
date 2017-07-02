<?php
/**
* @package AMP - Accelerated Mobile Pages
* @since 0.1
*/

if ( ! function_exists( 'wp_amp_the_posts_pagination' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 * @since 0.1
 */
function wp_amp_the_posts_pagination() {
    $nav = get_the_posts_pagination(array( 'mid_size' => 2));
    $nav = strip_tags_content($nav, '<h2>', TRUE);
    $nav = str_replace("'page-numbers'", '"button"', $nav);
    $nav = str_replace('"next page-numbers"', '"next button"', $nav);
    $nav = str_replace('"prev page-numbers"', '"prev button"', $nav);
    echo $nav;
}
endif;


if ( ! function_exists( 'wp_amp_theme_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the categories, tags.
 *
 * Create your own wp_amp_theme_entry_meta() function to override in a child theme.
 *
 * @since 0.2.0
 */
function wp_amp_theme_entry_meta() {
  if ( 'post' === get_post_type() ) {
    $author_avatar_size = apply_filters( 'wp_amp_theme_author_avatar_size', 49 );
    printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
      str_replace('img', 'amp-img', get_avatar( get_the_author_meta( 'user_email' ), $author_avatar_size )).'</amp-img>' ,
      esc_attr_x( 'Author', 'Used before post author name.', 'amp-accelerated-mobile-pages' ),
      esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
      get_the_author()
    );
  }

  if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
    wp_amp_theme_entry_date();
  }

  $format = get_post_format();
  if ( current_theme_supports( 'post-formats', $format ) ) {
    printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
      sprintf( '<span class="screen-reader-text">%s </span>', esc_attr_x( 'Format', 'Used before post format.', 'amp-accelerated-mobile-pages' ) ),
      esc_url( get_post_format_link( $format ) ),
      esc_attr(get_post_format_string( $format ))
    );
  }

  if ( 'post' === get_post_type() ) {
    wp_amp_theme_entry_taxonomies();
  }

  if ( ! is_singular() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
    echo '<span class="comments-link">';
    comments_popup_link( 
        sprintf( 
             /* translators: %s: post title */
            __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'amp-accelerated-mobile-pages' ), get_the_title() ) );
    echo '</span>';
  }
}
endif;

if ( ! function_exists( 'wp_amp_theme_entry_date' ) ) :
/**
 * Prints HTML with date information for current post.
 *
 * Create your own wp_amp_theme_entry_date() function to override in a child theme.
 *
 * @since 0.2.1
 */
function wp_amp_theme_entry_date() {
  $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

  if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
  }

  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( 'c' ) ),
    get_the_date(),
    esc_attr( get_the_modified_date( 'c' ) ),
    get_the_modified_date()
  );

  printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
    esc_attr_x( 'Posted on', 'Used before publish date.', 'amp-accelerated-mobile-pages' ),
    esc_url( get_permalink() ),
    $time_string
  );
}
endif;

if ( ! function_exists( 'wp_amp_theme_entry_taxonomies' ) ) :
/**
 * Prints HTML with category and tags for current post.
 *
 * Create your own wp_amp_theme_entry_taxonomies() function to override in a child theme.
 *
 * @since 0.2.1
 */
function wp_amp_theme_entry_taxonomies() {
  $categories_list = get_the_category_list( esc_attr_x( ', ', 'Used between list items, there is a space after the comma.', 'amp-accelerated-mobile-pages' ) );
  if ( $categories_list && wp_amp_theme_categorized_blog() ) {
    printf( '<span class="cat-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
      esc_attr_x( 'Categories', 'Used before category names.', 'amp-accelerated-mobile-pages' ),
      $categories_list
    );
  }

  $tags_list = get_the_tag_list( '', esc_attr_x( ', ', 'Used between list items, there is a space after the comma.', 'amp-accelerated-mobile-pages' ) );
  if ( $tags_list ) {
    printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
      esc_attr_x( 'Tags', 'Used before tag names.', 'amp-accelerated-mobile-pages' ),
      $tags_list
    );
  }
}
endif;

/**
 * Determine whether blog/site has more than one category.
 *
 *  @since 0.1
 *
 * @return bool True of there is more than one category, false otherwise.
 */
function wp_amp_theme_categorized_blog() {
  if ( false === ( $all_the_cool_cats = get_transient( 'wp_amp_theme_categories' ) ) ) {
    // Create an array of all the categories that are attached to posts.
    $all_the_cool_cats = get_categories( array(
      'fields'     => 'ids',
      'hide_empty' => 1,

      // We only need to know if there is more than one category.
      'number'     => 2,
    ) );

    // Count the number of categories that are attached to the posts.
    $all_the_cool_cats = count( $all_the_cool_cats );

    set_transient( 'wp_amp_theme_categories', $all_the_cool_cats );
  }

  if ( $all_the_cool_cats > 1 ) {
    return true;
  } else {
    return false;
  }
}


if ( ! function_exists( 'wp_amp_post_thumbnail' ) ) :
/**
 * @since 0.1.1
 */
function wp_amp_post_thumbnail() {
  if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
    return;
  }

  if ( is_singular() ) :
  ?>

  <div class="post-thumbnail">
    <?php the_post_thumbnail(); ?>
  </div><!-- .post-thumbnail -->

  <?php else : ?>

  <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
    <?php
      the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) );
    ?>
  </a>

  <?php endif; // End is_singular()
}
endif;
?>