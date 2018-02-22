<?php
$connomains = array(
'http://127.0.0.1/xgj/source/offlin/index.php',
'http://127.0.0.1/xgj/source/offlin/index.php',
'http://127.0.0.1/xgj/source/offlin/index.php',
'http://127.0.0.1/xgj/source/offlin/index.php',
);
$mh = curl_multi_init();
foreach ($connomains as $i => $url) {
     $conn[$i]=curl_init($url);
      curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);
      curl_multi_add_handle ($mh,$conn[$i]);
}
do { $n=curl_multi_exec($mh,$active); } while ($active);
foreach ($connomains as $i => $url) {
      $res[$i]=curl_multi_getcontent($conn[$i]);
      curl_close($conn[$i]);
}
print_r($res);