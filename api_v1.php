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
		//Return the levels
		if (count($request) == 3) {
			if (preg_replace('/[^a-z]+/i', '', $request[1]) == "sublevels") {
				//Get the sublevelid
				$sublevelid = preg_replace('/[^a-z0-9-]+/i', '', $request[2]);
				
				$sublevelsql = "";
				//Allow sublevel fetching by name
				if (is_numeric($sublevelid)) {
					$sublevelsql = "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE sublevel_id=$sublevelid";
				} else {
					$sublevelid = strtoupper(strtr($sublevelid, "-", " "));
					$sublevelsql = "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE UPPER(sublevel)='$sublevelid'";
				}

				//Execute the sql
				$result = mysqli_query($link, $sublevelsql);
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				echo "[";
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}
				echo "]";

				mysqli_close($link);
			} else if (preg_replace('/[^a-z]+/i', '', $request[1]) == "levels") {
				//Get the levelid
				$levelid = preg_replace('/[^a-z0-9-]+/i', '', $request[2]);

				$levelsql = "";
				//Allow level fetching by name
				if (is_numeric($levelid)) {
					$levelsql = "SELECT DISTINCT sublevel_id, sublevel FROM award_scheme WHERE level_id=$levelid";
				} else {
					$levelid = strtoupper(strtr($levelid, "-", " "));
					$levelsql = "SELECT DISTINCT sublevel_id, sublevel FROM award_scheme WHERE UPPER(level)='$levelid'";
				}

				//Execute the sql
				$result = mysqli_query($link, $levelsql);
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				echo "[";
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}
				echo "]";

				mysqli_close($link);
			} else if (preg_replace('/[^a-z]+/i', '', $request[1]) == "badges") {
				//Get the badgeid
				$badgeid = preg_replace('/[^a-z0-9-]+/i', '', $request[2]);
				
				$badgesql = "";
				//Allow level fetching by name
				if (is_numeric($badgeid)) {
					$badgesql = "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE id=$badgeid";
				} else {
					$badgeid = strtoupper(strtr($badgeid, "-", " "));
					$badgesql = "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE UPPER(name)='$badgeid'";
				}

				//Execute the sql
				$result = mysqli_query($link, $badgesql);
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}

				mysqli_close($link);
			} else {
				error(400); //Bad request
			}
		} else if (count($request) == 2) {
			if (preg_replace('/[^a-z]+/i', '', $request[1]) == "levels") {
				//Execute the sql
				$result = mysqli_query($link, "SELECT DISTINCT level_id, level FROM award_scheme");
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				echo "[";
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}
				echo "]";

				mysqli_close($link);
			//Return the sublevels
			} else if (preg_replace('/[^a-z]+/i', '', $request[1]) == "sublevels") {
				//Execute the sql
				$result = mysqli_query($link, "SELECT DISTINCT sublevel_id, sublevel FROM award_scheme");
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				echo "[";
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}
				echo "]";

				mysqli_close($link);
			} else if (preg_replace('/[^a-z]+/i', '', $request[1]) == "badges") {
				//Execute the sql
				$result = mysqli_query($link, "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme");
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				echo "[";
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}
				echo "]";

				mysqli_close($link);
			} else {
				//Get the badgeid
				$badgeid = preg_replace('/[^0-9]+/', '', $request[1]);

				//Execute the sql
				$result = mysqli_query($link, "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme WHERE id=$badgeid");
				if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

				header('Content-Type: application/json');
				for ($i=0; $i < mysqli_num_rows($result); $i++) {
					echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
				}

				mysqli_close($link);
			}
		//Return list of badges with names and ids
		} else if (count($request) == 1) {
			//Execute the sql
			$result = mysqli_query($link, "SELECT id, level_id, level, sublevel_id, sublevel, name, data FROM award_scheme");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

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
			if ($request[1] == 'new') error(405, "Use POST to add a new user");
			//Get the user id
			$userid = preg_replace('/[^0-9]+/', '', $request[1]);

			//Execute the sql
			$result = mysqli_query($link, "SELECT id, username, registerDate, realName FROM users WHERE id=$userid");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

			header('Content-Type: application/json');
			for ($i=0; $i < mysqli_num_rows($result); $i++) {
				echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result));
			}

			mysqli_close($link);
			
		//Return list of badges with names and ids
		} else if (count($request) == 1) {
			//Execute the sql
			$result = mysqli_query($link, "SELECT id, username, registerDate, realName FROM users");
			if (!$result || mysqli_num_rows($result) == 0) { error(404, mysqli_error($link)); } //Not found

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
				error(400); //Bad request
			}
			//Make sure post is not empty
			if (empty($_POST)) {
				error(400); //Bad request
			}

			$username = $_POST['user'];
			$email = $_POST['email'];
			$raw_password = $_POST['pass'];
			$raw_password_repeat = $_POST['passRepeat'];
			
			//Validate username
			if (!preg_match('/^[A-Za-z0-9-_]{3,45}$/', $username)) {
				error(400, "Error: Username must be longer than 2 characters and shorter than 46 characters, and must only contain letters, numbers, hyphens and underscores.");
			} else {
				//Check passwords match
				if ($raw_password != $raw_password_repeat) {
					error(400, "Error: Passwords must match.");
				} else {
					//Vaidate password
					if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,72}$/', $raw_password)) {
						error(400, "Error: Password must be longer than 7 characters, and must only contain at least one lowercase letter, one uppercase letter and one number.");
					} else {
						//Validate email
						if (filter_var(strtolower(trim($email)), FILTER_VALIDATE_EMAIL) === false) {
							error(400, "Error: Email address is not valid.");
						} else {
							$result = mysqli_query($link, "SELECT username FROM users WHERE UPPER(username)=UPPER('$username')");
							//If username exists in db already
							if ($result && mysqli_num_rows($result) > 0) {
								mysqli_free_result($result);
								error(400, "Error: Username is taken.");
							} else {
								//Make the new account
								$password_hash = password_hash($raw_password, PASSWORD_DEFAULT);
								if (mysqli_query($link, "INSERT INTO users (username, password, email, registerDate, originalIp) VALUES ('$username','$password_hash','" . strtolower(trim($email)) . "',NOW(),'" . $_SERVER['REMOTE_ADDR'] . "')")) {
									//"Success: New user created successfully.";
									http_response_code(201);

									die('{"username": ' . $username . ',"password_hash":"' . $password_hash . '"}');
								} else {
									error(404, "Error: " . mysqli_error($link));
								}
							}
							mysqli_close($link);
						}
					}
				}
			}

			mysqli_close($link);
		} else {
			error(400); //Bad request
		}
	} else {
		error(405); //Method not allowed
	}
} else if ($action == 'login') {
	if ($method == 'GET') {
		//Return user data if login successful
		if (count($request) == 1) {
			if (isset($_SERVER['PHP_AUTH_USER'])) {
				$username = $_SERVER['PHP_AUTH_USER'];
				$raw_password = $_SERVER['PHP_AUTH_PW'];

				$result = mysqli_query($link, "SELECT id, username, password, email, realName FROM users WHERE username='$username' OR email='$username'");
				
				//Query for the username
				if ($result && mysqli_num_rows($result) > 0) {
					//Add the results to an array
					$data = array();
					$i = 0;
					while ($row = mysqli_fetch_array($result)) {
						$data[$i] = array('id' => $row['id'], 'username' => $row['username'], 'password' => $row['password'], 'email' => $row['email'], 'realName' => $row['realName']);
						$i++;
					}
					
					//If pass correct
					if (password_verify($raw_password, $data[0]['password'])) {
						//Password correct!

						//Update currentIp
						$result = mysqli_query($link, "UPDATE users SET currentIp='" . $_SERVER['REMOTE_ADDR'] . "', lastLoginDate=NOW() WHERE id='" . $data[0]['id'] . "'");

						http_response_code(200);

						die('{"id":' . $data[0]['id'] . ',"username":"' . $data[0]['username'] . '","password_hash":"' . $data[0]['password'] . '","email":"' . $data[0]['email'] . '","realName":"' . $data[0]['realName'] . '"}');
					} else {
						//Password incorrect
						error(401, "Error: Username or password is incorrect.");
					}
				} else {
					//Username incorrect
					error(401, "Error: Username or password is incorrect.");
				}

				//Close the connection
				mysqli_close($link);
			} else {
				error(401, "Error: A username and password is needed to log in"); //Unauthorised
			}
		} else {
			error(400); //Bad request
		}
	} else {
		error(405); //Method not allowed
	}
} else {
	error(400, 'This action does not exist'); //Bad request
}