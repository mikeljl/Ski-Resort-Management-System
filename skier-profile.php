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
    <a href="lessons.php">Manage Lessons</a>
    <a class="active" href="skier-profile.php">Skier Profile</a>
    <a href="environment.php">Environment</a>
    <a href="security.php">Rescue Team</a>
</div>

<h2>Update Skier Profile [UPDATE]</h2>
<form method="POST" action="skier-profile.php">
    Skier ID: <input type="text" name="upID" placeholder="Skier ID">
    <input type="submit" value="search skier" name="searchSubmit"><br /><br />
    <?php
    if (isset($_POST['searchSubmit'])) {
        if (connectToDB()) {
            handleSearchRequest();
            disconnectFromDB();
        }
    } else if (isset($_POST['updateSubmit'])) {
        if (connectToDB()) {
            handleUpdateRequest();
            disconnectFromDB();
        }
    }

    function handleSearchRequest() {
        // Check if ID exists
        $query = "SELECT * FROM SKIER WHERE SKIERID = :upID";
        $result = executeSQLWithBinding($query, array(':upID' => $_POST['upID']));
        $row = OCI_Fetch_Array($result, OCI_BOTH);
        if (is_null($row) || empty($row)) {
            $message = "No skier with ID " . $_POST['upID'] . " found, please try again.";
            echo "<script>alert('$message');</script>";
        }
        // if ID exists, display skier info
        else {
            echo "Skier ID [$row[0]] found, modify entries below to update their information<br/><br/>";
            echo "<input type='hidden' name='upid' value='$row[0]'>";
            echo "First Name: <input type='text' name='upfirst' value='$row[2]'>\t\t";
            echo "Email: <input type='email' name='upemail' value='$row[3]'><br/><br/>";
            echo "Last Name: <input type='text' name='uplast' value='$row[1]'>\t\t";
            echo "Phone Number: <input type='tel' name='upphone' value='$row[4]'> <br/><br/>";
            echo "<input type='submit' value='Update' name='updateSubmit'><br /><br />";
        }
    }

    function handleUpdateRequest() {
        global $db_conn, $success;
        $id =  $_POST['upid'];
        $firstname =  $_POST['upfirst'];
        $lastname =  $_POST['uplast'];
        $email =  $_POST['upemail'];
        $phone =  $_POST['upphone'];

        $q1 = "UPDATE skier SET FirstName = :firstname WHERE SkierID = :id";
        executeSQLWithBinding($q1, array(':firstname' => $firstname, ':id' => $id));

        $q2 = "UPDATE skier SET LastName = :lastname WHERE SkierID = :id";
        executeSQLWithBinding($q2, array(':lastname' => $lastname, ':id' => $id));

        $q3 = "UPDATE skier SET Email = :email WHERE SkierID = :id";
        executeSQLWithBinding($q3, array(':email' => $email, ':id' => $id));

        $q4 = "UPDATE skier SET PhoneNumber = :phone WHERE SkierID = :id";
        executeSQLWithBinding($q4, array(':phone' => $phone, ':id' => $id));

        echo ($success == False) ?
            "<p style='color: blue;'>Update failed - the email and phone number must be unique</p>" :
            "<p style='color: blue;'>Update successful</p>";
        oci_commit($db_conn);
    }
    ?>

</form>
<hr />

<h2>Assign Ski Pass to Skier [INSERT]</h2>
<p>Skiers and snowboarders with a resort coupon can redeem a free ski pass</p>

<table style="border: 1px solid black; border-collapse: collapse;">
    <thead>
    <tr>
        <th style='border: 1px solid black; padding: 8px;'>Pass Type</th>
        <th style='border: 1px solid black; padding: 8px;'>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Daily</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective for one day</td>
    </tr>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Weekend</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective for a weekend (Saturday and Sunday)</td>
    </tr>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Weekly</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective for 7 consecutive days</td>
    </tr>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Holiday</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective from December 25 to January 5th, 2024</td>
    </tr>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Seasonal</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective for an entire ski season, not eligible for use on Blackout Days</td>
    </tr>
    <tr>
        <td style='border: 1px solid black; padding: 8px;'>Early Bird</td>
        <td style='border: 1px solid black; padding: 8px;'>Effective for an entire ski season including early opening dates, not eligible for use on Blackout Days</td>
    </tr>
    </tbody>
