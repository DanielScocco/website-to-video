#!/bin/bash

#make this the current dir, necessary to locate files and scripts correctly
cd $(dirname $0)

# parameters to call this script
# $1=folder to upload success.txt and error.txt 
# $2=id of video for logging purposes (can be URL in case of webpage -> video) 
# $3=category, used to select cover background image  

# make sure all parameters are present
if [ "$#" -ne 3 ]
then
	echo Please provide 3 parameters to run the script correctly.
	exit 1
fi

node upload.js $1 $2
echo --Upload complete

let "status = $?"

#if successful, upload custom thumbnail
if [ $status -eq 0 ]
then
	php create-cover.php $3
	echo --Created cover image
	node thumbnail.js $1
	echo --Updated video thumbnail
	rm thumb.png
	echo --Deleted temp image
fi

