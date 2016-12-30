<?php
if (isset($_GET['e'])) {
	http_response_code($_GET['e']);

	switch ($_GET['e']) {
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

	header('Content-Type: application/json');
	die('{"status": ' . $_GET['e'] . ',"status_text":"' . $text . '"}');
} else {
	header('Content-Type: application/json');
	die('{"status": 200,"status_text":"Of course it\'s a 200. Stop trying to hack my api. :/"}');
}