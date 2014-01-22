<?php
/*
Plugin Name: Recent Tweets
Plugin URI: http://zzzprofits.com/forums/21-Wordpress-Plugins-Support
Description: Recent Tweets allows you to easily display your most recent Tweets in a sidebar widget. To install, click activate and then go to Appearance > Widgets to find 'Recent Tweets'. All settings are contained within the widget.
Version: 1.1
Author: ZZZ Profits
Author URI: http://www.ZZZProfits.com
*/

add_action("widgets_init", array('Recent_Tweets', 'register'));
register_activation_hook( __FILE__, array('Recent_Tweets', 'activate'));
register_deactivation_hook( __FILE__, array('Recent_Tweets', 'deactivate'));
class Recent_Tweets {
  function activate(){
    $data = array('titletweets' => 'My Recent Tweets', 'option1' => '', 'option2' => '3', 'option3' => 'No', 'option4' => 'Yes');
    if ( ! get_option('recent_tweets')){
      add_option('recent_tweets' , $data);
    } else {
      update_option('recent_tweets' , $data);
    }
  }
  
 function control(){
  $data = get_option('recent_tweets');
  ?>
    <p><label>Title: <input name="recent_tweets_titletweets"
type="text" value="<?php echo $data['titletweets']; ?>" /></label></p>
  <p><label>Twitter Username:<br />@<input name="recent_tweets_option1"
type="text" value="<?php echo $data['option1']; ?>" /></label></p>
  <p><label># of Tweets to Show:</label> 
  <select name="recent_tweets_option2">
  <option <?php if ($data['option2'] == 1){echo 'selected="selected"';} ?>>1</option>
  <option <?php if ($data['option2'] == 2){echo 'selected="selected"';} ?>>2</option>
  <option <?php if ($data['option2'] == 3){echo 'selected="selected"';} ?>>3</option>
  <option <?php if ($data['option2'] == 4){echo 'selected="selected"';} ?>>4</option>
  <option <?php if ($data['option2'] == 5){echo 'selected="selected"';} ?>>5</option>
  <option <?php if ($data['option2'] == 6){echo 'selected="selected"';} ?>>6</option>
  <option <?php if ($data['option2'] == 7){echo 'selected="selected"';} ?>>7</option>
  <option <?php if ($data['option2'] == 8){echo 'selected="selected"';} ?>>8</option>
</select></p>
<p><label>Display "Follow Me" Link?</label> 
  <select name="recent_tweets_option3">
  <option <?php if ($data['option3'] == "Yes"){echo 'selected="selected"';} ?>>Yes</option>
  <option <?php if ($data['option3'] == "No"){echo 'selected="selected"';} ?>>No</option>
</select></p>
<p><label>Give Us Credit? </label>
  <select name="recent_tweets_option4">
  <option <?php if ($data['option4'] == "Yes"){echo 'selected="selected"';} ?>>Yes</option>
  <option <?php if ($data['option4'] == "No"){echo 'selected="selected"';} ?>>No</option>
</select></p>

  <?php
   if (isset($_POST['recent_tweets_option1'])){
   	$data['titletweets'] = attribute_escape($_POST['recent_tweets_titletweets']);
    $data['option1'] = attribute_escape($_POST['recent_tweets_option1']);
    $data['option2'] = attribute_escape($_POST['recent_tweets_option2']);
    $data['option3'] = attribute_escape($_POST['recent_tweets_option3']);
    $data['option4'] = attribute_escape($_POST['recent_tweets_option4']);
    update_option('recent_tweets', $data);
  }
}


  function deactivate(){
    delete_option('recent_tweets');
  }
  function widget($args){
  	$data = get_option('recent_tweets');
    echo $args['before_widget'];
    echo $args['before_title'] . $data['titletweets'] . $args['after_title'];
    
/* Call Twitter */
    echo '<style type="text/css">
#twitter_update_list li {
list-style-type: none;
padding-top: 10px;
}</style>
<div id="twitter_update_list">
<script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"> </script>
<script type="text/javascript" src="http://twitter.com/statuses/user_timeline/'.$data['option1'].'.json?callback=twitterCallback2&count='.$data['option2'].'"></script></div><br />';

/* Change Options */

$firstletter = $data['option1'];
if ($firstletter[0] == "a" || $firstletter[0] == "i" || $firstletter[0] == "m" || $firstletter[0] == "c" || $firstletter[0] == "j" || $firstletter[0] == "s" || $firstletter[0] == "e" || $firstletter[0] == "p" || $firstletter[0] == "g"){
$thelink = "Make Money Online";
} else {
$thelink = "Make Money Online Free";
}

	if ($data['option3'] == "Yes"){
echo '<a href="http://twitter.com/'.$data['option1'].'">Follow @'.$data['option1'].'</a><br />';} else {}
		
if ($data['option4'] == "Yes"){
echo 'Powered By: <a href="http://www.zzzprofits.com/">'.$thelink.'</a>';} else {}

echo $args['after_widget'];
  }
function register(){
    register_sidebar_widget('Recent Tweets', array('Recent_Tweets', 'widget'));
    register_widget_control('Recent Tweets', array('Recent_Tweets', 'control'));
  }
}



?>