<?php
/**
 * Template Name: vendor-search
 * @package WordPress
 * @subpackage themename
 */
$display_result = 0;
include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();

function calcDist($lat_A, $long_A, $lat_B, $long_B) {
    $distance = sin(deg2rad($lat_A))
            * sin(deg2rad($lat_B))
            + cos(deg2rad($lat_A))
            * cos(deg2rad($lat_B))
            * cos(deg2rad($long_A - $long_B));

    $distance = (rad2deg(acos($distance))) * 69.09; // calculate in miles
    //-- To get kilometers, multiply miles by 1.61
    //$distance = (float) $distance * 1.61;
    return $distance;
}
// get all the zipcodes within the specified radius - default 50
function zipcodeRadius($lat, $lon, $radius, $wpdb_amcdb)
{
    $radius = $radius ? $radius : 50;
    $sql = 'SELECT distinct(zip) FROM zip_codes  WHERE (3958*3.1415926*sqrt((Latitude-'.$lat.')*(Latitude-'.$lat.') + cos(Latitude/57.29578)*cos('.$lat.'/57.29578)*(Longitude-'.$lon.')*(Longitude-'.$lon.'))/180) <= '.$radius.';';
    //echo $sql;
    $result = $wpdb_amcdb->get_results($sql);
    foreach($result as $row)
    { 
	$zipcodeList[]=$row->zip;
    } 
    $zipcodeList = implode(",",$zipcodeList);
    return $zipcodeList;
}
//echo '<pre>';print_r($_REQUEST);echo '</pre>';

$vendor_service = $wpdb_amcdb->get_results("select s.id, s.title from services s");
$white_label = '';

$client_name = $_REQUEST['greekorg'];
//echo '<pre>';print_r($client_name);echo '</pre>';

if (empty($client_name)) {
    $client_name = $_REQUEST['client_name'];
}
$client = $wpdb_amcdb->get_row("select * from users where organization_name='$client_name'");
if (!sizeof($client)) {
    die('Invalid Parameter given');
} else {
    $client_id = $client->id;
    $white_label = $wpdb_amcdb->get_row("select * from white_label where client_id='$client_id'");
    if (!sizeof($white_label)) {
        $white_label = array();
    } else {
        // We'll populate the JS later
    }
}

$client_banner_sql = "select b.image,b.link,b.start_date,b.end_date from banners b
INNER JOIN banners_clients bc ON b.id = bc.banner_id
WHERE bc.client_id ='". $client_id ."'
AND b.is_active = 1";
//echo $client_banner_sql;
//$banner = $wpdb_amcdb->get_row($client_banner_sql);
$banner = $wpdb_amcdb->get_results($client_banner_sql);

/*
$sql_vendor = "SELECT  a . * , u . *, vpf.logo_url
FROM  users AS u
INNER JOIN category_association a ON u.id = a.vendor_id
LEFT JOIN vendor_profiles vpf ON u.id = vpf.user_id";
$join_tables = '';
$condition = " WHERE  u.id=a.vendor_id
AND a.`client_id` ='" . $client_id . "'";
*/
$sql_vendor = "SELECT  a . * , u . *, vpf.logo_url
FROM  users AS u
INNER JOIN licenses a ON u.id = a.vendor_id
LEFT JOIN vendor_profiles vpf ON u.id = vpf.user_id and vpf.id = ( select max(vpf2.id) from vendor_profiles vpf2 where vpf2.user_id = u.id and vpf2.active = 1 order by vpf2.update_date desc)";
$join_tables = '';
$condition = " WHERE  u.id=a.vendor_id
AND a.`client_id` ='" . $client_id . "' and status = 4";

$order_by = "  order by u.organization_name ASC ";
$group_by = "  group by u.id ";

