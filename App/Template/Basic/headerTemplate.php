<html>

    <head>
        <base href="/Network_project/">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
        <link rel="stylesheet" href="node_modules/toastr/build/toastr.min.css">
        <script src="node_modules/jquery/dist/jquery.js"></script>
        <script src="node_modules/toastr/build/toastr.min.js"></script>
        <script src="scripts/variable.js"></script>
        <script src="scripts/header.js"></script>
        <?php if ( isset($data['css']) ): ?>
            <?php foreach ($data['css'] as $css): ?>
                <link rel="stylesheet" href="<?= $css ?>">
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ( isset($data['js']) ): ?>
            <?php foreach ($data['js'] as $js): ?>
                <script src="<?= $js ?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>
    </head>

    <body>

        <header>
            <div class="navbar">
                <p class="hamburger"><i class="fas fa-bars"></i></p>
                <p class="logo">LINDA</p>
                <div class="dropdown">
                    <p>Админ панел</p>
                    <div class="start_panel">
                        <a href="addStaff">Добави персонал</a>
                        <a href="addAbonament">Добави абонамент</a>
                        <a href="addStreet">Добави улица</a>
                        <a href="logout">Изход</a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <aside>
                <ul>
                    <a href="system"><li><i class="fab fa-audible"></i> Система</li></a>
                    <a href="clients"><li><i class="fas fa-users"></i> Клиенти</li></a>
                    <a href="network"><li><i class="fas fa-network-wired"></i> Мрежа</li></ul></a>
                </ul>
            </aside>


