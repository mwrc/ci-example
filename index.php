<?php
require_once("includes/api_functions.inc.php");
require_once("includes/config.inc.php");
require_once("includes/functions.inc.php");

$cat_id = (int)$_GET['cat_id'];

$request_params = array();
$request_params["category_id"] = $cat_id;
$request_params["include_cross_sell_products"] = "yes";
$request_params["include_link_info"] = "yes";
$request_params["include_meta_info"] = "yes";
$request_params["include_category_products"] = "yes";
$request_params["recurse"] = "yes";
$request_params["recurse_parent_categories_downward"] = "yes";
$request_params["show_empty_objects"] = "yes";
$request_params["include_offline_products"] = "yes";
$request_params["include_offline_retailers"] = "yes";

// Build the possible params
$params = array();
foreach($request_params as $param=>$val) $params[] = $param."=".$val;

//print $cookie_string_for_api;


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Channel Islands - <?php echo (strlen($category->name))?" - ".$category->name:""; ?></title>

    <!------- MWRC SESSION HANDLING -------->
    
    <?php if(isset($_GET['mwrc_session_code']) && !$_COOKIE['mwrc_session_code_1_1']):?>
    <script type="text/javascript">
    <!--
    	document.cookie="mwrc_session_code_1_1=<?php echo $_GET['mwrc_session_code'] ?>; path=/";
    //-->
    </script>
    <?php endif; ?>
    
    <?php if($_COOKIE["mwrc_sync"]!="yes"):
    	$set_session_url="//channelislands.mwrc.net/set_session.php/". strtr(base64_encode(implode("", array_reverse(preg_split("//", gzcompress(serialize($_COOKIE)), -1, PREG_SPLIT_NO_EMPTY)))), array("+"=>"-", "/"=>"_", "="=>".")). "/cookie"; ?>
    
    <script type="text/javascript" src="<?php echo $set_session_url ?>.js"></script>
    <script type="text/javascript">
    <!--
        if (typeof mwrc_cookie_name != 'undefined' && mwrc_cookie_name.length && mwrc_cookie_value.length) {document.cookie=mwrc_cookie_name+"="+mwrc_cookie_value+"; path=/"; location.reload(true);}
        document.cookie="mwrc_sync=yes; path=/";
        if (mwrc_cookie_redirect=="yes") { location.reload(true); }
        else if (mwrc_cookie_redirect=="location") { window.location.assign('<?php echo $set_session_url?>.php'); }
    //-->
    </script>
    
    <?php endif; ?>

    <!------- /MWRC SESSION HANDLING -------->

    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    
    <!-- Optional theme -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    
    <link rel="stylesheet" type="text/css" href="css/main.css" media="all" charset="utf-8" />
    
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    
    <script type="text/javascript">
  	  var mwrc_widget_config = {
                                "container": ".cart_container", //Define shopping cart widget container
                                "template": { // create 'template' object to bypass default output.
                                              "items":"#mwrc_cart_qty", // CSS ID
                                              "subtotal":"#mwrc_cart_subtotal", //CSS ID
                                              "checkout_link":"#mwrc_checkout_link", //Your checkout link
                                              "account_link":"#mwrc_account_link" //Your account link
                                            }
                                };
    </script>
    
    <script type="text/javascript" src="http://channelislands.mwrc.net/js/cart-widget.js"></script>        

</head>

<body>

<div class="container">
<?php

// Submit request to MWRC
$xml = curl_get($mwrc_domain, $mwrc_lang_abbrev, "category.xml", implode("&", $params));


$xmlobj = simplexml_load_string($xml);
$category =& $xmlobj->category;
//print_r($category->products->product);
//exit;

$parent_cats = array(0=>array("cat_id"=>0, "name"=>"Product Categories"));

getParentCats($category->category_ancestry, $parent_cats);

if((int)$category["category_id"]>0) array_push($parent_cats, array("cat_id"=>(int)$category["category_id"], "name"=>(string)$category->name));

?>

    <h1>Channel Islands Catalog</h1>

        <div class="cart_container">
            <a href="<?php echo $mwrc_retailer_domain ?>/en/shopping-cart.php">
              <span id="mwrc_cart_qty"></span> items <!-- Example output: 2 -->
              <span id="mwrc_cart_subtotal"></span> <!-- Example output: $59.99 --> | 
              <a id="mwrc_checkout_link">Checkout</a> | 
              <a id="mwrc_account_link">Account</a>
            </a>
        </div>



<?php if(count($parent_cats)): ?>
<ul class="bread_crumbs">
<?php foreach((array)$parent_cats as $cat): ?>
<li>
<?php if((int)$category["category_id"]!=$cat["cat_id"]): ?>
<a href="index.php?cat_id=<?php echo $cat["cat_id"] ?>"><?php echo $cat['name'] ?></a>
<?php else: echo $cat["name"]; endif; ?>
</li>
<?php endforeach; ?>
</ul>
<div class="clear"></div>
<?php endif; ?>
<hr />

<?php if(count($category->child_categories->category)): ?>


<ul id="cats" class="list">
<?php foreach($category->child_categories->category as $sub_category): 
?>
<li class="item">
<a href="index.php?cat_id=<?php echo (int)$sub_category["category_id"] ?>"><?php echo htmlentities((string)$sub_category->name) ?></a>
</li>
<?php endforeach; ?>
</ul>

<?php foreach($category->child_categories->category as $sub_category): ?>
<option value="<?php echo (int)$sub_category["category_id"] ?>"><?php echo htmlentities((string)$sub_category->name) ?></option>
<?php endforeach; ?>

<?php endif; ?>


<?php if(count($category->products->product)): ?>

<h1>Products</h1>

<ul id="products" class="list">
<?php foreach($category->products->product as $product): 
//print_r($product);
?>

<li class="item">
<img src="<?php echo $product->image_url ?>" width="100" height="100" /><br />
<a href="product.php?id=<?php echo (int)$product["product_id"] ?>"><?php echo htmlentities((string)$product->name) ?></a><br />

<?php if(isset($product->discounted_retail_price)): ?>
<span class="price msrp"><?php echo $product->price ?></span>
<span class="price sale"><?php echo $product->discounted_retail_price ?></span>
<?php else: ?>
<span class="price sale"><?php echo $product->price ?></span>
<?php endif; ?>

</li>


<?php endforeach; ?>
</ul>

<?php endif; ?>
</div>
</body>

</html>


