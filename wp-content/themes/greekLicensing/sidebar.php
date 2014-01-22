<?php
/**
 * @package WordPress
 * @subpackage themename
 */
include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();
global $client;
?>

<script type="text/javascript">
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

<div id="secondary" class="widget-area">
    <div id="h_search">
        <form action="<?php echo home_url('/'); ?>vendor-search" method="post">
            <select name="greekorg" class="selectgorg" id="greekorg" onChange='editButton(this.value);'>
                <option value="" disabled="disabled" selected="selected">Select an Organization:</option>
                <?php
                $myclients = $wpdb_amcdb->get_results('SELECT * FROM  `users` WHERE account_type =3 AND organization_name <>  ""  and user_status="Current"  ORDER BY  `organization_name` ASC ');

                foreach ($myclients as $client) {
                    echo ' <option value="' . $client->organization_name . '">' . $client->organization_name . '</option>';
                }
                ?>



            </select>
            <br />
            <input type="submit" class="brown" value="Submit" id="jsbutton" disabled="disabled"/>
        </form>
    </div>
    <?php if (!dynamic_sidebar('sidebar')) : ?>

        <aside id="search" class="widget widget_search" role="complementary">
            <?php get_search_form(); ?>
        </aside>

        <aside id="archives" class="widget" role="complementary">
            <h2 class="widget-title"><?php _e('Archives', 'themename'); ?></h2>
            <ul>
                <?php wp_get_archives(array('type' => 'monthly')); ?>
            </ul>
        </aside>

        <aside id="meta" class="widget" role="complementary">
            <h2 class="widget-title"><?php _e('Meta', 'themename'); ?></h2>
            <ul>
                <?php wp_register(); ?>
                <aside role="complementary"><?php wp_loginout(); ?></aside>
                <?php wp_meta(); ?>
            </ul>
        </aside>

    <?php endif; // end sidebar widget area ?>
</div><!-- #secondary .widget-area -->

