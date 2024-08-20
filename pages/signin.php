<?php 
	require '../php_static/session_lookup.php';

	if (isset($_SESSION['username'])) {
		header('location: dashboard.php');
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>PHP Systems Sign-in</title>

		<?php include '../php_static/web/link-rels.php';?>
		<?php include '../php_static/web/scripts-rels.php';?>
	</head>

	<body class="hold-transition login-page">
		<div class="login-box">
			<div class="login-logo">
				<img src="../static/img/wip.png" style="height:150px;">
				<h2><b>Logistic Cost<br>Management</b></h2>
			</div>
			<!-- /.login-logo -->
			<div class="card">
				<div class="card-body login-card-body">
					<p class="login-box-msg"><b>Sign in to start your session</b></p>
					<?php include '../forms/signin_form.php';?>
				</div>
			</div>
		</div>
	</body>
</html>

<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        customClass: {
            popup: 'colored-toast',
        },
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
    })
</script>
<!-- handles notification -->
<?php
if (isset($_SESSION['notification'])) {
	$notification = json_decode($_SESSION['notification'], true);
	echo <<<HTML
	<script>
	Toast.fire({
		icon: "{$notification['icon']}",
		title: "{$notification['text']}",
	})
	</script>
	HTML;
	$_SESSION['notification'] = null;
}
?>