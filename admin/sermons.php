
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Show PHP errors during local development.
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Only logged-in administrators can manage sermons.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Language selection.
$lang = ($_GET['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Create a CSRF token.
if (
    !isset($_SESSION['sermons_csrf']) ||
    !is_string($_SESSION['sermons_csrf']) ||
    $_SESSION['sermons_csrf'] === ''
) {
    $_SESSION['sermons_csrf'] = bin2hex(random_bytes(32));
}

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function validYouTubeUrl(string $url): bool
{
    if ($url === '') {
        return true;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $host = strtolower((string) parse_url($url, PHP_URL_HOST));

    return in_array($host, [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtu.be',
        'www.youtu.be'
    ], true);
}

// English and Amharic translations.
$translations = [
    'en' => [
        'page_title' => 'Sermon Management',
        'administration' => 'Administration Panel',
        'heading' => 'Sermon Management',
        'subtitle' => 'Add, edit, and manage church sermons.',
        'back_dashboard' => 'Back to Dashboard',
        'edit_sermon' => 'Edit Sermon',
        'add_sermon' => 'Add New Sermon',
        'title_en' => 'Sermon Title (English)',
        'title_am' => 'Sermon Title (Amharic)',
        'preacher' => 'Preacher Name',
        'description_en' => 'Description (English)',
        'description_am' => 'Description (Amharic)',
        'video_url' => 'YouTube Video URL',
        'sermon_date' => 'Sermon Date',
        'title_en_placeholder' => 'Enter sermon title in English',
        'title_am_placeholder' => 'Enter sermon title in Amharic',
        'preacher_placeholder' => 'Enter preacher name',
        'description_en_placeholder' => 'Enter sermon description in English',
        'description_am_placeholder' => 'Enter sermon description in Amharic',
        'video_placeholder' => 'https://www.youtube.com/watch?v=...',
        'update_sermon' => 'Update Sermon',
        'save_sermon' => 'Add Sermon',
        'cancel' => 'Cancel Editing',
        'existing_sermons' => 'Existing Sermons',
        'total_sermons' => 'Total sermons:',
        'id' => 'ID',
        'title' => 'Title',
        'preacher_column' => 'Preacher',
        'date' => 'Date',
        'video' => 'Video',
        'actions' => 'Actions',
        'watch_video' => 'Watch Video',
        'no_video' => 'No video',
        'not_specified' => 'Not specified',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'empty' => 'No sermons have been added yet.',
        'confirm_delete' => 'Are you sure you want to delete this sermon?',
        'copyright' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel',

        'security_error' => 'Security check failed. Refresh the page and try again.',
        'invalid_id' => 'Invalid sermon ID.',
        'required_titles' => 'Enter the sermon title in both English and Amharic.',
        'title_length' => 'Each sermon title must be 255 characters or fewer.',
        'preacher_length' => 'The preacher name must be 150 characters or fewer.',
        'invalid_youtube' => 'Enter a valid YouTube video URL, or leave it empty.',
        'invalid_date' => 'Enter a valid sermon date.',
        'unknown_action' => 'Unknown action.',
        'database_error' => 'The sermon could not be saved. Check the database table and try again.',
        'not_found' => 'Sermon not found.',
        'added_success' => 'Sermon added successfully.',
        'updated_success' => 'Sermon updated successfully.',
        'deleted_success' => 'Sermon deleted successfully.'
    ],

    'am' => [
        'page_title' => 'የስብከት አስተዳደር',
        'administration' => 'የአስተዳደር ፓነል',
        'heading' => 'የስብከት አስተዳደር',
        'subtitle' => 'የቤተ ክርስቲያን ስብከቶችን ይጨምሩ፣ ያርትዑ እና ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'edit_sermon' => 'ስብከት አርትዕ',
        'add_sermon' => 'አዲስ ስብከት ጨምር',
        'title_en' => 'የስብከት ርዕስ (እንግሊዝኛ)',
        'title_am' => 'የስብከት ርዕስ (አማርኛ)',
        'preacher' => 'የሰባኪው ስም',
        'description_en' => 'መግለጫ (እንግሊዝኛ)',
        'description_am' => 'መግለጫ (አማርኛ)',
        'video_url' => 'የዩቲዩብ ቪዲዮ ሊንክ',
        'sermon_date' => 'የስብከት ቀን',
        'title_en_placeholder' => 'የስብከቱን ርዕስ በእንግሊዝኛ ያስገቡ',
        'title_am_placeholder' => 'የስብከቱን ርዕስ በአማርኛ ያስገቡ',
        'preacher_placeholder' => 'የሰባኪውን ስም ያስገቡ',
        'description_en_placeholder' => 'የስብከቱን መግለጫ በእንግሊዝኛ ያስገቡ',
        'description_am_placeholder' => 'የስብከቱን መግለጫ በአማርኛ ያስገቡ',
        'video_placeholder' => 'https://www.youtube.com/watch?v=...',
        'update_sermon' => 'ስብከቱን አዘምን',
        'save_sermon' => 'ስብከት ጨምር',
        'cancel' => 'አርትዖትን ሰርዝ',
        'existing_sermons' => 'ያሉ ስብከቶች',
        'total_sermons' => 'ጠቅላላ ስብከቶች፦',
        'id' => 'ቁጥር',
        'title' => 'ርዕስ',
        'preacher_column' => 'ሰባኪ',
        'date' => 'ቀን',
        'video' => 'ቪዲዮ',
        'actions' => 'ተግባሮች',
        'watch_video' => 'ቪዲዮውን ይመልከቱ',
        'no_video' => 'ቪዲዮ የለም',
        'not_specified' => 'አልተገለጸም',
        'edit' => 'አርትዕ',
        'delete' => 'ሰርዝ',
        'empty' => 'እስካሁን ምንም ስብከት አልተጨመረም።',
        'confirm_delete' => 'ይህን ስብከት ለመሰረዝ እርግጠኛ ነዎት?',
        'copyright' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',

        'security_error' => 'የደህንነት ማረጋገጫው አልተሳካም። ገጹን እንደገና ይክፈቱ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የስብከት መለያ ቁጥር።',
        'required_titles' => 'የስብከቱን ርዕስ በእንግሊዝኛም በአማርኛም ያስገቡ።',
        'title_length' => 'እያንዳንዱ የስብከት ርዕስ ከ255 ቁምፊዎች መብለጥ የለበትም።',
        'preacher_length' => 'የሰባኪው ስም ከ150 ቁምፊዎች መብለጥ የለበትም።',
        'invalid_youtube' => 'ትክክለኛ የዩቲዩብ ቪዲዮ ሊንክ ያስገቡ ወይም ባዶ ይተዉት።',
        'invalid_date' => 'ትክክለኛ የስብከት ቀን ያስገቡ።',
        'unknown_action' => 'ያልታወቀ የቅጽ ትዕዛዝ።',
        'database_error' => 'ስብከቱን ማስቀመጥ አልተቻለም። የዳታቤዝ ሰንጠረዡን ያረጋግጡና እንደገና ይሞክሩ።',
        'not_found' => 'ስብከቱ አልተገኘም።',
        'added_success' => 'ስብከቱ በተሳካ ሁኔታ ተጨምሯል።',
        'updated_success' => 'ስብከቱ በተሳካ ሁኔታ ታድሷል።',
        'deleted_success' => 'ስብከቱ በተሳካ ሁኔታ ተሰርዟል።'
    ]
];

$t = $translations[$lang];

$error = '';
$success = '';
$editing = false;

$sermon = [
    'id' => '',
    'title_en' => '',
    'title_am' => '',
    'preacher' => '',
    'description_en' => '',
    'description_am' => '',
    'video_url' => '',
    'sermon_date' => ''
];

// Handle add, update, and delete requests.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['sermons_csrf'], $token)
    ) {
        $error = 'security_error';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'delete') {
                $id = filter_var(
                    $_POST['id'] ?? '',
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (!$id) {
                    throw new RuntimeException('invalid_id');
                }

                $stmt = $pdo->prepare(
                    'DELETE FROM sermons WHERE id = ?'
                );
                $stmt->execute([$id]);

                header('Location: sermons.php?status=deleted&lang=' . urlencode($lang));
                exit;
            }

            if ($action === 'create' || $action === 'update') {
                $editing = ($action === 'update');

                $sermon = [
                    'id' => $_POST['id'] ?? '',
                    'title_en' => trim($_POST['title_en'] ?? ''),
                    'title_am' => trim($_POST['title_am'] ?? ''),
                    'preacher' => trim($_POST['preacher'] ?? ''),
                    'description_en' => trim($_POST['description_en'] ?? ''),
                    'description_am' => trim($_POST['description_am'] ?? ''),
                    'video_url' => trim($_POST['video_url'] ?? ''),
                    'sermon_date' => trim($_POST['sermon_date'] ?? '')
                ];

                if (
                    $sermon['title_en'] === '' ||
                    $sermon['title_am'] === ''
                ) {
                    throw new RuntimeException('required_titles');
                }

                if (
                    mb_strlen($sermon['title_en']) > 255 ||
                    mb_strlen($sermon['title_am']) > 255
                ) {
                    throw new RuntimeException('title_length');
                }

                if (mb_strlen($sermon['preacher']) > 150) {
                    throw new RuntimeException('preacher_length');
                }

                if (!validYouTubeUrl($sermon['video_url'])) {
                    throw new RuntimeException('invalid_youtube');
                }

                $date = $sermon['sermon_date'];

                if ($date !== '') {
                    $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);

                    if (
                        !$parsedDate ||
                        $parsedDate->format('Y-m-d') !== $date
                    ) {
                        throw new RuntimeException('invalid_date');
                    }
                }

                if ($action === 'create') {
                    $stmt = $pdo->prepare(
                        'INSERT INTO sermons
                        (
                            title_en,
                            title_am,
                            preacher,
                            description_en,
                            description_am,
                            video_url,
                            sermon_date
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?)'
                    );

                    $stmt->execute([
                        $sermon['title_en'],
                        $sermon['title_am'],
                        $sermon['preacher'] ?: null,
                        $sermon['description_en'] ?: null,
                        $sermon['description_am'] ?: null,
                        $sermon['video_url'] ?: null,
                        $date ?: null
                    ]);

                    header('Location: sermons.php?status=created&lang=' . urlencode($lang));
                    exit;
                }

                $id = filter_var(
                    $sermon['id'],
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                if (!$id) {
                    throw new RuntimeException('invalid_id');
                }

                $stmt = $pdo->prepare(
                    'UPDATE sermons SET
                        title_en = ?,
                        title_am = ?,
                        preacher = ?,
                        description_en = ?,
                        description_am = ?,
                        video_url = ?,
                        sermon_date = ?
                    WHERE id = ?'
                );

                $stmt->execute([
                    $sermon['title_en'],
                    $sermon['title_am'],
                    $sermon['preacher'] ?: null,
                    $sermon['description_en'] ?: null,
                    $sermon['description_am'] ?: null,
                    $sermon['video_url'] ?: null,
                    $date ?: null,
                    $id
                ]);

                header('Location: sermons.php?status=updated&lang=' . urlencode($lang));
                exit;
            }

            throw new RuntimeException('unknown_action');

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'database_error';

        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }
}

// Load a sermon into the form when Edit is clicked.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['edit'])) {
    $id = filter_var(
        $_GET['edit'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM sermons WHERE id = ?');
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($record) {
            $sermon = array_merge($sermon, $record);
            $editing = true;
        } else {
            $error = 'not_found';
        }
    } else {
        $error = 'invalid_id';
    }
}

// Display feedback after a successful action.
$statusMessages = [
    'created' => 'added_success',
    'updated' => 'updated_success',
    'deleted' => 'deleted_success'
];

if (
    isset($_GET['status']) &&
    isset($statusMessages[$_GET['status']])
) {
    $success = $t[$statusMessages[$_GET['status']]];
}

// Translate validation errors.
$error = $t[$error] ?? $error;

// Fetch sermons for the management table.
$stmt = $pdo->query(
    'SELECT id, title_en, title_am, preacher, video_url, sermon_date
     FROM sermons
     ORDER BY sermon_date DESC, id DESC'
);

$sermons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-sermons.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<!-- SERMON MANAGEMENT CONTENT -->
<div class="admin-container">

    <!-- PAGE HEADER -->
    <header class="page-header">

        <div>
            <h1><?= h($t['heading']) ?></h1>
            <p><?= h($t['subtitle']) ?></p>
        </div>

        <div class="header-actions">
            <a href="dashboard.php?lang=<?= h($lang) ?>" class="back-button">
                <?= h($t['back_dashboard']) ?>
            </a>
        </div>

    </header>

    <!-- FEEDBACK MESSAGES -->
    <?php if ($error !== ''): ?>
        <div class="alert alert-error" role="alert">
            <?= h($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success" role="status">
            <?= h($success) ?>
        </div>
    <?php endif; ?>

    <!-- SERMON FORM -->
    <section class="sermon-form-section">

        <h2>
            <?= h($editing ? $t['edit_sermon'] : $t['add_sermon']) ?>
        </h2>

        <form
            action="sermons.php?lang=<?= h($lang) ?>"
            method="POST"
            class="sermon-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= h($_SESSION['sermons_csrf']) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="<?= $editing ? 'update' : 'create' ?>"
            >

            <?php if ($editing): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= h($sermon['id']) ?>"
                >
            <?php endif; ?>

            <div class="form-group">

                <label for="title_en">
                    <?= h($t['title_en']) ?> *
                </label>

                <input
                    type="text"
                    id="title_en"
                    name="title_en"
                    maxlength="255"
                    value="<?= h($sermon['title_en']) ?>"
                    placeholder="<?= h($t['title_en_placeholder']) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="title_am">
                    <?= h($t['title_am']) ?> *
                </label>

                <input
                    type="text"
                    id="title_am"
                    name="title_am"
                    maxlength="255"
                    value="<?= h($sermon['title_am']) ?>"
                    placeholder="<?= h($t['title_am_placeholder']) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="preacher">
                    <?= h($t['preacher']) ?>
                </label>

                <input
                    type="text"
                    id="preacher"
                    name="preacher"
                    maxlength="150"
                    value="<?= h($sermon['preacher']) ?>"
                    placeholder="<?= h($t['preacher_placeholder']) ?>"
                >

            </div>

            <div class="form-group">

                <label for="description_en">
                    <?= h($t['description_en']) ?>
                </label>

                <textarea
                    id="description_en"
                    name="description_en"
                    rows="4"
                    placeholder="<?= h($t['description_en_placeholder']) ?>"
                ><?= h($sermon['description_en']) ?></textarea>

            </div>

            <div class="form-group">

                <label for="description_am">
                    <?= h($t['description_am']) ?>
                </label>

                <textarea
                    id="description_am"
                    name="description_am"
                    rows="4"
                    placeholder="<?= h($t['description_am_placeholder']) ?>"
                ><?= h($sermon['description_am']) ?></textarea>

            </div>

            <div class="form-group">

                <label for="video_url">
                    <?= h($t['video_url']) ?>
                </label>

                <input
                    type="url"
                    id="video_url"
                    name="video_url"
                    value="<?= h($sermon['video_url']) ?>"
                    placeholder="<?= h($t['video_placeholder']) ?>"
                >

            </div>

            <div class="form-group">

                <label for="sermon_date">
                    <?= h($t['sermon_date']) ?>
                </label>

                <input
                    type="date"
                    id="sermon_date"
                    name="sermon_date"
                    value="<?= h($sermon['sermon_date']) ?>"
                >

            </div>

            <div class="form-actions">

                <button type="submit" class="submit-button">
                    <?= h($editing ? $t['update_sermon'] : $t['save_sermon']) ?>
                </button>

                <?php if ($editing): ?>
                    <a
                        href="sermons.php?lang=<?= h($lang) ?>"
                        class="cancel-button"
                    >
                        <?= h($t['cancel']) ?>
                    </a>
                <?php endif; ?>

            </div>

        </form>

    </section>

    <!-- EXISTING SERMONS -->
    <section class="sermon-list-section">

        <h2><?= h($t['existing_sermons']) ?></h2>

        <p class="sermon-count">
            <?= h($t['total_sermons']) ?> <?= count($sermons) ?>
        </p>

        <?php if (count($sermons) > 0): ?>

            <div class="table-wrapper">

                <table class="sermon-table">

                    <thead>
                        <tr>
                            <th><?= h($t['id']) ?></th>
                            <th><?= h($t['title']) ?> (EN)</th>
                            <th><?= h($t['title']) ?> (አማ)</th>
                            <th><?= h($t['preacher_column']) ?></th>
                            <th><?= h($t['date']) ?></th>
                            <th><?= h($t['video']) ?></th>
                            <th><?= h($t['actions']) ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($sermons as $row): ?>

                            <tr>

                                <td><?= h($row['id']) ?></td>

                                <td><?= h($row['title_en']) ?></td>

                                <td><?= h($row['title_am']) ?></td>

                                <td>
                                    <?= h($row['preacher'] ?: $t['not_specified']) ?>
                                </td>

                                <td>
                                    <?= h($row['sermon_date'] ?: $t['not_specified']) ?>
                                </td>

                                <td>

                                    <?php if (!empty($row['video_url'])): ?>

                                        <a
                                            href="<?= h($row['video_url']) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <?= h($t['watch_video']) ?>
                                        </a>

                                    <?php else: ?>

                                        <?= h($t['no_video']) ?>

                                    <?php endif; ?>

                                </td>

                                <td class="action-buttons">

                                    <a
                                        href="sermons.php?edit=<?= (int)$row['id'] ?>&lang=<?= h($lang) ?>"
                                        class="edit-button"
                                    >
                                        <?= h($t['edit']) ?>
                                    </a>

                                    <form
                                        action="sermons.php?lang=<?= h($lang) ?>"
                                        method="POST"
                                        class="delete-form"
                                        onsubmit="return confirm(<?= h(json_encode($t['confirm_delete'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP)) ?>);"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= h($_SESSION['sermons_csrf']) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= h($row['id']) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-button"
                                        >
                                            <?= h($t['delete']) ?>
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <p><?= h($t['empty']) ?></p>
            </div>

        <?php endif; ?>

    </section>

</div>

<!-- ADMIN FOOTER -->
<footer class="admin-footer">

    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= h($t['copyright']) ?>
    </p>

    <p>
        <?= h($t['admin_panel']) ?>
    </p>

</footer>

</body>
</html>
