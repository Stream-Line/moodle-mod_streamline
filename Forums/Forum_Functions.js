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
var cid, huid, uid; // global variables for reference

/*
	Initialises global variables for ease of use. Course ID and User ID are
	passed as parameters as they are required to be encoded from php.
*/
function init(courseid, hasheduserid, userid){
	this.cid = courseid;
	this.huid = hasheduserid;
	this.uid = userid;
	getUser(cid, uid, "displaypicture", function(code){ // get user display pic
		$("#forum-parent").prepend(newForumTextArea("n|", code))
	});
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
	Loads the forum history in order by recursively calling itself, after the 
	ajax requests have returned, until history is empty.
*/
function loadHistory(history){
	if(!history){
		$("#forum-loading").remove();
		$("#forum-no-history").removeClass("forum-hidden");
	} else if(history.length > 0){
		var post = history.shift();
		var data = extractPostData(post);
		getUser(cid, data["uid"], "fullname", function(name){ // send an ajax request for users fullname
			getUser(cid, data["uid"], "displaypicture", function(dp){ // send ajax request for dp in html
				getUser(cid, data["uid"], "profile", function(profile){ // send ajax request for user profile url
					var div = newPost(data["npid"], data["msg"], data["uid"], name, dp, data["date"], profile);
					if(data["pid"] == "n"){ // new post
						$('#forum-area').prepend(div);
					} else { // reply post
						var p = $('#'+data["pid"]);
						var msg = div.getElementsByClassName("post-message")[0];
						var user = p.find(".post-profile")[0].cloneNode(true);
						user.textContent = "+" + user.textContent;
						msg.innerHTML = user.outerHTML + msg.innerHTML;
						div.classList.add("post-reply");
						((p.parent().attr("id") != "forum-area") ? p.parent() : p).append(div);
					}
					loadHistory(history);
				});
			});
		});
	} else {
		$("#forum-loading").remove();
		$("#forum-no-history").remove();
		$("#forum-area").removeClass("forum-hidden");
	}
}

/*
	Called when the user clicks send. Sends the formatted post to the server
	and clears the text input. It also hides the 
*/
function PostF(message){
	socket.emit('Forum', message, cid, huid, cid.toString());
}

/*
	Creates a new text area for the user to type into and sumbit either a
	new forum post, or forum reply. Requires the prefix the post will send and
	the html code for the user dp, from the getUser, then returns a formatted,
	fully handled div DOM element.
*/
function newForumTextArea(prefix, dpCode){
	var div = document.createElement("div"); // main div
	div.classList.add("forum-post");
	
	div.appendChild(newUserDP(dpCode)); // add user dp to main div
	
	var input = document.createElement("div"); // div for textarea
	input.classList.add("forum-input");
	var textarea = document.createElement("textarea");
	input.appendChild(textarea);
	div.appendChild(input); // add textarea input to main div
	
	var buttons = document.createElement("div");
	buttons.classList.add("input-buttons");
	buttons.classList.add("forum-hidden");
	
	var cancel = document.createElement("button");
	cancel.textContent = "Cancel";
	buttons.appendChild(cancel);
	
	var send = document.createElement("button");
	send.textContent = "Send";
	send.classList.add("btn-blue");
	buttons.appendChild(send);
	div.appendChild(buttons); // add buttons to main div
	
	// add event handlers
	textarea.onfocus = function(){
		if(buttons.classList.contains("forum-hidden"))
			buttons.classList.remove("forum-hidden");
	};
	
	var remove = function(){
		if(prefix == "n|")
			buttons.classList.add("forum-hidden");
		else
			div.parentNode.removeChild(div);
	};
	
	cancel.onclick = remove;
	
	send.onclick = function(){
		PostF(prefix + textarea.value);
		textarea.value = "";
		remove();
	};
	
	return div;
}

/*
	Creates a post with id pid and utilises newUserDP, newPostContents and 
	newReplyButton. Returns a new fully formatted and filled div DOM Element.
*/
function newPost(pid, message, sid, name, dp, time, profile){
	var div = document.createElement("div");
	div.id = pid;
	div.classList.add("forum-post");
	
	div.appendChild(newUserDP(dp)); // append display picture of user
	div.appendChild(newPostContents(name, message, time, profile)); // append the contents of the post 
	div.appendChild(newPostOptions(pid)); // append options, such as reply
	
	return div;
}

/*	
	Creates a formatted div DOM Element that contains the users display picture.
	It is formatted to sit to the left of a post-contents div.
*/
function newUserDP(html){
	var div = document.createElement("div"); // main div
	div.classList.add("post-dp");
	div.innerHTML = html;
	
	return div;
}

/*
	Creates a formatted div DOM Element that contains the users name and message.
	It is formatted to sit next to a post-dp div. 
*/
function newPostContents(name, message, time, profile){
	var div = document.createElement("div"); // main div
	div.classList.add("post-contents");
	
	var hdiv = document.createElement("div"); // header div
	hdiv.classList.add("post-contents-header");
	
	var user = document.createElement("a");
	user.classList.add("post-profile");
	user.href = profile;
	user.target = "_blank";
	user.textContent = name;
	hdiv.appendChild(user);
	
	var date = document.createElement("p");
	date.classList.add("post-date");
	date.textContent = time; // replace with time parameter
	hdiv.appendChild(date);
	
	div.appendChild(hdiv);
	
	var mdiv = document.createElement("div"); // message div
	mdiv.classList.add("post-message");
	var msg = document.createElement("p");
	msg.innerHTML = HyperLinks(message);
	mdiv.appendChild(msg);

	div.appendChild(mdiv);
	
	return div;
}

/*
	Creates a formatted div DOM Element that contains options relative to the
	post, such as a reply button. It is formatted to sit next to a post-dp div.
*/
function newPostOptions(pid){
	var div = document.createElement("div"); // main div
	div.classList.add("post-options");
	
	div.appendChild(newReplyButton(pid)); // reply button
	
	return div;
}

/* 
	Creates a formatted button DOM Element that appends a newForumTextArea, used 
	to reply to a post, after itself but before any other element.
*/
function newReplyButton(pid){
	var reply_btn = document.createElement("button");
	reply_btn.classList.add("post-reply-btn");
	reply_btn.textContent = "Reply";
	reply_btn.onclick = function() {
		getUser(cid, uid, "displaypicture", function(code){ // get user display pic
			var input = newForumTextArea(pid+"|", code);
			input.classList.add("post-reply");
			var parent = reply_btn.parentNode;
			parent.parentNode.insertBefore(input, parent.nextSibling);
			input.getElementsByTagName("textarea")[0].focus();
		});
	};
	
	return reply_btn;
}