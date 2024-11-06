<?php
// Connect to your database
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

// Prepare SQL statement to select data from your recipe table
$sql = "SELECT * FROM recipe";
$result = $conn->query($sql);

// Get unique categories for the filter buttons
$category_sql = "SELECT DISTINCT category FROM recipe";
$category_result = $conn->query($category_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas | EchoPitch</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS styles */
        body {
            background-image: url("images/bg6.webp");
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            color: white;
            font-family: Georgia, 'Times New Roman', Times, serif;
        }
        .card {
            background-color: #343a40;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: white;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .rating {
            unicode-bidi: bidi-override;
            color: #57564a;
            font-size: 18px;
            height: 18px;
            margin-bottom: 10px;
        }
        .checked {
            color: orange;
        }
        /* Category Filter Buttons */
.btn-group .btn {
    background-color: #007bff; /* Bright blue background for better visibility */
    color: white; /* White text for contrast */
    border-color: #0056b3; /* Darker border for definition */
}

.btn-group .btn:hover {
    background-color: #0056b3; /* Darker shade on hover */
    color: #fff;
}

#clear-filter {
    background-color: #28a745; /* Green background for the clear filter button */
    color: white;
    border-color: #1e7e34; /* Darker green border */
}

#clear-filter:hover {
    background-color: #1e7e34; /* Darker shade on hover */
    color: #fff;
}

    </style>
</head>
<body>
    <?php
        $activePage = 'recipes';
        include 'menu.php';
    ?>
    <br>
    <div class="container">
        <!-- Category Filter Buttons -->
        <h4>Filter by Category</h4>
        <div class="btn-group mb-4" role="group" aria-label="Category filter">
            <?php
            if ($category_result->num_rows > 0) {
                while ($category_row = $category_result->fetch_assoc()) {
                    echo '<button class="btn btn-outline-light category-filter" data-category="' . $category_row['category'] . '">' . $category_row['category'] . '</button>';
                }
            }
            ?>
            <button class="btn btn-outline-light" id="clear-filter">Clear Filter</button>
        </div>

        <!-- Recipe Cards -->
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img class="card-img-top" src="<?php echo $row['img_url']; ?>" alt="Card image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['Title']; ?></h5>
                                <h6 class="card-category"><?php echo $row['category']; ?></h6>
                                <div class="rating">
                                    <?php
                                    $title = $row['Title'];
                                    $rating_sql = "SELECT rating FROM ratings WHERE Title = '$title'";
                                    $rating_result = $conn->query($rating_sql);
                                    if ($rating_result->num_rows > 0) {
                                        $rating_row = $rating_result->fetch_assoc();
                                        $rating = $rating_row['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $rating ? '<span class="fa fa-star checked"></span>' : '<span class="fa fa-star"></span>';
                                        }
                                    } else {
                                        echo "No ratings yet";
                                    }
                                    ?>
                                </div>
                                <a href="fullrecipe.php?title=<?php echo urlencode($row['Title']); ?>" class="btn btn-success">View IDEA</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No records found</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <h4 class="p-3 bg-dark text-white text-center">EchoPitch Contact: +91 9344381081</h4>
    </footer>

    <!-- AJAX and jQuery for category filter -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.category-filter').click(function() {
                const category = $(this).data('category');
                $.ajax({
                    url: 'fetch_recipes.php',
                    method: 'GET',
                    data: { category: category },
                    success: function(data) {
                        $('.row').html(data); // Update the recipes display with filtered data
                    }
                });
            });

            $('#clear-filter').click(function() {
                $.ajax({
                    url: 'fetch_recipes.php',
                    method: 'GET',
                    success: function(data) {
                        $('.row').html(data); // Reset to show all data
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
// Close the database connection
$conn->close();
?>
