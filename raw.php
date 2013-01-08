<html>
<head>
	<title>Raw Data</title>
</head>
<body>
	<?php
		$db = pg_connect("host=localhost port=5432 dbname=biogrid user=bbroderickphillips password=yargBD");
		$sid = $_GET['sid'];
		
		$path = "/var/www/biogrid/rawfiles/" . $sid . "-biogrid.csv";
		$handle = fopen($path, 'w');

		$result = pg_query($db, "SELECT refpID, refp, intpID, intp, intosi, pubmedID FROM interactors WHERE sid = '$sid'");
		echo "<b><a href='rawfiles/" . $sid . "-biogrid.csv'>Download data as CSV</a></b><br /><i>Option-Click, or Right Click > Save As</i><hr>";
		
		echo "Reference Protein Biogrid ID, Reference Protein Name, Intermediate Protein Biogrid ID, Intermediate Protein Name, Intermediate Protein Interactor, Pubmed ID<br />";
		fwrite($handle, "Reference Protein Biogrid ID, Reference Protein Name, Intermediate Protein Biogrid ID, Intermediate Protein Name, Intermediate Protein Interactor, Pubmed ID\r\n");		
		while($row = pg_fetch_assoc($result)){
			echo implode(",", $row);
			echo "<br />";
			fputcsv($handle, $row);
		}		
		
		fclose($handle);
	?>
	

</body>
</html>