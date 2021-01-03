<?php

$url = "https://www.ebay.com/itm/Asus-VivoBook-15-6-FHD-Laptop-AMD-Ryzen-3-3200U-4GB-128GB-F512DA-WH31-NEW/184464799047?_trkparms=aid%3D111001%26algo%3DREC.SEED%26ao%3D1%26asc%3D20180816085401%26meid%3Dffe31ec67d2f46f58f2cc7f182b86720%26pid%3D100970%26rk%3D2%26rkt%3D4%26mehot%3Dpp%26sd%3D393013259268%26itm%3D184464799047%26pmt%3D1%26noa%3D1%26pg%3D2380057%26brand%3DASUS&_trksid=p2380057.c100970.m5481&_trkparms=pageci%3A68776298-3b32-11eb-b379-3a805c291fa5%7Cparentrq%3A4ea6d5581760ad4a4ceda718fffe8dd2%7Ciid%3A1";

$html = file_get_contents($url);
$htmlDom = new DOMDocument();
@$htmlDom->loadHTML($html);

$title = $htmlDom->getElementById('itemTitle');
echo $title->textContent;
