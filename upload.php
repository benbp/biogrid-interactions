<html>
<head>
	<script type="text/javascript">
		function expand(sid, protein){
			if(document.getElementById("button-" + protein).innerHTML == 'hide'){
				document.getElementById(protein).innerHTML = "";
				document.getElementById("button-" + protein).innerHTML = "expand";
				return;
			}
			
			var xmlhttp = new XMLHttpRequest();
			var url = "expand.php?sid=" + sid + "&pname=" + protein;

			xmlhttp.onreadystatechange = function() {
               if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                	document.getElementById(protein).innerHTML = xmlhttp.responseText;
					document.getElementById("button-" + protein).innerHTML = "hide";
               }
            }

            xmlhttp.open('GET', url, true);
            xmlhttp.send();
		}
	</script>
</head>
<body>
	<?php
		$db = pg_connect("host=localhost port=5432 dbname=biogrid user=bbroderickphillips password=yargBD");
		$sid = uniqid();
		$proteinNames = $_POST["proteinNames"];
		$counter = 0;
		foreach ($_FILES['uploads']['tmp_name'] as $fname){
			importData($fname, $proteinNames[$counter], $counter, $sid);
			$counter++;
		}

		echo "<b>Intermediate proteins shared by <i>";
		echo strtoupper(implode(", ", $proteinNames));
		echo "</i></b><br />";

		echo "<a href='raw.php?sid=$sid'>Generate download of raw data</a><br /><br />";

		echo "<div id='proteins'>";
		// Display list of intermediate proteins, with buttons for showing expandable divs containing data
		$result = pg_query($db, "SELECT DISTINCT intp FROM find_intermediates('$sid', 0)");
		while ($row = pg_fetch_assoc($result)) {
			echo "<div>";
				echo "<b>" . $row["intp"] . "</b>";
				echo "<button id=button-" . $row["intp"] . " onClick=expand('" . $sid . "','" . $row["intp"] . "');>expand</button>";
				echo "<div id='" . $row["intp"] . "'>";
				echo "</div>";
			echo "</div>";
		}
		echo "</div>";

		function importData($fname, $pname, $filenum, $sid){
			global $db;
			$bio = fopen($fname, 'r');
			$header = fgetcsv($bio, 0, "\t");
			$data = array();
			$psqlData = array();
			$fmp = strtoupper($pname);

			while ($row = fgetcsv($bio, 0, "\t")){
				$data[] = array_combine($header, $row);
			}

			fclose($bio);

			foreach ($data as $row){
				$psqlData[] = array($row['BioGRID ID Interactor A'], $row['BioGRID ID Interactor B'], $row['Official Symbol Interactor A'], $row['Official Symbol Interactor B'], $row['Pubmed ID']);
			}

			foreach($psqlData as $row){
				if ($row[2] == $fmp){
					pg_query($db, "INSERT INTO interactors VALUES ('$sid', '$row[0]', '$fmp', '$row[1]', '$row[3]', 'OSIB', '$row[4]', $filenum)");
				} else {
					pg_query($db, "INSERT INTO interactors VALUES ('$sid', '$row[1]', '$fmp', '$row[0]', '$row[2]', 'OSIA', '$row[4]', $filenum)");
				}
			}
		}

	?>
</body>
</html>