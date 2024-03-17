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

// Set some parameters
// Database access configuration
$config["dbuser"] = "ora_cwl";	// change "cwl" to your own CWL !!!!!!
$config["dbpassword"] = "aSID";	// change to 'a' + your student number !!!!!
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()
$success = true;	// keep track of errors so page redirects only if there are no errors
$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>Lingo Exchange</title>
</head>
<style>
    table, th, td {border: 1px solid; padding: 5px; border-collapse: collapse;}
    th {text-align: center}
</style>
<body>
	<h1 style="text-align:center">Projection Page</h1>
    <h2>View Tables & Attributes</h2>
	<p>View all the tables in our database (for the current user).</p>
	<form method="GET" action="projection.php">
		<input type="hidden" id="viewTablesGetRequest" name="viewTablesGetRequest">
		<input type="submit" value="View Tables" name="viewTables"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
    <p>View all the attributes for a specific table in our database (for the current user).</p>
	<form method="GET" action="projection.php">
		<input type="hidden" id="viewAttributesGetRequest" name="viewAttributesGetRequest">
		Table Name: <input type="text" name="table"> <br /><br />
		<input type="submit" value="View Attributes" name="viewAttributes"></p>
	</form>
    <hr />
    <h2>Project Attributes</h2>
    <p>Project the specified attributes for a specific table in our database (for the current user).<br>
       Please provide the desired attribute names as a <b>comma-separated list</b>.<br></p>
	<form method="GET" action="projection.php">
		<input type="hidden" id="projectionGetRequest" name="projectionGetRequest">
		Table Name: <input type="text" name="table"> <br /><br />
        Attributes: <input type="text" name="attributes"> <br /><br />
		<input type="submit" value="Project Attributes" name="projectAttributes"></p>
	</form>
	<hr />

	<?php
	// The following code will be parsed as PHP
	function debugAlertMessage($message)
	{
		global $show_debug_alert_messages;

		if ($show_debug_alert_messages) {
			echo "<script type='text/javascript'>alert('" . $message . "');</script>";
		}
	}

	function connectToDB()
	{
		global $db_conn;
		global $config;

		// Your username is ora_(CWL_ID) and the password is a(student number). For example,
		// ora_platypus is the username and a12345678 is the password.
		// $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;
		}
	}

	function disconnectFromDB()
	{
		global $db_conn;
		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}

	function executePlainSQL($cmdstr)
	{ //takes a plain (no bound variables) SQL command and executes it
		//echo "<br>running ".$cmdstr."<br>";
		global $db_conn;
		$response = array();
		$response["success"] = True;
		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
		if (!$statement) {
			// echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			// echo htmlentities($e['message']);
			$response["success"] = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			// echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
			// echo htmlentities($e['message']);
			$response["success"] = False;
		}
		$response["statement"] = $statement;
		return $response;
	}

	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn;
		$response = array();
		$response["success"] = True;
		$statement = oci_parse($db_conn, $cmdstr);
		if (!$statement) {
			// echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			// echo htmlentities($e['message']);
			$response["success"] = False;
		}
		foreach ($list as $tuple) {
			foreach ($tuple as $bind => $val) {
				//echo $val;
				//echo "<br>".$bind."<br>";
				oci_bind_by_name($statement, $bind, $val);
				unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
			}
			$r = oci_execute($statement, OCI_DEFAULT);
			if (!$r) {
				// echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				// echo htmlentities($e['message']);
				// echo "<br>";
				$response["success"] = False;
			}
		}
		$response["statement"] = $statement;
		return $response;
	}

	function printViewTables($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing all tables:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>TableName</th>
			 </tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			// echo <tr> $row[0]  <tr>;
			 echo "<tr>
			 			<td>" . $row[0] . "</td>
					</tr>";
		}
		echo "</table>";
	}

	function printViewAttributes($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing attributes of a specified table:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>TableName</th>
				<th>ColumnName</th>
			 </tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			// echo <tr> $row[0]  <tr>;
			 echo "<tr>
			 			<td>" . $row[0] . "</td>
			 			<td>" . $row[1] . "</td>
					</tr>";
		}
		echo "</table>";
	}

    function printProjectAttributes($result, $attributes, $count)
	{ //prints results from a select statement
		echo "<br>Results for projecting specified attributes of a specified table:<br><br>";
		echo "<table>";
        $header = "<tr>";
        foreach (explode(',', $attributes) as $value) {
            $header .= "<th>" . $value . "</th>";
        }
        $header .= "</tr>";
        echo $header;
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			$display = "<tr>";
            for ($i=0; $i < $count; $i++) {
                $display .= "<td>" . $row[$i] . "</td>";
            }
            $display .= "</tr>";
            echo $display;
		}
		echo "</table>";
	}

	function handleViewTablesGetRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT table_name FROM user_tables");
		printViewTables($result["statement"]);
	}


	function handleViewAttributesGetRequest()
	{
		global $db_conn;

		$tuple = array(
			":bind1" => $_GET['table'],
		);
		$alltuples = array(
			$tuple
		);
		$result = executeBoundSQL("SELECT table_name, column_name 
								   FROM USER_TAB_COLUMNS T
								   WHERE table_name=:bind1", $alltuples);
		oci_commit($db_conn);
        if ($result["success"] == TRUE) {
			printViewAttributes($result["statement"]);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to show the attributes for the specified table :( <br>
					Make sure that you've entered a valid table name.</font><p>";
		}
	}

    function handleProjectAttributesGetRequest()
	{
		global $db_conn;

        
        $table = $_GET['table'];
        $attributes = $_GET['attributes'];
        $count = count(explode(',', $attributes));
        $query  = "SELECT " . $attributes . " FROM " . $table;
		$result = executePlainSQL($query);

		oci_commit($db_conn);
        if ($result["success"] == TRUE) {
			printProjectAttributes($result["statement"], $attributes, $count);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to project the specified attributes for the specified table :( <br>
					Make sure that you've entered a valid table name and a comma-separated list of attributes.</font><p>";
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('viewTables', $_GET)) {
				handleViewTablesGetRequest();
			} elseif (array_key_exists('viewAttributes', $_GET)) {
				handleViewAttributesGetRequest();
            } elseif (array_key_exists('projectAttributes', $_GET)) {
				handleProjectAttributesGetRequest();
            }
			disconnectFromDB();
		}
	}

	if (isset($_GET['viewTablesGetRequest']) || isset($_GET['viewAttributesGetRequest']) || isset($_GET['projectionGetRequest'])) {
		handleGETRequest();
	} 
	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>