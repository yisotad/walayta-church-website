
<?php
/*
|--------------------------------------------------------------------------
| PUBLIC LIVE PROGRAMS PAGE
|--------------------------------------------------------------------------
| Reads published programs from the live_programs database table.
*/

require_once __DIR__ . '/config/db.php';

// English or Amharic
$lang = ($_GET['lang'] ?? 'en') === 'am' ? 'am' : 'en';

$content = [
    'en' => [
        'page_title' => 'Live Streaming',
        'subtitle' => 'Worship with us from anywhere in the world.',
        'heading' => 'Join Our Live Worship',
        'description' => 'Watch our church services, prayers, worship, and biblical teachings online.',
        'not_live' => 'No Live Stream at the Moment',
        'waiting' => 'When our church starts a live service, the video will appear here.',
        'watch' => 'Watch on YouTube',
        'channel' => 'Visit Our YouTube Channel',
        'schedule' => 'Church Service Schedule',
        'sunday' => 'Sunday Worship Service',
        'sunday_time' => 'Add your Sunday service time here.',
        'prayer' => 'Prayer Meeting',
        'prayer_time' => 'Add your prayer meeting time here.',
        'upcoming' => 'Upcoming Live Programs',
        'no_upcoming' => 'There are no upcoming programs at the moment.',
        'live_now' => 'LIVE NOW',
        'scheduled' => 'Scheduled',
        'ended' => 'Ended',
        'date' => 'Date and Time',
        'location' => 'Location',
        'watch_program' => 'Watch Program',
        'database_error' => 'We could not load live programs. Please try again later.',
        'program_description' => 'Church program'
    ],

    'am' => [
        'page_title' => 'ቀጥታ ስርጭት',
        'subtitle' => 'ከየትኛውም ቦታ ሆነው ከእኛ ጋር አምልኮን ያድርጉ።',
        'heading' => 'በቀጥታ የአምልኮ አገልግሎታችንን ይቀላቀሉ',
        'description' => 'የቤተ ክርስቲያናችንን አገልግሎት፣ ጸሎት፣ አምልኮና የመጽሐፍ ቅዱስ ትምህርቶችን በመስመር ላይ ይከታተሉ።',
        'not_live' => 'በአሁኑ ጊዜ ቀጥታ ስርጭት የለም',
        'waiting' => 'የቤተ ክርስቲያናችን ቀጥታ አገልግሎት ሲጀምር ቪዲዮው እዚህ ይታያል።',
        'watch' => 'በዩቲዩብ ይመልከቱ',
        'channel' => 'የዩቲዩብ ቻናላችንን ይጎብኙ',
        'schedule' => 'የቤተ ክርስቲያን የአገልግሎት ሰዓት',
        'sunday' => 'የእሁድ አምልኮ አገልግሎት',
        'sunday_time' => 'የእሁድ አገልግሎት ሰዓትዎን እዚህ ያስገቡ።',
        'prayer' => 'የጸሎት ስብሰባ',
        'prayer_time' => 'የጸሎት ስብሰባ ሰዓትዎን እዚህ ያስገቡ።',
        'upcoming' => 'መጪ የቀጥታ ስርጭት ፕሮግራሞች',
        'no_upcoming' => 'በአሁኑ ጊዜ የተመዘገቡ መጪ ፕሮግራሞች የሉም።',
        'live_now' => 'አሁን በቀጥታ',
        'scheduled' => 'የተያዘ',
        'ended' => 'የተጠናቀቀ',
        'date' => 'ቀን እና ሰዓት',
        'location' => 'ቦታ',
        'watch_program' => 'ፕሮግራሙን ይመልከቱ',
        'database_error' => 'የቀጥታ ስርጭት ፕሮግራሞችን ማሳየት አልተቻለም። እባክዎ ቆይተው ይሞክሩ።',
        'program_description' => 'የቤተ ክርስቲያን ፕሮግራም'
    ]
];

$t = $content[$lang];

if (!function_exists('live_h')) {
    function live_h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

/*
|--------------------------------------------------------------------------
| Convert supported YouTube links into safe embed URLs
|--------------------------------------------------------------------------
*/

function live_get_youtube_embed($url) {
    $url = trim((string)$url);

    if ($url === '') {
        return '';
    }

    $parts = parse_url($url);

    if (!$parts || empty($parts['host'])) {
        return '';
    }

    $host = strtolower($parts['host']);
    $videoId = '';

    if ($host === 'youtu.be' || $host === 'www.youtu.be') {
        $videoId = trim($parts['path'] ?? '', '/');
    } elseif (
        in_array($host, [
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com'
        ], true)
    ) {
        $path = $parts['path'] ?? '';

        if ($path === '/watch') {
            parse_str($parts['query'] ?? '', $query);
            $videoId = $query['v'] ?? '';
        } elseif (preg_match('~^/(embed|live|shorts)/([^/?]+)~', $path, $matches)) {
            $videoId = $matches[2];
        }
    }

    // YouTube video IDs contain 11 valid characters.
    if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
        return '';
    }

    return 'https://www.youtube-nocookie.com/embed/' . $videoId;
}

/*
|--------------------------------------------------------------------------
| LOAD PUBLISHED PROGRAMS
|--------------------------------------------------------------------------
*/

