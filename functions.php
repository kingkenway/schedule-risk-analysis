<?php

$alpha = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
// Critical Path Generator
function criticalPath($path=''){
  $array = explode("-", $path);
  shuffle($array);
  array_pop($array);
  array_pop($array);
  sort($array);
  array_unique($array);
  $array = implode('-', $array);

  $patch1 = $path;
  $patch2 = $array;

	$final_patch = array($patch1, $patch2);
	$final_patch2 = array_rand($final_patch);
	$final_patch2 = $final_patch[$final_patch2];
	return $final_patch2;
}

// The simulated Project Duration
function simulatedProjectDuration($value=''){
	return rand(20,130);
}


// Find the Standard Deviation
if (!function_exists('StDev')) {
    /**
     * This user-land implementation follows the implementation quite strictly;
     * it does not attempt to improve the code or algorithm in any way. It will
     * raise a warning if you have fewer than 2 values in your array, just like
     * the extension does (although as an E_USER_WARNING, not E_WARNING).
     *
     * @param array $a
     * @param bool $sample [optional] Defaults to false
     * @return float|bool The standard deviation or false on error.
     */
    function StDev(array $a, $sample = true) {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
           --$n;
        }
        return sqrt($carry / $n);
    }
}

function random_decimals() {
    $x = (float)mt_rand() / (float)mt_getrandmax();
    return round($x, 2);
}

function random_whole_numbers($value=''){
	return rand(0,100);
}

function logarithm_ln($value=''){
	return log($value,2.71828182846); // 2.71828182846 equivalent to e
}

function generate_number($value='', $lambda=0.05){
	$u = random_decimals();
	$a = logarithm_ln(1-$u);
	$b = -(1/$lambda);
	return floor($a * $b);
}

function new_generated_number($value=''){
	$gen = generate_number();
	if (is_infinite($gen)) {
		return random_whole_numbers();
	}elseif ($gen < 1) {
		return random_whole_numbers();
	}
	else{
		return $gen;
	}
}


function summation($value=''){
	return true;
}

// echo generate_number();

// SENSITIVITY MEASURES
//
// 1. Critical Index

function CriticalIndex($value=''){
	$nrs = ""; 	// Number of Monte-Carlo simulation runs (index k)
	$k   = 1;  	// Simulation runs
	$tf  = 0; 	// Total float of activity i

	$ci  = (1 / $nrs) * ($k);

}


// 2. Significance Index

function SignificanceIndex($e='', $di="", $tf="", $rd=""){
	$e 	= $e;  // Expected value of x
	$di = $di; // Duration of activity i
	$tf = $tf; // Total float of activity i
	$rd = $rd; // Total real project duration (as a result of a simulation run)

	$si = $e * (($di / ($di + $tf)) * ($rd / ($e * $rd)));

}

// 3. Cruciality Index

// 3a. Cruciality Index (Pearson's) &upsih;

function CrucialityIndexPearson($value=''){
	$sad = $sad; // The simulated activity duration
	$sadBar = $sadBar;
	$spd = $spd; // The simulated project duration
	$spdBar = $spdBar;

	$a1 = ($sad-$sadBar)*($spd-$spdBar);
	$a2 = sqrt((($sad-$sadBar) ** 2) * (($spd-$spdBar) ** 2));
	$a3 = abs($a1 / $a2);
	return($a3);
}


// 3b. Cruciality Index (Spearman's) &rho;

function CrucialityIndexSpearman($nrs='', $diff_rank){
	$diff_rank = $diff_rank; // The difference between the ranking values of SAD and SPD
	$nrs = $nrs;			 // The number of simulation runs
							 // summation-> The sum of all x-values over all simulation runs

	$a = summation($diff_rank ** 2);
	$a = $a / ($nrs * (($nrs ** 2) - 1));
	$a = 6 * $a;
	$a = 1 - $a;
	return $a;
}



// 3c. Cruciality Index (Kendall's) &zeta;

function CrucialityIndexKendall($nrs='', $diff_rank){
	$diff_rank = $diff_rank; // The difference between the ranking values of SAD and SPD
	$nrs = $nrs;			 // The number of simulation runs
							 // summation-> The sum of all x-values over all simulation runs

	$a = summation($diff_rank ** 2);
	$a = $a / ($nrs * (($nrs ** 2) - 1));
	$a = 6 * $a;
	$a = 1 - $a;
	return $a;
}


// 4. Schedule Sensitivity Index SSI:

function ScheduleSensitivityIndex($ad="" ,$ci="" ,$pd=""){
	$ad = $ad; // Activity Duration
	$ci = $ci; // Criticality Index
	$pd = $pd; // Project Duration

	return (StDev($ad) * $ci) / (StDev($pd));
}

// $simulation_runs = 5;
// $activities_number = 8;
// $planned_duration = 90;
// $critical_path = "1-2-5-8";
//
// $sensitivity_titles = ["CI", "SI", "CRI &upsih;", "CRI &rho;", "CRI &zeta;", "SSI"];
$sensitivity_titles = ["CI", "SI", "CRI &upsih;", "CRI &rho;", "CRI &zeta;", "SSI"];


?>
