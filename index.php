<?php include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_car'])) {
    if (!isLoggedIn() || getRole() != 'customer') {
        header("Location: login_customer.php");
        exit();
    }

    $car_id = $_POST['car_id'];
    $days = $_POST['days'];
    $start_date = $_POST['start_date'];
    $rent = $_POST['rent_val'];
    $total_cost = $rent * $days;
    $customer_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO bookings (car_id, customer_id, start_date, days, total_cost) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issid", $car_id, $customer_id, $start_date, $days, $total_cost);
    
    if ($stmt->execute()) {
    
        $msg = "Success! Car booked for ₹$total_cost";
    }
}

$result = $conn->query("SELECT * FROM cars");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent A Car</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <header>
        <div class="logo">Rent<span>Car</span></div>
        <nav>
            <a href="index.php">Home</a>
            <?php if(isLoggedIn()): ?>
                <a href="#"><?php echo $_SESSION['name']; ?></a>
                <?php if(getRole() == 'agency'): ?>
                    <a href="agency_dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login_customer.php">Customer Login</a>
                <a href="login_agency.php">Agency Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="hero">
        <h1>Find Your Best Car</h1>
        <p>Luxury, Sports, and Economy cars available for rent</p>
    </div>

    <div class="container">
        
        <div class="section-title">
            <h2>Featured Cars</h2>
            <div class="line"></div>
        </div>

        <?php if(isset($msg)) echo "<div style='padding:15px; background:#d4edda; color:#155724; margin-bottom:20px;'>$msg</div>"; ?>

        <div class="car-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="car-card">
                    <div class="car-img-placeholder">
                        🚗 <?php echo $row['model']; ?>
                    </div>
                    <div class="car-info">
                        <h3><?php echo $row['model']; ?> <span class="car-price">₹<?php echo $row['rent_per_day']; ?></span></h3>
                        <div class="car-details">
                            <p>🔢 No: <?php echo $row['vehicle_number']; ?></p>
                            <p>💺 Seats: <?php echo $row['capacity']; ?></p>
                        </div>

                        <hr style="border:0; border-top:1px solid #eee; margin: 15px 0;">

                        <?php if (isLoggedIn() && getRole() == 'customer'): ?>
                            <form method="POST">
                                <input type="hidden" name="car_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="rent_val" value="<?php echo $row['rent_per_day']; ?>">
                                
                                <div style="display:flex; gap:10px; margin-bottom:10px;">
                                    <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                                    <select name="days">
                                        <option value="1">1 Day</option>
                                        <option value="3">3 Days</option>
                                        <option value="7">7 Days</option>
                                    </select>
                                </div>
                                <button type="submit" name="book_car" class="btn btn-block">Book Now</button>
                            </form>
                        <?php elseif (isLoggedIn() && getRole() == 'agency'): ?>
                            <button disabled class="btn btn-block" style="background:#ddd;">Agency View</button>
                        <?php else: ?>
                            <a href="login_customer.php" class="btn btn-block">Login to Book</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>