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

	$shower_used = calcShower($shower, $time, $freq, $shavelegs, $shaveface);

	$brush_used = calcBrush($faucet, $brush_used);

	$water_used += $wash * 0.5 * 7 * 4;
	$water_used += $drink * 3/32 * 7 * 4;

	$flush_used += $flush * 3 * 7 * 4;

	$laundry_used += $laundry * 25 * 4;

	$dishes_used = dishesCalc($dishwasher, $dishes_used, $handwash);

	$water_used += $shower_used + $brush_used + $flush_used + $laundry_used + $dishes_used;

	printf("<p>You use %.2f gallons of water per month:</p>\n", $water_used);
	print("<ul>\n");
	printf("\t<li>%.2f%% from showering</li>\n",      100 * $shower_used / $water_used);
	printf("\t<li>%.2f%% from brushing teeth</li>\n", 100 * $brush_used / $water_used);
	printf("\t<li>%.2f%% from toilet flushes</li>\n", 100 * $flush_used / $water_used);
	printf("\t<li>%.2f%% from laundry</li>\n",        100 * $laundry_used / $water_used);
	printf("\t<li>%.2f%% from washing dishes</li>\n", 100 * $dishes_used / $water_used);
	print("</ul>\n");

	if ($shower_used > 400) {
		print("<p>You use significantly more water when showering than the average Californian. Next time you shower, try this trick to conserve water: first, use the first 30-60 seconds to get yourself wet. Next, turn off the shower while you lather up with soap and put shampoo in your hair. Finally, turn on the shower for a minute or two to rinse all the soap off of your body and out of your hair.</p>");
	}
	if ($brush_used > 40) {
		print("<p>You use more than a gallon of water to brush your teeth every day, which is above the Californian and the national average. To use less water while brushing your teeth, try turning on the faucet briefly to wet your toothbrush, turning it off while actually brushing your teeth, and then turning it on again to clean your toothbrush.</p>");
	}
	exit;
}

function calcShower($shower, $time, $freq, $shavelegs, $shaveface) {
	$shower_used = 0; // for both shower & shave
	if ($shower == "onCampus") {
		$shower_used += 1.5 * $time * $freq * 4;
	} else {
		$shower_used += 2 * $time * $freq * 4;
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

?>
		</main>
	</body>
</html>
