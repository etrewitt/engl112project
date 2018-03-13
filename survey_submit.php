<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title>Water Usage Survey</title>
		<link rel="stylesheet" type="text/css" href="project.css">
	</head>
	<body>
		<header>
			<h1>Water Usage Survey</h1>
		</header>
		<nav>
      <ul>
				<li><a id="home" class="home" href="http://students.engr.scu.edu/~etrewitt/water/">Home</a></li
        ><li><a id="survey" class="current" href="survey.html">Survey</a></li
        >
				<!-- <li><a id="viewRequests" href="requests.php">Requests</a></li
        ><li><a id="priorities" href="priorities.php">Course Priorities</a></li
        ><li><a id="respondants" href="respondants.php">Respondants</a></li
        ><li><a id="student_info" href="student_info.php">Student Info</a></li> -->
      </ul>
    </nav>
		<div class="below-nav"></div>
		<main>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// collect input data
	$id         = $_POST['id'];
	$rlc        = $_POST['rlc'];
	$time       = $_POST['showerTime'];
	$freq       = $_POST['showerFreq'];
	$shower     = $_POST['shower'];
	$shavelegs  = $_POST['shavelegs'];
	$shaveface  = $_POST['shaveface'];
	$faucet     = $_POST['faucet'];
	$wash       = $_POST['wash'];
	$drink      = $_POST['drink'];
	$flush      = $_POST['flush'];
	$laundry    = $_POST['laundry'];
	$dishwasher = $_POST['dishwasher'];
	$handwash   = $_POST['handwash'];

	$water_used = 0;
	$shower_used = 0; // shower + shave imo
	$brush_used = 0;
	$flush_used = 0;
	$laundry_used = 0;
	$dishes_used = 0;

	if ($shower == "onCampus") {
		$shower_used += 1.5 * $time * $freq * 4;
	} else {
		$shower_used += 2 * $time * $freq * 4;
	}
	if ($shavelegs != "No") {
		$shower_used += 1 * $freq * 4; // Here, I'm assuming that people shave with the same frequency as they shower
	}
	if ($shaveface != "No") {
		$shower_used += 1 * $freq * 4; // Here, I'm assuming that people shave with the same frequency as they shower
	}
	if ($faucet == "faucetOn") {
		$brush_used += 4 * 2 * 7 * 4; // 4 gallons * 2x per day * 7 days per week * 4 weeks
	} else {
		$brush_used += 0.5 * 2 * 7 * 4;
	}
	$water_used += $wash * 0.5 * 7 * 4;
	$water_used += $drink * 3/32 * 7 * 4;
	$flush_used += $flush * 3 * 7 * 4;
	$laundry_used += $laundry * 25 * 4;
	if ($dishwasher > 0) {
		$dishes_used += $dishwasher * 6 * 4;
	} else {
		$dishes_used += $handwash * 1.5 * 7 * 4;
	}

	$water_used += $shower_used + $brush_used + $flush_used + $laundry_used + $dishes_used;



	printf("<p>You use %.2f gallons of water per month:</p>\n", $water_used);
	print("<ul>\n");
	printf("\t<li>%.2f%% from showering</li>\n",      100 * $shower_used / $water_used);
	printf("\t<li>%.2f%% from brushing teeth</li>\n", 100 * $brush_used / $water_used);
	printf("\t<li>%.2f%% from toilet flushes</li>\n", 100 * $flush_used / $water_used);
	printf("\t<li>%.2f%% from laundry</li>\n",        100 * $laundry_used / $water_used);
	printf("\t<li>%.2f%% from washing dishes</li>\n", 100 * $dishes_used / $water_used);
	print("</ul>\n");
	exit;

	// // validate selection
	// $valid = validateInput($id, $dept, $courseNo, $year, $quarter);
  //
	// if ($valid) {
	// 	// add course
	// 	addCourse($id, $dept, $courseNo, $year, $quarter);
	// }
}

