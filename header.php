<?php
/**
 * The template for displaying the header
 * @package AMP - Accelerated Mobile Pages
 * @since 0.1
 */
?>
<!DOCTYPE html>
<html amp <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta content="IE=Edge" http-equiv="X-UA-Compatible">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>

    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
</head>
<body <?php body_class(); ?>>

<amp-sidebar id="sidebar" side="right" layout="nodisplay">
  <form class="menu-layer primary" action="/" target="_top">
    <button type="reset" class="close-button" id="menu-button" on='tap:sidebar.toggle'></button>
    
    <?php if ( has_nav_menu( 'header-left' ) ) : ?>
      <div id="site-navigation" class="items" role="navigation">
        <?php
          // Primary navigation menu.
          wp_nav_menu( array(
            'menu_class'     => 'menu-item',
            'theme_location' => 'header-left',
          ) );
        ?>
      </div><!-- .main-navigation -->
    <?php endif; ?>
    
  </form>
</amp-sidebar>

<header class="header fixed">

  <div class="container">
    <div class="nav-container">
      <div class="left-nav alt">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tab header-title"><span></span></a>
        <?php if ( has_nav_menu( 'header-left' ) ) : ?>
        <div id="main-nav">
          <?php
            // Primary navigation menu.
            wp_nav_menu( array(
              //'menu_class'     => 'menu-item',
              'theme_location' => 'header-left',
              'container' => '',
            ) );
          ?>
        </div><!-- .main-navigation -->
      <?php endif; ?>
       
      </div>
      <div class="right alt">
        <?php
        
          if ( ! is_active_sidebar( 'header_rigth_amp' ) ) : ?>
          
          
              <?php get_search_form(); ?>
              

          <?php else : ?>
              <?php dynamic_sidebar( 'header_rigth_amp' ); ?>
        <?php endif; ?>
        <button class="tab hamburger" id="menu-button" on='tap:sidebar.toggle'></button>
      </div>
    </div>
  </div>
</header>

