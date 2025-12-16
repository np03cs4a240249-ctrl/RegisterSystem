<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = '';
$email = '';

$errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email required.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check for errors
    $hasError = false;
    foreach ($errors as $error) {
        if (!empty($error)) {
            $hasError = true;
        }
    }

    // Save to users.json
    if (!$hasError) {

        $file = 'users.json';

        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }

        $users = json_decode(file_get_contents($file), true);
        if (!is_array($users)) {
            $users = [];
        }

        $users[] = [
            'name' => htmlspecialchars($name),
            'email' => htmlspecialchars($email),
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

        echo "<p style='color:green;'>Registration successful!</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>

<h2>User Registration</h2>

<form method="post">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    <span style="color:red;"><?php echo $errors['name']; ?></span><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <span style="color:red;"><?php echo $errors['email']; ?></span><br><br>

    <label>Password:</label><br>
    <input type="password" name="password">
    <span style="color:red;"><?php echo $errors['password']; ?></span><br><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password">
    <span style="color:red;"><?php echo $errors['confirm_password']; ?></span><br><br>

    <button type="submit">Register</button>

</form>

</body>
</html>
