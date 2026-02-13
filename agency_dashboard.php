<?php include 'db.php';

if (!isLoggedIn() || getRole() != 'agency') {
    header("Location: login_agency.php");
    exit();
}

$agency_id = $_SESSION['user_id'];


if (isset($_POST['add_car'])) {
    $model = $_POST['model'];
    $number = $_POST['number'];
    $capacity = $_POST['capacity'];
    $rent = $_POST['rent'];

    $stmt = $conn->prepare("INSERT INTO cars (agency_id, model, vehicle_number, capacity, rent_per_day) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issid", $agency_id, $model, $number, $capacity, $rent);
    $stmt->execute();
    $msg = "Car added successfully!";
}


if (isset($_POST['update_car'])) {
    $car_id = $_POST['car_id'];
    $rent = $_POST['rent'];
    $stmt = $conn->prepare("UPDATE cars SET rent_per_day = ? WHERE id = ? AND agency_id = ?");
    $stmt->bind_param("dii", $rent, $car_id, $agency_id);
    $stmt->execute();
    $msg = "Car updated!";
}


$cars = $conn->query("SELECT * FROM cars WHERE agency_id = $agency_id");

$booking_query = "
    SELECT b.*, u.name as customer_name, c.model, c.vehicle_number 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id 
    JOIN users u ON b.customer_id = u.id 
    WHERE c.agency_id = $agency_id ORDER BY b.created_at DESC";
$bookings = $conn->query($booking_query);


$total_cars = $cars->num_rows;
$total_earnings = 0;
$total_bookings = $bookings->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agency Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        .dashboard-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-card h2 { margin: 0; font-size: 2rem; color: #2c2c2c; }
        .stat-card p { margin: 5px 0 0; color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .content-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
        }
        .content-header h3 { margin: 0; color: #333; }


        .add-car-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; background: #f8f9fa; color: #555; font-weight: 600; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; color: #444; }
        tr:last-child td { border-bottom: none; }
        .btn-sm { padding: 8px 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header>
        <div class="logo">Agency<span>Portal</span></div>
        <nav>
            <span style="color:white; margin-right:20px; font-size:0.9rem;">👋 Welcome, <?php echo $_SESSION['name']; ?></span>
            <a href="index.php" style="font-size:0.9rem;">View Site</a>
            <a href="logout.php" class="btn" style="background: #dc3545; color: white;">Logout</a>
        </nav>
    </header>

    <div class="container">
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h2><?php echo $total_cars; ?></h2>
                <p>Total Cars</p>
            </div>
            <div class="stat-card">
                <h2><?php echo $total_bookings; ?></h2>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card" style="border-bottom: 4px solid #fec200;">
                <h2 id="total-earnings">₹...</h2>
                <p>Total Revenue</p>
            </div>
        </div>

        <?php if(isset($msg)) echo "<div class='alert' style='background:#d4edda; color:#155724; padding:15px; margin-bottom:20px; border-radius:5px;'>$msg</div>"; ?>

        <div class="content-section">
            <div class="content-header">
                <h3>➕ Add New Vehicle</h3>
            </div>
            <form method="POST" class="add-car-grid">
                <div class="form-group">
                    <label>Car Model</label>
                    <input type="text" name="model" placeholder="e.g. Swift Dzire" required>
                </div>
                <div class="form-group">
                    <label>Vehicle Number</label>
                    <input type="text" name="number" placeholder="MP-04-AB-1234" required>
                </div>
                <div class="form-group">
                    <label>Seating</label>
                    <input type="number" name="capacity" placeholder="4" required>
                </div>
                <div class="form-group">
                    <label>Rent Per Day (₹)</label>
                    <input type="number" name="rent" placeholder="2000" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_car" class="btn">Add Vehicle</button>
                </div>
            </form>
        </div>

        <div class="content-section">
            <div class="content-header">
                <h3>🚗 Your Fleet (Edit Prices)</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Number</th>
                        <th>Seating</th>
                        <th>Rent / Day</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $cars->data_seek(0); 
                    while($car = $cars->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><strong><?php echo $car['model']; ?></strong></td>
                        <td><span style="background:#eee; padding:2px 6px; border-radius:4px; font-size:0.9em;"><?php echo $car['vehicle_number']; ?></span></td>
                        <td><?php echo $car['capacity']; ?> Seats</td>
                        <form method="POST">
                            <td>
                                <div style="display:flex; align-items:center;">
                                    <span style="margin-right:5px;">₹</span>
                                    <input type="number" name="rent" value="<?php echo $car['rent_per_day']; ?>" style="width: 80px; padding: 5px; border:1px solid #ddd; border-radius:4px;">
                                </div>
                            </td>
                            <td>
                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                <button type="submit" name="update_car" class="btn btn-sm">Update</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="content-section">
            <div class="content-header">
                <h3>📅 Recent Bookings</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Customer</th>
                        <th>Trip Dates</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $real_earnings = 0;
                    if ($bookings->num_rows > 0):
                        while($b = $bookings->fetch_assoc()): 
                            $real_earnings += $b['total_cost'];
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo $b['model']; ?></strong><br>
                            <small style="color:#888;"><?php echo $b['vehicle_number']; ?></small>
                        </td>
                        <td>
                            <?php echo $b['customer_name']; ?>
                        </td>
                        <td>
                            <span style="color:#2c2c2c; font-weight:500;"><?php echo $b['start_date']; ?></span>
                            <br><small><?php echo $b['days']; ?> Days</small>
                        </td>
                        <td>
                            <span style="color:#155724; background:#d4edda; padding:4px 8px; border-radius:4px; font-weight:bold;">
                                + ₹<?php echo $b['total_cost']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; 
                    else: ?>
                    <tr><td colspan="4" style="text-align:center; padding:20px;">No bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.getElementById('total-earnings').innerText = '₹<?php echo $real_earnings; ?>';
    </script>
</body>
</html>