<?php

/*
** This file needs to be cleaned up BIG TIME
** It has been a sort of hack it when I need it catch all
** It might be better to move most of it out to an 
** Object
*/
error_reporting(E_ALL);
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

include("../Classes/Base/base.php");
include("../Classes/Phaux-base/phaux-base.php");
$errorHandler = Object::construct("WHErrorHandler")->start();
$app_configurations = array();

$app = WHConfiguration::currentApplicationName();

/* FIXME
** Currently Phaux parses the INI file on every request 
** This is excessive. We should only parse the ini file
** at the start of a session and use the configuration 
** from the last session if present
**
** The problem is that we can't restore the session untill
** the includes have been processes and the includes
** are stored in the session and in the INI files
*/

$app_configurations[$app] = WHConfiguration::parseConfigurationFileForApp($app);

if($app_configurations[$app] == NULL){
	$errorHandler->end();
	WHConfiguration::render404();
}else{

	/*
	**We must include before we construct the configuration
	*/
	foreach($app_configurations[$app]['includes'] as $var => $value){
		include($value);
	}

	$configuration_class = $app_configurations[$app]['general']['configuration_class'];
	$configuration = Object::construct($configuration_class);
	$configuration->setApplicationName($app)->
										setConfigValues($app_configurations[$app]);
	
	ini_set("session.use_cookies",$configuration->useCookie());
	ini_set("session.name","SID");
			
	$session_class = $configuration->sessionClass();
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

if(isset($_REQUEST["_r"])){
	$_SESSION[$app]['session']->restoreRegistry($_REQUEST["_r"]);
}

$REDIRECT = FALSE;

if(isset($_REQUEST["_k"])){
	if(is_object($_SESSION[$app]['session']->callbackByKey($_REQUEST["_k"]))){
		$_SESSION[$app]['session']->callbackByKey($_REQUEST["_k"])->run();
	}
	$REDIRECT = TRUE;
	
}

if(isset($_FILES['_i']) && is_array($_FILES['_i'])){

	foreach($_FILES['_i']['error'] as $key => $file){
		$_REQUEST['_i'][$key] = array('name'=>$_FILES['_i']['name'][$key],
		 								'type'=>$_FILES['_i']['type'][$key],
										'tmp_name'=>$_FILES['_i']['tmp_name'][$key],
										'error'=>$_FILES['_i']['error'][$key],
										'size'=>$_FILES['_i']['size'][$key]);
	}
	ksort($_REQUEST['_i']);
}

if(isset($_REQUEST['_i']) && is_array($_REQUEST['_i'])){
	foreach($_REQUEST['_i'] as $key => $value){
		//var_dump($_SESSION[$app]['session']);
		if(is_object($_SESSION[$app]['session']->callbackByKey($key))){
			$_SESSION[$app]['session']->callbackByKey($key)->runWithArgument($value);
		}
		
	}
	$REDIRECT = TRUE;
}

if(!isset($_SESSION[$app]['mainComponent'])){
	$main_class = $configuration->mainClass();
	$_SESSION[$app]['mainComponent'] = Object::construct($main_class);
	if($configuration->debugMode()){
		$_SESSION[$app]['mainComponent']->addDecoration(
			Object::Construct('WHMainDevelopmentDecoration'));	
	}
}

$htmlRoot = Object::construct("WHHtmlRoot");
$_SESSION[$app]['mainComponent']->updateRootWithChildren($htmlRoot);

/*
** If this is a live update we don't want to redirect
*/
if(isset($_REQUEST['_lu'])){
	$REDIRECT = FALSE;
}

if($REDIRECT){
	
	if($configuration->redirectAfterCallback() == 1){
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

foreach($configuration->styles() as $var => $value){
	$htmlRoot->needsStyle($value);
}

foreach($configuration->scripts() as $var => $value){
	$htmlRoot->needsScript($value);
}

$_SESSION[$app]['session']->startingRenderStep();

if($configuration->debugMode()){
	if($_SESSION[$app]['session']->isHalosOn()){
		$_SESSION[$app]['mainComponent']->setupHaloForAll();
	}else{
		$_SESSION[$app]['mainComponent']->setupHaloForAll(TRUE);
	}
}
	
$html = WHHtmlCanvas::construct($configuration->renderClass());

if(!isset($_REQUEST['_lu']) || $_REQUEST['_lu'] == ""){

    $styles = trim($_SESSION[$app]['mainComponent']->styles());
    $scripts = trim($_SESSION[$app]['mainComponent']->scripts());

	$html->html()->with(
		$html->head()->with(
			$htmlRoot->renderHeadContentsOn($html).
		    ($styles ? $html->style()->with($styles) : '').
		    ($scripts ? $html->script()->type("text/javascript")->with($scripts) : '')
	    ).
			$html->body()->id('main-body')->with(
				$_SESSION[$app]['mainComponent']->renderOn($html)
				)
		);
		
}else{
	/*
	**For live request we need to save the current
	** registry as is before we call session save.
	** session save will perserve the current registry
	** and prepare a new one
	*/
	$_SESSION[$app]['session']->currentRegistry()->saveState();
	$html->makeLiveResponce();
	if(is_object($_SESSION[$app]['session']->callbackByKey($_REQUEST['_lu']))){
		$html->html()->with(
					$_SESSION[$app]['session']->
						callbackByKey($_REQUEST['_lu'])->
						runWithArgument($html)
		);
	}
}
echo $html->document();
$errorHandler->end();
$_SESSION[$app]['session']->save();