<?php
// Shared header used only by admin pages.

$lang = ($lang ?? 'en') === 'am' ? 'am' : 'en';

$adminLabels = [
    'en' => [
        'church' => 'Walayta Sodo Kalehiwot Church',
        'dashboard' => 'Dashboard',
        'events' => 'Events',
        'sermons' => 'Sermons',
        'live_programs' => 'Live Programs',
        'announcements' => 'Announcements',
        'members' => 'Members',
        'messages' => 'Messages',
        'donations' => 'Donations',
        'public' => 'Public Website',
        'menu' => 'Open navigation menu'
    ],
    'am' => [
        'church' => 'ወላይታ ሶዶ ቃለ ሕይወት ቤተ ክርስቲያን',
        'dashboard' => 'ዳሽቦርድ',
        'events' => 'መርሃ ግብሮች',
        'sermons' => 'ስብከቶች',
        'live_programs' => 'የቀጥታ ስርጭት ፕሮግራሞች',
        'announcements' => 'ማስታወቂያዎች',
        'members' => 'አባላት',
        'messages' => 'መልዕክቶች',
        'donations' => 'ልገሳዎች',
        'public' => 'የህዝብ ድረ-ገጽ',
        'menu' => 'የአሰሳ ምናሌን ክፈት'
    ]
];

$adminText = $adminLabels[$lang];
$currentAdminPage = basename($_SERVER['PHP_SELF'] ?? '');


if (!function_exists('adminHeaderEscape')) {
    function adminHeaderEscape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>

<header class="navbar admin-navbar">

    <a class="brand" href="dashboard.php?lang=<?= $lang ?>">
        <img
            src="../assets/images/church-logo.jpg"
            alt="Walayta Sodo Kalehiwot Church Logo"
            class="logo-image"
        >

        <span class="brand-name">
            <?= adminHeaderEscape($adminText['church']) ?>
        </span>
    </a>

    <button
        class="menu-toggle"
        type="button"
        aria-label="<?= adminHeaderEscape($adminText['menu']) ?>"
        aria-expanded="false"
        aria-controls="main-navigation"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

    <nav id="main-navigation" aria-label="Admin navigation">

        <a href="dashboard.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'dashboard.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['dashboard']) ?>
        </a>

        <a href="events.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'events.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['events']) ?>
        </a>

        <a href="sermons.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'sermons.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['sermons']) ?>
        </a>

        <a href="live-programs.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'live-programs.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['live_programs']) ?>
        </a>

        <a href="announcements.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'announcements.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['announcements']) ?>
        </a>

        <a href="members.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'members.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['members']) ?>
        </a>

        <a href="messages.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'messages.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['messages']) ?>
        </a>

        <a href="donations.php?lang=<?= $lang ?>"
           class="<?= $currentAdminPage === 'donations.php' ? 'current' : '' ?>">
            <?= adminHeaderEscape($adminText['donations']) ?>
        </a>

        <a href="../index.php?lang=<?= $lang ?>">
            <?= adminHeaderEscape($adminText['public']) ?>
        </a>

        <div class="languages">
            <a
                class="<?= $lang === 'en' ? 'active' : '' ?>"
                href="<?= adminHeaderEscape($currentAdminPage) ?>?lang=en"
            >EN</a>

            <span>|</span>

            <a
                class="<?= $lang === 'am' ? 'active' : '' ?>"
                href="<?= adminHeaderEscape($currentAdminPage) ?>?lang=am"
            >አማ</a>
        </div>

    </nav>

</header>
