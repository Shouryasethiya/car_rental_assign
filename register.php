<?php include 'db.php'; 

$type = isset($_GET['type']) ? $_GET['type'] : 'customer'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        header("Location: login.php?msg=registered");
        exit();
    } else {
        $error = "Error: Email likely already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <header>
        <div class="logo">CarRental System</div>
        <nav><a href="index.php">Home</a> <a href="login.php">Login</a></nav>
    </header>

    <div class="container">
        <h2>Register as <?php echo ucfirst($type); ?></h2>
        
        <p>Not a <?php echo $type; ?>? 
            <a href="register.php?type=<?php echo ($type == 'customer') ? 'agency' : 'customer'; ?>">
                Register as <?php echo ($type == 'customer') ? 'Agency' : 'Customer'; ?>
            </a>
        </p>

        <?php if(isset($error)) echo "<div class='alert error'>$error</div>"; ?>

        <form method="POST">
            <input type="hidden" name="role" value="<?php echo $type; ?>">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
    </div>
</body>
</html>