// function validateInput($id, $dept, $courseNo, $year, $quarter) {
// 	$conn = oci_connect( 'etrewitt', '/* password here */', '//dbserver.engr.scu.edu/db11g' );
// 	if (!$conn) {
// 		print "<br> connection failed:";
// 		exit;
// 	}
//
// 	// make sure the course exists
// 	$query = oci_parse(
// 		$conn,
// 		"SELECT *
// 		 FROM   CourseList
// 		 WHERE  dept = '$dept' and courseNo = '$courseNo'"
// 	);
// 	oci_execute($query);
// 	$i = 0;
// 	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {
// 		$i++;
// 	}
// 	if ($i == 0) {
// 		print "<br><b>$dept $courseNo</b> doesn't exist; valid courses in $dept are:";
// 		$query = oci_parse(
// 			$conn,
// 			"SELECT (dept || ' ' || courseNo) as course, name, units
// 			 FROM   CourseList
// 			 WHERE  dept = '$dept'"
// 		);
// 		oci_execute($query);
//
// 		// print valid courses in the selected department
// 		echo "\n<table>\n";
// 		echo "\t<tr><th>Course No.</th><th>Course title</th><th>Units</th></tr>\n\t";
// 		while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {
// 			echo "<tr>\n";
// 			echo "\t\t<td>" . $row[0] . "</td>\n";
// 			echo "\t\t<td>" . $row[1] . "</td>\n";
// 			echo "\t\t<td>" . $row[2] . "</td>\n";
// 			echo "\t</tr>";
// 		}
// 		echo "\n</table>\n";
// 		echo '<br><a href="enter_course.html" class="button" style="font-size: 10pt;">Add a different course</a><br>';
// 		OCILogoff($conn);
// 		return false;
// 	}
//
// 	// make sure the student isn't already taking this course
// 	$query = oci_parse(
// 		$conn,
// 		"SELECT *
// 		 FROM   CourseRequests
// 		 WHERE  studentID = '$id' and
// 		        dept      = '$dept' and
// 						courseNo  = '$courseNo'"
// 	);
// 	oci_execute($query);
// 	$i = 0;
// 	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {
// 		$i++;
// 	}
// 	if ($i != 0) {
// 		print "<p>$id is already registered for $dept $courseNo</p>";
// 		echo '<a href="enter_course.html" class="button" style="font-size: 10pt;">Add a different course</a><br>';
// 		OCILogoff($conn);
// 		return false;
// 	}
//
// 	OCILogoff($conn);
// 	return true;
// }
//
// function addCourse($id, $dept, $courseNo, $year, $quarter) {
// 	// connect to your database. Type in your username, password and the DB path
// 	$conn = oci_connect( 'etrewitt', '/* password here */', '//dbserver.engr.scu.edu/db11g' );
// 	if (!$conn) {
// 		print "<br> connection failed:";
// 		exit;
// 	}
//
// 	$query = oci_parse(
// 		$conn,
// 		"INSERT INTO CourseRequests values ('$id', '$dept', '$courseNo', '$quarter', $year)"
// 	);
// 	if (! @oci_execute($query)) {
// 		$err = oci_error($query);
// 		$code = $err["code"];
// 		if ($code == 20000) {
// 			echo "<p><b>Error $code:</b> Failed to meet the prerequisite(s) for $dept $courseNo.</p>\n";
// 			echo '<a href="enter_course.html" class="button" style="font-size: 10pt;">Add a different course</a><br>';
// 		} elseif ($code == 20001) {
// 			echo "<p><b>Error $code:</b> Already completed $dept $courseNo.</p>\n";
// 			echo '<a href="enter_course.html" class="button" style="font-size: 10pt;">Add a different course</a><br>';
// 		}
// 	} else {
// 		$nextYear = $year + 1;
// 		print "<p>Successfully requested $dept $courseNo for $id in the $quarter $year-$nextYear quarter.</p>";
// 	}
//
// 	OCILogoff($conn);
// }

?>
		</main>
	</body>
</html>
