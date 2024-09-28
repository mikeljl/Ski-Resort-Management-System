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
	<title>Incident and Wildlife Monitoring </title>
</head>

<body>
<div class="navbar">
    <a href="home.php">Home</a>
    <a href="lessons.php">Manage Lessons</a>
    <a href="skier-profile.php">Skier Profile</a>
    <a href="security.php">Rescue Team</a>
    <a class="active" href="environment.php">Environment</a>
</div>

<h1> Wildlife Collection </h1>
<form method="POST" action="environment.php">
    <p>Select attributes to view from Wildlife:</p>
    <input type="checkbox" id="Species" name="attributes[]" value="Species">
    <label for="Species">Species</label><br>

    <input type="checkbox" id="LastObservedDate" name="attributes[]" value="LastObservedDate">
    <label for="LastObservedDate">Last Observed Date</label><br>

    <input type="hidden" name="selectAttributesSpecies" value="Select Attributes">
    <input type="submit" value="View">
</form>

<hr />

    <h2>Show Latest Date Species were Observed</h2>
    <form method="POST" action="environment.php">
        <input type="submit" name="findSpeciesLastObserved" value="Search">
    </form>
<hr />

    <h2>Threshold Species with Frequency </h2>
    <form method="POST" action="environment.php">
        Specie Observed more than: <input type="number" name="frequency" value="2"> times
        <input type="submit" name="thresholdSpeciesWithFrequency" value="Search">
    </form>
<hr />
<h2>All observed wildlife [DIVISION]</h2>
<p>Find slopes that has observed all selected wildlife</p>
<form method="POST" action="environment.php">
    <table style="border: 1px solid black; border-collapse: collapse;">
        <?php	displayAllWildlife(); ?>
    </table>
    <br>
    <input type="submit" value="Find slopes" name="divisionSubmit">
</form>

<hr />
    <h2>Output </h2>


</html>
	<?php

    $InhabitsAttributes = ['SlopeName', 'Species'];
    $WildlifeAttributes = ['Species', 'LastObservedDate'];


    function findSpeciesLastObserved(){
        $query = "
        SELECT
            I.SlopeName, I.Species, W.LastObservedDate
        FROM
            Wildlife W, Inhabits I
        WHERE
            W.Species = I.Species";

        global $success;
        $statement = executePlainSQL($query);
        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

        echo "<h2>Search Results</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Slope Name</th><th>Species</th><th>Last Observed Date</th><th>";

        while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    function thresholdSpeciesWithFrequency(){
        global $success;

        $frequency = $_POST['frequency'];
        $sql = "
        SELECT SlopeName, COUNT(Species)
        FROM Inhabits
        GROUP BY SlopeName
        HAVING COUNT(*)>= :frequency";

        $result = executeSQLWithBinding($sql, [":frequency" => $frequency]);

        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

//        $row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS);
        echo "<h2>Search Results</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Slope Name</th><th>Frequency</th><th>";

        $flag = 0;
        While ($row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $flag = 1;
            echo "<tr>";
            foreach ($row as $item){
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        if ($flag == 0) {
            echo "<p>No species has been observed over $frequency times</p>";
        }
    }

    function displaySelectedAttributes($validAttributes, $selectedAttributes, $table) {
        $safeAttributes = array_intersect($selectedAttributes, $validAttributes);

        if (empty($safeAttributes)) {
            echo '<script>alert("No attribute selected");</script>';
            return;
        }

        $query = "SELECT " . implode(", ", $safeAttributes) . " FROM " . $table;
        $executeResult = executePlainSQL($query);
        $allRows = oci_fetch_all($executeResult, $results, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);

        echo "<table border='1'>";
        echo "<tr>";
        foreach ($safeAttributes as $attribute) {
            echo "<th>" . htmlspecialchars($attribute) . "</th>";
        }
        echo "</tr>";

        if ($allRows > 0) {
            foreach ($results as $row) {
                echo "<tr>";
                foreach ($safeAttributes as $attribute) {
                    $data = $row[strtoupper($attribute)] ?? 'N/A';
                    echo "<td>" . htmlspecialchars($data) . "</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='" . count($safeAttributes) . "'>No data found</td></tr>";
        }
        echo "</table>";
    }

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
        $array = array_intersect($array, ["Brown Bear", "Elk", "Lynx", "Mountain Goat", "Snowshoe Hare"]);

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

    function debugAlertMessage($message) {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }
    function executePlainSQL($cmdstr)
    {
        // takes a plain (no bound variables) SQL command and executes it
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

    // HANDLE POST ROUTES
    function handlePOSTRequest()
    {
        global $InhabitsAttributes;
        global $WildlifeAttributes;

        if (connectToDB()) {
            if (array_key_exists('findSpeciesLastObserved', $_POST)) {
                findSpeciesLastObserved();
            } else if (array_key_exists('thresholdSpeciesWithFrequency', $_POST)) {
                thresholdSpeciesWithFrequency();
            } else if (array_key_exists('selectAttributesSpecies', $_POST)) {
                displaySelectedAttributes($WildlifeAttributes, $_POST['attributes'], 'Wildlife');
            }

            disconnectFromDB();
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
    // Your function to execute SQL query might look something like this
    function executeQuery($query) {
        global $db_conn; // Your database connection
        $statement = oci_parse($db_conn, $query);
        oci_execute($statement);
        return $statement;
    }

    if (isset($_POST['findSpeciesLastObserved']) ||
        isset($_POST['thresholdSpeciesWithFrequency']) ||
        isset($_POST['selectAttributesSpecies'])
    ) {
        handlePOSTRequest();
    } else if (isset($_GET['showIncidentsPerSlopeRequest'])) {
        handleGETRequest();
    } else if (isset($_POST['divisionSubmit'])) {
        if (connectToDB()) {
            handleDivisionRequest();
            disconnectFromDB();
        }
    }
    ?>
</body>

</html>