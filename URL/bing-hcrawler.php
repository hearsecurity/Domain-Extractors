<?php

error_reporting(0);

include("keys2.php");

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
    echo "   Tool: Bing Website spider    \n";
    echo "------------------------------------------\n";
    echo "   Usage: bing-hcrawler.php <dork-file>    \n";
    echo "-----------------------------------------\n\n";
}

function file_get_contents_curl($url) {

  $useragent = random_user_agent();
  $cookie = "cookie.txt";

  $ch = curl_init();
  curl_setopt ($ch, CURLOPT_URL, $url);
  curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt ($ch, CURLOPT_USERAGENT, $useragent);
  curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
  curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt ($ch, CURLOPT_REFERER, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function website_crawler($query) {

  $page = 0;
  $counter = 0;

  echo "\n[*] Dorks Loaded: ".count($query) . "\n";
  echo "[*] Gathering sites..\n";
  echo "[*] It may take a few minutes, Please wait...\n\n";

  while($counter < count($query)) {

     $page = 0;
     $engine = "www.bing.com";
     echo "[*] Dorking: ". $query[$counter];

     while($page < 110) {

        $url = "https://".$engine."/search?q=".urlencode($query[$counter]).'&first='.$page;
        $scrape = file_get_contents_curl($url);

        preg_match_all('#\b(http[s]?://|ftp[s]?://){1,}?([-a-zA-Z0-9\.]+)([-a-zA-Z0-9\.]){1,}([-a-zA-Z0-9_\.\#\@\:%_/\?\=\~\-\//\!\'\(\)\s\^\:blank:\:punct:\:xdigit:\:space:\$]+)#si', $scrape, $result);

        foreach($result[0] as $url) {

           if(strstr($url, "http") || strstr($url, "https")) {
            $url = str_replace('"'," ",$url);
            $domain = $url;

             if($domain == $engine) {
                  echo "";
              } else {
                  $fp = fopen('sites.txt', 'a');
                  if(!empty($domain)) {
                      fwrite($fp, $domain ."\n");
                  }
              }

            }
       }
       $page += 11;
     }
     $counter++;
  }
}

function load_dorks($dork_file) {

  $file = fopen($dork_file, "r");
  $dorks = array();

  while (!feof($file)) {
     $dorks[] = fgets($file);
  }
  fclose($file);
  return $dorks;
}

$domains = array();
$page = 0;

if(isset($argv[1])) {

    $dorks = load_dorks($argv[1]);
    website_crawler($dorks);
    echo "\n\n[*] Saving results to sites.txt\n";
    echo "\n";

}else{
    banner();
}

?>
