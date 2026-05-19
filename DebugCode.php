<?php

$conn = mysqli_connect("localhost", "root", "", "test");

// 1. Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// 2. Validate input
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid ID provided");
}

// 3. Prepare statement (SECURE)
$stmt = mysqli_prepare($conn, "SELECT name FROM users WHERE id = ?");

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

// 4. Bind parameter
mysqli_stmt_bind_param($stmt, "i", $id);

// 5. Execute
mysqli_stmt_execute($stmt);

// 6. Get result
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo htmlspecialchars($row['name']) . "<br>";
    }
} else {
    echo "Query execution failed: " . mysqli_error($conn);
}

// 7. Cleanup
mysqli_stmt_close($stmt);
mysqli_close($conn);

?>