
<?php

if($_POST)
{
    $_url = $_POST['url'];
    if (get_domain($_url) == "app.goo.gl")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch); // $a will contain all headers
        curl_close($ch);
        if (preg_match_all('/<meta([^>]+)content="([^>]+)>/', $contents, $matches))
        {
            $doc = new DOMDocument();
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . implode($matches[0]));
            $tags = array();
            foreach($doc->getElementsByTagName('meta') as $metaTag)
            {
              if ($metaTag->getAttribute('property') == "og:image")
              {
                $tags = $metaTag->getAttribute('content');
              }
            }
        }

         $url = $tags;
         $_start     = strstr($url,'?center=');
         $_end       = strstr($_start,'&size=');
         $_center    = str_replace($_end,'',$_start);
         $_q         = str_replace('?center=','',$_center);
         $_location  = explode("&", $_q);
         $location   = $_location[0];
         $_zoom      = $_location[1];
         $z          = str_replace('zoom=','',$_zoom);
         $_l         = explode("%2C", $location);
         if( $_SERVER['SERVER_NAME']  == "localhost")
        {
             $c = 0;
        }else{
            $c       = 9.99547728 - 9.99109992 ;
        }
         $y          =   40.75515040  - 40.75515038 ;
         $x          =  -73.89744780 - (-73.89525912);
         $lat        = $_l[0] + $y;
         $log        = $_l[1] + $x +$c;

        $l     = array(
            "lat"  => $lat,
            "log"  => $log,
            "z"    => $z
        );
    }else
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $a   = curl_exec($ch); // $a will contain all headers
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL
        $url; // Voila
        $str     = strstr($url,'@');
        if($str !="")
        {
            $strstr     = strstr($url,'@');
            $endurl     = strstr($url,'z'); //get 3d ,4d
            $dirction   = strstr($url,'!3d');
            if($dirction !="")
            {
                $_dirction  = explode("!", $dirction);
                $lat        = str_replace('3d','',$_dirction[1]);
                $_4d        = str_replace('4d','',$_dirction[2]);
                $extra      = strstr($_4d,'?');
                $log        = str_replace($extra,'',$_4d);
                $q          = str_replace($endurl,'',$strstr);
                $location   = str_replace('@','',$q);
                $_location  = explode(",", $location);
                $z          = $_location[2];
            }else{
                $_location        = str_replace('@','',$strstr);
                $location         = str_replace('z','',$_location);
                $location         = explode(",", $location);
                $lat              = $location[0];
                $log              = $location[1];
                $z                = $location[2];
            }


        }else{
             $strstr     = strstr($url,'q');
             $endurl     = strstr($url,'&');
             $q          = str_replace($endurl,'',$strstr);
             $_q         = str_replace('q=','',$q);
             $_location  = explode(",", $_q);
             $lat        = $_location[0];
             $log        = $_location[1];
             $z          = 17;
        }
        $l          = array(
            "lat"  => $lat,
            "log"  => $log,
            "z"    => $z
        );
    }
}else{
   $_url ="";
}


function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
  }
  return false;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>LAT && log</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2>LAT && log</h2>
    <form action="test.php" method="post" enctype="multipart/form-data">
      <div class="form-group">
      <label for="email">url:</label>
        <input type="text" class="form-control" name="url" value="<?php echo $_url;?>" width="500px">
    </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <br>

    <div class="form-group">
        <iframe width='700' height='440' src='https://google.com/maps?hl=en&amp;z=<?php echo $z; ?>&q=+(<?php echo $l['lat']; ?>,<?php echo $l['log']; ?>)&amp;output=embed' frameborder='0'></iframe>
    </div>

</div>



</body>
</html>




