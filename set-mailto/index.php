<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<!-- 
code by Tuxis internet Engineering
Questions?
https://www.tuxis.nl/
info at tuxis.nl
-->
<html>
<head>
<title>Set mailto link in Firefox or Chrome</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
<link rel="stylesheet" href="css/style-min.css" type="text/css" />
  <script type="text/javascript">
    navigator.registerProtocolHandler("mailto",
        "https://<?php echo $_SERVER['SERVER_NAME']; ?>/webmail/index2.html#window/mail/compose/compose:%s",
        "Kerio Connect");
  </script>
</head>
<body>
<a href="https://www.kerioindecloud.nl" class="buildby">Kerio Connect en Operator<br />in de cloud!</a>
<img src="images/kerio-connect.png">
  <h1>Open mailto: link met uw Kerio Connect client</h1>
  <div class="content">
  <p>Deze pagina zorgt ervoor dat mailto: linkjes geopend worden door de Kerio Connect client in uw browser.</p>
  <p>Het werkt in Chrome, Firefox en Opera.<br /><br />Belangrijk: Op dit moment vraagt uw browser toestemming.
  <br />Firefox en Opera doen dit met een balk.<br />Chrome met een klein icoontje in de adresbalk.<br />Nadat u toestemming hebt gegeven wordt het pas actief.</p>
  </div>
  <br />
  <h1>Open mailto: url with Kerio Connect client</h1>
  <div class="content">
  <p>This page sets your browser to open mailto: urls width the Kerio Connect client.</p><p>It works in Chrome, Firefox and Opera.<br /><br />Important: At this time your browser requests permission.<br />Firefox and Opera do this with a bar.<br />Chrome with a small icon in the address bar.<br />After consent it will be active.</p>
  </div>
</body>
</html>
