<?php
function getDirectories($base_dir, $pathProject, $level = 0) {
	global $addJs, $addCss;
	$directories = array();
	foreach(scandir($base_dir) as $file) {
		if($file == '.' || $file == '..') continue;
		$dir = $base_dir.DIRECTORY_SEPARATOR.$file;
		if(is_dir($dir) && $dir != $base_dir.'/www.googleadservices.com' 
			&& $dir != $base_dir.'/static.user-grey.com' ) {
			$directories[]= array(
					'level' => $level,
					'name' => $file,
					'path' => $dir,
					'children' => getDirectories($dir, $pathProject, $level +1)
			);
		} else {
			$directories[]= array(
				'level' => $level,
				'name' => $file,
				'path' => $dir,
			);
			
			$tmpFile = new SplFileInfo($file);
			$extension = $tmpFile->getExtension();
			if ($extension == 'html' || $extension == 'htm') {
				rename($dir, $pathProject . DIRECTORY_SEPARATOR . 'index.html');
			}
			if ($extension == 'css') {
				if ($file != 'order_me.css') {
					if (count($addCss) == 0 || !in_array($file, $addCss)) {
						$addCss[] = $file;
						rename($dir, $pathProject . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file);
					}
				}
			}
			$js_files = array(
				'jquery-1.12.4.min.js',
				'js.cookie.js',
				'moment-with-locales-2.18.1.min.js',
				'order_me.js',
				'validation.js',
				'video_avid.js',
				'dr-dtime.js',
				'placeholders-3.0.2.min.js',
				'history.ielte7.min.js',
				'9.js'
			);
			if ($extension == 'js') {
				if (!in_array($file, $js_files)) {
					if (count($addJs) == 0 || !in_array($file, $addJs)) {
						$addJs[] = $file; 
						rename($dir, $pathProject . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file);
					}
				}
			}
			if ($extension == 'jpg' || $extension == 'jpeg'
					|| $extension == 'png' || $extension == 'gif' ) {
				rename($dir, $pathProject . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file);
			}
			if ($extension == 'eot' || $extension == 'ttf' || $extension == 'svg'
					|| $extension == 'woff' || $extension == 'woff2' || $extension == 'woff2' ) {
				rename($dir, $pathProject . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $file);
			}
			
		}
	}
	// $addJs = array_unique($addJs);
	// $adds = array_unique($addCss);
	return $directories;
}
