<?php 
    include "vendor/autoload.php";
    include "crawlerpages/SeccionAmarilla.php";
    use CrawlerPages\SeccionAmarilla;

    set_time_limit(3000);
    @session_start();
    function dd($arr) {
        echo "<pre>";
        print_r($arr);
        echo "</pre>"; exit;
    }

    $crawler = new SeccionAmarilla();

    $crawler->crawler(); //init page

    foreach($crawler->links as $link) {
        if(!in_array($link, $crawler->visitLink)) {
            $crawler->crawler($crawler->domain.$link);

           if(count($crawler->business) >= 100) 
               break;
           
        }
    }

    
    if(isset($_SESSION["permanentData"]) && $_SESSION["permanentData"] !== ""){
        $oldData = json_decode($_SESSION["permanentData"]);
        $newData = array_merge($oldData, $crawler->business);
        $_SESSION["permanentData"] = json_encode($newData);
    } else {
        $_SESSION["permanentData"] = json_encode($crawler->business);
    }

    dd($crawler->business);

?>