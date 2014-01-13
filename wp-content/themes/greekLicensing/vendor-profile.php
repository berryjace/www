<?php
/**
* Template Name: vendor-profile
 * @package WordPress
 * @subpackage themename
 */
include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();

$vendor = $wpdb_amcdb->get_row("select * from users where id='".$_GET['vid']."'");
$vendor_profile = $wpdb_amcdb->get_row("select * from vendor_profiles where user_id='".$_GET['vid']."' and active = 1 order by update_date DESC");
$vendor_sample_product = $wpdb_amcdb->get_results("select * from vendor_sample_files where vendor_id='".$_GET['vid']."' AND use_for LIKE 'web_profile' AND active = 1");
$vendor_service = $wpdb_amcdb->get_results("select s.title from vendor_services vs INNER JOIN services s ON vs.service_id = s.id where vs.vendor_id='".$_GET['vid']."'");
//$vendor_offered_product = $wpdb_amcdb->get_results("select p.product_name from vendor_web_profile_products vp INNER JOIN products p ON vp.product_id = p.id where vp.vendor_id='".$_GET['vid']."'");

$vendor_profile_products = $wpdb_amcdb->get_results("select product_offered from vendor_profiles where user_id=".$_GET['vid'] . " and active = 1 order by update_date desc");

$vendor_profile_products = explode(",", $vendor_profile_products['0']->product_offered);

$vendor_offered_product = array();

foreach($vendor_profile_products as $vpp){
	error_log("\nvpp: " . $vpp, 3, "./errorLog.log");
	
	if ($vpp == '' || $vpp == null) continue;
	$p = $wpdb_amcdb->get_results("select product_name from products where id=".$vpp);
	
	$vendor_offered_product[] = $p['0'];
	
}

$vendor_operation = $wpdb_amcdb->get_row("select * from vendor_operations where user_id='".$_GET['vid']."'");
get_header(); 
?>

<style>
.topbar{background:#eee; margin-top:-20px; margin-bottom:20px; padding:10px}
.topbar span{ float:right}
.topbar p{ font-size:18px; color:#777}
.vendor_logo{ float:right}
.vendor_logo div{
/*    border:1px solid #eee;*/
    border:0px solid #eee;
    display:table-cell;
    vertical-align:middle;
    text-align:center
}
.vendor_logo img{
	height:auto;
	max-height:250px;  
	max-width:250px;
}

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
.sample_ctn{    
    width: 600px;
}

	#overlay{
		visibility: hidden;
		position: absolute;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		text-align: center;
		z-index: 1000;
		background-color: rgba(0, 0, 0, 0.5);
	}
	
	#overlay div{
		position: relative;
		margin: 100px auto;
		background-color: #fff;
		border: 1px solid #000;
		padding: 15px;
		text-align: center;
	}
	
	#xButton{
		position: absolute;
		top: -10px;
		right: -10px;
	}
	.resized_img{
		max-width: 250px;
		max-height: 250px;
	}
</style>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.jcarousel.min.js"></script> 
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery-fancybox-1.3.4.js"></script>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/skins/tango/skin.css" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/skins/fancybox/jquery.fancybox-1.3.4.css" />

<script type="text/javascript">


    
</script>

<div>
    <div class="topbar"><span><a href="javascript:goBack();">Return to Search Result</a></span>
        <p>Vendor Profile</p></div>
    <?php
	if(sizeof($vendor_profile) && $vendor_profile->logo_url!='')
	{
        echo '<div class="vendor_logo"><div><img class="resized_img" src="'.
		esc_url( home_url( '/' ) ).
		'crm/assets/files/vendor_profile/'.
		$vendor_profile->logo_url.'"/></div></div>';
    }
	else
	{
        echo '<div class="vendor_logo"><div><img src="'.
		get_template_directory_uri().
		'/images/OLP_Logo_BLK_SCR_250.jpg"  /></div></div>';
    }
    ?>
    
