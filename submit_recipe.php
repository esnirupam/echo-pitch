<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_name = $_POST['User_Name'];

    // Connect to your database
    $host = 'localhost'; // Change this if your MySQL server is hosted elsewhere
    $username = 'root'; // Change this to your MySQL username
    $password = ''; // Change this to your MySQL password
    $database = 'recipe_db'; // Change this to your MySQL database name

    // Create a connection
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to check if the username exists
    $sql = "SELECT * FROM users WHERE user_name = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there is a matching record
        if ($result->num_rows == 0) {
            // Username doesn't exist, redirect to create account page
            echo "<script>alert('User Name does not exist. Please create your account to contribute.'); 
            window.location.href = 'SignUp.php';</script>";
            exit; // Stop further execution
        } else {
            // Retrieve other form data
            $title = $_POST['Title'];
            $ingredients = $_POST['Ingredients'];
            $instructions = $_POST['Instructions'];
            $category = $_POST['Category'];
            $diet_info = $_POST['Diet_Info'];
            $image = $_POST['Img_Url']; // Column name updated to 'img_url'
            $rating = $_POST['Rating'];

            // Validate input (add more validation as needed)
            if (empty($title) || empty($ingredients) || empty($instructions) || empty($category) || empty($user_name)) {
                die("Please fill out all required fields.");
            }

            // Prepare SQL statements to insert data into the tables
            $sql1 = "INSERT INTO recipe (title, category, user_name, img_url) VALUES (?, ?, ?, ?)";
            if ($stmt1 = $conn->prepare($sql1)) {
                $stmt1->bind_param("ssss", $title, $category, $user_name, $image);

                $sql2 = "INSERT INTO ingredient (title, ingredients) VALUES (?, ?)";
                if ($stmt2 = $conn->prepare($sql2)) {
                    $stmt2->bind_param("ss", $title, $ingredients);

                    $sql3 = "INSERT INTO instruction (title, instructions) VALUES (?, ?)";
                    if ($stmt3 = $conn->prepare($sql3)) {
                        $stmt3->bind_param("ss", $title, $instructions);

                        $sql4 = "INSERT INTO ratings (title, rating) VALUES (?, ?)";
                        if ($stmt4 = $conn->prepare($sql4)) {
                            $stmt4->bind_param("ss", $title, $rating);

                            $sql5 = "INSERT INTO dietary_info (title, diet_info) VALUES (?, ?)";
                            if ($stmt5 = $conn->prepare($sql5)) {
                                $stmt5->bind_param("ss", $title, $diet_info);

                                // Execute the prepared statements
                                if ($stmt1->execute() && $stmt2->execute() && $stmt3->execute() && $stmt4->execute() && $stmt5->execute()) {
                                    // Success message
                                    echo '<script>alert("Your business idea is pitched successfully."); window.location.href = "contribute.php";</script>';
                                } else {
                                    // Error message
                                    echo '<script>alert("Error executing statement: ' . $conn->error . '");</script>';
                                }

                                // Close the statement
                                $stmt5->close();
                            } else {
                                echo 'Error preparing statement 5: ' . $conn->error;
                            }

                            $stmt4->close();
                        } else {
                            echo 'Error preparing statement 4: ' . $conn->error;
                        }

                        $stmt3->close();
                    } else {
                        echo 'Error preparing statement 3: ' . $conn->error;
                    }

                    $stmt2->close();
                } else {
                    echo 'Error preparing statement 2: ' . $conn->error;
                }

                $stmt1->close();
            } else {
                echo 'Error preparing statement 1: ' . $conn->error;
            }
        }

        // Close the database connection
        $stmt->close();
    } else {
        echo 'Error preparing SELECT statement: ' . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
