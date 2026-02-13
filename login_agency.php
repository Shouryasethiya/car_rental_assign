<?php include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND role = 'agency'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            header("Location: agency_dashboard.php");
            exit();
        }
    }
    $error = "Invalid Agency credentials";
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <header>
        <div class="logo">Rent<span>Car</span></div>
        <nav><a href="index.php">Home</a></nav>
    </header>

    <div class="auth-box" style="border-top: 5px solid #2c2c2c;">
        <h2 style="text-align:center;">Agency Portal</h2>
        <p style="text-align:center; color:#666;">Login to manage your fleet.</p>
        
        <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Agency Email</label>
                <input type="email" name="email" required placeholder="agency@company.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block" style="background:#2c2c2c; color:white;">Login to Dashboard</button>
        </form>
        <p style="text-align:center; margin-top:20px;">
            Want to partner? <a href="register.php?type=agency" style="color:#fec200;">Register Agency</a>
        </p>
    </div>
</body>
</html>