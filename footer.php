<?php
	
	/*
	 * Firefront Footer Action 
	 * 10 - firefront_site_footer
	 */
	do_action( 'firefront_footer' ); 

	/*
	 * Firefront Footer After Action 
	 * 10 - firefront_overlay_search_form
	 * 20 - firefront_mobile_menu
	 * 30 - firefront_secondary_bar
	 * 40 - firefront_back_to_top
	 */
	do_action( 'firefront_footer_after' ); 
?>
		</div><!-- .firefront-body-inner -->
	<?php wp_footer(); ?>
	</body>
</html>
