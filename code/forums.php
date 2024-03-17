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
	<h1 style="text-align:center">Forums Management Page</h1>
	<h2>Join a Forum</h2>
	<p>Join an existing forum!</p>
	<form method="POST" action="forums.php">
		<input type="hidden" id="joinForumInsertRequest" name="joinForumInsertRequest">
		User ID: <input type="text" name="uid"> <br /><br />
		Forum URL: <input type="text" name="url"> <br /><br />
		<input type="submit" value="Join Forum" name="joinForum"></p>
	</form>
	<hr />

	<h2>Leave a Forum</h2>
	<p>Leave a forum that you previously joined.</p>
	<p></p>
	<form method="POST" action="forums.php">
		<input type="hidden" id="leaveForumDeleteRequest" name="leaveForumDeleteRequest">
		User ID: <input type="text" name="uid"> <br /><br />
		Forum URL: <input type="text" name="url"> <br /><br />
		<input type="submit" value="Leave Forum" name="leaveForum"></p>
	</form>
	<hr />

	<h2>View Forums</h2>
	<p>View all the forums you've joined.</p>
	<form method="GET" action="forums.php">
		<input type="hidden" id="viewMyForumsGetRequest" name="viewMyForumsGetRequest">
		User ID: <input type="text" name="uid"> <br /><br />
		<input type="submit" value="View My Forums" name="viewMyForums"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>View all of our existing forums.</p>
	<form method="GET" action="forums.php">
		<input type="hidden" id="viewForumsGetRequest" name="viewForumsGetRequest">
		<input type="submit" value="View Forums" name="viewForums"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>
		View a <b>filtered</b> list of existing forums.
		<br>
		Select the attributes you want to filter by specifing the desired attribute value.
		<br>
		Leave the attribute value blank if you do not want to filter by that attribute.
	</p>
	<form method="GET" action="forums.php">
		<input type="hidden" id="filterForumsGetRequest" name="filterForumsGetRequest">
		<table>
			<tr><th>Attribute Name</th><th>Attribute Value</th></tr>
			<tr><td>URL</td><td><input type="text" name="url"></td></tr>
			<tr><td>Status</td><td><input type="text" name="status"></td></tr>
			<tr><td>Title</td><td><input type="text" name="title"></td></tr>
		</table>
		<br>
		<input type="submit" value="Filter Forums" name="filterForums"></p>
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

	function printViewForums($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing all forums:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>URL</th>
				<th>Status</th>
				<th>Title</th>
			 </tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			// echo <tr> $row[0]  <tr>;
			 echo "<tr>
			 			<td>" . $row[0] . "</td>
			 			<td>" . $row[1] . "</td>
						<td>" . $row[2] . "</td>
					</tr>";
		}
		echo "</table>";
	}

	function printFilteredForums($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing filtered forums:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>URL</th>
				<th>Status</th>
				<th>Title</th>
			 </tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			// echo <tr> $row[0]  <tr>;
			 echo "<tr>
			 			<td>" . $row[0] . "</td>
			 			<td>" . $row[1] . "</td>
						<td>" . $row[2] . "</td>
					</tr>";
		}
		echo "</table>";
	}

	function printViewMyForums($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing your forums:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>URL</th>
				<th>Title</th>
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

	function handleViewForumsGetRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM Forum4");
		printViewForums($result["statement"]);
	}

	function handleViewMyForumsGetRequest()
	{
		global $db_conn;

		$tuple = array(
			":bind1" => $_GET['uid'],
		);
		$alltuples = array(
			$tuple
		);
		$result = executeBoundSQL("SELECT * FROM Participates WHERE UserID=:bind1", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			printViewMyForums($result["statement"]);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to show your forums :( <br>
					 Make sure that you've entered a valid User ID (which should be an integer).</font><p>";
		}
	}

	function handleFilterForumsGetRequest()
	{
		global $db_conn;
		$count = 0; // 0 indicates that we're handling the first filtering condition; 
		$tuple = array();
		$query = "SELECT * FROM Forum4 WHERE";		
		if (!empty($_GET["url"]) && $count==0) {
			$query .= " URL=:bind1";
			$tuple[':bind1'] = $_GET["url"];
			$count = 1;
		} elseif(!empty($_GET["url"])) {
			$query .= " AND URL=:bind1";
			$tuple[':bind1'] = $_GET["url"];
		}
		if (!empty($_GET["status"]) && $count==0) {
			$query .= " Status=:bind2";
			$tuple[':bind2'] = $_GET["status"];
			$count = 1;
		} elseif(!empty($_GET["status"])) {
			$query .= " AND Status=:bind2";
			$tuple[':bind2'] = $_GET["status"];
		}
		if (!empty($_GET["title"]) && $count==0) {
			$query .= " Title=:bind3";
			$tuple[':bind3'] = $_GET["title"];
			$count = 1;
		} elseif(!empty($_GET["title"])) {
			$query .= " AND Title=:bind3";
			$tuple[':bind3'] = $_GET["title"];
		}
		$alltuples = array($tuple);
		$result = executeBoundSQL($query, $alltuples);
		oci_commit($db_conn);		
		if ($result["success"] == TRUE) {
			printFilteredForums($result["statement"]);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to show the filtered forums :( </font><p>";
		}
	}

	function handleJoinForumInsertRequest()
	{
		global $db_conn;

		// Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['uid'],
			":bind2" => $_POST['url']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Participates VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Your request was successfully processed :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to add you to a forum :( <br>
					Make sure that you've entered a valid User ID (which should be an integer) 
					and the URL to an <i>existing</i> Forum (use our view forums functionality to see what's available).</font><p>";
		}
	}
	
	function handleLeaveForumDeleteRequest()
	{
		global $db_conn;

		// Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['uid'],
			":bind2" => $_POST['url']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("DELETE FROM Participates WHERE UserID=:bind1 AND URL=:bind2", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Your request was successfully processed :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to remove you from a forum :( <br>
					Make sure that you've entered a valid User ID (which should be an integer).</font><p>";
		}
	}

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('joinForumInsertRequest', $_POST)) {
				handleJoinForumInsertRequest();
			} else if (array_key_exists('leaveForumDeleteRequest', $_POST)) {
				handleLeaveForumDeleteRequest();
			} 
			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('viewForums', $_GET)) {
				handleViewForumsGetRequest();
			} elseif (array_key_exists('viewMyForums', $_GET)) {
				handleViewMyForumsGetRequest();
			} elseif (array_key_exists('filterForums', $_GET)) {
				handleFilterForumsGetRequest();
			} 
			disconnectFromDB();
		}
	}

	if (isset($_POST['joinForum']) || isset($_POST['leaveForum'])) {
		handlePOSTRequest();
	} else if (isset($_GET['viewForumsGetRequest']) || isset($_GET['viewMyForumsGetRequest']) || isset($_GET['filterForumsGetRequest'])) {
		handleGETRequest();
	} 
	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>