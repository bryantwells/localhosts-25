<?php

	// YAML and .md parsing
    require __DIR__ . '/../vendor/autoload.php';
	
	// main app
	include 'app/Router.php';
	include 'app/Entry.php';
	include 'app/File.php';
	include 'app/Site.php';
	
	// build the site object
	$SITE = new Site('content');
	
	// json
	header("Content-Type: application/json");
	echo json_encode($SITE);

	exit();
?>