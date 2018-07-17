<?php

	$dif = $argv[1];

	$string = file_get_contents("lengths.txt");
	$lengths = explode(",",$string);
	
	$file = fopen("lengths.txt","w");
	
	$increment = $dif / count($lengths);

	$string = "";
	for($i=0;$i<count($lengths);$i++){
		if($i>0)
			$string .= ",";
		$string .= round(floatval($lengths[$i])+$increment,1);
	}

	fwrite($file,$string);
	fclose($file);	

	//copy for debugging
	//exec("cp lengths.txt lengths-fixed.txt");

?>