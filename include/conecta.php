<?php
// db.php
$host = 'localhost';     // ou o IP do seu servidor MySQL
$dbname = 'crm_imobiliario';  // Nome do banco de dados
$user = 'root';           // Usuário MySQL
$pass = '';               // Senha do MySQL

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar no banco de dados: " . $e->getMessage();
    exit;
}
?>
