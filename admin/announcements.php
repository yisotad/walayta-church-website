
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Set language.
$lang = ($_GET['lang'] ?? $_POST['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Translations.
$translations = [
    'en' => [
        'page_title' => 'Announcements Management',
        'description' => 'Create, update, and manage church announcements.',
        'back_dashboard' => 'Back to Dashboard',
        'add_announcement' => 'Add New Announcement',
        'edit_announcement' => 'Edit Announcement',
        'title_en' => 'Announcement Title (English)',
        'title_am' => 'Announcement Title (Amharic)',
        'content_en' => 'Announcement Content (English)',
        'content_am' => 'Announcement Content (Amharic)',
        'title_en_placeholder' => 'Enter announcement title in English',
        'title_am_placeholder' => 'Enter announcement title in Amharic',
        'content_en_placeholder' => 'Enter announcement details in English',
        'content_am_placeholder' => 'Enter announcement details in Amharic',
        'publication_date' => 'Publication Date and Time',
        'date_help' => 'Leave empty to use the current date and time.',
        'add_button' => 'Add Announcement',
        'update_button' => 'Update Announcement',
        'cancel' => 'Cancel Editing',
        'all_announcements' => 'All Announcements',
        'total' => 'Total:',
        'id' => 'ID',
        'title_column_en' => 'Title (English)',
        'title_column_am' => 'Title (Amharic)',
        'date_column' => 'Publication Date',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'not_set' => 'Not set',
        'empty_title' => 'No Announcements Found',
        'empty_description' => 'Use the form above to add your first church announcement.',
        'footer_rights' => 'All rights reserved.',
        'footer_panel' => 'Church Administration Panel',
        'confirm_delete' => 'Are you sure you want to delete this announcement?',

        'added' => 'Announcement added successfully.',
        'updated' => 'Announcement updated successfully.',
        'deleted' => 'Announcement deleted successfully.',
        'security_error' => 'Invalid security token. Please refresh the page and try again.',
        'required_titles' => 'Both English and Amharic titles are required.',
        'title_length' => 'Titles must not exceed 255 characters.',
        'invalid_date' => 'Please enter a valid publication date.',
        'invalid_id' => 'Invalid announcement ID.',
        'database_error' => 'A database error occurred. Please check the information and try again.',
        'not_found' => 'The selected announcement could not be found.',
        'invalid_action' => 'Invalid form action.'
    ],

    'am' => [
        'page_title' => 'የማስታወቂያዎች አስተዳደር',
        'description' => 'የቤተ ክርስቲያኑን ማስታወቂያዎች ይፍጠሩ፣ ያዘምኑ እና ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'add_announcement' => 'አዲስ ማስታወቂያ ያክሉ',
        'edit_announcement' => 'ማስታወቂያ ያርትዑ',
        'title_en' => 'የማስታወቂያ ርዕስ (እንግሊዝኛ)',
        'title_am' => 'የማስታወቂያ ርዕስ (አማርኛ)',
        'content_en' => 'የማስታወቂያ ይዘት (እንግሊዝኛ)',
        'content_am' => 'የማስታወቂያ ይዘት (አማርኛ)',
        'title_en_placeholder' => 'የማስታወቂያውን ርዕስ በእንግሊዝኛ ያስገቡ',
        'title_am_placeholder' => 'የማስታወቂያውን ርዕስ በአማርኛ ያስገቡ',
        'content_en_placeholder' => 'የማስታወቂያውን ዝርዝር በእንግሊዝኛ ያስገቡ',
        'content_am_placeholder' => 'የማስታወቂያውን ዝርዝር በአማርኛ ያስገቡ',
        'publication_date' => 'የህትመት ቀን እና ሰዓት',
        'date_help' => 'ባዶ ከተዉት የአሁኑ ቀንና ሰዓት ይጠቀማል።',
        'add_button' => 'ማስታወቂያ ያክሉ',
        'update_button' => 'ማስታወቂያውን ያዘምኑ',
        'cancel' => 'ማስተካከያውን ይተዉ',
        'all_announcements' => 'ሁሉም ማስታወቂያዎች',
        'total' => 'ጠቅላላ፦',
        'id' => 'ቁጥር',
        'title_column_en' => 'ርዕስ (እንግሊዝኛ)',
        'title_column_am' => 'ርዕስ (አማርኛ)',
        'date_column' => 'የህትመት ቀን',
        'actions' => 'ተግባራት',
        'edit' => 'አርትዕ',
        'delete' => 'ሰርዝ',
        'not_set' => 'አልተወሰነም',
        'empty_title' => 'ምንም ማስታወቂያ አልተገኘም',
        'empty_description' => 'የመጀመሪያውን የቤተ ክርስቲያን ማስታወቂያ ለመጨመር ከላይ ያለውን ቅጽ ይጠቀሙ።',
        'footer_rights' => 'መብቱ በሕግ የተጠበቀ ነው።',
        'footer_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',
        'confirm_delete' => 'ይህንን ማስታወቂያ ማጥፋት እርግጠኛ ነዎት?',

        'added' => 'ማስታወቂያው በተሳካ ሁኔታ ተጨምሯል።',
        'updated' => 'ማስታወቂያው በተሳካ ሁኔታ ታድሷል።',
        'deleted' => 'ማስታወቂያው በተሳካ ሁኔታ ተሰርዟል።',
        'security_error' => 'የደህንነት ማረጋገጫው አልተሳካም። እባክዎ ገጹን አድስተው እንደገና ይሞክሩ።',
        'required_titles' => 'የእንግሊዝኛና የአማርኛ ርዕሶች መሞላት አለባቸው።',
        'title_length' => 'ርዕሶቹ ከ255 ቁምፊዎች መብለጥ የለባቸውም።',
        'invalid_date' => 'እባክዎ ትክክለኛ የህትመት ቀን ያስገቡ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የማስታወቂያ መለያ ቁጥር።',
        'database_error' => 'የዳታቤዝ ችግር ተፈጥሯል። እባክዎ መረጃውን ያረጋግጡና እንደገና ይሞክሩ።',
        'not_found' => 'የተመረጠው ማስታወቂያ አልተገኘም።',
        'invalid_action' => 'ትክክለኛ ያልሆነ የቅጽ ትእዛዝ።'
    ]
];

$t = $translations[$lang];

// Only logged-in administrators may access this page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php?lang=' . urlencode($lang));
    exit;
}

// CSRF protection.
if (!isset($_SESSION['announcement_csrf'])) {
    $_SESSION['announcement_csrf'] = bin2hex(random_bytes(32));
}

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$message = '';
$messageType = '';

if (isset($_GET['status'])) {
    $status = $_GET['status'];

    if (isset($t[$status]) && in_array($status, ['added', 'updated', 'deleted'], true)) {
        $message = $t[$status];
        $messageType = 'success';
    }
}

$editAnnouncement = null;

// Handle form submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['announcement_csrf'], $token)
    ) {
        $message = $t['security_error'];
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'add' || $action === 'update') {
                $titleEn = trim($_POST['title_en'] ?? '');
                $titleAm = trim($_POST['title_am'] ?? '');
                $contentEn = trim($_POST['content_en'] ?? '');
                $contentAm = trim($_POST['content_am'] ?? '');
                $publishedAt = trim($_POST['published_at'] ?? '');
                $id = (int)($_POST['id'] ?? 0);

                if ($titleEn === '' || $titleAm === '') {
                    throw new Exception('required_titles');
                }

                if (mb_strlen($titleEn) > 255 || mb_strlen($titleAm) > 255) {
                    throw new Exception('title_length');
                }

                if ($publishedAt !== '') {
                    $date = DateTime::createFromFormat('!Y-m-d\TH:i', $publishedAt);
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
                        $date->format('Y-m-d\TH:i') !== $publishedAt
                    ) {
                        throw new Exception('invalid_date');
                    }

                    $publishedAt = $date->format('Y-m-d H:i:s');
                }

                if ($action === 'add') {
                    if ($publishedAt === '') {
                        $sql = "INSERT INTO announcements
                                (title_en, title_am, content_en, content_am)
                                VALUES (:title_en, :title_am, :content_en, :content_am)";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':title_en' => $titleEn,
                            ':title_am' => $titleAm,
                            ':content_en' => $contentEn !== '' ? $contentEn : null,
                            ':content_am' => $contentAm !== '' ? $contentAm : null
                        ]);
                    } else {
                        $sql = "INSERT INTO announcements
                                (title_en, title_am, content_en, content_am, published_at)
                                VALUES (:title_en, :title_am, :content_en, :content_am, :published_at)";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':title_en' => $titleEn,
                            ':title_am' => $titleAm,
                            ':content_en' => $contentEn !== '' ? $contentEn : null,
                            ':content_am' => $contentAm !== '' ? $contentAm : null,
                            ':published_at' => $publishedAt
                        ]);
                    }

                    header('Location: announcements.php?status=added&lang=' . urlencode($lang));
                    exit;
                }

                if ($action === 'update') {
                    if ($id <= 0) {
                        throw new Exception('invalid_id');
                    }

                    if ($publishedAt === '') {
                        $sql = "UPDATE announcements
                                SET title_en = :title_en,
                                    title_am = :title_am,
                                    content_en = :content_en,
                                    content_am = :content_am
                                WHERE id = :id";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':title_en' => $titleEn,
                            ':title_am' => $titleAm,
                            ':content_en' => $contentEn !== '' ? $contentEn : null,
                            ':content_am' => $contentAm !== '' ? $contentAm : null,
                            ':id' => $id
                        ]);
                    } else {
                        $sql = "UPDATE announcements
                                SET title_en = :title_en,
                                    title_am = :title_am,
                                    content_en = :content_en,
                                    content_am = :content_am,
                                    published_at = :published_at
                                WHERE id = :id";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':title_en' => $titleEn,
                            ':title_am' => $titleAm,
                            ':content_en' => $contentEn !== '' ? $contentEn : null,
                            ':content_am' => $contentAm !== '' ? $contentAm : null,
                            ':published_at' => $publishedAt,
                            ':id' => $id
                        ]);
                    }

                    header('Location: announcements.php?status=updated&lang=' . urlencode($lang));
                    exit;
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new Exception('invalid_id');
                }

                $stmt = $pdo->prepare('DELETE FROM announcements WHERE id = :id');
                $stmt->execute([':id' => $id]);

                header('Location: announcements.php?status=deleted&lang=' . urlencode($lang));
                exit;
            } else {
                throw new Exception('invalid_action');
            }
        } catch (PDOException $e) {
            error_log('Announcement database error: ' . $e->getMessage());
            $message = $t['database_error'];
            $messageType = 'error';
        } catch (Exception $e) {
            $errorKey = $e->getMessage();
            $message = $t[$errorKey] ?? $t['database_error'];
            $messageType = 'error';
        }
    }
}

