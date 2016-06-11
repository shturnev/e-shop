<?
require_once("functions/DB.php");
require_once("functions/auth.php");
require_once("functions/path.php");
require_once("functions/product.php");

$stranica   = "main";
$this_page  = path_withoutGet();

    if($_GET["cat_id"]){ $cat_id = $_GET["cat_id"]; }
    else if($_POST["cat_id"]){$cat_id = $_GET["cat_id"]; }
    else{ $cat_id = null;}


    if($_GET["page"]){ $page = $_GET["page"]; }
    else if($_POST["page"]){$page = $_GET["page"]; }
    else{ $page = null;}



/*------------------------------
Достенем инфо про эту страницу
-------------------------------*/
$resInfo = db_row("SELECT * FROM page_settings WHERE stranica='".$stranica."'")["item"];
if($resInfo){ $resInfo["meta"] = json_decode($resInfo["meta"], true); }

/*------------------------------
Соберём категории
-------------------------------*/
$resCats = db_select("SELECT * FROM categories ORDER BY title")["items"];

/*------------------------------
Выведем инфо для большого слайдера
-------------------------------*/
$resBigSlider = db_select("SELECT * FROM bigSlider WHERE stranica='".$stranica."' ORDER BY nomer")["items"];

/*------------------------------
Вывод записей
-------------------------------*/
$arr = [
    "cat_id"     => $cat_id
    ,"search"    => @$_POST["search"]
    ,"page"      => $page
    ,"limit"     => 6
];

$resProducts = (!$_POST["search"])? products_get_1($arr, true) : products_get_2($arr, true) ;




?>



<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><? echo $resInfo["meta"]["title"]; ?></title>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/for_index.css" />
	
	<meta name="keywords" content="<? echo $resInfo["meta"]["keywords"]; ?>" />
	<meta name="description" content="<? echo $resInfo["meta"]["desc"]; ?>" />
	
	
</head>
<body>
	
