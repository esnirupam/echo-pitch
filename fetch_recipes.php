<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'recipe_db';

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = isset($_GET['category']) ? $_GET['category'] : '';

// Prepare SQL query based on the category
if ($category) {
    $sql = "SELECT * FROM recipe WHERE category = '$category'";
} else {
    $sql = "SELECT * FROM recipe";
}

$result = $conn->query($sql);

// Display the recipes
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-4">';
        echo '<div class="card">';
        echo '<img class="card-img-top" src="' . $row['img_url'] . '" alt="Card image">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $row['Title'] . '</h5>';
        echo '<h6 class="card-category">' . $row['category'] . '</h6>';
        echo '<a href="fullrecipe.php?title=' . urlencode($row['Title']) . '" class="btn btn-success">View IDEA</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No records found for this category.</p>";
}

$conn->close();
?>