</table>
<br>

<form method="POST" action="skier-profile.php">
    Skier: <input type="text" name="insID" placeholder="Enter Skier ID">
    Pass Type: <select name="insType">
        <option value='Daily'>Daily</option>
        <option value='Weekend'>Weekend</option>
        <option value='Weekly'>Weekly</option>
        <option value='Holiday'>Holiday</option>
        <option value='Seasonal'>Seasonal</option>
        <option value='Early Bird'>Early Bird</option>
    </select>
    <input type="submit" value="Assign Pass" name="assignSubmit">
</form>

<hr/>
<h2>Messages</h2>

<?php

function handleAssignRequest() {
    $queryCheckSkier = "SELECT * FROM SKIER WHERE SKIERID = :insID";
    $result = executeSQLWithBinding($queryCheckSkier, array(':insID' => $_POST['insID']));
    $row = OCI_Fetch_Array($result, OCI_BOTH);
    if (is_null($row) || empty($row)) {
        $message = "No skier with ID " . $_POST['insID'] . " found, please try again.";
        echo "<script>alert('$message');</script>";
    }
    // if ID exists, assign ski pass to the skier
    else {
        global $db_conn;

        // == INSERT into passtype ==
        $id = $_POST['insID'];
        $type = $_POST['insType'];
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');
        switch ($type) {
            case 'Daily':
                $startDate = date('Y-m-d');
                $endDate = $startDate;
                break;

            case 'Weekend':
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime('+1 day'));
                break;

            case 'Weekly':
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime('+7 days'));
                break;

            case 'Holiday':
                $startDate = date('Y-m-d', strtotime('2024-12-25'));
                $endDate = date('Y-m-d', strtotime('2025-01-05'));
                break;

            case 'Seasonal':
                $startDate = date('Y-m-d', strtotime('2024-11-20'));
                $endDate = date('Y-m-d', strtotime('2025-03-31'));
                break;

            case 'Early Bird':
                $startDate = date('Y-m-d', strtotime('2024-12-25'));
                $endDate = date('Y-m-d', strtotime('2025-03-31'));
                break;
        }

        // check if pass type already exists
        $sqlCheckPass = "SELECT * FROM PASSTYPE WHERE TYPE = :type AND STARTDATE = TO_DATE(:startDate, 'YYYY-MM-DD')";
        $params = array(
            ':type' => $type,
            ':startDate' => $startDate
        );
        $result = executeSQLWithBinding($sqlCheckPass, $params);
        $row = OCI_Fetch_Array($result, OCI_BOTH);
        // if pass type doesn't exist, create new pass type (otherwise do nothing)
        if (is_null($row) || empty($row)) {
            $query1 = "INSERT INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES (:type, TO_DATE(:startDate, 'YYYY-MM-DD'), TO_DATE(:endDate, 'YYYY-MM-DD'), 'Active')";
            $params1 = array(
                ':type' => $type,
                ':startDate' => $startDate,
                ':endDate' => $endDate
            );
            executeSQLWithBinding($query1, $params1);
        }

        // == INSERT into skipass ==
        $passnumber = rand();
        $query2 = "INSERT INTO SkiPass (PassNumber, StartDate, Type) VALUES (:passnumber, TO_DATE(:startDate, 'YYYY-MM-DD'), :type)";
        $params2 = array(
            ':passnumber' => $passnumber,
            ':startDate' => $startDate,
            ':type' => $type
        );
        executeSQLWithBinding($query2, $params2);

        // == INSERT into buys ==
        $query3 = "INSERT INTO Buys (SkierID, PassNumber) VALUES (:id, :passnumber)";
        $params3 = array(
            ':id' => $id,
            ':passnumber' => $passnumber
        );
        executeSQLWithBinding($query3, $params3);

        oci_commit($db_conn);

        // print out confirmation message
        echo "<p style='color: blue;'>Successfully assigned skier ID [$id] with a $type pass, valid from $startDate to $endDate</p>";
    }
}
// ================================

if (isset($_POST['assignSubmit'])) {
    if (connectToDB()) {
        handleAssignRequest();
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