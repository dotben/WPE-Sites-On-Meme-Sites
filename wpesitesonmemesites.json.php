<?php
require_once 'magpierss/rss_fetch.inc'; // http://magpierss.sourceforge.net/

$wpeDomains = getDomainsWPEHosted();

$wpeSitesOnTechMeme = array_values(array_intersect(getDomainsFromTechMeme(), $wpeDomains));
$wpeSitesOnHackerNews = array_values(array_intersect(getDomainsFromMemeSite("http://news.ycombinator.com/rss"), $wpeDomains));

$arrayForJSON = array("wpeSitesOnMemeSites" => array(
												"Hacker News" => $wpeSitesOnHackerNews,
												"TechMeme" => $wpeSitesOnTechMeme
												)
				);
				
echo json_encode($arrayForJSON);


function getDomainsFromMemeSite($feedUrl) {
    //get RSS feed from meme site, pull out urls, return array of normalized domains
    $rss = fetch_rss($feedUrl);
    $memeSiteDomains = array();    
    
    foreach ($rss->items as $item ) {
	$tempUrl = parse_url(strtolower($item[link]), PHP_URL_HOST); //take url from RSS, lowercase it, pull out just the hostname
        $tempUrl = str_replace ('www.','', $tempUrl); //in case there is a www
        $tempUrl = str_replace ('/','', $tempUrl); //remove a trailing '/'
        if ($tempUrl) {$memeSiteDomains[$tempUrl]++;} //insert domain as key to avoid dupes
    }
        
    return array_keys($memeSiteDomains);
}

function getDomainsFromTechMeme() {
    //get TechMeme front page then pull out all urls that are not techmeme.com (this is because TechMeme's RSS doesn't properly contain all urls on homepage)
    $html = file_get_contents("http://www.techmeme.com/");
    $techMemeSiteUrls = array();
    $techMemeSiteDomains = array();
    
    $html = strip_tags($html,"<a>");
	$d = preg_split("/<\/a>/",strtolower($html));
	foreach ( $d as $k=>$u ){
	    if( strpos($u, "<a href=") !== FALSE ){
	        $u = preg_replace("/.*<a\s+href=\"/sm","",$u);
	        $u = preg_replace("/\".*/","",$u);
	        $techMemeSiteUrls[] = $u;
	    }
	}
    
    foreach ($techMemeSiteUrls as $url ) {
        if ((stristr($url, 'techmeme.com') === FALSE) && (stristr($url, 'memeorandum.com') === FALSE) && (stristr($url, 'wesmirch.com') === FALSE)) {
            $tempUrl = parse_url(strtolower($url), PHP_URL_HOST); //take url from RSS, lowercase it, pull out just the hostname
            $tempUrl = str_replace ('www.','', $tempUrl); //in case there is a www
            $tempUrl = str_replace ('/','', $tempUrl); //remove a trailing '/'
            if ($tempUrl) {$techMemeSiteDomains[$tempUrl]++;} //insert domain as key to avoid dupes
        }
    }
    
    return array_keys($techMemeSiteDomains);
    
}

function getDomainsWPEHosted($wpeClientUrlListUrl) {
    //override for now, fake urls
    $domains = array("techcrunch.com","news.cnet.com");
    
    return $domains;
}

?>