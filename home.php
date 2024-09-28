<!-- Test Oracle file for UBC CPSC304
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  Modified by Jason Hall (23-09-20)
  This file shows the very basics of how to execute PHP commands on Oracle.
  Specifically, it will drop a table, create a table, insert values update
  values, and then query for values
  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up All OCI commands are
  commands to the Oracle libraries. To get the file to work, you must place it
  somewhere where your Apache server can run it, and you must rename it to have
  a ".php" extension. You must also change the username and password on the
  oci_connect below to be your ORACLE username and password
-->

<?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require "db.php";

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ski Resort Management System</title>
    <style>
        .title {
            text-align: center;
            font-size: 36px;
            margin-top: 50px;
        }
    </style>
</head>

<body>
<h1 class="title">Ski Resort Management System</h1>
</body>


<head>
	<title>Ski Resort System</title>
    <form method="GET" action="lessons.php">
        <input type="submit" value="Manager Lesson">
    </form>
    <form method="GET" action="skier-profile.php">
        <input type="submit" value="Skier Profile">
    </form>
    <form method="GET" action="environment.php">
        <input type="submit" value="Wildlife and Incidents">
    </form>
    <form method="GET" action="security.php">
        <input type="submit" value="Rescue Team and Location">
    </form>

</head>

<body>

<hr />
<h2>View Data</h2>
<h3>Slope</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Slope:</p>
    <input type="checkbox" id="SlopeName" name="attributes[]" value="SlopeName">
    <label id="SlopeNameSlope" for="SlopeNameSlope">Slope Name</label><br>

    <input type="checkbox" id="TerrainType" name="attributes[]" value="TerrainType">
    <label for="TerrainType">Terrain Type</label><br>

    <input type="checkbox" id="Status" name="attributes[]" value="Status">
    <label for="Status">Status</label><br>

    <input type="checkbox" id="Length" name="attributes[]" value="Length">
    <label for="Length">Length</label><br>

    <input type="checkbox" id="TeamNumber" name="attributes[]" value="TeamNumber">
    <label for="TeamNumber">Team Number</label><br>

    <input type="checkbox" id="LiftID" name="attributes[]" value="LiftID">
    <label for="LiftID">Lift ID</label><br>

    <input type="hidden" name="selectAttributes" value="Select Attributes">
    <input type="submit" value="View">
</form>

<h3>Incidents</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Incidents:</p>
    <input type="checkbox" id="IncidentID" name="Incidentsattributes[]" value="IncidentID">
    <label for="IncidentID">Incident ID</label><br>

    <input type="checkbox" id="IncidentDate" name="Incidentsattributes[]" value="IncidentDate">
    <label for="IncidentDate">Incident Date</label><br>

    <input type="checkbox" id="Description" name="Incidentsattributes[]" value="Description">
    <label for="Description">Description</label><br>

    <input type="checkbox" id="SlopeName" name="Incidentsattributes[]" value="SlopeName">
    <label  id="SlopeNameIncident" for="SlopeNameIncident">Slope Name</label><br>

    <input type="checkbox" id="SkierID" name="Incidentsattributes[]" value="SkierID">
    <label for="SkierID">Skier ID</label><br>

    <input type="hidden" name="selectAttributesIncidents" value="Select Attributes">
    <input type="submit" value="View">
</form>

<h3>Weather Conditions</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Weather Conditions:</p>
    <input type="checkbox" id="Time" name="WeatherAttributes[]" value="Time">
    <label for="Time">Time</label><br>

    <input type="checkbox" id="SlopeName" name="WeatherAttributes[]" value="SlopeName">
    <label for="SlopeName">Slope Name</label><br>

    <input type="checkbox" id="WindSpeed" name="WeatherAttributes[]" value="WindSpeed">
    <label id="WindSpeedWC" for="WindSpeedWC" >Wind Speed</label><br>

    <input type="hidden" name="selectWeatherAttributes" value="Select Attributes">
    <input type="submit" value="View">
</form>

<h3>Wind Speed Information</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Wind Speed Information:</p>
    <input type="checkbox" id="WindSpeed" name="WindSpeedAttributes[]" value="WindSpeed">
    <label id="WindSpeedWSI" for="WindSpeedWSI">Wind Speed</label><br>

    <input type="checkbox" id="Temperature" name="WindSpeedAttributes[]" value="Temperature">
    <label id="TemperatureWSI" for="TemperatureWSI">Temperature</label><br>

    <input type="checkbox" id="AvalancheRiskLevel" name="WindSpeedAttributes[]" value="AvalancheRiskLevel">
    <label for="AvalancheRiskLevel">Avalanche Risk Level</label><br>

    <input type="hidden" name="selectWindSpeedAttributes" value="Select Attributes">
    <input type="submit" value="View">
</form>

<h3>Temperature Information</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Temperature Information:</p>
    <input type="checkbox" id="Temperature" name="TemperatureAttributes[]" value="Temperature">
    <label id="TemperatureTI" for="TemperatureTI">Temperature</label><br>

    <input type="checkbox" id="Precipitation" name="TemperatureAttributes[]" value="Precipitation">
    <label id = "PrecipitationTI" for="PrecipitationTI">Precipitation</label><br>

    <input type="hidden" name="selectTemperatureAttributes" value="Select Attributes">
    <input type="submit" value="View">
