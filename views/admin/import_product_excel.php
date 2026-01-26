<?php
session_start();
include(__DIR__ . "/../../config/database.php");
include(__DIR__ . "/../../model/Product.php");
include(__DIR__ . "/../../model/Category.php");
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$productModel = new Product($conn);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Upload và giải nén ZIP
    $zipFile = $_FILES['images_zip']['tmp_name'];
    $zip = new ZipArchive();
    $uploadDir = __DIR__ . '/../../images/';
    
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($uploadDir);
        $zip->close();
    } else {
        $_SESSION['error'] = "Không giải nén được file ZIP ảnh!";
        header("Location: add_products.php");
        exit();
    }

    // Load Excel
    $excelFile = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($excelFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    // Bỏ header
    unset($rows[0]);

    foreach($rows as $row){
        list($id_product, $name, $price, $description, $stock, $id_category, $id_featured, $image_filename) = $row;

        $image = $image_filename ? 'images/' . $image_filename : '';

        // Tạo sản phẩm
        $productModel->create($id_product, $name, $price, $description, $image, $stock, $id_category, $id_featured);
    }

    $_SESSION['success'] = "Import thành công!";
    header("Location: /QuanLyBanHangFigure/views/admin/products.php");
    exit();
}
?>
