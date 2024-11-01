<?php
class GoogleImageOptimizer
{	
	public static function create_unique_html_for_image($filename, $src, $width, $height, $with_month = false)
	{		
		if($with_month === true)
		{
			$htmlfile = fopen(WPGIO_UPLOAD_PATH."/".$filename.".html", "w") or die("Unable to open file!");
			$html = '<img src= "'.WPGIO_UPLOAD_URL.'/'. $src.'" width="' . $width . '" height="' . $height . '" />';
		}
		else
		{
			$htmlfile = fopen(WPGIO_UPLOAD_PATH."/".$filename.".html", "w") or die("Unable to open file!");
			$html = '<img src= "'.WPGIO_UPLOAD_BASEURL.'/'. $src.'" width="' . $width . '" height="' . $height . '" />';
		}
		fwrite($htmlfile, $html);
		fclose($htmlfile);
	}
	
	public static function downloadZipFile($url, $filepath){
		
		$zipFile = WPGIO_UPLOAD_PATH."/zipfile.zip";
		
		$zipResource = fopen($zipFile, "w");
		
		$domain = get_option('siteurl');
		$domain = parse_url($domain);
		$domain = str_replace('www.','',$domain['host']);
		
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_FAILONERROR, true);
		 curl_setopt($ch, CURLOPT_HEADER, 0);
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		 curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		 curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		 curl_setopt($ch, CURLOPT_REFERER, $domain);
		 curl_setopt($ch, CURLOPT_FILE, $zipResource);
		 $raw_file_data = curl_exec($ch);

		 if(curl_errno($ch)){
			false;
		 }
		 curl_close($ch);
		 return true;
	 }
	
	public static function download_optimized_image($filename)
	{
		$optimizations_done_today = (int)get_option('google_image_optimizer_optimizations_today');
		
		if($optimizations_done_today <= 25000)
		{		
			$downloadURL = '';
			
				$downloadURL = WPGIO_GOOGLE_OPTIMIZE_URL.WPGIO_UPLOAD_URL.'/'.$filename.'.html'.WPGIO_GOOGLE_STRATEGY;
				$savePath = WPGIO_UPLOAD_PATH;
			
			if(!self::downloadZipFile($downloadURL,$savePath,$theme_image))
			{
				
			}
			else
			{
				if(get_option('google_image_optimizer_last_day') == date('Ymd'))
				{
					update_option('google_image_optimizer_optimizations_today',$optimizations_done_today + 1);
				}
				else
				{
					update_option('google_image_optimizer_last_day',date('Ymd'));
					update_option('google_image_optimizer_optimizations_today',1);
				}
			}
		}
		else
		{
			//JS error Google Api limit reached for today. Read more for explanation.
		}
	}
	
	public static function unzip_and_get_filename($filename)
	{
		$zip = new ZipArchive;
		
		$path = WPGIO_UPLOAD_PATH;
		
		$zip->open($path.'/zipfile.zip');
		if($zip->getNameIndex(0) !== 'MANIFEST')
		{
			$zip->extractTo($path.'/');
			$oldname = explode('/',$zip->getNameIndex(0));
			$zip->close();

			
			return end($oldname);
		}
		else
		{
			$zip->close();
			return false;
		}
	}
	
	public static function cleanup_temp_files($filename)
	{
		
			$path = WPGIO_UPLOAD_PATH;
		
		@unlink($path.'/MANIFEST');
		@unlink($path.'/zipfile.zip');
		@unlink($path.'/'.$filename.'.html');
		@rmdir($path.'/image/');
	}
	
	public static function google_image_optimizer_replace_uploaded_image($image_data, $bulk = false, $attachmentID = false)
	{		
		if(google_image_optimizer_getExt($image_data['file']) == "jpg" || google_image_optimizer_getExt($image_data['file']) == "png")
		{
			$filebasename = pathinfo(WPGIO_UPLOAD_PATH . '/' . $image_data['file']);
			
			self::create_unique_html_for_image($filebasename['filename'], $image_data['file'], $image_data['width'], $image_data['height'], false);
			self::download_optimized_image($filebasename['filename']);	
			
			$oldname = self::unzip_and_get_filename($filebasename['filename']);
			if($oldname !== false)
			{
				if(!$image_data['original_file_size'])
				{
					$image_data['original_file_size'] = (filesize(WPGIO_UPLOAD_PATH.'/'.$filebasename['basename']));
				}
				rename(WPGIO_UPLOAD_PATH.'/image/'.$oldname, WPGIO_UPLOAD_PATH.'/'.$filebasename['basename']);	
				$image_data['new_file_size'] = (filesize(WPGIO_UPLOAD_PATH.'/'.$filebasename['basename']));
			}

			self::cleanup_temp_files($filebasename['filename']);
			
			$sizes = get_intermediate_image_sizes();
			$sizesToCrop = array();
			if($sizes)
			{
				foreach($sizes as $size)
				{
					if(get_option('google_image_optimizer_size_'.$size) == 1)
					{
						array_push($sizesToCrop,$size);
					}
				}
			}
			
			foreach($image_data['sizes'] as $key => $size)
			{
				if(in_array($key,$sizesToCrop))
				{
					$filebasename = pathinfo(WPGIO_UPLOAD_BASEDIR . '/' . $size['file']);
					$filebasenameWithMonth = pathinfo(WPGIO_UPLOAD_PATH . '/' . $size['file']);

					self::create_unique_html_for_image($filebasename['filename'], $size['file'], $size['width'], $size['height'], true);

					self::download_optimized_image($filebasenameWithMonth['filename']);

					$oldname = self::unzip_and_get_filename($filebasenameWithMonth['filename']);
					if($oldname !== false)
					{
						if(!$image_data['sizes'][$key]['original_file_size'])
						{
							$image_data['sizes'][$key]['original_file_size'] = (filesize(WPGIO_UPLOAD_PATH.'/'.$size['file']));
						}
						@rename(WPGIO_UPLOAD_PATH.'/image/'.$oldname,WPGIO_UPLOAD_PATH.'/'.$size['file']);
						$image_data['sizes'][$key]['new_file_size'] = (filesize(WPGIO_UPLOAD_PATH.'/'.$size['file']));
					}

					self::cleanup_temp_files($filebasename['filename']);
				}
			}
		}
		
			return $image_data;
		
	}
	
	public static function google_image_optimizer_get_all_attachment_ids()
	{
		
			$args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post_status' => 'any', 'post_parent' => null, 'fields' => 'ids' );
			$attachmentsbefore = get_posts( $args );
			$attachments = array();
			foreach($attachmentsbefore as $attachment)
			{
				$attchmentinfo = wp_get_attachment_metadata($attachment);
				if($attchmentinfo['original_file_size'] == $attchmentinfo['new_file_size'] || !$attchmentinfo['original_file_size'] && !$attchmentinfo['new_file_size'])
				{
					array_push($attachments,$attachment);
				}
			}
		
		echo json_encode($attachments);
		wp_die();
	}
	
	public static function admin_notice_google_image_optimizer_api_key()
	{
		if(!get_option('google_image_optimizer_api_key'))
		{
			?>
			<div class="notice notice-info">
				<p>An Google PageSpeed Ingsight API key is required. Save it <a href="upload.php?page=google-image-optimizer-settings">here</a>. If you do not save this, the plugin will not work.</p>
			</div>
			<?php
		}
	}
}