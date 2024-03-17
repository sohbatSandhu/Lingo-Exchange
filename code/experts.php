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
	<h1 style="text-align:center">Experts Management Page</h1>
	<h2>Request an Expert</h2>
	<p>Get assigned to an expert by selecting one from our drop-down below!</p>
	<form method="POST" action="experts.php">
		<input type="hidden" id="requestExpertUpdateRequest" name="requestExpertUpdateRequest">
		User ID: <input type="text" name="uid"> 
		<p><select id="email" name="email">
			<option value="romina.m@mail.com">Romina M</option>
			<option value="annie.w@mail.com">Annie W</option>
			<option value="sohbat.s@mail.com">Sohbat S</option>
			<option value="james.w@mail.com'">James W</option>
			<option value="kayla.k@mail.com">Kayla K</option>
			<option value="kate.m@mail.com">Kate M</option>
		</select></p>
		<input type="submit" value="Request Expert" name="requestExpert"></p>
	</form>
	<hr />

	<h2>View Experts</h2>
	<p>View your current expert.</p>
	<form method="GET" action="experts.php">
		<input type="hidden" id="viewMyExpertGetRequest" name="viewMyExpertGetRequest">
		User ID: <input type="text" name="uid"> <br /><br />
		<input type="submit" value="View My Expert" name="viewMyExpert"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>View all of our experts.</p>
	<form method="GET" action="experts.php">
		<input type="hidden" id="viewExpertsGetRequest" name="viewExpertsGetRequest">
		<input type="submit" value="View Experts" name="viewExperts"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>View all of our experts for a specific delivery mode.</p>
	<form method="GET" action="experts.php">
		<input type="hidden" id="viewFilteredExpertsGetRequest" name="viewFilteredExpertsGetRequest">
		<p><select id="mode" name="mode">
			<option value="Online Morning">Online Morning</option>
			<option value="Online Afternoon">Online Afternoon</option>
			<option value="Online Evening">Online Evening</option>
			<option value="In-Person Morning'">In-Person Morning</option>
			<option value="In-Person Afternoon">In-Person Afternoon</option>
			<option value="In-Person Evening">In-Person Evening</option>
		</select></p>
		<input type="submit" value="View Filtered Experts" name="viewFilteredExperts"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>View the experts who specialize in all the languages offered across our platform.</p>
	<form method="GET" action="experts.php">
		<input type="hidden" id="viewSpecializedExperts" name="viewSpecializedExperts">
		<input type="submit" value="View Specialized Experts" name="viewSpecializedExperts"></p>
	</form>
	<hr style="border: 1px dashed gray;" />
	<p>View the cities whose number of experts is equal to or above a specified threshold.</p>
	<form method="GET" action="experts.php">
		<input type="hidden" id="viewCitiesGetRequest" name="viewCitiesGetRequest">
		Threshold: <input type="text" name="threshold"> <br /><br />
		<input type="submit" value="View Cities" name="viewCities"></p>
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

	function printViewExperts($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing all experts:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>ExpertEmail</th>
				<th>ExpertName</th>
				<th>City</th>
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

	function printViewMyExpert($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing your current expert:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>UserID</th>
				<th>ExpertName</th>
				<th>ExpertEmail</th>
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

	function printViewFilteredExperts($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing filtered experts:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>ExpertEmail</th>
				<th>ExpertName</th>
				<th>DeliveryMode</th>
				<th>City</th>
			 </tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			// echo <tr> $row[0]  <tr>;
			 echo "<tr>
			 			<td>" . $row[0] . "</td>
			 			<td>" . $row[1] . "</td>
						<td>" . $row[2] . "</td>
						<td>" . $row[3] . "</td>
					</tr>";
		}
		echo "</table>";
	}

	function printViewSpecializedExperts($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing specialized experts:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>ExpertEmail</th>
				<th>ExpertName</th>
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

	function printViewCities($result)
	{ //prints results from a select statement
		echo "<br>Results for viewing cities:<br><br>";
		echo "<table>";
		echo "<tr>
				<th>City</th>
				<th>Count</th>
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

	function handleViewExpertsGetRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT ExpertEmail, ExpertName, City FROM Expert4");
		printViewExperts($result["statement"]);
	}

	function handleViewMyExpertGetRequest()
	{
		global $db_conn;

		$tuple = array(
			":bind1" => $_GET['uid'],
		);
		$alltuples = array(
			$tuple
		);
		$result = executeBoundSQL("SELECT L.UserID, E.ExpertName, E.ExpertEmail
								   FROM Learner_Consults L, Expert4 E
								   WHERE L.ExpertEmail=E.ExpertEmail
								   	AND L.UserID=:bind1", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			printViewMyExpert($result["statement"]);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to show your expert :( <br>
					 Make sure that you've entered a valid User ID (which should be an integer).</font><p>";
		}
	}

	function handleViewFilteredExpertsGetRequest()
	{
		global $db_conn;

		$tuple = array(
			":bind1" => $_GET['mode'],
		);
		$alltuples = array(
			$tuple
		);
		$result = executeBoundSQL("SELECT E4.ExpertEmail, E4.ExpertName, E3.DeliveryMode, E4.City 
								   FROM Expert3 E3, Expert4 E4 
								   WHERE E3.DeliveryMode=:bind1
								   	 AND E3.ExpertName=E4.ExpertName
									 AND E3.City=E4.City", $alltuples);
		oci_commit($db_conn);
		printViewFilteredExperts($result["statement"]);
	}

	function handleViewSpecializedExpertsGetRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT ExpertEmail, ExpertName
								   FROM Expert4 E
								   WHERE NOT EXISTS 
								   ((SELECT LanguageName FROM Language2) 
								    MINUS 
								    (SELECT LanguageName 
									 FROM Specializes S 
									 WHERE E.ExpertEmail=S.ExpertEmail))");
		printViewSpecializedExperts($result["statement"]);
	}

	function handleViewCitiesGetRequest()
	{
		global $db_conn;

		$tuple = array(
			":bind1" => $_GET['threshold'],
		);
		$alltuples = array(
			$tuple
		);
		$result = executeBoundSQL("SELECT City, COUNT(*)
								   FROM Expert4 
								   GROUP BY City
								   HAVING COUNT(*) >=:bind1", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			printViewCities($result["statement"]);
		}
		if ($result["success"] == FALSE) {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to show the cities meeting the above condition :( <br>
					Make sure that you've entered a valid User ID (which should be an integer).</font><p>";
		}
	}

	function handleRequestExpertUpdateRequest()
	{
		global $db_conn;

		// Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['uid'],
			":bind2" => $_POST['email']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("UPDATE Learner_Consults SET ExpertEmail=:bind2 WHERE UserID=:bind1 ", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Your request was successfully processed :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: We encountered a problem when trying to assign you and expert :( <br>
					Make sure that you've entered a valid threshold value (which should be an integer).</font><p>";
		}
	}
	

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('requestExpertUpdateRequest', $_POST)) {
				handleRequestExpertUpdateRequest();
			}
			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('viewExperts', $_GET)) {
				handleViewExpertsGetRequest();
			} elseif (array_key_exists('viewFilteredExperts', $_GET)) {
				handleViewFilteredExpertsGetRequest();
			} elseif (array_key_exists('viewSpecializedExperts', $_GET)) {
				handleViewSpecializedExpertsGetRequest();
			} elseif (array_key_exists('viewCities', $_GET)) {
				handleViewCitiesGetRequest();
			} elseif (array_key_exists('viewMyExpert', $_GET)) {
				handleViewMyExpertGetRequest();
			} 
			disconnectFromDB();
		}
	}

	if (isset($_POST['requestExpert'])) {
		handlePOSTRequest();
	} else if (isset($_GET['viewExpertsGetRequest']) || isset($_GET['viewFilteredExpertsGetRequest']) || isset($_GET['viewCitiesGetRequest']) ||
			   isset($_GET['viewSpecializedExperts']) || isset($_GET['viewMyExpertGetRequest'])) {
		handleGETRequest();
	} 
	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>