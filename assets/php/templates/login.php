<div id="inputCode">

	<div id="logo"></div>

	<div id="top"><img src="<? echo $baseUrl; ?>/assets/images/blank.gif" width="1" height="1" /></div>
	<div id="body">

		<form method="post" action="<? echo $PHP_SELF; ?>">
 
			<div class="title"><img src="<? echo $baseUrl; ?>/assets/images/txt-rentallogin.png"></div>

			<? if (isset($error)) echo "<div class='error'>".$error."</div>"; ?>
			
			<div class="label">Username</div>
			<div class="textfield"><input type="text" name="username" /></div>
			<div class="label">Password</div>
			<div class="textfield"><input type="password" name="password" /></div>
			<div class="button"><input type="image" alt="Enter" src="<? echo $baseUrl; ?>/assets/images/button-enter.png" width="220" height="63" /></div>

			<input type="hidden" name="form_id" value="login">

		</form>

	</div>
	<div id="bottom"><img src="<? echo $baseUrl; ?>/assets/images/blank.gif" width="1" height="1" /></div>

</div>