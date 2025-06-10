// test-connection.php
<?php
include_once 'database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "✅ Conexión exitosa a la base de datos";
} else {
    echo "❌ Error al conectar a la base de datos";
}
?>