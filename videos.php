<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="videos.css">
  <script src='videos.js'></script>
	<title>HTML Assignment 4 Part 2</title>
</head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include 'storedInfo.php';

// Test MYSQL connection
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", $myUsername, $myPassword, $myUsername);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MYSQL <br>";
}

// If this is a POST, then we are attempting to add a video
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (count($_POST) > 0 && isset($_POST['vidName']) && isset($_POST['vidCat']) && isset($_POST['vidLen'])) {

		// Prepare the insert statment
		if (!($stmt = $mysqli->prepare("INSERT INTO videos(name, category, length, rented) VALUES (?, ?, ?, ?)"))) {
  	  echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
  	  die();
		}

		$vidname = $_POST['vidName'];
		$vidcat = $_POST['vidCat']; 
		$vidlen = $_POST['vidLen'];
		$rented = false; // Default added videos to not rented

		// Add values to SQL insert statement
		if (!$stmt->bind_param("ssii", $vidname, $vidcat, $vidlen, $rented)) {
		    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
		    die();
		}

		// Execute sql statement
		if (!$stmt->execute()) {
		    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		    die();
		}
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (count($_GET) > 0 && isset($_GET['deleteall']) && $_GET['deleteall'] === 'true') {
  	// Add code here to delete all vids
  }
}

?>
<body>
	<div>
		<form action="https://web.engr.oregonstate.edu/~toke/a4p2/videos.php" method="POST" onsubmit="return checkAddVidFields();">
			<fieldset>
				<legend>Add Videos here:</legend>
				<fieldset>
					<label>Video Name: </label>
					<input type="text" id="vidName" name="vidName"><br>
				</fieldset>
				<fieldset>
					<label>Video Category: </label>
					<input type="text" id="vidCat" name="vidCat"><br>
				</fieldset>		
				<fieldset>
					<label>Video Length: </label>
					<input type="number" id="vidLen" name="vidLen" min=0><br>
				</fieldset>		
				<fieldset>
				<input type="submit" value="Add Video">
				</fieldset>
			</fieldset>
		</form>
	</div>
	<div>
		<form action="https://web.engr.oregonstate.edu/~toke/a4p2/videos.php" method="GET">
			<fieldset>
				<legend>Delete all videos:</legend>
				<fieldset>
					<input type="hidden" name="deleteall" value="true">
					<input type="submit" value="Delete All">
				</fieldset>
			</fieldset>
		</form>	
	</div>
	<div>
    <h1>Video List: </h1>
		<?php
			if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM videos"))) {
		    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!$stmt->execute()) {
		    echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
			}

			if (!($res = $stmt->get_result())) {
		    echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
			}

			if ($res->num_rows === 0) {
				echo "No videos exist";
			}
			else {
				echo "<table>\n";

				// Output Column Headers
				echo "<tr>\n"
        ."<td>Video Name</td>\n"
        ."<td>Video Category</td>\n"
        ."<td>Video Length</td>\n"
        ."<td>Video Rental Status</td>\n"
        ."</tr>\n";

				for ($row_no = ($res->num_rows - 1); $row_no >= 0; $row_no--) {
					$res->data_seek($row_no);
	    		$row = $res->fetch_assoc();

					$vidname = $row['name'];
					$vidcat = $row['category']; 
					$vidlen = $row['length'];
					$rented = $row['rented']; 

					if ($rented === 0) {
						$rented = 'available';
					}
					else {
						$rented = 'checked out';
					}

					echo "<tr>\n"
        	."<td>$vidname</td>\n"
        	."<td>$vidcat</td>\n"
        	."<td>$vidlen</td>\n"
        	."<td>$rented</td>\n"
        	.'<td><input type="button" value="Delete" onclick="deleteVid();"></td>\n'
        	."</tr>\n";
				}

				echo '</table>';
			}

		?>
  </div>
</body>
</html>
