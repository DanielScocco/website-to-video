<?php

	$folder = $argv[1];
	$logo_source = "../".$folder."/logo.png";

	//define font
	$font = "fonts/Roboto-Bold.ttf";

	$string = file_get_contents("../script.txt");
	$sentences = explode("\n",$string);

	for($i=0;$i<count($sentences);$i++){
		$parts = explode(";",$sentences[$i]);
		$color = $parts[0];
		$alignment = $parts[1];
		$text = $parts[3];
		$sentence_length = strlen($text);

		//creat logo image
		if($alignment=='ll'){
			createLogo($logo_source,$i);
			continue;
		}		

		//define font size, margins, etc
		if($sentence_length<200){
			$short = true;
			$font_size = 50;
			$limit = 52; //max chars per line	
			$line_height = 120;		
			$margin_boost = 7; //used to adjust the top margin
			$internal_margin_dif = 22; //used to reduce the size of internal lines. Increase this to flatten lines
		}
		else if($sentence_length<250){
			$font_size = 47;
			$limit = 55; //max chars per line	
			$line_height = 98;	
			$margin_boost = 5;	
			$internal_margin_dif = 18;
		}
		else if($sentence_length<300){
			$font_size = 44;
			$limit = 60; //max chars per line	
			$line_height = 88;	
			$margin_boost = 0;
			$internal_margin_dif = 14;
		}
		else if($sentence_length<350){
			$font_size = 41;
			$limit = 65; //max chars per line	
			$line_height = 84;	
			$margin_boost = 0;
			$internal_margin_dif = 14;
		}
		else{
			$font_size = 38;
			$limit = 70; //max chars per line	
			$line_height = 82;	
			$margin_boost = 0;
			$internal_margin_dif = 14;
		}
		

		//define lines			
		$lines = [""];
		$words = explode(" ",$text);
		$currentLine = 0;
		for($j=0;$j<count($words);$j++){		
			//word fits on current line
			if((strlen($lines[$currentLine])+strlen($words[$j]))<$limit){
				$lines[$currentLine] .= $words[$j]." ";
			}
			else{
				$currentLine++;
				$lines[$currentLine] = $words[$j]." ";
			}
		}
		$n_lines = count($lines);		
				
		//load background
		$im  = imagecreatefrompng("image-sources/background.png");
		imagesavealpha($im, true);

		//load mask
		$mask  = imagecreatefrompng("image-sources/dark-mask.png");
		imagesavealpha($mask, true);

		//font color
		if($color=='white'){
			$fontColor = imagecolorallocate($im, 255, 255, 255);
		}
		else if($color=='black'){
			$fontColor = imagecolorallocate($im, 0, 0, 0);
		}

		//define width, finding the longest line
		$longest_width = 0;
		for($l=0;$l<$n_lines;$l++){
			$line_width = estimateLength($lines[$l],$font,$font_size);
			if($line_width>$longest_width)
				$longest_width = $line_width;
		}		
		if($longest_width > 1750)
			$width = 1750;
		else
			$width = $longest_width;	

		//define height
		$height = $n_lines * $line_height;		
		$margin_left = (1920 - $width)/2;
		$margin_top = (1080 - $height)/2;
		
		//overlay mask on background
		imagecopy($im, $mask, $margin_left, $margin_top, 0, 0, $width, $height);
		
		//define margins
		$margin_left += 45;
		$margin_top += 80;		
		$margin_top += ($n_lines-1) * $margin_boost;
		
		for($j=0;$j<count($lines);$j++){	
			imagettftext ($im, $font_size ,0 , $margin_left , $margin_top + $j * ($line_height-$internal_margin_dif), $fontColor , $font , $lines[$j]);
		}	

		//output image
		header('Content-Type: image/png');
		imagepng($im,"$i.png");
		//destroy image on memory
		imagedestroy($im);
		imagedestroy($mask);
		
	}

	function createLogo($logo_source,$id){
		 // Create image instances
	    $im = imagecreatefrompng('image-sources/dark-mask.png');
	    $logo = imagecreatefrompng($logo_source);

	    //get dimensions
	    $width = imagesx($logo);
	    $height = imagesy($logo);

	    $x = (1920 - $width)/2;
	    $y = (1080 - $height)/2;

	    //alphe channel
		imagesavealpha($im, true);
		imagesavealpha($logo, true);

	    // Copy and merge
	    //imagecopymerge($im, $logo, 0, 0, 0, 0, 573, 331, 100);
		imagecopy($im, $logo, $x, $y, 0, 0, $width, $height);

	    // Output and free from memory
	    header('Content-Type: image/png');
	    imagepng($im,"$id.png");
	    imagedestroy($im);
	}

	function estimateLength($text,$font,$font_size){
		$width = 0;

		$result = imagettfbbox($font_size, 0, $font, $text);

		return ($result[2] - $result[0] + 90);
		
	}

?>
