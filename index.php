<?php

global $httpRequest, $httpResponse, $session;

class BadRequestException extends \Exception {}
class MissingParameterException extends \Exception {}
class InvalidParameterException extends \Exception {}
class AuthenticationException extends \Exception {}
class AuthorizationException extends \Exception {}
class InternalException extends \Exception {}

//  Load dependencies & files
require "./vendor/autoload.php";

use Nette\Http\UrlScript;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\NotImplementedException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use App\Service;

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

$err["error"] = 'Service encountered internal error.';
try
{
	//  Start application
	$app = Service::init(APP_TARGET);
	$app->processRequest(@array_keys($_GET)[0]);

	//  Send data
	echo Json::encode($app->getData(), $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}
catch (AuthenticationException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Failed to authenticate user.";
	$err["description"] = $ex->getMessage();
}
catch (AuthorizationException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "User is not allowed to access this resource.";
	$err["description"] = $ex->getMessage();
}
catch (MissingParameterException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Missing required parameter.";
	$err["description"] = $ex->getMessage();
}
catch (InvalidParameterException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Invalid parameter value.";
	$err["description"] = $ex->getMessage();
}
catch (BadRequestException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Requested endpoint was not found or does not support used method.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();
}
catch (NotImplementedException $ex)
{
	//  Internal not implemented exception
	$err["error"] = "Requested endpoint is not yet implemented.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();
}
catch (PDOException $ex)
{
	//  Database exception
	$err["error"] = "Database layer encountered error.";
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();
}
catch (JsonException $ex)
{
	//  Json exception
	$err["error"] = "Failed to encode data to json.";
	$err["description"] = $ex->getMessage();
}
catch (Exception $ex)
{
	$path = LOG_DIR . "/exception_" . substr(md5($ex->getMessage()), 0, 8) . ".log";
	$res = @file_put_contents($path, $ex->getMessage() . "\n");
	if ($res)
	{
		file_put_contents($path, $ex->getFile() . ":" . $ex->getLine() . "\n\n", FILE_APPEND);
		file_put_contents($path, $ex->getTraceAsString(), FILE_APPEND);
	}

	//  Json exception

	$err["error"] = 'Service encountered internal error.' . ($res ? " Exception trace successfully saved to '$path'." : " System was unable to save exception trace.");
	$err["description"] = $ex->getMessage();
	$err["file"] = $ex->getFile() . ':' . $ex->getLine();
}
finally
{
	if ($httpResponse->isSent() === false)
		echo Json::encode($err, $httpRequest->isAjax() === false ? 0 : Json::PRETTY);
}

exit;