// Load announcement for editing.
if (isset($_GET['edit'])) {
    $editId = filter_var(
        $_GET['edit'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($editId) {
        $stmt = $pdo->prepare('SELECT * FROM announcements WHERE id = :id');
        $stmt->execute([':id' => $editId]);
        $editAnnouncement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editAnnouncement) {
            $message = $t['not_found'];
            $messageType = 'error';
        }
    }
}

// Load announcements.
$stmt = $pdo->query(
    'SELECT * FROM announcements ORDER BY published_at DESC, id DESC'
);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

$formDate = '';

if ($editAnnouncement && !empty($editAnnouncement['published_at'])) {
    $formDate = date(
        'Y-m-d\TH:i',
        strtotime($editAnnouncement['published_at'])
    );
}
?>

<!DOCTYPE html>
<html lang="<?= h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-announcements.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<main class="admin-container">

    <section class="page-header">
        <div>
            <h1><?= h($t['page_title']) ?></h1>
            <p><?= h($t['description']) ?></p>
        </div>

        <a href="dashboard.php?lang=<?= h($lang) ?>" class="back-button">
            <?= h($t['back_dashboard']) ?>
        </a>
    </section>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= h($messageType) ?>">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <section class="announcement-form-section">

        <h2>
            <?= h($editAnnouncement ? $t['edit_announcement'] : $t['add_announcement']) ?>
        </h2>

        <form
            method="POST"
            action="announcements.php?lang=<?= h($lang) ?><?= $editAnnouncement ? '&edit=' . (int)$editAnnouncement['id'] : '' ?>"
            class="announcement-form"
        >

            <input type="hidden" name="lang" value="<?= h($lang) ?>">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= h($_SESSION['announcement_csrf']) ?>"
            >

            <input
                type="hidden"
                name="action"
                value="<?= $editAnnouncement ? 'update' : 'add' ?>"
            >

            <?php if ($editAnnouncement): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$editAnnouncement['id'] ?>"
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
                    required
                    value="<?= h($editAnnouncement['title_en'] ?? '') ?>"
                    placeholder="<?= h($t['title_en_placeholder']) ?>"
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
                    required
                    value="<?= h($editAnnouncement['title_am'] ?? '') ?>"
                    placeholder="<?= h($t['title_am_placeholder']) ?>"
                >
            </div>

            <div class="form-group">
                <label for="content_en">
                    <?= h($t['content_en']) ?>
                </label>

                <textarea
                    id="content_en"
                    name="content_en"
                    rows="5"
                    placeholder="<?= h($t['content_en_placeholder']) ?>"
                ><?= h($editAnnouncement['content_en'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="content_am">
                    <?= h($t['content_am']) ?>
                </label>

                <textarea
                    id="content_am"
                    name="content_am"
                    rows="5"
                    placeholder="<?= h($t['content_am_placeholder']) ?>"
                ><?= h($editAnnouncement['content_am'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="published_at">
                    <?= h($t['publication_date']) ?>
                </label>

                <input
                    type="datetime-local"
                    id="published_at"
                    name="published_at"
                    value="<?= h($formDate) ?>"
                >

                <small><?= h($t['date_help']) ?></small>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-button">
                    <?= h($editAnnouncement ? $t['update_button'] : $t['add_button']) ?>
                </button>

                <?php if ($editAnnouncement): ?>
                    <a
                        href="announcements.php?lang=<?= h($lang) ?>"
                        class="cancel-button"
                    >
                        <?= h($t['cancel']) ?>
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </section>

    <section class="announcement-list-section">

        <div class="list-header">
            <h2><?= h($t['all_announcements']) ?></h2>

            <span class="announcement-count">
                <?= h($t['total']) ?> <?= count($announcements) ?>
            </span>
        </div>

        <?php if (count($announcements) > 0): ?>

            <div class="table-wrapper">
                <table class="announcement-table">

                    <thead>
                        <tr>
                            <th><?= h($t['id']) ?></th>
                            <th><?= h($t['title_column_en']) ?></th>
                            <th><?= h($t['title_column_am']) ?></th>
                            <th><?= h($t['date_column']) ?></th>
                            <th><?= h($t['actions']) ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td><?= (int)$announcement['id'] ?></td>

                                <td><?= h($announcement['title_en']) ?></td>

                                <td class="amharic-text">
                                    <?= h($announcement['title_am']) ?>
                                </td>

                                <td>
                                    <?= !empty($announcement['published_at'])
                                        ? h(date('M d, Y h:i A', strtotime($announcement['published_at'])))
                                        : h($t['not_set']) ?>
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a
                                            href="announcements.php?edit=<?= (int)$announcement['id'] ?>&amp;lang=<?= h($lang) ?>"
                                            class="edit-button"
                                        >
                                            <?= h($t['edit']) ?>
                                        </a>

                                        <form
                                            method="POST"
                                            action="announcements.php?lang=<?= h($lang) ?>"
                                            class="delete-form"
                                            onsubmit="return confirm(<?= h(json_encode($t['confirm_delete'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP)) ?>);"
                                        >

                                            <input type="hidden" name="lang" value="<?= h($lang) ?>">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= h($_SESSION['announcement_csrf']) ?>"
                                            >

                                            <input type="hidden" name="action" value="delete">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$announcement['id'] ?>"
                                            >

                                            <button type="submit" class="delete-button">
                                                <?= h($t['delete']) ?>
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3><?= h($t['empty_title']) ?></h3>
                <p><?= h($t['empty_description']) ?></p>
            </div>

        <?php endif; ?>

    </section>

</main>

<footer class="admin-footer">
    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= h($t['footer_rights']) ?>
    </p>

    <p><?= h($t['footer_panel']) ?></p>
</footer>

</body>
</html>
