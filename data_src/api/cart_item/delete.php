<?php
require_once "../../includes/database_config.php";
require_once "../../classes/FoodDatabase.php";

       
   
$key = isset($_POST["APIKEY"])?$_POST["APIKEY"]:""; 
$id = isset($_POST["productID"])?intval($_POST["productID"]):"";
$basket_id = isset($_POST["basketID"])?intval($_POST["basketID"]):"";
$basket_item_id = isset($_POST["basketItemID"])?intval($_POST["basketItemID"]):"";

if($key!=$GLOBAL_API_KEY){
  echo json_encode(["message"=>"Invalid API KEY"]);
  exit;
}
if($basket_id==""||$basket_id==0){
  echo json_encode(["message"=>"No Basket ID"]);
  exit;
}
//Remove a cart item
if($basket_id!="" && $basket_id!=0 && $id != "" && $id != 0 && $basket_item_id != "" && $basket_item_id != 0){
  $params = [":id"=>$id, ":basket_id"=>$basket_id, ":basket_item_id"=>$basket_item_id];
  //Count the number of rows returned to make sure the cart item exists in the database
  $count_sql = "SELECT COUNT(productID) as count FROM basketItem WHERE productID = :id AND BasketID = :basket_id AND basketItemID = :basket_item_id";
  $sql = "DELETE FROM basketItem WHERE productID = :id AND BasketID = :basket_id AND basketItemID = :basket_item_id";
  $count_result = FoodDatabase::getDataFromSQL($count_sql, $params);
   //See if their are results by checking if count > 0
  $count = intval($count_result[0]["count"]);
  if ($count > 0) {
  try {
    // Execute the SQL query and handle error
    FoodDatabase::executeSQL($sql, $params);
    http_response_code(200); // OK
    $message = ["message" => "Cart Item Removed Successfully!"];
    echo json_encode($message);
  } catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database Error: " . $e->getMessage()]);
  }
} else {
  $message = ["message" => "Cart Item Does Not Exist!"]; //Display if cart item doesn't exist
  echo json_encode($message);
}
  exit;
}
//empty out the cart
if($id==""||$id==0){  
  $params = [":basket_id"=>$basket_id];
  //Count the number of rows returned to make sure the cart exists in the database
  $count_sql = "SELECT COUNT(basketID) as count FROM basketItem WHERE BasketID = :basket_id";
  $sql = "DELETE FROM basketItem WHERE BasketID = :basket_id";
  $count_result = FoodDatabase::getDataFromSQL($count_sql, $params);
  $count = intval($count_result[0]["count"]);
  //See if their are results by checking if count > 0
  if ($count > 0) {
  try {
    // Execute the SQL query and handle error
    FoodDatabase::executeSQL($sql, $params);
    http_response_code(200); // OK
    $message = ["message" => "Cart Successfully Deleted!"];
    echo json_encode($message);
  } catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(["message" => "Database Error: " . $e->getMessage()]);
  }
} else {
  $message = ["message" => "Cart Does Not Exist!"]; //Display if cart doesn't exist
  echo json_encode($message);
}
  exit;
}
 else {
  echo json_encode(["message"=>"Invalid API Call!"]);
}
?>