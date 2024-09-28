<?php
// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Retrieve configuration variables for connecting to DB
require "db.php";

// Set some parameters
$success = true;	// keep track of errors so page redirects only if there are no errors
$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())
?>

<html>

<head>
	<title>All Tables</title>
</head>

<body>
<div class="navbar">
  <a href="home.php">Home</a>
  <a href="lessons.php">Manage Lessons</a>
  <a href="skier-profile.php">Skier Profile</a>
  <a href="wildlife.php">Wildlife</a>
  <a href="all-tables.php">All Tables</a>
</div>

	<h2>All Tables [PROJECTION]</h2>
  <p>View all tables in the database</p>
	<form method="POST" action="all-tables.php">
		<?php	displayAllTables(); ?>
  </form>
  <input type="submit" value="Get table" name="getSubmit"><br /><br />

	<?php
	// ====== HELPERS ======
	function displayAllTables() {
		if (connectToDB()) { 		
			$result = queryAllTables();
			printAllTablesDropdown($result);
			disconnectFromDB();
		}
	}

	// RETURNS a list of all wildlife
	function queryAllTables() {
		$result = executePlainSQL("SELECT table_name FROM user_tables");
		return $result;
	}

	function printAllTablesDropdown($result) {
    echo "<select name='tableName' id='tableName'>";
    echo "<option value=''>--- Choose a table ---</option>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
      echo "<option value='$row[0]'>$row[0]</option>";
		}
	}

	function handleGetRequest() {
		$queryAvailableAttributes = "SELECT COLUMN_NAME from ALL_TAB_COLUMNS where TABLE_NAME = :tableName";
		$result = executeSQLWithBinding($queryAvailableAttributes, array(':tableName' => $_POST['tableName']));
		$style = "style='border: 1px solid black; padding: 8px;'";

		echo "<table style='border: 1px solid black; border-collapse: collapse;'>";
		echo "<tr><th $style>" . $_POST['tableName'] . "</th><th $style></th></tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>";
			echo "<td $style>" . $row[0] . "</td>";
			echo "<td $style>" . "<input type='checkbox' name='formAttributes[]' value='" . $_POST['tableName'] . "/" . $row[0] . "'>" . "</td></tr>";
		}
		echo "</table>";
		echo "<br/>";
		echo "<input type='submit' value='View selected columns' name='selectSubmit'>";
	}

  function handleSelectRequest() {
		$array = $_POST['formAttributes'];
		$N = count($array);
		$tableName = '';
		$SELECT = '';
		$colnames = array();

		// concatenate the selected attributes to a SELECT clause
		for($i=0; $i < $N; $i++) {
			$splits = explode("/", $array[$i]);
			$tableName = $splits[0];
			$attributeName = $splits[1];
			if ($i == 0) {
				$SELECT = $SELECT . $attributeName;
			}
			else {
				$SELECT = $SELECT . ", " . $attributeName;
			}
			$colnames[] = $attributeName;
		}
		echo "<p style='color: blue;'>Selecting columns: $SELECT <br/> From table: $tableName</p>";
		$result = executePlainSQL("SELECT $SELECT from $tableName");
		displaySelectedAttributes($result, $colnames);
	}

	function displaySelectedAttributes($result, $colnames) {
		$style = "style='border: 1px solid black; padding: 8px;'";
		// print column names
		$N = count($colnames);
		echo "<table style='border: 1px solid black; border-collapse: collapse;'><tr>";
		for($i=0; $i < $N; $i++) {
			echo "<th $style>$colnames[$i]</th>";
		}
		echo "</tr>";

		// print values
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>";
			for($i=0; $i < $N; $i++) {
				echo "<td $style>$row[$i]</td>";
			}
			echo "</tr>"; 
		}
		echo "</table>";
	}

	if (isset($_POST['getSubmit'])) {
		if (connectToDB()) {
			handleGetRequest();
			disconnectFromDB();
		}
	}
	else if (isset($_POST['selectSubmit'])) {
		if (connectToDB()) {
			handleSelectRequest();
			disconnectFromDB();
		}
	} 

		// ====== Helpers ======
		function debugAlertMessage($message) {
			global $show_debug_alert_messages;

			if ($show_debug_alert_messages) {
				echo "<script type='text/javascript'>alert('" . $message . "');</script>";
			}
		}

		function executeSQLWithBinding($cmdstr, $params) {
			global $db_conn, $success;
	
			$statement = oci_parse($db_conn, $cmdstr);
			if (!$statement) {
					echo "<br>Cannot parse the following command: " . htmlentities($cmdstr) . "<br>";
					$e = OCI_Error($db_conn);
					echo htmlentities($e['message']);
					$success = False;
			}
	
			foreach ($params as $key => &$val) {
					if (!oci_bind_by_name($statement, $key, $val)) {
							echo "error in binding";
							$success = False;
					}
			}
	
	
			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
					echo "<br>Cannot execute the following command: " . htmlentities($cmdstr) . "<br>";
					$e = oci_error($statement);
					echo htmlentities($e['message']);
					$success = False;
			}
	
			return $statement;
		}

		function executePlainSQL($cmdstr) {
			// echo "<br>running ".$cmdstr."<br>";
			global $db_conn, $success;

			$statement = oci_parse($db_conn, $cmdstr);
			//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

			if (!$statement) {
				echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
				echo htmlentities($e['message']);
				$success = False;
			}

			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = oci_error($statement); // For oci_execute errors pass the statementhandle
				echo htmlentities($e['message']);
				$success = False;
			}

			return $statement;
		}

	?>
</body>
</html>
