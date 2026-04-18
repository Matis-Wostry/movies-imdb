<?php
$suggestions = [];
foreach (glob(__DIR__ . '/data/imdb/search_*.html') as $f) {
    $name = str_replace('search_', '', basename($f, '.html'));
    $suggestions[] = ucwords(str_replace('_', ' ', $name));
}
?>
<header class="border-b border-gray-100 py-10">
    <div class="max-w-4xl mx-auto px-6 flex flex-col items-center gap-5">
        <a href="index.php" class="text-2xl font-semibold tracking-tight">Movies</a>

        <form action="results.php" method="get" class="flex gap-2 w-full max-w-sm">
            <input type="text" name="q" id="search-input"
                   value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                   placeholder="Rechercher un film…"
                   class="flex-1 border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            <button type="submit"
                    class="bg-gray-900 text-white text-sm px-5 py-2 rounded-md hover:bg-gray-700 transition-colors">
                Chercher
            </button>
        </form>

        <?php if (!empty($suggestions)) { ?>
            <div class="flex flex-wrap justify-center gap-2">
                <?php foreach ($suggestions as $suggestion) { ?>
                    <button type="button"
                            onclick="document.getElementById('search-input').value = '<?= htmlspecialchars($suggestion, ENT_QUOTES) ?>'; this.closest('header').querySelector('form').submit();"
                            class="text-xs text-gray-500 border border-gray-200 rounded-full px-3 py-1 hover:bg-gray-50 transition-colors">
                        <?= htmlspecialchars($suggestion) ?>
                    </button>
                <?php } ?>
            </div>
        <?php } ?>

    </div>
</header>
