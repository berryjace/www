<?php
/**
 * Template Name: Homepage Template
 * Description: If you'd rather have a specific homepage rather than your blog posts. View readme.txt file for instructions.
 *
 * @package WordPress
 * @subpackage Terminal
 * @since Terminal 1.0
 */
include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();

get_header();
?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/slider/jquery.ad-gallery.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/slider/jquery.ad-gallery.js"></script>
<script type="text/javascript">
    $(function() {

        var galleries = $('.ad-gallery').adGallery();
        $('#switch-effect').change(
        function() {
            galleries[0].settings.effect = $(this).val();
            return false;
        }
    );
        $('#toggle-slideshow').click(
        function() {
            galleries[0].slideshow.toggle();
            return false;
        }
    );
        $('#toggle-description').click(
        function() {
            if(!galleries[0].settings.description_wrapper) {
                galleries[0].settings.description_wrapper = $('#descriptions');
            } else {
                galleries[0].settings.description_wrapper = false;
            }
            return false;
        }
    );


    });

function editButton(value){
if (value == '')
    {
        $('#jsbutton').attr('disabled', 'disabled');
    }
    else
    {
        $('#jsbutton').removeAttr('disabled');
    }
}
</script>
<style type="text/css">
    pre {
        font-family: "Lucida Console", "Courier New", Verdana;
        border: 1px solid #CCC;
        background: #f2f2f2;
        padding: 10px;
    }
    code {
        font-family: "Lucida Console", "Courier New", Verdana;
        margin: 0;
        padding: 0;
    }
    #gallery {
        padding: 15px;
        background: #eaeae5;
        margin-bottom:20px
    }
    #descriptions {
        position: relative;
        height: 50px;
        margin-top: 10px;
        width: 530px;
        padding: 10px;
        overflow: hidden;
    }
    #descriptions .ad-image-description {
        position: absolute;
        color:#CCC
    }
    #descriptions .ad-image-description .ad-description-title {
        display: block;
    }

    #gallery .ad-forward, #gallery .ad-back{ display:none}
</style>
<div id="homepage">
    <div id="col1">
        <div id="h_search">
            <form action="vendor-search" method="post">
                <select name="greekorg" class="selectgorg" id="greekorg" onChange='editButton(this.value);'>
                    <option value="" disabled="disabled" selected="selected">Select an Organization:</option>
                    <?php
                    $myclients = $wpdb_amcdb->get_results('SELECT * FROM  `users` WHERE account_type =3 AND organization_name <>  ""  and user_status="Current" ORDER BY  `organization_name` ASC ');

                    foreach ($myclients as $client) {
                        echo ' <option value="' . $client->organization_name . '">' . $client->organization_name . '</option>';
                    }
                    ?>



                </select>
                <br />
                <input type="submit" class="brown" value="Submit" id="jsbutton" disabled="disabled"/>
            </form>
        </div>
        <div>
            <div><a href="http://www.greekquote.com/"><img src="<?php bloginfo('template_url'); ?>/images/clientlist.jpg" border="0" /></a></div>
            <br style="height:20px" />
            <h3>Nominate a Vendor</h3>
            <form action="nominate-vendor" method="post">
                <input name="vendor-name" type="text"  class="tinput" id="vendor-name" value="vendor name" onclick="value=''"/>
                <br />
                <input type="submit" class="white" value="Submit"/>
            </form>
        </div>
        <br style="height:20px" />
        <div>
            <h3>Become an Official Shopper</h3>
            <div style="margin-top:-9px; margin-bottom:10px">Get Free Stuff!</div>
            <form action="official-shopper" method="post">
                <input name="os_email" type="text" class="tinput" id="os_email" value="email address" onclick="value=''"/>
                <br />
                <input name="submit" type="submit" class="white" value="Submit"/>
            </form>
        </div>
        <br style="height:20px" />
        <div>
            <h3>Proud Associates</h3>
            <img src="<?php bloginfo('template_url'); ?>/images/greeklicensing_associates.jpg" /> </div>
    </div>
    <div id="col2">
        <div id="gallery" class="ad-gallery">
            <div class="ad-image-wrapper"> </div>
            <div class="ad-controls"> </div>
            <div class="ad-nav">
                <div class="ad-thumbs">
                    <ul class="ad-thumb-list">
                        <li> <a href="<?php bloginfo('template_url'); ?>/slider/images/1.jpg" target="_self"> <img src="<?php bloginfo('template_url'); ?>/slider/images/thumbs/t1.jpg" class="image0" alt="Become an official Vendor: <br/>Expand your business into the Greek market and sell quality products and services to more than 8 million collective undergraduate and alumni members." longdesc ="become-a-licensed-vendor"> </a> </li>
                        <li> <a href="<?php bloginfo('template_url'); ?>/slider/images/2.jpg"> <img src="<?php bloginfo('template_url'); ?>/slider/images/thumbs/t2.jpg" alt="Find Licensed Products:  <br/>Locate the highest quality Greek products from trusted vendors in your area." class="image1" longdesc="find-licensed-products"> </a> </li>
                        <li> <a href="<?php bloginfo('template_url'); ?>/slider/images/3.jpg"> <img src="<?php bloginfo('template_url'); ?>/slider/images/thumbs/t3.jpg"  alt="What Is Licensing?
                                                                                                       <br/>Discover why licensing is beneficial for consumers, necessary for vendors, and essential for trademark owners" class="image2" longdesc="about-us"> </a> </li>
                    </ul>
                </div>
            </div>
        </div>
        <h1>Licensing Quality Products and Services for North America's Fraternities, Sororities, and Honorary Societies</h1>
        <img src="<?php bloginfo('template_url'); ?>/images/greeklicensing_imgs.jpg"  /><br />
        <h4>The site is designed for</h4>
        <ul id="sitedesignedfor">
            <li> Manufacturers looking to produce Greek merchandise</li>
            <li> Greek stores seeking new and innovative products to buy</li>
            <li> Students, parents, and alumni searching for products or information about licensing </li>
        </ul>
    </div>
    <div id="col3">
        <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar(1)) : else : ?>
<?php endif; ?>
    </div>
    <!-- #homepage -->

    <br  style="clear:both"/>
    <?php get_footer(); ?>
