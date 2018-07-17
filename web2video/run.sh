#!/bin/bash

#make this the current dir
cd $(dirname $0)

# parameters to call this script
# $1=folder to upload success.txt and error.txt when uploading video
# $2=url to get content from 
# $3=category of video
# $4=audio number 1,2,3,4 etc. 99=narration
# $5=background music number. Only used if audio choice = 99. 0 means no background music
# $6=language pt/en/etc used for narration

# make sure all parameters are present
if [ "$#" -ne 6 ]
then
	echo Please provide 6 parameters to run the script correctly.
	exit 1
fi

#generate script from web page
php generate-script.php $2
echo Video script generated

#create video
../autovideo/run.sh $3 $4 $5 $6 $1

#if video was created, proceed
if [ -e ../output.mp4 ]
then
	#generate details for YouTube upload
	php generate-upload-details.php $2
	echo Video upload details generated
	#upload
	../upload/run.sh $1 $2 $3
	echo Upload scripts done
	#cleanup
	rm ../script.txt
	rm ../upload-details.txt
	rm ../output.mp4
fi

echo Webpage2video cleanup done