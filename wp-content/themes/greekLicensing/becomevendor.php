<?php
/**
* Template Name: becomevendor
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
						<?php edit_post_link( __( 'Edit', 'themename' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-<?php the_ID(); ?> -->

				<?php comments_template( '', true ); ?>

				<div>
					<h2><b>How to request access to the Vendor Gateway: <a href="javascript:void(0);" rel=" http://www.screencast.com/t/pcVLORrUXb4Z" class="tutorialLink" target="_blank">Watch Video</a></b></h2>
				</div>
				
				<script type="text/javascript">
					$(document).on("ready", function(){
						$(".tutorialLink").each(function(){
							$(this).on("click", function(){
								var url = $(this).prop('rel');
				
								window.open(url, "_blank", "height="+$(window).height()+",width="+$(window).width());
							});
						});
					});
				</script>
			</div><!-- #content -->
		</div><!-- #primary -->
 <div id="col3">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(3) ) : else : ?>


<?php endif; ?>
</div>
<?php get_footer(); ?>