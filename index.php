
<?php
require_once __DIR__ . '/config/db.php';

// Select website language.
$lang = ($_GET['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Escape output safely.
function homeEscape($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Get upcoming events.
$upcomingEvents = $pdo->query(
    "SELECT id, title_en, title_am, description_en,
            description_am, event_date, location
     FROM events
     WHERE event_date >= NOW()
     ORDER BY event_date ASC
     LIMIT 3"
)->fetchAll(PDO::FETCH_ASSOC);

// Get latest sermons.
$latestSermons = $pdo->query(
    "SELECT id, title_en, title_am, preacher,
            description_en, description_am, sermon_date
     FROM sermons
     ORDER BY sermon_date DESC, id DESC
     LIMIT 3"
)->fetchAll(PDO::FETCH_ASSOC);

// Website translations.
$text = [
    'en' => [
        'home' => 'Home',
        'about' => 'About Us',
        'sermons' => 'Sermons',
        'events' => 'Events',
        'contact' => 'Contact',
        'welcome' => 'Welcome to Our Church',
        'subtitle' => 'A place of worship, faith, love, and fellowship.',
        'join' => 'Join Our Worship',
        'about_title' => 'Growing Together in Faith',
        'about_text' => 'We welcome you to worship with us, learn the Word of God, and grow together as a church family.',
        'about_button' => 'Learn More',
        'activities' => 'Our Church Activities',
        'sermons_text' => 'Listen to sermons and grow through Bible teachings.',
        'events_text' => 'Discover upcoming worship services and church events.',
        'live_text' => 'Join our online worship when live streaming is available.',
        'sermons_link' => 'Explore Sermons',
        'events_link' => 'View Events',
        'live_link' => 'Watch Live',
        'live_title' => 'Live Streaming',
        'service' => 'Worship with Us',
        'schedule' => 'Join us for worship and fellowship. Service times will be announced here.',
        'upcoming' => 'Upcoming Events',
        'latest' => 'Latest Sermons',
        'view_all' => 'View All',
        'no_events' => 'There are no upcoming events at the moment.',
        'no_sermons' => 'No sermons have been added yet.',
        'explore_events' => 'Explore Events',
        'explore_sermons' => 'Explore Sermons',
        'preacher' => 'Preacher:',
        'contact_title' => 'We Would Love to Hear from You',
        'contact_text' => 'Have a question or want to join us? Get in touch with our church.',
        'contact_button' => 'Contact Us',
        'footer' => 'All rights reserved.',
        'faith' => 'FAITH · HOPE · LOVE',
        'who_we_are' => 'WHO WE ARE',
        'community' => 'JOIN OUR COMMUNITY',
        'welcome_label' => 'YOU ARE WELCOME',
        'happening' => "WHAT'S HAPPENING",
        'grow_word' => 'GROW IN THE WORD',
        'get_touch' => 'GET IN TOUCH'
    ],

    'am' => [
        'home' => 'መነሻ',
        'about' => 'ስለ እኛ',
        'sermons' => 'ስብከቶች',
        'events' => 'መርሃ ግብሮች',
        'contact' => 'ያግኙን',
        'welcome' => 'ወደ ቤተ ክርስቲያናችን እንኳን በደህና መጡ',
        'subtitle' => 'የአምልኮ፣ የእምነት፣ የፍቅርና የኅብረት ቦታ።',
        'join' => 'በአምልኮ ይቀላቀሉን',
        'about_title' => 'በእምነት አብረን እናድግ',
        'about_text' => 'ከእኛ ጋር እንዲያመልኩ፣ የእግዚአብሔርን ቃል እንዲማሩና በኅብረት እንዲያድጉ እንጋብዝዎታለን።',
        'about_button' => 'ተጨማሪ ይወቁ',
        'activities' => 'የቤተ ክርስቲያን አገልግሎቶች',
        'sermons_text' => 'ስብከቶችንና የመጽሐፍ ቅዱስ ትምህርቶችን ያዳምጡ።',
        'events_text' => 'መጪ የአምልኮና የቤተ ክርስቲያን መርሃ ግብሮችን ይመልከቱ።',
        'live_text' => 'የቀጥታ ስርጭት ሲኖር በመስመር ላይ ይቀላቀሉን።',
        'sermons_link' => 'ስብከቶችን ይመልከቱ',
        'events_link' => 'መርሃ ግብሮችን ይመልከቱ',
        'live_link' => 'የቀጥታ ስርጭት',
        'live_title' => 'ቀጥታ ስርጭት',
        'service' => 'ከእኛ ጋር ያምልኩ',
        'schedule' => 'ለአምልኮና ለኅብረት ከእኛ ጋር ይቀላቀሉ። የአምልኮ ሰዓቶች እዚህ ይገለጻሉ።',
        'upcoming' => 'መጪ መርሃ ግብሮች',
        'latest' => 'የቅርብ ጊዜ ስብከቶች',
        'view_all' => 'ሁሉንም ይመልከቱ',
        'no_events' => 'በአሁኑ ጊዜ የታቀዱ መጪ መርሃ ግብሮች የሉም።',
        'no_sermons' => 'እስካሁን ስብከቶች አልተጨመሩም።',
        'explore_events' => 'መርሃ ግብሮችን ይመልከቱ',
        'explore_sermons' => 'ስብከቶችን ያዳምጡ',
        'preacher' => 'ሰባኪ፦',
        'contact_title' => 'ከእኛ ጋር ይገናኙ',
        'contact_text' => 'ጥያቄ ካለዎት ወይም ከእኛ ጋር መቀላቀል ከፈለጉ ያግኙን።',
        'contact_button' => 'ያግኙን',
        'footer' => 'መብቱ በሕግ የተጠበቀ ነው።',
        'faith' => 'እምነት · ተስፋ · ፍቅር',
        'who_we_are' => 'ስለ እኛ',
        'community' => 'የእኛን ማኅበረሰብ ይቀላቀሉ',
        'welcome_label' => 'እንኳን ደህና መጡ',
        'happening' => 'በቤተ ክርስቲያናችን የሚካሄዱ መርሃ ግብሮች',
        'grow_word' => 'በእግዚአብሔር ቃል ያድጉ',
        'get_touch' => 'እኛን ያግኙ'
    ]
];

$t = $text[$lang];
?>

<!DOCTYPE html>
<html lang="<?= homeEscape($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= homeEscape($lang === 'am'
            ? 'ወላይታ ሶዶ ቃለ ሕይወት ቤተ ክርስቲያን'
            : 'Walayta Sodo Kalehiwot Church') ?>
    </title>

    <meta name="description"
          content="Welcome to Walayta Sodo Kalehiwot Church.">

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main>

    <!-- HERO SECTION -->
    <section class="hero hero-with-image">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <p class="eyebrow"><?= homeEscape($t['faith']) ?></p>

            <h1><?= homeEscape($t['welcome']) ?></h1>

            <p class="hero-subtitle">
                <?= homeEscape($t['subtitle']) ?>
            </p>

            <a class="button" href="#contact">
                <?= homeEscape($t['join']) ?>
            </a>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about section" id="about">
        <div class="about-content">
            <p class="eyebrow"><?= homeEscape($t['who_we_are']) ?></p>

            <h2><?= homeEscape($t['about_title']) ?></h2>

            <p><?= homeEscape($t['about_text']) ?></p>

            <a class="text-link" href="#activities">
                <?= homeEscape($t['about_button']) ?> →
            </a>
        </div>

        <div class="about-image">
            <img src="assets/images/about-bible.jpg"
                 alt="Open Bible on a wooden table">
        </div>
    </section>

    <!-- ACTIVITIES SECTION -->
    <section class="section activities" id="activities">
        <div class="section-heading">
            <p class="eyebrow"><?= homeEscape($t['community']) ?></p>
            <h2><?= homeEscape($t['activities']) ?></h2>
        </div>

        <div class="cards">
            <article class="card">
                <div class="card-icon" aria-hidden="true">♫</div>
                <h3><?= homeEscape($t['sermons']) ?></h3>
                <p><?= homeEscape($t['sermons_text']) ?></p>

                <a href="sermons.php?lang=<?= homeEscape($lang) ?>">
                    <?= homeEscape($t['sermons_link']) ?> →
                </a>
            </article>

            <article class="card">
                <div class="card-icon" aria-hidden="true">▦</div>
                <h3><?= homeEscape($t['events']) ?></h3>
                <p><?= homeEscape($t['events_text']) ?></p>

                <a href="events.php?lang=<?= homeEscape($lang) ?>">
                    <?= homeEscape($t['events_link']) ?> →
                </a>
            </article>

            <article class="card">
                <div class="card-icon" aria-hidden="true">▶</div>
                <h3><?= homeEscape($t['live_title']) ?></h3>
                <p><?= homeEscape($t['live_text']) ?></p>

                <a href="live.php?lang=<?= homeEscape($lang) ?>">
                    <?= homeEscape($t['live_link']) ?> →
                </a>
            </article>
        </div>
    </section>

    <!-- SERVICE SECTION -->
    <section class="service section worship-section">
        <div class="service-content">
            <p class="eyebrow"><?= homeEscape($t['welcome_label']) ?></p>

            <h2><?= homeEscape($t['service']) ?></h2>

            <p><?= homeEscape($t['schedule']) ?></p>

            <a class="button button-light"
               href="contact.php?lang=<?= homeEscape($lang) ?>">
                <?= homeEscape($t['contact_button']) ?>
            </a>
        </div>
    </section>

    <!-- UPCOMING EVENTS -->
    <section class="home-section" id="upcoming-events">
        <div class="home-section-heading">
            <div>
                <p class="eyebrow"><?= homeEscape($t['happening']) ?></p>
                <h2><?= homeEscape($t['upcoming']) ?></h2>
            </div>

            <a class="home-view-all"
               href="events.php?lang=<?= homeEscape($lang) ?>">
                <?= homeEscape($t['view_all']) ?> →
            </a>
        </div>

        <?php if (empty($upcomingEvents)): ?>
            <p class="home-empty"><?= homeEscape($t['no_events']) ?></p>
        <?php else: ?>
            <div class="home-card-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <article class="home-card">

                        <p class="home-card-date">
                            <?= homeEscape(
                                date('M d, Y · g:i A', strtotime($event['event_date']))
                            ) ?>
                        </p>

                        <h3>
                            <?= homeEscape(
                                $lang === 'am'
                                    ? $event['title_am']
                                    : $event['title_en']
                            ) ?>
                        </h3>

                        <?php if (!empty($event['location'])): ?>
                            <p class="card-location">
                                📍 <?= homeEscape($event['location']) ?>
                            </p>
                        <?php endif; ?>

                        <?php
                        $description = $lang === 'am'
                            ? $event['description_am']
                            : $event['description_en'];
                        ?>

                        <?php if (!empty($description)): ?>
                            <p><?= nl2br(homeEscape($description)) ?></p>
                        <?php endif; ?>

                        <a class="home-card-link"
                           href="events.php?lang=<?= homeEscape($lang) ?>">
                            <?= homeEscape($t['explore_events']) ?> →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- LATEST SERMONS -->
    <section class="home-section latest-sermons" id="latest-sermons">
        <div class="home-section-heading">
            <div>
                <p class="eyebrow"><?= homeEscape($t['grow_word']) ?></p>
                <h2><?= homeEscape($t['latest']) ?></h2>
            </div>

            <a class="home-view-all"
               href="sermons.php?lang=<?= homeEscape($lang) ?>">
                <?= homeEscape($t['view_all']) ?> →
            </a>
        </div>

        <?php if (empty($latestSermons)): ?>
            <p class="home-empty"><?= homeEscape($t['no_sermons']) ?></p>
        <?php else: ?>
            <div class="home-card-grid">
                <?php foreach ($latestSermons as $sermon): ?>
                    <article class="home-card">

                        <img class="sermon-image"
                             src="assets/images/sermon.jpg"
                             alt="Bible and cross">

                        <p class="home-card-date">
                            <?= homeEscape(
                                date('M d, Y', strtotime($sermon['sermon_date']))
                            ) ?>
                        </p>

                        <h3>
                            <?= homeEscape(
                                $lang === 'am'
                                    ? $sermon['title_am']
                                    : $sermon['title_en']
                            ) ?>
                        </h3>

                        <?php if (!empty($sermon['preacher'])): ?>
                            <p>
                                <strong><?= homeEscape($t['preacher']) ?></strong>
                                <?= homeEscape($sermon['preacher']) ?>
                            </p>
                        <?php endif; ?>

                        <?php
                        $description = $lang === 'am'
                            ? $sermon['description_am']
                            : $sermon['description_en'];
                        ?>

                        <?php if (!empty($description)): ?>
                            <p><?= nl2br(homeEscape($description)) ?></p>
                        <?php endif; ?>

                        <a class="home-card-link"
                           href="sermons.php?lang=<?= homeEscape($lang) ?>">
                            <?= homeEscape($t['explore_sermons']) ?> →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- CONTACT SECTION -->
    <section class="contact-section" id="contact">
        <div class="contact-image">
            <img src="assets/images/contact-prayer.jpg"
                 alt="Hands joined together in prayer">
        </div>

        <div class="contact-content">
            <p class="eyebrow"><?= homeEscape($t['get_touch']) ?></p>

            <h2><?= homeEscape($t['contact_title']) ?></h2>

            <p><?= homeEscape($t['contact_text']) ?></p>

            <a class="button"
               href="contact.php?lang=<?= homeEscape($lang) ?>">
                <?= homeEscape($t['contact_button']) ?>
            </a>
        </div>
    </section>

</main>

<!-- SHARED FOOTER -->
<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
