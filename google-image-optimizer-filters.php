<?php
class GoogleImageOptimizerFilters extends GoogleImageOptimizer
{	
	public static function google_image_optimizer_do_filters()
	{
		add_filter('wp_generate_attachment_metadata','GoogleImageOptimizer::google_image_optimizer_replace_uploaded_image');
		add_filter('manage_media_columns','GoogleImageOptimizerAdminView::google_image_optimizer_column_id');
		add_filter('manage_media_custom_column','GoogleImageOptimizerAdminView::google_image_optimizer_column_id_row',10,2);
	}
}