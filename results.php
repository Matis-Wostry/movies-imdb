<?php
$q       = trim($_GET['q'] ?? '');
$results = [];
$error   = null;
$searchQuery = $q;

if ($q) {
    $normalized  = trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($q)), '_');
    $searchFiles = glob(__DIR__ . '/data/imdb/search_*.html');
    $file        = null;

    foreach ($searchFiles as $f) {
        $name = str_replace('search_', '', basename($f, '.html'));
        if (strpos($normalized, $name) !== false || strpos($name, $normalized) !== false) {
            $file = $f;
            break;
        }
    }

    if ($file) {
        $html = file_get_contents($file);

        preg_match_all('/href="\/title\/(tt\d+)\/[^"]*"[^>]*aria-label="View title page for ([^"]+)"/', $html, $titleMatches);
        preg_match_all('/src="(https:\/\/m\.media-amazon\.com\/images\/M\/[^" <]+)"/', $html, $imgMatches);

        $ids    = $titleMatches[1];
        $titles = $titleMatches[2];
        $images = $imgMatches[1];

        foreach ($ids as $i => $id) {
            $results[] = [
                'id'    => $id,
                'title' => $titles[$i],
                'image' => $images[$i] ?? null,
            ];
        }
    } else {
        $error = "Aucun résultat pour \"" . htmlspecialchars($q) . "\".";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats — <?= htmlspecialchars($q) ?></title>
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

    <?php if ($q) { ?>
        <h2 class="text-base font-semibold mb-8">
            Résultats pour <span class="text-gray-400 font-normal">«&nbsp;<?= htmlspecialchars($q) ?>&nbsp;»</span>
        </h2>
    <?php } ?>

    <?php if (!$q) { ?>
        <p class="text-gray-400 text-sm">Entrez un terme de recherche.</p>
    <?php } elseif ($error) { ?>
        <p class="text-gray-400 text-sm"><?= $error ?></p>
    <?php } else { ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            <?php foreach ($results as $result) { ?>
                <div class="flex flex-col">
                    <?php if ($result['image']) { ?>
                        <img src="<?= htmlspecialchars($result['image']) ?>"
                             alt="<?= htmlspecialchars($result['title']) ?>"
                             class="w-full rounded-lg shadow-sm">
                    <?php } else { ?>
                        <div class="w-full aspect-[2/3] bg-gray-100 rounded-lg"></div>
                    <?php } ?>
                    <p class="mt-2 text-sm font-medium leading-tight"><?= htmlspecialchars($result['title']) ?></p>
                    <a href="detail.php?id=<?= htmlspecialchars($result['id']) ?>"
                       class="mt-2 text-center text-xs border border-gray-200 rounded-md px-3 py-1.5 hover:bg-gray-50 transition-colors">
                        Voir le film
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</main>

</body>
</html>
