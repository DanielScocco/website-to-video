<?php	
	
	//get parameters
	$audioNumber = $argv[1];
	//voice narration
	if($audioNumber==99){
		$shortestFlag = "";
		$audioPath = "audio.mp3";
	}
	//background music
	else{
		$shortestFlag = "-shortest";
		$audioPath = "audio-sources/$audioNumber.mp3";
	}	

	$string = file_get_contents("lengths.txt");
	$lengths = explode(",",$string);

	$command = "ffmpeg -i background.mp4";
	for($i=0;$i<count($lengths);$i++){
		$command .= " -i $i.png";
	}
	$command .= " -i $audioPath -filter_complex \"";
	$start_time = 1;
	for($i=0;$i<count($lengths);$i++){
		$j = $i+1;
		$end_time = $start_time + $lengths[$i];
		if($i>0)
			$command .= ";[v";
		else
			$command .= "[";

		$command .= "$i][$j]overlay=0:0:enable='between(t,$start_time,$end_time)'[v$j]";
		$start_time = $end_time;
	}
	$audioNumber = $j + 1;
	$command .= "\" -map '[v$j]' -map $audioNumber:a -pix_fmt yuv420p -s 1280x720 -c:a aac -strict -2 $shortestFlag ../output.mp4";

	//echo $command;
	exec($command);

?>