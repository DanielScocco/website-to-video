#!/bin/bash

#make this the current dir
cd $(dirname $0)

# parameters to call this script
# $1=category
# $2=audio number 1,2,3  99=narration
# $3=background music number. Only used if audio choice = 99. 0 means no background music
# $4=language pt=portuguese en=english used for narration  
# $5=folder -> used to grab logo.png

# make sure all parameters are present
if [ "$#" -ne 5 ]
then
	echo Please provide 5 parameters to run the script correctly.
	exit 1
fi

#generate text image overlays
php generate-images.php $5
echo Generated text images

#if voice, generate, else proceed
if [ $2 -eq 99 ]
then 
	echo generating voice
	#generate audio script
	php generate-polly-script.php $4
	#get voice from amazon
	node aws/polly-nolimit.js $4
	#no background music, just change name of file
	if [ $3 -eq 0 ]
	then
		mv voice.mp3 audio.mp3
	#add background music
	else	
		ffmpeg -i voice.mp3 -i audio-sources/$3.mp3 -filter_complex "[0:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo,volume=3.0[a1]; [1:a]aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo,volume=0.4[a2]; [a1][a2]amerge,pan=stereo|c0<c0+c2|c1<c1+c3[out]" -map "[out]" -c:a libmp3lame -q:a 0 -shortest audio.mp3
	fi	
else
	echo proceeding with music only
fi

#get length of audio file if used narration
if [ $2 -eq 99 ]
then 
	length=$(mp3info -p "%S" audio.mp3)
	length2=$(php estimate-length.php $4)
	let "dif = $length - $length2"
	#check if audio file is different than estimate. If so, fix estimates
	if [ $dif -gt 1 ] || [ $dif -lt -1 ]
	then
		echo --- Fixing lengths, audio file is larger 
		php fixEstimate.php $dif
	fi
#else estimate length
else
	length=$(php estimate-length.php $4)
fi

#create background video with correct length
php create-video.php $1 $length 1
echo Created background video

#overlay images and audio on video
php overlay-images-audio.php $2
echo Done overlaying images amd audio on video

php cleanup.php
echo Done cleaning up.

echo ---------- All set! ------------

# : <<'END'
# END
