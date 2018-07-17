<?php	

	$url = $argv[1];
	
	$file = fopen("../upload-details.txt","w");
	$site = file_get_contents($url);

	//get date
	$pos1 = strpos($site,"Postado por Red");
	$pos2 = strpos($site,"&",$pos1+41);
	$date = substr($site,$pos1 + 41, $pos2-$pos1);
	$date_array = explode(" ",$date);
	$date = $date_array[0] . " " .$date_array[1] . " " . $date_array[2];

	//get title
	$pos1 = strpos($site,"<h1") + 18;
	$pos2 =  strpos($site,"</h1>");
	$title = substr($site,$pos1,$pos2-$pos1);
	$title = sanit($title);
	fwrite($file,$title."+++");

	//get description
	$site = substr($site,$pos2);
	$pos1 = strpos($site,"<p>");
	$pos2 = strpos($site,"</p>");
	$desc_para = substr($site,$pos1+3,$pos2-$pos1-3);

	//create string
	$desc = sanit("Artigo publicado em: " . $date);
	$desc .= "\r\n\r\n";
	$desc .= sanit($desc_para);

	$desc .= "\r\n\r\n";
	$desc .= "Nosso site: http://wwww.blogdoandroid.com";
	$desc .= "\r\n";
	$desc .= "Artigo original:".$url;
	$desc .= "\r\n\r\n";
	$desc .= "Vídeo criado com: https://www.videoshout.com";

	fwrite($file,$desc."+++");

	$tags = explode(" ",$title);

	for($i=0;$i<count($tags);$i++){
		if(strlen($tags[$i])>3){
			if($i>0)
				fwrite($file,",");
			fwrite($file,$tags[$i]);
		}
	}	

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
	

?>