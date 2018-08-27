
    <?php
    require_once('phpQuery.php');
	require('HtmlFormatter.php');
	//$filename - zip
	$html = phpQuery::newDocumentFile('../uploads/' . $folderName . '/html/index.html' );

	$html['head']->find('script')->remove();
	$html->find('noscript')->remove();
	$html->find('style')->remove();
	$html->find('link')->remove();

	$html->find('script')->filter(function($i, $node) {
	  if ($node->getAttribute('src')) {
			$nodeTmp = pq($node);
			$nodeTmp->remove();
		}
		if (strpos($node->nodeValue, 'dtime') === false) {
			$node->nodeValue = '';
		}
	});

	$imgs = $html->find('img');

	foreach ($imgs as $img) {
		$pqLink = pq($img); //pq делает объект phpQuery
	//	$src[] = $pqLink->attr('src');
		$re = '/(.*\/(.*\/.*\.(png|jpg|jpeg|gif|svg)))/';
		$subst = '$2';
		$new_src = preg_replace($re, $subst, $pqLink->attr('src'));
		$pqLink->attr('src', $new_src);
	}

	if ($backfix) {
		$html['head']->append('<script src="/js/script.js"></script>');
	}
	if ($jquery) {
		$html['head']->append('<script src="js/jquery-1.12.4.min.js"></script>');
	}
	if ($dtime) {
		$html['head']->append('<script src="js/dr-dtime.js"></script>');
	}

	$html['a']->attr('href', '');
	$html['img']->removeAttr('tppabs');
	$html['a']->removeAttr('tppabs');

	/**********add styles and scripts on page**********/

	$Css_count = count($addCss);
	if ($Css_count > 1) {
		for ($i = 0; $i < $Css_count; $i++) {
			$html['head']->append('<link rel="stylesheet" href="css/' . $addCss[$i] . '">');
		}
	} else if ($Css_count != 0) {
		$html['head']->append('<link rel="stylesheet" href="css/' . $addCss[0] . '">');
	}

	$Js_count = count($addJs);
	if ($Js_count > 1) {
		for ($i = 0; $i < $Js_count; $i++) {
			$html['head']->append('<script src="js/' . $addJs[$i] . '"></script>');
		}
	} else if ($Js_count != 0) {
		$html['head']->append('<script src="js/' . $addJs[0] . '"></script>');
	}
	/****************************************************/

	file_put_contents('../uploads/tmp.html', $html);


	/*******************************************/

	$html2 = file_get_contents('../uploads/tmp.html');
	$html2 = preg_replace('/<!--(.*?)-->/', '', $html2);
	$html2 = preg_replace('(<script type="text/javascript"></script>)', '', $html2);
	$html2 = preg_replace('(<script></script>)', '', $html2);
	$html2 = preg_replace('/href=""/', 'href="'.$link.'"', $html2);
	//htmlspecialchars_decode
	$html2 = preg_replace('/\n\n/', '', $html2);
	
	
	$html2 = HtmlFormatter::format($html2);
	file_put_contents('../uploads/'. $folderName .'/html/index_clean.html', $html2);
	unlink('../uploads/tmp.html');
	// unlink('../uploads/'. $folderName . '/' . $filename);
	$dirname = preg_replace('(.zip)', '', $filename);
	rmRec('../uploads/' . $folderName . '/' . $dirname);
	zip('../uploads/' . $folderName . '/html', '../uploads/' . $folderName . '/html.zip');


	function rmRec($path) {
		if (is_file($path)) return unlink($path);
		if (is_dir($path)) {
		  foreach(scandir($path) as $p) 
		  	if (($p!='.') && ($p!='..'))
				rmRec($path.DIRECTORY_SEPARATOR.$p);
		  	return rmdir($path); 
		}
		return false;
	}


	function zip($source, $destination)
	{
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}
	
		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}
	
		$source = str_replace('\\', DIRECTORY_SEPARATOR, realpath($source));
		$source = str_replace('/', DIRECTORY_SEPARATOR, $source);
	
		if (is_dir($source) === true) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source),
				RecursiveIteratorIterator::SELF_FIRST);
	
			foreach ($files as $file) {
				$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
				$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
	
				if ($file == '.' || $file == '..' || empty($file) || $file == DIRECTORY_SEPARATOR) {
					continue;
				}
				// Ignore "." and ".." folders
				if (in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1), array('.', '..'))) {
					continue;
				}
	
				$file = realpath($file);
				$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
				$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
	
				if (is_dir($file) === true) {
					$d = str_replace($source . DIRECTORY_SEPARATOR, '', $file);
					if (empty($d)) {
						continue;
					}
					$zip->addEmptyDir($d);
				} elseif (is_file($file) === true) {
					$zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file),
						file_get_contents($file));
				} else {
					// do nothing
				}
			}
		} elseif (is_file($source) === true) {
			$zip->addFromString(basename($source), file_get_contents($source));
		}
	
		return $zip->close();
	}
