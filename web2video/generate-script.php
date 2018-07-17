<?php	

	$url = $argv[1];
	
	$file = fopen("../script.txt","w");
	$site = file_get_contents($url);	

	//get title
	$pos1 = strpos($site,"<h1") + 18;
	$pos2 =  strpos($site,"</h1>");
	$title = substr($site,$pos1,$pos2-$pos1);
	$title = sanit($title);
	fwrite($file,"white;cm;2;".$title."\n");

	//get and write paragraphs
	$site = substr($site,$pos2);
	$pos1 = strpos($site,"<p>");
	$pos2 = strpos($site,"<div style=\"margin-top:10px");
	$content = substr($site,$pos1,$pos2-$pos1);

	//break in <p> or <li>
	$parts = preg_split("/<\/p>|<\/li>/",$content,-1);
	$parts = array_slice($parts,0,count($parts)-1);

	//sanitize
	for($i=0;$i<count($parts);$i++){
		$parts[$i] = sanit($parts[$i]);
	}

	//apply max length of 400 chars per paragraph, break if necessary
	$final = [];
	$k = 0;
	for($i=0;$i<count($parts);$i++){	
		//discard empty paragraphs
		if(strlen($parts[$i])<2)	
			continue;
		if(strlen($parts[$i])>400){
			$sentences = splitSentences($parts[$i]);			
			//keep growing paragraph until it reachs limit, then start a new one
			$extra_arr = [];
			$index = 0;
			for($j=0;$j<count($sentences);$j++){
				if($j==0)
					$extra_arr[0] = "";
				//current sentence fits on current paragraph, so add it
				if(strlen($extra_arr[$index])+strlen($sentences[$j])+1<400){
					$extra_arr[$index] .= $sentences[$j];
				}
				//we need a new paragraph
				else{
					$index++;
					$extra_arr[$index] = $sentences[$j];
				}
			}			
			//add new paragraphs to final array
			for($j=0;$j<count($extra_arr);$j++){
				$final[$k++] = $extra_arr[$j];
			}
		}
		else{
			$final[$k++] = $parts[$i];
		}
	}

	//base line
	$base = "white;cm;1;";

	//write to file, limit at 4000 chars
	$chars = 0;
	for($i=0;$i<count($final);$i++){
		if($i>0)
			fwrite($file,"\n");
		fwrite($file,$base.trim($final[$i]));

		//check for limit
		$chars += strlen($final[$i]);
		if($chars>4000)
			break;
	}

	fwrite($file,"\nwhite;ll;3;Leia este e outros artigos no blog do androide ponto com");

	fclose($file);

	function sanit($content){
		//substitute breakline with period
		$content = str_replace("<br />",".",$content);
		//strip html tags
		$content = strip_tags($content);
		//decode html entitites
		$content = html_entity_decode($content);
		//remove newlines
		$content = str_replace("\n"," ",$content);
		//remove written URLs like http://google.com
		$content = preg_replace("/http.+?(\)| )/", "", $content);
		//remove repeat punctuation
		$content = preg_replace("/\?+/", "?", $content);
		$content = preg_replace("/\!+/", "!", $content);

		return $content;
	}

	function splitSentences($sentence){
		$var = [];
		$parts = preg_split("/(\. |! |\? )/",$sentence,-1,PREG_SPLIT_DELIM_CAPTURE);		
	
		for($i=0,$k=0;$i<count($parts)/2;$i++,$k+=2){
			if($parts[$k]==""||$parts[$k]==" ")
				continue;
			if($k+1<count($parts))
				$var[$i] = $parts[$k].$parts[$k+1];
			else
				$var[$i] = $parts[$k];
		}
		return $var;
	}

?>