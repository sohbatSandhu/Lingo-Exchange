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
$config["dbuser"] = "ora_anniew02";			// change "cwl" to your own CWL
$config["dbpassword"] = "a47832274";	// change to 'a' + your student number
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
	<h1>Resources Page</h1>

	<h2>All Resources</h2>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllMatRequest" name="displayAllMatRequest">
		<input type="submit" value="View Resources" name="displayAllMat"></p>
	</form>
	<hr />

	<h2>All Books</h2>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllBooksRequest" name="displayAllBooksRequest">
		<input type="submit" value="View Books" name="displayAllBooks"></p>
	</form>
	<hr />

	<h2>All Apps</h2>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllAppsRequest" name="displayAllAppsRequest">
		<input type="submit" value="View Apps" name="displayAllApps"></p>
	</form>
	<hr />

	<h2>All Websites</h2>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllWebsitesRequest" name="displayAllWebsitesRequest">
		<input type="submit" value="View Websites" name="displayAllWebsites"></p>
	</form>
	<hr />

	<!-- TODO: Allow users to view specific resources based on language/dialect? -->

	<h2>Add Resource</h2>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addMatRequest" name="addMatRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		MaterialName: <input type="text" name="matName"> <br /><br />
		Purpose: <input type="text" name="matPurpose"> <br /><br />
		<label for="dropdown">Select a resource type:</label>
		<p><select id="resourceType" name="resourceType">
			<option value="Book">Book</option>
			<option value="App">App</option>
			<option value="Website">Website</option>
		</select></p>
        <input type="submit" value="Add" name="addMat">
	</form> 
	<hr />
	
	<!-- TODO: Implement another user input for each dropdown option -->

	<h2>Add Book</h2>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addBookRequest" name="addBookRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		Author: <input type="text" name="author"> <br /><br />
        <input type="submit" value="Add" name="addBook"></p>
	</form> 
	<hr />
	
	<h2>Add App</h2>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addAppRequest" name="addAppRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		Developer: <input type="text" name="dev"> <br /><br />
        <input type="submit" value="Add" name="addApp"></p>
	</form> 
	<hr />
	
	<h2>Add Website</h2>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addWebsiteRequest" name="addWebsiteRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		URL: <input type="text" name="url"> <br /><br />
        <input type="submit" value="Add" name="addWebsite"></p>
	</form> 
	<hr />
	

	<h2>Remove Resource</h2>
	<form method="POST" action="materials.php">
		<input type="hidden" id="deleteMatRequest" name="deleteMatRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		<input type="submit" value="Delete" name="deleteMat"></p>
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

	function printAllMaterials($result)
	{ //prints all resources from a select statement
		echo "<br>Resources:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>MaterialName</th>
			<th>Purpose</th>
		</tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
				<td>" . $row[1] . "</td>
				<td>" . $row[2] . "</td>
			</tr>"; 
		}
		echo "</table>";
	}

	function printAllBooks($result)
	{ //prints all resources that are books from a select statement
		echo "<br>Books:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>Author</th>
		</tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
				<td>" . $row[1] . "</td>
			</tr>"; 
		}
		echo "</table>";
	}

	function printAllApps($result)
	{ //prints all resources from a select statement
		echo "<br>Apps:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>Developer</th>
		</tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
				<td>" . $row[1] . "</td>
			</tr>"; 
		}
		echo "</table>";
	}

	function printAllWebsites($result)
	{ //prints all resources from a select statement
		echo "<br>Websites:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>URL</th>
		</tr>";
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
				<td>" . $row[1] . "</td>
			</tr>"; 
		}
		echo "</table>";
	}

	function handleDiaplayAllMatRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM Material");
		printAllMaterials($result["statement"]);
	}

	function handleDiaplayAllBooksRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, b.Author FROM Book b, Material m WHERE b.MaterialID = m.MaterialID");
		printAllBooks($result["statement"]);

		//TODO: display MaterialNames as well
	}

	function handleDiaplayAllAppsRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, a.Developer FROM App a, Material m WHERE a.MaterialID = m.MaterialID");
		printAllApps($result["statement"]);
	}

	function handleDiaplayAllWebsitesRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, w.URL FROM Website w, Material m WHERE w.MaterialID = m.MaterialID");
		printAllWebsites($result["statement"]);
	}

	function handleAddMatRequest()
	{ //Getting the values from user input and insert data into Material table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['matName'],
			":bind3" => $_POST['matPurpose'],
			":bind4" => $_POST['resourceType'] //warning message because currently not being used
		);

		//TODO: based on resource type, add user input for Author, Developer, or URL to Book, App, or Website table respectively

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Material VALUES (:bind1, :bind2, :bind3)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new resource :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Try again!</font><p>";
		}
	}

	function handleAddBookRequest()
	{ //Getting the values from user input and insert data into Book table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['author']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Book VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new book :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Try again!</font><p>";
		}
	}

	function handleAddAppRequest()
	{ //Getting the values from user input and insert data into App table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['dev']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO App VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new app :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Try again!</font><p>";
		}
	}

	function handleAddWebsiteRequest()
	{ //Getting the values from user input and insert data into Website table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['url']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Website VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new website :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Try again!</font><p>";
		}
	}

	function handleDeleteMatRequest()
	{ // Getting the value from user input and delete data from Material table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("DELETE FROM Material WHERE MaterialID = :bind1", $alltuples);
		
		oci_commit($db_conn);

		if ($result["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Deleted a resource :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Check your're deleting the right Material ID. Try again!</font><p>";
		}
	}

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('addMat', $_POST)) {
				handleAddMatRequest();
			} else if (array_key_exists('addBook', $_POST)) {
				handleAddBookRequest();
			} else if (array_key_exists('addApp', $_POST)) {
				handleAddAppRequest();
			} else if (array_key_exists('addWebsite', $_POST)) {
				handleAddWebsiteRequest();
			} else if (array_key_exists('deleteMat', $_POST)) {
				handleDeleteMatRequest();
			}
			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('displayAllMatRequest', $_GET)) {
				handleDiaplayAllMatRequest();
			} else if(array_key_exists('displayAllBooksRequest', $_GET)) {
				handleDiaplayAllBooksRequest();
			} else if(array_key_exists('displayAllAppsRequest', $_GET)) {
				handleDiaplayAllAppsRequest();
			} else if(array_key_exists('displayAllWebsitesRequest', $_GET)) {
				handleDiaplayAllWebsitesRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['addMatRequest']) || isset($_POST['addBookRequest']) || isset($_POST['addAppRequest']) || isset($_POST['addWesbiteRequest']) || isset($_POST['deleteMatRequest'])) {
		handlePOSTRequest();
	} else if (isset($_GET['displayAllMat']) || isset($_GET['displayAllBooks']) || isset($_GET['displayAllApps']) || isset($_GET['displayAllWebsites'])) {
		handleGETRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
