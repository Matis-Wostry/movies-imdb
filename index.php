<?php
require_once 'db.php';

$movies = $pdo->query('SELECT id, title, image, rating, date FROM movies ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Movies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-white text-gray-900 min-h-screen">

<?php require '_header.php'; ?>

<main class="max-w-4xl mx-auto px-6 py-12">

    <h2 class="text-base font-semibold mb-8">Ma collection</h2>

    <?php if (empty($movies)) { ?>
        <p class="text-gray-400 text-sm">Aucun film en base. Visitez une page détail pour en ajouter.</p>
    <?php } else { ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            <?php foreach ($movies as $film) { ?>
                <a href="detail.php?id=<?= htmlspecialchars($film['id']) ?>" class="group">
                    <?php if ($film['image']) { ?>
                        <img src="<?= htmlspecialchars($film['image']) ?>"
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="w-full rounded-lg shadow-sm group-hover:shadow-md transition-shadow">
                    <?php } else { ?>
                        <div class="w-full aspect-[2/3] bg-gray-100 rounded-lg"></div>
                    <?php } ?>
                    <div class="mt-2">
                        <p class="text-sm font-medium leading-tight"><?= htmlspecialchars($film['title']) ?></p>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <?php if ($film['date']) { ?>
                                <span><?= substr($film['date'], 0, 4) ?></span>
                            <?php } ?>
                            <?php if ($film['rating']) { ?>
                                <span class="flex items-center gap-0.5">
                                    <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.561-.955L10 0l2.951 5.955 6.561.955-4.756 4.635 1.122 6.545z"/>
                                    </svg>
                                    <?= htmlspecialchars($film['rating']) ?>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

</main>

<?php require '_local_films.php'; ?>

</body>
</html>
