<?php
  $arr = [1, 2, 3, 4, 5];
  function findMinMaxSum($arr = []) {
     $numbers = array_slice($arr, 0);
     sort($numbers);
     $max_score = 0;
     $min_score = 0;
         
     for($i = 0; $i < count($numbers) - 1; $i++) {
        $min_score += $numbers[$i];
     }

     for($j = 1; $j < count($numbers); $j++) {
        $max_score += $numbers[$j];
     }
         
     return [$min_score, $max_score];
  }
  print_r(findMinMaxSum($arr));
?>