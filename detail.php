<?php
require_once 'db.php';

$id      = $_GET['id'] ?? null;
$movie   = null;
$error   = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $note        = (int) ($_POST['note'] ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if ($note >= 1 && $note <= 10) {
        $stmt = $pdo->prepare('INSERT INTO reviews (movie_id, note, commentaire) VALUES (?, ?, ?)');
        $stmt->execute([$id, $note, $commentaire]);
        $success = true;
    } else {
        $error = "La note doit être comprise entre 1 et 10.";
    }
}

if ($id) {
    $file = __DIR__ . '/data/imdb/' . $id . '.html';

    if (file_exists($file)) {
        $html = file_get_contents($file);

        preg_match('/<script type="application\/ld\+json">(\{.*?\})<\/script>/s', $html, $ldMatch);
        $ld = $ldMatch[1] ?? '';

        if ($ld) {
            preg_match('/"name":"([^"]+)"/', $ld, $m);
            $title = $m[1] ?? '';

            preg_match('/"image":"([^"]+)"/', $ld, $m);
            $image = $m[1] ?? '';

            preg_match('/"aggregateRating":\{[^}]*"ratingValue":([0-9.]+)/', $ld, $m);
            $rating = $m[1] ?? '';

            preg_match('/"datePublished":"([^"]+)"/', $ld, $m);
            $date = $m[1] ?? '';

            preg_match('/"description":"([^"]+)"/', $ld, $m);
            $description = $m[1] ?? '';

            preg_match('/"actor":\[([^\]]+)\]/', $ld, $m);
            preg_match_all('/"name":"([^"]+)"/', $m[1] ?? '', $actorMatches);
            $actors = $actorMatches[1] ?? [];

            $movie = compact('title', 'image', 'rating', 'date', 'description', 'actors');

            $stmt = $pdo->prepare('
                INSERT INTO movies (id, title, image, rating, date, description, actors)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    title       = VALUES(title),
                    image       = VALUES(image),
                    rating      = VALUES(rating),
                    date        = VALUES(date),
                    description = VALUES(description),
                    actors      = VALUES(actors)
            ');
            $stmt->execute([
                $id,
                $title,
                $image,
                $rating ?: null,
                $date   ?: null,
                $description,
                implode(', ', $actors),
            ]);
        } else {
            $error = "Impossible de lire les données du film.";
        }
    } else {
        $error = "Film introuvable (ID : " . htmlspecialchars($id) . ").";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $movie ? htmlspecialchars($movie['title']) . ' — Movies' : 'Movies' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="bg-white text-gray-900 min-h-screen">

    <main class="max-w-3xl mx-auto px-6 py-16">

        <?php if ($error) { ?>
            <p class="text-red-500 text-sm"><?= $error ?></p>
        <?php } elseif (!$id) { ?>
            <p class="text-gray-400 text-sm">Aucun film sélectionné. Ajoutez <code class="bg-gray-100 px-1 rounded">?id=tt0468569</code> à l'URL.</p>
        <?php } elseif ($movie) { ?>

            <div class="flex gap-10 items-start my-10">

                <?php if ($movie['image']) { ?>
                    <img src="<?= htmlspecialchars($movie['image']) ?>"
                        alt="<?= htmlspecialchars($movie['title']) ?>"
                        class="w-40 rounded-lg shadow-sm flex-shrink-0">
                <?php } ?>

                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-semibold leading-tight"><?= htmlspecialchars($movie['title']) ?></h1>

                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                        <?php if ($movie['date']) { ?>
                            <p><?= htmlspecialchars(substr($movie['date'], 0, 4)) ?></p>
                        <?php } ?>
                        <?php if ($movie['rating']) { ?>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.561-.955L10 0l2.951 5.955 6.561.955-4.756 4.635 1.122 6.545z" />
                                </svg>
                                <p><?= htmlspecialchars($movie['rating']) ?> / 10</p>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if ($movie['description']) { ?>
                        <p class="mt-4 text-sm text-gray-600 leading-relaxed"><?= htmlspecialchars($movie['description']) ?></p>
                    <?php } ?>

                    <?php if ($movie['actors']) { ?>
                        <div class="mt-4">
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Acteurs</p>
                            <p class="mt-1 text-sm text-gray-700"><?= htmlspecialchars(implode(', ', $movie['actors'])) ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <section>
                <h2 class="text-base font-semibold mb-6">Laisser un avis</h2>

                <?php if ($success) { ?>
                    <p class="mb-5 text-sm text-green-600">Avis enregistré, merci !</p>
                <?php } ?>

                <form action="" method="post" class="space-y-5">
                    <input type="hidden" name="movie_id" value="<?= htmlspecialchars($id) ?>">

                    <div>
                        <label class="block text-sm text-gray-500 mb-1" for="note">Note <span class="text-gray-300">(1 – 10)</span></label>
                        <input type="number" id="note" name="note" min="1" max="10"
                            class="w-24 border border-gray-200 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-500 mb-1" for="commentaire">Commentaire</label>
                        <textarea id="commentaire" name="commentaire" rows="4"
                            class="w-full border border-gray-200 rounded-md px-3 py-2 text-sm resize-none focus:outline-none focus:ring-1 focus:ring-gray-400"
                            placeholder="Votre avis sur le film…"></textarea>
                    </div>

                    <button type="submit"
                        class="bg-gray-900 text-white text-sm px-5 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        Enregistrer
                    </button>
                </form>
            </section>

        <?php } ?>

    </main>

</body>

</html>