</form>

<h3>Precipitation Information</h3>
<form method="POST" action="home.php">
    <p>Select attributes to view from Precipitation Information:</p>
    <input type="checkbox" id="Precipitation" name="PrecipitationAttributes[]" value="Precipitation">
    <label id = "PrecipitationPI" for="PrecipitationPI">Precipitation</label><br>

    <input type="checkbox" id="SnowType" name="PrecipitationAttributes[]" value="SnowType">
    <label for="SnowType">Snow Type</label><br>

    <input type="checkbox" id="SnowDepth" name="PrecipitationAttributes[]" value="SnowDepth">
    <label for="SnowDepth">Snow Depth</label><br>

    <input type="hidden" name="selectPrecipitationAttributes" value="Select Attributes">
    <input type="submit" value="View">
</form>






<hr />

    <h2>Search Ski Slopes</h2>
    <form method="POST" action="home.php">
        <label for="conditions">Enter your conditions:</label><br>
        <textarea id="conditions" name="conditions" rows="4" cols="50" placeholder="e.g., Length=500 AND Status='Open'"></textarea><br>
        <input type="submit" value="Search" name="searchSlopes">
    </form>

<hr />

    <h2>Incidents by Ski Slope</h2>

    <form method="GET" action="home.php">
        <input type="hidden" id="showIncidentsPerSlopeRequest" name="showIncidentsPerSlopeRequest">
        <input type="submit" value="Show Slope Incidents" name="showIncidentsPerSlope">
    </form>

    <form method="POST" action="home.php">
        <input type="hidden" id="mostIncidents" name="mostIncidents">
        <p><input type="submit" value="Most Incident Slope" name="most"></p>
    </form>

    <form method="POST" action="home.php">
        <input type="hidden" id="leastIncidents" name="leastIncidents">
        <p><input type="submit" value="Least Incident Slope" name="least"></p>
    </form>

    <hr />

    <h2>Find Slope with Max Average Precipitation</h2>
    <form method="POST" action="home.php">
        <input type="submit" name="findMaxAvgPrecipitation" value="Find Slope with Max Average Precipitation">
    </form>

<hr />


