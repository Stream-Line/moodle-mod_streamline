/*
    A script containing the global functions used by all modules.
    To use them without erros, include a global <func> line commented out
    with / * * /.
*/

/*
	Generic function that takes in uid, desired user info and function to
	callback. Uses an ajax request on the created getUser.php in the main
	streamline directory. This function has been moved to streamline_modules
	as it allows the other modules access without having to rewrite this.
	
	Parameters are:
		cid is the course module id ($cm->id)
		uid is the user id
		param is a string, one of the ones in getUser
		callback is a function
*/
function getUser(cid, uid, param, callback){
	$.get("getUser.php", {"id" : cid, "uid" : uid, "param" : param}, callback);
}

/*
	Takes in a string message possibly containing a URL and returns a string.
	If the string contained a URL the string will have a hyperlink. If no URL is
	detected the string is returned without change.
*/
function HyperLinks(msg){
    var r = msg.split(" ");
	var whiteList = ["http", "www"];
	for(var x in r){
		for(var i in whiteList){
			if(r[x].indexOf(whiteList[i]) != -1){
			    var href = (whiteList[i] == "www") ? "http://"+r[x] : r[x];
                r[x] = "<a href=\"" + href + "\" target=\"_blank\">" + r[x] + "</a>";
				break;
			}
		}
	}
	return r.join(" ");
}