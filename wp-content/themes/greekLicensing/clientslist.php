<?php
/**
 * Template Name: clients list
 * @package WordPress
 * @subpackage themename
 */

include_once(ABSPATH . '/cmsdb.php');
$wpdb_amcdb = CMS_DB::getInstance();
$wpdb_amcdb->show_errors();


get_header(); ?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.pajinate.js"></script> 


<script type="text/javascript">					

$(document).ready(function(){
		$('#paging_container3').pajinate({
items_per_page : 10,
item_container_id : '.alt_content',
nav_panel_id : '.alt_page_navigation'

});
		});			

</script>
<style>
#women, #men, #co-ed{ float:left; width:32%}
#women li, #men li{ list-style:none; padding:10px;}
#women li a, #men li a{ text-decoration:none}

#paging_container3{
	height: 10px;
}

div{
overflow: auto;
}

#wrapper{
margin: 0px auto;
	text-align: left;
width: 960px;
}
#paging_container3{
height: 490px;
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
padding: 10px 0 10px 7px;
}

.page_navigation a, .alt_page_navigation a{
padding:3px 5px;
margin:2px;
color:white;
      text-decoration:none;
float: left;
       font-family: Tahoma;
       font-size: 12px;
       background-color:#DB5C04;
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
</style>
<div id="women" >
<h1>Women's Organizations</h1>
<div id="paging_container" class="">					
<ul class="alt_content2">
<?php
$myclients = $wpdb_amcdb->get_results("SELECT * FROM `client_profiles` as c, users as u WHERE c.user_id=u.id and c.`greek_org_type`='2' and u.`user_status` = 'Current' ORDER BY  `u`.`organization_name` ASC ");

foreach ($myclients as $client) {
if ($client->organization_name != "Bank Fee" && $client->organization_name != "Affinity Consultants" && $client->organization_name != "Affinity Overpayment Refund")
		echo '<li><p><a href="'.home_url( '/client-profile' ).'?id='.$client->id.'">'.$client->organization_name .'</a></p></li>';					   
}
?>
</ul>	

<div class="alt_page_navigation2"></div>		
</div>
</div>


<div id="men">
<h1>Men's Organizations</h1>
<div id="paging_container" class="">					
<ul class="alt_content2">
<?php
$myclients = $wpdb_amcdb->get_results("SELECT * FROM `client_profiles` as c, users as u WHERE c.user_id=u.id and c.`greek_org_type`='1' and u.`user_status` = 'Current' ORDER BY  `u`.`organization_name` ASC ");

foreach ($myclients as $client) {
if ($client->organization_name != "Bank Fee" && $client->organization_name != "Affinity Consultants" && $client->organization_name != "Affinity Overpayment Refund")
		echo '<li><p><a href="'.home_url( '/client-profile' ).'?id='.$client->id.'">'.$client->organization_name .'</a></p></li>';					   
}
?>
</ul>	

<div class="alt_page_navigation2"></div>		
</div>
</div>


<div id="men">
<h1>Co-Ed Organizations</h1>
<div id="paging_container" class="">					
<ul class="alt_content2">
<?php
$myclients = $wpdb_amcdb->get_results("SELECT * FROM `client_profiles` as c, users as u WHERE c.user_id=u.id and c.`greek_org_type`='3' and u.`user_status` = 'Current' ORDER BY  `u`.`organization_name` ASC");

foreach ($myclients as $client) {
if ($client->organization_name != "Bank Fee" && $client->organization_name != "Affinity Consultants" && $client->organization_name != "Affinity Overpayment Refund")
		echo '<li><p><a href="'.home_url( '/client-profile' ).'?id='.$client->id.'">'.$client->organization_name .'</a></p></li>';					   
}
?>
</ul>	

<div class="alt_page_navigation2"></div>		
</div>
</div>
<?php get_footer(); ?>
