
<?php
require_once __DIR__ . '/config/db.php';

// Escape output safely.
function sermonEscape($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Extract a YouTube video ID from supported URL formats.
function youtubeId(?string $url): ?string
{
    if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }

    $parts = parse_url($url);
    $host = strtolower($parts['host'] ?? '');
    $path = $parts['path'] ?? '';

    $allowedHosts = [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtu.be',
        'www.youtu.be'
    ];

    if (!in_array($host, $allowedHosts, true)) {
        return null;
    }

    $id = null;

    if ($host === 'youtu.be' || $host === 'www.youtu.be') {
        $id = trim($path, '/');
    } else {
        $query = [];
        parse_str($parts['query'] ?? '', $query);

        $id = $query['v'] ?? null;

        if (!$id && preg_match(
            '#^/(?:embed|shorts)/([A-Za-z0-9_-]{11})(?:/|$)#',
            $path,
            $matches
        )) {
            $id = $matches[1];
        }
    }

    return is_string($id) &&
        preg_match('/^[A-Za-z0-9_-]{11}$/', $id)
        ? $id
        : null;
}

// Get the selected language from the URL.
$lang = (($_GET['lang'] ?? 'en') === 'am') ? 'am' : 'en';

// Load sermons from database.
try {
    $stmt = $pdo->query(
        'SELECT id, title_en, title_am, preacher,
                description_en, description_am,
                video_url, sermon_date
         FROM sermons
         ORDER BY sermon_date DESC, id DESC'
    );

    $sermons = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Public sermons error: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to load sermons at this time. Please try again later.');
}
?>

<!DOCTYPE html>
<html lang="<?= sermonEscape($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $lang === 'am' ? 'የቤተ ክርስቲያን ስብከቶች' : 'Church Sermons' ?>
        | Walayta Sodo Kalehiwot Church
    </title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/sermons.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body class="<?= $lang === 'am' ? 'show-am' : '' ?>">

    <!-- Shared Navigation -->
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Sermons Hero Section -->
    <section class="hero">

        <div class="hero-content">

            <span class="hero-subtitle">
                Walayta Sodo Kalehiwot Church
            </span>

            <h1>
                <span class="en">Church Sermons</span>
                <span class="am" lang="am">የቤተ ክርስቲያን ስብከቶች</span>
            </h1>

            <p>
                <span class="en">
                    Listen to God's Word and grow in faith.
                </span>

                <span class="am" lang="am">
                    የእግዚአብሔርን ቃል ይስሙ፤ በእምነትም ያድጉ።
                </span>
            </p>

        </div>

    </section>

    <!-- Sermons Content -->
    <main class="container">

        <?php if (empty($sermons)): ?>

            <div class="empty">

                <h2>
                    <span class="en">No sermons available yet</span>

                    <span class="am" lang="am">
                        እስካሁን ስብከቶች አልተጨመሩም
                    </span>
                </h2>

                <p>
                    <span class="en">
                        Please visit again soon for new sermons.
                    </span>

                    <span class="am" lang="am">
                        ለአዳዲስ ስብከቶች በቅርቡ እንደገና ይጎብኙን።
                    </span>
                </p>

            </div>

        <?php else: ?>

            <div class="sermon-grid">

                <?php foreach ($sermons as $sermon): ?>

                    <?php
                        $videoId = youtubeId($sermon['video_url'] ?? null);
                    ?>

                    <article class="sermon-card">

                        <div class="video">

                            <?php if ($videoId): ?>

                                <iframe
                                    src="https://www.youtube-nocookie.com/embed/<?= sermonEscape($videoId) ?>"
                                    title="<?= sermonEscape(
                                        $lang === 'am'
                                            ? $sermon['title_am']
                                            : $sermon['title_en']
                                    ) ?>"
                                    loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>

                            <?php else: ?>

                                <div class="no-video">

                                    <span class="en">Video not available</span>

                                    <span class="am" lang="am">
                                        ቪዲዮው አልተገኘም
                                    </span>

                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="sermon-content">

                            <h2 class="en">
                                <?= sermonEscape($sermon['title_en']) ?>
                            </h2>

                            <h2 class="am" lang="am">
                                <?= sermonEscape($sermon['title_am']) ?>
                            </h2>

                            <?php if (!empty($sermon['preacher'])): ?>

                                <p class="meta">
                                    <span class="en">Preacher: </span>
                                    <span class="am" lang="am">ሰባኪ፦ </span>
                                    <?= sermonEscape($sermon['preacher']) ?>
                                </p>

                            <?php endif; ?>

                            <?php if (!empty($sermon['sermon_date'])): ?>

                                <p class="meta">
                                    <span class="en">Date: </span>
                                    <span class="am" lang="am">ቀን፦ </span>
                                    <?= sermonEscape($sermon['sermon_date']) ?>
                                </p>

                            <?php endif; ?>

                            <?php if (!empty($sermon['description_en'])): ?>

                                <p class="description en">
                                    <?= nl2br(sermonEscape($sermon['description_en'])) ?>
                                </p>

                            <?php endif; ?>

                            <?php if (!empty($sermon['description_am'])): ?>

                                <p class="description am" lang="am">
                                    <?= nl2br(sermonEscape($sermon['description_am'])) ?>
                                </p>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

    <!-- Shared Website Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script>
    // Change the language of the sermons page.
    function setLanguage(language) {

        if (language !== 'en' && language !== 'am') {
            language = 'en';
        }

        document.querySelectorAll('.en').forEach(function(element) {
            element.hidden = (language !== 'en');
        });

        document.querySelectorAll('.am').forEach(function(element) {
            element.hidden = (language !== 'am');
        });

        document.documentElement.lang = language;

        document.body.classList.toggle('show-am', language === 'am');

        const englishButton = document.getElementById('englishBtn');
        const amharicButton = document.getElementById('amharicBtn');

        if (englishButton) {
            englishButton.classList.toggle('active', language === 'en');
        }

        if (amharicButton) {
            amharicButton.classList.toggle('active', language === 'am');
        }

        const url = new URL(window.location.href);
        url.searchParams.set('lang', language);

        window.history.replaceState({}, '', url);
    }

    // Apply the language selected in the URL.
    document.addEventListener('DOMContentLoaded', function() {
        setLanguage(<?= json_encode($lang) ?>);
    });
    </script>

    <script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
