<?php
# Managed Hosting Services : www.stardothosting.com
# Web Design Toronto : www.shift8web.com

#This program is free software: you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation, either version 3 of the License, or
#(at your option) any later version.

#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
#GNU General Public License for more details.
#
#You should have received a copy of the GNU General Public License
#along with this program in the LICENSE file. If not, see <http://www.gnu.org/licenses/>

header( 'Content-Type: text/plain' );
header( 'Cache-Control: max-age=0' );

// Cross Site Script  & Code Injection Sanitization
function xss_cleaner($input_str) {
    $return_str = str_replace( array('<',';','|','&','>',"'",'"',')','('), array('&lt;','&#58;','&#124;','&#38;','&gt;','&apos;','&#x22;','&#x29;','&#x28;'), $input_str );
    $return_str = str_ireplace( '%3Cscript', '', $return_str );
    return $return_str;
}

function purgeURL( $hostname, $ip_address, $purgeURL, $debug )
{
    print( "Purging " . $hostname . $pageURL . "\n" );

    $header = array
    (
        "Host: www." . $hostname, // IMPORTANT
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3",
        "Accept-Encoding: gzip,deflate,sdch",
        "Accept-Language: it-IT,it;q=0.8,en-US;q=0.6,en;q=0.4",
        "Cache-Control: max-age=0",
        "Connection: keep-alive",
    );


$curlOptionList = array(
        CURLOPT_URL                     => 'http://' . $ip_address . $purgeURL,
        CURLOPT_HTTPHEADER              => $header,
        CURLOPT_CUSTOMREQUEST           => "PURGE",
        CURLOPT_VERBOSE                 => true,
        CURLOPT_RETURNTRANSFER          => true,
        CURLOPT_NOBODY                  => true,
        CURLOPT_CONNECTTIMEOUT_MS       => 2000,
);

    //$curl = curl_init();
    //curl_setopt_array($curl, $curlOptionList);
    //curl_exec($curl);
    //curl_close($curl);


    $fd = false;
    if( $debug == true ) {
        print "\n---- Purge Output -----\n";
        $fd = fopen("php://output", 'w+');
        $curlOptionList[CURLOPT_VERBOSE] = true;
        $curlOptionList[CURLOPT_STDERR]  = $fd;
    }

    $curlHandler = curl_init();
    curl_setopt_array( $curlHandler, $curlOptionList );
    curl_exec( $curlHandler );
    curl_close( $curlHandler );
    if( $fd !== false ) {
        fclose( $fd );
    }


}


if(!empty($_POST['urlpurge']) && !empty($_POST['domainpurge']))
{
        $url_purge = $_POST['urlpurge'];
        $domain_purge = $_POST['domainpurge'];

	// This is the array of ip addresses for each varnish server you want to receive the purge request
        $ip_array = array("10.0.0.1", "10.0.0.2", "10.0.0.3");
        $URL      = xss_cleaner($url_purge);
        $host_name = xss_cleaner($domain_purge);
        $debug    = true;
        print "Updating the article in the database ...\n";
        foreach ($ip_array as &$ipaddress) {
                purgeURL( $host_name, $ipaddress, $URL, $debug );
        }
}
?>
