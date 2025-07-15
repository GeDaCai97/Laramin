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
        <?= vite_asset('js/main.js') ?>
    <?php endif; ?>

</head>
<body>
    <?php echo $this->renderComponent('header', []); ?>
    
    <main>
        <section class="main_container">
            <div class="logo">
                <img src="<?= asset('img/laravel.svg') ?>" alt="Logo" class="logo_laravel">
            </div>
            <h1 class="titulo_principal text-3xl font-bold">Bienvenido a Laramin Framework, inspirado en el framework basado en PHP de Laravel</h1>
        </section>
        <section class="mx-20 my-8">
            <h3 class="p-8 text-2xl font-bold text-center">¡Novedades!</h3>
            <div class="grid grid-cols-3 gap-4 content-center">
                <?php echo $this->renderComponent('card', ['slot' => <<<HTML
Integracion de TailwindCSS y SCSS
HTML]); ?>
                <?php echo $this->renderComponent('card', ['slot' => <<<HTML
Soporte para components en Blade
HTML]); ?>
                <?php echo $this->renderComponent('card', ['slot' => <<<HTML
Motor Blade para vistas
HTML]); ?>
            </div>

        </section>
    </main>

    

    <?php echo $this->renderComponent('footer', []); ?>
</body>
</html>