if (isset($_REQUEST['hdnsubmit'])) {
    $display_result = 1;
    $display_result = 1;
    $city = $_REQUEST['city'];
    $zip_code = $_REQUEST['zip_code'];
    $product = ($_REQUEST['product'] == 'Product Name') ? '': trim($_REQUEST['product']) ;
    $services = $_REQUEST['services'];
	$vendor_name = $_REQUEST['vendor_name'];

	if ($city == "City or State") $city = null;
	if ($zip_code == "Zip Code") $zip_code = null;
	if ($product == "Product Name") $product = null;
	if ($vendor_name == "Company Name") $vendor_name = null;


    //$sql_vendor='';
    if (!empty($city) and $city != "City/State") {
        $condition .= " AND ( u.city like '" . $city . "%' ";
        $condition .= " OR u.state like '" . $city . "%' ) ";
    }
	if (!empty($vendor_name) and $vendor_name != "Company Name") {
	    $condition .= " AND u.organization_name like '%" . $vendor_name . "%' ";
       }
    if (!empty($zip_code) and $zip_code != "Zip Code") {
        //$condition .= " AND u.zipcode like '%".$zip_code."%' ";
        $zip_sql = "select latitude, longitude from zip_codes
			WHERE zip ='" . $zip_code . "'
			GROUP BY zip";
        $zip_info = $wpdb_amcdb->get_row($zip_sql);
        if (sizeof($zip_info)) {
	    $zipCodeList = zipcodeRadius($zip_info->latitude, $zip_info->longitude, 100, $wpdb_amcdb);
	    //echo $zipCodeList; exit;
	    $condition .= " AND u.zipcode IN($zipCodeList) AND u.account_type='2'";
	    //print_r($zip_info);
        }
    }

    if (!empty($product) and $product != "Product Name") {
        $join_tables .=
                "
			LEFT JOIN vendor_web_profile_products vwpp ON u.id = vwpp.vendor_id
			LEFT JOIN products p ON vwpp.product_id = p.id ";
        $condition .=
                " AND
			(
			 u.organization_name LIKE '%" . $product . "%'
			 OR vpf.organization_name LIKE '%" . $product . "%'
			 OR vpf.company_discripction LIKE '%" . $product . "%'
			 OR p.product_name LIKE '%" . $product . "%'
			) ";
    }

    if (!empty($services) && count($services) > 0) {
        $service = '';
        foreach ($services as $s) {
            $service.=$s . ',';
        }
        $service = substr($service, 0, -1);
        $join_tables .=" LEFT JOIN vendor_services vs ON u.id = vs.vendor_id ";
        $condition .= " AND vs.service_id IN (" . $service . ") ";
    }
}
$sql_vendor = $sql_vendor . $join_tables . $condition . $group_by . $order_by;
//echo $sql_vendor;

$vendors = $wpdb_amcdb->get_results($sql_vendor);
$vendors_array = array();
if (count($vendors) > 0) {
    foreach ($vendors as $vendor) {
        if (isset($_REQUEST['hdnsubmit']) && (!empty($zip_code) && $zip_code != "Zip Code")) {
            //$distance_in_miles = calcDist($zip_info->latitude, $zip_info->longitude, $vendor->latitude, $vendor->longitude);
            //$vendor->distanceInMiles = $distance_in_miles;
            //$vendor->distanceInKM = $distance_in_miles * 1.61;
            //** within 100 miles
            $vendors_array[] = $vendor;
           
        } else {
            $vendor->distanceInMiles = 0.0;
            $vendor->distanceInKM = 0.0;
            $vendors_array[] = $vendor;
        }
    }
}
//echo '<pre>';print_r($vendors_array);echo '</pre>';
if(!empty($clientId))
{
    get_header('whitelabel');
}
else
{
    include_once('header.php');
}
?>

<script type="text/javascript">
/*
    var white_label_props=<?php echo json_encode($white_label); ?>;
    $(document).ready(function(){
       //$('body').css('background-color',white_label_props.bg_color);
       $("#whitelabel-footer").css("background-color",white_label_props.footer_color);

       $(".resized_img").each(function(){

           var dim = $(this).width()/$(this).height();

           var bWide = $(this).width() > $(this).height();

           if (bWide){
				$(this).width("150px");
				$(this).height(150 + "px");

           } else {
				$(this).width(150 + "px");
				$(this).height("150px");
           }
    	   
       });
    }); */
    
</script>

