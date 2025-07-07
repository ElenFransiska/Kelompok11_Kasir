<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Login Karyawan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Pastikan path CSS ini benar dan berisi gaya dasar yang konsisten -->
    <link rel="stylesheet" href="css/css_style.css"> 
    <style>
        /* Variabel CSS untuk konsistensi */
        :root {
            --primary-blue: #007bff;
            --primary-dark-blue: #0056b3;
            --text-dark: #343a40;
            --text-medium: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #ced4da;
            --shadow-light: 0 8px 30px rgba(0, 0, 0, 0.1);
            --bg-gradient-start: #e0f8ff; 
            --bg-gradient-end: #d0efff;  
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-dark); 
            line-height: 1.6;
        }

        .container.login-form-container {
            background-color: white;
            padding: 3rem;
            border-radius: 18px; 
            box-shadow: var(--shadow-light); 
            width: 100%;
            max-width: 420px; 
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(5px); 
            -webkit-backdrop-filter: blur(5px); 
            transition: transform 0.3s ease; 
        }

        .container.login-form-container:hover {
            transform: translateY(-5px); /* Sedikit naik saat hover */
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15); /* Shadow lebih besar saat hover */
        }

        /* Judul Login */
        .container.login-form-container h2 {
            font-size: 2.2rem; /* Ukuran lebih besar */
            color: var(--primary-dark-blue); /* Warna judul lebih menonjol */
            margin-bottom: 2.5rem; /* Margin bawah lebih besar */
            font-weight: 700; /* Lebih tebal */
        }

        /* Grup Form (Label & Input) */
        .form-group {
            margin-bottom: 1.8rem; /* Jarak antar grup lebih besar */
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 1rem; /* Ukuran label sedikit lebih besar */
            color: var(--text-medium);
            margin-bottom: 0.6rem; /* Jarak label dengan input */
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 1rem 1.2rem; /* Padding lebih besar */
            border: 1px solid var(--border-color);
            border-radius: 10px; /* Lebih membulat */
            font-size: 1.05rem; /* Ukuran font input */
            color: var(--text-dark);
            background-color: var(--light-bg); /* Background input sedikit berbeda */
            transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2); /* Shadow fokus yang lebih halus */
            background-color: white; /* Kembali ke putih saat fokus */
        }

        /* Tombol Login */
        .login-button {
            width: 100%;
            padding: 1rem 1.2rem; /* Padding lebih besar */
            font-size: 1.15rem; /* Ukuran font lebih besar */
            font-weight: 600;
            border-radius: 10px; /* Lebih membulat */
            cursor: pointer;
            background: linear-gradient(to right, var(--primary-blue), var(--primary-dark-blue)); /* Gradient */
            color: white;
            border: none;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px; /* Jarak ikon lebih besar */
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3); /* Shadow tombol */
        }

        .login-button:hover {
            background: linear-gradient(to right, var(--primary-dark-blue), #004085); /* Gradient lebih gelap */
            transform: translateY(-3px); /* Efek naik lebih pronounces */
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.4); /* Shadow lebih besar saat hover */
        }

        .login-button:active {
            transform: translateY(0); /* Kembali ke posisi semula saat diklik */
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .arrow-icon {
            font-size: 1.3em; /* Ukuran ikon panah */
            transition: transform 0.3s ease;
        }

        .login-button:hover .arrow-icon {
            transform: translateX(8px); /* Geser panah saat hover */
        }

        /* Pesan Error */
        p.error-message {
            color: #dc3545; /* Merah untuk error */
            text-align: center;
            margin-bottom: 20px; /* Jarak lebih besar */
            font-weight: 600; /* Lebih tebal */
            font-size: 1.05rem; /* Ukuran font lebih besar */
            animation: fadeIn 0.5s ease-out; /* Animasi fade-in */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Link Kembali ke Home */
        p.back-link {
            margin-top: 2.5rem; /* Jarak lebih besar dari tombol */
            font-size: 0.95rem;
        }

        p.back-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease, text-decoration 0.2s ease;
        }

        p.back-link a:hover {
            color: var(--primary-dark-blue);
            text-decoration: underline;
        }

        /* Responsive Adjustments */
        @media (max-width: 500px) {
            .container.login-form-container {
                padding: 2.5rem 1.5rem;
                margin: 20px; /* Margin dari sisi layar */
                border-radius: 12px;
            }
            .container.login-form-container h2 {
                font-size: 1.8rem;
                margin-bottom: 2rem;
            }
            .form-group {
                margin-bottom: 1.2rem;
            }
            .form-group label {
                font-size: 0.9rem;
            }
            .form-group input[type="text"],
            .form-group input[type="password"] {
                padding: 0.7rem 1rem;
                font-size: 0.95rem;
                border-radius: 8px;
            }
            .login-button {
                padding: 0.8rem 1rem;
                font-size: 1rem;
                border-radius: 8px;
                gap: 8px;
            }
            .arrow-icon {
                font-size: 1em;
            }
            p.error-message {
                font-size: 0.9rem;
                margin-bottom: 15px;
            }
            p.back-link {
                margin-top: 1.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container login-form-container">
        <h2>Login Karyawan</h2>
        <?php
        // Menampilkan pesan error "Salah username atau password"
        if(isset($_GET['error'])){
            echo "<p class='error-message'>Salah username atau password</p>";
        }
        ?>
        <form method="post" action="prosesloginUser.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" size="30" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" size="30" placeholder="Masukkan password" required/>
            </div>
            <button type="submit" class="login-button">
                <span class="arrow-icon">&rarr;</span> OK
            </button>
            <p class="back-link"><a href="../index.php">&larr; Kembali ke Home</a></p>
        </form>
    </div>
</body>
</html>