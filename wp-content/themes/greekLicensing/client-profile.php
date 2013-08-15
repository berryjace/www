<?php
/**
 * Template Name Posts: client-profile
 * @package WordPress
 * @subpackage themename
 */
get_header();
?>

<style>
    .topbar{background:#eee; margin-top:-20px; margin-bottom:20px; padding:10px}
    .topbar span{ float:right}
    .topbar p{ font-size:18px; color:#777}
    .vendor_logo{ float:right}

    h1{ color: #662219;
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 0 0 2px #D78074;}

    h2{ color: #333;
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0;
        text-shadow: 0 0 2px #999;}
    </style>



    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.jcarousel.min.js"></script> 


    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/skins/tango/skin.css" />


    <div >

        <div class="topbar"><span><a href="<?php echo home_url('/our-clients'); ?>">Return to Clients List</a></span>
        <p>Client Profile</p></div>
    <div class="vendor_logo"><div id="h_search">
            <form action="vendor-search" method="post">
                <select name="greekorg" class="selectgorg" id="greekorg" >
                    <option value="0">Select an Organization:</option>
                    <option value="Alpha Chi Omega">Alpha Chi Omega </option>
                    <option value="Alpha Delta Pi">Alpha Delta Pi </option>
                    <option value="Alpha Epsilon Phi">Alpha Epsilon Phi </option>
                    <option value="Alpha Epsilon Pi">Alpha Epsilon Pi </option>
                    <option value="Alpha Gamma Delta">Alpha Gamma Delta </option>   
                </select>
                <br />
                <input type="submit" class="brown" value="Submit"/>
            </form>
        </div></div>
    
    <?php
    global $wp_query;
    $postid = $wp_query->post->ID;
    $postid = $wp_query->post->ID;
    $greek_symbol = get_post_meta($postid, 'greek_symbol', true);


    wp_reset_query();
    ?><div style="font-size:100px; color:#ddd; float:right; padding-right:50px; font-family:'Times New Roman', Times, serif"> <?php echo $greek_symbol; ?></div>
    <?php if (have_posts())
        while (have_posts()) : the_post(); ?>
            <h1> <?php the_title(); ?></h1>

            <?php the_content(); ?>
    <?php endwhile; // end of the loop.  ?>






</div><!-- #primary -->
<div id="col3">

</div>
<?php get_footer(); ?>