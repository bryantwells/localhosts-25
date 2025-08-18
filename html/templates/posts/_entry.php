<?php
    $title = ($entry->meta->title) ? $entry->meta->title : 'Localhosts';
    $header = dirname(__FILE__) . '/../_includes/_header--post.php';
	$footer = dirname(__FILE__) . '/../_includes/_footer--home.php';
    $view = dirname(__FILE__) . '/../_views/_post.php';
    include dirname(__FILE__) . '/../_layout.php';
?>