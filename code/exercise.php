<!-- Exercise Page
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

	<!-- <p>Choose the language and dialect you want exercises for.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="viewExerciseRequest" name="viewExerciseRequest">
		<p><select id="language" name="language">
			<option value="English">English</option>
			<option value="Spanish">Spanish</option>
			<option value="French">French</option>
			<option value="German">German</option>
			<option value="Chinese">Chinese</option>
			<option value="Korean">Korean</option>
			<option value="Japanese">Japanese</option>
		</select></p>

		<p><select id="dialect" name="dialect">
			<option value="American English">American English</option>
			<option value="British English">British English</option>
			<option value="Latin American Spanish">Latin American Spanish</option>
			<option value="European Spanish">European Spanish</option>
			<option value="European French">European French</option>
			<option value="Canadian French">Canadian French</option>
			<option value="Belgian French">Belgian French</option>
			<option value="Afrian French">Afrian French</option>
			<option value="Standard German">Standard German</option>
			<option value="Swiss German">Swiss German</option>
			<option value="Mandarin">Mandarin</option>
			<option value="Shanghainese">Shanghainese</option>
			<option value="Cantonese">Cantonese</option>
			<option value="Gyeonggi dialect">Gyeonggi dialect</option>
			<option value="Hyojungo">Hyojungo</option>
		</select></p>
		<input type="submit" value="Search" name="viewExercise"></p>
	</form>
	<hr /> -->

	<h2>View Exercises</h2>
	<p>View all exercises.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="displayExercisesRequest" name="displayExercisesRequest">
		<input type="submit" value="View All" name="displayExercises"></p>
	</form> 
	<hr />

	<h2>Question</h2>
	<p>Choose the exercise you want to work on.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="displayQuestionRequest" name="displayQuestionRequest">
		<p><select id="exerciseName" name="exerciseName">
			<option value="Active to Passive Voice English">Active to Passive Voice English</option>
			<option value="Parisian Culture">Parisian Culture</option>
			<option value="Chinese Vocabulary Quiz">Chinese Vocabulary Quiz</option>
			<option value="Spanish Grammar Quiz">Spanish Grammar Quiz</option>
			<option value="German Pronunciation Workout">German Pronunciation Workout</option>
			<option value="Korean Alphabet Quiz">Korean Alphabet Quiz</option>
			<option value="Mock Spelling Bee">Mock Spelling Bee</option>
			<option value="French Pronunciation Quiz">French Pronunciation Quiz</option>
			<option value="Chinese Oral Exercise">Chinese Oral Exercise</option>
			<option value="Advanced Spanish Grammar Quiz">Advanced Spanish Grammar Quiz</option>
			<option value="Must-Know Korean Phrases">Must-Know Korean Phrases</option>
		</select></p>

		<p><select id="exerciseNum" name="exerciseNum">
			<option value="61">61</option>
			<option value="62">62</option>
			<option value="63">63</option>
			<option value="64">64</option>
			<option value="65">65</option>
			<option value="92">92</option>
			<option value="93">93</option>
			<option value="94">94</option>
			<option value="95">95</option>
			<option value="96">96</option>
			<option value="97">97</option>
		</select></p>
		<input type="submit" value="Show Question" name="displayQuestion">
	</form>
	<hr />

	<h2>Mark Complete</h2>
	<form method="POST" action="exercise.php">
		<input type="hidden" id="markCompleteRequest" name="markCompleteRequest">
		<p><input type="submit" value="Mark Complete" name="markCompleted"></p>
	</form>
	<hr />

	<h2>View Exercise Statistics</h2>
	<p>View maximum score.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="viewMaxRequest" name="viewMaxRequest">
		<input type="submit" value='View' name="viewMax"></p>
	</form>

	<p>View minimum score.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="viewMinRequest" name="viewMinRequest">
		<input type="submit" value='View' name="viewMin"></p>
	</form>

	<p>View average score.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="viewAvgRequest" name="viewAvgRequest">
		<input type="submit" value='View' name="viewAvg"></p>
	</form>
	<hr />

	<h2>Count Scores Above Average</h2>
	<p>Show the number of times you scored above average.</p>
	<form method="GET" action="exercise.php">
		<input type="hidden" id="countScoresRequest" name="countScoresRequest">
		<input type="submit" value="Calculate" name="countScores"></p>
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

	function displayExercises($result)
	{ //prints all exercises from a select statement
		echo "<br>Exercises:<br>";
		echo "<table>";
		echo "<tr>
			<th>ExerciseName</th>
			<th>ExerciseNumber</th>
			<th>Purpose</th>
			<th>TimeLimit</th>
		</tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
				<td>" . $row[1] . "</td>
				<td>" . $row[2] . "</td>
				<td>" . $row[3] . "</td>  
			</tr>"; 
		}
		echo "</table>";
	} // warning on line 229: Undefined array key 3 - still printed the whole table

	function displayQuestion($result) 
	{ //prints questions for each exercise from a select statement
		echo "<table>";
		echo "<tr>
			<th>QuestionName</th>
		</tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr>
				<td>" . $row[0] . "</td>
			</tr>"; 
		}
		echo "</table>";

		//TODO: add error handling for empty tables
	}


	// TODOS
	function viewMax()
	{

	}

	function viewMin()
	{
		
	}

	function viewAvg()
	{
		
	}

	function countScores()
	{
		
	}


	function handleDisplayExercisesRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT e4.ExerciseName, e4.ExerciseNumber, e1.Purpose, e3.TimeLimit
								   FROM Exercise4 e4, Exercise1 e1, Exercise3 e3
								   WHERE e4.ExerciseName = e1.ExerciseName
								   AND e1.ExerciseName = e3.ExerciseName");
		displayExercises($result["statement"]);
	}

	function handleDisplayQuestionRequest()
	{
		global $db_conn;
		
		$tuple = array(
			":bind1" => $_GET['exerciseName'],
			":bind2" => $_GET['exerciseNum'],
		);

		$alltuples = array(
			$tuple
		);

		$result = executeBoundSQL("SELECT QuestionName FROM Question_Has
								   WHERE ExerciseName = :bind1
								   AND ExerciseNumber = :bind2", $alltuples);
		oci_commit($db_conn);
		if ($result["success"] == TRUE) {
			displayQuestion($result["statement"]);
		} else {
			echo "<p><font color=red> <b>ERROR</b>: Try again! Check that the exercise is correct</font><p>";
		}
	}

	function handleMarkCompletedRequest()
	{
		echo "<p>
			<font color=green> <b>Yay! You completed this exercise :)</font>
		</p>";

		//TODO: Implement an update on Completes.CompletionDate
	}


	// TODOS
	function handleViewMaxRequest()
	{

	}

	function handleViewMinRequest()
	{
		
	}

	function handleViewAvgRequest()
	{
		
	}

	function handleCountScoresRequest()
	{
		
	}


	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('markCompleted', $_POST)) {
				handleMarkCompletedRequest();
			}
			disconnectFromDB();
		}
	}

	// HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('displayQuestionRequest', $_GET)) {
				handleDisplayQuestionRequest();
			} else if (array_key_exists('displayExercisesRequest', $_GET)) {
				handleDisplayExercisesRequest();
			} else if (array_key_exists('viewMaxRequest', $_GET)) {
				handleViewMaxRequest();
			} else if (array_key_exists('viewMinRequest', $_GET)) {
				handleViewMinRequest();
			} else if (array_key_exists('viewAvgRequest', $_GET)) {
				handleViewAvgRequest();
			} else if (array_key_exists('countScoresRequest', $_GET)) {
				handleCountScoresRequest();
			} 
			disconnectFromDB();
		}
	}

	if (isset($_POST['markCompletedRequest'])) {
		handlePOSTRequest();
	} else if (isset($_GET['displayQuestion']) || isset($_GET['displayExercises']) || isset($_GET['viewMax']) || isset($_GET['viewMin']) || isset($_GET['viewAvg']) || isset($_GET['countScores'])) {
		handleGETRequest();
	}

	// TODO: Implement user input to choose language and dialect.
	//		 Allow user to view the max, min, and/or average score for exercises grouping by language and using aggregation.
	//		 Allow user to view the count of exercise scores for each language that are above average using nested aggregation and group by.


	// End PHP parsing and send the rest of the HTML content
	?>
</body>

</html>
 