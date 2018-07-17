<?php

	$lang = $argv[1];
	
	$file = fopen("lengths.txt","w");
	$string = file_get_contents("../script.txt");
	$sentences = explode("\n",$string);

	//minimum length = 1 second intro
	$length = 1;	
	$length_string = "";

	for($i=0;$i<count($sentences);$i++){
		$parts = explode(";",$sentences[$i]);
		$sentence = $parts[3];	
		$pause = $parts[2];

		if($lang=='en'){
			$interval = calcIntervalEnglish($sentence);
		}
		else if($lang=='pt'){
			$interval = calcIntervalPortuguese($sentence);
		}	
		$interval += intval($pause);

		$length += $interval;
		if($i>0)
			$length_string .= ",";
		$length_string .= $interval;
	}

	fwrite($file,$length_string);
	fclose($file);

	//copy for debugging
	//exec("cp lengths.txt lengths-original.txt");

	echo round($length,0);
	return 0;

	function calcIntervalEnglish($sentence){
		$length = strlen($sentence);
		$interval = $length * 0.05;

		$periods = countPeriods($sentence);
		$colonsSemi = countColonsSemi($sentence);
		$commas = countCommas($sentence);
		$otherPunctuation = countPunctuation($sentence);
		$espaces = countEspaces($sentence);
		$digits = preg_match_all("/[0-9]/",$sentence);

		$interval += $colonsSemi * 0.5;
		$interval += $digits * 0.4;
		$interval += $periods * 0.3;
		$interval += $commas * 0.4;
		$interval += $otherPunctuation * 0.2;
		$interval += $espaces * 0.03;

		

		return round($interval,1);
	}


	function calcIntervalPortuguese($sentence){
		$length = strlen($sentence);
		$interval = $length * 0.06;

		$colonsSemi = countColonsSemi($sentence);
		$periods = countPeriods($sentence);
		$commas = countCommas($sentence);
		$otherPunctuation = countPunctuation($sentence);
		$espaces = countEspaces($sentence);
		$digits = preg_match_all("/[0-9]/",$sentence);

		$interval += $colonsSemi * 0.5;
		$interval += $digits * 0.3;
		$interval += $periods * 0.3;
		$interval += $commas * 0.4;
		$interval += $otherPunctuation * 0.2;
		$interval += $espaces * 0.03;

		return round($interval,1);
	}

	function countEspaces($sentence){
		$count = 0;
		for($i=0;$i<strlen($sentence);$i++){
			if($sentence[$i]==' ')
				$count++;
		}
		return $count;
	}
	function countPeriods($sentence){
		$count = 0;
		for($i=0;$i<strlen($sentence);$i++){
			if($sentence[$i]=='.')
				$count++;
		}
		return $count;
	}
	function countCommas($sentence){
		$count = 0;
		for($i=0;$i<strlen($sentence);$i++){
			if($sentence[$i]==',')
				$count++;
		}
		return $count;
	}
	function countPunctuation($sentence){
		$count = 0;
		for($i=0;$i<strlen($sentence);$i++){
			if($sentence[$i]=='!'||$sentence[$i]=='?')
				$count++;
		}
		return $count;
	}
	function countColonsSemi($sentence){
		$count = 0;
		for($i=0;$i<strlen($sentence);$i++){
			if($sentence[$i]==':'||$sentence[$i]==';')
				$count++;
		}
		return $count;
	}

?>