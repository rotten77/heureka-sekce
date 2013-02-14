<?php
$xmlUrl = "http://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml";
$xmlSoubor = dirname(__FILE__) . "/xml/heureka-sekce.xml";
/**
 * Stáhnout XML?
 */
if(!is_file($xmlSoubor) || (is_file($xmlSoubor) && filemtime($xmlSoubor)<strtotime("now - 6 hours"))) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $xmlUrl);
	// curl_setopt($ch, CURLOPT_HEADER, 1); 
	// curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
	$result = curl_exec($ch);
	curl_close($ch);

	$fp = fopen($xmlSoubor, "w+");
	fwrite($fp, $result);
	fclose($fp);
}

?><!DOCTYPE html>
<html lang="cs">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<meta name="description" content="Prohlížeč stromu sekcí Heureka.cz" />
	<link href="./stylesheet.css" rel="stylesheet" />
	<title>Strom sekcí Heureka.cz</title>
</head>
<body>
<div id="wrapper">
<?php if(!is_file($xmlSoubor)) die('<p style="color:#c00;">XML soubor se nepodařilo stáhnout.</p></div></body></html>'); ?>

<h1>Strom sekcí Heureka.cz</h1>
<div id="panel">
	<div id="name"><span class="nazev">Název</span> <span class="hodnota"></span></div>
	<div id="fullname"><span class="nazev">Umístění</span> <span class="hodnota"><input type="text" id="fullname-input" /></span></div>
	<div id="id"><span class="nazev">ID</span> <span class="hodnota"></span></div>
</div>
<?php
/**
 * Funkce pro větve
 */
function vetev($PARENT) {
	
	if(isset($PARENT->CATEGORY)) {
		echo '<ul>';
			foreach($PARENT->CATEGORY as $CHILD) {
				echo '<li>';

					$CATEGORY_FULLNAME = isset($CHILD->CATEGORY_FULLNAME) ? $CHILD->CATEGORY_FULLNAME : "";
					$CATEGORY_FULLNAME = str_replace("Heureka.cz |", "", $CATEGORY_FULLNAME);
					$CATEGORY_FULLNAME = str_replace("|", "&gt;", $CATEGORY_FULLNAME);
					$CATEGORY_FULLNAME = trim($CATEGORY_FULLNAME);

					$ikona = isset($CHILD->CATEGORY) ? true : false;

						echo '<a href="javascript:void(0);" class="vetev" data-full-name="'.$CATEGORY_FULLNAME.'" data-id="'.$CHILD->CATEGORY_ID.'">'.($ikona ? '<span class="ikona">&plus;</span> ' : '').'<span class="nazev'.(!$ikona ? ' nazev-margin' : '').'">'.$CHILD->CATEGORY_NAME.'</span></a>';
						vetev($CHILD);	
					
				echo '</li>';
			}
			
		echo '</ul>';
	}
}

/**
 * Výpis sekcí
 */
$xml = file_get_contents($xmlSoubor);
$HEUREKA = new SimpleXMLElement($xml);

vetev($HEUREKA);
?>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
$(function(){
	$('li ul, #panel').hide();


	$('a.vetev').click(function(){
		$('a.vetev').removeClass("aktivni");
		$(this).addClass("aktivni").next().toggle({step: function(){
			var ikona = $(this).prev().children('.ikona');
			if($(this).is(":visible")) {
				ikona.html("&minus;");
			} else {
				ikona.html("&plus;");
			}
		}});

		$('#panel').show();
		$('#fullname').show().children('.hodnota').html($(this).data("full-name")!="" ? '<input type="text" id="fullname-input" value="'+$(this).data("full-name")+'" />' : "<em>neuvedeno</em>");
		$('#name').show().children('.hodnota').html($(this).children('.nazev').html());
		$('#id').show().children('.hodnota').html($(this).data("id"));
		return false;
	});
});
</script>
<p id="footer">
autor: <a href="http://rotten77.cz/" target="_blank">Jan Zatloukal</a> &#124; <a href="https://github.com/rotten77/heureka-sekce" target="_blank">GitHub</a>
</p>
</div>
</body>
</html>