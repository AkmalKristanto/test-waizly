<?php

function convert_24($time)
    {
     // Get hours 
     $str_time_1 = intval($time[1]);
     $str_time_2 = intval($time[0]);
     $hours = ($str_time_2 * 10 + $str_time_1 % 10);
     if ($time[8] == 'A'){                        // Time is in "AM"
        if ($hours == 12){
           echo "00";
           for ($i = 2; $i <= 7; $i++)
              echo $time[$i];
        }else{
           for ($i = 0; $i <= 7; $i++)
              echo $time[$i];
        }
     }else{                                       // Time is in "PM"
        if ($hours == 12){
           echo "12";
           for ($i = 2; $i <= 7; $i++)
              echo $time[$i];
        }else{
           $hours = $hours + 12;
           echo $hours;
           for ($i = 2; $i <= 7; $i++)
              echo $time[$i];
        }
     }
    }
    $time = "07:05:45PM";
    print_r(convert_24($time));

?>