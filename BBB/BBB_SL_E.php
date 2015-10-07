<script>
	function toggleFullScreen(elem) {
	
		if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
			console.log("Setting to fullscreen mode");
			elem.style.width = '100%';
			elem.style.height = '100%';
			if (elem.requestFullScreen) {
				elem.requestFullScreen();
			} else if (elem.mozRequestFullScreen) {
				elem.mozRequestFullScreen();
			} else if (elem.webkitRequestFullScreen) {
				elem.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			} else if (elem.msRequestFullscreen) {
				elem.msRequestFullscreen();
			}
			else
			{
				alert("wtf");
			}
			
			switchLayout('StreamLine Lecture');
		} else {		
			console.log("Setting to normal mode");
			elem.style.width = '75%';
			
			var windowHeight = window.innerHeight;
			var navHeight = $('.navbar').height()
			containerHeight = windowHeight - navHeight;
	
			elem.style.height = containerHeight + 'px';
			
			if (document.cancelFullScreen) {
				document.cancelFullScreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	}
	
	$(document).keyup(function(e) {
		 if (e.keyCode == 27) { // escape key maps to keycode `27`
			// <DO YOUR WORK HERE>
			var elem = document.getElementById("middleContainer");
			elem.style.width = '75%';
			elem.style.height = '650px';
			switchLayout('StreamLine')
		}
	});
	
	
	$('.leave_button').click(function() {
	
		//Add logout/leave javascript here
		// Runs function created by Matt which ends the meeting and redirects to view.php
		//look in endmeeting.php for more info on what this function does
		if(meetingEnded == false) {
			var x;
			if (confirm("Are you sure you want to end the meeting?") == true) {
				exitMeeting();
			}
		
		}
		else{
			alert("The session has already ended.");
			
		}
	});
	$('.quiz_button').click(function() {
		//Add quiz javascript here
	});
	$('.fullscreen_button').click(function() {
		toggleFullScreen(document.getElementById('middleContainer'));
		
		
	});
	
	
	

	
  </script>
  
