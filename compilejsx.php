<?php

	$arr = array(
		"cache_path"	=>	"/var/www/html/compile_cache",
		"output_path"	=>	"/var/www/html/bin",
		"jsfolders"		=>	array("builder","exporter","filemanager","usermanager"),
		"cssfolders"	=>	array("builder","exporter","filemanager","usermanager","login","toolbar","mainmenu","viewer"),
		"types"			=>	array("css", "js")
	);

	$hashes = json_decode(file_get_contents($arr['cache_path']."/compilejsx_hashes.json"), true);

	$new = false;

	if(!isset($hashes['md5'])) {
		$hashes['md5'] = array();
		$new = true;
	}
	if(!isset($hashes['crc32'])) {
		$hashes['crc32'] = array();
		$new = true;
	} 

	$write_hash = false;

	foreach($arr['types'] as $type) {

		foreach($arr[$type.'folders'] as $app) {

			$state = false;

			if(!$new) {
				foreach(glob($arr['cache_path']."/".$type."/".$app."/*.".$type) as $js) {	// go through all js files
					$cached_crc32 = $hashes['crc32'][$js];					// get stored crc32 hash
					$current_crc32 = crc32(file_get_contents($js));			// generate current crc32 hash
					if($cached_crc32 === $current_crc32) {					// if same, then file may be same
						$cached_md5 = $hashes['md5'][$js];					// get stored md5 hash
						$current_md5 = md5(file_get_contents($js));			// generate current md5 hash
						if($cached_md5 !== $current_md5) {					// if NOT same, then file has changed
							$hashes['md5'][$js] = $current_md5;				// store md5 hash for later
							$hashes['crc32'][$js] = $current_crc32;			// store crc32 hash for later
							$state = true;									// will write new file and update hashes
						}
					} else {
						$hashes['md5'][$js] = md5(file_get_contents($js));	// store md5 hash for later
						$hashes['crc32'][$js] = $current_crc32;				// store crc32 hash for later
						$state = true;										// will write new file and update hashes
					}
				}
			} else {
				foreach(glob($arr['cache_path']."/".$type."/".$app."/*.".$type) as $js) {	// go through all js files
					$hashes['md5'][$js] = md5(file_get_contents($js));		// store md5 hash for later
					$hashes['crc32'][$js] = crc32(file_get_contents($js));	// store crc32 hash for later
					$state = true;											// will write new file and update hashes
				}
			}
			/*End check hash*/
			
			if($state) {

				$file = fopen($arr['output_path']."/".$type."/".$app.".".$type, 'w');
				foreach(glob($arr['cache_path']."/".$type."/".$app."/*.".$type) as $js)
					fwrite($file, file_get_contents($js)."\n");
				fclose($file);

				echo $arr['output_path']."/".$type."/".$app.".".$type." created\n";

				$write_hash = true;
			}
		}
	}

	if($write_hash) {
		$file = fopen($arr['cache_path']."/compilejsx_hashes.json", 'w');
		fwrite($file, json_encode($hashes));
		fclose($file);
		echo $arr['cache_path']."/compilejsx_hashes.json created\n";
	}	
?>