<?php
// $docid = array(1, 2, 3, 4, 5);
//     $table = array('technology' => 1 , 'languange' => 2, 'town' => 3, 'gadget' => 4, 'smartphone' => 5);
//     echo "<table><tr><th>Token/Document</th>";
//     $count = count($table);
//     $doc_count = count($docid);
//     for($i=1; $i<=$count; $i++)
//     {
//         echo "<th>Doc.$i</th>";
//     }
//     foreach($table as $key=>$value)
//     {
//         echo "<tr><td>$key</td>";
//         for($i=0; $i<$doc_count;$i++)
//         {
//             $random = rand(1, 8);
//             $results[$i][] = $random; // save in array for totaling
//             echo "<td>$random</td>";
//         }
//     }
//     echo "</tr><td>RESULT</td>";
//     for($i=0; $i<$doc_count;$i++)
//     {
//         echo "<td>".array_sum($results[$i])."</td>"; // total array using array_sum()
//     }
//     echo "</tr></table>";

?>


<?php 
// PHP Code to find rank of elements 

// Function to find rank 
function rankify($A , $n) 
{ 

	// Rank Vector 
	$R = array(0); 
	
	// Sweep through all elements in A for each 
	// element count the number of less than and 
	// equal elements separately in r and s. 
	for ($i = 0; $i < $n; $i++) 
	{ 
		$r = 1; 
		$s = 1; 
		
		for ($j = 0; $j < $n; $j++) 
		{ 
			if ($j != $i && $A[$j] < $A[$i]) 
				$r += 1; 
				
			if ($j != $i && $A[$j] == $A[$i]) 
				$s += 1;	 
		} 
		
		// Use formula to obtain rank 
		$R[$i] = $r + (float)($s - 1) / (float) 2; 
	
	} 
	
	for ($i = 0; $i < $n; $i++) 
		print number_format($R[$i], 1) . ' '; 
} 

// Driver Code 
$A = array(1, 2, 5, 2, 1, 25, 2); 
$n = count($A); 
for ($i = 0; $i < $n; $i++) 
echo $A[$i] . ', '; 
echo "\n"; 

rankify($A, $n); 

// This code is contributed by Rajput-Ji 
?> 





<!-- js -->

<!-- // Function to find rank  
function rankify(A , n){  
      // Rank Vector  
    R = [];
    lala = [];
      
    // Sweep through all elements in A for each  
    // element count the number of less than and  
    // equal elements separately in r and s.  
    for (i = 0; i < n; i++) {  
        r = 1;
        s = 1;
          
        for (j = 0; j < n; j++){  
            if (j != i && A[j] < A[i]){
              r += 1;
            }
                  
            if (j != i && A[j] == A[i]){
              s += 1;      
            }
        }  
          
        // Use formula to obtain rank  
        R[i] = r + (s - 1) / 2;
    }  
      
    for (i = 0; i < n; i++) {
      lala[lala.length] = R[i];
    } 

    return lala;
        
}   -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  Script



<script>

function carve(index, arr) {
  index = index + 1;
  // arr = arr.filter((_, index) => arr.hasOwnProperty(index)); //ES2015+
  arr = arr.filter(function(_, index) { return arr.hasOwnProperty(index); });
  arr.splice(0, index);
  return arr;
}

function pairwise(sad) {
  last_sad = sad.length-1;
  sad_arr = [];
  // console.log(sad);
  //
  for (let i = 0; i < sad.length; i++) {
    if (i != last_sad) {    
      element = carve(i, sad);
      sad_result = [];
      for (let j = 0; j < element.length; j++) {
        r = sad[i] - element[j];
        sad_result[sad_result.length] = r;  
      }
      sad_arr.push(sad_result);
    }
  }
  var sadfinal = [].concat.apply([], sad_arr);
  return sadfinal;
}

sad = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
spd = [8, 3, 9, 2, 4, 6, 5, 1, 10, 7];

console.log(pairwise(sad));
console.log(pairwise(spd));

var sum = sad.reduce(function(a, b) { return a + b; }, 0);

console.log(sum);


// const numbers = [10, 20, 30, 40] // sums to 100
// // function for adding two numbers. Easy!
// const add = (a, b) =>
//   a + b
// // use reduce to sum our array
// const sum = numbers.reduce(add);

</script>

</body>
</html>