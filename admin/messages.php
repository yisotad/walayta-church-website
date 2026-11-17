
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Select language.
$lang = ($_GET['lang'] ?? $_POST['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Only logged-in administrators can access this page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php?lang=' . urlencode($lang));
    exit;
}

// English and Amharic translations.
$translations = [
    'en' => [
        'page_title' => 'Messages Management',
        'subtitle' => 'View and manage messages sent by church website visitors.',
        'back_dashboard' => 'Back to Dashboard',
        'received_messages' => 'Received Messages',
        'messages_description' => 'Messages submitted through the Contact Us page.',
        'total_messages' => 'Total Messages',
        'id' => 'ID',
        'sender_name' => 'Sender Name',
        'email' => 'Email Address',
        'message' => 'Message',
        'date_received' => 'Date Received',
        'action' => 'Action',
        'not_provided' => 'Not provided',
        'not_available' => 'Not available',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this contact message?',
        'no_messages' => 'No Contact Messages',
        'no_messages_description' => 'There are currently no messages from website visitors.',
        'deleted' => 'Contact message deleted successfully.',
        'invalid_token' => 'Invalid security token. Please refresh the page and try again.',
        'invalid_id' => 'Invalid message ID.',
        'delete_error' => 'Unable to delete the message. Please try again.',
        'invalid_action' => 'Invalid form action.',
        'database_error' => 'A database error occurred. Please try again.',
        'footer_rights' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel'
    ],

    'am' => [
        'page_title' => 'የመልዕክቶች አስተዳደር',
        'subtitle' => 'ከቤተ ክርስቲያኑ ድረ-ገጽ ጎብኚዎች የተላኩ መልዕክቶችን ይመልከቱና ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'received_messages' => 'የደረሱ መልዕክቶች',
        'messages_description' => 'በእኛን ያግኙን ገጽ የተላኩ መልዕክቶች።',
        'total_messages' => 'ጠቅላላ መልዕክቶች',
        'id' => 'መለያ ቁጥር',
        'sender_name' => 'የላኪው ስም',
        'email' => 'የኢሜይል አድራሻ',
        'message' => 'መልዕክት',
        'date_received' => 'የተቀበለበት ቀን',
        'action' => 'ተግባር',
        'not_provided' => 'አልተሰጠም',
        'not_available' => 'አይገኝም',
        'delete' => 'ሰርዝ',
        'confirm_delete' => 'ይህን የእውቂያ መልዕክት ለመሰረዝ እርግጠኛ ነዎት?',
        'no_messages' => 'ምንም የእውቂያ መልዕክቶች የሉም',
        'no_messages_description' => 'በአሁኑ ጊዜ ከድረ-ገጽ ጎብኚዎች ምንም መልዕክት አልደረሰም።',
        'deleted' => 'የእውቂያ መልዕክቱ በተሳካ ሁኔታ ተሰርዟል።',
        'invalid_token' => 'የደህንነት ቶከኑ ትክክል አይደለም። እባክዎ ገጹን አድስና እንደገና ይሞክሩ።',
        'invalid_id' => 'ትክክለኛ ያልሆነ የመልዕክት መለያ ቁጥር ነው።',
        'delete_error' => 'መልዕክቱን መሰረዝ አልተቻለም። እባክዎ እንደገና ይሞክሩ።',
        'invalid_action' => 'ትክክለኛ ያልሆነ የፎርም ተግባር ነው።',
        'database_error' => 'የዳታቤዝ ስህተት ተፈጥሯል። እባክዎ እንደገና ይሞክሩ።',
        'footer_rights' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል'
    ]
];

$t = $translations[$lang];

// CSRF protection.
if (empty($_SESSION['messages_csrf'])) {
    $_SESSION['messages_csrf'] = bin2hex(random_bytes(32));
}

