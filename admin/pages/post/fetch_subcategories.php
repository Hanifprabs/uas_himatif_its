<?php
require_once '../../includes/config.php'; // File untuk menghubungkan ke database

if (isset($_POST['categoryId'])) {
    $categoryId = $_POST['categoryId'];
    $sql_subcategories = "SELECT * FROM tblsubcategory WHERE CategoryId = ?";
    $stmt_subcategories = $con->prepare($sql_subcategories);
    $stmt_subcategories->bind_param('i', $categoryId);
    $stmt_subcategories->execute();
    $result_subcategories = $stmt_subcategories->get_result();

    echo '<option value="">Select Sub-Category</option>';
    while ($row_sub = $result_subcategories->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row_sub['SubCategoryId']) . '">' . htmlspecialchars($row_sub['Subcategory']) . '</option>';
    }
}
?>
