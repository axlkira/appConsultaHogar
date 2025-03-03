<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=familiam_bdprotocoloservidor', 'familiamed', '432598med9878');
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
}
