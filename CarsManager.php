<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>Gerenciador de Carros</h1>
<?php

  /* Conecta ao MySQL e seleciona o banco de dados. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Falha ao conectar ao MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Garante que a tabela CARS exista. */
  VerifyCarsTable($connection, DB_DATABASE);

  /* Se os campos do formulário estiverem preenchidos, adiciona uma linha à tabela CARS. */
  $car_brand = htmlentities($_POST['BRAND']);
  $car_model = htmlentities($_POST['MODEL']);
  $car_year = htmlentities($_POST['YEAR']);
  $car_price = htmlentities($_POST['PRICE']);

  if (strlen($car_brand) && strlen($car_model) && strlen($car_year) && strlen($car_price)) {
    AddCar($connection, $car_brand, $car_model, $car_year, $car_price);
  }
?>

<!-- Formulário de entrada -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>MARCA</td>
      <td>MODELO</td>
      <td>ANO</td>
      <td>PREÇO (ex: 75990.50)</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="BRAND" maxlength="50" size="25" required />
      </td>
      <td>
        <input type="text" name="MODEL" maxlength="50" size="25" required />
      </td>
      <td>
        <input type="number" name="YEAR" min="1900" max="2099" size="10" required />
      </td>
      <td>
        <input type="number" step="0.01" name="PRICE" size="20" required />
      </td>
      <td>
        <input type="submit" value="Adicionar Carro" />
      </td>
    </tr>
  </table>
</form>

<!-- Exibição dos dados da tabela. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>MARCA</td>
    <td>MODELO</td>
    <td>ANO</td>
    <td>PREÇO</td>
    <td>DATA DE CRIAÇÃO</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM CARS");

while($query_data = mysqli_fetch_row($result)) {
  echo "<tr>";
  echo "<td>",$query_data[0], "</td>",
       "<td>",$query_data[1], "</td>",
       "<td>",$query_data[2], "</td>",
       "<td>",$query_data[3], "</td>",
       "<td>R$ ", number_format($query_data[4], 2, ',', '.'), "</td>",
       "<td>",$query_data[5], "</td>";
  echo "</tr>";
}
?>

</table>

<!-- Limpeza. -->
<?php

  mysqli_free_result($result);
  mysqli_close($connection);

?>

</body>
</html>


<?php

/* Adiciona um carro à tabela. */
function AddCar($connection, $brand, $model, $year, $price) {
   $b = mysqli_real_escape_string($connection, $brand);
   $m = mysqli_real_escape_string($connection, $model);
   $y = mysqli_real_escape_string($connection, $year);
   $p = mysqli_real_escape_string($connection, $price);

   $query = "INSERT INTO CARS (BRAND, MODEL, YEAR, PRICE) VALUES ('$b', '$m', '$y', '$p');";

   if(!mysqli_query($connection, $query)) echo("<p>Erro ao adicionar dados do carro.</p>");
}

/* Verifica se a tabela existe e, se não, a cria. */
function VerifyCarsTable($connection, $dbName) {
  if(!TableExists("CARS", $connection, $dbName))
  {
     $query = "CREATE TABLE CARS (
         ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         BRAND VARCHAR(50) NOT NULL,
         MODEL VARCHAR(50) NOT NULL,
         YEAR INT(4) NOT NULL,
         PRICE DECIMAL(10, 2) NOT NULL,
         DATE_ADDED TIMESTAMP DEFAULT CURRENT_TIMESTAMP
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Erro ao criar a tabela.</p>");
  }
}

/* Verifica a existência de uma tabela. */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