<style type="text/css">
    #searchfilter td{ padding:5px}
    .outer_b{float:left; background:#ececec; margin-right:15px; margin-bottom:18px;-webkit-border-radius: .54em;
             -moz-border-radius: .5em; }
    .logo_b{background:#fff; height:111px; width:210px; text-align:center;  border:1px solid #cfcfcf;-webkit-border-radius: .4em;
            -moz-border-radius: .4em; display:table-cell; vertical-align:middle}
    .logo_b li{ list-style:none;}
    .logo_b ul{ padding:0; margin:0}
    .logo_b li a{ display:block}

    .info_b{padding:5px; text-align:center;}
    .info_b span{ font-size:11px}
    #featured_vendor_banner{ text-align:center; padding:10px 0px 10px 10px;}

    #paging_container3{
        height: 10px;
    }

    #wrapper{
        margin: 0px auto;
        text-align: left;
        width: 960px;
    }

    .alt_content2  { padding:0; margin:0; width:300px}
    .alt_content2 li{ border-bottom:1px solid #efefef; }
    .alt_content2 li a:hover{ font-weight:bold}

    .ellipse{
        float: left;
    }

    .container{
        width: 260px;
        float: left;
        margin: 50px 10px 10px;
        padding: 20px;
        background-color: white;
    }

    .page_navigation , .alt_page_navigation{
        padding: 10px ; clear:both; float:right; margin-right:10px
    }

    .page_navigation a, .alt_page_navigation a{
        padding:3px 5px;
        margin:2px;
        color:white;
        text-decoration:none;
        float: left;
        font-family: Tahoma;
        font-size: 12px;
        background-color: #800000;
    }
    .active_page{
        background-color:white !important;
        color:black !important;
    }

    .content, .alt_content{
        color: black;
        margin:0;
    }

    .content li, .alt_content li, .content > p{
        padding: 5px
    }
    .left,.right{border: 0px solid;}
    .left{
        float: left;
    }
    .right{
        float: right;
        margin-right: 100px;
    }
    .right input[type=text]{
        width: 110px;
        margin-bottom: 0px !important;
    }
    .clear{
        clear: both;
    }
    .services{
        list-style: none;
        margin: 0px;
        padding: 0px;
    }
    .services li{
        padding: 5px;
    }
    .bannerCtn{
        border: 0px solid;
        margin-right:10px;
        text-decoration: none;
    }
    #previousCtn{
        float: right;
        margin: 18px 100px 0 0;
    }
    
    #banner_container{
    	margin: 0 auto;
    	width: 1170px;
    	text-align: center;
    	max-height: 100px;
    	overflow: hidden;
    }
    .resized_img{
    	max-width: 150px;
    	max-height: 150px;
    }
    .img_container{
    	height : 150px;
    }
</style>

<script type="text/javascript">
	$(document).ready(function(){
	    console.log("ready");
	    $(".resized_img").each(function(){
	
			var height = $(this).height();
	
			console.log("height: " + height + " src: " + $(this).attr("src"));
	
			$(this).css("margin-top", (75 - (height/2)) + "px");
	    });//*/
	
	    $("a.next_link,a.last_link,a.page_link,a.first_link,a.previous_link").on("click", function(){
	        $(".resized_img").each(function(){
	
				var height = $(this).height();
	
				console.log("height: " + height + " src: " + $(this).attr("src"));
	
				$(this).css("margin-top", (75 - (height/2)) + "px");
	        });
	    });
	});
</script>


<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.pajinate.js"></script>