<!--    <h1><?php echo $vendor->organization_name; ?></h1>-->    
    <?php
    if(sizeof($vendor_profile)){
    ?>
        <h1><?php echo $vendor_profile->organization_name; ?></h1>
        <small>
    <?php 
    $first = true;
    
    if (sizeof($vendor_service)){
		foreach($vendor_service as $service){
			if (!$first) echo ", ";
			
			$first = false;
			
			echo $service->title;
		}
	}
    ?>
    </small>
    <br/>
    <br/>
        <?php echo '<a href="http://' . $vendor_profile->web_page . '">' . $vendor_profile->web_page . '</a>'; ?><br />        
        <?php echo $vendor_profile->address1; ?><br />
        <?php echo $vendor_profile->city.'&nbsp;'.$vendor_profile->state.'&nbsp;'.$vendor_profile->zip; ?><br />
        <?php echo $vendor_profile->phone1; ?><br />        
    <?php
    }else{?>
        <h1><?php echo $vendor->organization_name; ?></h1>
        Company Information not available.
    <?php
    }
    ?>
    

    <br />
    <!--  <h2>Services</h2>-->
    <?php
    /*
    if(sizeof($vendor_service)){        
        echo "<ul>";
        foreach ($vendor_service as $service) {
        ?>
            <li><?php echo $service->title;?></li>
        <?php
        }
        echo "</ul>";
    }else{
        echo "The vendor is yet to add this information to their profile.";
    } 
//*/   
    ?>

    

    <h2>Products Offered</h2>
    <?php
/*    $products = explode(",",$vendor_profile->product_offered);
    foreach($products as $product){
	if($product="")
	{
		$qry = "select product_name from products where id =" .$product;
    		//print_r($wpdb_amcdb->get_results($qry));
		$rslt=$wpdb_amcdb->get_results($qry);
		foreach ($rslt as $entry){
			$disp[] = $entry->product_name;
	}
	}
    }
    echo implode(', ', $disp);*/
//*
	error_log("\nvendor_offered_product count: " . count($vendor_offered_product), 3, "./errorLog.log");
    if(empty($vendor_operation->vendor_products) && count($vendor_offered_product) == 0) {
	echo "";
    
    } elseif(count($vendor_offered_product) > 0) {
	$productsArr = array();
        foreach ($vendor_offered_product as $product) {        
             $productsArr[] = $product->product_name;        
        }
	$products = implode(', ', $productsArr);
	echo $products;
    
    } else {
	echo $vendor_operation->vendor_products;
    }
 // */  
    /*
    if(sizeof($vendor_offered_product)){
        $products = '';
        foreach ($vendor_offered_product as $product) {        
             $products .= $product->product_name.', ';        
        }
        $products = substr(trim($products),0,-1).'.';
        echo $products;
    
    
    }else{
        echo "The vendor is yet to add the products they offer to their profile.";
    }
    
    echo $vendor_profile->product_offered;
    */
    ?>

    <h2>Company Description</h2>
    <?php
    echo '<p>'.!empty($vendor_profile->company_discripction) ? $vendor_profile->company_discripction : $vendor_profile->product_offered.'</p>';
    ?>
    


    <h2>Sample Products *</h2>
    <?php
    if(sizeof($vendor_sample_product)){
        echo '<div class="sample_ctn"><ul id="mycarousel" class="jcarousel-skin-tango">';
        foreach ($vendor_sample_product as $sample) {
             //$sample->file_url
            ?>
            <li>
            	<a href="javascript:void(0)" onclick="javascript:showOverlay('<?php echo esc_url( home_url( '/' ) ); ?>crm/assets/files/samples/products/large/<?php echo $sample->file_url;?>');">
            		<img src="<?php echo esc_url( home_url( '/' ) ); ?>crm/assets/files/samples/products/thumbs/<?php echo $sample->file_url;?>" width="75" height="75" alt="" />
            	</a>
            </li>
            <?php
        }
        echo '</ul></div>';
    }else{
        echo "The vendor is yet to add product samples to their profile.";
    }
    ?>    
    <div style="display:none;">
    	<?php foreach($vendor_sample_product as $sample){?>
    		<span class="links"><?php echo esc_url( home_url('/'));?>crm/assets/files/samples/products/large/<?php echo $sample->file_url;?></span>
    	<?php }?>
    </div>
