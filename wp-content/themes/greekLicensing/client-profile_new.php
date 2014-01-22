<?php
/**
 * Template Name: clients Profile
 * @package WordPress
 * @subpackage themename
 */
include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();
get_header();

$client_id = $_GET['id'];
if (empty($client_id)) {
    die();
}
?>

<style>
    .topbar{background:#eee; margin-top:-20px; margin-bottom:20px; padding:10px}
    .topbar span{ float:right}
    .topbar p{ font-size:18px; color:#777}
    .vendor_logo{ float:right}
    .lebel{ font-size:11px ; font-weight:bold; color:#555}
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
    .org_prof td{ padding:10px 5px; border-bottom:1px dotted #efefef}
</style>



<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.jcarousel.min.js"></script>
<script type='text/javascript'>
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

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/skins/tango/skin.css" />


<div >

    <div class="topbar"><span><a href="<?php echo home_url('/our-clients'); ?>">Return to Clients List</a></span>
        <p>Client Profile</p></div>

    <div class="vendor_logo"><div id="h_search">
            <form action="<?php echo home_url('/vendor-search'); ?>" method="post">
                <select name="greekorg" class="selectgorg" id="greekorg" onChange='editButton(this.value);'>
                    <option value="" disabled="disabled" selected="selected">Select an Organization:</option>
                    <?php
                    $myclients = $wpdb_amcdb->get_results('SELECT * FROM  `users` WHERE account_type =3 AND organization_name <>  "" ORDER BY  `organization_name` ASC ');

                    foreach ($myclients as $client) {
                        echo ' <option '.($client_id==$client->id ? " selected=\"selected\"" : "").'value="' . $client->organization_name . '">' . $client->organization_name . '</option>';
                    }
                    ?>
                </select>
                <br />
                <input type="submit" class="brown" value="Submit" />
            </form>
        </div></div>



    <?php
    $myclients = $wpdb_amcdb->get_results("SELECT * FROM client_profiles as c , users as u WHERE c.user_id=u.id and c.user_id = " . $client_id);
    foreach ($myclients as $client) {
        ?>


        <div style="font-size:100px; color:#ddd; float:right; padding-right:50px; font-family:'Times New Roman', Times, serif"> <?php echo $client->greek_name; ?></div>

        <h1> <?php echo $client->organization_name; ?></h1>




        <table class="org_prof" width="550" border="0" cellspacing="5" cellpadding="0">
            <tbody>
                <tr>
                    <td class="lebel" width="222">Founded</td>
                    <td width="20">:</td>
                    <td width="338"><span><?php echo $client->org_founding_year; ?> </span></td>
                </tr>
                <tr>
                    <td class="lebel">Year</td><td>:</td><td><span><?php echo date("Y", strtotime($client->greek_founding_year)); ?></span></td>
                </tr>
                <tr>
                    <td class="lebel">Number of collegiate chapters</td><td>:</td><td><?php echo number_format($client->greek_total_ug_chapters); ?></td>
                </tr>
                <tr>
                    <td class="lebel">Number of alumni/ae chapters</td><td>:</td><td><?php echo number_format($client->greek_number_of_alumni_chapters); ?></td>
                </tr>
                <tr>
                    <td class="lebel">Total number of undergraduates</td><td>:</td><td><span><?php echo number_format($client->greek_number_of_undergrads); ?></span></td>
                </tr>
                <tr>
                    <td class="lebel">Total number of alumni/ae</td><td>:</td><td><?php echo number_format($client->greek_number_of_alumni); ?></td>
                </tr>
                <tr>
                    <td class="lebel">Headquarters</td><td>:</td><td><span><span><?php echo $client->city; ?>, <?php echo $client->state; ?></span></span></td>
                </tr>
                <tr>
                    <td class="lebel">Website</td><td>:</td><td><?php echo $client->website; ?></td>
                </tr>
                <tr>
                    <td class="lebel">Phone</td><td>:</td><td><?php echo $client->phone; ?></td>
                </tr>
            </tbody>
        </table>           

    <?php
}
?>


</div><!-- #primary -->
<div id="col3">

</div>
<?php get_footer(); ?>
