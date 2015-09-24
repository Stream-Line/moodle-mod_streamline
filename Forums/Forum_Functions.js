var prefix = ""; // variable to hold whether post is new or a reply
var input, btn_new, cid, uid; // global variables for reference

var postIndex = 0; // temp variable to hold number of posts
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
	var sid = post.substr(post.lastIndexOf("+")+1);
	// send an ajax request for users fullname, then format the post
	getUser(sid, "fullname", function(name){
		formatPost(post, post.substr(post.lastIndexOf("+")+1), name);
	});
});

/*
    Gets triggered when page is loaded and loads in the forum history.
*/
socket.on('loadedF', function(history){
	for(var post in history){
		var sid = post.substr(post.lastIndexOf("+")+1);
		// send an ajax request for users fullname, then format the post
		getUser(sid, "fullname", function(name){
			formatPost(post, post.substr(post.lastIndexOf("+")+1), name);
		});
	}
});

/*
===============================================
		Javascript formatting functions
===============================================
*/

/*
	Initialises global variables for ease of use. Course ID and User ID are
	passed as parameters as they are required to be encoded from php.
*/
function init(courseid, userid){
	this.cid = courseid;
	this.uid = userid;
	this.input = document.getElementById("forum_input");
	this.btn_new = document.getElementById("btn_new_post");
	input.style.display = "none";
	$("#btn-send").onclick("PostF()");
}

/*
	Called when the user clicks New Post. Changes the location of the text input
	and Send button to the top of the forum, hides the New Post button and sets
	the prefix of the users message to new rather than a reply.
*/
function NewPost(){
	// set displays
	input.style.display = "block";
	btn_new.style.display = "none";
	
	$("#forum_parent").prepend(input); // append to top of forum
	
	prefix = "n|"; // set prefix to new post
}

/*
	Called when the user clicks Reply. Pid is the id of the post that the user
	is replying. Changes the location of the text input and Send button to bellow
	post pid and sets the prefix to pid.
*/
function Reply(pid){
	// set displays
	input.style.display = "block";
	//btn_new.style.display = "none";
	
	//console.log($("#" + pid).children);
	//$("#" + pid).children[1].insertAdjacentElement("afterEnd", input);
	//parent.insertBefore(input, parent.children[2]); // append chat to div of message being replied
	$("#" + pid).append(input);
	
	prefix = pid + "|"; // set prefix to post being replied
}

/*
	Called when the user clicks send. Sends the formatted post to the server
	and clears the text input. It also hides the 
*/
function PostF(){
	// !! stuval is going to have to change to student id
	//$socket.emit('Forum', $('#ForumSend').val(), cid, stuval, cid.toString());
	var post = prefix + $('#ForumSend').val() + "|p" + postIndex + "+" + uid;
	
	var sid = post.substr(post.lastIndexOf("+")+1);
	// send an ajax request for users fullname, then format the post
	getUser(sid, "fullname", function(name){
		formatPost(post, sid, name);
	});
	
	$('#ForumSend').val(""); // clear input field
	//postIndex++; // increment postIndex (temp line)
	
	// reset displays
	input.style.display = "none";
	btn_new.style.display = "block";
}

/* */
function getUser(sid, param, callback){
	$.get("getUser.php", {"id" : cid, "uid" : sid, "param" : param}, callback);
}

/* 	Takes in the post message and appends it to the page based on post type and id 
	New post format: "n|test question", returns "n|test question|npid+userid"
	Reply format: "r|test reply|pid", returns "pid|test reply|npid+userid"
*/
function formatPost(post, sid, name){
    var div = Post(
    	post.substring(post.lastIndexOf("|")+1, post.lastIndexOf("+")), // new post id
    	post.substring(post.indexOf("|")+1, post.lastIndexOf("|")), // message
    	sid, name // user who owns the post
    );
    
    if(post[0] == "n"){ // new post
        $('#forum-area').prepend(div);
    } else { // reply post
        $('#'+post.substring(0, post.indexOf("|"))).append(div);
        div.style.marginLeft = "5%";
    }
}

/*
	Creates a post with id pid and utilises newUserDP, newPostContents and 
	newReplyButton. Returns a new fully formatted and filled div.
*/
function Post(pid, message, sid, name){
	var div = document.createElement("div");
    div.id = pid;
    div.classList.add("forum-post");
    
	div.appendChild(newUserDP(sid)); // append display picture of user
	div.appendChild(newPostContents(name, message)); // append the contents of the post 
	div.appendChild(newReplyButton(pid)); // append reply button
    
    return div;
}

/*	
	Creates a formatted div that contains the users display picture. It is
	formatted to sit to the left of a post-contents div.
*/
function newUserDP(sid){
	var div = document.createElement("div"); // main div
	div.classList.add("post-dp");
	
	// send an ajax request for users display picture, then add it to div
	getUser(sid, "displaypicture", function(code){
		div.innerHTML = code;
	});
	
	return div;
}

/*
	Creates a formatted div that contains the username and message.
	It is formatted to sit next to a post-dp div. 
*/
function newPostContents(username, message){
	var div = document.createElement("div"); // main div
	div.classList.add("post-contents");
	
	var udiv = document.createElement("div"); // username div
	udiv.classList.add("post-username");
	udiv.textContent = username;
	div.appendChild(udiv);
	
	var mdiv = document.createElement("div"); // message div
	mdiv.textContent = message;
	div.appendChild(mdiv);
	
	return div;
}

/* 
	Creates a formatted <button> that call Reply() with the provided pid.
*/
function newReplyButton(pid){
	var reply_btn = document.createElement("button");
	reply_btn.classList.add("btn-blue");
    reply_btn.classList.add("btn-reply");
    reply_btn.textContent = "Reply";
    reply_btn.onclick = function() { Reply(pid); };
    
    return reply_btn;
}

/* ideal forum post div
    <div id="p001" style="overflow: auto; clear: right;">
		<div style="float: left; width: auto; ">
			<img src="https://v.dreamwidth.org/97845/324" width="50px" height="50px" />
		</div>
		<div style="margin-left: 50px;">
			<div>
				<a href="user/profile.html">Username</a>
				<p style="font-size: 0.8em; color: grey;">12:55 21/08/15</p>
			</div>
			<div>This is a test message</div>
		</div>
		<button class="btn-blue" style="width: inherit; float: right;" onclick="Reply(p001)">Reply</button>
	</div>
*/