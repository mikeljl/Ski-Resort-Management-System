<?php
// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Retrieve configuration variables for connecting to DB
require "db.php";

// Set some parameters
$success = true;	// keep track of errors so page redirects only if there are no errors
$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())
?>

<html>

<head>
    <title>Wildlife</title>
</head>

<body>
<div class="navbar">
    <a href="home.php">Home</a>
    <a href="lessons.php">Manage Lessons</a>
    <a href="skier-profile.php">Skier Profile</a>
    <a href="environment.php">Environment</a>
    <a href="security.php">Rescue Team</a>
</div>

<h2>All observed wildlife [DIVISION]</h2>
<p>Find slopes that has observed all selected wildlife</p>
<form method="POST" action="wildlife.php">
    <table style="border: 1px solid black; border-collapse: collapse;">
        <?php	displayAllWildlife(); ?>
    </table>
    <br>
    <input type="submit" value="Find slopes" name="divisionSubmit">
</form>

<?php
// ====== HELPERS ======
function displayAllWildlife() {
    if (connectToDB()) {
        $result = queryAllWildlife();
        printAllWildlifeCheckbox($result);
        disconnectFromDB();
    }
}

// RETURNS a list of all wildlife
function queryAllWildlife() {
    $result = executePlainSQL("SELECT DISTINCT Species FROM Wildlife");
    return $result;
}

function printAllWildlifeCheckbox($result) {
    $style = "style='border: 1px solid black; padding: 8px;'";

    echo "<tr>
					<th $style>Species</th>
					</tr>";

    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
        echo "<tr>";
        echo "<td $style>" . $row["SPECIES"] . "</td>";
        echo "<td $style>" . "<input type='checkbox' name='formSpecies[]' value='" . $row["SPECIES"] . "'>" . "</td>";
        echo "</tr>";
    }
}

function handleDivisionRequest() {
    $array = $_POST['formSpecies'];

    if(empty($array)) {
        echo "<p style='color: blue;'>You didn't select any species.</p>";
    }
    else {
        $N = count($array);
        $condition ="";

        for($i=0; $i < $N; $i++) {
            if ($i == 0) {
                $condition = $condition . "w.species='$array[$i]'";
            }
            else {
                $condition = $condition . "or w.species='$array[$i]'";
            }
        }

        $query = "SELECT DISTINCT i1.SlopeName
						FROM Inhabits i1
						WHERE NOT EXISTS (
						(SELECT w.species FROM Wildlife w WHERE $condition) 
						MINUS 
						(SELECT i2.species FROM Inhabits i2 WHERE i1.slopename = i2.slopename))";

        $result = executePlainSQL($query);
        displayDivisionResult($result);
    }
}

function displayDivisionResult($result) {
    echo "<table>";
    echo "<tr><th>Slope Name</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>";
    }
    echo "</table>";
}

if (isset($_POST['divisionSubmit'])) {
    if (connectToDB()) {
        handleDivisionRequest();
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