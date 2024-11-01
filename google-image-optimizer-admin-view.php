<?php

require_once('google-image-optimizer-functions.php');

class GoogleImageOptimizerAdminView extends GoogleImageOptimizer
{	
	
	public static function google_image_optimizer_bulk_optimize_view()
	{
		$args = array('post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any', 'post_parent' => null); 
		$attachments = get_posts($args);
		
		?>
		<div class="wrap"><h1>Google Bulk Image Optimizer</h1>
		<div class="postbox "><div class="inside">
		
		<form>
		<table class="form-table">
		
		<tr valign="top">
		<td>		
		<input disabled type="checkbox" name="only_unoptimized" id="only_unoptimized" checked="checked" /> Reoptimize	
		<p class="description">Only available in premium version.</p></td>
		</tr>
		
		
		
		<tr valign="top">
		<td>
		<input disabled type="submit" value="Optimize images" />
		</td>
		</tr>
		
		</table>		
		</form>
		
		<a href="https://codecanyon.net/item/wp-google-pagespeed-insights-image-optimizer/20596917" target="_blank"><h2>Get your PRO version today, with much more features</h2><img src="<?php echo plugin_dir_url(__FILE__);?>/assets/590.png" /></a>
		
		<span id="percentagedone"><div class="loader"></div><div id="percentagebarholder"><div id="percentagebar"><span>0%</span></div></div></span>
		<div id="bulk_results">
			<table>
				<thead>
					<tr>
						<th>
							Filename
						</th>
						<th>
							Original
						</th>
						<th>
							Optimized
						</th>
						<th>
							Percentage
						</th>
						<th>
							Thumbnails optimized
						</th>
						<th>
							Total savings
						</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
		</div>
		</div>
		</div>
		
		<?php
	}
	
	public static function google_image_optimizer_settings_view()
	{
		$domain = get_option('siteurl');
		$domain = parse_url($domain);
		$domain = str_replace('www.','',$domain['host']);

		?>
		<div class="wrap">
		<h1>Google Image Optimizer Settings</h1>
		<div class="postbox "><div class="inside">
	
		<form method="post" action="options.php">
			<?php settings_fields( 'google-image-optimizer-settings' ); ?>
			<?php do_settings_sections( 'google-image-optimizer-settings' ); ?>
			<table class="form-table">
				<tr valign="top">
				<th scope="row">Google PageSpeed API Key</th>
				<td><input type="text" name="google_image_optimizer_api_key" value="<?php echo esc_attr( get_option('google_image_optimizer_api_key') ); ?>" /><p class="description">Request your <a href="https://console.developers.google.com/" target="_blank">Google PageSpeed Insights API key</a><br>Please read the documentation of the plugin.<br />Your API restriction HTTP referrer is: <strong><?php echo $domain;?></strong></p></td>
				</tr>
				
				<tr valign="top">
				<th scope="row">Make backups</th>
				<td><input type="checkbox" disabled /><p class="description">Only available in premium version.</p></td>
				</tr>
				
				<tr valign="top">
				<th scope="row">Restore backups</th>
				<td><input disabled type="button" value="Restore all backups"><p class="description">Only available in premium version.</p></td>
				</tr>
				
				<?php
				$sizes = get_intermediate_image_sizes();
				if($sizes)
				{
					?>
					<th scope="row">Image sizes</th>
					<td>
					<?php
					foreach($sizes as $size)
					{
						?>						
						<label><input type="checkbox" name="google_image_optimizer_size_<?php echo $size;?>" value="1" <?php echo checked( 1, get_option('google_image_optimizer_size_'.$size), false );?> /> <?php echo $size;?></label>
						<?php
					}
					?>
					<p class="description">Select image sizes to optimize</p>
					</td>
					</tr>
					<?php
				}
				?>
				
				<tr valign="top">
				<th scope="row">Optimize theme images</th>
				<td><input disabled type="button" value="Optimize theme images"><p class="description">Only available in premium version.</p></td>
				</tr>
				
				<tr valign="top">
				<th scope="row">Optimize plugin images</th>
				<td><input disabled type="button" value="Optimize plugin images"><p class="description">Only available in premium version.</p></td>
				</tr>
				
				<tr valign="top">
				<th scope="row">Optimizations done today</th>
				<td><?php echo get_option('google_image_optimizer_optimizations_today');?> / 25000<p class="description">Because of Google's API you can not optimize more than 25000 images (including cropped versions) per day.</p></td>
				</tr>
			</table>
			
			
			<a href="https://codecanyon.net/item/wp-google-pagespeed-insights-image-optimizer/20596917" target="_blank"><h2>Get your PRO version today, with much more features</h2><img src="<?php echo plugin_dir_url(__FILE__);?>/assets/590.png" /></a>

			<?php submit_button(); ?>

		</form>
		</div>
		</div>
		</div>
		<?php
	}
	
