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
$config["dbuser"] = "ora_cwl";			// change "cwl" to your own CWL
$config["dbpassword"] = "a12345678";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP
?>

<html>

<head>
	<title>Welcome to CS 304 Language Learning Platform</title>
</head>

<body>

	<h1>Welcome to Language Learning Platform</h1>
	<!-- // TODO: COMPLETE SUMMARY-->
	<p>TODO : Give summary of the project ... </p>

	<hr />

	<h2>Create New Account</h2>
	<p>CHOOSE A UNIQUE USER NAME (Leave a blank space between each part of the name) </p>
	<form method="POST" action="welcome.php">
		<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
		User Name : <input type="text" name="insName"> <br /><br />
		Age (between 1 and 150) : <input type="number" name="insAge" min="1" max="150"> <br /><br />
		Password: <input type="text" name="insPassword"> <br /><br />

		<input type="submit" value="Start Your Learning Journey" name="insertSubmit"></p>
	</form>

	<hr />

	<h2>Login</h2>
	<p>SIGN IN TO CONTINUE TO YOUR LANGUAGE LEARNING JOURNEY: HOME PAGE</p>
	<p> (The values are case sensitive and if you enter in the wrong case, access will be denied)</p>
	<form method="GET" action="welcome.php">
		<input type="hidden" id="accessAccountRequest" name="accessAccountRequest">
		User Name: <input type="text" name="getName"> <br /><br />
		Password: <input type="text" name="getPassword"> <br /><br />

		<input type="submit" value="Login" name="getLogin"></p>
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

	function executeBoundSQL($cmdstr, $list)
	{
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

		global $db_conn, $success;
		$statement = oci_parse($db_conn, $cmdstr);

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
			$success = False;
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
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				echo htmlentities($e['message']);
				echo "<br>";
				$success = False;
			}
		}
	}

	function printResult($result)
	{ //prints results from a select statement
		echo "<br>Retrieved data from table demoTable:<br>";
		echo "<table>";
		echo "<tr><th>ID</th><th>Name</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
		}

		echo "</table>";
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

	function handleInsertRequest()
	{
		global $db_conn;

		// Checking Missing Values required for Login
		if (empty($_POST['insName']) || empty($_POST['insPassword'])) {
			echo "<p><font color=red> <b>ERROR</b>: Missing values for User Name and/or Password.</font><p>";
			return;
		}

		$checkName = $_POST['insName'];
		$result = executePlainSQL("SELECT COUNT(UserID) FROM Learner_Consults WHERE userName = '$checkName'");
		$row = oci_fetch_row($result);

		if ($row[0] > 0) {
			echo "<p><font color=red> <b>ERROR</b>: User Name already in use. Please Select a new and unique User Name</font><p>";
			return;
		}

		//Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => time(),
			":bind2" => $_POST['insName'],
			":bind3" => $_POST['insAge'],
			":bind4" => $_POST['insPassword']
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("INSERT INTO Learner_Consults values (:bind1, :bind2, :bind3, :bind4, NULL)", $alltuples);
		echo "<p><font color=blue> <b>SUCCESS</b>: Account Created Successfully!!</font><p>";
		oci_commit($db_conn);
	}

	function handleAccessRequest()
	{
		global $db_conn;

		// Checking uniqueness of the User name
		$checkPassword = $_GET['getPassword'];
		$checkUserName = $_GET['getName'];
		// Execute SQL query to fetch the password associated with the provided username
		$result = executePlainSQL("SELECT * FROM Learner_Consults L WHERE L.userName = '$checkUserName'");
		$row = oci_fetch_row($result);

		// Check if a row was fetched
		if ($row) {
			// Password found in the database, compare it with the provided password
			if ($row[3] == $checkPassword) {
				header("Location: home.php"); // Redirect user to home page
				exit; // Stop further execution
			} else {
				echo "<p><font color=red> <b>ERROR</b>: Incorrect password</font><p>";
			}
		} else {
			echo "<p><font color=red> <b>ERROR</b>: User not found</font><p>";
		}

	}

	// HANDLE ALL POST ROUTES
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			}

			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('accessAccountRequest', $_GET)) {
				handleAccessRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['insertSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['getLogin'])) {
		handleGETRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
