
<?php
session_start();

// Show errors during local development.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

// Language selection.
$lang = ($_GET['lang'] ?? $_POST['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Translations for the complete page.
$translations = [
    'en' => [
        'page_title' => 'Manage Live Programs',
        'eyebrow' => 'ADMINISTRATION PANEL',
        'page_description' => "Schedule, update, and manage your church's live streaming programs.",
        'back_dashboard' => '← Back to Dashboard',
        'program_details' => 'PROGRAM DETAILS',
        'edit_program' => 'Edit Live Program',
        'add_program' => 'Add New Live Program',
        'title_en' => 'Program Title (English)',
        'title_am' => 'Program Title (Amharic)',
        'program_date' => 'Program Date and Time',
        'stream_status' => 'Streaming Status',
        'scheduled' => 'Scheduled',
        'live' => 'Live Now',
        'ended' => 'Ended',
        'youtube_url' => 'YouTube Live Stream URL',
        'youtube_help' => 'Enter the YouTube video or livestream link.',
        'description_en' => 'Description (English)',
        'description_am' => 'Description (Amharic)',
        'description_en_placeholder' => 'Write a description of this program...',
        'description_am_placeholder' => 'የፕሮግራሙን መግለጫ ያስገቡ...',
        'publish' => 'Publish this program on the public website',
        'update_program' => 'Update Program',
        'save_program' => 'Save Program',
        'cancel_edit' => 'Cancel Edit',
        'management' => 'PROGRAM MANAGEMENT',
        'existing_programs' => 'Existing Live Programs',
        'programs' => 'Programs',
        'program' => 'Program',
        'date_time' => 'Date and Time',
        'status' => 'Status',
        'visibility' => 'Visibility',
        'actions' => 'Actions',
        'no_programs' => 'No live programs have been added yet.',
        'view_youtube' => 'View YouTube Link',
        'published' => 'Published',
        'hidden' => 'Hidden',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this live program?',
        'footer_rights' => 'All rights reserved.',
        'footer_panel' => 'Church Administration Panel',

        'security_error' => 'Security validation failed. Please refresh the page and try again.',
        'invalid_id' => 'Invalid program ID.',
        'deleted' => 'Live program deleted successfully.',
        'not_found_delete' => 'Live program not found.',
        'required_fields' => 'English title, Amharic title, and program date are required.',
        'title_length' => 'Program titles must not exceed 255 characters.',
        'url_length' => 'YouTube URL must not exceed 500 characters.',
        'invalid_status' => 'Invalid streaming status.',
        'invalid_date' => 'Please enter a valid program date and time.',
        'invalid_url' => 'Please enter a valid YouTube URL beginning with http:// or https://.',
        'updated' => 'Live program updated successfully.',
        'added' => 'Live program added successfully.',
        'invalid_action' => 'Invalid form action.',
        'database_error' => 'A database error occurred. Please check the database and try again.',
        'edit_not_found' => 'The selected live program could not be found.'
    ],

    'am' => [
        'page_title' => 'የቀጥታ ስርጭት ፕሮግራሞችን ያስተዳድሩ',
        'eyebrow' => 'የአስተዳደር ፓነል',
        'page_description' => 'የቤተ ክርስቲያኑን የቀጥታ ስርጭት ፕሮግራሞች ያቅዱ፣ ያዘምኑ እና ያስተዳድሩ።',
        'back_dashboard' => '← ወደ ዳሽቦርድ ተመለስ',
        'program_details' => 'የፕሮግራም ዝርዝሮች',
        'edit_program' => 'የቀጥታ ፕሮግራም ያርትዑ',
        'add_program' => 'አዲስ የቀጥታ ፕሮግራም ያክሉ',
        'title_en' => 'የፕሮግራሙ ርዕስ (እንግሊዝኛ)',
        'title_am' => 'የፕሮግራሙ ርዕስ (አማርኛ)',
        'program_date' => 'የፕሮግራሙ ቀን እና ሰዓት',
        'stream_status' => 'የስርጭት ሁኔታ',
        'scheduled' => 'በመርሃ ግብር የተያዘ',
        'live' => 'አሁን በቀጥታ',
        'ended' => 'ተጠናቋል',
        'youtube_url' => 'የዩቲዩብ የቀጥታ ስርጭት አገናኝ',
        'youtube_help' => 'የዩቲዩብ ቪዲዮ ወይም የቀጥታ ስርጭት አገናኝ ያስገቡ።',
        'description_en' => 'መግለጫ (እንግሊዝኛ)',
        'description_am' => 'መግለጫ (አማርኛ)',
        'description_en_placeholder' => 'የፕሮግራሙን መግለጫ በእንግሊዝኛ ያስገቡ...',
        'description_am_placeholder' => 'የፕሮግራሙን መግለጫ ያስገቡ...',
        'publish' => 'ይህንን ፕሮግራም በህዝብ ድረ-ገጹ ላይ ያትሙ',
        'update_program' => 'ፕሮግራሙን ያዘምኑ',
        'save_program' => 'ፕሮግራሙን ያስቀምጡ',
        'cancel_edit' => 'ማስተካከያውን ይተዉ',
        'management' => 'የፕሮግራም አስተዳደር',
        'existing_programs' => 'ያሉ የቀጥታ ፕሮግራሞች',
        'programs' => 'ፕሮግራሞች',
        'program' => 'ፕሮግራም',
        'date_time' => 'ቀን እና ሰዓት',
        'status' => 'ሁኔታ',
        'visibility' => 'በድረ-ገጹ ላይ መታየት',
        'actions' => 'ተግባራት',
        'no_programs' => 'እስካሁን ምንም የቀጥታ ፕሮግራም አልተጨመረም።',
        'view_youtube' => 'የዩቲዩብ አገናኙን ይመልከቱ',
        'published' => 'ታትሟል',
        'hidden' => 'ተደብቋል',
        'edit' => 'አርትዕ',
        'delete' => 'ሰርዝ',
        'confirm_delete' => 'ይህንን የቀጥታ ፕሮግራም ማጥፋት እርግጠኛ ነዎት?',
        'footer_rights' => 'መብቱ በሕግ የተጠበቀ ነው።',
        'footer_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',

        'security_error' => 'የደህንነት ማረጋገጫው አልተሳካም። እባክዎ ገጹን አድስተው እንደገና ይሞክሩ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የፕሮግራም መለያ ቁጥር።',
        'deleted' => 'የቀጥታ ፕሮግራሙ በተሳካ ሁኔታ ተሰርዟል።',
        'not_found_delete' => 'የቀጥታ ፕሮግራሙ አልተገኘም።',
        'required_fields' => 'የእንግሊዝኛ ርዕስ፣ የአማርኛ ርዕስ እና የፕሮግራሙ ቀን መሞላት አለባቸው።',
        'title_length' => 'የፕሮግራሙ ርዕሶች ከ255 ቁምፊዎች መብለጥ የለባቸውም።',
        'url_length' => 'የዩቲዩብ አገናኙ ከ500 ቁምፊዎች መብለጥ የለበትም።',
        'invalid_status' => 'ትክክለኛ ያልሆነ የስርጭት ሁኔታ።',
        'invalid_date' => 'እባክዎ ትክክለኛ የፕሮግራም ቀንና ሰዓት ያስገቡ።',
        'invalid_url' => 'እባክዎ http:// ወይም https:// የሚጀምር ትክክለኛ የዩቲዩብ አገናኝ ያስገቡ።',
        'updated' => 'የቀጥታ ፕሮግራሙ በተሳካ ሁኔታ ታድሷል።',
        'added' => 'አዲስ የቀጥታ ፕሮግራም በተሳካ ሁኔታ ተጨምሯል።',
        'invalid_action' => 'ትክክለኛ ያልሆነ የቅጽ ትእዛዝ።',
        'database_error' => 'የዳታቤዝ ችግር ተፈጥሯል። እባክዎ ዳታቤዙን ያረጋግጡና እንደገና ይሞክሩ።',
        'edit_not_found' => 'የተመረጠው የቀጥታ ፕሮግራም አልተገኘም።'
    ]
];

$t = $translations[$lang];

// Only logged-in administrators may manage live programs.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php?lang=' . urlencode($lang));
    exit;
}

// Create CSRF token.
$_SESSION['live_programs_csrf'] ??= bin2hex(random_bytes(32));

function liveEscape($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$message = '';
$error = '';
$editingProgram = null;

// Handle form submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['live_programs_csrf'], $token)
    ) {
        $error = 'security_error';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            // DELETE PROGRAM
            if ($action === 'delete') {
                $id = filter_var(
                    $_POST['id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (!$id) {
                    throw new RuntimeException('invalid_id');
                }

                $stmt = $pdo->prepare('DELETE FROM live_programs WHERE id = ?');
                $stmt->execute([$id]);

                $_SESSION['live_programs_flash'] = $stmt->rowCount()
                    ? 'deleted'
                    : 'not_found_delete';

                header('Location: live-programs.php?lang=' . urlencode($lang));
                exit;
            }

            // ADD OR UPDATE PROGRAM
            if ($action === 'save') {
                $id = filter_var(
                    $_POST['id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                $titleEn = trim($_POST['title_en'] ?? '');
                $titleAm = trim($_POST['title_am'] ?? '');
                $descriptionEn = trim($_POST['description_en'] ?? '');
                $descriptionAm = trim($_POST['description_am'] ?? '');
                $programDate = trim($_POST['program_date'] ?? '');
                $youtubeUrl = trim($_POST['youtube_url'] ?? '');
                $status = $_POST['stream_status'] ?? 'scheduled';
                $isPublished = isset($_POST['is_published']) ? 1 : 0;

                if ($titleEn === '' || $titleAm === '' || $programDate === '') {
                    throw new RuntimeException('required_fields');
                }

                if (mb_strlen($titleEn) > 255 || mb_strlen($titleAm) > 255) {
                    throw new RuntimeException('title_length');
                }

                if (mb_strlen($youtubeUrl) > 500) {
                    throw new RuntimeException('url_length');
                }

                $allowedStatuses = ['scheduled', 'live', 'ended'];

                if (!in_array($status, $allowedStatuses, true)) {
                    throw new RuntimeException('invalid_status');
                }

                $date = DateTime::createFromFormat('!Y-m-d\TH:i', $programDate);
                $dateErrors = DateTime::getLastErrors();

                if (
                    !$date ||
                    (
                        $dateErrors !== false &&
                        (
                            $dateErrors['warning_count'] > 0 ||
                            $dateErrors['error_count'] > 0
                        )
                    ) ||
                    $date->format('Y-m-d\TH:i') !== $programDate
                ) {
                    throw new RuntimeException('invalid_date');
                }

                $databaseDate = $date->format('Y-m-d H:i:s');

                // Validate YouTube URL if provided.
                if ($youtubeUrl !== '') {
                    if (
                        !filter_var($youtubeUrl, FILTER_VALIDATE_URL) ||
                        !in_array(
                            strtolower((string)parse_url($youtubeUrl, PHP_URL_SCHEME)),
                            ['http', 'https'],
                            true
                        )
                    ) {
                        throw new RuntimeException('invalid_url');
                    }
                }

                if ($id) {
                    // UPDATE
                    $stmt = $pdo->prepare(
                        'UPDATE live_programs
                         SET title_en = ?,
                             title_am = ?,
                             description_en = ?,
                             description_am = ?,
                             program_date = ?,
                             youtube_url = ?,
                             stream_status = ?,
                             is_published = ?
                         WHERE id = ?'
                    );

                    $stmt->execute([
                        $titleEn,
                        $titleAm,
                        $descriptionEn !== '' ? $descriptionEn : null,
                        $descriptionAm !== '' ? $descriptionAm : null,
                        $databaseDate,
                        $youtubeUrl !== '' ? $youtubeUrl : null,
                        $status,
                        $isPublished,
                        $id
                    ]);

                    $_SESSION['live_programs_flash'] = 'updated';
                } else {
                    // INSERT
                    $stmt = $pdo->prepare(
                        'INSERT INTO live_programs
                         (
                             title_en,
                             title_am,
                             description_en,
                             description_am,
                             program_date,
                             youtube_url,
                             stream_status,
                             is_published
                         )
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                    );

                    $stmt->execute([
                        $titleEn,
                        $titleAm,
                        $descriptionEn !== '' ? $descriptionEn : null,
                        $descriptionAm !== '' ? $descriptionAm : null,
                        $databaseDate,
                        $youtubeUrl !== '' ? $youtubeUrl : null,
                        $status,
                        $isPublished
                    ]);

                    $_SESSION['live_programs_flash'] = 'added';
                }

                header('Location: live-programs.php?lang=' . urlencode($lang));
                exit;
            }

            throw new RuntimeException('invalid_action');

        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $error = 'database_error';

        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

// Retrieve success message after redirect.
if (isset($_SESSION['live_programs_flash'])) {
    $flashKey = $_SESSION['live_programs_flash'];
    $message = $t[$flashKey] ?? '';
    unset($_SESSION['live_programs_flash']);
}

// Translate any error key.
if ($error !== '') {
    $error = $t[$error] ?? $t['database_error'];
}

// Load program for editing.
$editId = filter_input(
    INPUT_GET,
    'edit',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM live_programs WHERE id = ?');
    $stmt->execute([$editId]);
    $editingProgram = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$editingProgram) {
        $error = $t['edit_not_found'];
    }
}

// Load all programs.
$stmt = $pdo->query(
    "SELECT * FROM live_programs
     ORDER BY
        CASE stream_status
            WHEN 'live' THEN 1
            WHEN 'scheduled' THEN 2
            WHEN 'ended' THEN 3
        END,
        program_date ASC"
);

$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare form values.
$formProgram = $editingProgram ?: [
    'id' => '',
    'title_en' => '',
    'title_am' => '',
    'description_en' => '',
    'description_am' => '',
    'program_date' => '',
    'youtube_url' => '',
    'stream_status' => 'scheduled',
    'is_published' => 1
];

$formDate = '';

if (!empty($formProgram['program_date'])) {
    $formDate = date('Y-m-d\TH:i', strtotime($formProgram['program_date']));
}

// Preserve language in page links.
$langQuery = '?lang=' . urlencode($lang);
?>

<!DOCTYPE html>
<html lang="<?= liveEscape($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= liveEscape($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/live-programs.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<main class="live-admin-container">

    <section class="live-page-header">
        <div>
            <span class="live-eyebrow"><?= liveEscape($t['eyebrow']) ?></span>
            <h1><?= liveEscape($t['page_title']) ?></h1>
            <p><?= liveEscape($t['page_description']) ?></p>
        </div>

        <a href="dashboard.php<?= $langQuery ?>" class="live-back-button">
            <?= liveEscape($t['back_dashboard']) ?>
        </a>
    </section>

    <?php if ($message !== ''): ?>
        <div class="live-notice live-success">
            <?= liveEscape($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="live-notice live-error">
            <?= liveEscape($error) ?>
        </div>
    <?php endif; ?>

    <section class="live-admin-panel">

        <div class="live-panel-heading">
            <div>
                <span class="live-section-label">
                    <?= liveEscape($t['program_details']) ?>
                </span>

                <h2>
                    <?= liveEscape($editingProgram ? $t['edit_program'] : $t['add_program']) ?>
                </h2>
            </div>
        </div>

        <form method="POST" action="live-programs.php?lang=<?= liveEscape($lang) ?>">

            <input type="hidden" name="lang" value="<?= liveEscape($lang) ?>">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= liveEscape($_SESSION['live_programs_csrf']) ?>"
            >

            <input type="hidden" name="action" value="save">

            <input
                type="hidden"
                name="id"
                value="<?= liveEscape($formProgram['id']) ?>"
            >

            <div class="live-form-grid">

                <div class="live-field">
                    <label for="title_en">
                        <?= liveEscape($t['title_en']) ?> <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="title_en"
                        name="title_en"
                        maxlength="255"
                        required
                        value="<?= liveEscape($formProgram['title_en']) ?>"
                        placeholder="Example: Sunday Worship Live"
                    >
                </div>

                <div class="live-field">
                    <label for="title_am">
                        <?= liveEscape($t['title_am']) ?> <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="title_am"
                        name="title_am"
                        maxlength="255"
                        required
                        value="<?= liveEscape($formProgram['title_am']) ?>"
                        placeholder="የፕሮግራሙ ርዕስ"
                    >
                </div>

                <div class="live-field">
                    <label for="program_date">
                        <?= liveEscape($t['program_date']) ?> <span>*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="program_date"
                        name="program_date"
                        required
                        value="<?= liveEscape($formDate) ?>"
                    >
                </div>

                <div class="live-field">
                    <label for="stream_status">
                        <?= liveEscape($t['stream_status']) ?>
                    </label>

                    <select id="stream_status" name="stream_status" required>
                        <option value="scheduled" <?= $formProgram['stream_status'] === 'scheduled' ? 'selected' : '' ?>>
                            <?= liveEscape($t['scheduled']) ?>
                        </option>

                        <option value="live" <?= $formProgram['stream_status'] === 'live' ? 'selected' : '' ?>>
                            <?= liveEscape($t['live']) ?>
                        </option>

                        <option value="ended" <?= $formProgram['stream_status'] === 'ended' ? 'selected' : '' ?>>
                            <?= liveEscape($t['ended']) ?>
                        </option>
                    </select>
                </div>

                <div class="live-field live-full-width">
                    <label for="youtube_url">
                        <?= liveEscape($t['youtube_url']) ?>
                    </label>

                    <input
                        type="url"
                        id="youtube_url"
                        name="youtube_url"
                        maxlength="500"
                        value="<?= liveEscape($formProgram['youtube_url']) ?>"
                        placeholder="https://www.youtube.com/watch?v=..."
                    >

                    <small><?= liveEscape($t['youtube_help']) ?></small>
                </div>

                <div class="live-field live-full-width">
                    <label for="description_en">
                        <?= liveEscape($t['description_en']) ?>
                    </label>

                    <textarea
                        id="description_en"
                        name="description_en"
                        rows="4"
                        placeholder="<?= liveEscape($t['description_en_placeholder']) ?>"
                    ><?= liveEscape($formProgram['description_en']) ?></textarea>
                </div>

                <div class="live-field live-full-width">
                    <label for="description_am">
                        <?= liveEscape($t['description_am']) ?>
                    </label>

                    <textarea
                        id="description_am"
                        name="description_am"
                        rows="4"
                        placeholder="<?= liveEscape($t['description_am_placeholder']) ?>"
                    ><?= liveEscape($formProgram['description_am']) ?></textarea>
                </div>

                <div class="live-field live-full-width">
                    <label class="live-publish-option">
                        <input
                            type="checkbox"
                            name="is_published"
                            value="1"
                            <?= !empty($formProgram['is_published']) ? 'checked' : '' ?>
                        >

                        <span><?= liveEscape($t['publish']) ?></span>
                    </label>
                </div>

                <div class="live-form-actions live-full-width">

                    <button type="submit" class="live-primary-button">
                        <?= liveEscape($editingProgram ? $t['update_program'] : $t['save_program']) ?>
                    </button>

                    <?php if ($editingProgram): ?>
                        <a
                            href="live-programs.php<?= $langQuery ?>"
                            class="live-secondary-button"
                        >
                            <?= liveEscape($t['cancel_edit']) ?>
                        </a>
                    <?php endif; ?>

                </div>

            </div>
        </form>
    </section>

    <section class="live-admin-panel">

        <div class="live-panel-heading">
            <div>
                <span class="live-section-label">
                    <?= liveEscape($t['management']) ?>
                </span>

                <h2><?= liveEscape($t['existing_programs']) ?></h2>
            </div>

            <span class="live-program-count">
                <?= count($programs) ?> <?= liveEscape($t['programs']) ?>
            </span>
        </div>

        <div class="live-table-wrap">

            <table class="live-program-table">

                <thead>
                    <tr>
                        <th><?= liveEscape($t['program']) ?></th>
                        <th><?= liveEscape($t['date_time']) ?></th>
                        <th><?= liveEscape($t['status']) ?></th>
                        <th><?= liveEscape($t['visibility']) ?></th>
                        <th><?= liveEscape($t['actions']) ?></th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($programs)): ?>

                    <tr>
                        <td colspan="5" class="live-empty">
                            <?= liveEscape($t['no_programs']) ?>
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($programs as $program): ?>

                        <tr>
                            <td>
                                <strong class="live-program-title">
                                    <?= liveEscape($program['title_en']) ?>
                                </strong>

                                <span class="live-amharic-title">
                                    <?= liveEscape($program['title_am']) ?>
                                </span>

                                <?php if (!empty($program['youtube_url'])): ?>
                                    <a
                                        class="live-youtube-link"
                                        href="<?= liveEscape($program['youtube_url']) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <?= liveEscape($t['view_youtube']) ?>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= liveEscape(date('M d, Y', strtotime($program['program_date']))) ?>

                                <span class="live-time">
                                    <?= liveEscape(date('h:i A', strtotime($program['program_date']))) ?>
                                </span>
                            </td>

                            <td>
                                <span class="live-status status-<?= liveEscape($program['stream_status']) ?>">
                                    <?= liveEscape($t[$program['stream_status']] ?? $program['stream_status']) ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($program['is_published']): ?>
                                    <span class="live-visibility published">
                                        <?= liveEscape($t['published']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="live-visibility hidden">
                                        <?= liveEscape($t['hidden']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="live-row-actions">

                                    <a
                                        href="live-programs.php?edit=<?= (int)$program['id'] ?>&amp;lang=<?= liveEscape($lang) ?>"
                                        class="live-edit-button"
                                    >
                                        <?= liveEscape($t['edit']) ?>
                                    </a>

                                    <form
                                        method="POST"
                                        action="live-programs.php?lang=<?= liveEscape($lang) ?>"
                                        class="live-delete-form"
                                        onsubmit="return confirm(<?= htmlspecialchars(json_encode($t['confirm_delete']), ENT_QUOTES, 'UTF-8') ?>);"
                                    >

                                        <input type="hidden" name="lang" value="<?= liveEscape($lang) ?>">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= liveEscape($_SESSION['live_programs_csrf']) ?>"
                                        >

                                        <input type="hidden" name="action" value="delete">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$program['id'] ?>"
                                        >

                                        <button type="submit" class="live-delete-button">
                                            <?= liveEscape($t['delete']) ?>
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </section>

</main>

<footer class="live-admin-footer">
    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= liveEscape($t['footer_rights']) ?>
    </p>

    <p><?= liveEscape($t['footer_panel']) ?></p>
</footer>

</body>
</html>