</div><!-- #primary -->
<div id="col3">

</div>
<?php get_footer(); ?>

<div id="overlay">
	<div id="leftArrow" style="display:none; position:fixed;">
		<a href="javascript:void(0)" onclick="previousImage()"><img src="<?php echo esc_url(home_url('/'));?>crm/assets/images/arrow_left.png" alt="left" /></a>
	</div>
	<div id="imgContainer">
		<a href="javascript:void(0)" onclick="hideOverlay()" title="click to close"><img id="xButton" alt="X" src="<?php echo esc_url(home_url('/')); ?>crm/assets/css/fancy_close.png"/></a>
		<img id="lrgPrev"alt="large preview" src=""/>
	</div>
	<div id="rightArrow" style="display:none; position:fixed;">
		<a href="javascript:void(0)" onclick="nextImage()"><img src="<?php echo esc_url(home_url('/'));?>crm/assets/images/arrow_right.png" alt="right"/></a>
	</div>
</div>

<script type="text/javascript">

jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel(
	{
		scroll:5,
		visible:5

		}

	);

	$("#lrgPrev").on("load", function(){

		
		var src = $(this).attr('src');

		var width = $(this).width();

		//width = ($(document).width() - width)/2;
		
		var centerOffset = width/2;

		$("#leftArrow").css("left", ($(document).width()/2 - centerOffset - 100) + "px");
		$("#rightArrow").css("right", ($(document).width()/2 - centerOffset - 100) + "px");
		$("#leftArrow").css("top", ($(window).height()/3) + "px");
		$("#rightArrow").css("top", ($(window).height()/3) + "px");

		$(this).parent().css("top", ($(window).height()/3 - $(this).height()/2) + "px");
		$(this).parent().css("left", ($(window).width()/2 - $(this).width()/2) + "px");
		$(this).parent().css("position", "absolute");
		
		if ($("span.links").first().text() == src){
			$("#leftArrow").hide();
		} else {
			$("#leftArrow").show();
		}

		if ($("span.links").last().text() == src){
			$("#rightArrow").hide();
		} else {
			$("#rightArrow").show();
		}

		
	});
    
});

</script>
<style>
	#leftArrow #rightArrow{
		display: none;
		position: absolute;
		width: 30px;
		margin: 0 auto;
	}
</style>
<script>
function showOverlay(src){
	
	if ($("#lrgPrev").attr('src') == src){
		
			$("#overlay>div#imgContainer").css("width", $("#lrgPrev").width() + "px");
		
			$("#overlay").css("visibility", "visible");
		
		
	} else {

		
		$("#lrgPrev").attr('src', src);

		//alert($("#lrgPrev").attr('src'));
	
		$("#lrgPrev").load(function(){
		
			$("#overlay>div#imgContainer").css("width", $("#lrgPrev").width() + "px");
			
			$("#overlay").css("visibility", "visible");
	
		});
	}
}

function nextImage(){
	var changed = false;
	$("span.links").each(function(){
		if (!changed){
		if ($(this).text() == $("#lrgPrev").attr('src')){
			var src = $(this).next("span.links").text();
			
			changed = true;

			showOverlay(src);
		}
		}
	});
}

function previousImage(){
	var changed = false;
	$("span.links").each(function(){
		if (!changed){
		if ($(this).text() == $("#lrgPrev").attr('src')){
			var src = $(this).prev("span.links").text();
			
			changed = true;

			showOverlay(src);
		}
		}
	});
}

function hideOverlay(){
	$("#overlay").css("visibility", "hidden");
}

function goBack()
  { 
  window.history.back()
  }
</script>
