
<?php
session_start();

// Show errors during local development.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

// Only logged-in administrators may manage events.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Language selection.
$lang = ($_GET['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Create a CSRF token.
$_SESSION['events_csrf'] ??= bin2hex(random_bytes(32));

function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// English and Amharic translations.
$translations = [
    'en' => [
        'page_title' => 'Manage Events',
        'heading' => 'Manage Church Events',
        'subtitle' => 'Add, edit, and manage church events.',
        'back_dashboard' => 'Back to Dashboard',
        'edit_event' => 'Edit Event',
        'add_event' => 'Add New Event',
        'title_en' => 'Event Title (English)',
        'title_am' => 'Event Title (Amharic)',
        'event_date' => 'Event Date and Time',
        'location' => 'Location',
        'description_en' => 'Description (English)',
        'description_am' => 'Description (Amharic)',
        'update_event' => 'Update Event',
        'save_event' => 'Save Event',
        'cancel_edit' => 'Cancel Edit',
        'existing_events' => 'Existing Events',
        'title' => 'Title',
        'date_time' => 'Date and Time',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_events' => 'No events have been added yet.',
        'copyright' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel',

        'security_error' => 'Security validation failed. Please refresh the page and try again.',
        'invalid_id' => 'Invalid event ID.',
        'deleted' => 'Event deleted successfully.',
        'not_found' => 'Event not found.',
        'required_fields' => 'English title, Amharic title, and event date are required.',
        'title_length' => 'Event titles must not exceed 255 characters.',
        'location_length' => 'Location must not exceed 255 characters.',
        'invalid_date' => 'Please enter a valid event date and time.',
        'updated' => 'Event updated successfully.',
        'added' => 'Event added successfully.',
        'invalid_action' => 'Invalid form action.',
        'database_error' => 'A database error occurred. Please try again.',
        'selected_not_found' => 'The event you selected could not be found.'
    ],

    'am' => [
        'page_title' => 'መርሃ ግብሮችን ያስተዳድሩ',
        'heading' => 'የቤተ ክርስቲያን መርሃ ግብሮችን ያስተዳድሩ',
        'subtitle' => 'የቤተ ክርስቲያን መርሃ ግብሮችን ይጨምሩ፣ ያርትዑ እና ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'edit_event' => 'መርሃ ግብር አርትዕ',
        'add_event' => 'አዲስ መርሃ ግብር ጨምር',
        'title_en' => 'የመርሃ ግብር ርዕስ (እንግሊዝኛ)',
        'title_am' => 'የመርሃ ግብር ርዕስ (አማርኛ)',
        'event_date' => 'የመርሃ ግብሩ ቀን እና ሰዓት',
        'location' => 'ቦታ',
        'description_en' => 'መግለጫ (እንግሊዝኛ)',
        'description_am' => 'መግለጫ (አማርኛ)',
        'update_event' => 'መርሃ ግብሩን አዘምን',
        'save_event' => 'መርሃ ግብሩን አስቀምጥ',
        'cancel_edit' => 'አርትዖትን ሰርዝ',
        'existing_events' => 'ያሉ መርሃ ግብሮች',
        'title' => 'ርዕስ',
        'date_time' => 'ቀን እና ሰዓት',
        'actions' => 'ተግባሮች',
        'edit' => 'አርትዕ',
        'delete' => 'ሰርዝ',
        'no_events' => 'እስካሁን ምንም መርሃ ግብር አልተጨመረም።',
        'copyright' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',

        'security_error' => 'የደህንነት ማረጋገጫው አልተሳካም። እባክዎ ገጹን እንደገና ይክፈቱ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የመርሃ ግብር መለያ ቁጥር።',
        'deleted' => 'መርሃ ግብሩ በተሳካ ሁኔታ ተሰርዟል።',
        'not_found' => 'መርሃ ግብሩ አልተገኘም።',
        'required_fields' => 'የእንግሊዝኛ ርዕስ፣ የአማርኛ ርዕስ እና የመርሃ ግብሩ ቀን ያስፈልጋሉ።',
        'title_length' => 'የመርሃ ግብር ርዕሶች ከ255 ቁምፊዎች መብለጥ የለባቸውም።',
        'location_length' => 'የቦታው ስም ከ255 ቁምፊዎች መብለጥ የለበትም።',
        'invalid_date' => 'እባክዎ ትክክለኛ የመርሃ ግብር ቀን እና ሰዓት ያስገቡ።',
        'updated' => 'መርሃ ግብሩ በተሳካ ሁኔታ ታድሷል።',
        'added' => 'አዲስ መርሃ ግብር በተሳካ ሁኔታ ተጨምሯል።',
        'invalid_action' => 'ትክክለኛ ያልሆነ የቅጽ ትዕዛዝ።',
        'database_error' => 'የዳታቤዝ ስህተት ተፈጥሯል። እባክዎ እንደገና ይሞክሩ።',
        'selected_not_found' => 'የመረጡት መርሃ ግብር ሊገኝ አልቻለም።'
    ]
];

$t = $translations[$lang];

// Handle form submissions.
$message = '';
$error = '';
$editingEvent = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['events_csrf'], $token)
    ) {
        $error = 'security_error';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'delete') {
                $id = filter_var(
                    $_POST['id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (!$id) {
                    throw new RuntimeException('invalid_id');
                }

                $stmt = $pdo->prepare(
                    'DELETE FROM events WHERE id = ?'
                );
                $stmt->execute([$id]);

                $message = $stmt->rowCount()
                    ? 'deleted'
                    : 'not_found';

            } elseif ($action === 'save') {
                $id = filter_var(
                    $_POST['id'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                $titleEn = trim($_POST['title_en'] ?? '');
                $titleAm = trim($_POST['title_am'] ?? '');
                $descriptionEn = trim($_POST['description_en'] ?? '');
                $descriptionAm = trim($_POST['description_am'] ?? '');
                $eventDate = trim($_POST['event_date'] ?? '');
                $location = trim($_POST['location'] ?? '');

                if (
                    $titleEn === '' ||
                    $titleAm === '' ||
                    $eventDate === ''
                ) {
                    throw new RuntimeException('required_fields');
                }

                if (
                    mb_strlen($titleEn) > 255 ||
                    mb_strlen($titleAm) > 255
                ) {
                    throw new RuntimeException('title_length');
                }

                if (mb_strlen($location) > 255) {
                    throw new RuntimeException('location_length');
                }

                $date = DateTime::createFromFormat(
                    '!Y-m-d\TH:i',
                    $eventDate
                );

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
                    $date->format('Y-m-d\TH:i') !== $eventDate
                ) {
                    throw new RuntimeException('invalid_date');
                }

                $databaseDate = $date->format('Y-m-d H:i:s');

                if ($id) {
                    $stmt = $pdo->prepare(
                        'UPDATE events
                         SET title_en = ?, title_am = ?,
                             description_en = ?, description_am = ?,
                             event_date = ?, location = ?
                         WHERE id = ?'
                    );

                    $stmt->execute([
                        $titleEn,
                        $titleAm,
                        $descriptionEn !== '' ? $descriptionEn : null,
                        $descriptionAm !== '' ? $descriptionAm : null,
                        $databaseDate,
                        $location !== '' ? $location : null,
                        $id
                    ]);

                    $message = 'updated';

                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO events
                         (title_en, title_am, description_en,
                          description_am, event_date, location)
                         VALUES (?, ?, ?, ?, ?, ?)'
                    );

                    $stmt->execute([
                        $titleEn,
                        $titleAm,
                        $descriptionEn !== '' ? $descriptionEn : null,
                        $descriptionAm !== '' ? $descriptionAm : null,
                        $databaseDate,
                        $location !== '' ? $location : null
                    ]);

                    $message = 'added';
                }

            } else {
                throw new RuntimeException('invalid_action');
            }

        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $error = 'database_error';

        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
}

