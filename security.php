<?php
// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Retrieve configuration variables for connecting to DB
require "db.php";

// Set some parameters
$success = true;    // keep track of errors so page redirects only if there are no errors
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
    <a href="environment.php">Environment</a>
    <a class="active" href="security.php">Rescue Team</a>
</div>

<h1> Rescue Team and Rescue Location </h1>
<h2> View Rescue Team </h2>
<!--RescueTeam (TeamNumber, Location, TeamLeader, NumMembers)-->
<form method="POST" action="security.php">
    <p>Select attributes to view from Rescue Team:</p>
    <input type="checkbox" id="TeamNumber" name="attributes[]" value="TeamNumber">
    <label for="TeamNumber">Team Number</label><br>

    <input type="checkbox" id="Location" name="attributes[]" value="Location">
    <label for="Location">Location</label><br>

    <input type="checkbox" id="TeamLeader" name="attributes[]" value="TeamLeader">
    <label for="TeamLeader">Team Leader</label><br>

    <input type="checkbox" id="NumMembers" name="attributes[]" value="NumMembers">
    <label for="NumMembers">Number of Members</label><br>

    <input type="hidden" name="selectAttributesRTeam" value="Select Attributes">
    <input type="submit" value="View">
</form>

<hr />

<h2> View Rescue Location </h2>
<!--RescueLocation (Location, MaxResponseTime, Equipment)-->
<form method="POST" action="security.php">
    <p>Select attributes to view from Rescue Location:</p>
    <input type="checkbox" id="Location" name="Locationattributes[]" value="Location">
    <label for="Location">Location</label><br>

    <input type="checkbox" id="MaxResponseTime" name="Locationattributes[]" value="MaxResponseTime">
    <label for="MaxResponseTime">Maximum Response Time</label><br>

    <input type="checkbox" id="Equipment" name="Locationattributes[]" value="Equipment">
    <label for="Equipment">Equipment</label><br>

    <input type="hidden" name="selectAttributesRLocation" value="Select Attributes">
    <input type="submit" value="View">
</form>

<hr />

<h2> Find Rescue Team with Selected Equipments </h2>
<form method="POST" action="security.php">
    Equipment: <select name="eqpType">
        <option value='First Aid Kit'>First Aid Kit</option>
        <option value='Radio Communication Set'>Radio Communication Set</option>
        <option value='GPS Devices'>GPS Devices</option>
        <option value='Emergency Flares'>Emergency Flares</option>
        <option value='Avalanche Probes'>Avalanche Probes</option>
    </select>
    <input type="submit" value="Find Rescue Team" name="findRescueTeamWithEquip">
</form>

<hr />
<h2>Output </h2>



</html>
<?php

    $RescueLocationAttributes = ['Location', 'MaxResponseTime', 'Equipment'];
    $RescueTeamAttributes = ['TeamNumber', 'Location', 'TeamLeader', 'NumMembers'];
function findRescTeamWithEquip(){
    global $success;

    $equip = $_POST['eqpType'];

    $query = "
    SELECT rl.Equipment, rt.TeamNumber, rt.Location, rl.MaxResponseTime
    FROM RescueTeam rt, RescueLocation rl
    WHERE rl.Location = rt.Location AND rl.Equipment = :equip
    ORDER BY rl.MaxResponseTime DESC";

    $results = executeSQLWithBinding($query, array(':equip' => $equip));

    if (!$success) {
        echo '<script>alert("Error executing query");</script>';
        return;
    }

//        $row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS);
    echo "<h2>Search Results</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Equipment</th><th>TeamNumber</th><th>Location</th><th>MaxResponseTime</th><th>";

    While ($row = oci_fetch_array($results, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>";
        foreach ($row as $item){
            echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
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

// HANDLE POST ROUTES
function handlePOSTRequest()
{
    global $RescueLocationAttributes;
    global $RescueTeamAttributes;

    if (connectToDB()) {
        if (array_key_exists('findRescueTeamWithEquip', $_POST)) {
            findRescTeamWithEquip();
        } else if (array_key_exists('selectAttributesRTeam', $_POST)) {
            displaySelectedAttributes($RescueTeamAttributes, $_POST['attributes'], 'RescueTeam');
        } else if (array_key_exists('selectAttributesRLocation', $_POST)) {
            displaySelectedAttributes($RescueLocationAttributes, $_POST['Locationattributes'], 'RescueLocation');
        }
        disconnectFromDB();
    }
}
// Your function to execute SQL query might look something like this
function executeQuery($query) {
    global $db_conn; // Your database connection
    $statement = oci_parse($db_conn, $query);
    oci_execute($statement);
    return $statement;
}

if (isset($_POST['findRescueTeamWithEquip']) ||
    isset($_POST['selectAttributesRTeam']) ||
    isset($_POST['selectAttributesRLocation'])
) {
    handlePOSTRequest();
}
?>
</body>

</html>




