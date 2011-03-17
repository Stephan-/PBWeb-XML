<?php 
function _iscurlinstalled() {
if  (in_array  ('curl', get_loaded_extensions())) {
  return true;
}
else{
  return false;
}
}

if (eregi("[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,6}",urldecode(strip_tags($_REQUEST['pbsvip'])))) 
{
  if ($pbsvip != urldecode(strip_tags($_REQUEST['pbsvip']))) 
  {
    $cleanipstr = @spliti (":", trim(urldecode(strip_tags($_REQUEST['pbsvip']))), 2);

      if (intval($cleanipstr[1]) < "65535" AND intval($cleanipstr[1]) > "0")
      {
        $pbsvip = $cleanipstr[0];
        $pbsvport = $cleanipstr[1];
        $pbsvserver = "$cleanipstr[0]:$cleanipstr[1]";
      }    
       
  } elseif ($pbsvip == urldecode(strip_tags($_REQUEST['pbsvip']))) {
  
    if (eregi($pbsvip,urldecode(strip_tags($_SERVER['REQUEST_URI'])))) 
    {
    } else {
      $cleanipstr = @spliti (":", trim(urldecode(strip_tags($_REQUEST['pbsvip']))), 2);

        if (intval($cleanipstr[1]) < "65535" AND intval($cleanipstr[1]) > "0")
        {
          $pbsvip = $cleanipstr[0];
          $pbsvport = $cleanipstr[1];
          $pbsvserver = "$cleanipstr[0]:$cleanipstr[1]";
        }    
      
    }
  }
} 
elseif (eregi("[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,6}",urldecode(strip_tags($_SERVER['REQUEST_URI'])))) 
{
  if (@preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,6}/i', $_SERVER['REQUEST_URI'], $matches))
  {
    $cleanipstr = @spliti (":", trim(urldecode(strip_tags($matches[0]))), 2);

      if (intval($cleanipstr[1]) < "65535" AND intval($cleanipstr[1]) > "0")
      {
        $pbsvip = $cleanipstr[0];
        $pbsvport = $cleanipstr[1];
        $pbsvserver = "$cleanipstr[0]:$cleanipstr[1]";
      }    
     
  }
}

if (eregi("[0-9A-Za-z]{1,32}",urldecode(strip_tags($_REQUEST['httpkey'])))) 
{
  if ($httpkey != urldecode(strip_tags($_REQUEST['httpkey']))) 
  {
    $httpkey = trim(urldecode(strip_tags($_REQUEST['httpkey'])));
  } elseif ($httpkey == urldecode(strip_tags($_REQUEST['httpkey']))) {
    if (eregi($httpkey,urldecode(strip_tags($_SERVER['REQUEST_URI'])))) 
    {
    } else {
      $httpkey = trim(urldecode(strip_tags($_REQUEST['httpkey'])));
    }
  }
}
elseif(eregi("\/httpkey\/",urldecode(strip_tags($_SERVER['REQUEST_URI'])))) 
{
  if (@preg_match('/\/httpkey\/(.*)/i', $_SERVER[REQUEST_URI], $matches))
  {  
    $str = @preg_replace('/\//', '', $matches[1]);
	$httpkey = trim(urldecode(strip_tags($str)));
  }
}

  
if (@eregi("/pb_sv_",urldecode(strip_tags($_SERVER['REQUEST_URI'])))) 
{
  if (@preg_match('/\/pb_sv_(.*)\//i', $_SERVER[REQUEST_URI], $matches))
  { 
    $str = preg_split ('/\//', $matches[0], -1, PREG_SPLIT_OFFSET_CAPTURE);
	$befehl = trim(urldecode(strip_tags($str[1][0])));
  } else {
    $befehl = "pb_sv_ver";
  }
} else {
  $befehl = "pb_sv_ver";
}

echo '<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="0.91">
<channel>
';	
if ($pbsvip AND $pbsvport AND $httpkey) 
{

  if (_iscurlinstalled()) 
  { 

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "http://$pbsvserver/pbsvweb");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "webkey=$httpkey&exec_cmd=To+console&exec_cmd_text=$befehl");

    $output = curl_exec($ch);
    curl_close($ch);
    
    $chars = preg_split('/pre>/', $output, -1, PREG_SPLIT_OFFSET_CAPTURE);
    $chars2 = preg_split('/\n/', $chars[1][0], -1, PREG_SPLIT_OFFSET_CAPTURE);

    $startit = false;
    foreach ($chars2 as $lastrow) {
      if ($lastrow[0] != "" AND $lastrow[0] != "</") {
        $startit = true;
echo '
<item>
	<title></title>
	<link></link>
	<description><![CDATA[';
print_r(strip_tags($lastrow[0]));
echo ']]></description>
	<pubDate></pubDate>
	<guid isPermaLink="false"></guid>
</item>';
      }   
    }  
  
    if ($startit == false) {
echo '
<item>
	<title></title>
	<link></link>
	<description><![CDATA[';
echo "Wrong Password ?";
echo ']]></description>
	<pubDate></pubDate>
	<guid isPermaLink="false"></guid>
</item>';

    }
  } 
  else 
  { 
echo '
<item>
	<title></title>
	<link></link>
	<description><![CDATA[';
echo "cURL is NOT installed";
echo ']]></description>
	<pubDate></pubDate>
	<guid isPermaLink="false"></guid>
</item>';  
}

} else {
echo '
<item>
	<title></title>
	<link></link>
	<description><![CDATA[';
echo "need more parameter";
echo ']]></description>
	<pubDate></pubDate>
	<guid isPermaLink="false"></guid>
</item>';
}
echo "</channel>\n";
echo '</rss>';	
?>