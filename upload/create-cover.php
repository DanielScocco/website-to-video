<?php

	$category = $argv[1];

	//get title
	$details = file_get_contents("../upload-details.txt");
	$parts = explode("+++",$details);
	$title = $parts[0];

	//create image
	$id = rand(1,33);
	$im  = imagecreatefrompng("image-sources/$category-$id.png");
	
	//set background colors
	$white = imagecolorallocate($im, 255, 255, 255);

	//write title
	$words = explode(" ",$title);
	$length = strlen($title);
	if($length<20){
		$font = 180;
		$height = 240;
		$limit = 17;
	}
	else if($length<40){
		$font = 160;
		$height = 220;
		$limit = 18;
	}
	else if($length<60){
		$font = 140;
		$height = 190;
		$limit = 20;
	}
	else if($length<80){
		$font = 130;
		$height = 180;
		$limit = 22;
	}
	else{
		$font = 120;
		$height = 170;
		$limit = 24;
	}

	
	$lines = [""];
	$currentLine = 0;

	for($i=0;$i<count($words);$i++){
		//word fits on current line
		if((strlen($lines[$currentLine])+strlen($words[$i]))<$limit){
			$lines[$currentLine] .= $words[$i]." ";
		}
		else{
			$currentLine++;
			$lines[$currentLine] = $words[$i]." ";
		}
	}

	//write lines
	for($i=0;$i<count($lines);$i++){
		if($lines[$i]!=''){
			imagettftext ($im, $font ,0 , 35 , $height+($height * $i) , $white , "fonts/Roboto-Bold.ttf" , $lines[$i]);
		}
	}

	//output image
	imagepng($im,"thumb.png");

?>