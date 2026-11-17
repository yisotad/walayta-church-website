
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Select language.
$lang = ($_GET['lang'] ?? $_POST['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Only logged-in administrators may manage members.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php?lang=' . urlencode($lang));
    exit;
}

// English and Amharic translations.
$translations = [
    'en' => [
        'page_title' => 'Members Management',
        'subtitle' => 'Manage church members and their information.',
        'back_dashboard' => 'Back to Dashboard',
        'add_member' => 'Add New Member',
        'edit_member' => 'Edit Member',
        'full_name' => 'Full Name',
        'full_name_required' => 'Full Name *',
        'enter_full_name' => 'Enter member full name',
        'email' => 'Email Address',
        'enter_email' => 'Enter email address',
        'phone' => 'Phone Number',
        'enter_phone' => 'Enter phone number',
        'preferred_language' => 'Preferred Language',
        'select_language' => 'Select Language',
        'english' => 'English',
        'amharic' => 'Amharic',
        'wolaytta' => 'Wolaytta',
        'update_member' => 'Update Member',
        'add_member_button' => 'Add Member',
        'cancel_editing' => 'Cancel Editing',
        'all_members' => 'All Church Members',
        'total_members' => 'Total Members',
        'id' => 'ID',
        'registration_date' => 'Registration Date',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_members' => 'No Members Found',
        'no_members_description' => 'Add your first church member using the form above.',
        'not_available' => 'Not available',
        'confirm_delete' => 'Are you sure you want to delete this member?',
        'added' => 'Member added successfully.',
        'updated' => 'Member updated successfully.',
        'deleted' => 'Member deleted successfully.',
        'invalid_token' => 'Invalid security token. Refresh the page and try again.',
        'name_required' => 'Member full name is required.',
        'valid_email' => 'Please enter a valid email address.',
        'invalid_id' => 'Invalid member ID.',
        'database_error' => 'A database error occurred. Please check the member information.',
        'footer_rights' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel'
    ],

    'am' => [
        'page_title' => 'የአባላት አስተዳደር',
        'subtitle' => 'የቤተ ክርስቲያን አባላትንና መረጃቸውን ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'add_member' => 'አዲስ አባል ጨምር',
        'edit_member' => 'የአባል መረጃ አስተካክል',
        'full_name' => 'ሙሉ ስም',
        'full_name_required' => 'ሙሉ ስም *',
        'enter_full_name' => 'የአባሉን ሙሉ ስም ያስገቡ',
        'email' => 'የኢሜይል አድራሻ',
        'enter_email' => 'የኢሜይል አድራሻ ያስገቡ',
        'phone' => 'ስልክ ቁጥር',
        'enter_phone' => 'ስልክ ቁጥር ያስገቡ',
        'preferred_language' => 'የሚመርጡት ቋንቋ',
        'select_language' => 'ቋንቋ ይምረጡ',
        'english' => 'እንግሊዝኛ',
        'amharic' => 'አማርኛ',
        'wolaytta' => 'ወላይትኛ',
        'update_member' => 'የአባል መረጃ አዘምን',
        'add_member_button' => 'አባል ጨምር',
        'cancel_editing' => 'ማስተካከያውን ሰርዝ',
        'all_members' => 'ሁሉም የቤተ ክርስቲያን አባላት',
        'total_members' => 'ጠቅላላ አባላት',
        'id' => 'መለያ ቁጥር',
        'registration_date' => 'የምዝገባ ቀን',
        'actions' => 'ተግባሮች',
        'edit' => 'አስተካክል',
        'delete' => 'ሰርዝ',
        'no_members' => 'ምንም አባላት አልተገኙም',
        'no_members_description' => 'ከላይ ባለው ፎርም የመጀመሪያውን የቤተ ክርስቲያን አባል ይመዝግቡ።',
        'not_available' => 'አይገኝም',
        'confirm_delete' => 'ይህን አባል ለመሰረዝ እርግጠኛ ነዎት?',
        'added' => 'አባሉ በተሳካ ሁኔታ ተጨምሯል።',
        'updated' => 'የአባሉ መረጃ በተሳካ ሁኔታ ተዘምኗል።',
        'deleted' => 'አባሉ በተሳካ ሁኔታ ተሰርዟል።',
        'invalid_token' => 'የደህንነት ቶከኑ ትክክል አይደለም። ገጹን አድስና እንደገና ሞክር።',
        'name_required' => 'የአባሉ ሙሉ ስም ማስገባት ያስፈልጋል።',
        'valid_email' => 'እባክዎ ትክክለኛ የኢሜይል አድራሻ ያስገቡ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የአባል መለያ ቁጥር ነው።',
        'database_error' => 'የዳታቤዝ ስህተት ተፈጥሯል። የአባሉን መረጃ ያረጋግጡ።',
        'footer_rights' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል'
    ]
];