<div>

	<div id="banner_container">
		<?php 
		if (!empty($banner)){

		$bans = array();
		$today = new DateTime();
		foreach($banner as $ban){
			if (new DateTime($ban->start_date) <= $today && $today <= new DateTime($ban->end_date)){
				$bans[] = $ban;
			}
		}
		if (!empty($bans)){
		$id = array_rand($bans);
		?> 
		<a href="<?php echo $bans[$id]->link;?>"><img src="<?php echo esc_url(home_url('/'));?>crm/assets/files/banners/<?php echo $bans[$id]->image?>"/></a>
		<?php }}?>
	</div>
    <h1><?php echo $client_name; ?></h1>

    <div style="background-color: #FFF; padding:20px; border:1px solid #ddd;box-shadow:inset 0 0 20px #eee"><h2 style="font-size:20px">Narrow Result</h2>

        <form id="form_filter" name="form1" method="get" action="">
            <div class="left">
                <ul class="services">
                    <?php
                    foreach ($vendor_service as $service) {
						$checked = 0;
						
						foreach($services as $serv){
							if ($service->id == $serv) $checked = 1;
						}
                        ?>
                        <li>
                            <input type="checkbox" name="services[]" id="checkbox<?php echo $service->id; ?>" value="<?php echo $service->id; ?>" <?php echo ($checked)? 'checked':''?>/>&nbsp;&nbsp;<label for="checkbox<?php echo $service->id; ?>"><?php echo ltrim($service->title,'We '); ?></label><br/>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="right">
		Looking For:&nbsp;
                <input name="product" type="text" id="product" onclick="this.value = '';" value="<?php echo !empty($product)? $product:'Product Name'?>" size="20" />
				&nbsp;&nbsp;&nbsp;&nbsp;<input name="vendor_name" type="text" id="vendor_name" value="<?php echo !empty($vendor_name)? $vendor_name:'Company Name'?>" onclick="this.value = '';" size="20" />
                &nbsp;&nbsp;&nbsp;Near&nbsp;&nbsp;<input name="zip_code" type="text" id="zip_code" value="<?php echo !empty($zip_code)? $zip_code:'Zip Code'?>" onclick="this.value = '';" size="20" />
                &nbsp;&nbsp;&nbsp;&nbsp;Or&nbsp;&nbsp;<input name="city" type="text" id="city" onclick="this.value = '';" value="<?php echo !empty($city)? $city:'City or State'?>" />
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="button" id="button" value="Submit" class="brown" />
                <input type='hidden' name='hdnsubmit' value='submit'/>
                <input type='hidden' name='client_name' value='<?php echo $client_name; ?>' />
                <input type='hidden' name='greekorg' value='<?php echo $client_name; ?>' />
            </div>
            <?php
            if (isset($_REQUEST['hdnsubmit'])) {
                ?>
                <div id="previousCtn">
                    <a href="javascript:goBack();" id="view_previous">View Previous Results</a>
                </div>
                <?php
            }
            ?>

            <div class="clear"></div>

        </form>
    </div>
    <?php
//if($display_result==1){
    ?>
    <div><img src="<?php bloginfo('template_url'); ?>/images/greeklicensing_box_shadow.jpg" /></div>

    <div style="margin-top:-4px;background:url(<?php bloginfo('template_url'); ?>/images/greeklicensing_gray_gradient.jpg) repeat-x; height:47px;  border-left:1px solid #ddd; border-right:1px solid #ddd; text-align:center; padding-left:10px">

        <table width="1151" border="0" cellspacing="0" cellpadding="0" height="47" align="center">
            <tr>
                <td width="159" height="47" align="left" valign="middle"><?php echo sizeof($vendors_array); ?>  search results</td>
                <td width="715" align="center" valign="middle">&nbsp;</td>
                <td width="277" align="right" valign="middle"><img src="<?php bloginfo('template_url'); ?>/images/information.png" /> Click company Logo to view Profile </td>
            </tr>
        </table>
    </div>
    <div style="background-color: white; border:1px solid #eee; padding:18px; padding-right:0px;">
        <div id="paging_container3" class="">
            <ul class="alt_content">
                <?php
                if (sizeof($vendors) > 0) {
                    //foreach ($vendors as $vendor) {
                    foreach ($vendors_array as $vendor) {
                        ?>
                        <div class="outer_b">
                            <?php
                            //echo $vendor->latitude.', '. $vendor->longitude.', '. $vendor->zipcode.', '. $vendor->distanceInMiles.', '. $vendor->distanceInKM;
                            ?>
                            <div class="logo_b">
                                <ul>
                                    <li>
                                    	<div class="img_container">
                                        <a href="vendor-profile/?vid=<?php echo $vendor->id; ?>">
                                            <?php
                                            if (trim($vendor->logo_url) != '') {
                                                ?>
                                                <img class="resized_img" src="<?php echo esc_url(home_url('/')); ?>crm/assets/files/vendor_profile/<?php echo $vendor->logo_url; ?>" />
                                                <?php
                                            } else {
                                                ?>
                                                <!--                                    <img src="<?php //bloginfo('template_url');      ?>/images/vendor_logo_1.jpg" />-->
                                                <img src="<?php bloginfo('template_url'); ?>/images/OLP_Logo_GRAY_150.jpg" />
                                                <?php
                                            }
                                            ?>

                                        </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="info_b">

                                <?php echo(substr($vendor->organization_name, 0, 30)); ?> <br />
                                <span><?php // echo $vendor->website;       ?></span>
                                <?php echo $vendor->phone; ?>
                            </div>
                        </div>

                        <?php
                    }
                }
                ?>
            </ul>

            <div class="alt_page_navigation"> </div>

        </div>
        <br  style="clear:both" />
    </div>


    <?php
//}
    ?>

    <br style="clear:both" />
</div>

</div><!-- #primary -->
<div id="col3">

</div>
<?php
if(!empty($clientId))
{
    get_footer('whitelabel'); 
}
else
{
    include_once('footer.php');
}
?>

<script type="text/javascript">

    $(document).ready(function() {
        if($(".outer_b").size()){
            $('#paging_container3').pajinate({
                items_per_page: 15,
                item_container_id: '.alt_content',
                nav_panel_id: '.alt_page_navigation'

            });

            $('#view_previous').on('click', function() {
                jQuery('#form_filter')[0].submit();
                return false;
            });
        }
    });

</script>
<script>
function goBack()
  { 
  window.history.back()
  }
</script>
