<?php 
	$home = $_SERVER['REQUEST_URI'] == '/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/reset.css?v=<?= rand() ?>">
    <link rel="stylesheet" href="/assets/css/main.css?v=<?= rand() ?>">
	<link rel="stylesheet" href="/assets/css/bodytext.css?v=<?= rand() ?>">
    <link rel="stylesheet" href="/assets/css/slideshow.css?v=<?= rand() ?>">
    <title><?= $title; ?></title>
</head>
<body data-uri="<?= $_SERVER['REQUEST_URI'] ?>">

	<?php if ($header): ?>
		<?php include $header; ?>
	<?php endif; ?>

	<?php include $view; ?>
	
	<?php if ($footer): ?>
		<?php include $footer; ?>
	<?php endif; ?>
	
    <script src="/assets/main.js?v=<?= rand() ?>"></script>
</body>
</html>