// Escape output safely.
if (!function_exists('h')) {
    function h($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$message = '';
$messageType = '';

// Display success message after deletion.
if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $message = $t['deleted'];
    $messageType = 'success';
}

// Handle message deletion.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['messages_csrf'], $token)
    ) {
        $message = $t['invalid_token'];
        $messageType = 'error';

    } elseif (($_POST['action'] ?? '') === 'delete') {

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $id <= 0) {

            $message = $t['invalid_id'];
            $messageType = 'error';

        } else {

            try {

                $stmt = $pdo->prepare(
                    'DELETE FROM contact_messages WHERE id = :id'
                );

                $stmt->execute([':id' => $id]);

                if ($stmt->rowCount() > 0) {
                    header('Location: messages.php?status=deleted&lang=' . urlencode($lang));
                    exit;
                }

                $message = $t['invalid_id'];
                $messageType = 'error';

            } catch (PDOException $ex) {

                error_log('Contact message deletion error: ' . $ex->getMessage());

                $message = $t['delete_error'];
                $messageType = 'error';
            }
        }

    } else {

        $message = $t['invalid_action'];
        $messageType = 'error';
    }
}

// Retrieve all contact messages.
try {

    $stmt = $pdo->query(
        'SELECT id, name, email, message, created_at
         FROM contact_messages
         ORDER BY created_at DESC, id DESC'
    );

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $ex) {

    error_log('Contact messages retrieval error: ' . $ex->getMessage());

    $messages = [];
    $message = $t['database_error'];
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="<?= h($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= h($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-messages.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<!-- MAIN CONTENT -->
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

    <!-- STATUS MESSAGE -->
    <?php if ($message !== ''): ?>

        <div class="alert alert-<?= h($messageType) ?>">
            <?= h($message) ?>
        </div>

    <?php endif; ?>

    <!-- MESSAGES SECTION -->
    <section class="messages-section">

        <div class="list-header">

            <div>
                <h2><?= h($t['received_messages']) ?></h2>

                <p><?= h($t['messages_description']) ?></p>
            </div>

            <span class="message-count">
                <?= h($t['total_messages']) ?>: <?= count($messages) ?>
            </span>

        </div>

        <?php if (count($messages) > 0): ?>

            <div class="table-wrapper">

                <table class="messages-table">

                    <thead>
                        <tr>
                            <th><?= h($t['id']) ?></th>
                            <th><?= h($t['sender_name']) ?></th>
                            <th><?= h($t['email']) ?></th>
                            <th><?= h($t['message']) ?></th>
                            <th><?= h($t['date_received']) ?></th>
                            <th><?= h($t['action']) ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($messages as $contactMessage): ?>

                            <tr>

                                <td>
                                    <?= (int)$contactMessage['id'] ?>
                                </td>

                                <td>
                                    <?= h($contactMessage['name']) ?>
                                </td>

                                <td>

                                    <?php if (!empty($contactMessage['email'])): ?>

                                        <a
                                            href="mailto:<?= h($contactMessage['email']) ?>"
                                            class="email-link"
                                        >
                                            <?= h($contactMessage['email']) ?>
                                        </a>

                                    <?php else: ?>

                                        <span class="no-email">
                                            <?= h($t['not_provided']) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="message-content">
                                    <?= nl2br(h($contactMessage['message'])) ?>
                                </td>

                                <td>

                                    <?php
                                    echo !empty($contactMessage['created_at'])
                                        ? h(date(
                                            'M d, Y h:i A',
                                            strtotime($contactMessage['created_at'])
                                        ))
                                        : h($t['not_available']);
                                    ?>

                                </td>

                                <td>

                                    <form
                                        method="POST"
                                        action="messages.php?lang=<?= h($lang) ?>"
                                        class="delete-form"
                                        onsubmit="return confirm(<?= h(json_encode($t['confirm_delete'])) ?>);"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= h($_SESSION['messages_csrf']) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="lang"
                                            value="<?= h($lang) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$contactMessage['id'] ?>"
                                        >

                                        <button type="submit" class="delete-button">
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

                <h3><?= h($t['no_messages']) ?></h3>

                <p><?= h($t['no_messages_description']) ?></p>

            </div>

        <?php endif; ?>

    </section>

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
