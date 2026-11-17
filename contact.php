
<?php
session_start();

require_once __DIR__ . '/config/db.php';

// Select website language.
$lang = (($_GET['lang'] ?? 'en') === 'am') ? 'am' : 'en';

// English and Amharic translations.
$text = [
    'en' => [
        'title' => 'Contact Our Church',
        'subtitle' => 'We would love to hear from you. Send us a message.',
        'info_title' => 'Get in Touch',
        'church' => 'Walayta Sodo Kalehiwot Church',
        'info_text' => 'For questions, prayer requests, or information about our church, please send us a message.',
        'form_title' => 'Send Us a Message',
        'name' => 'Your Name',
        'email' => 'Email Address (optional)',
        'message' => 'Your Message',
        'send' => 'Send Message',
        'success' => 'Your message has been sent successfully. Thank you for contacting us!',
        'required' => 'Please enter your name and message.',
        'invalid_email' => 'Please enter a valid email address.',
        'invalid_length' => 'Your name or message does not meet the required length.',
        'error' => 'Sorry, your message could not be sent. Please try again.',
        'back' => 'Back to Home',
        'name_placeholder' => 'Enter your full name',
        'email_placeholder' => 'Enter your email address',
        'message_placeholder' => 'Write your message here...',
        'required_note' => 'Fields marked with * are required.',
        'contact_note' => 'We appreciate your interest in our church. We look forward to hearing from you.',
        'language' => 'Language'
    ],

    'am' => [
        'title' => 'የቤተ ክርስቲያናችን ያግኙን',
        'subtitle' => 'ከእርስዎ መልእክት መቀበል ደስ ይለናል። መልእክት ይላኩልን።',
        'info_title' => 'እኛን ያግኙ',
        'church' => 'ወላይታ ሶዶ ቃለ ሕይወት ቤተ ክርስቲያን',
        'info_text' => 'ስለ ቤተ ክርስቲያናችን ጥያቄ፣ የጸሎት ጥያቄ ወይም ተጨማሪ መረጃ ካስፈለገዎት መልእክት ይላኩልን።',
        'form_title' => 'መልእክት ይላኩልን',
        'name' => 'ሙሉ ስም',
        'email' => 'የኢሜይል አድራሻ (አስፈላጊ አይደለም)',
        'message' => 'መልእክትዎ',
        'send' => 'መልእክት ላክ',
        'success' => 'መልእክትዎ በተሳካ ሁኔታ ተልኳል። እናመሰግናለን!',
        'required' => 'እባክዎ ስምዎንና መልእክትዎን ያስገቡ።',
        'invalid_email' => 'እባክዎ ትክክለኛ የኢሜይል አድራሻ ያስገቡ።',
        'invalid_length' => 'የስምዎ ወይም የመልእክትዎ ርዝመት ትክክል አይደለም።',
        'error' => 'ይቅርታ፣ መልእክትዎ ሊላክ አልቻለም። እንደገና ይሞክሩ።',
        'back' => 'ወደ መነሻ ገጽ ተመለስ',
        'name_placeholder' => 'ሙሉ ስምዎን ያስገቡ',
        'email_placeholder' => 'የኢሜይል አድራሻዎን ያስገቡ',
        'message_placeholder' => 'መልእክትዎን እዚህ ይጻፉ...',
        'required_note' => '* የተለዩ መስኮች መሞላት አለባቸው።',
        'contact_note' => 'ለቤተ ክርስቲያናችን ላሳዩት ፍላጎት እናመሰግናለን። መልእክትዎን በደስታ እንጠብቃለን።',
        'language' => 'ቋንቋ'
    ]
];

$t = $text[$lang];

// HTML escaping helper.
function contact_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token.
if (
    empty($_SESSION['contact_csrf']) ||
    !is_string($_SESSION['contact_csrf'])
) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

// Form variables.
$name = '';
$email = '';
$message = '';

$success = '';
$error = '';

