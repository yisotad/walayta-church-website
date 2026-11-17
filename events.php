
<?php
require_once __DIR__ . '/config/db.php';

// Get selected language.
$lang = (($_GET['lang'] ?? 'en') === 'am') ? 'am' : 'en';

// Safely display content.
function eventEscape($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Load events from database.
try {
    $stmt = $pdo->query(
        'SELECT id, title_en, title_am, description_en, description_am,
                event_date, location
         FROM events
         ORDER BY event_date ASC, id DESC'
    );

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    error_log('Public events error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Events could not be loaded. Please try again later.');
}

// Separate upcoming and past events.
$upcomingEvents = [];
$pastEvents = [];

foreach ($events as $event) {

    $eventTimestamp = strtotime($event['event_date']);

    if ($eventTimestamp === false) {
        continue;
    }

    if ($eventTimestamp >= time()) {
        $upcomingEvents[] = $event;
    } else {
        $pastEvents[] = $event;
    }
}

// Page translations.
$text = [
    'en' => [
        'title' => 'Church Events',
        'hero_title' => 'Church Events',
        'hero_subtitle' => 'Join us for worship, fellowship, and special events in our church community.',
        'upcoming_heading' => 'Upcoming Events',
        'past_heading' => 'Past Events',
        'empty_upcoming' => 'There are no upcoming events at the moment. Please check again soon.',
        'empty_past' => 'There are no past events to display yet.',
        'upcoming' => 'Upcoming',
        'past' => 'Completed',
        'date' => 'Date and time:',
        'location' => 'Location:'
    ],

    'am' => [
        'title' => 'የቤተክርስቲያን ዝግጅቶች',
        'hero_title' => 'የቤተክርስቲያን ዝግጅቶች',
        'hero_subtitle' => 'ከቤተክርስቲያናችን መጪ ዝግጅቶች፣ አምልኮዎችና ልዩ መርሃ ግብሮች ጋር ይቀላቀሉ።',
        'upcoming_heading' => 'መጪ ዝግጅቶች',
        'past_heading' => 'ያለፉ ዝግጅቶች',
        'empty_upcoming' => 'በአሁኑ ጊዜ መጪ ዝግጅቶች የሉም። እባክዎ በቅርቡ እንደገና ይመልከቱ።',
        'empty_past' => 'እስካሁን የሚታዩ ያለፉ ዝግጅቶች የሉም።',
        'upcoming' => 'መጪ',
        'past' => 'ያለፈ',
        'date' => 'ቀንና ሰዓት፦',
        'location' => 'ቦታ፦'
    ]
];

$t = $text[$lang];
?>

<!DOCTYPE html>
<html lang="<?= eventEscape($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= eventEscape($t['title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/events.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

    <!-- Shared Navigation -->
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Events Hero Section -->
    <section class="events-hero">
        <div class="events-hero-overlay">
            <div class="events-hero-content">

                <span class="events-hero-subtitle">
                    Walayta Sodo Kalehiwot Church
                </span>

                <h1>
                    <?= eventEscape($t['hero_title']) ?>
                </h1>

                <p>
                    <?= eventEscape($t['hero_subtitle']) ?>
                </p>

            </div>
        </div>
    </section>

    <main class="events-container">

        <!-- UPCOMING EVENTS -->
        <section class="events-group upcoming-events">

            <div class="events-heading">
                <h2><?= eventEscape($t['upcoming_heading']) ?></h2>
            </div>

            <?php if (!empty($upcomingEvents)): ?>

                <div class="event-grid">

                    <?php foreach ($upcomingEvents as $event): ?>

                        <?php
                        $eventTimestamp = strtotime($event['event_date']);

                        $formattedDate = date(
                            'F j, Y \a\t g:i A',
                            $eventTimestamp
                        );

                        $eventTitle = $lang === 'am'
                            ? $event['title_am']
                            : $event['title_en'];

                        $eventDescription = $lang === 'am'
                            ? $event['description_am']
                            : $event['description_en'];
                        ?>

                        <article class="event-card upcoming-card">

                            <span class="status upcoming-status">
                                <?= eventEscape($t['upcoming']) ?>
                            </span>

                            <h3>
                                <?= eventEscape($eventTitle) ?>
                            </h3>

                            <div class="event-date">
                                <span><?= eventEscape($t['date']) ?></span>
                                <?= eventEscape($formattedDate) ?>
                            </div>

                            <?php if (!empty($event['location'])): ?>
                                <div class="event-location">
                                    <span><?= eventEscape($t['location']) ?></span>
                                    <?= eventEscape($event['location']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($eventDescription)): ?>
                                <div class="event-description">
                                    <?= nl2br(eventEscape($eventDescription)) ?>
                                </div>
                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty">
                    <p><?= eventEscape($t['empty_upcoming']) ?></p>
                </div>

            <?php endif; ?>

        </section>


        <!-- PAST EVENTS -->
        <section class="events-group past-events">

            <div class="events-heading">
                <h2><?= eventEscape($t['past_heading']) ?></h2>
            </div>

            <?php if (!empty($pastEvents)): ?>

                <div class="event-grid">

                    <?php foreach ($pastEvents as $event): ?>

                        <?php
                        $eventTimestamp = strtotime($event['event_date']);

                        $formattedDate = date(
                            'F j, Y \a\t g:i A',
                            $eventTimestamp
                        );

                        $eventTitle = $lang === 'am'
                            ? $event['title_am']
                            : $event['title_en'];

                        $eventDescription = $lang === 'am'
                            ? $event['description_am']
                            : $event['description_en'];
                        ?>

                        <article class="event-card past-card">

                            <span class="status past-status">
                                <?= eventEscape($t['past']) ?>
                            </span>

                            <h3>
                                <?= eventEscape($eventTitle) ?>
                            </h3>

                            <div class="event-date">
                                <span><?= eventEscape($t['date']) ?></span>
                                <?= eventEscape($formattedDate) ?>
                            </div>

                            <?php if (!empty($event['location'])): ?>
                                <div class="event-location">
                                    <span><?= eventEscape($t['location']) ?></span>
                                    <?= eventEscape($event['location']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($eventDescription)): ?>
                                <div class="event-description">
                                    <?= nl2br(eventEscape($eventDescription)) ?>
                                </div>
                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="empty">
                    <p><?= eventEscape($t['empty_past']) ?></p>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <!-- Shared Website Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
