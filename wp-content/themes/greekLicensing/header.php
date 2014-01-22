<?php
/**
 * @package WordPress
 * @subpackage themename
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<!--[if !IE 7]>
	<style type="text/css">
		#page {display:table;height:98%}
	</style>
<![endif]-->



<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="chrome=1">

<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'themename' ), max( $paged, $page ) );

	?></title>
	<meta name="description" content="">
	<meta name="author" content="">
	<!--  Mobile Viewport Fix -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Place favicon.ico and apple-touch-icon.png in the images folder -->
	<link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/favicon.ico" />
	<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/images/apple-touch-icon.png"><!--60X60-->

	<link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); echo '?' . filemtime( get_stylesheet_directory() . '/style.css'); ?>" type="text/css" media="screen, projection" />

	<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

	<?php wp_head(); ?>
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/customSelect.jquery.js"></script>


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>



	</head>

	<body <?php body_class(); ?>>
	<div id="page" class="hfeed">
		<header id="branding" role="banner">
			<!--	<hgroup>
					<h1 id="site-title"><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
					<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
				</hgroup>-->



				<nav >
					<div id="menu-main2">
<?php /*?><?php wp_nav_menu( array( 'container_class' => 'menu-main', 'theme_location' => 'primary' ) ); ?>
<?php */?>

<ul>
<li class="<?php if (is_page('home')) { echo "current_page_item"; }?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="home">Home</a></li>
<li class="<?php if (is_page('our-clients')) { echo "current_page_item"; }?>"><a href="our-clients" class="ourclients">About Us</a></li>
<li class="<?php if (is_page('about-us')) { echo "current_page_item"; }?>"><a href="about-us" class="about">About Us</a></li>
<li class="<?php if (is_page('find-licensed-products')) { echo "current_page_item"; }?>"><a href="find-licensed-products" class="find">Find Licensed Products</a></li>
<li class="<?php if (is_page('become-a-licensed-vendor')) { echo "current_page_item"; }?>"><a href="become-a-licensed-vendor" class="become">Become a Licensed Vendor  </a></li>
<li class="<?php if (is_page('vendor-login')) { echo "current_page_item"; }?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>crm/login?type=vendor" class="vendor">Vendor  Login</a></li>
<li class="<?php if (is_page('client-login')) { echo "current_page_item"; }?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>crm/login?type=client" class="client">Client Login</a></li>
<li class="<?php if (is_page('contact')) { echo "current_page_item"; }?>"><a href="contact" class="contact">Contact Us</a></li>
</ul>
</div>
				</nav><!-- #access -->
		</header><!-- #branding -->


		<div id="main">