$t = $translations[$lang];

// CSRF token.
if (empty($_SESSION['members_csrf'])) {
    $_SESSION['members_csrf'] = bin2hex(random_bytes(32));
}

if (!function_exists('h')) {
    function h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$message = '';
$messageType = '';

// Display status messages.
if (isset($_GET['status'])) {
    $statuses = [
        'added' => 'added',
        'updated' => 'updated',
        'deleted' => 'deleted'
    ];

    if (isset($statuses[$_GET['status']])) {
        $message = $t[$statuses[$_GET['status']]];
        $messageType = 'success';
    }
}

// Handle add, update and delete.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['members_csrf'], $token)
    ) {
        $message = $t['invalid_token'];
        $messageType = 'error';

    } else {

        $action = $_POST['action'] ?? '';

        try {

            if ($action === 'add' || $action === 'update') {

                $fullName = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $language = trim($_POST['preferred_language'] ?? '');
                $id = (int)($_POST['id'] ?? 0);

                if ($fullName === '') {
                    throw new Exception('name_required');
                }

                if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('valid_email');
                }

                if ($action === 'add') {

                    $sql = "INSERT INTO members
                            (full_name, email, phone, preferred_language)
                            VALUES (:full_name, :email, :phone, :language)";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ':full_name' => $fullName,
                        ':email' => $email !== '' ? $email : null,
                        ':phone' => $phone !== '' ? $phone : null,
                        ':language' => $language !== '' ? $language : null
                    ]);

                    header('Location: members.php?status=added&lang=' . urlencode($lang));
                    exit;
                }

                if ($action === 'update') {

                    if ($id <= 0) {
                        throw new Exception('invalid_id');
                    }

                    $sql = "UPDATE members
                            SET full_name = :full_name,
                                email = :email,
                                phone = :phone,
                                preferred_language = :language
                            WHERE id = :id";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ':full_name' => $fullName,
                        ':email' => $email !== '' ? $email : null,
                        ':phone' => $phone !== '' ? $phone : null,
                        ':language' => $language !== '' ? $language : null,
                        ':id' => $id
                    ]);

                    header('Location: members.php?status=updated&lang=' . urlencode($lang));
                    exit;
                }

            } elseif ($action === 'delete') {

                $id = (int)($_POST['id'] ?? 0);

                if ($id <= 0) {
                    throw new Exception('invalid_id');
                }

                $stmt = $pdo->prepare('DELETE FROM members WHERE id = :id');
                $stmt->execute([':id' => $id]);

                header('Location: members.php?status=deleted&lang=' . urlencode($lang));
                exit;

            } else {
                throw new Exception('invalid_id');
            }

        } catch (PDOException $ex) {

            error_log('Members database error: ' . $ex->getMessage());

            $message = $t['database_error'];
            $messageType = 'error';

        } catch (Exception $ex) {

            $message = $t[$ex->getMessage()] ?? $t['invalid_id'];
            $messageType = 'error';
        }
    }
}

// Load member for editing.
$editMember = null;

if (isset($_GET['edit'])) {

    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT);

    if ($editId !== false && $editId > 0) {

        $stmt = $pdo->prepare('SELECT * FROM members WHERE id = :id');
        $stmt->execute([':id' => $editId]);

        $editMember = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editMember) {
            $message = $t['invalid_id'];
            $messageType = 'error';
        }

    } else {
        $message = $t['invalid_id'];
        $messageType = 'error';
    }
}

// Load all members.
$stmt = $pdo->query(
    'SELECT * FROM members ORDER BY created_at DESC, id DESC'
);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Language options remain unchanged in the database.
$languages = [
    'English' => $t['english'],
    'Amharic' => $t['amharic'],
    'Wolaytta' => $t['wolaytta']
];

$currentLanguage = $editMember['preferred_language'] ?? '';

if (
    $currentLanguage !== '' &&
    !array_key_exists($currentLanguage, $languages)
) {
    $languages[$currentLanguage] = $currentLanguage;
}
?>

