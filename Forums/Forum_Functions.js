/*
==================================
		Socket.io handlers
==================================
*/

/*
    Gets triggered when someone posts to the forum. 
    Updates the page without having to refresh.
*/
socket.on('forumback', function(post){
	formatPost(post);
	/*
	var sid = post.substring(post.lastIndexOf("+")+1, post.lastIndexOf("@"));
	// send an ajax request for users fullname, then format the post
	getUser(sid, "fullname", function(name){
		formatPost(post, sid, name);
	});
	*/
});

/*
    Gets triggered when page is loaded and loads in the forum history.
*/
socket.on('loadedF', function(history){
	for(var i in history){
		formatPost(history[i]);
		/*
		var sid = history[i].substring(history[i].lastIndexOf("+")+1, history[i].lastIndexOf("@"));
		// send an ajax request for users fullname, then format the post
		getUser(sid, "fullname", function(name){
			formatPost(history[i], sid, name);
		});
		*/
	}
});

/*
===============================================
		Javascript formatting functions
===============================================
*/

var prefix = ""; // variable to hold whether post is new or a reply
var cid, huid, uid; // global variables for reference

//var postIndex = 0; // temp variable to hold number of posts

/*
	Initialises global variables for ease of use. Course ID and User ID are
	passed as parameters as they are required to be encoded from php.
*/
function init(courseid, hasheduserid, userid){
	this.cid = courseid;
	this.huid = hasheduserid;
	this.uid = userid;
	setupEventHandlers();
	getUser(uid, "displaypicture", function(code){
		$("#forum-user-dp-main").html(code);
		$("#forum-user-dp-reply").html(code);
	});
}

/*
	Adds the event handlers to the buttons and textareas in Forums.php
*/
function setupEventHandlers(){
	// setup input for new posts
	$("#btn-send-main").click(function(){
		prefix = "n|"; // set prefix to new post
		PostF($("#forum-textarea-main").val());
		$("#forum-input-btns-main").addClass("hidden");
		$("#forum-textarea-main").val("");
	});
	$("#forum-textarea-main").focus(function(){
		var elem = document.getElementById("forum-input-btns-main");
		if(elem.classList.contains("hidden"))
			elem.classList.remove("hidden");
	});
	$("#btn-cancel-main").click(function(){
		$("#forum-input-btns-main").addClass("hidden");
	});
	
	// setup input for replies
	$("#btn-send-reply").click(function(){
		prefix = $(this).parent().parent().parent().attr("id") + "|"; // set prefix to post being replied
		PostF($("#forum-textarea-reply").val());
		$("#forum-input-reply").addClass("hidden");
		$("#forum-textarea-reply").val("");
	});
	$("#btn-cancel-reply").click(function(){
		$("#forum-input-reply").addClass("hidden");
	});
}

/*
	Called when the user clicks Reply. Pid is the id of the post that the user
	is replying. Changes the location of the text input and Send button to bellow
	post pid and sets the prefix to pid.
*/
function Reply(pid){
	$("#forum-input-reply").insertAfter($("#" + pid).children("button")[0]);
	
	var elem = document.getElementById("forum-input-reply");
	if(elem.classList.contains("hidden"))
		elem.classList.remove("hidden"); // show elem if hidden
}

/*
	Called when the user clicks send. Sends the formatted post to the server
	and clears the text input. It also hides the 
*/
function PostF(message){
	socket.emit('Forum', (prefix + message), cid, huid, cid.toString());
}

/*
	Generic function that takes in uid, desired user info and function to
	callback. Uses an ajax request on the created getUser.php in the main
	streamline directory. I will likely move this function to streamline_modules
	as it would allow the other modules access without having to rewrite this.
*/
function getUser(sid, param, callback){
	$.get("getUser.php", {"id" : cid, "uid" : sid, "param" : param}, callback);
}

/* 	
	Takes in the post message and appends it to the page based on post type and id 
	New post format: "n|test question", returns "n|test question|npid+userid@time"
	Reply format: "r|test reply|pid", returns "pid|test reply|npid+userid@time"
*/
function formatPost(post){
	var sid = post.substring(post.lastIndexOf("+")+1, post.lastIndexOf("@"));
	// send an ajax request for users fullname
	getUser(sid, "fullname", function(name){
		getUser(sid, "displaypicture", function(dp){
			var div = Post(
		    	post.substring(post.lastIndexOf("|")+1, post.lastIndexOf("+")), // new post id
		    	post.substring(post.indexOf("|")+1, post.lastIndexOf("|")), // message
		    	sid, name, dp, // user who owns the post
		    	post.substr(post.lastIndexOf("@")+1) // time/date of post
		    );
		    
		    if(post[0] == "n"){ // new post
		        $('#forum-area').prepend(div);
		    } else { // reply post
		        $('#'+post.substring(0, post.indexOf("|"))).append(div);
		        div.classList.add("post-reply");
		    }
		});
	});
}

/*
	Creates a post with id pid and utilises newUserDP, newPostContents and 
	newReplyButton. Returns a new fully formatted and filled div.
*/
function Post(pid, message, sid, name, dp, time){
	var div = document.createElement("div");
    div.id = pid;
    div.classList.add("forum-post");
    
	div.appendChild(newUserDP(dp)); // append display picture of user
	div.appendChild(newPostContents(name, message, time)); // append the contents of the post 
	div.appendChild(newReplyButton(pid)); // append reply button
	
    return div;
}

/*	
	Creates a formatted div that contains the users display picture. It is
	formatted to sit to the left of a post-contents div.
*/
function newUserDP(html){
	var div = document.createElement("div"); // main div
	div.classList.add("post-dp");
	div.innerHTML = html;
	/*
	// send an ajax request for users display picture, then add it to div
	getUser(sid, "displaypicture", function(code){
		div.innerHTML = code;
	});
	*/
	
	return div;
}

/*
	Creates a formatted div that contains the username and message.
	It is formatted to sit next to a post-dp div. 
*/
function newPostContents(name, message, time){
	var div = document.createElement("div"); // main div
	div.classList.add("post-contents");
	
	var hdiv = document.createElement("div"); // header div
	hdiv.classList.add("post-contents-header");
	
	var user = document.createElement("a");
	user.href = ""; // get user profile page using getUser
	user.textContent = name;
	hdiv.appendChild(user);
	
	var date = document.createElement("p");
	date.classList.add("post-date");
	date.textContent = time; // replace with time parameter
	hdiv.appendChild(date);
	
	div.appendChild(hdiv);
	
	var mdiv = document.createElement("div"); // message div
	var msg = document.createElement("p");
	msg.textContent = message;
	mdiv.appendChild(msg);
	div.appendChild(mdiv);
	
	return div;
}

/* 
	Creates a formatted <button> that call Reply() with the provided pid.
*/
function newReplyButton(pid){
	var reply_btn = document.createElement("button");
	reply_btn.classList.add("btn-blue");
    reply_btn.textContent = "Reply";
    reply_btn.onclick = function() { Reply(pid); };
    
    return reply_btn;
}

/*	ideal forum post div
    <div id="p001" class="forum-post">
		<div class="post-dp">
			<img src="https://v.dreamwidth.org/97845/324" />
		</div>
		<div class="post-contents">
			<div id="post-contents-header">
				<a href="user/profile.html">Username</a>
				<p class="post-date">12:55 21/08/15</p>
			</div>
			<div>
			    <p>This is a test message</p>
			</div>
		</div>
		<button class="btn-blue" onclick="Reply(p001)">Reply</button>
	</div>
	
	jwt
	
*/