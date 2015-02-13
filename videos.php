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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (count($_POST) > 0 && isset($_POST['vidName']) && isset($_POST['vidCat']) && isset($_POST['vidLen'])) {
    // Take care of the case where we are adding a video

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
  else if (count($_POST) > 0 && isset($_POST['deletevid'])) {
    // Take care of the case where we are deleting a video 

    // Prepare the insert statment
    if (!($stmt = $mysqli->prepare("DELETE FROM videos WHERE name=?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      die();
    }

    // Add values to SQL insert statement
    $vidname = $_POST['deletevid'];
    if (!$stmt->bind_param("s", $vidname)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        die();
    }

    // Execute sql statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        die();
    }
  }
  else if (count($_POST) > 0 && isset($_POST['checkout']) && isset($_POST['name'])) {
    // Take care of the case where we are checking in/out a video 

    // Prepare the insert statment
    if (!($stmt = $mysqli->prepare("UPDATE videos SET rented=? WHERE name=?"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      die();
    }

    // Add values to SQL insert statement
    $vidname = $_POST['name'];
    $rented = $_POST['checkout'] === "true" ? 1 : 0; // 1 means true. 0 means false.
    if (!$stmt->bind_param("is", $rented, $vidname)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        die();
    }

    // Execute sql statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        die();
    }
  }
  else if (count($_POST) > 0 && isset($_POST['deleteall']) && $_POST['deleteall'] === 'true') {
    // Delete all vids 

    echo "in the deets";
    // Prepare the insert statment
    if (!($stmt = $mysqli->prepare("DELETE FROM videos"))) {
      echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
      die();
    }

    // Execute sql statement
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        die();
    }
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
    <br>
    <input type="button" value="Delete All Videos" onclick="deleteAllVids();">	
	</div>
  <h1>Video List:</h1>
	<div id="vid-list">
    <div id="vid-list-table">
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
  				echo "No videos exist\n";
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
              $checkoutBtnText = 'Checkout';
  					}
  					else {
  						$rented = 'checked out';
              $checkoutBtnText = 'Checkin';
  					}

  					echo "<tr>\n"
          	."<td>$vidname</td>\n"
          	."<td>$vidcat</td>\n"
          	."<td>$vidlen</td>\n"
          	."<td>$rented</td>\n"
            ."<td><input type=\"button\" value=\"$checkoutBtnText\" onclick=\"updateCheckout('$checkoutBtnText', '$vidname');\"></td>\n"
          	."<td><input type=\"button\" value=\"Delete\" onclick=\"deleteVid('$vidname');\"></td>\n"
          	."</tr>\n";
  				}

  				echo "</table>\n";
  			}
  		?>
    </div>
  </div>
</body>
</html>
