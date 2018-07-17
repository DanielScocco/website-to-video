<?php	
	
	//get parameters
	$category = $argv[1];
	$length = $argv[2];
	$include_generic = $argv[3];

	echo "category=$category\n";
	echo "length=$length\n";

	//check if length is ok
	if($length>300)
		return 0;

	//create array of videos of this category id=>length
	$vid_array = [];
	$file = fopen("videos-db.txt","r");
	while (($line = fgets($file)) !== false){
		$fields = explode(";",$line);
		$categories = explode(",",$fields[1]);
		if(in_array($category,$categories)){
			$vid_array[$fields[0]] = $fields[2];
		}
		//check if generic videos should be included
		if($include_generic==1){
			if(in_array("generic",$categories)){
				$vid_array[$fields[0]] = $fields[2];
			}
		}
	}	
	
	//define number of video segments
	$n_segments = 0;
	if($length<21)
		$n_segments = 1;
	else if($length<31)
		$n_segments = 2;
	else if($length<61)
		$n_segments = 3;
	else if($length<121)
		$n_segments = 4;
	else if($length<181)
		$n_segments = 5;
	else if($length<241)
		$n_segments = 6;
	else
		$n_segments = 7;

	//define average segment length
	$average = intval($length / $n_segments);

	//select all pieces that are longer than average
	$candidates = [];
	$k = 0;
	foreach($vid_array as $key => $value){
		if($value>=$average)
			$candidates[$k++] = $key;	
	}	

	//randomly choose videos
	$selected = [];
	for($i=0;$i<$n_segments;$i++){		
		while(true){
			$pick = rand(0,count($candidates)-1);
			if(in_array($candidates[$pick], $selected)){
				continue;
			}
			else{
				break;
			}
		}		
		$selected[$i] = $candidates[$pick];
	}
	echo "Selected:\n";
	print_r($selected);

	//cut videos to average length
	for($i=0;$i<$n_segments;$i++){
		$video = $selected[$i].".mp4";
		if($average==60)
			$time = '00:01:00.0';
		else{
			$seconds = sprintf('%02d', $average);
			$time = "00:00:$seconds.0";
		}
		exec("ffmpeg -i video-sources/$video -c copy -t $time $i.mp4");
	}

	//create list.txt
	$file = fopen("list.txt","w");
	for($i=0;$i<$n_segments;$i++){
		fwrite($file,"file '$i.mp4'\n");
	}
	fclose($file);

	//concatenate video files
	exec("ffmpeg -f concat -i list.txt -c copy background.mp4");

	//delete auxiliary files
	exec("rm list.txt");
	for($i=0;$i<$n_segments;$i++){
		exec("rm $i.mp4");
	}
	
?>