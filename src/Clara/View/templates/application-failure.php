<?php
/**
 * application-failure.php
 *
 * This DocBlock was generated automatically by PhpStorm
 *
 * @author      Kevin O'Rourke <kevin@korourke.net>
 * @package     Clara
 */
?>
<!doctype HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
	<title>Oops! Something went wrong...</title>
	<style>
		* { font-family: "Helvetica Nueue", Helvetica, arial, sans-serif; color: #444; }
		html { background: #eee; }
		main { max-width: 600px; margin: auto; }
		div.clara-error-message {
			max-width: 600px;
			margin: 50px auto 25px;
			-webkit-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
			-moz-box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
			box-shadow: 0px 2px 6px rgba(50, 50, 50, 0.1);
			padding: 24px;
			background: #fff;
			border: 1px solid #ccc;
			text-align: center;
		}
		h1 { font-size: 24px; font-weight: bold; }
		h2 { font-size: 16px; font-weight: normal; color: #888; }
		span { font-style: italic; font-size: 12px; text-align: center; color: #888; }
	</style>
</head>
<body>
	<header></header>
	<main>
		<div class="clara-error-message">
			<h1><?php echo $title ?></h1>
			<h2><?php echo $message ?></h2>
			<span>Error code: <?php echo $errorCode ?></span>
		</div>
	</main>
	<footer>
		<?php if( ! empty($debugInfo)): ?>
			<?php Kint::dump($debugInfo) ?>
		<?php endif ?>
	</footer>
</body>
</html>