</html>

	<?php

    $PrecipitationAttributes = ['Precipitation', 'SnowType', 'SnowDepth'];
    $TemperatureAttributes = ['Temperature', 'Precipitation'];
    $WindSpeedAttributes = ['WindSpeed', 'Temperature', 'AvalancheRiskLevel'];
    $WeatherAttributes = ['Time', 'SlopeName', 'WindSpeed'];
    $IncidentsAttributes = ['IncidentID', 'IncidentDate', 'Description', 'SlopeName', 'SkierID'];
    $SkiSlopeAttributes = ['SlopeName', 'TerrainType', 'Status', 'Length', 'TeamNumber', 'LiftID'];

    function findSlopeWithMaxAvgPrecipitation() {
        $query = "
    WITH Temp AS (
        SELECT 
            WC.SlopeName AS \"SlopeName\", 
            AVG(TI.Precipitation) AS \"AvgPrecipitation\"
        FROM 
            WeatherCondition WC, WindSpeedInformation WSI, TemperatureInformation TI
        WHERE 
            WC.WindSpeed = WSI.WindSpeed 
            AND WSI.Temperature = TI.Temperature
        GROUP BY 
            WC.SlopeName
    )
    SELECT 
        T.\"SlopeName\", 
        T.\"AvgPrecipitation\" 
    FROM 
        Temp T
    WHERE 
        T.\"AvgPrecipitation\" = (
            SELECT MAX(Temp.\"AvgPrecipitation\") FROM Temp
        )";

        global $success;
        $statement = executePlainSQL($query);
        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }
        $row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS);
        if ($row) {
            echo "<h2>Slope with Max Average Precipitation</h2>";
            echo "<p>Slope with Max Average: " . htmlspecialchars($row['SlopeName']) .
                " - Average Precipitation: " . htmlspecialchars($row['AvgPrecipitation']) . "</p>";
        } else {
            echo "<p>No data found</p>";
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

    function searchSkiSlopesWithUserConditions() {
        global $success;

        $userConditions = $_POST['conditions'];
        $disallowed = array("UPDATE", "INSERT", "DELETE", "TRUNCATE", "DROP", "CREATE", "ALTER");
        foreach ($disallowed as $keyword) {
            if (stripos($userConditions, $keyword) !== false) {
                echo '<script>alert("Invalid input");</script>';
                return;
            }
        }

        $query = "SELECT * FROM SkiSlope WHERE " . $userConditions;
        $result = executePlainSQL($query);

        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

        echo "<h2>Search Results</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Slope Name</th><th>Terrain Type</th><th>Status</th><th>Length</th><th>Team Number</th><th>Lift ID</th></tr>";

        while ($row = oci_fetch_array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr>";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    function handleMostIncidentsRequest() {
        global $success;

        $sql = "SELECT SlopeName, COUNT(*) AS IncidentCount FROM IncidentsOccurs GROUP BY SlopeName ORDER BY IncidentCount DESC FETCH FIRST 1 ROWS ONLY";
        $result = executePlainSQL($sql);
        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

        if (!$result) {
            echo "<p>error in retrieving least incident slope info</p>";
            return;
        }

        if ($row = OCI_Fetch_Array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<h2>Slope with Most Incidents:</h2>";
            echo "<p>Slope: " . htmlspecialchars($row["SLOPENAME"]) . " - Incidents: " . htmlspecialchars($row["INCIDENTCOUNT"]) . "</p>";
        } else {
            echo "<p>no incidents data</p>";
        }
    }
    function handleLeastIncidentsRequest() {
        global $success;

        $sql = "SELECT SlopeName, COUNT(*) AS IncidentCount FROM IncidentsOccurs GROUP BY SlopeName ORDER BY IncidentCount ASC FETCH FIRST 1 ROWS ONLY";
        $result = executePlainSQL($sql);
        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

        if (!$result) {
            echo "<p>error in retrieving least incident slope info</p>";
            return;
        }

        if ($row = OCI_Fetch_Array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<h2>Slope with Least Incidents:</h2>";
            echo "<p>Slope: " . htmlspecialchars($row["SLOPENAME"]) . " - Incidents: " . htmlspecialchars($row["INCIDENTCOUNT"]) . "</p>";
        } else {
            echo "<p>no incidents data</p>";
        }
    }

    function handleIncidentsPerSlopeRequest() {
        global $success;

        $sql = "SELECT SlopeName, COUNT(*) AS IncidentCount FROM IncidentsOccurs GROUP BY SlopeName ORDER BY IncidentCount DESC";

        $result = executePlainSQL($sql);
        if (!$success) {
            echo '<script>alert("Error executing query");</script>';
            return;
        }

        echo "<h2>Incident Counts per Ski Slope</h2>";
        echo "<table>";
        echo "<tr><th>Slope Name</th><th>Incident Count</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC+OCI_RETURN_NULLS)) {
            echo "<tr><td>" . htmlspecialchars($row["SLOPENAME"]) . "</td><td>" . htmlspecialchars($row["INCIDENTCOUNT"]) . "</td></tr>";
        }

        echo "</table>";
    }
    function debugAlertMessage($message)
    {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function executePlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
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

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
        global $TemperatureAttributes;
        global $WindSpeedAttributes;
        global $WeatherAttributes;
        global $IncidentsAttributes;
        global $SkiSlopeAttributes;
        global $PrecipitationAttributes;

		if (connectToDB()) {
			if (array_key_exists('mostIncidents', $_POST)) {
                handleMostIncidentsRequest();
            } else if (array_key_exists('leastIncidents', $_POST)) {
                handleLeastIncidentsRequest();
            } else if (array_key_exists('searchSlopes', $_POST)) {
                searchSkiSlopesWithUserConditions();
            }  else if (array_key_exists('findMaxAvgPrecipitation', $_POST)) {
                findSlopeWithMaxAvgPrecipitation();
            } else if (array_key_exists('selectAttributes', $_POST)) {
                displaySelectedAttributes($SkiSlopeAttributes, $_POST['attributes'], 'SkiSlope');
            }
            else if (array_key_exists('selectAttributesIncidents', $_POST)) {
                displaySelectedAttributes($IncidentsAttributes, $_POST['Incidentsattributes'], 'IncidentsOccurs');
            }
            else if (array_key_exists('selectWeatherAttributes', $_POST)) {
                displaySelectedAttributes($WeatherAttributes, $_POST['WeatherAttributes'], 'WeatherCondition');
            }
            else if (array_key_exists('selectWindSpeedAttributes', $_POST)) {
                displaySelectedAttributes($WindSpeedAttributes, $_POST['WindSpeedAttributes'], 'WindSpeedInformation');
            }
            else if (array_key_exists('selectTemperatureAttributes', $_POST)) {
                displaySelectedAttributes($TemperatureAttributes, $_POST['TemperatureAttributes'], 'TemperatureInformation');
            } else if (array_key_exists('selectPrecipitationAttributes', $_POST)) {
                displaySelectedAttributes($PrecipitationAttributes, $_POST['PrecipitationAttributes'], 'PrecipitationInformation');
            }

			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('showIncidentsPerSlopeRequest', $_GET)) {
                handleIncidentsPerSlopeRequest();
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


    if (isset($_POST['most']) ||
        isset($_POST['least']) ||
        isset($_POST['searchSlopes']) ||
        isset($_POST['selectAttributes']) ||
        isset($_POST['selectAttributesIncidents']) ||
        isset($_POST['findMaxAvgPrecipitation']) ||
        isset($_POST['selectWeatherAttributes']) ||
        isset($_POST['selectWindSpeedAttributes']) ||
        isset($_POST['selectTemperatureAttributes']) ||
        isset($_POST['selectPrecipitationAttributes'])

    ) {
		handlePOSTRequest();
	} else if (isset($_GET['showIncidentsPerSlopeRequest'])) {
		handleGETRequest();
	}
    // End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
