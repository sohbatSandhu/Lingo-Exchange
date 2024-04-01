<!-- Resources Page - The script assumes you already have a server set up All OCI commands are
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
ini_set('display_errors', 0); //change to 0 to hide warnings
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_cwl";			// change "cwl" to your own CWL
$config["dbpassword"] = "pass";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()
$success = true;	// keep track of errors so page redirects only if there are no errors
$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

// The next tag tells the web server to stop parsing the text as PHP. Use the
// pair of tags wherever the content switches to PHP

session_start();

if (empty($_SESSION)) { //check if $_SESSION is empty
	header('Location: welcome.php'); 
	exit;
} 

//set logged in user information
$userID = $_SESSION['userID'];
$userName = $_SESSION['userName'];
$age = $_SESSION['age'];
$password = $_SESSION['password'];
$expert = $_SESSION['expert'];

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
	<h1 style="text-align:center">Materials Page</h1>
	<div style="text-align:center">
		<form method="POST" action="home.php">
			<input type="hidden" id="home" name="home">
			<input type="submit" value="Return to Home Page">
		</form>
	</div>
	<hr />

	<h2>Study Materials</h2>
	<p>View all study materials.</p>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllMatRequest" name="displayAllMatRequest">
		<input type="submit" value="View Materials" name="displayAllMat"></p>
	</form>
	<hr />

	<h2>Books</h2>
	<p>View all books.</p>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllBooksRequest" name="displayAllBooksRequest">
		<input type="submit" value="View Books" name="displayAllBooks"></p>
	</form>
	<hr />

	<h2>Apps</h2>
	<p>View all apps.</p>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllAppsRequest" name="displayAllAppsRequest">
		<input type="submit" value="View Apps" name="displayAllApps"></p>
	</form>
	<hr />

	<h2>Websites</h2>
	<p>View all websites.</p>
	<form method="GET" action="materials.php">
		<input type="hidden" id="displayAllWebsitesRequest" name="displayAllWebsitesRequest">
		<input type="submit" value="View Websites" name="displayAllWebsites"></p>
	</form>
	<hr />
	
	<h2>Add Book</h2>
	<p>Add a book.</p>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addBookRequest" name="addBookRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		Author: <input type="text" name="author"> <br /><br />
        <input type="submit" value="Add" name="addBook"></p>
	</form> 
	<hr />
	
	<h2>Add App</h2>
	<p>Add an app.</p>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addAppRequest" name="addAppRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		Developer: <input type="text" name="dev"> <br /><br />
        <input type="submit" value="Add" name="addApp"></p>
	</form> 
	<hr />
	
	<h2>Add Website</h2>
	<p>Add a website.</p>
	<form method="POST" action="materials.php">
		<input type="hidden" id="addWebsiteRequest" name="addWebsiteRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		URL: <input type="text" name="url"> <br /><br />
        <input type="submit" value="Add" name="addWebsite"></p>
	</form> 
	<hr />

	<h2>Update Study Material</h2>
	<p>Input the MaterialID for the study material you want to update.</p>
	<form method="POST" action="materials.php">
		<input type="hidden" id="updateMatRequest" name="updateMatRequest">
		MaterialID: <input type="text" name="mid"> <br /><br />
		MaterialName: <input type="text" name="matName"> <br /><br />
		Purpose: <input type="text" name="matPurpose"> <br /><br />
        <input type="submit" value="Update" name="updateMat"></p>
	</form> 
	<hr />

	<h2>Remove Study Material</h2>
	<p>Input the MaterialID of the study material you wish to remove.</p>
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
		echo "<br>All study materials offered:<br>";
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
		echo "<br>All books offered:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>MaterialName</th>
			<th>Author</th>
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

	function printAllApps($result)
	{ //prints all resources that are apps from a select statement
		echo "<br>All apps offered:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>MaterialName</th>
			<th>Developer</th>
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

	function printAllWebsites($result)
	{ //prints all resources that are websites from a select statement
		echo "<br>All websites offered:<br>";
		echo "<table>";
		echo "<tr>
			<th>MaterialID</th>
			<th>MaterialName</th>
			<th>URL</th>
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

	function handleDisplayAllMatRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM Material");
		printAllMaterials($result["statement"]);
	}

	function handleDisplayAllBooksRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, m.MaterialName, b.Author FROM Book b, Material m WHERE b.MaterialID = m.MaterialID");
		printAllBooks($result["statement"]);
	}

	function handleDisplayAllAppsRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, m.MaterialName, a.Developer FROM App a, Material m WHERE a.MaterialID = m.MaterialID");
		printAllApps($result["statement"]);
	}

	function handleDisplayAllWebsitesRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT m.MaterialID, m.MaterialName, w.URL FROM Website w, Material m WHERE w.MaterialID = m.MaterialID");
		printAllWebsites($result["statement"]);
	}

	function handleAddBookRequest()
	{ //Getting the values from user input and insert data into Book and Materials table 
	  //Note: User will need to update the Material table to insert MaterialName and Purpose
		global $db_conn;

		$matName = "Update MaterialName"; 	//default material name
		$matPurpose = "Update Purpose"; 	//default purpose

		$matTuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $matName,
			":bind3" => $matPurpose
		);

		$allMatTuples = array(
			$matTuple
		);

		$matResult = executeBoundSQL("INSERT INTO Material VALUES (:bind1, :bind2, :bind3)", $allMatTuples);

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['author']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Book VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE && $matResult["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new book :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Check that the MaterialID is correct (it shouldn't exist in the list of resources)</font><p>";
		}
	}

	function handleAddAppRequest()
	{ //Getting the values from user input and insert data into App and Material table
	  //Note: User will need to update the Material table to insert MaterialName and Purpose
		global $db_conn;

		$matName = "Update MaterialName"; 	//default material name
		$matPurpose = "Update Purpose"; 	//default purpose
		
		$matTuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $matName,
			":bind3" => $matPurpose
		);

		$allMatTuples = array(
			$matTuple
		);

		$matResult = executeBoundSQL("INSERT INTO Material VALUES (:bind1, :bind2, :bind3)", $allMatTuples);
		
		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['dev']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO App VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE && $matResult["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new app :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Check that the MaterialID is correct (it shouldn't exist in the list of resources)</font><p>";
		}
	}

	function handleAddWebsiteRequest()
	{ //Getting the values from user input and insert data into Website and Material table
	  //Note: User will need to update the Material table to insert MaterialName and Purpose
		global $db_conn;

		$matName = "Update MaterialName"; 	//default material name
		$matPurpose = "Update Purpose"; 	//default purpose
		
		$matTuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $matName,
			":bind3" => $matPurpose
		);

		$allMatTuples = array(
			$matTuple
		);

		$matResult = executeBoundSQL("INSERT INTO Material VALUES (:bind1, :bind2, :bind3)", $allMatTuples);

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['url']
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("INSERT INTO Website VALUES (:bind1, :bind2)", $alltuples);
		oci_commit($db_conn);

		if ($result["success"] == TRUE && $matResult["success"] == TRUE) {
			echo "<p><font color=green> <b>SUCCESS</b>: Added a new website :)</font></p>";
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Check that the MaterialID is correct (it shouldn't exist in the list of resources)</font><p>";
		}
	}

	function handleUpdateMatRequest()
	{ //Getting user input to update the name and purpose in the Material table
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid'],
			":bind2" => $_POST['matName'],
			":bind3" => $_POST['matPurpose']
		);

		$alltuples = array(
			$tuple
		);

		$check = executeBoundSQL("SELECT COUNT(*) FROM Material WHERE MaterialID = :bind1", $alltuples);
		$counter = oci_fetch_row($check["statement"])[0];

		if($counter > 0) {
			$result = executeBoundSQL("UPDATE Material SET MaterialName = :bind2, Purpose = :bind3 WHERE MaterialID = :bind1", $alltuples);
			oci_commit($db_conn);

			if ($result["success"] == TRUE) {
				echo "<p><font color=green> <b>SUCCESS</b>: Updated a study material :)</font></p>";
			} else {
				echo "<p><font color=red> <b>ERROR</b>: Try again! Check that the MaterialID is correct.</font><p>";
			}
		} else {
			echo "<p><font color=grey><b>This MaterialID doesn't exist :( Check the list of Materials for the correct ID.</b></font><p>";
		} 
	}

	function handleDeleteMatRequest()
	{ // Getting MaterialID from user input and delete that tuple from Material table 
		global $db_conn;

		$tuple = array(
			":bind1" => $_POST['mid']
		);

		$alltuples = array(
			$tuple
		);

		$check = executeBoundSQL("SELECT COUNT(*) FROM Material WHERE MaterialID = :bind1", $alltuples);
		$counter = oci_fetch_row($check["statement"])[0];

		if($counter > 0) {
			$result = executeBoundSQL("DELETE FROM Material WHERE MaterialID = :bind1", $alltuples);
			oci_commit($db_conn);

			if ($result["success"] == TRUE) {
				echo "<p><font color=green> <b>SUCCESS</b>: Removed a resource :)</font></p>";
			} else {
				echo "<p><font color=red> <b>ERROR</b>: Check that your're deleting the right MaterialID. Try again!</font><p>";
			}
		} else {
			echo "<p><font color=grey><b>This MaterialID doesn't exist :( Check the list of Materials for the correct ID.</b></font><p>";
		}
	}

	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('addBook', $_POST)) {
				handleAddBookRequest();
			} else if (array_key_exists('addApp', $_POST)) {
				handleAddAppRequest();
			} else if (array_key_exists('addWebsite', $_POST)) {
				handleAddWebsiteRequest();
			} else if (array_key_exists('updateMat', $_POST)) {
				handleUpdateMatRequest();
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
				handleDisplayAllMatRequest();
			} else if(array_key_exists('displayAllBooksRequest', $_GET)) {
				handleDisplayAllBooksRequest();
			} else if(array_key_exists('displayAllAppsRequest', $_GET)) {
				handleDisplayAllAppsRequest();
			} else if(array_key_exists('displayAllWebsitesRequest', $_GET)) {
				handleDisplayAllWebsitesRequest();
			}
			disconnectFromDB();
		}
	}

	if (isset($_POST['addBookRequest']) || isset($_POST['addAppRequest']) || isset($_POST['addWebsiteRequest']) || isset($_POST['updateMatRequest']) || isset($_POST['deleteMatRequest'])) {
		handlePOSTRequest();
	} else if (isset($_GET['displayAllMat']) || isset($_GET['displayAllBooks']) || isset($_GET['displayAllApps']) || isset($_GET['displayAllWebsites'])) {
		handleGETRequest();
	}

	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
