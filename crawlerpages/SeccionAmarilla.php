<?php
namespace CrawlerPages;
use Sunra\PhpSimple\HtmlDomParser;

class SeccionAmarilla {
    public $visitLink       = ["#"]; //urls analizadas
    public $links           = []; //urls por analizar
    public $crawlerCounter  = 0;
    public $business        = []; //informacion extraida
    public $lastCounterBus  = 0;
    public $domain          = "https://www.seccionamarilla.com.mx";
    
    public $linkCrawler     = [
        "https://www.seccionamarilla.com.mx/resultados/restaurantes/1"
    ];
    

    public function crawler($newLink = "") {
        if($newLink == "")
            $str = $this->getSslPage($this->linkCrawler[0]);
        else 
            $str = $this->getSslPage($newLink);

        $dom        = HtmlDomParser::str_get_html($str);
        $this->getData($dom); //obtiene la informacion de la primer pagina
    }

    public function getData($dom) {
        $this->analizeBusiness($dom);
        $this->analizePagination($dom);
        $this->crawlerCounter++;
    }
    
    public function analizeBusiness($dom) {
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

            $this->business[] = $data;
        }
    }

    public function analizePagination($dom) {
        foreach($dom->find(".pagination li a") as $link) {
            if(!in_array($link->href, $this->visitLink)) {
                $this->links[] = $link->href;
            }
        }
    }

    public function getSslPage($url) {
        $curl = curl_init();
        $headers[] = "Cookie: ASP.NET_SessionId=pfmljjdefw0ch023tze3bq2e";
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $str = curl_exec($curl);
        curl_close($curl);
        return $str;
    }
}
?>