</div><!-- #main  -->
</div><!-- #page -->

	<footer role="contentinfo">
		<div style="clear:both;text-align:center;width:100%;">
			<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose Symantec SSL for secure e-commerce and confidential communications.">
				<tr>
				<td width="135" align="center" valign="top"><script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=www.greeklicensing.com&amp;size=L&amp;use_flash=NO&amp;use_transparent=NO&amp;lang=en">
				</script>
				<br />
				<a href="http://www.symantec.com/verisign/ssl-certificates" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;">ABOUT SSL CERTIFICATES</a></td>
				</tr>
			</table>
		</div>
			<div id="colophon">
				<small>&copy Copyright <?php echo date('Y') . " " . esc_attr( get_bloginfo( 'name', 'display' ) ); ?> </small>
				<?php /*?><?php wp_nav_menu( array( 'theme_location' => 'footer' ) ); ?><?php */?>
			</div>
	</footer><!-- #colophon -->



<?php wp_footer(); ?>
<script language="javascript">
function positionFooter() { var mFoo = $("#colophon"); 
if ((($(document.body).height() + mFoo.height()) < $(window).height() && mFoo.css("position") == "fixed") || ($(document.body).height() < $(window).height() && mFoo.css("position") != "fixed")) { mFoo.css({ position: "fixed", bottom: "0px" }); } 
else { mFoo.css({ position: "static" }); } } $(document).ready(function () { positionFooter(); $(window).scroll(positionFooter); $(window).resize(positionFooter); $(window).load(positionFooter); });
</script>

</body>
</html>