$liveProgram = null;
$upcomingPrograms = [];
$databaseError = false;

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            title_en,
            title_am,
            description_en,
            description_am,
            program_date,
            youtube_url,
            stream_status
        FROM live_programs
        WHERE is_published = 1
        ORDER BY
            CASE WHEN stream_status = 'live' THEN 0 ELSE 1 END,
            program_date ASC
    ");

    $stmt->execute();
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($programs as $program) {
        if ($program['stream_status'] === 'live' && $liveProgram === null) {
            $liveProgram = $program;
        } elseif ($program['stream_status'] === 'scheduled') {
            $upcomingPrograms[] = $program;
        }
    }

} catch (PDOException $e) {
    error_log('Public live programs error: ' . $e->getMessage());
    $databaseError = true;
}

?>

<!DOCTYPE html>
<html lang="<?= live_h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= live_h($t['page_title']) ?>
        | Walayta Sodo Kalehiwot Church
    </title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/live.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="live-container">

    <!-- Live Streaming Hero -->
    <section class="live-hero">
        <div class="live-hero-content">

            <div class="live-hero-subtitle">
                <?= live_h($t['subtitle']) ?>
            </div>

            <h1>
                <?= live_h($t['heading']) ?>
            </h1>

            <p>
                <?= live_h($t['description']) ?>
            </p>

        </div>
    </section>

    <!-- Live Video Section -->
    <section class="live-video-section">

        <h2>
            <?= live_h($t['page_title']) ?>
        </h2>

        <?php if ($databaseError): ?>

            <div class="live-placeholder">
                <h3><?= live_h($t['database_error']) ?></h3>
            </div>

        <?php elseif ($liveProgram): ?>

            <?php
                $liveTitle = $lang === 'am'
                    ? $liveProgram['title_am']
                    : $liveProgram['title_en'];

                $liveDescription = $lang === 'am'
                    ? $liveProgram['description_am']
                    : $liveProgram['description_en'];

                $liveEmbedUrl = live_get_youtube_embed($liveProgram['youtube_url']);
            ?>

            <div class="live-program-heading">
                <span class="live-status">
                    <?= live_h($t['live_now']) ?>
                </span>

                <h3><?= live_h($liveTitle) ?></h3>

                <?php if (!empty($liveDescription)): ?>
                    <p><?= nl2br(live_h($liveDescription)) ?></p>
                <?php endif; ?>
            </div>

            <div class="live-video">

                <?php if ($liveEmbedUrl !== ''): ?>

                    <iframe
                        src="<?= live_h($liveEmbedUrl) ?>"
                        title="<?= live_h($liveTitle) ?>"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>

                <?php else: ?>

                    <div class="live-placeholder">
                        <h3><?= live_h($t['not_live']) ?></h3>
                        <p><?= live_h($t['waiting']) ?></p>
                    </div>

                <?php endif; ?>

            </div>

            <?php if ($liveEmbedUrl !== ''): ?>
                <a
                    class="live-watch-button"
                    href="<?= live_h($liveProgram['youtube_url']) ?>"
                    target="_blank"
                    rel="noopener noreferrer">
                    <?= live_h($t['watch']) ?>
                </a>
            <?php endif; ?>

        <?php else: ?>

            <div class="live-video">
                <div class="live-placeholder">

                    <div class="live-play-icon">&#9658;</div>

                    <h3><?= live_h($t['not_live']) ?></h3>

                    <p><?= live_h($t['waiting']) ?></p>

                </div>
            </div>

        <?php endif; ?>

    </section>

    <!-- Upcoming Programs -->
    <section class="live-schedule">

        <h2><?= live_h($t['upcoming']) ?></h2>

        <?php if (!$databaseError && !empty($upcomingPrograms)): ?>

            <div class="live-schedule-grid">

                <?php foreach ($upcomingPrograms as $program): ?>

                    <?php
                        $programTitle = $lang === 'am'
                            ? $program['title_am']
                            : $program['title_en'];

                        $programDescription = $lang === 'am'
                            ? $program['description_am']
                            : $program['description_en'];

                        $programDate = date(
                            'F j, Y - g:i A',
                            strtotime($program['program_date'])
                        );

                        $programEmbedUrl = live_get_youtube_embed(
                            $program['youtube_url']
                        );
                    ?>

                    <article class="live-schedule-card">

                        <div class="schedule-icon">&#128197;</div>

                        <h3><?= live_h($programTitle) ?></h3>

                        <?php if (!empty($programDescription)): ?>
                            <p><?= nl2br(live_h($programDescription)) ?></p>
                        <?php endif; ?>

                        <p>
                            <strong><?= live_h($t['date']) ?>:</strong><br>
                            <?= live_h($programDate) ?>
                        </p>

                        <?php if ($programEmbedUrl !== ''): ?>
                            <a
                                class="live-watch-button"
                                href="<?= live_h($program['youtube_url']) ?>"
                                target="_blank"
                                rel="noopener noreferrer">
                                <?= live_h($t['watch_program']) ?>
                            </a>
                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php elseif (!$databaseError): ?>

            <p><?= live_h($t['no_upcoming']) ?></p>

        <?php endif; ?>

    </section>

    <!-- Church Service Schedule -->
    <section class="live-schedule">

        <h2><?= live_h($t['schedule']) ?></h2>

        <div class="live-schedule-grid">

            <article class="live-schedule-card">

                <div class="schedule-icon">&#128197;</div>

                <h3><?= live_h($t['sunday']) ?></h3>

                <p><?= live_h($t['sunday_time']) ?></p>

            </article>

            <article class="live-schedule-card">

                <div class="schedule-icon">&#128591;</div>

                <h3><?= live_h($t['prayer']) ?></h3>

                <p><?= live_h($t['prayer_time']) ?></p>

            </article>

        </div>

    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
