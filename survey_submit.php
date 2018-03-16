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
	$shower_used = 0; // shower + shave
	$brush_used = 0;
	$flush_used = 0;
	$laundry_used = 0;
	$dishes_used = 0;

	// Calculations were determined based on publicly available data,
	// sourced in our references. Months are assumed to be 4 weeks long.

	$shower_used = calcShower($shower, $time, $freq, $shavelegs, $shaveface);

	$brush_used = calcBrush($faucet, $brush_used);

	$water_used += $wash * 0.5 * 7 * 4;
	$water_used += $drink * 3/32 * 7 * 4;

	$flush_used += $flush * 3 * 7 * 4;

	$laundry_used += $laundry * 25 * 4;

	$dishes_used = dishesCalc($dishwasher, $dishes_used, $handwash);

	$water_used += $shower_used + $brush_used + $flush_used + $laundry_used + $dishes_used;


	if ($shower_used > 400) {
		print("<p>You use significantly more water when showering than the average Californian. Next time you shower, try this trick to conserve water: first, use the first 30-60 seconds to get yourself wet. Next, turn off the shower while you lather up with soap and put shampoo in your hair. Finally, turn on the shower for a minute or two to rinse all the soap off of your body and out of your hair.</p>");
	}
	if ($brush_used > 40) {
		print("<p>You use more than a gallon of water to brush your teeth every day, which is above the Californian and the national average. To use less water while brushing your teeth, try turning on the faucet briefly to wet your toothbrush, turning it off while actually brushing your teeth, and then turning it on again to clean your toothbrush.</p>");
	}

	recordData($_POST, $water_used, $shower_used, $brush_used, $flush_used, $laundry_used, $dishes_used);

	compareData($water_used, $shower_used, $brush_used, $flush_used, $laundry_used, $dishes_used);

	exit;
}

function calcShower($shower, $time, $freq, $shavelegs, $shaveface) {
	$shower_used = 0; 	// for both shower & shave
	if ($shower == "onCampus") {
		$shower_used += 1.5 * $time * $freq * 4;
	} else if ($shower == "offCampus") {
		$shower_used += 2 * $time * $freq * 4;
	} else {
		// according to the EPA, a full bath is 70 gallons
		$shower_used += 70 * $freq * 4;
	}
	// Here, I'm assuming that people shave with the same frequency as they shower
	if ($shavelegs != "No") {
		$shower_used += 1 * $freq * 4;
	}
	if ($shaveface != "No") {
		$shower_used += 1 * $freq * 4;
	}
	return $shower_used;
}

function calcBrush($faucet, $brush_used) {
	$brush_used = 0;
	if ($faucet == "on") {
		$brush_used += 4 * 2 * 7 * 4; // 4 gallons * 2x per day * 7 days per week * 4 weeks
	} else {
		$brush_used += 0.5 * 2 * 7 * 4;
	}
	return $brush_used;
}

function dishesCalc($dishwasher, $dishes_used, $handwash) {
	$dishes_used = 0;
	if ($dishwasher > 0) {
		$dishes_used += $dishwasher * 6 * 4;
	} else {
		$dishes_used += $handwash * 1.5 * 7 * 4;
	}
	return $dishes_used;
}

function recordData($POST, $water_used, $shower_used, $brush_used, $flush_used, $laundry_used, $dishes_used) {
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

	$idhash = md5($id);

	$conn = oci_connect( /* blank */, /* blank */, '//dbserver.engr.scu.edu/db11g' );
	if (!$conn) {
		print "<br> connection failed:";
		exit;
	}

	$query = oci_parse(
		$conn,
		"INSERT INTO Waterresponses values ('$idhash', '$rlc', $time, $freq, '$shower', '$shavelegs', '$shaveface', '$faucet', $wash, $drink, $flush, $laundry, $dishwasher, $handwash, $shower_used, $brush_used, $flush_used, $laundry_used, $dishes_used, $water_used)"
	);
	if (! @oci_execute($query)) {
		$err = oci_error($query);
		return false;
	}

	OCILogoff($conn);
	return true;
}

function compareData($water_used, $shower_used, $brush_used, $flush_used, $laundry_used, $dishes_used) {
	$conn = oci_connect( /* blank */, /* blank */, '//dbserver.engr.scu.edu/db11g' );
	if (!$conn) {
		print "<br> connection failed:";
		exit;
	}

	$query = oci_parse(
		$conn,
		"SELECT showerTotal, brushTotal, flushTotal, flushTotal, laundryTotal, dishesTotal, total FROM Waterresponses"
	);
	if (! @oci_execute($query)) {
		$err = oci_error($query);
		print($err);
	}

	$shower_sum	 = 0;
	$brush_sum	 = 0;
	$flush_sum	 = 0;
	$laundry_sum = 0;
	$dishes_sum	 = 0;
	$total_sum	 = 0;
	$num = 0;

	while (($row = oci_fetch_array($query, OCI_BOTH)) != false) {
		// foreach ($row as $key => $value) { echo "Key: $key; Value: $value\n"; }
		$shower_sum	 += $row["SHOWERTOTAL"];
		$brush_sum	 += $row["BRUSHTOTAL"];
		$flush_sum	 += $row["FLUSHTOTAL"];
		$laundry_sum += $row["LAUNDRYTOTAL"];
		$dishes_sum	 += $row["DISHESTOTAL"];
		$total_sum	 += $row["TOTAL"];
		$num += 1;
	}

	$shower_sum		= $shower_sum / $num;
	$brush_sum		= $brush_sum / $num;
	$flush_sum		= $flush_sum / $num;
	$laundry_sum	= $laundry_sum / $num;
	$dishes_sum		= $dishes_sum / $num;
	$total_sum		= $total_sum / $num;

	printf("<p>You use %.2f gallons of water per month, compared to the average SCU student's %.2f:</p>\n", $water_used, $total_sum);
	print("<ul>\n");
	printf("\t<li>%.2f gal. (%.2f%%) from showering (SCU ave. %.2f)</li>\n",      $shower_used, 100 * $shower_used / $water_used, $shower_sum);
	printf("\t<li>%.2f gal. (%.2f%%) from brushing teeth (SCU ave. %.2f)</li>\n", $brush_used, 100 * $brush_used / $water_used, $brush_sum);
	printf("\t<li>%.2f gal. (%.2f%%) from toilet flushes (SCU ave. %.2f)</li>\n", $flush_used, 100 * $flush_used / $water_used, $flush_sum);
	printf("\t<li>%.2f gal. (%.2f%%) from laundry (SCU ave. %.2f)</li>\n",        $laundry_used, 100 * $laundry_used / $water_used, $laundry_sum);
	printf("\t<li>%.2f gal. (%.2f%%) from washing dishes (SCU ave. %.2f)</li>\n", $dishes_used, 100 * $dishes_used / $water_used, $dishes_sum);
	print("</ul>\n");

	OCILogoff($conn);
	return true;
}

?>
		</main>
	</body>
</html>
