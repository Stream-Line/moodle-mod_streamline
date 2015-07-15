/* Variables */

var webrtc = null;
var ids = null;
var user = null;
var course = null;
var started = false;

var localVideo = document.getElementById("localVideo");
var remoteVideo = document.getElementById("remoteVideo");
var startButton = document.getElementById("startButton");
var stopButton = document.getElementById("stopButton");

/* Functions */

function toggleButtons(){
  if(ids.indexOf(user.id) == -1){
    startButton.style.display = "none";
    stopButton.style.display = "none";
    localVideo.style.display = "none";
  } else {
    startButton.style.display = (started) ? "none" : "block";
    stopButton.style.display = (started) ? "block" : "none";
  }
}

// create new SimpleWebRTC object and connect to course room
function connectToCourseLiveStream(coursename){
  webrtc = new SimpleWebRTC({
    localVideoEl: 'localVideo',
    remoteVideosEl:'remoteVideo',
    autoRequestMedia: false,
    localVideo: {
      autoplay: true,
      mirror: false,
      muted: true
    }
  });
  webrtc.joinRoom("streamlineRTC".concat(coursename));
}

function startLiveStream(){
  // start local media access
  webrtc.startLocalVideo();
  started = true;
  toggleButtons();
}

function stopLiveStream(){
  // if in a room, leave
  if(webrtc != null && webrtc.roomName != null){
      webrtc.leaveRoom();
      // stop local media access
      webrtc.stopLocalVideo(); 
  }
  started = false;
  toggleButtons();
}