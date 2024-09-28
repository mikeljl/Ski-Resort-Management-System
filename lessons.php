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
	<title>Manage Lessons</title>
</head>

<body>
<div class="navbar">
    <a href="home.php">Home</a>
    <a href="lessons.php" class="active">Manage Lessons</a>
    <a href="skier-profile.php">Skier Profile</a>
    <a href="environment.php">Environment</a>
    <a href="security.php">Rescue Team</a>
</div>

	<h2>All Lessons [DELETE]</h2>
	<form method="POST" action="lessons.php">
		<table style="border: 1px solid black; border-collapse: collapse;">
		<?php	displayAllLessons(); ?>
		</table>
	<br>
	<input type="submit" value="Delete Lesson and Update" name="removeSubmit">
  </form>

	<hr/>
	<h2>Messages</h2>

	<?php
	// ====== HELPERS ======
	function displayAllLessons() {
		if (connectToDB()) { 		
			$result = queryAllLessons();
			printAllLessonsTable($result);
			disconnectFromDB();
		}
	}

	// RETURNS a list of all lessons
	function queryAllLessons() {
		global $db_conn;
		$result = executePlainSQL("SELECT LESSONDATE,
																			LESSONTIME,
																			TO_CHAR(LESSONTIME, 'HH:MI AM') AS FORMATED_LESSONTIME, 
																			INSTRUCTORNAME, 
																			COST
															FROM lesson");
		return $result;
	}
	// ====== DELETE operation ======
	function printAllLessonsTable($result) {
		$style = "style='border: 1px solid black; padding: 8px;'";

		echo "<tr>
					<th $style>Date</th>
					<th $style>Time</th>
					<th $style>Instructor</th>
					<th $style>Cost (CAD)</th>
					<th $style></th>
					</tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr>";
			echo "<td $style>" . $row["LESSONDATE"] . "</td>";
			echo "<td $style>" . $row["FORMATED_LESSONTIME"] . "</td>";
			echo "<td $style>" . $row["INSTRUCTORNAME"] . "</td>";
			echo "<td $style>" . $row["COST"] . "</td>";
			$name = $row["LESSONDATE"] . "/" . $row["LESSONTIME"] . "/" . $row["INSTRUCTORNAME"] . "/" . $row["FORMATED_LESSONTIME"] . "/";
			echo "<td $style>" . "<input type='checkbox' name='formLessons[]' value='" . $name . "'>" . "</td>";
			echo "</tr>"; 
		}
	}

	function handleRemoveRequest() {
		global $db_conn;
		$array = $_POST['formLessons'];

		if(empty($array)) {
			echo "<p style='color: blue;'>You didn't select any lessons.</p>";
		} 
		else {
			$N = count($array);
			echo "<p style='color: blue;'>$N lesson(s) selected... </p>";
			for($i=0; $i < $N; $i++) {
				$attributes = explode("/", $array[$i]);
				$date = $attributes[0];
				$time = $attributes[1];
				$instructor = $attributes[2];
//				$WHERE = "LESSONDATE=" . "TO_DATE('" . $date . "', 'DD-MON-YY')" .
//				" AND LESSONTIME=" . "TO_TIMESTAMP('" . $time . "', 'DD-MON-YY HH:MI:SS.FF6 AM')" .
//				" AND INSTRUCTORNAME='" . $instructor . "'";

                $deleteTakes = "DELETE FROM Takes WHERE LESSONDATE = TO_DATE(:lesson_date, 'DD-MON-YY') AND LESSONTIME = TO_TIMESTAMP(:lesson_time, 'DD-MON-YY HH:MI:SS.FF6 AM') AND INSTRUCTORNAME = :instructor";
                $deleteLesson = "DELETE FROM Lesson WHERE LESSONDATE = TO_DATE(:lesson_date, 'DD-MON-YY') AND LESSONTIME = TO_TIMESTAMP(:lesson_time, 'DD-MON-YY HH:MI:SS.FF6 AM') AND INSTRUCTORNAME = :instructor";

                executeSQLWithBinding($deleteTakes, array(':lesson_date' => $date, ':lesson_time' => $time, ':instructor' => $instructor));
                executeSQLWithBinding($deleteLesson, array(':lesson_date' => $date, ':lesson_time' => $time, ':instructor' => $instructor));
//				executePlainSQL("DELETE FROM Takes WHERE " . $WHERE);		// remove tuple from child table first
//				executePlainSQL("DELETE FROM Lesson WHERE " . $WHERE);	// remove tuple from target table

				// print out confirmation
				$formattedTime = $attributes[3];
				echo "<p style='color: blue;'>Removing: $date $formattedTime $instructor</p>";
			}
			oci_commit($db_conn);
			header("refresh: 1"); 
		}
	}

	if (isset($_POST['removeSubmit'])) {
		if (connectToDB()) {
			handleRemoveRequest();
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
			//echo "<br>running ".$cmdstr."<br>";
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
