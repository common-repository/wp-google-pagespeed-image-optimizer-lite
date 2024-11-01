<?php
class GoogleImageOptimizerAdminStyles extends GoogleImageOptimizer
{	
	public static function wp_google_pagespeed_image_optimizer_load_css($hook)
	{
		if($hook == 'upload.php' || $hook == 'media_page_bulk-image-optimizer')
		{
			wp_register_style( 'wp_google_pagespeed_image_optimizer_css', plugins_url('assets/css/wp-google-pagespeed-image-optimizer.css', __FILE__), false, '1.0.0' );
			wp_enqueue_style( 'wp_google_pagespeed_image_optimizer_css' );
		}
	}
}