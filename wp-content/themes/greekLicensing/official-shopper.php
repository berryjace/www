<?php
/**
* Template Name: official-shopper
 * @package WordPress
 * @subpackage themename
 */

get_header(); ?>

		<div id="primary">
			<div id="content">

<?php the_post(); ?>

			  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'themename' ), 'after' => '</div>' ) ); ?>
						<?php /*?><?php edit_post_link( __( 'Edit', 'themename' ), '<span class="edit-link">', '</span>' ); ?><?php */?>
				</div><!-- .entry-content -->
				</article><!-- #post-<?php the_ID(); ?> -->

<table width="711" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td width="274" valign="top"><strong>What is your full name?</strong></td>
    <td width="417" valign="top"><input type="text" name="textfield3" id="textfield3" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>What organization are you a member of?</strong></td>
    <td valign="top"><label for="textfield"></label>
      <input type="text" name="textfield" id="textfield" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>What university do you currently attend?</strong></td>
    <td valign="top"><input type="text" name="textfield7" id="textfield7" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>What is your anticipated graduation <br />
      month and year?</strong></td>
    <td valign="top"><label for="select"></label>
      <select name="select" id="select">
        <option>Month</option>
        <option>Jan</option>
        <option>Feb</option>
        <option>Mar</option>
        <option>Apr</option>
        <option>May</option>
        <option>Jun</option>
        <option>Jul</option>
        <option>Aug</option>
        <option>Sep</option>
        <option>Oct</option>
        <option>Nov</option>
        <option>Dec</option>
      </select> <label for="select2"></label>
      <select name="select2" id="select2">
     <option>Year</option>
         <option>2012</option>
         <option>2013</option>
         <option>2014</option>
         <option>2015</option>
         <option>2016</option>
         <option>2017</option>
        <option>2018</option>
      </select></td>
  </tr>
  <tr>
    <td valign="top"><strong>What is your zipcode?</strong></td>
    <td valign="top"><input type="text" name="textfield9" id="textfield9" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>What is your phone number?</strong></td>
    <td valign="top"><input type="text" name="textfield2" id="textfield2" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>What is your email address?</strong></td>
    <td valign="top"><input  type="text" value="<?php echo $_POST["os_email"]; ?>" readonly="readonly"/></td>
  </tr>
  <tr>
    <td valign="top"><strong>Are you, or have you been, employed at a company that sells greek merchandise?</strong></td>
    <td valign="top"><label for="radio">
	      <input type="radio" name="employed" value="yes"> Yes &nbsp &nbsp
<input type="radio" name="employed" value="no"> No
    </label></td>
  </tr>
  <tr>
    <td valign="top"><strong>If so, which company?</strong></td>
    <td valign="top"><input type="text" name="textfield13" id="textfield13" /></td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top"><input type="submit" name="button" id="button" value="Submit" class="brown"/></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
</table>




				<?php comments_template( '', true ); ?>

			</div><!-- #content -->
		</div><!-- #primary -->
 <div id="col3">

</div>
<?php get_footer(); ?>