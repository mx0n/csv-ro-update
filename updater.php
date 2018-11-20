<?php
require('config.php');
$connect = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die('Ошибка подключения');
$message = '';

if(isset($_POST["upload"]))
{
 if($_FILES['product_file']['name'])
 {
  $filename = explode(".", $_FILES['product_file']['name']);
  if(end($filename) == "csv")
  {
    $query_disable = "UPDATE `oc_relatedoptions` SET `quantity` = 0";
    mysqli_query($connect, $query_disable);
   $handle = fopen($_FILES['product_file']['tmp_name'], "r");
   $rows = 0;
   while($data = fgetcsv($handle, 1000, ";"))
   {
    $rows++;
    if ($rows == 1) {continue;}
    $sku = mysqli_real_escape_string($connect, $data[0]);
    $quantity = mysqli_real_escape_string($connect, $data[2]);
    $price = mysqli_real_escape_string($connect, $data[3]);
    $query = "UPDATE `oc_relatedoptions` SET `quantity` = $quantity, `price` = $price WHERE `sku` = '$sku'";
    mysqli_query($connect, $query);
    //echo mysql_errno() . ": " . mysql_error();
   }
   fclose($handle);
   header("location: updater.php?updation=1");
  }
  else
  {
   $message = '<label class="text-danger">Доступен импорт только файлов в формате CSV</label>';
  }
 }
 else
 {
  $message = '<label class="text-danger">Выберите файл</label>';
 }
}

if(isset($_GET["updation"]))
{
 $message = '<label class="text-success">Остатки обновлены</label>';
}

//$query = "SELECT * FROM oc_relatedoptions";
//$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html>
 <head>
  <title>Обновление остатков</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
 </head>
 <body>
  <br />
  <div class="container">
   <h2 align="center">Обновление остатков</a></h2>
   <br />
   <form method="post" enctype='multipart/form-data'>
    <p><label>Выберите файл с остатками (Только в формате CSV)</label>
    <input type="file" name="product_file" /></p>
    <br />
    <input type="submit" name="upload" class="btn btn-info" value="Upload" />
   </form>
   <br />
   <?php echo $message; ?>
   <br />
   <div class="table-responsive">
    <table class="table table-bordered table-striped">
     <tr>
      <th>sku</th>
      <th>quantity</th>
      <th>price</th>
     </tr>
     <!-- <?php
     while($row = mysqli_fetch_array($result))
     {
      echo '
      <tr>
       <td>'.$row["sku"].'</td>
       <td>'.$row["quantity"].'</td>
       <td>'.$row["price"].'</td>
      </tr>
      ';
     }
     ?> -->
    </table>
   </div>
  </div>
 </body>
</html>