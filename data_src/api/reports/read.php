<?PHP

require_once "../../includes/database_config.php";
require_once "../../classes/FoodDatabase.php";

$table = isset($_GET["table"])?$_GET["table"]:"";
$id = isset($_GET["id"])?$_GET["id"]:"";
$catID = isset($_GET["catID"])?$_GET["catID"]:"";
$key = isset($_GET["APIKEY"])?$_GET["APIKEY"]:"";
if($key!=$GLOBAL_API_KEY){
  echo json_encode(["message"=>"Invalid API KEY"]);
  exit;
}
$graphType = isset($_GET["graphType"])?$_GET["graphType"]:"";

if($graphType=="ByProduct"){
  $sql = "select P.productName, SUM(TD.quantity) total
  from product P,
  transactionsDetails TD
  where P.productID = TD.productID 
  GROUP BY P.productName;";
  $params = null;
}else if($graphType=="ByCategory"){
  $catID = isset($catID)&&$catID!=""?$catID:2;
  $sql = "select P.productName, COUNT(TD.transactionID) total
  from product P,
  transactionsDetails TD
  where P.productID = TD.productID and P.catID = :catid
  GROUP BY P.productName";
  $params = [":catid"=>$catID];
  // echo $sql;
  // print_r($params);
}else if($graphType=="ByDependentInfo"){
  $sql = "select userID, SUM(children), SUM(adult), SUM(senior)
  from registration GROUP BY userID";
  $params = null;
}

else if($graphType=="ByUserInfo"){
  $sql = "SELECT CONCAT('Visits ',y.total,IF(y.total=1,' time',' times')) as total, COUNT(*) as user
FROM (
SELECT COUNT(x.user) total,x.user 
  FROM (SELECT count(userID) user FROM transactions group by userID)x
  GROUP BY x.user) y
  GROUP BY y.total;";
  $params = null;
}
else if($graphType=="TempData"){
  $sql = "SELECT Date_format(timestamp,'%Y-%m-%d') as caldate, MIN(temp) mintemp,MAX(temp) maxtemp 
FROM temperature 
GROUP BY caldate
order by 1 ASC;";
  $params = null;
}
else{
 
  if(isset($_GET["date1"])){
    $params = [":date1"=>$_GET["date1"],":date2"=>$_GET["date2"]];
    $where = "where date BETWEEN :date1 and :date2";
  }else{
    $params = null;
    $where = '';
  }
  

  // Pulls and Formats transaction Date
  $sql = "select DATE_FORMAT( date, '%Y-%m-%d' ) as transactionDate, COUNT(date) total
      from transactions
      ".$where."
      GROUP BY date;";

      
  
  
  }



$data = FoodDatabase::getDataFromSQL($sql, $params);

if(count($data)>0)

	echo json_encode($data);












?>