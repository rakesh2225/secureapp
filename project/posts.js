{
	function savePost(username) {
		alert("Saving post");
		var post = document.getElementById("newpost").value;
		if (post == "") {
			alert("Please enter your post");
		}
		var data = {
			"username" : username, 
			"post" : post, 
			"date" : new Date()
		};
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				handleSuccessPost(username);
			}
		}
		xhttp.open("POST", "/minibook/addpost.php");
		xhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
		xhttp.send(JSON.stringify(data));
	}

	function handleSuccessPost(username) {
		alert('Posted successfully');
		document.getElementById("newpost").value = "";
	}
	
	function handleFailedPost() {
		alert('Failed to post');
		document.getElementById("newpost").value = "";
	}
}