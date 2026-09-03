<html>
<head>
<title>MyPhotoCode</title>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<script src="https://code.jquery.com/jquery-1.9.1.js"></script>
<link rel="stylesheet" type="text/css" href="css.css" media="screen" />
</head>
<body>
<div class='header'>
	<div class='logo'>
	<div class='titleTopLeft bar'></div>
	<a href="http://digital-centre.com" target="_blank"><img src='images/web/logo.png' class='imgLogo'></a>
	</div>
	<div class='topBar'>
		<div class='titleTop bar '><span id='a1'></span><span id='a2'></span><span id='a3'></span><span id='a4'></span></div>
		<div class='menu2'>
		
		</div>
		<div class='menu3 bar' id='menu3'>
			
		</div>
	</div>
</div>
<div class='content'>
	<div class='menu'>
		<p class="dMenu" onClick='setSection("owner" , 2 , 1);' >
			<span class='iMenu' ><img src='images/icons/menu/owner.png' class='imgMenu'> </span>
			<span class='tMenu'>My Profile</span>
		</p>
		<p class="dMenu" onClick='setSection("photobooths" ,1);'>
			<span class='iMenu'><img src='images/icons/menu/photobooths.jpg' class='imgMenu'> </span>
			<span class='tMenu'>PhotoBooths</span>
		</p>
		<p class="dMenu" onClick='setSection("events" , 1);'>
			<span class='iMenu'><img src='images/icons/menu/events.png' class='imgMenu'> </span>
			<span class='tMenu'>Events</span>
		</p>
		<p class="dMenu" onClick='setSection("emails" ,1);'>
			<span class='iMenu'><img src='images/icons/menu/emails.png' class='imgMenu'> </span>
			<span class='tMenu'>Emails</span>
		</p>
		<p class="dMenu" onClick='setSection("alerts" ,1);'>
			<span class='iMenu'><img src='images/icons/menu/alerts.png' class='imgMenu'> </span>
			<span class='tMenu'>Alerts</span>
		</p>		
                
	</div>
	<div class='contingut'>
	<div class='inContent'>
		<h1>Aquest seria el apartat on sortirien les noticies </h1>
		<h1> Titulo 1 </h1>
		<h2> Titulo 2 </h2>
		<h3> Titulo 3 </h3>
		<p> Parrafo </p>
		<p class='infoP'> 
		Parrafo Alerta!! <br>
		Hola , para subir un logo debes pulsar etc etc etc 
		</p>
		<p class="errorP">
		Error !!
		Ha habido un error porfavor ....
		</p>
		<p class="okP">
		Perfecto !!
		tal tal tal tal tal talt altl 
		</p>
		<p> Buttons </p>
                <input type="button" class='editButton'/>
		<input type='button' class='okB' value='BUTTON OK'/>
		<input type='button' class='cancelB' value='BUTTON CANCEL'/>
		<input type='button' class='thirdB' value='BUTTON THIRD'/>
		<p> Caixes de text , Input: </p>
		<input type='text' class='textInput'>
		<p> TextArea:</p>
		<textarea class='areaText'></textarea>
		<p> Select:</p>
		<select class='selectText'>
			<option value="">Value1</option>
			<option value="">Value2</option>
			<option value="">Value3</option>
			<option value="">Value4</option>
			<option value="">Value5</option>
			<option value="">Value6</option>
		</select>
		</div>
	</div>
</div>
<script src="js.js"></script>
</body>
</html>