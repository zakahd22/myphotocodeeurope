<div id="inputCode">

	<div id="logo"></div>

	<div id="top"><img src="<? echo $baseUrl; ?>/assets/images/blank.gif" width="1" height="1" /></div>
	<div id="body">

		<form method="post" action="<? echo $PHP_SELF; ?>">
 
			<div class="title"><img src="<? echo $baseUrl; ?>/assets/images/txt-insertyourphotocode.png"></div>

			<? if (isset($error)) echo "<div class='error'>".$error."</div>"; ?>

			<div class="textfield"><input type="text" name="code" /></div>
			<div class="button" style="padding:0px;background:transparent;"><input type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-submit.png" width="220" height="63" /></div>

			<input type="hidden" name="form_id" value="code">

		</form>

	</div>
	<div id="bottom"><img src="<? echo $baseUrl; ?>/assets/images/blank.gif" width="1" height="1" /></div>

</div>