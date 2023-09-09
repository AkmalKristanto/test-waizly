<?php

function plusMinus($arr) {
         $postive = 0;
         $negative = 0;
         $neutral = 0;
         $arrsize  = sizeof($arr);
         for ($i = 0; $i < $arrsize; $i++){
            if ($arr[$i] > 0){
               $positive++;
            }else if ($arr[$i] < 0){
               $negative++;
            }else if ($arr[$i] === 0){
               $neutral++;
            }
         }
         $pos_res = number_format($positive / $arrsize, $arrsize);
         $neg_res = number_format($negative/ $arrsize, $arrsize);
         $neu_res = number_format($neutral / $arrsize, $arrsize);
   
         echo $pos_res . PHP_EOL;
         echo $neg_res . PHP_EOL;
         echo $neu_res. PHP_EOL;
}
$n = intval(trim(fgets(STDIN)));
$arr_temp = rtrim(fgets(STDIN));
$arr = array_map('intval', preg_split('/ /', $arr_temp, -1, PREG_SPLIT_NO_EMPTY));      
print_r(plusMinus($arr));

?>