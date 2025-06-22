<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Error</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f8ff, #e6f7ff); /* Light blue gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
            color: #343a40;
        }
        .error-container {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .error-container h2 {
            color: #dc3545; /* Red color for error */
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .error-container p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .back-to-login-btn {
            display: inline-block;
            background-color: #007bff; /* Primary blue button */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.2s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        .back-to-login-btn:hover {
            background-color: #0056b3; /* Darker blue */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        /* Responsive */
        @media (max-width: 500px) {
            .error-container {
                padding: 30px 20px;
                margin: 20px;
            }
            .error-container h2 {
                font-size: 1.8rem;
            }
            .error-container p {
                font-size: 1rem;
            }
            .back-to-login-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h2>Login Gagal!</h2>
        <p>Username atau password tidak valid.</p>
        <a href="login.php" class="back-to-login-btn">Kembali ke Halaman Login</a>
    </div>

    <?php
    // Tampilkan pop-up JavaScript hanya saat halaman diakses dengan parameter 'error'
    if(isset($_GET['error']) && $_GET['error'] == 1){
        echo "<script>alert('Username atau password tidak valid.');</script>";
    }
    ?>
</body>
</html>