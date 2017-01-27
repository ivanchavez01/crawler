<?php 
    include "vendor/autoload.php";

    use Sunra\PhpSimple\HtmlDomParser;
    
    set_time_limit(3000);

    function dd($arr) {
        echo "<pre>";
        print_r($arr);
        echo "</pre>"; exit;
    }

    function get_business($dom) {
        $business = [];
         foreach($dom->find("ul.list li") as $element) {
            $phone = $element->find(".l-tel a span");
            $data = [
                "name"      => $element->find(".l-datos a h2 span")[0]->innertext,
                "website"   => $element->find(".l-btn-container a")[0]->href,
                "address"   => $element->find(".l-datos .l-address span")[0]->innertext,
                "neibor"    => $element->find(".l-datos .l-address span")[1]->innertext,
                "city"      => $element->find(".l-datos .l-address span")[2]->innertext,
                "state"     => $element->find(".l-datos .l-address span")[3]->innertext,
                "phone"     => isset($phone[0]->href) ? $phone[0]->href : "",
                "infopage"  => $element->find(".l-datos a")[0]->href
            ];

            $business[] = $data;
        }

        return $business;
    }

    function getSslPage($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($curl);
        curl_close($curl);
        return $str;
    }

    $str = getSslPage("https://www.seccionamarilla.com.mx/resultados/restaurantes/1");
    
    if($str !== "") {
        $dom = HtmlDomParser::str_get_html($str);
        $businessData   = [];
        $visitLink      = ["#"];
        $businessData   = get_business($dom);

        foreach($dom->find(".pagination li a") as $link) {
            if(!in_array($link->href, $visitLink)) {
                $visitLink[] = $link->href;

                $strNext    = getSslPage("https://www.seccionamarilla.com.mx".$link->href);
                $domNext    = HtmlDomParser::str_get_html($str);
                $items      = get_business($domNext);
            }
        }
        
        exit;

    } else {
        echo "pagina vacia";
    }

?>