<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Principal</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?php if ($_SERVER['SERVER_NAME'] === 'localhost'): ?>
        <script type="module" src="http://localhost:5173/js/main.js"></script>
    <?php else: ?>
        <?= htmlspecialchars(vite_asset('js/main.js')) ?>
    <?php endif; ?>

</head>
<body>
    <?php include 'D:\Proyectos_web\FrameworkProject/storage/cache/484135d900fbc53efda3e5c3fda5853f.php'; ?>
    
    <h1>Bienvenido, Carlos XDD</h1>

    <?= htmlspecialchars(dd($data)) ?>

</body>
</html>
