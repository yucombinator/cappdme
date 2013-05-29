<?php
/**
 * @title            cappd.me
 * @desc             link shortener with caps
 *
 * @author           Yu Chen Hou<me@yuchenhou.com>
 * @copyright        (c) 2012, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License.
 * @version          1.0
 */

//DEBUG
//TODO ue a debug variable
ini_set('display_errors', 1);
error_reporting(~0);

require "Slim/Slim.php";
require "NotORM.php";
require "linkmanager.class.php";

define('DB_DRIVER', 'mysql');
define('DB_HOST', 'INFO HERE');
define('DB_NAME', 'INFO HERE');
define('DB_USER', 'INFO HERE');
define('DB_PASS', 'INFO HERE');
//define base
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

//TODO use variable here
$pdo = new PDO(DB_DRIVER.":host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
$db = new NotORM($pdo);
$linkManager = new linkManager($db);

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim(array(
    'log.enabled' => true,
	'templates.path' => './templates'
));

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, and `Slim::delete`
 * is an anonymous function.
 */

// homepage
$app->get('/', function () use ($app){
	$app->render('main.php');
});

// faq route
$app->get('/faq', function () use($app) {
	$app->render('faq.php');
});

// redirection
$app->get('/:uid+', function ($uid) use($app, $linkManager) {
	//if ($uid[1] == "delete")) {
	//	echo "To be implemented.";
	//}else{
		$id =$linkManager->decodeShortenedURL($uid[0]);
		$result = $linkManager->fetch($id);

		if($result == null){
		    $app->render('404.php');
		}else{
			//Send the user on his way
			$app->redirect($result); 
		}
	//}
	//TODO Set cookie

});

// API POST route
$app->post('/api/create', function () use($app, $linkManager) {
		$app->response()->header("Content-Type", "application/json");
		// Get request object
		$req = $app->request();
		//TODO Validation, Errors
		$url = $req->post('url');
		$expiration_time = $req->post('expire_time');
		$daily_cap = $req->post('daily_cap');
		$total_cap = $req->post('total_cap');
		
		if($url == null){
			echo json_encode(array("error" => "no url entered"));
			return;
		}
		if($total_cap < 0){
			echo json_encode(array("error" => "total cap is negative"));
			return;
		}
		
		if(strtotime($expiration_time.' + 1 day') < time()){
			echo json_encode(array("error" => "expiration date already passed!"));
			return;
		}

		//Validate the input and put them in an array
		if(strstr($url,"://") == false){
			$url = "http://" . $url;
		}
		$data = array(
			"url" =>filter_var($url, FILTER_SANITIZE_URL),
			"expiration_time" => filter_var($expiration_time, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			"daily_cap" => filter_var($daily_cap, FILTER_VALIDATE_INT),
			"total_cap" => filter_var($total_cap, FILTER_VALIDATE_INT),
		);
		$result = $linkManager->save($data);
		$permalink =$linkManager->encodeFromID($result);
		$array = array("permalink" => $permalink,"generated_time" => time());
		echo json_encode($array);

});

// API POST route
$app->post('/api/qr', function () use($app, $linkManager) {

});

// PUT route
$app->put('/put', function () {
    echo 'This is a PUT route';
});

// DELETE route
$app->delete('/delete', function () {
    echo 'This is a DELETE route';
});

$app->notFound(function () use ($app) {
    $app->render('404.php');
});

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
