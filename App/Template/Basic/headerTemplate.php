<html>

    <head>
        <base href="/Network_project/">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
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
            <span>SAIP</span>
        </header>

        <main>
            <aside>
                <ul>
                    <li><i class="fab fa-audible"></i> Система</li>
                    <li><i class="fas fa-users"></i> <a href="clients/1">Клиенти</a></li>
                    <li><i class="fas fa-network-wired"></i> Мрежа</li>
                </ul>
            </aside>