<!-- Shell -->	
<div class="shell">
	
	<!-- Header -->	
	<div id="header">
		<h1 id="logo"><a href="#">shoparound</a></h1>	
		
		<!-- Cart -->
		<div id="cart">
			<a href="#" class="cart-link">Your Shopping Cart</a>
			<div class="cl">&nbsp;</div>
			<span>Articles: <strong>4</strong></span>
			&nbsp;&nbsp;
			<span>Cost: <strong>$250.99</strong></span>
		</div>
		<!-- End Cart -->
		
		<!-- Navigation -->
		<div id="navigation">
			<ul>
			    <li><a href="#" class="active"><? echo $resInfo["btn_title"]; ?></a></li>
			    <li><a href="#">Support</a></li>
			    <li><a href="#">My Account</a></li>
			    <li><a href="#">The Store</a></li>
			    <li><a href="#">Contact</a></li>
			</ul>
		</div>
		<!-- End Navigation -->
	</div>
	<!-- End Header -->
	
	<!-- Main -->
	<div id="main">
		<div class="cl">&nbsp;</div>
		
		<!-- Content -->
		<div id="content">
			
			<!-- Content Slider -->
            <? if($resBigSlider): ?>
			<div id="slider" class="box">
				<div id="slider-holder" class="jcarousel">
					<ul>
                        <? foreach ($resBigSlider as $item) { ?>
					    <li><a href="#" style="background-image:url('FILES/forSlider/big/<? echo $item["photo"]; ?>');"></a></li>
                        <? } ?>
					</ul>
				</div>
				<div id="slider-nav">
                    <? for ($i = 0; $i < count($resBigSlider); $i++): ?>
                        <a href="#"><? echo i; ?></a>
                    <? endfor; ?>
				</div>
			</div>
            <? endif; ?>
			<!-- End Content Slider -->
			
			<!-- Products -->
            <? if($resProducts["items"]): ?>
			<div class="products">
				<div class="cl">&nbsp;</div>
				<ul>
                    <? $i = 0; foreach ($resProducts["items"] as $item) {
                       ++$i;
                       $last = ($i == 3)? "last" : null;
                       $type = ($item["type"] == 1)? "MEN'S": "WOMEN’S";
                       if($last){ $i = 0; }



                    ?>
				    <li class="<? echo $last; ?>">
				    	<a href="#"><img src="FILES/products/small/<? echo $item["photo"]; ?>" alt="" /></a>
				    	<div class="product-info">
				    		<h3><? echo $item["title"]; ?></h3>
				    		<div class="product-desc">
								<h4><? echo $type; ?></h4>
				    			<p><? echo mb_substr(strip_tags($item["text"]), 0, 20, "utf-8"); ?></p>
				    			<strong class="price"><? echo "\$".$item["price"] ?></strong>
				    		</div>
				    	</div>
			    	</li>
                    <? } ?>
				</ul>
				<div class="cl">&nbsp;</div>
			</div>
            <? endif; ?>
			<!-- End Products -->
			
		</div>
		<!-- End Content -->
		
		<!-- Sidebar -->
		<div id="sidebar">
			
			<!-- Search -->
			<div class="box search">
				<h2>Search by <span></span></h2>
				<div class="box-content">
					<form action="<? echo $this_page ?>" method="post">
						
						<label>Keyword</label>
						<input type="text" class="field" name="search"  />
						
						<label>Category</label>
						<select class="field">
                            <? if($resCats): foreach ($resCats as $item) { ?>
                                <option value="<? echo $item["ID"] ?>"><? echo $item["title"] ?></option>

                            <?} endif; ?>
						</select>
						
				<!--		<div class="inline-field">
							<label>Price</label>
							<select class="field small-field">
								<option value="">$10</option>
							</select>
							<label>to:</label>
							<select class="field small-field">
								<option value="">$50</option>
							</select>
						</div>-->
						
						<input type="submit" class="search-submit" value="Search" />
						
						<p>
							<a href="#" class="bul">Advanced search</a><br />
							<a href="#" class="bul">Contact Customer Support</a>
						</p>
	
					</form>
				</div>
			</div>
			<!-- End Search -->
			
			<!-- Categories -->
			<div class="box categories">
				<h2>Categories <span></span></h2>
				<div class="box-content">
                    <? if($resCats): ?>
					<ul>
                        <? foreach ($resCats as $item) {
                            $active = ($cat_id == $item["ID"])? "active" : null;

                            ?>
					    <li><a href="<? echo $this_page."?cat_id=".$item["ID"]; ?>" class="<? echo $active ?>"><? echo $item["title"] ?></a></li>
                        <? } ?>
					</ul>
                    <? endif; ?>
				</div>
			</div>
			<!-- End Categories -->
			
		</div>
		<!-- End Sidebar -->
		
		<div class="cl">&nbsp;</div>
	</div>
	<!-- End Main -->
	
	<!-- Side Full -->
	<div class="side-full">
		
		<!-- More Products -->
		<div class="more-products">
			<div class="more-products-holder">
				<ul>
				    <li><a href="#"><img src="images/small1.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small2.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small3.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small4.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small5.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small6.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small7.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small1.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small2.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small3.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small4.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small5.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small6.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small7.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small1.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small2.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small3.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small4.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small5.jpg" alt="" /></a></li>
				    <li><a href="#"><img src="images/small6.jpg" alt="" /></a></li>
				    <li class="last"><a href="#"><img src="images/small7.jpg" alt="" /></a></li>
				</ul>
			</div>
			<div class="more-nav">
				<a href="#" class="prev">previous</a>
				<a href="#" class="next">next</a>
			</div>
		</div>
		<!-- End More Products -->
		
		<!-- Text Cols -->
		<div class="cols">
			<div class="cl">&nbsp;</div>
			<div class="col">
				<h3 class="ico ico1">Donec imperdiet</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec imperdiet, metus ac cursus auctor, arcu felis ornare dui.</p>
				<p class="more"><a href="#" class="bul">Lorem ipsum</a></p>
			</div>
			<div class="col">
				<h3 class="ico ico2">Donec imperdiet</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec imperdiet, metus ac cursus auctor, arcu felis ornare dui.</p>
				<p class="more"><a href="#" class="bul">Lorem ipsum</a></p>
			</div>
			<div class="col">
				<h3 class="ico ico3">Donec imperdiet</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec imperdiet, metus ac cursus auctor, arcu felis ornare dui.</p>
				<p class="more"><a href="#" class="bul">Lorem ipsum</a></p>
			</div>
			<div class="col col-last">
				<h3 class="ico ico4">Donec imperdiet</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec imperdiet, metus ac cursus auctor, arcu felis ornare dui.</p>
				<p class="more"><a href="#" class="bul">Lorem ipsum</a></p>
			</div>
			<div class="cl">&nbsp;</div>
		</div>
		<!-- End Text Cols -->
		
	</div>
	<!-- End Side Full -->
	
	<!-- Footer -->
	<div id="footer">
		<p class="left">
			<a href="#">Home</a>
			<span>|</span>
			<a href="#">Support</a>
			<span>|</span>
			<a href="#">My Account</a>
			<span>|</span>
			<a href="#">The Store</a>
			<span>|</span>
			<a href="#">Contact</a>
		</p>
		<p class="right">
			&copy; 2010 Shop Around.
			Design by <a href="http://chocotemplates.com" target="_blank" title="The Sweetest CSS Templates WorldWide">Chocotemplates.com</a>
		</p>
	</div>
	<!-- End Footer -->
	
</div>	
<!-- End Shell -->


<? if(is_admin()): ?>
<!--Админ панель-->
<section id="admBar">
    <a href="#" class="tymbler"><i class="material-icons">&#xE23E;</i></a>
    <ul class="listBtns">
        <li>
            <a href="adm/page_settings.php?method_name=edit&ID=<? echo $resInfo["ID"] ?>">Редактировать старницу</a>
        </li>
        <li>
            <a href="adm/categories.php">Категории</a>
        </li>
        <li>
            <a href="adm/forSlider.php?stranica=<? echo $stranica ?>">Большой слайдер</a>
        </li>
        <li>
            <a href="adm/products.php">Товары</a>
        </li>
    </ul>


</section>
<? else: ?>
    <a href="login.php" class="login" title="Авторизоваться"></a>
<? endif; ?>

<!-- JS -->
<script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>
<script src="js/slider/jquery.jcarousellite.min.js" type="text/javascript"></script>
<script src="js/slider/forSlider.js" type="text/javascript"></script>

<script src="js/face/admBar.min.js" type="text/javascript"></script>
<!-- End JS -->

</body>
</html>