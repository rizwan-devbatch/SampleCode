<?php
	define('MODULE_NAME', $this->router->fetch_class());
	define('ACTION_NAME', $this->router->fetch_method());
	define('URI_SEGMENT', MODULE_NAME . '_' . ACTION_NAME);
?>
<html>
	<head>
		<title>CI Template</title>
		<meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta name="description" content="">
                <meta name="author" content="">

		<?php $this->load->view('themes/-admin-login-top-includes.php'); ?>
	</head>
	<body class="<?php// echo $body_class; ?>">
		<div class="container">
			<?php echo $output; ?>
		</div>
		<?php $this->load->view('themes/-admin-login-footer.php'); ?>
	</body>
</html>