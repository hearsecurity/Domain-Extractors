<?php

error_reporting(0);

include("keys.php");

function load_dorks($dork_file) {

  $file = fopen($dork_file, "r");
  $dorks = array();

  while (!feof($file)) {
     $dorks[] = fgets($file);
  }
  fclose($file);
  return $dorks;
}

function extract_urls($dorks) {

$dork_count = count($dorks);
echo "\n[*] Dorks loaded: ".$dork_count . "\n";
echo "\n[*] Gathering sites..\n";
echo "[*] It may take a few minutes, Please wait...\n\n";

$sites = array();
$urls = array();
$page = 0;
$i = 0;

   while($i < $dork_count) {
      $dorks[$i] = str_replace(array("\n", "\r"), '', $dorks[$i]);
      $page = 0;

      echo "[*] Dorking: ". $dorks[$i] . "\n";

    while($page < 100) {

      $key = generate_keys();
      $url = "https://www.googleapis.com/customsearch/v1?key="
     .$key."&cx=013036536707430787589:_pqjad5hr1a&q=".urlencode($dorks[$i])."&alt=json&start=".$page."";
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_TIMEOUT, 25);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

      $output = curl_exec($curl);
      $array = json_decode($output, true);
      $count = 0;

      while($count < 9) {

        $sites[$count] = $array['items'][$count]['link'];
        $fp = fopen('sites.txt', 'a');
        if(!empty($sites[$count])) {
            fwrite($fp, $sites[$count] ."\n");
        }
        $count++;
      }
      $page += 10;

    }
    $i++;
  }
}


function banner() {

echo "  _   _                 ____                       _ _           \n";
echo " | | | | ___  __ _ _ __/ ___|  ___  ___ _   _ _ __(_) |_ _   _   \n";
echo " | |_| |/ _ \/ _` | '__\___ \ / _ \/ __| | | | '__| | __| | | |  \n";
echo " |  _  |  __/ (_| | |   ___) |  __/ (__| |_| | |  | | |_| |_| |  \n";
echo " |_| |_|\___|\__,_|_|  |____/ \___|\___|\__,_|_|  |_|\__|\__, |  \n";
echo "                                                         |___/   ";

    echo "\n-------------------------\n";
    echo "   Author: HearSecurity  \n";
    echo "----------------------------------\n";
    echo "   Tool: Google API Website spider    \n";
    echo "--------------------------------------------\n";
    echo "   Usage: googleapi.php <dork-file>         \n";
    echo "--------------------------------------------\n\n";
}


if(isset($argv[1])) {

    $dorks = load_dorks($argv[1]);
    extract_urls($dorks);
    echo "\n\n[*] Saving results to sites.txt\n";
    echo "\n";

}else{
    banner();
}

?>
