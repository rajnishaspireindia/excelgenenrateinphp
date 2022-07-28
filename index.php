 <?php 
// Load the database configuration file 
include_once 'config.php'; 
 
// Filter the excel data 
function filterData(&$str){ 
    $str = preg_replace("/\t/", "\\t", $str); 
    $str = preg_replace("/\r?\n/", "\\n", $str); 
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
} 
 
// Excel file name for download 
$fileName = "order_details" . date('Y-m-d') . ".xls"; 
 
// Column names 
$fields = array('product_name','price','shipping_cost','tax_percent','order_date','variation(kg)','shipping_address','billing_address','delivery_date','delivery_type','order_id','grand_total'); 
 
// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n"; 
 
// Fetch records from database 
$query = $db->query("SELECT order_details.shipping_cost,order_details.price,order_details.tax_percent,order_details.order_date,products.name,order_details.variation,orders.shipping_address,orders.billing_address,orders.delivery_date,orders.delivery_type,orders.code,orders.grand_total
FROM order_details
LEFT JOIN products 
ON order_details.product_id=products.id
LEFT JOIN orders 
ON order_details.product_id=orders.id "); 
error_reporting();
if($query->num_rows > 0){ 
    // Output each row of the data 
    while($row = $query->fetch_assoc()){ 
        // $status = ($row['status'] == 1)?'Active':'Inactive'; 
        $lineData = array($row['name'],$row['price'],$row['shipping_cost'],$row['tax_percent'],$row['order_date'],$row['variation'],$row['shipping_address'],$row['billing_address'],$row['delivery_date'],$row['delivery_type'],$row['code'],$row['grand_total']); 
        array_walk($lineData, 'filterData'); 
        $excelData .= implode("\t", array_values($lineData)) . "\n"; 
    } 
}else{ 
    $excelData .= 'No records found...'. "\n"; 
} 
 
// Headers for download 
header("Content-Type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename=\"$fileName\""); 
 
// Render excel data 
echo $excelData; 
 
exit;