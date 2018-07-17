<?php

	$lang = $argv[1];

	$string = file_get_contents("../script.txt");
	$sentences = explode("\n",$string);

	$file = fopen("audio-script.txt","w");	
	for($i=0;$i<count($sentences);$i++){
		$parts = explode(";",$sentences[$i]);
		$pause = $parts[2];
		$text = $parts[3];

		//fix pronounciation of known problematic words
		$text = fixPronounciation($text,$lang);

		fwrite($file,$text);		
		fwrite($file,"<break time=\"{$pause}s\"/>");
		//break point for polly script
		fwrite($file,"+++");
		
	}
	fclose($file);

	function fixPronounciation($text,$lang){
		//portuguese
		if($lang=='pt'){
			$text = str_replace("mobile","mobaio",$text);
			$text = str_replace("Mobile","mobaio",$text);
			$text = str_replace("tablet","táblet",$text);
			$text = str_replace("tablets","táblets",$text);
			$text = str_replace("smartphone","smartfone",$text);
			$text = str_replace("smartphones","smartfones",$text);
			$text = str_replace("android","andróide",$text);
			$text = str_replace("Android","andróide",$text);
			$text = str_replace("store","istór",$text);
			$text = str_replace("Store","istór",$text);
			$text = str_replace("app","épi",$text);
			$text = str_replace("google","gúgou",$text);
			$text = str_replace("Google","gúgou",$text);
			$text = str_replace("pay","pei",$text);
			$text = str_replace("Pay","pei",$text);
			$text = str_replace("whatsapp","uatisápi",$text);
			$text = str_replace("Whatsapp","uatisápi",$text);
			$text = str_replace("conexão","conecção",$text);
			$text = str_replace("games","gueimes",$text);			
			$text = preg_replace("/(F|f)riday/","fraidei",$text);
			$text = preg_replace("/(A|a)pple/","épol",$text);
			$text = preg_replace("/(M|m)usic/","míuusic",$text);
			$text = preg_replace("/(X|x)peria/","écspiria",$text);
			$text = preg_replace("/(P|p)ixel/","pícseu",$text);
			$text = preg_replace("/(P|p)ixels/","pícseus",$text);
			$text = preg_replace("/(G|g)alaxy/","gálacsi",$text);
			$text = preg_replace("/(I|i)nstagram/","instagran",$text);
			$text = preg_replace("/ (I|i)(O|o)(S|s) /","aioése",$text);
			$text = preg_replace("/(P|p)lay/","plei",$text);
			$text = preg_replace("/ (N|n)exus /","nécsus",$text);
		}

		return $text;
	}

?>

