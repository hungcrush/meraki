<?php
date_default_timezone_set('Asia/Bangkok');
$st_time    =   strtotime($resttimefrom);
$end_time   =   strtotime($resttimeto);
$cur_time   =   strtotime(now);

echo date('H:i:s', $cur_time);