<!DOCTYPE html>
<html lang="<?= h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-members.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<main class="admin-container">

    <!-- PAGE HEADER -->
    <section class="page-header">

        <div>
            <h1><?= h($t['page_title']) ?></h1>
            <p><?= h($t['subtitle']) ?></p>
        </div>

        <a href="dashboard.php?lang=<?= h($lang) ?>" class="back-button">
            <?= h($t['back_dashboard']) ?>
        </a>

    </section>

    <!-- ALERT MESSAGE -->
    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= h($messageType) ?>">
            <?= h($message) ?>
        </div>
    <?php endif; ?>

    <!-- ADD OR EDIT MEMBER FORM -->
    <section class="member-form-section">

        <h2>
            <?= h($editMember ? $t['edit_member'] : $t['add_member']) ?>
        </h2>

        <form
            method="POST"
            action="members.php<?= $editMember
                ? '?edit=' . (int)$editMember['id'] . '&lang=' . h($lang)
                : '?lang=' . h($lang) ?>"
            class="member-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= h($_SESSION['members_csrf']) ?>"
            >

            <input type="hidden" name="lang" value="<?= h($lang) ?>">

            <input
                type="hidden"
                name="action"
                value="<?= $editMember ? 'update' : 'add' ?>"
            >

            <?php if ($editMember): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$editMember['id'] ?>"
                >
            <?php endif; ?>

            <div class="form-group">

                <label for="full_name">
                    <?= h($t['full_name_required']) ?>
                </label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    required
                    maxlength="150"
                    value="<?= h($editMember['full_name'] ?? '') ?>"
                    placeholder="<?= h($t['enter_full_name']) ?>"
                >

            </div>

            <div class="form-group">

                <label for="email"><?= h($t['email']) ?></label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= h($editMember['email'] ?? '') ?>"
                    placeholder="<?= h($t['enter_email']) ?>"
                >

            </div>

            <div class="form-group">

                <label for="phone"><?= h($t['phone']) ?></label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= h($editMember['phone'] ?? '') ?>"
                    placeholder="<?= h($t['enter_phone']) ?>"
                >

            </div>

            <div class="form-group">

                <label for="preferred_language">
                    <?= h($t['preferred_language']) ?>
                </label>

                <select id="preferred_language" name="preferred_language">

                    <option value="">
                        <?= h($t['select_language']) ?>
                    </option>

                    <?php foreach ($languages as $languageValue => $languageLabel): ?>

                        <option
                            value="<?= h($languageValue) ?>"
                            <?= $currentLanguage === $languageValue ? 'selected' : '' ?>
                        >
                            <?= h($languageLabel) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-actions">

                <button type="submit" class="submit-button">
                    <?= h($editMember ? $t['update_member'] : $t['add_member_button']) ?>
                </button>

                <?php if ($editMember): ?>

                    <a
                        href="members.php?lang=<?= h($lang) ?>"
                        class="cancel-button"
                    >
                        <?= h($t['cancel_editing']) ?>
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>

    <!-- MEMBERS LIST -->
    <section class="member-list-section">

        <div class="list-header">

            <h2><?= h($t['all_members']) ?></h2>

            <span class="member-count">
                <?= h($t['total_members']) ?>:
                <?= count($members) ?>
            </span>

        </div>

        <?php if (count($members) > 0): ?>

            <div class="table-wrapper">

                <table class="member-table">

                    <thead>
                        <tr>
                            <th><?= h($t['id']) ?></th>
                            <th><?= h($t['full_name']) ?></th>
                            <th><?= h($t['email']) ?></th>
                            <th><?= h($t['phone']) ?></th>
                            <th><?= h($t['preferred_language']) ?></th>
                            <th><?= h($t['registration_date']) ?></th>
                            <th><?= h($t['actions']) ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($members as $member): ?>

                            <tr>

                                <td><?= (int)$member['id'] ?></td>

                                <td><?= h($member['full_name']) ?></td>

                                <td><?= h($member['email'] ?? '') ?></td>

                                <td><?= h($member['phone'] ?? '') ?></td>

                                <td>
                                    <?php
                                    $storedLanguage = $member['preferred_language'] ?? '';

                                    echo h($languages[$storedLanguage] ?? $storedLanguage);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo !empty($member['created_at'])
                                        ? h(date('M d, Y', strtotime($member['created_at'])))
                                        : h($t['not_available']);
                                    ?>
                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <a
                                            href="members.php?edit=<?= (int)$member['id'] ?>&lang=<?= h($lang) ?>"
                                            class="edit-button"
                                        >
                                            <?= h($t['edit']) ?>
                                        </a>

                                        <form
                                            method="POST"
                                            action="members.php?lang=<?= h($lang) ?>"
                                            class="delete-form"
                                            onsubmit="return confirm(<?= h(json_encode($t['confirm_delete'])) ?>);"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= h($_SESSION['members_csrf']) ?>"
                                            >

                                            <input type="hidden" name="lang" value="<?= h($lang) ?>">

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$member['id'] ?>"
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

                <h3><?= h($t['no_members']) ?></h3>

                <p><?= h($t['no_members_description']) ?></p>

            </div>

        <?php endif; ?>

    </section>

    <div class="bottom-dashboard-link">
        <a href="dashboard.php?lang=<?= h($lang) ?>" class="back-dashboard-button">
            &larr; <?= h($t['back_dashboard']) ?>
        </a>
    </div>

</main>

<!-- ADMIN FOOTER -->
<footer class="admin-footer">

    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= h($t['footer_rights']) ?>
    </p>

    <p><?= h($t['admin_panel']) ?></p>

</footer>

</body>
</html>
