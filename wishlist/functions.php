<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}

function save_product($title, $price, $details, $featured, $source){
    $conn = get_connection();
    $sql = "INSERT INTO wish_list (`title`,`price`,`details`,`featured`,`source`)
       VALUES (?,?,?,?,?)";
    $query = $conn->prepare($sql);
    $query->execute([$title, $price, $details, $featured, $source]);
    return $conn->lastInsertId();
}

function get_all_products(){
    $results = [];
    try {
        $conn = get_connection();
        $results = $conn->query("SELECT * from wish_list");
    }catch (Exception $e){

    }
    return $results;
}

function get_product($id){
    $results = [];
    try {
        $conn = get_connection();
        $query = $conn->prepare("SELECT * from wish_list WHERE id=? LIMIT 1");
        $query->execute([$id]);
        $results = $query->fetchAll();
        if($results){
            $results = $results[0];
        }
    }catch (Exception $e){

    }
    return $results;
}



function get_product_title($dom){
    $title = $dom->getElementById("itemTitle");
    return $title->textContent;
}

function get_product_costs($dom){
    $prices = [];
    $price = $dom->getElementById('vi-mskumap-none');
    if($price && property_exists($price, 'textContent')){
        $prices[] = 'General: ' . preg_replace('/\s+/', ' ', $price->textContent);
    }
    $price = $dom->getElementById('prcIsum');
    if($price && property_exists($price, 'textContent')){
        $prices[] = 'Savings: ' . preg_replace('/\s+/', ' ', $price->textContent);
    }
    return $prices;
}


function get_product_description($dom){
    $desc = $dom->getElementById('viTabs_0_is');
    return preg_replace('/\s+/', ' ', $desc->textContent);
}

function get_product_images($dom){
    $images = [];
    $imagesDiv = $dom->getElementById('vi_main_img_fs');
    if($imagesDiv){
        $links = $imagesDiv->getElementsByTagName("img");
        foreach ($links as $link){
            $linkHref = $link->getAttribute('src');
            if(!empty($linkHref)){
                $images[] = $linkHref;
            }
        }
    }
    return $images;
}