<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Restoran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/menu.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Menu Restoran Kami</h1>
            <p class="text-lg text-gray-600">Pilih makanan dan minuman favorit Anda</p>
        </header>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Order Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-lg sticky top-4 p-6">
                    <!-- Order summary content remains the same -->
                    <!-- ... -->
                </div>
            </div>

            <!-- Menu Items -->
            <div class="lg:w-2/3">
                <!-- Food Section -->
                <section class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-1 bg-blue-600 rounded mr-3"></div>
                        <h2 class="text-2xl font-bold text-gray-800">Makanan</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php foreach ($menuData['makanan'] as $item): ?>
                            <!-- Menu item cards -->
                            <!-- ... -->
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Drink Section -->
                <section>
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-1 bg-green-600 rounded mr-3"></div>
                        <h2 class="text-2xl font-bold text-gray-800">Minuman</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php foreach ($menuData['minuman'] as $item): ?>
                            <!-- Menu item cards -->
                            <!-- ... -->
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="assets/js/order_handler.js"></script>
</body>
</html>
