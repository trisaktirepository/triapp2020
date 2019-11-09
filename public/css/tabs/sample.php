<html>
<head>
	<link type="text/css" rel="stylesheet" href="tabpane1.css" />
	<script type="text/javascript" src="tabpane.js"></script>
	
	
</head>
<body>

<p>This page reloads as soon as the page is loaded... look at the memory consuption.</p>

<div class="tab-pane" id="tabPane1">

<!--optional setting-->
	<script type="text/javascript">
		//var tabPane1 = new WebFXTabPane( document.getElementById( "tabPane1" ), 1 )	
		//0:false - disabled tab history//
		var tabPane1 = new WebFXTabPane( document.getElementById( "tabPane1" ), 0 )	
	</script>
	
<?
	for ($i=1;$i<5;$i++) {
?>
  <div class="tab-page" id="tabPage<?=$i?>">
    <h2 class="tab">General <?=$i?></h2>
    
    <table><tr><td>
      This is text of tab <?=$i?>. 
    </td></tr></table>
    
  </div>
<?}?>

</div>

<!--load properly (optional)-->
<script type="text/javascript">
	setupAllTabs();
</script>

</body>
</html>