// Process submitted form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $token = $_POST['csrf_token'] ?? '';

    // CSRF validation.
    if (
        !is_string($token) ||
        !hash_equals($_SESSION['contact_csrf'], $token)
    ) {
        $error = $t['error'];
    }

    // Required fields.
    elseif ($name === '' || $message === '') {
        $error = $t['required'];
    }

    // Length validation.
    elseif (
        mb_strlen($name, 'UTF-8') > 150 ||
        mb_strlen($email, 'UTF-8') > 255 ||
        mb_strlen($message, 'UTF-8') < 5 ||
        mb_strlen($message, 'UTF-8') > 5000
    ) {
        $error = $t['invalid_length'];
    }

    // Email validation.
    elseif (
        $email !== '' &&
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $error = $t['invalid_email'];
    }

    else {
        try {

            $stmt = $pdo->prepare(
                'INSERT INTO contact_messages
                (name, email, message)
                VALUES
                (:name, :email, :message)'
            );

            $stmt->execute([
                ':name' => $name,
                ':email' => $email !== '' ? $email : null,
                ':message' => $message
            ]);

            $success = $t['success'];

            // Clear submitted values.
            $name = '';
            $email = '';
            $message = '';

            // Generate a new token after successful submission.
            $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));

        } catch (PDOException $e) {

            error_log('Contact form error: ' . $e->getMessage());

            $error = $t['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= contact_h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Contact Walayta Sodo Kalehiwot Church for questions, prayer requests and information.">

    <title>
        <?= contact_h($t['title']) ?> |
        Walayta Sodo Kalehiwot Church
    </title>

    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>

<body>

    <!-- Shared Navigation -->
    <?php include __DIR__ . '/includes/header.php'; ?>


    <!-- Hero section -->
    <section class="contact-hero">
        <div class="contact-hero-content">

            <span class="contact-small-title">
                Walayta Sodo Kalehiwot Church
            </span>

            <h1><?= contact_h($t['title']) ?></h1>

            <p><?= contact_h($t['subtitle']) ?></p>

        </div>
    </section>

    <!-- Main content -->
    <main class="contact-layout">

        <!-- Contact information -->
        <section class="contact-info">

            <div class="contact-icon" aria-hidden="true">✉</div>

            <h2><?= contact_h($t['info_title']) ?></h2>

            <h3><?= contact_h($t['church']) ?></h3>

            <p><?= contact_h($t['info_text']) ?></p>

            <div class="contact-divider"></div>

            <p class="contact-note">
                <?= contact_h($t['contact_note']) ?>
            </p>

        </section>

        <!-- Contact form -->
        <section class="contact-form-card">

            <h2><?= contact_h($t['form_title']) ?></h2>

            <p class="form-intro">
                <?= contact_h($t['required_note']) ?>
            </p>

            <!-- Success message -->
            <?php if ($success !== ''): ?>
                <div class="contact-alert contact-success" role="status">
                    <span aria-hidden="true">✓</span>
                    <?= contact_h($success) ?>
                </div>
            <?php endif; ?>

            <!-- Error message -->
            <?php if ($error !== ''): ?>
                <div class="contact-alert contact-error" role="alert">
                    <span aria-hidden="true">!</span>
                    <?= contact_h($error) ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="contact.php?lang=<?= urlencode($lang) ?>"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= contact_h($_SESSION['contact_csrf']) ?>"
                >

                <!-- Name -->
                <label for="name">
                    <?= contact_h($t['name']) ?> *
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="150"
                    value="<?= contact_h($name) ?>"
                    placeholder="<?= contact_h($t['name_placeholder']) ?>"
                    autocomplete="name"
                    required
                >

                <!-- Email -->
                <label for="email">
                    <?= contact_h($t['email']) ?>
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="255"
                    value="<?= contact_h($email) ?>"
                    placeholder="<?= contact_h($t['email_placeholder']) ?>"
                    autocomplete="email"
                >

                <!-- Message -->
                <label for="message">
                    <?= contact_h($t['message']) ?> *
                </label>

                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    minlength="5"
                    maxlength="5000"
                    placeholder="<?= contact_h($t['message_placeholder']) ?>"
                    required
                ><?= contact_h($message) ?></textarea>

                <!-- Submit -->
                <button class="contact-submit" type="submit">
                    <span aria-hidden="true">➤</span>
                    <?= contact_h($t['send']) ?>
                </button>

            </form>

            <!-- Back to homepage -->
            <a
                class="contact-back"
                href="index.php?lang=<?= urlencode($lang) ?>"
            >
                &larr; <?= contact_h($t['back']) ?>
            </a>

        </section>

    </main>

    <!-- Shared Website Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="assets/js/mobile-menu.js" defer></script>

</body>
</html>
