
<?php
$footerLang = (isset($lang) && $lang === 'am') ? 'am' : 'en';

$footerText = [
    'en' => [
        'church' => 'Walayta Sodo Kalehiwot Church',
        'faith' => 'FAITH · HOPE · LOVE',
        'home' => 'Home',
        'about' => 'About Us',
        'sermons' => 'Sermons',
        'events' => 'Events',
        'contact' => 'Contact',
        'rights' => 'All rights reserved.'
    ],
    'am' => [
        'church' => 'ወላይታ ሶዶ ቃለ ሕይወት ቤተ ክርስቲያን',
        'faith' => 'እምነት · ተስፋ · ፍቅር',
        'home' => 'መነሻ',
        'about' => 'ስለ እኛ',
        'sermons' => 'ስብከቶች',
        'events' => 'መርሃ ግብሮች',
        'contact' => 'ያግኙን',
        'rights' => 'መብቱ በሕግ የተጠበቀ ነው።'
    ]
];

$ft = $footerText[$footerLang];

function footerEscape($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<footer class="site-footer">

    <div class="footer-content">

        <div class="footer-brand">

            <img
                src="assets/images/church-logo.jpg"
                alt="Walayta Sodo Kalehiwot Church Logo"
                class="footer-church-logo"
            >

            <h3><?= footerEscape($ft['church']) ?></h3>

            <p><?= footerEscape($ft['faith']) ?></p>

        </div>

        <div class="footer-links">

            <a href="index.php?lang=<?= footerEscape($footerLang) ?>">
                <?= footerEscape($ft['home']) ?>
            </a>

            <a href="index.php?lang=<?= footerEscape($footerLang) ?>#about">
                <?= footerEscape($ft['about']) ?>
            </a>

            <a href="sermons.php?lang=<?= footerEscape($footerLang) ?>">
                <?= footerEscape($ft['sermons']) ?>
            </a>

            <a href="events.php?lang=<?= footerEscape($footerLang) ?>">
                <?= footerEscape($ft['events']) ?>
            </a>

            <a href="contact.php?lang=<?= footerEscape($footerLang) ?>">
                <?= footerEscape($ft['contact']) ?>
            </a>

        </div>

    </div>

    <div class="footer-bottom">

        <p>
            &copy; <?= date('Y') ?>
            <?= footerEscape($ft['church']) ?>.
            <?= footerEscape($ft['rights']) ?>
        </p>

    </div>

</footer>
