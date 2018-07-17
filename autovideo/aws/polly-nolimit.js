/*works with any length of input, as it breaks it down in chunks and uses promises to sync everything*/

var lang = process.argv[2];

var fs = require('fs');

//read audio text
var path = process.cwd();
var buffer = fs.readFileSync(path + "/audio-script.txt");
var audioText = buffer.toString();

//split in chunks
var chunks = audioText.split('+++');

//combine if possible
for(var i=0;i<chunks.length-1;i++){
	//if possible, combine two chunks
	while(chunks[i+1]!=""&&chunks[i].length+chunks[i+1].length<1450){
		chunks[i] = chunks[i] + chunks[i+1];
		//move all other chunks left
		for(var j=i+1;j<chunks.length-1;j++){
			chunks[j] = chunks[j+1];
		}
		chunks[chunks.length-1] = "";
	}
}

//add <speak> tags and cut array only to existing text
for(var i=0;i<chunks.length;i++){
	if(chunks[i]=="")
		break;
	else{
		if(i==0)
			chunks[i] = "<speak><break time=\"1s\"/>" + chunks[i] + "</speak>";
		else	
			chunks[i] = "<speak>" + chunks[i] + "</speak>";
	}
}
chunks = chunks.slice(0,i);

//console.log(chunks);

// Load the SDK
const AWS = require('aws-sdk')

// Create an Polly client
const Polly = new AWS.Polly({
    signatureVersion: 'v4',
    region: 'us-east-1'
})

var bufferArray = [];
//default voice
var voice = "Joanna";
if(lang=='pt'){
	voice = "Vitoria";
}

let params = {
    'Text': "",
    'OutputFormat': 'mp3',
    'VoiceId': voice,
    'TextType':'ssml'
}

var bufferArray = [];
var calls = 0;
getAudios();

function getAudios(){	
	var promise = makeCall(params,chunks,calls);
	console.log("-----------call -> "+calls);
	promise.then(function(success){
		bufferArray.push(success);
		//still chunks to get audio for
		if(calls<chunks.length-1){
			calls++;
			getAudios();
		}
		//processed all audio chunks, combine and exit
		else{
			var bufferLength = 0;
			for(var i=0;i<bufferArray.length;i++){
				bufferLength += bufferArray[i].length;
			}
			var buf = Buffer.concat(bufferArray,bufferLength);
			fs.writeFile("./voice.mp3", buf, function(err) {
			    if (err) {
			        return console.log(err);
			    }
			    console.log("-----------The file was saved!");		    
			});
		}
	}).catch(function(error){
		console.log(error);
	});
}


function makeCall(params,chunks,calls){
	return new Promise(function(resolve,reject){
		params.Text = chunks[calls];		
		Polly.synthesizeSpeech(params, (err, data) => {
		    if (err) {		       
		        console.log("Polly  call error:"+err)
		        reject(err.code);
		    }
		    else{
		    	resolve(data.AudioStream);
		    }
		});
	});
}

