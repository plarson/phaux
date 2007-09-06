<?php

/*
** This file needs to be cleaned up BIG TIME
** It has been a sort of hack it when I need it catch all
** It might be better to move most of it out to an 
** Object
*/
if(get_magic_quotes_gpc()){
	foreach($_REQUEST as $var => $value){
		if(is_array($_REQUEST[$var])){
			foreach($_REQUEST[$var] as $iVar => $iValue){
				$_REQUEST[$var][$iVar] = stripslashes($iValue);
			}
		}else{
			$_REQUEST[$var] = stripslashes($value);
		}
	}
}

include_once("../Classes/Base/base.php");
$errorHandler = Object::construct("WHError")->start();
$base_configuration = parse_ini_file("../Configuration/base.ini",TRUE);
$dir = dir("../Configuration");
$app_configurations = array();

if(!isset($_REQUEST['app'])){
	$path_portions = explode('/', $_SERVER['PATH_INFO']);
	$app = $path_portions[1];
	$_REQUEST['app'] = $app;
}else{
	$app = $_REQUEST['app'];
}
//var_dump($_SERVER);
/*
**Parse the ini config files and set up the applications
*/

while(false !== ($dirent = $dir->read())){
	if($dirent != "base.ini" &&
		
			$dirent[0] != '.'){
		$fparts = explode(".",$dirent);
		$app_configurations[$fparts[0]] = $base_configuration;
		$new_conf = parse_ini_file("../Configuration/$dirent",TRUE);
		
		foreach($new_conf as $section => $values){
			if(is_array($base_configuration[$section])){
				$new_conf[$section] = array_merge($base_configuration[$section],$values);
			}
		}
		foreach($base_configuration as $section =>$values){
			if(!isset($new_conf[$section])){
				$new_conf[$section] = $values;
			}
		}
		
		$app_configurations[$fparts[0]] = $new_conf;
	}
}


if($app_configurations[$app] == NULL){
	header("HTTP/1.0 404 Not Found");
	echo("<h1>No such application $app </h1>");
	$errorHandler->end();
	exit;
}else{
	
	
	foreach($app_configurations[$app]['includes'] as $var => $value){
		include("$value");
	}	
	
	ini_set("session.use_cookies",$app_configurations[$app]['general']['use_cookie']);
	ini_set("session.name","SID");
	 
	$configuration_class = $app_configurations[$app]['general']['configuration_class'];
	$configuration = Object::construct($configuration_class);
	$configuration->setApplicationName($app)->
										setConfigValues($app_configurations[$app]);
	
	
	$session_class = $app_configurations[$app]['general']['session_class'];
	
	$session = call_user_func(array(
						Object::construct($session_class),
						"startSessionOnAppWithConfiguration"),$app,$configuration);
	
}


/*
**If this is a style for a class handle it
** Might be nice if we do some cache control
** With out cache control it better to include the
** css in the page
*
if($_REQUEST['_sfc']){
	if(class_exists($_REQUEST['_sfc'])){
	
		if($_REQUEST['_type'] == "style"){
			header("Content-Type: text/css");
			echo Object::construct($_REQUEST['_sfc'])->style();
		}else{
			header("Content-Type: text/javascript");
			echo Object::construct($_REQUEST['_sfc'])->script();
		}
	}
	$errorHandler->end();
	exit;
}
*/

if($_REQUEST["_r"]){
	$_SESSION[$app]['session']->restoreRegistry($_REQUEST["_r"]);
}

$REDIRECT = FALSE;

if($_REQUEST["_k"]){
	//var_dump($_SESSION[$app]['session']);
	if(is_object($_SESSION[$app]['session']->callbackByKey($_REQUEST["_k"]))){
		$_SESSION[$app]['session']->callbackByKey($_REQUEST["_k"])->run();
	}
	$REDIRECT = TRUE;
	
}

if(is_array($_REQUEST["_i"])){
	foreach($_REQUEST["_i"] as $key => $value){
		//var_dump($_SESSION[$app]['session']);
		if(is_object($_SESSION[$app]['session']->callbackByKey($key))){
			$_SESSION[$app]['session']->callbackByKey($key)->runWithArgument($value);
		}
		
	}
	$REDIRECT = TRUE;
}

if($_SESSION[$app]['mainComponent'] == NULL){
	$main_class = $app_configurations[$app]['general']['main_class'];
	$_SESSION[$app]['mainComponent'] = Object::construct($main_class);
	if($configuration->debugMode()){
		$_SESSION[$app]['mainComponent']->addDecoration(
			Object::Construct('WHMainDevelopmentDecoration'));	
	}
}

$htmlRoot = Object::construct("WHHtmlRoot");
$_SESSION[$app]['mainComponent']->updateRootWithChildren($htmlRoot);

foreach($app_configurations[$app]['styles'] as $var => $value){
	$htmlRoot->needsStyle($value);
}

foreach($app_configurations[$app]['scripts'] as $var => $value){
	$htmlRoot->needsScript($value);
}

/*
** If this is a live update we don't want to redirect
*/
if(isset($_REQUEST['_lu'])){
	$REDIRECT = FALSE;
}

if($REDIRECT){
	
	if($app_configurations[$app]['general']['redirect_after_callback'] == 1){
		$_SESSION[$app]['session']->save();
		$urlExtra = $htmlRoot->getExtraUrl();
		header("Location: ".$_SESSION[$app]['session']->url().$urlExtra);
							
		/*
		** I am just using xdebug for now
		*/
		if($configuration->debugMode() && function_exists('xdebug_time_index')){
			$_SESSION[$app]['session']->setDebugCallbackTime(xdebug_time_index());
		}
		$errorHandler->end();
		exit;
	}
}

$_SESSION[$app]['session']->startingRenderStep();

if($configuration->debugMode()){
	if($_SESSION[$app]['session']->isHalosOn()){
		$_SESSION[$app]['mainComponent']->setupHaloForAll();
	}else{
		$_SESSION[$app]['mainComponent']->setupHaloForAll(TRUE);
	}
}
	

if($_REQUEST['_lu'] == ""){
	$html = WHHtmlCanvas::construct("WHHtmlCanvas");
	$html->html()->with(
		$html->head()->with(
			$htmlRoot->renderHeadContentsOn($html).
		
			$html->style()->with(
				$_SESSION[$app]['mainComponent']->styles()
			).
			$html->script()->type("text/javascript")->with(
				$_SESSION[$app]['mainComponent']->scripts()
				)
			).
			
		
			$html->body()->with(
				$_SESSION[$app]['mainComponent']->renderOn($html)
				)
		);
		
}else{
	$html = WHHtmlCanvas::construct("WHLiveResponceCanvas");
	if(is_object($_SESSION[$app]['session']->callbackByKey($_REQUEST['_lu']))){
		$html->html()->with(
					$_SESSION[$app]['session']->
						callbackByKey($_REQUEST['_lu'])->
						runWithArgument($html)
		);
	}
}

echo $html;
if($configuration->debugMode()){
	echo $DEBUG_ERRORS;
}
$errorHandler->end();
$_SESSION[$app]['session']->save();
