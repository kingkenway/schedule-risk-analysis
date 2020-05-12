<?php

require("functions.php");
require("functions2.php");

if (isset($_POST['activity_counts']) AND isset($_POST['simulation_counts']) AND isset($_POST['critical_path']) AND isset($_POST['planned_duration']) AND isset($_POST['tasks'])) {
	$activities_number = $_POST['activity_counts'];
	$simulation_runs = $_POST['simulation_counts'];
	$critical_path = $_POST['critical_path'];
	$planned_duration = $_POST['planned_duration'];
	$tasks = $_POST['tasks'];

	function simulationRuns($activities_number="", $simulation_runs="", $critical_path=""){
			global $alpha, $tasks;

			$html = "";
			$html .= "<p>{$simulation_runs} simulation scenarios to perform a Schedule Risk Analysis.</p>
			<p>Critical Path: <label id='generatedcriticalpath'>{$critical_path}</label>
			<input id='generatedactivities' value='{$tasks}' type='hidden'>
			</p>
			";
			$html .= " <div class='w3-responsive'><table class='w3-table w3-bordered w3-striped w3-border' style='width: 100%' id='table2'>
				<tr>
					<td></td>
					<td colspan='{$activities_number}'>SAD</td>
					<td colspan=''>SPD</td>
					<td colspan=''>CP</td>
				</tr>
				<tr>
					<td></td>
		 		";

					for ($i=1; $i <=$activities_number ; $i++) {
						$html .= "<th>".$alpha[$i]."</th>";
					}
				$html .= "</tr>";

				 for ($i=1; $i <= $simulation_runs ; $i++) {
					$html .= "
					<tr>
						<th>Run {$i}</th>
					";
						$spd = simulatedProjectDuration();
						$spdresults[$i] = $spd;

						for ($a=1; $a <= $activities_number ; $a++) {
							$xxy = new_generated_number();
							$results[$a][] = $xxy; // save in array for totaling
							$html .= "<td sad-id='{$i}.{$a}'>".$xxy."</td>";
						}
						$html .= "
						<td spd-id='{$i}'>{$spd}</td>
						<td id='criticalpath'  class='fitwidth'>".criticalPath($critical_path)."</td>
					</tr>
					";
				 }
				 $html .= "<tr><td>Average</td>";

				 for ($i=1; $i <= $activities_number ; $i++) {
					 $html .= "<td id='average_activities'>".round(array_sum($results[$i])/count($results[$i]), 2)."</td>";
				 }

				 $html .= "<td id='average_total'>".round(array_sum($spdresults)/count($spdresults), 2)."</td>";
				 $html .= "<tr><td>StDev.</td>";

					for ($i=1; $i <= $activities_number ; $i++) {
						$html .= "<td id='stdev_activities'>".round(StDev($results[$i], true), 2)."</td>";
					}

					$html .= "<td id='stdev_spd'>".round(StDev($spdresults, true),2)."</td>";


				 $html .= "</tr>";

				 $html .= "</table></div>
				 <br>
				 <p class='w3-center'>
	 	      <button id='submit3' class='w3-button w3-grey w3-text-white w3-padding-medium w3-round-xxlarge'>
	 	        Generate Critical Index
	 	      </button>
			   <hr>

				 ";

		return $html;
		}





	echo simulationRuns($activities_number, $simulation_runs, $critical_path);
}else{
	echo "nahhh";
}






?>
