<?PHP
require_once "GoogleChartDisplay.php";
class EditItemForm{
  public static function getDeleteForm($productID) {
    $display = "<div style='float:right'>";
    $display .= "<form method='post' action='index.php' class='w3-form' enctype='multipart/form-data'>";
    $display .= "<input type='hidden' id='id' name='id' value='{$productID}'>";
    $display .= "<input type='submit' class='w3-button w3-red' name='deleteBtn' value='Delete Product'>";
    $display .= "</form>";
    $display .= "</div>";
    return $display; 

}


 // public static function getBlockOfForms($product,$categories)
    public static function getEditForm($product,$categories){
        if($product!=null){
            $formTitle = "Edit Item";
            $productID = $product->productID;
            $img = $product->img;
            $productName = $product->productName;
            $quantity = $product->quantity;
            $catID = $product->catID;
            $expirationDate = $product->expirationDate;
          }else{
            $formTitle = "Add Item";
            $productID = "";
            $img = "";
            $productName = "";
            $quantity = "";
            $catID = "";
            $expirationDate = "";
          }
      $error = (isset($_SESSION["error"]))?$_SESSION["error"]:"";
      $display = '
      <div class="w3-container w3-light-grey" style="padding:128px 16px">
          <div id="main" style="padding:12px 160px">
          '. EditItemForm::getDeleteForm($productID)      .'
              <h1>'.$formTitle.'</h1>
              <div id="error">'.$error.'</div>';
      $display .= "<form method='post' action='index.php' class='w3-form'  enctype='multipart/form-data'>";
      if($productID > 0 ) {
        $display .= "Product ID: {$productID}<br>"; //USe this in validateAndDeleteData function
      }
      $display .= "<input type='hidden' name='id' id='id' value='{$productID}'>";
      $display .= "Product Name: <input type='text' name='name' value='{$productName}'><br>";
      $display .= "Quantity: <input type='text' name='qty' value='{$quantity}'><br>";
      $display .= "Expiration Date (YYYY-MM-DD): <input type='text' name='expirationDate' value='{$expirationDate}'><br>";
      $display .= "Cat ID: ";
      $display .= "<select name='catID' id='catID'>";
      $display .= "<option value=''>Choose Category</option>";
      foreach($categories as $category){
        $display .= "<option value='".$category->categoryID."'";
        if($category->categoryID == $catID){
            $display .= " SELECTED ";
        }
        $display .= ">";
        $display .= $category->categoryType;
        $display .= "</option>";
      }
      $display .= "</select><BR><BR>";
      if($img!=""){
        $display .= "Current Image:<img  src='{$img}' alt='productImg' style='width:120px;height:160px;'><br>";
      }
      $display .= "Upload New Image: <input type='file' name='imgfile'><br>";
      $display .= "<BR><BR><input type='submit' class='w3-button w3-red' id='saveBtn' name='saveBtn' value='Save Product Info'>";
      $display .= "</form>";
      $display .= "</div>";
      $display .= "</div>";
      return $display;    
  }
  public static function validateAndAddData($formdata,$imgdata,$url){
    global $api_key;
    $message = "";
    $image = "";
    $name = $formdata["name"];
    $quantity = intval($formdata["qty"]);
    $expirationDate = $formdata["expirationDate"]; 
    
    $catID = intval($formdata["catID"]);
   
    if($catID==0){
        $message = "No Category ID";
    }
    
  print_r($expirationDate);

    if($message==""){

        $imagepath =  EditItemForm::moveImageFile($imgdata,"imgfile");
        $url = $url."/data_src/api/products/create.php";
        $data = array("APIKEY" => $api_key,"productName" => $name,"quantity"=>$quantity,"img"=>$imagepath,"catID"=>$catID, "expirationDate"=>$expirationDate);
        // print_r($data);
        // exit;
        $response = DatabaseAPIConnection::post($url,$data);
        

        $message = $response;
    }else{
        $responseData = ["message"=>$message];
        $message = json_encode($http_response_header);
    }
    return $message;
  }

  public static function validateAndDeleteData($formdata, $url){
    global $api_key;
    $message = ""; //initialize message to an empty string

    if($message==""){
        $url = $url."/data_src/api/products/delete.php";
        $productID = $formdata["id"];
        $data = array("APIKEY" => $api_key, "id" => $productID);
        // print_r($data);
        // exit;

        $response = DatabaseAPIConnection::post($url,$data);

        $message = $response;

    }else{
          $responseData = ["message"=>$message];
          $message = json_encode($http_response_header);
      }
  return $message; // Return the error message
}

  public static function validateAndEditData($formdata,$imgdata,$url){
    global $api_key;
    $message = "";
    $image = "";
    $name = $formdata["name"];
    $quantity = intval($formdata["qty"]);
    $expirationDate = $formdata["expirationDate"];
    
    $catID = intval($formdata["catID"]);
    $id = intval($formdata["id"]);
    if($id==0){
        $message = "No Item ID";
    }
    if($catID==0){
        $message = "No Category ID";
    }
    

    if($message==""){

        $imagepath =  EditItemForm::moveImageFile($imgdata,"imgfile");
        $url = $url."/data_src/api/products/update.php";
        $data = array("APIKEY" => $api_key,"productName" => $name,"quantity"=>$quantity,"img"=>$imagepath,"catID"=>$catID,"productID"=>$id, "expirationDate"=>$expirationDate);
        // print_r($data);
        // exit;
        $response = DatabaseAPIConnection::put($url,$data);
        
        $message = $response;
    }else{
        $responseData = ["message"=>$message];
        $message = json_encode($http_response_header);
    }
    return $message;
  }
  private static function moveImageFile($imgdata,$fieldName){
    $imagePath = "";
    if($imgdata[$fieldName]["tmp_name"]!="" && file_exists($imgdata[$fieldName]["tmp_name"])){
        $path = $_FILES[$fieldName]['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $imageName = date("Y_m_d_h_i_").rand(1,100).".".$ext;
        $imagePath = "images/products/".$imageName;
        if(is_file($imagePath)){
            $imageName = date("Y_m_d_h_i_").rand(1,100).".".$ext;
            $imagePath = "images/products/".$imageName;
        }
        move_uploaded_file($imgdata[$fieldName]["tmp_name"],$imagePath);
    }

    return $imagePath;
  }
}
?>