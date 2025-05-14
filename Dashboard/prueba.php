<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include 'db/connection.php';

$sql = "SELECT id, name, lastname, email FROM clients LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['lastname']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

$conn->close();
?>
