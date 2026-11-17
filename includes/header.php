
<?php
// Shared public website header.
// Each public page should define $lang before including this file.

$lang = ($lang ?? 'en') === 'am' ? 'am' : 'en';

$headerLabels = [
    'en' => [
        'home' => 'Home',
        'about' => 'About Us',
        'sermons' => 'Sermons',
        'events' => 'Events',
        'contact' => 'Contact',
        'menu' => 'Open navigation menu',
        'church' => 'Walayta Sodo Kalehiwot Church'
    ],
    'am' => [
        'home' => 'መነሻ',
        'about' => 'ስለ እኛ',
        'sermons' => 'ስብከቶች',
        'events' => 'መርሃ ግብሮች',
        'contact' => 'ያግኙን',
        'menu' => 'የአሰሳ ምናሌን ክፈት',
        'church' => 'ወላይታ ሶዶ ቃለ ሕይወት ቤተ ክርስቲያን'
    ]
];

$headerText = $headerLabels[$lang];
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');

function sharedHeaderEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<header class="navbar">

    <a class="brand" href="index.php?lang=<?= $lang ?>">
        <img
            src="assets/images/church-logo.jpg"
            alt="Walayta Sodo Kalehiwot Church Logo"
            class="logo-image"
        >

        <span class="brand-name">
            <?= sharedHeaderEscape($headerText['church']) ?>
        </span>
    </a>

    <button
        class="menu-toggle"
        type="button"
        aria-label="<?= sharedHeaderEscape($headerText['menu']) ?>"
        aria-expanded="false"
        aria-controls="main-navigation"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="languages">
        <a
            class="<?= $lang === 'en' ? 'active' : '' ?>"
            href="<?= sharedHeaderEscape($currentPage) ?>?lang=en"
            aria-label="English"
        >EN</a>

        <span>|</span>

        <a
            class="<?= $lang === 'am' ? 'active' : '' ?>"
            href="<?= sharedHeaderEscape($currentPage) ?>?lang=am"
            aria-label="Amharic"
        >አማ</a>
    </div>

    <nav id="main-navigation" aria-label="Main navigation">

        <a href="index.php?lang=<?= $lang ?>"
           class="<?= $currentPage === 'index.php' ? 'current' : '' ?>">
            <?= sharedHeaderEscape($headerText['home']) ?>
        </a>

        <a href="index.php?lang=<?= $lang ?>#about">
            <?= sharedHeaderEscape($headerText['about']) ?>
        </a>

        <a href="sermons.php?lang=<?= $lang ?>"
           class="<?= $currentPage === 'sermons.php' ? 'current' : '' ?>">
            <?= sharedHeaderEscape($headerText['sermons']) ?>
        </a>

        <a href="events.php?lang=<?= $lang ?>"
           class="<?= $currentPage === 'events.php' ? 'current' : '' ?>">
            <?= sharedHeaderEscape($headerText['events']) ?>
        </a>

        <a href="index.php?lang=<?= $lang ?>#contact">
            <?= sharedHeaderEscape($headerText['contact']) ?>
        </a>

        <a href="live.php?lang=<?= $lang ?>"
           class="<?= $currentPage === 'live.php' ? 'current' : '' ?>">
            <?= $lang === 'am' ? 'ቀጥታ ስርጭት' : 'Live' ?>
        </a>
<a href="announcements.php?lang=<?php echo $lang; ?>">
    <?php echo $lang === 'am' ? 'ማስታወቂያዎች' : 'Announcements'; ?>
</a>
    </nav>

</header>
