
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Count database records safely.
function countRows(PDO $pdo, string $table): int
{
    $allowed = [
        'sermons',
        'events',
        'announcements',
        'members',
        'contact_messages',
        'live_programs'
    ];

    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    return (int) $pdo->query(
        "SELECT COUNT(*) FROM `$table`"
    )->fetchColumn();
}

// Selected language.
$lang = ($_GET['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Dashboard translations.
$translations = [
    'en' => [
        'page_title' => 'Admin Dashboard',
        'welcome' => 'Welcome,',
        'logout' => 'Log out',
        'dashboard' => 'Dashboard',
        'overview_description' => 'Overview of your church website administration.',
        'notice' => 'Manage your church website content, members, events, sermons, announcements, live programs, messages, and donations from this dashboard.',
        'website_overview' => 'Website Overview',
        'management_sections' => 'Management Sections',
        'open_section' => 'Open section →',
        'public_website' => '← View public website',
        'copyright' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel',

        'sermons' => 'Sermons',
        'events' => 'Events',
        'announcements' => 'Announcements',
        'members' => 'Members',
        'messages' => 'Contact messages',
        'live_programs' => 'Live programs',

        'sermons_desc' => 'Manage sermon titles and video links',
        'events_desc' => 'Manage church events and schedules',
        'announcements_desc' => 'Publish church news in both languages',
        'live_desc' => 'Schedule and manage church livestream programs',
        'members_desc' => 'View member registrations',
        'messages_desc' => 'Review messages from visitors',
        'donations' => 'Donations',
        'donations_desc' => 'Review donation records'
    ],

    'am' => [
        'page_title' => 'የአስተዳዳሪ ዳሽቦርድ',
        'welcome' => 'እንኳን ደህና መጡ፣',
        'logout' => 'ውጣ',
        'dashboard' => 'ዳሽቦርድ',
        'overview_description' => 'የቤተ ክርስቲያኑ ድረ-ገጽ አስተዳደር አጠቃላይ እይታ።',
        'notice' => 'የቤተ ክርስቲያኑን የድረ-ገጽ ይዘት፣ አባላት፣ መርሃ ግብሮች፣ ስብከቶች፣ ማስታወቂያዎች፣ የቀጥታ ስርጭት ፕሮግራሞች፣ መልዕክቶች እና ልገሳዎችን ከዚህ ዳሽቦርድ ያስተዳድሩ።',
        'website_overview' => 'የድረ-ገጽ አጠቃላይ እይታ',
        'management_sections' => 'የአስተዳደር ክፍሎች',
        'open_section' => 'ክፍሉን ክፈት →',
        'public_website' => '← የህዝብ ድረ-ገጹን ይመልከቱ',
        'copyright' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',

        'sermons' => 'ስብከቶች',
        'events' => 'መርሃ ግብሮች',
        'announcements' => 'ማስታወቂያዎች',
        'members' => 'አባላት',
        'messages' => 'የእውቂያ መልዕክቶች',
        'live_programs' => 'የቀጥታ ስርጭት ፕሮግራሞች',

        'sermons_desc' => 'የስብከት ርዕሶችን እና የቪዲዮ ሊንኮችን ያስተዳድሩ',
        'events_desc' => 'የቤተ ክርስቲያን መርሃ ግብሮችን እና የጊዜ ሰሌዳዎችን ያስተዳድሩ',
        'announcements_desc' => 'የቤተ ክርስቲያን ዜናዎችን በሁለቱም ቋንቋዎች ያትሙ',
        'live_desc' => 'የቤተ ክርስቲያን የቀጥታ ስርጭት ፕሮግራሞችን ያቅዱ እና ያስተዳድሩ',
        'members_desc' => 'የአባላትን ምዝገባ ይመልከቱ',
        'messages_desc' => 'ከጎብኚዎች የተላኩ መልዕክቶችን ይመልከቱ',
        'donations' => 'ልገሳዎች',
        'donations_desc' => 'የልገሳ መዝገቦችን ይመልከቱ'
    ]
];

$t = $translations[$lang];

// Dashboard statistics.
$stats = [
    $t['sermons'] => countRows($pdo, 'sermons'),
    $t['events'] => countRows($pdo, 'events'),
    $t['announcements'] => countRows($pdo, 'announcements'),
    $t['members'] => countRows($pdo, 'members'),
    $t['messages'] => countRows($pdo, 'contact_messages'),
    $t['live_programs'] => countRows($pdo, 'live_programs')
];

// Dashboard management cards.
$sections = [
    [$t['sermons'], $t['sermons_desc'], 'sermons.php'],
    [$t['events'], $t['events_desc'], 'events.php'],
    [$t['announcements'], $t['announcements_desc'], 'announcements.php'],
    [$t['live_programs'], $t['live_desc'], 'live-programs.php'],
    [$t['members'], $t['members_desc'], 'members.php'],
    [$t['messages'], $t['messages_desc'], 'messages.php'],
    [$t['donations'], $t['donations_desc'], 'donations.php']
];

$username = $_SESSION['admin_username'] ?? 'Admin';
$csrfToken = $_SESSION['logout_csrf'] ?? '';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= htmlspecialchars($t['page_title'], ENT_QUOTES, 'UTF-8') ?>
        | Walayta Sodo Kalehiwot Church
    </title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<!-- ADMIN WELCOME BAR -->
<div class="admin-welcome-bar">

    <div>
        <?= htmlspecialchars($t['welcome'], ENT_QUOTES, 'UTF-8') ?>

        <strong>
            <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
        </strong>
    </div>

    <form action="logout.php" method="post" class="logout-form">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit" class="logout-button">
            <?= htmlspecialchars($t['logout'], ENT_QUOTES, 'UTF-8') ?>
        </button>

    </form>

</div>

<!-- DASHBOARD CONTENT -->
<main class="container">

    <h2><?= htmlspecialchars($t['dashboard'], ENT_QUOTES, 'UTF-8') ?></h2>

    <p class="muted">
        <?= htmlspecialchars($t['overview_description'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <div class="notice">
        <?= htmlspecialchars($t['notice'], ENT_QUOTES, 'UTF-8') ?>
    </div>

    <!-- WEBSITE STATISTICS -->
    <h2>
        <?= htmlspecialchars($t['website_overview'], ENT_QUOTES, 'UTF-8') ?>
    </h2>

    <div class="stats">

        <?php foreach ($stats as $label => $count): ?>

            <section class="stat">

                <span>
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <strong>
                    <?= $count ?>
                </strong>

            </section>

        <?php endforeach; ?>

    </div>

    <!-- MANAGEMENT SECTIONS -->
    <h2>
        <?= htmlspecialchars($t['management_sections'], ENT_QUOTES, 'UTF-8') ?>
    </h2>

    <div class="section-grid">

        <?php foreach ($sections as [$title, $description, $url]): ?>

            <section class="section-card">

                <h3>
                    <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p class="muted">
                    <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>?lang=<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($t['open_section'], ENT_QUOTES, 'UTF-8') ?>
                </a>

            </section>

        <?php endforeach; ?>

    </div>

    <p class="public-site-link">
        <a href="../index.php?lang=<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($t['public_website'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>

</main>

<!-- ADMIN FOOTER -->
<footer class="admin-footer">

    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= htmlspecialchars($t['copyright'], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <p>
        <?= htmlspecialchars($t['admin_panel'], ENT_QUOTES, 'UTF-8') ?>
    </p>

</footer>

</body>
</html>
