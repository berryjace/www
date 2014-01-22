<?php
    global $client_name;
?>
</div><!-- #main  -->
</div><!-- #page -->
	<footer id="whitelabel-footer" role="contentinfo">
			<div>
                            &COPY; <?php echo $client_name; ?>
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