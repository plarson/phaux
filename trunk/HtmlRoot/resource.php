<?php
/*
** This file will serve up resoureces that are outside
** your sourece directory. 
** Specify the resource directories in your ini file 
** under the section resources
*/
include("../Classes/Base/base.php");
include("../Classes/Phaux-base/phaux-base.php");
$app = WHConfiguration::currentApplicationName();
if(!$app){
	WHConfiguration::render404();
}
$config = WHConfiguration::parseConfigurationFileForApp($app);
$resourceDirs = $config['resources'];
$path_portions = explode('/', $_SERVER['PATH_INFO']);
$resource = $path_portions[2];

if(!eregi('^[A-Z_0-9_.]*$', $resource)){
	WHConfiguration::render404();
	exit;
}

$typesForExt = array('css'=>'text/css',
					'html'=>'text/html',
					'jpg'=>'image/jpeg',
					'gif'=>'image/gif',
					'png'=>'image/png');

foreach($resourceDirs as $dir){
	if(is_file($dir.'/'.$resource)){
		$offset = 3600 * 24;	
		header('Expires: '.date('r',strtotime('tomorrow')));
		header('Cache-control: public');
	  	header('Content-Length: '.filesize($dir.'/'.$resource));
		$gmt_mtime = gmdate('D, d M Y H:i:s', filemtime($dir.'/'.$resource) ) . ' GMT';
		header('Last-Modified: '.$gmt_mtime);
		$eparts = explode('.',$resource);
		$ext = $eparts[count($eparts)-1];
		$mime = $typesForExt[$ext];
		if(!$mime){
			$mime = 'application/octet-stream';
		}
		header('Content-Type: '.$mime);
		readfile($dir.'/'.$resource);
		exit;
	}
	
}

WHConfiguration::render404();