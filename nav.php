<!DOCTYPE html>
<html>
	<head></head>
	<body>
		<?php
			require_once('connect.php');
			// set user variables
			$user = 'ngiordano';
			$result = mysqli_query($db, "SELECT ID, Administrator FROM Account WHERE Username = '$user';") or die("Error! Can't load data!");
			$row = mysqli_fetch_assoc($result);
			$userid = $row['ID'];
			$admin = $row['Administrator'];
		?>
		<div class="top">
			<div class="user">
				<div class="login">
					Logged in as <span><?php echo $user ?></span>
				</div><div class="admin">
					<?php if($admin) {echo '<a href="data.php">Database Administration</a>';} ?>
				</div>
			</div>
		</div>
		<div class="logo"><a onclick="storePosition()" href="index.php"><img src="images/logo.png"></a></div>
		<div class="social">
			<a target="_blank" href="http://www.twitter.com"><img src="images/twitter.png"></a><br />
			<a target="_blank" href="http://www.instagram.com"><img src="images/instagram.png"></a><br />
			<a target="_blank" href="http://www.facebook.com"><img src="images/facebook.png"></a><br />
			<a target="_blank" href="http://www.youtube.com"><img src="images/youtube.png"></a><br />
			<a target="_blank" href="http://www.plus.google.com"><img src="images/googleplus.png"></a><br />
			<a target="_blank" href="http://www.linkedin.com"><img src="images/linkedin.png"></a><br />
		</div>
		<div class="footer">
			Copyright © 2017 -- Animation Station -- All rights reserved
		</div>
	</body>
</html>