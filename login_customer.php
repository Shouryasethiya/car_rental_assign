<?php include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ? AND role = 'customer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            header("Location: index.php");
            exit();
        }
    }
    $error = "Invalid Customer credentials";
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

    <div class="auth-box">
        <h2 style="text-align:center;">Customer Login</h2>
        <p style="text-align:center; color:#666;">Welcome back! Please login to book a car.</p>
        
        <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="customer@example.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block">Login as Customer</button>
        </form>
        <p style="text-align:center; margin-top:20px;">
            No account? <a href="register.php?type=customer" style="color:#fec200;">Register here</a>
        </p>
    </div>
</body>
</html>