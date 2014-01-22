<?php
/**
* Template Name: nominate-vendor
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
    <td width="240" valign="top"><strong>Company Name*</strong></td>
    <td width="451" valign="top"><input  type="text" value="<?php echo $_POST["vendor-name"]; ?>" readonly="readonly"/></td>
  </tr>
  <tr>
    <td valign="top"><strong>Company contact person:</strong></td>
    <td valign="top"><label for="textfield"></label>
      <input type="text" name="textfield" id="textfield" /></td>
  </tr>
  <tr>
    <td valign="top"style=" height:50px"><strong>Company address:</strong></td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top" style="padding-left:20px"><strong>Street Address</strong></td>
    <td valign="top"><input type="text" name="textfield3" id="textfield3" /></td>
  </tr>
  <tr>
    <td valign="top" style="padding-left:20px"><strong>City</strong></td>
    <td valign="top"><input type="text" name="textfield4" id="textfield4" /></td>
  </tr>
  <tr>
    <td valign="top" style="padding-left:20px"><strong>State</strong></td>
    <td valign="top"><input type="text" name="textfield5" id="textfield5" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Company phone number:</strong></td>
    <td valign="top"><input type="text" name="textfield6" id="textfield6" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Company email address:</strong></td>
    <td valign="top"><input type="text" name="textfield7" id="textfield7" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Company website:</strong></td>
    <td valign="top"><input type="text" name="textfield8" id="textfield8" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Branded products offered for sale:*</strong></td>
    <td valign="top"><input type="text" name="textfield9" id="textfield9" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Please describe why you are nominating this vendor (if this vendor already sells Greek merchandise, please describe any evidence you may have): *</strong></td>
    <td valign="top"><textarea name="textfield10" rows="5" id="textfield10"></textarea></td>
  </tr>
  <tr>
    <td valign="top"><strong>Additional comments:</strong></td>
    <td valign="top"><textarea name="textfield2" rows="5" id="textfield2"></textarea></td>
  </tr>
  <tr>
    <td valign="top"><strong>Your name:</strong></td>
    <td valign="top"><input type="text" name="textfield12" id="textfield12" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Your email address:</strong></td>
    <td valign="top"><input type="text" name="textfield13" id="textfield13" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Your phone number:</strong></td>
    <td valign="top"><input type="text" name="textfield14" id="textfield14" /></td>
  </tr>
  <tr>
    <td valign="top"><strong>Your Greek affiliation or company:</strong></td>
    <td valign="top"><input type="text" name="textfield15" id="textfield15" /></td>
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