<?php
require 'db.php';

$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>User List:</h2>";
    echo "<table border='1' cellpadding='10'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["id"] . "</td>
                <td>" . htmlspecialchars($row["username"]) . "</td>
                <td>" . htmlspecialchars($row["email"]) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No users found.";
}

$conn->close();
?>
