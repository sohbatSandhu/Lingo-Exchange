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

// Logged In User Information maded available using SESSION variable
session_start();

if (empty($_SESSION)) { header('Location: welcome.php'); exit; } // Check if $_SESSION is empty

// Set Logged In user Information
$userID = $_SESSION['userID'];
$userName = $_SESSION['userName'];
$age = $_SESSION['age'];
$password = $_SESSION['password'];
$expert = $_SESSION['expert'];
?>

<html>

<head>
	<title>Home: Lingo Learning </title>
</head>

<style>
	table, th, td {border: 1px solid; padding: 5px; border-collapse: collapse;}
	th {text-align: center}
</style>

<body>

	<h1 style="text-align:center">Home Page: Language Learning</h1>
	<h3 style="text-align:center">
		Welcome <?php global $userName; echo htmlspecialchars($userName); ?>, where language learning is made easy, fun, and effective. 
		<br>
		Learn, speak, connect - Unlock new Horizons. Challenge Yourself.
	</h3>
	
	<hr />

	<h2> Edit Profile (UserID: <?php global $userID; echo htmlspecialchars($userID); ?>)</h2>
	<p>
		Update the attributes you want to update by specifing the desired attribute value under the <b>New Information</b> column.
		<br>
		To update assined expert, check the checkbox to redirect to Expert Management Page after clicking Update Information. If still not redirecting, edit experts using the "Manage Experts" button in <b>Navigation</b> below.
		<br>
		Leave the attribute value blank if you do not want to update that attribute.
		<br>
		<font color="blue"><b>WARNING:</b> New Username should be unique</font>
	</p>
	<form method="POST" action="home.php">
		<input type="hidden" id="updateUserInformation" name="updateUserInformation">
		<table>
			<tr><th>Attribute Name</th><th>Current Information</th><th>Update Information</th></tr>
			<tr><td>User Name</td><th> <?php global $userName; echo htmlspecialchars($userName); ?> </th><td><input type="text" name="newUserName"></td></tr>
			<tr><td>Age (Between 1 and 150)</td><th> <?php global $age; echo htmlspecialchars($age); ?> </th><td><input type="number" name="newAge" min="1" max="150"></td></tr>
			<tr><td>Password</td><th> <?php global $password; echo htmlspecialchars($password); ?> </th><td><input type="text" name="newPassword"></td></tr>
			<tr><td>Expert Assigned</td><th> <?php global $expert; echo (isset($expert)) ? htmlspecialchars($expert) : "Not Assigned"; ?> 	</th><td>
								<input type="checkbox" id="editExpert" name="editExpert" value="true">
								<label for="editExpert"> Edit Expert</label>
							</td></tr>
		</table> <br/>
		<input type="submit" value="Update Information" name="updateUser">
	</form>
	<form method="POST" action="welcome.php">
		<input type="hidden" id="logoutRequest" name="logoutRequest">
		<input type="submit" value="LOGOUT" name="logoutUser">
	</form>
	<form method="POST" action="home.php">
		<input type="hidden" id="deleteUserRequest" name="deleteUserRequest">
		<input type="submit" value="DELETE ACCOUNT" name="deleteAccount" style="color:red;">
	</form>
	
	<hr />

	<h2>Navigation</h2>
	<p>NAVIGATE TO OTHER PAGES FOR LANGUAGE LEARNING</p>
	<form method="POST" action="exercise.php">
		<input type="hidden" id="excerciseNavRequest" name="excerciseNavRequest">
		<input type="submit" value="Practice Exercises">
	</form>
	<form method="POST" action="materials.php">
		<input type="hidden" id="materialsNavRequest" name="materialsNavRequest">
		<input type="submit" value="View Materials Provided">
	</form>
	<form method="POST" action="experts.php">
		<input type="hidden" id="expertNavRequest" name="expertNavRequest">
		<input type="submit" value="Manage Experts">
	</form>
	<form method="POST" action="forums.php">
		<input type="hidden" id="forumsNavRequest" name="forumsNavRequest">
		<input type="submit" value="Participate in Interaction Forums">
	</form>

	<hr />

	<h2>View and Add Provided Languages</h2>
	<p>VIEW ALL PROVIDED LANGUAGES AND DIALECT COMBINATIONS TO START LEARINING</p>
	<form method="GET" action="home.php">
		<input type="hidden" id="viewLanguageRequest" name="viewLanguageRequest"> 
		<input type="submit" value="Show All Languages Provided">
	</form>

	<hr style="border: 1px dashed gray;" />

	<p>VIEW ALL PROVIDED LANGUAGE NAMES HAVING SPECIFIED MINIMUM NUMBER OF DIALECTS</p>
	<form method="GET" action="home.php">
		<input type="hidden" id="viewMinDialectsLanguageRequest" name="viewMinDialectsLanguageRequest">
		Minimum Number of Dialects: <input type="number" name="minDia" min="0"> <br /><br />
		<input type="submit" value="Show Languages">
	</form>

	<hr style="border: 1px dashed gray;" />

	<p>
		INPUT LANGUAGE AND DIALET COMBINATION TO ADD LANGUAGUE INTO YOUR CURRENT LANGUAGES
		<br>
		<font color=blue><b>WARNING:</b> Languages and dialects are case sensitive. </font>
	</p>
	
	<form method="POST" action="home.php">
		<input type="hidden" id="updateLanguagesRequest" name="updateLanguagesRequest">
		Language: <input type="text" name="addLang"> <br /><br />
		Dialect: <input type="text" name="addDialect"> <br /><br />
		<input type="submit" value="Add Language" name="addLanguage">
	</form>
	
	<hr />

	<h2>View and Remove Current Languages</h2>
	<p>VIEW ALL LANGUAGES AND DIALECT COMBINATIONS CURRENTLY LEARNING</p>
	<form method="GET" action="home.php">
		<input type="hidden" id="viewCurrentLanguageRequest" name="viewCurrentLanguageRequest">
		<input type="submit" value="View Current Languages">
	</form>

	<hr style="border: 1px dashed gray;" />
	
	<p>
		INPUT LANGUAGE AND DIALECT COMBINATION TO REMOVE LANGUAGE FROM YOUR CURRENT LANGUAGES
		<br>
		<font color=blue><b>WARNING:</b> Languages and dialects are case sensitive. </font>
	</p>
	<form method="POST" action="home.php">
		<input type="hidden" id="removeLanguagesRequest" name="removeLanguagesRequest">
		Language: <input type="text" name="removeLang"> <br /><br />
		Dialect: <input type="text" name="removeDialect"> <br /><br />
		<input type="submit" value="Remove Language" name="removeLanguage">
	</form>

	<hr />

	<h2>Achievements</h2>
	<p>VIEW ALL ACHIEVEMENTS EARNED</p>
	<form method="GET" action="home.php">
		<input type="hidden" id="viewAchievementsRequest" name="viewAchievementsRequest"> 
		<input type="submit" value="Show All Achievements">
	</form>

	<hr style="border: 1px dashed gray;" />

	<p>VIEW NUMBER OF ACHIEVEMENTS EARNED FOR A REWARD</p>
	<form method="GET" action="home.php">
		<input type="hidden" id="viewAchievementNumbersRequest" name="viewAchievementNumbersRequest">
		<input type="submit" value="View Achievement Count">
	</form>

	<hr />

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

	function printLanguages($result)
	{ 	// prints all provided languages
		echo "<br>Retrieved Provided Languages:<br>";
		echo "<table>";
		echo "<tr>
				<th>Language Name</th>
				<th>Dialect</th>
				<th>Number of Characters</th>
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

	function printMinDialectsLanguages($result)
	{	// prints languages with minimum number of dialects
		echo "<br>Retrieved Languages with a Minimum of " . $_GET["minDia"] . " Dialects:<br>";
		echo "<table>";
		echo "<tr>
				<th>Language Name</th>
				<th>Number of Dialects</th>
			</tr>";
		$count = 0;
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
					<td>" . $row[0] . "</td>
					<td>" . $row[1] . "</td>
				</tr>";
			$count = $count + 1;
		}

		if ($count == 0) {
			echo "<p><font color=red> <b>ERROR</b>: No Languages found with a Minimum of " . $_GET["minDia"] . " Dialects</font><p>";
		}

		echo "</table>";
	}

	function printSelectedLanguages($result)
	{	// prints all selected languages
		echo "<br>Retrieved User's Languages:<br>";
		echo "<table>";
		echo "<tr>
				<th>Language Name</th>
				<th>Dialect</th>
				<th>Start Date</th>
			</tr>";
		$count = 0; // counter to check the number of tuples extracted from $result
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
					<td>" . $row[1] . "</td>
					<td>" . $row[2] . "</td>
					<td>" . $row[3] . "</td>
				</tr>";
			$count = $count + 1;
		}

		if ($count == 0) {
			echo "<p><font color=red> <b>ERROR</b>: No Currently Selected Languages. Retry after selecting languages</font><p>";
		}

		echo "</table>";
	}

	function printAchievements($result)
	{	// prints all achieved rewards 
		echo "<br>Retrieved User's Achievements:<br>";
		echo "<table>";
		echo "<tr>
				<th>Achievement Name</th>
				<th>Achievement Description</th>
				<th>Reward Name</th>
				<th>Receival Date</th>
			</tr>";
		$count = 0; // counter to check the number of tuples extracted from $result
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
					<td>" . $row[0] . "</td>
					<td>" . $row[1] . "</td>
					<td>" . $row[2] . "</td>
					<td>" . $row[3] . "</td>
				</tr>";
			$count = $count + 1;
		}

		if ($count == 0) {
			echo "<p><font color=red> <b>ERROR</b>: No Achievements Found</font><p>";
		}

		echo "</table>";
	}

	function printAchievementNumbers($result)
	{	// prints all achieved rewards 
		echo "<br>Retrieved User's Reward Count:<br>";
		echo "<table>";
		echo "<tr>
				<th>Reward Name</th>
				<th>Number Earned</th>
			</tr>";
		$count = 0; // counter to check the number of tuples extracted from $result
		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
					<td>" . $row[0] . "</td>
					<td>" . $row[1] . "</td>
				</tr>";
			$count = $count + 1;
		}

		if ($count == 0) {
			echo "<p><font color=red> <b>ERROR</b>: No Achievements Found</font><p>";
		}

		echo "</table>";
	}

	function handleUpdateUserRequest() 
	{
		global $db_conn, $userID, $userName, $age, $password;

		$newName = $_POST['newUserName'];
		$newAge = $_POST['newAge'];
		$newPassword = $_POST['newPassword'];
		$editExpert = $_POST['editExpert'];

		// Identify blanks for no change
		if (empty($_POST['newUserName'])) {
			$newName = $userName;
		} else {
			// Check uniqueness of new username
			$result = executePlainSQL(
				"SELECT COUNT(UserID) FROM Learner_Consults WHERE Username = '$newName' AND UserID != '$userID'"
			);
			$row = oci_fetch_row($result["statement"]);
			if ($row[0] > 0) {
				echo "<p><font color=red> <b>ERROR</b>: New Username already exists.</font><p>";
				return;
			}

		}
		if (empty($_POST['newAge'])) {
			$newAge = $age;
		}
		if (empty($_POST['newPassword'])) {
			$newPassword = $password;
		}

		// SQL query to update user information
		executePlainSQL(
			"UPDATE Learner_Consults L
			SET L.Username='$newName', L.Age = '$newAge', L.Password ='$newPassword' 
			WHERE UserID='$userID'"
		);

		$_SESSION["userName"] = $newName;
		$_SESSION["password"] = $newPassword;
		$_SESSION["age"] = $newAge;		
		oci_commit($db_conn);

		// Redirect to experts page to update information
		if ($editExpert != "true") {
			header("Refresh:0; url=home.php");
		} else {
			header("Location: experts.php");
		}
	}

	function handleRemoveLanguageRequest() 
	{
		global $db_conn, $userID;

		// Checking Missing Values required for Language Removal
		if (empty($_POST['removeLang']) || empty($_POST['removeDialect'])) {
			echo "<p><font color=red> <b>ERROR</b>: Missing values for Language Removal and/or Dialect.</font><p>";
			return;
		}

		// Check tuple existance
		$checkLang = $_POST['removeLang'];
		$checkDial = $_POST['removeDialect'];
		$result = executePlainSQL(
			"SELECT COUNT(UserID)
			FROM Learns
			WHERE LanguageName = '$checkLang' AND Dialect = '$checkDial'"
		);
		$row = oci_fetch_row($result["statement"]);

		if ($row[0] == 0) {
			echo "<p><font color=red> <b>ERROR</b>: Dialect and Language combination does not exist.</font><p>";
			return;
		}

		//Getting tuple for removal
		$tuple = array(
			":bind1" => $userID,
			":bind2" => $_POST['removeLang'],
			":bind3" => $_POST['removeDialect']
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("DELETE FROM Learns WHERE UserID=:bind1 AND LanguageName=:bind2 AND Dialect=:bind3", $alltuples);
		echo "<p><font color=green> <b>SUCCESS</b>: Removed " . $checkLang . "Language with " . $checkDial . " dialect successfully!</font><p>";
		oci_commit($db_conn);
	}
	
	function handleAddLanguageRequest()
	{
		global $db_conn, $userID, $userName;

		// Checking Missing Values required for Language Addition
		if (empty($_POST['addLang']) || empty($_POST['addDialect'])) {
			echo "<p><font color=red> <b>ERROR</b>: Missing values for Language and/or Dialect.</font><p>";
			return;
		}

		// Check Duplication
		$checkLang = $_POST['addLang'];
		$checkDial = $_POST['addDialect'];
		$result = executePlainSQL(
			"SELECT COUNT(UserID) 
			FROM Learns
			WHERE LanguageName = '$checkLang' AND Dialect = '$checkDial' AND UserID = '$userID'"
		);
		$row = oci_fetch_row($result["statement"]);

		if ($row[0] > 0) {
			echo "<p><font color=red> <b>FAILURE TO INCLUDE COMBINATION</b>: Dialect and Language combination already included in selected languages.</font><p>";
			return;
		}

		$startDate = date("Y-m-d", time());

		//Getting the language name and dialect from user and insert data into DB
		$tuple = array(
			":bind1" => $userID,
			":bind2" => $_POST['addLang'],
			":bind3" => $_POST['addDialect'],
			":bind4" => $startDate
		);

		$alltuples = array(
			$tuple
		);
		executeBoundSQL("INSERT INTO Learns VALUES (:bind1, :bind2, :bind3, TO_DATE(:bind4, 'YYYY-MM-DD'))", $alltuples);
		echo "<p><font color=green> <b>SUCCESS</b>: Added " . $checkLang . " Language with " . $checkDial . " dialect to " . $userName . "'s languages successfully on " . $startDate . " !</font><p>";
		oci_commit($db_conn);
	}

	function handleDisplayMinDialectsLanguageRequest()
	{
		global $db_conn;

		$minNum = $_GET["minDia"];

		$result = executePlainSQL(
			"SELECT LanguageName, COUNT(Dialect)
			FROM Language2
			GROUP BY LanguageName 
			HAVING COUNT(Dialect) >= '$minNum'"
		);
		printMinDialectsLanguages($result["statement"]);
	}
	
	function handleDisplayCurrentLanguageRequest()
	{
		global $db_conn, $userID;

		
		$result = executePlainSQL(
			"SELECT * 
			FROM Learns 
			WHERE UserID = '$userID'"
		);

		printSelectedLanguages($result["statement"]);
		
	}

	function handleDisplayAllLanguageRequest()
	{
		global $db_conn;
		$result = executePlainSQL(
			"SELECT L2.LanguageName, L2.Dialect, L1.NumChars 
			FROM Language1 L1 
			INNER JOIN Language2 L2 
			ON L1.LanguageName = L2.LanguageName"
		);
		printLanguages($result["statement"]);
	}

	function handleViewAchievementsRequest()
	{
		global $db_conn, $userID;

		$result = executePlainSQL(
			"SELECT A2.AchievementName, A2.AchievementDescription, A1.RewardName, E.ReceivalDate
			FROM Achievement1 A1, Achievement2 A2, Earns E
			WHERE A1.RewardID = A2.RewardID AND A2.AchievementID = E.AchievementID AND E.UserID='" . $userID . "'"
		);
		
		printAchievements($result["statement"]);
	}

	function handleViewAchievementNumbersRequest()
	{
		global $db_conn, $userID;

		$tuple = array(
			":bind1" => $userID
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL(
			"SELECT A1.RewardName, COUNT(E.ReceivalDate)
			FROM Achievement1 A1, Achievement2 A2, Earns E
			WHERE A1.RewardID = A2.RewardID AND A2.AchievementID = E.AchievementID AND E.UserID=:bind1
			GROUP BY A1.RewardName" , $alltuples);
		
		printAchievementNumbers($result["statement"]);
	}

	function handleDeleteUserRequest()
	{
		global $db_conn, $userID;

		// Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $userID
		);

		$alltuples = array(
			$tuple
		);

		executePlainSQL("DELETE FROM Learner_Consults WHERE UserID=:bind1");
		oci_commit($db_conn);
		session_destroy();
	}

	function addLanguageAchievement()
	{
		global $db_conn, $userID;

		$startDate = date("Y-m-d", time());

		$tuple = array(
			":bind1" => $userID,
			":bind2" => '46',
			":bind3" => $startDate
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("INSERT INTO Earns VALUES (:bind1, :bind2, TO_DATE(:bind3, 'YYYY-MM-DD'))", $alltuples);
		oci_commit($db_conn);
	}

	// HANDLE ALL POST ROUTES
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('updateUser', $_POST)) {
				handleUpdateUserRequest();
			} else if (array_key_exists('addLanguage', $_POST)) {
				handleAddLanguageRequest();
				addLanguageAchievement();
			} else if (array_key_exists('removeLanguage', $_POST)) {
				handleRemoveLanguageRequest();
			} else if (array_key_exists('logoutRequest', $_POST)) {
				session_destroy();
				header('Location: welcome.php');
				exit;
			} else if (array_key_exists('deleteUserRequest', $_POST)) {
				handleDeleteUserRequest();
			}

			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('viewLanguageRequest', $_GET)) {
				handleDisplayAllLanguageRequest();
			} elseif (array_key_exists('viewCurrentLanguageRequest', $_GET)) {
				handleDisplayCurrentLanguageRequest();
			} elseif (array_key_exists('viewMinDialectsLanguageRequest', $_GET)) {
				handleDisplayMinDialectsLanguageRequest();
			} elseif (array_key_exists('viewAchievementsRequest', $_GET)) {
				handleViewAchievementsRequest();
			} elseif (array_key_exists('viewAchievementNumbersRequest', $_GET)) {
				handleViewAchievementNumbersRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['updateUser']) || isset($_POST['addLanguage']) || isset($_POST['removeLanguage']) || isset($_POST['logoutUser']) || isset($_POST['deleteAccount'])) {
		handlePOSTRequest();
	} else if (isset($_GET['viewLanguageRequest']) || isset($_GET['viewCurrentLanguageRequest']) || isset($_GET['viewMinDialectsLanguageRequest']) || isset($_GET['viewAchievementsRequest']) || isset($_GET['viewAchievementNumbersRequest'])) {
		handleGETRequest();
	}
	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>