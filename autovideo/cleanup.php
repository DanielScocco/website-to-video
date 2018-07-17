<?php	
	
	//delete background
	exec("rm background.mp4");

	//delete text images
	$string = file_get_contents("lengths.txt");
	$images = explode(",",$string);
	for($i=0;$i<count($images);$i++)
		exec("rm $i.png");

	//delete lengths file
	exec("rm lengths.txt");

	//delete voice narration, if it exsits
	exec("rm audio.mp3");
	//delete audio script, if it exists
	exec("rm audio-script.txt");
	//delete voice file if it exists
	exec("rm voice.mp3");


?>