<?php

global $httpRequest, $httpResponse, $session;

class BadRequestException extends \Exception {}
class AuthenticationException extends \Exception {}
class AuthorizationException extends \Exception {}
class InternalException extends \Exception {}

//  Load dependencies & files
require "./vendor/autoload.php" or die("ERROR: Composer's autoload file was not found!");

use Nette\Http\UrlScript;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\NotImplementedException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

if ($_SERVER["REMOTE_HOST"] !== "b07-1314a.kn.vutbr.cz")
{
	error_reporting(~E_ALL);
}

$url = new UrlScript("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $_SERVER["SCRIPT_FILENAME"]);
$httpRequest = new Request($url, null, $_POST, $_FILES, $_COOKIE, getallheaders(), $_SERVER["REQUEST_METHOD"]);
$httpResponse = new Response();
$session = new \Nette\Http\Session($httpRequest, $httpResponse);

define("APP_TARGET", "fit");
define("CONFIG_DIR", realpath("./config"));

@mkdir("./cache");
define("CACHE_DIR", realpath("./cache"));

@mkdir("./log");
define("LOG_DIR", realpath("./log"));

try
{
	//  Start application
	$app = Application::init(APP_TARGET);
	$app->processRequest(@array_keys($_GET)[0] ?: "/info");

	//  Send data
	echo Json::encode($app->getData(), $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (BadRequestException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Requested endpoint was not found or does not support used method.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();

	echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (NotImplementedException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Requested endpoint is not yet implemented.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();

	echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (PDOException $ex)
{
	//  Database exception
	$err["error"] = "Database layer encountered error.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();

	echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (JsonException $ex)
{
	//  Json exception
	$err["error"] = "Failed to encode data to json.";
	$err["description"] = $ex->getMessage();

	echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (Exception $ex)
{
	$path = LOG_DIR . "/exception_" . substr(md5($ex->getMessage()), 0, 8) . ".log";
	$res = @file_put_contents($path, $ex->getMessage() . "\n");
	if ($res)
	{
		file_put_contents($path, $ex->getFile() . ":" . $ex->getLine(), FILE_APPEND);
		file_put_contents($path, $ex->getTraceAsString(), FILE_APPEND);
	}

	//  Json exception

	$err["error"] = 'Application encountered internal error.' . ($res ? " Exception trace successfully saved to '$path'." : " System was unable to save exception trace.");
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();

	echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);

}

exit;
