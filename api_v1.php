<?php
//Error function
function error($status, $msg = '') {
	http_response_code($status);
	header('Content-Type: application/json');
	
	switch ($status) {
		case 200: $text = 'OK'; break;
		case 201: $text = 'Created'; break;
		case 204: $text = 'No Content'; break;
		case 400: $text = 'Bad Request'; break;
		case 401: $text = 'Unauthorized'; break;
		case 403: $text = 'Forbidden'; break;
		case 404: $text = 'Not Found'; break;
		case 405: $text = 'Method Not Allowed'; break;
		case 409: $text = 'Conflict'; break;
		case 500: $text = 'Internal Server Error'; break;
		case 501: $text = 'Not Implemented'; break;
		default: $text = 'Unknown Error'; break;
	}
	
	die('{"status": ' . $status . ',"status_text":"' . $text . '","msg":"' . $msg . '"}');
}

//Get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
//$input = json_decode(file_get_contents('php://input'),true);
if ($request[0] == "") {
	error(400); //Bad request
}

//Connect to the mysql database
include_once "database_creds.php";
$link = mysqli_connect($sql_server, $sql_username, $sql_password, $sql_database);

//Get the action
$action = preg_replace('/[^a-z0-9_]+/i', '', $request[0]);

//If award scheme
if ($action == 'award_scheme') {
	if ($method == 'GET') {
		//Return badge md data
		if (count($request) == 2) {
			//Get the badgeid
			$badgeid = preg_replace('/[^0-9]+/', '', $request[1]);

			//Execute the sql
			$result = mysqli_query($link, "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE id=$badgeid");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error()); } //Not found

			header('Content-Type: application/json');
			for ($i=0; $i < mysqli_num_rows($result); $i++) {
				echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
			}

			mysqli_close($link);
			
		//Return list of badges with names and ids
		} else if (count($request) == 1) {
			//Execute the sql
			$result = mysqli_query($link, "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error()); } //Not found

			header('Content-Type: application/json');
			echo "[";
			for ($i=0; $i < mysqli_num_rows($result); $i++) {
				echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
			}
			echo "]";

			mysqli_close($link);
		} else {
			error(400); //Bad request
		}
	} else {
		error(405); //Method not allowed
	}
//If users
} else if ($action == 'users') {
	//Get the user data
	if ($method == 'GET') {
		//Return user data
		if (count($request) == 2) {
			//Get the user id
			$userid = preg_replace('/[^0-9]+/', '', $request[1]);

			//Execute the sql
			$result = mysqli_query($link, "SELECT id, username, registerDate, realName FROM users WHERE id=$userid");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error()); } //Not found

			header('Content-Type: application/json');
			for ($i=0; $i < mysqli_num_rows($result); $i++) {
				echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
			}

			mysqli_close($link);
			
		//Return list of badges with names and ids
		} else if (count($request) == 1) {
			//Execute the sql
			$result = mysqli_query($link, "SELECT id, username, registerDate, realName FROM users");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error()); } //Not found

			header('Content-Type: application/json');
			echo "[";
			for ($i=0; $i < mysqli_num_rows($result); $i++) {
				echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
			}
			echo "]";

			mysqli_close($link);
		} else {
			error(400); //Bad request
		}
	//Make a new user
	} else if ($method == 'POST') {
		if (count($request) == 2) {
			//Make sure that the second keyword is 'new'
			if (preg_replace('/[^a-z]+/', '', $request[1]) != 'new') {
				error(400);
			}

			//eck
			error(501);

			mysqli_close($link);
			
		} else {
			error(400); //Bad request
		}
	} else {
		error(405); //Method not allowed
	}
} else {
	error(400, 'This action does not exist'); //Bad request
}