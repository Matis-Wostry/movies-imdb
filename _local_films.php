<?php
$localFiles = glob(__DIR__ . '/data/imdb/tt*.html');
$localFilms = [];

foreach ($localFiles as $file) {
    $id   = basename($file, '.html');
    $html = file_get_contents($file);

    preg_match('/<script type="application\/ld\+json">(\{.*?\})<\/script>/s', $html, $ldMatch);
    $ld = $ldMatch[1] ?? '';

    if (!$ld) { continue; }

    preg_match('/"name":"([^"]+)"/', $ld, $m);
    $title = html_entity_decode($m[1] ?? $id, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    preg_match('/"image":"([^"]+)"/', $ld, $m);
    $image = $m[1] ?? null;

    preg_match('/"datePublished":"([^"]+)"/', $ld, $m);
    $year = isset($m[1]) ? substr($m[1], 0, 4) : null;

    $localFilms[] = compact('id', 'title', 'image', 'year');
}
?>

<section class="max-w-4xl mx-auto px-6 py-12 border-t border-gray-100">

    <h2 class="text-base font-semibold mb-8">Films en local</h2>

    <?php if (empty($localFilms)) { ?>
        <p class="text-gray-400 text-sm">Aucun fichier local trouvé.</p>
    <?php } else { ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            <?php foreach ($localFilms as $film) { ?>
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
                        <?php if ($film['year']) { ?>
                            <p class="mt-0.5 text-xs text-gray-400"><?= $film['year'] ?></p>
                        <?php } ?>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

</section>
