<?php
	$db = pg_connect("host=localhost port=5432 dbname=biogrid user=bbroderickphillips password=yargBD");

	$sid = $_GET['sid'];
	$pname = $_GET['pname'];
	expand($sid, $pname);

	function expand($sid, $pname){
		global $db;
	
		$result = pg_query($db, "SELECT refpID, refp, intpID, intp, intosi, pubmedID FROM interactors WHERE sid = '$sid' AND intp = '$pname'");

		echo "<table border=1 cellpadding=6px>";
		echo "<tr><th>Reference Protein</th><th>Intermediate Protein</th><th>Pubmed ID</th></tr>";
		while($row = pg_fetch_assoc($result)){
			$refpURL = 'http://thebiogrid.org/' . $row['refpid'];
			$intpURL = 'http://thebiogrid.org/' . $row['intpid'];
			$pubmedURL = 'http://www.ncbi.nlm.nih.gov/pubmed/' . $row['pubmedid'];
			
			echo "<tr>";
				echo "<td>";
					echo "<a href='$refpURL' target='_blank'>" . $row['refp'] . "</a>";
				echo "</td>";
				echo "<td>";
					echo "<a href='$intpURL' target='_blank'>" . $row['intp'] . "</a> (<i>" . $row['intosi'] . "</i>)";
				echo "</td>";
				echo "<td>";
					echo "<a href='$pubmedURL' target='_blank'>" . $row['pubmedid'] . "</a>";
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
		
	}
?>