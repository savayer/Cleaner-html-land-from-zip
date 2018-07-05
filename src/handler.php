<?php
//	echo "<pre>";
//die(json_encode(array('info'=>$_POST['backfix'])));

	if (isset($_POST)) {
		$backfix = (bool)$_POST['backfix'];
		$jquery = (bool)$_POST['jquery'];
		$dtime = (bool)$_POST['dtime'];
		$link = $_POST['link'];
	}
	$filename = $_FILES['file']['name'];
	
	if ($_FILES['file']['type'] !== 'application/zip' 
	/* $_FILES['file']['type'] !== 'application/x-rar' */) {
		/* echo json_encode(
			array(
				'info' => 'Only zip archive'
			)
		); */
		echo 'Only zip archive';
		die();
	}

    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
		//ob_start();
		$folderName = 'project__' . date('d-M-Y__H-i-s');
		$path = '../uploads/' . $folderName;		
		$pathFile = $path . '/' . $filename;
		$pathProject = $path . '/' . 'html';

		mkdir($path, 0777);
		mkdir($pathProject, 0777);
		mkdir($pathProject.'/css',0777);
		mkdir($pathProject.'/js',0777);
		mkdir($pathProject.'/img',0777);
		mkdir($pathProject.'/fonts',0777);

		copy('add_js_files/dr-dtime.js', $pathProject . '/js/dr-dtime.js');
		copy('add_js_files/jquery-1.12.4.min.js', $pathProject . '/js/jquery-1.12.4.min.js');

		require_once('getDirectories.php');

        if (move_uploaded_file($_FILES['file']['tmp_name'], $pathFile)) {
			$zip = new ZipArchive;
			$zip->open($pathFile);
			$zip->extractTo($path);
			$zip->close();

			//print_r(getDirectories($path, $pathProject));
			getDirectories($path, $pathProject);

			require_once('html_cleaner.php');
        }

    }
	
	//$info_for_download = ob_get_contents();
	//$info_for_download = 'Сleaning completed. <a href="uploads/'.$folderName.'/html.zip" download>Download</a>';
	//ob_get_clean();
	
	echo 'Сleaning completed. <a href="uploads/'.$folderName.'/html.zip" download>Download</a>';
	print_r($addJs);
	// echo json_encode(array(
	// 	'info' => $info_for_download
	// ));

?>