// Redirect after successful changes.
if ($message !== '') {
    $_SESSION['events_flash'] = $message;
    header('Location: events.php?lang=' . urlencode($lang));
    exit;
}

// Retrieve success message after redirect.
if (isset($_SESSION['events_flash'])) {
    $message = $_SESSION['events_flash'];
    unset($_SESSION['events_flash']);
}

// Load an event for editing.
$editId = filter_input(
    INPUT_GET,
    'edit',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($editId) {
    $stmt = $pdo->prepare(
        'SELECT * FROM events WHERE id = ?'
    );
    $stmt->execute([$editId]);
    $editingEvent = $stmt->fetch();

    if (!$editingEvent) {
        $error = 'selected_not_found';
    }
}

// Load all events, with upcoming events first.
$stmt = $pdo->query(
    'SELECT * FROM events
     ORDER BY (event_date >= NOW()) DESC, event_date ASC'
);
$events = $stmt->fetchAll();

$formEvent = $editingEvent ?: [
    'id' => '',
    'title_en' => '',
    'title_am' => '',
    'description_en' => '',
    'description_am' => '',
    'event_date' => '',
    'location' => ''
];

$formDate = '';

if (!empty($formEvent['event_date'])) {
    $formDate = date(
        'Y-m-d\TH:i',
        strtotime($formEvent['event_date'])
    );
}

// Translate messages.
$message = $t[$message] ?? $message;
$error = $t[$error] ?? $error;
?>

<!DOCTYPE html>
<html lang="<?= e($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-events.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
    <script src="../assets/js/admin-events.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<!-- MAIN CONTENT -->
<main class="admin-container">

    <section class="page-header">

        <div>
            <h1><?= e($t['heading']) ?></h1>
            <p><?= e($t['subtitle']) ?></p>
        </div>

        <a href="dashboard.php?lang=<?= e($lang) ?>" class="back-button">
            <?= e($t['back_dashboard']) ?>
        </a>

    </section>

    <?php if ($message !== ''): ?>
        <div class="notice success">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="notice failure">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- ADD OR EDIT EVENT FORM -->
    <section class="panel">

        <h2>
            <?= $editingEvent ? e($t['edit_event']) : e($t['add_event']) ?>
        </h2>

        <form method="POST" action="events.php?lang=<?= e($lang) ?>">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['events_csrf']) ?>"
            >

            <input type="hidden" name="action" value="save">

            <input
                type="hidden"
                name="id"
                value="<?= e($formEvent['id']) ?>"
            >

            <div class="form-grid">

                <div class="field">
                    <label for="title_en">
                        <?= e($t['title_en']) ?>
                    </label>

                    <input
                        type="text"
                        id="title_en"
                        name="title_en"
                        maxlength="255"
                        required
                        value="<?= e($formEvent['title_en']) ?>"
                    >
                </div>

                <div class="field">
                    <label for="title_am">
                        <?= e($t['title_am']) ?>
                    </label>

                    <input
                        type="text"
                        id="title_am"
                        name="title_am"
                        maxlength="255"
                        required
                        value="<?= e($formEvent['title_am']) ?>"
                    >
                </div>

                <div class="field">
                    <label for="event_date">
                        <?= e($t['event_date']) ?>
                    </label>

                    <input
                        type="datetime-local"
                        id="event_date"
                        name="event_date"
                        required
                        value="<?= e($formDate) ?>"
                    >
                </div>

                <div class="field">
                    <label for="location">
                        <?= e($t['location']) ?>
                    </label>

                    <input
                        type="text"
                        id="location"
                        name="location"
                        maxlength="255"
                        value="<?= e($formEvent['location']) ?>"
                    >
                </div>

                <div class="field full-width">
                    <label for="description_en">
                        <?= e($t['description_en']) ?>
                    </label>

                    <textarea
                        id="description_en"
                        name="description_en"
                    ><?= e($formEvent['description_en']) ?></textarea>
                </div>

                <div class="field full-width">
                    <label for="description_am">
                        <?= e($t['description_am']) ?>
                    </label>

                    <textarea
                        id="description_am"
                        name="description_am"
                    ><?= e($formEvent['description_am']) ?></textarea>
                </div>

                <div class="field full-width actions">

                    <button type="submit" class="primary">
                        <?= $editingEvent ? e($t['update_event']) : e($t['save_event']) ?>
                    </button>

                    <?php if ($editingEvent): ?>
                        <a
                            class="button secondary"
                            href="events.php?lang=<?= e($lang) ?>"
                        >
                            <?= e($t['cancel_edit']) ?>
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        </form>

    </section>

    <!-- EXISTING EVENTS TABLE -->
    <section class="panel">

        <h2><?= e($t['existing_events']) ?></h2>

        <div class="table-wrap">

            <table>

                <thead>
                    <tr>
                        <th><?= e($t['title']) ?></th>
                        <th><?= e($t['date_time']) ?></th>
                        <th><?= e($t['location']) ?></th>
                        <th><?= e($t['actions']) ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($events)): ?>

                        <tr>
                            <td colspan="4">
                                <?= e($t['no_events']) ?>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($events as $event): ?>

                            <?php
                            $eventTitle = $lang === 'am'
                                ? $event['title_am']
                                : $event['title_en'];

                            $eventDescription = $lang === 'am'
                                ? $event['description_am']
                                : $event['description_en'];
                            ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= e($eventTitle) ?>
                                    </strong>

                                    <p class="subtext">
                                        <?= e($eventDescription) ?>
                                    </p>
                                </td>

                                <td>
                                    <?= e($event['event_date']) ?>
                                </td>

                                <td>
                                    <?= e($event['location']) ?>
                                </td>

                                <td>

                                    <div class="actions">

                                        <a
                                            class="button secondary"
                                            href="events.php?edit=<?= (int)$event['id'] ?>&lang=<?= e($lang) ?>"
                                        >
                                            <?= e($t['edit']) ?>
                                        </a>

                                        <form
                                            method="POST"
                                            action="events.php?lang=<?= e($lang) ?>"
                                            class="inline-form confirm-delete"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e($_SESSION['events_csrf']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$event['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="danger"
                                            >
                                                <?= e($t['delete']) ?>
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

<!-- ADMIN FOOTER -->
<footer class="admin-footer">

    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= e($t['copyright']) ?>
    </p>

    <p><?= e($t['admin_panel']) ?></p>

</footer>

</body>
</html>
