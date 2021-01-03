<?php
 include 'functions.php';

$prices = [];
$images = [];
$product = null;
$productDescription = null;
$isProductActive = false;
$url = "";


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['parse_link'])){

    $url = isset($_POST['url']) ? $_POST['url'] : null;
    if($url){
        $isProductActive = true;
        $html = file_get_contents($url);
        $htmlDom = new DOMDocument();
        @$htmlDom->loadHTML($html);
        $product = get_product_title($htmlDom);
        $prices = get_product_costs($htmlDom);
        $productDescription = get_product_description($htmlDom);
        $images = get_product_images($htmlDom);
    }

}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save-to-list'])){

    $url = isset($_POST['url']) ? $_POST['url'] : null;
    $product = isset($_POST['product']) ? $_POST['product'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $details = isset($_POST['details']) ? $_POST['details'] : null;
    $fImg = isset($_POST['featuredImg']) ? $_POST['featuredImg'] : null;
    if($url && $product && $price && $details && $fImg){
        $id = save_product($product, $price, $details, $fImg, $url);
        if($id){

        }
    }

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wish List</title>
</head>
<body>
<h3>Add Items to your wish list</h3>
<form method="post">
    Enter the url: <input type="text" name="url" placeholder="Enter your url" autocomplete="off" value="<?php echo $url;?>"/>
    <button name="parse_link" type="submit">Submit</button>
    <br>
    <?php if($isProductActive):?>
    <br>
    Title: <input type="text" value="<?Php echo $product;?>" name="product"/><br>
    <br>
    Pricing: <br>
    <?Php foreach ($prices as $price):?>
        <input type="radio" name="price" value="<?php echo $price;?>"/><?php echo $price;?>
        <br>
    <?php endforeach;?>
    <br>
        Description:<br>
    <textarea rows="7" cols="50" name="details"><?php echo$productDescription;?></textarea>

    <h4>Product Images</h4>
        <?Php foreach ($images as $img):?>
            <input type="radio" name="featuredImg" value="<?php echo $img;?>"/>
            <a target="_blank" href="<?php echo $img;?>"><img width="50px" height="50px" src="<?php echo $img;?>"/></a>
            <br>
        <?php endforeach;?>
        <?php
            if(count($images) <= 0){
                echo "Add your image: <input type='text' name='featuredImg'/><br>";
            }
        ?>
    <br>
    <button type="submit" name="save-to-list">Save to wish list</button>
    <?php endif;?>
</form>

<h3>My Wish list</h3>
<?php foreach (get_all_products() as $product):?>
<div style="border: 1px solid #ccc; padding: 5px; border-radius: 3px; margin: 4px;">
    <h3>
        <img width="50px" src="<?php echo $product['featured'];?>">
        <?php echo $product["title"];?></h3>
    <hr>
    <br><span style="font-weight: bold;"><?php echo $product['price'];?>
    | <a target="_blank" href="<?php echo $product["source"];?>">Go to Ebay :( </a></span>
    <p><?php echo $product["details"];?></p>
    <span><a target="_blank" href="share.php?id=<?php echo $product["id"];?>">Share via email</a></span>
</div>
<?php endforeach;?>
<div style="margin-bottom: 100px;"></div>
</body>
</html>
