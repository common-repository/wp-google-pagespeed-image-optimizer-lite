<?php
function google_image_optimizer_humanFileSize($size,$unit=""){
	if( (!$unit && $size >= 1<<30) || $unit == "GB")
		return number_format($size/(1<<30),2)."GB";
	if( (!$unit && $size >= 1<<20) || $unit == "MB")
		return number_format($size/(1<<20),2)."MB";
	if( (!$unit && $size >= 1<<10) || $unit == "KB")
		return number_format($size/(1<<10),2)."KB";
	return number_format($size)." bytes";
}

function google_image_optimizer_getExt($file) {
    $tmp = explode(".", $file);
    return $tmp[(count($tmp) - 1)];
}