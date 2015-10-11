/*
==================================
		Socket.io handlers
==================================
*/

/* global socket */

/*
    Gets triggered when someone posts to the forum. 
    Updates the page without having to refresh.
*/
socket.on('forumback', function(post){
	loadHistory([post]);
});

/*
    Gets triggered when page is loaded and loads in the forum history.
*/
socket.on('loadedF', loadHistory);

/*
===============================================
		Javascript formatting functions
===============================================
*/

/* global getUser, HyperLinks */
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
	getUser(cid, uid, "displaypicture", function(code){
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
	Extracts the data from the raw post message and formats it into an array.
	New post format: "n|test question", returns "n|test question|npid+userid@time"
	Reply format: "pid|test reply", returns "pid|test reply|npid+userid@time"
*/
function extractPostData(post){
	return {
		"pid"	: post.substring(0, post.indexOf("|")), // new post or id of parent post
		"msg"	: post.substring(post.indexOf("|")+1, post.lastIndexOf("|")), // message
		"npid"	: post.substring(post.lastIndexOf("|")+1, post.lastIndexOf("+")), // new post id
		"uid"	: post.substring(post.lastIndexOf("+")+1, post.lastIndexOf("@")), // user who owns the post
		"date"	: post.substr(post.lastIndexOf("@")+1) // time/date of post
	};
}

/*
	Called when the user clicks send. Sends the formatted post to the server
	and clears the text input. It also hides the 
*/
function PostF(message){
	socket.emit('Forum', (prefix + message), cid, huid, cid.toString());
}

/*
	Loads the forum history in order by recursively calling itself, after the 
	ajax requests have returned, until history is empty.
*/
function loadHistory(history){
	if(!history){
		$("#forum-loading").remove();
		$("#forum-no-history").removeClass("hidden");
	} else if(history.length > 0){
		var post = history.shift();
		var data = extractPostData(post);
		getUser(cid, data["uid"], "fullname", function(name){ // send an ajax request for users fullname
			getUser(cid, data["uid"], "displaypicture", function(dp){ // send ajax request for dp in html
				var div = Post(data["npid"], data["msg"], data["uid"], name, dp, data["date"]);
				if(data["pid"] == "n"){ // new post
					$('#forum-area').prepend(div);
				} else { // reply post
					$('#'+data["pid"]).append(div);
					div.classList.add("post-reply");
				}
				loadHistory(history);
			});
		});
	} else {
		$("#forum-loading").remove();
		$("#forum-no-history").remove();
		$("#forum-area").removeClass("hidden");
	}
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
	msg.innerHTML = HyperLinks(message);
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