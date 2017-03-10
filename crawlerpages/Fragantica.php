<?php 
namespace CrawlerPages;

class Fragantica {
    public $visitLink       = []; //urls analizadas
    public $links           = []; //urls por analizar
    public $crawlerCounter  = 0;
    public $business        = []; //informacion extraida
    
    public $brands          = [];

    public $lastCounterBus  = 0;
    public $domain          = "http://www.fragrantica.es/buscar/";
    
    public $linkCrawler     = [];

    
    public function crawler($newLink = "") {
        if($newLink == "")
            $str = $this->getSslPage($this->linkCrawler[0]);
        else 
            $str = $this->getSslPage($newLink);

        $dom     = HtmlDomParser::str_get_html($str);
        $this->getData($dom); //obtiene la informacion de la primer pagina
    }

    public function getData($dom) {
        $this->analizeBrands($dom);
        //$this->analizePagination($dom);
        $this->crawlerCounter++;
    }
    
    public function analizeBrands($dom) {
        foreach($dom->find("#subnav div a") as $element) {
            $data = [
                "name"      => $element->find(".l-datos a h2 span")[0]->innertext,
                "link"      => $element->find(".l-btn-container a")[0]->href,
            ];

            $this->brands[] = $data;
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
        $headers[] = "Cookie: ASP.NET_SessionId=lvykvhxl0tc3ikglfzeksw4r";
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