	public static function register_google_image_optimizer_settings() {
		$sizes = get_intermediate_image_sizes();
		if($sizes)
		{
			foreach($sizes as $size)
			{
				register_setting( 'google-image-optimizer-settings', 'google_image_optimizer_size_'.$size );
			}
		}
		
		register_setting( 'google-image-optimizer-settings', 'google_image_optimizer_api_key' );
		
		if(!get_option('google_image_optimizer_last_day'))
		{
			register_setting( 'google-image-optimizer-settings', 'google_image_optimizer_last_day' );
		}	
		if(!get_option('google_image_optimizer_optimizations_today'))
		{
			register_setting( 'google-image-optimizer-settings', 'google_image_optimizer_optimizations_today' );
		}		
	}
	
	public static function google_image_optimizer_column_id($columns)
	{
		$columns['col_space_saved'] = __('Space saved');
		return $columns;
	}
	
	public static function google_image_optimizer_column_id_row($columnName, $attachmentID)
	{
		if($columnName == 'col_space_saved')
		{
			$info = wp_get_attachment_metadata($attachmentID);
			$ext = pathinfo(get_attached_file($attachmentID), PATHINFO_EXTENSION);
			
			if(isset($info['new_file_size']))
			{
				if($info['original_file_size'])
				{
					echo '<span>Original file: '.google_image_optimizer_humanFileSize($info['original_file_size']).'</span><br>';
				}
				else
				{
					echo '<span>Original file: '.google_image_optimizer_humanFileSize(filesize(get_attached_file($attachmentID))).'<br>';
				}
				
				echo '<span>Optimized file: '.google_image_optimizer_humanFileSize($info['new_file_size']).'</span><br>';
				echo '<span>Savings: '.round(((floatval($info['original_file_size']) - floatval($info['new_file_size'])) / floatval($info['original_file_size']) * 100),2).'%</span>';
				
			}
			elseif($ext != 'jpg' && $ext != 'png')
			{
				echo '<span>For now this file can\'t be optimized.</span>';
			}
			else
			{
				echo '<span>Original file: '.google_image_optimizer_humanFileSize(filesize(get_attached_file($attachmentID))).'</span><br>';
				echo '<span>Optimized file: File not yet optimized.</span>';			
			}
		}
	}
	
	public static function register_google_image_optimizer_submenus()
	{
		add_submenu_page(
			'upload.php',
			'Bulk Image Optimizer',
			'Bulk Image Optimizer',
			'manage_options',
			'bulk-image-optimizer',
			'GoogleImageOptimizerAdminView::google_image_optimizer_bulk_optimize_view'
		);
		
		add_submenu_page(
			'upload.php',
			'Google Image Optimizer Settings',
			'Google Image Optimizer Settings',
			'manage_options',
			'google-image-optimizer-settings',
			'GoogleImageOptimizerAdminView::google_image_optimizer_settings_view'
		);
	}
}