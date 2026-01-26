<?php
include("../config/database.php");
include("../model/Product.php");

$productModel = new Product($conn);
$product = $productModel->getAll();
?>