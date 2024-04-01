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

session_start();

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_sohbat";			// change "cwl" to your own CWL
$config["dbpassword"] = "a79661179";	// change to 'a' + your student number
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

	<h1 style="text-align:center">Welcome to Language Learning Platform</h1>
	<h3 style="text-align:center">
		<font color="purple">LINGO EXCHANGE</font> aspires to provide an engaging online environment that allows users to comprehensively learn new languages with fellow passionate learners while being assisted by various learning resources. 
		<br>
		Our focus extends beyond mere language acquisition - we strive to facilitate a comprehensive language learning journey. 
	</h3>

	<hr />

	<h2>Create New Account</h2>
	<p>CHOOSE A UNIQUE USERNAME (Leave a blank space between each part of the name) </p>
	<form method="POST" action="welcome.php">
		<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
		Username : <input type="text" name="insName"> <br /><br />
		Age (between 1 and 150) : <input type="number" name="insAge" min="1" max="150"> <br /><br />
		Password: <input type="text" name="insPassword"> <br /><br />

		<input type="submit" value="Start Your Learning Journey" name="insertUser"></p>
	</form>

	<hr />

	<h2>Login</h2>
	<p>
		SIGN IN TO CONTINUE TO YOUR LANGUAGE LEARNING JOURNEY
		<br>
		<font color=blue><b>WARNING:</b> The values are case sensitive and if you enter in the wrong case, access will be denied </font>
	</p>

	<form method="POST" action="welcome.php">
		<input type="hidden" id="accessAccountRequest" name="accessAccountRequest">
		<b>Username:</b> <input type="text" name="getName"> <br /><br />
		<b>Password:</b> <input type="text" name="getPassword"> <br /><br />
		<input type="submit" value="Login" name="getLogin"></p>
	</form>

	<hr />

	<h3 color="red">Administration View</h3>
	<p>
		ACCESS LANGUAGE LEARNING DATABASE AS AN ADMINISTRATOR
	</p>
	<form method="POST" action="admin.php">
		<input type="hidden" id="adminLoginRequest" name="adminLoginRequest">
		<input type="submit" value="ADMIN LOGIN" name="adminLogin" style="color:red;">
	</form>

	<hr style="border: 1px dashed gray;" />

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
		global $db_conn;
		$response = array();
		$response["success"] = True;
		$statement = oci_parse($db_conn, $cmdstr);
		//There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
			echo htmlentities($e['message']);
			$response["success"] = False;
		}

		$r = oci_execute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For oci_execute errors pass the statementhandle
			echo htmlentities($e['message']);
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
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn);
			echo htmlentities($e['message']);
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
				echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
				$e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
				echo htmlentities($e['message']);
				echo "<br>";
				$response["success"] = False;
			}
		}

		$response["statement"] = $statement;
		return $response;
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
		$result = executePlainSQL("SELECT COUNT(UserID) FROM Learner_Consults WHERE Username = '$checkName'");
		$row = oci_fetch_row($result["statement"]);

		if ($row[0] > 0) {
			echo "<p><font color=red> <b>ERROR</b>: User Name already in use. Please Select a new and unique User Name</font><p>";
			return;
		}
		$id = time();
		//Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $id,
			":bind2" => $_POST['insName'],
			":bind3" => $_POST['insAge'],
			":bind4" => $_POST['insPassword'], 
			":bind5" => ""
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("INSERT INTO Learner_Consults values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
		addCreationAchievement($id);
		echo "<p><font color=green> <b>SUCCESS</b>: Account Created Successfully!</font><p>";
		oci_commit($db_conn);
	}

	function handleAccessRequest()
	{
		global $db_conn;

		// Checking uniqueness of the User name
		$checkPassword = $_POST['getPassword'];
		$checkUserName = $_POST['getName'];
		// Execute SQL query to fetch the password associated with the provided username
		$result = executePlainSQL("SELECT * FROM Learner_Consults L WHERE L.Username = '$checkUserName'");

		// Check if a row was fetched
		if (($row = oci_fetch_row($result["statement"])) != false) {
			// Password found in the database, compare it with the provided password
			if ($row[3] == $checkPassword) {
				$_SESSION['userID'] = $row[0];
				$_SESSION['userName'] = $row[1];
				$_SESSION['age'] = $row[2];
				$_SESSION['password'] = $row[3];
				$_SESSION['expert'] = $row[4];

				header('Location: home.php'); // Redirect user to home page
				exit; // Stop further execution
			} else {
				echo "<p><font color=red> <b>ERROR</b>: Incorrect password</font><p>";
			}
		} else {
			echo "<p><font color=red> <b>ERROR</b>: User not found</font><p>";
		}
	}

	function addCreationAchievement($id)
	{
		global $db_conn;

		$startDate = date("Y-m-d", time());

		$tuple = array(
			":bind1" => $id,
			":bind2" => '40',
			":bind3" => $startDate
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("INSERT INTO Earns VALUES (:bind1, :bind2, TO_DATE(:bind3, 'YYYY-MM-DD'))", $alltuples);
		echo "<p><font color=green> <b>SUCCESS</b>: Earned Learning Enthusiast! Congratulations on taking the first for this immersive and enriching journey.</font><p>";
		oci_commit($db_conn);
	}

	// HANDLE ALL POST ROUTES
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			} else if (array_key_exists('accessAccountRequest', $_POST)) {
				handleAccessRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['insertUser']) || isset($_POST['getLogin'])) {
		handlePOSTRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
