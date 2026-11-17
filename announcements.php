
<?php
require_once __DIR__ . '/config/db.php';

// Select language.
$lang = (($_GET['lang'] ?? 'en') === 'am') ? 'am' : 'en';

// Page translations.
$text = [
    'en' => [
        'title' => 'Church Announcements',
        'subtitle' => 'Stay informed about the latest news and announcements from our church.',
        'latest' => 'Latest Announcements',
        'empty' => 'No announcements available',
        'empty_text' => 'Please check again later for church news and updates.',
        'published' => 'Published on',
        'home' => 'Home'
    ],
    'am' => [
        'title' => 'የቤተክርስቲያን ማስታወቂያዎች',
        'subtitle' => 'ከቤተክርስቲያናችን የሚወጡ አዳዲስ ዜናዎችንና ማስታወቂያዎችን ይከታተሉ።',
        'latest' => 'የቅርብ ጊዜ ማስታወቂያዎች',
        'empty' => 'ምንም ማስታወቂያ የለም',
        'empty_text' => 'እባክዎ ለአዳዲስ የቤተክርስቲያን ዜናዎች በኋላ ይመለሱ።',
        'published' => 'የታተመበት ቀን',
        'home' => 'ዋና ገጽ'
    ]
];

$t = $text[$lang];

// Escape output safely.
function announcementEscape($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Retrieve announcements from the database.
try {
    $stmt = $pdo->query(
        'SELECT id, title_en, title_am, content_en, content_am, published_at
         FROM announcements
         ORDER BY published_at DESC, id DESC'
    );

    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Public announcements error: ' . $e->getMessage());
    $announcements = [];
}
?>

<!DOCTYPE html>
<html lang="<?= announcementEscape($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= announcementEscape($t['title']) ?>
        | Walayta Sodo Kalehiwot Church
    </title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/announcements-public.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="public-announcements">

    <!-- ANNOUNCEMENTS HERO -->
    <section class="announcements-hero">

        <h1><?= announcementEscape($t['title']) ?></h1>

        <p><?= announcementEscape($t['subtitle']) ?></p>

    </section>

    <!-- ANNOUNCEMENTS CONTENT -->
    <section class="announcements-content">

        <h2><?= announcementEscape($t['latest']) ?></h2>

        <?php if (!empty($announcements)): ?>

            <div class="announcement-grid">

                <?php foreach ($announcements as $announcement): ?>

                    <?php
                    $title = $lang === 'am'
                        ? $announcement['title_am']
                        : $announcement['title_en'];

                    $content = $lang === 'am'
                        ? $announcement['content_am']
                        : $announcement['content_en'];
                    ?>

                    <article class="announcement-card">

                        <div class="announcement-date">

                            <?= announcementEscape($t['published']) ?>:

                            <?php if (!empty($announcement['published_at'])): ?>

                                <?= announcementEscape(
                                    date(
                                        'F d, Y',
                                        strtotime($announcement['published_at'])
                                    )
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </div>

                        <h3><?= announcementEscape($title) ?></h3>

                        <?php if (!empty($content)): ?>

                            <p class="announcement-description">
                                <?= nl2br(announcementEscape($content)) ?>
                            </p>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="announcement-empty">

                <h3><?= announcementEscape($t['empty']) ?></h3>

                <p><?= announcementEscape($t['empty_text']) ?></p>

            </div>

        <?php endif; ?>

        <div class="announcements-home-link">

            <a href="index.php?lang=<?= announcementEscape($lang) ?>">
                &larr; <?= announcementEscape($t['home']) ?>
            </a>

        </div>

    </section>

</main>

<!-- SHARED FOOTER -->
<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
