
<?php
session_start();

require_once __DIR__ . '/../config/db.php';

// Select language.
$lang = ($_GET['lang'] ?? $_POST['lang'] ?? 'en') === 'am' ? 'am' : 'en';

// Only logged-in administrators may manage donations.
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php?lang=' . urlencode($lang));
    exit;
}

// English and Amharic translations.
$translations = [
    'en' => [
        'page_title' => 'Donation Management',
        'subtitle' => 'Record, view, edit, and manage church donations.',
        'back_dashboard' => 'Back to Dashboard',
        'total_donations' => 'Total Donations',
        'donation_records' => 'Donation Records',
        'birr' => 'Birr',
        'edit_donation' => 'Edit Donation',
        'new_donation' => 'Record New Donation',
        'donor_name' => 'Donor Name',
        'enter_donor' => 'Enter donor name',
        'amount' => 'Donation Amount (Birr)',
        'enter_amount' => 'Enter amount',
        'payment_method' => 'Payment Method',
        'select_method' => 'Select payment method',
        'purpose' => 'Donation Purpose',
        'purpose_placeholder' => 'Example: Church Building',
        'donation_date' => 'Donation Date',
        'notes' => 'Additional Notes (Optional)',
        'notes_placeholder' => 'Enter additional information',
        'update' => 'Update Donation',
        'save' => 'Save Donation',
        'cancel' => 'Cancel Edit',
        'no_donations' => 'No donations have been recorded yet.',
        'number' => 'No.',
        'donor' => 'Donor',
        'date' => 'Date',
        'actions' => 'Actions',
        'total' => 'Total',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this donation?',
        'success_updated' => 'Donation updated successfully.',
        'success_recorded' => 'Donation recorded successfully.',
        'success_deleted' => 'Donation deleted successfully.',
        'not_found_delete' => 'Donation record was not found.',
        'invalid_token' => 'Invalid security token. Refresh the page and try again.',
        'invalid_record' => 'Invalid donation record.',
        'valid_donor' => 'Enter a valid donor name.',
        'valid_amount' => 'Enter a valid donation amount.',
        'valid_method' => 'Select a valid payment method.',
        'valid_purpose' => 'Enter a valid donation purpose.',
        'valid_date' => 'Enter a valid donation date.',
        'record_not_found' => 'Donation record not found.',
        'database_error' => 'Database error occurred. Please check your database table and try again.',
        'invalid_action' => 'Invalid form action.',
        'footer_rights' => 'All rights reserved.',
        'admin_panel' => 'Church Administration Panel',
        'cash' => 'Cash',
        'bank' => 'Bank Transfer',
        'telebirr' => 'Telebirr',
        'other' => 'Other'
    ],

    'am' => [
        'page_title' => 'የልገሳ አስተዳደር',
        'subtitle' => 'የቤተ ክርስቲያን ልገሳዎችን ይመዝግቡ፣ ይመልከቱ፣ ያስተካክሉ እና ያስተዳድሩ።',
        'back_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
        'total_donations' => 'ጠቅላላ ልገሳ',
        'donation_records' => 'የልገሳ መዝገቦች',
        'birr' => 'ብር',
        'edit_donation' => 'ልገሳን አስተካክል',
        'new_donation' => 'አዲስ ልገሳ መዝግብ',
        'donor_name' => 'የለጋሹ ስም',
        'enter_donor' => 'የለጋሹን ስም ያስገቡ',
        'amount' => 'የልገሳ መጠን (ብር)',
        'enter_amount' => 'የገንዘብ መጠኑን ያስገቡ',
        'payment_method' => 'የክፍያ ዘዴ',
        'select_method' => 'የክፍያ ዘዴ ይምረጡ',
        'purpose' => 'የልገሳ ዓላማ',
        'purpose_placeholder' => 'ምሳሌ፦ የቤተ ክርስቲያን ግንባታ',
        'donation_date' => 'የልገሳ ቀን',
        'notes' => 'ተጨማሪ ማስታወሻ (አማራጭ)',
        'notes_placeholder' => 'ተጨማሪ መረጃ ያስገቡ',
        'update' => 'ልገሳን አዘምን',
        'save' => 'ልገሳን መዝግብ',
        'cancel' => 'ማስተካከያውን ሰርዝ',
        'no_donations' => 'እስካሁን ምንም ልገሳ አልተመዘገበም።',
        'number' => 'ቁጥር',
        'donor' => 'ለጋሽ',
        'date' => 'ቀን',
        'actions' => 'ተግባሮች',
        'total' => 'ድምር',
        'edit' => 'አስተካክል',
        'delete' => 'ሰርዝ',
        'confirm_delete' => 'ይህን ልገሳ ለመሰረዝ እርግጠኛ ነዎት?',
        'success_updated' => 'ልገሳው በተሳካ ሁኔታ ተዘምኗል።',
        'success_recorded' => 'ልገሳው በተሳካ ሁኔታ ተመዝግቧል።',
        'success_deleted' => 'ልገሳው በተሳካ ሁኔታ ተሰርዟል።',
        'not_found_delete' => 'የልገሳ መዝገቡ አልተገኘም።',
        'invalid_token' => 'የደህንነት ቶከኑ ትክክል አይደለም። ገጹን አድስና እንደገና ሞክር።',
        'invalid_record' => 'ትክክለኛ ያልሆነ የልገሳ መዝገብ ነው።',
        'valid_donor' => 'ትክክለኛ የለጋሽ ስም ያስገቡ።',
        'valid_amount' => 'ትክክለኛ የልገሳ መጠን ያስገቡ።',
        'valid_method' => 'ትክክለኛ የክፍያ ዘዴ ይምረጡ።',
        'valid_purpose' => 'ትክክለኛ የልገሳ ዓላማ ያስገቡ።',
        'valid_date' => 'ትክክለኛ የልገሳ ቀን ያስገቡ።',
        'record_not_found' => 'የልገሳ መዝገቡ አልተገኘም።',
        'database_error' => 'የዳታቤዝ ስህተት ተፈጥሯል። የዳታቤዝ ሰንጠረዡን ያረጋግጡና እንደገና ይሞክሩ።',
        'invalid_action' => 'ትክክለኛ ያልሆነ የፎርም ተግባር ነው።',
        'footer_rights' => 'መብቱ በሙሉ የተጠበቀ ነው።',
        'admin_panel' => 'የቤተ ክርስቲያን አስተዳደር ፓነል',
        'cash' => 'ጥሬ ገንዘብ',
        'bank' => 'የባንክ ዝውውር',
        'telebirr' => 'ቴሌብር',
        'other' => 'ሌላ'
    ]
];

$t = $translations[$lang];

// Create CSRF token.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$error = '';
$editDonation = null;

function e($value): string
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Handle form submissions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        $error = $t['invalid_token'];

    } else {

        $action = $_POST['action'] ?? '';

        try {

            // ADD OR UPDATE DONATION
            if ($action === 'save') {

                $idRaw = trim($_POST['id'] ?? '');

                if ($idRaw === '') {
                    $id = null;
                } else {
                    $id = filter_var($idRaw, FILTER_VALIDATE_INT);

                    if ($id === false || $id <= 0) {
                        throw new Exception('invalid_record');
                    }
                }

                $donorName = trim($_POST['donor_name'] ?? '');
                $amount = trim($_POST['amount'] ?? '');
                $paymentMethod = trim($_POST['payment_method'] ?? '');
                $purpose = trim($_POST['donation_purpose'] ?? '');
                $donationDate = trim($_POST['donation_date'] ?? '');
                $notes = trim($_POST['notes'] ?? '');

                $allowedMethods = [
                    'Cash',
                    'Bank Transfer',
                    'Telebirr',
                    'Other'
                ];

                if ($donorName === '' || mb_strlen($donorName) > 150) {
                    throw new Exception('valid_donor');
                }

                if (
                    !is_numeric($amount) ||
                    (float)$amount <= 0 ||
                    (float)$amount > 9999999999.99
                ) {
                    throw new Exception('valid_amount');
                }

                if (!in_array($paymentMethod, $allowedMethods, true)) {
                    throw new Exception('valid_method');
                }

                if ($purpose === '' || mb_strlen($purpose) > 150) {
                    throw new Exception('valid_purpose');
                }

                $dateObject = DateTime::createFromFormat('!Y-m-d', $donationDate);
                $dateErrors = DateTime::getLastErrors();

                if (
                    !$dateObject ||
                    (
                        $dateErrors !== false &&
                        (
                            $dateErrors['warning_count'] > 0 ||
                            $dateErrors['error_count'] > 0
                        )
                    ) ||
                    $dateObject->format('Y-m-d') !== $donationDate
                ) {
                    throw new Exception('valid_date');
                }

                if ($id !== null) {

                    $sql = "UPDATE donations
                            SET donor_name = ?,
                                amount = ?,
                                payment_method = ?,
                                donation_purpose = ?,
                                donation_date = ?,
                                notes = ?
                            WHERE id = ?";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        $donorName,
                        $amount,
                        $paymentMethod,
                        $purpose,
                        $donationDate,
                        $notes !== '' ? $notes : null,
                        $id
                    ]);

                    $message = $t['success_updated'];

                } else {

                    $sql = "INSERT INTO donations
                            (
                                donor_name,
                                amount,
                                payment_method,
                                donation_purpose,
                                donation_date,
                                notes
                            )
                            VALUES (?, ?, ?, ?, ?, ?)";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        $donorName,
                        $amount,
                        $paymentMethod,
                        $purpose,
                        $donationDate,
                        $notes !== '' ? $notes : null
                    ]);

                    $message = $t['success_recorded'];
                }

            } elseif ($action === 'delete') {

                $id = filter_var(
                    $_POST['id'] ?? '',
                    FILTER_VALIDATE_INT
                );

                if ($id === false || $id === null || $id <= 0) {
                    throw new Exception('invalid_record');
                }

                $stmt = $pdo->prepare(
                    'DELETE FROM donations WHERE id = ?'
                );

                $stmt->execute([$id]);

                $message = $stmt->rowCount() > 0
                    ? $t['success_deleted']
                    : $t['not_found_delete'];

            } else {
                throw new Exception('invalid_action');
            }

        } catch (PDOException $ex) {

            error_log($ex->getMessage());
            $error = $t['database_error'];

        } catch (Exception $ex) {

            $error = $t[$ex->getMessage()] ?? $t['invalid_action'];
        }
    }
}

// Load donation for editing.
if (isset($_GET['edit'])) {

    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT);

    if ($editId === false || $editId === null || $editId <= 0) {
        $error = $t['invalid_record'];
    } else {

        $stmt = $pdo->prepare(
            'SELECT * FROM donations WHERE id = ?'
        );

        $stmt->execute([$editId]);

        $editDonation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editDonation) {
            $error = $t['record_not_found'];
        }
    }
}

// Get donation records.
$stmt = $pdo->query(
    'SELECT * FROM donations
     ORDER BY donation_date DESC, id DESC'
);

$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total donations.
$stmt = $pdo->query(
    'SELECT COALESCE(SUM(amount), 0) FROM donations'
);

$totalDonations = $stmt->fetchColumn();

$totalRecords = count($donations);
$today = date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="<?= e($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($t['page_title']) ?> | Walayta Sodo Kalehiwot Church</title>

    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/admin-donations.css">

    <script src="../assets/js/mobile-menu.js" defer></script>
</head>

<body>

<?php require_once __DIR__ . '/admin-header.php'; ?>

<main class="donations-container">

    <!-- PAGE HEADER -->
    <section class="page-header">

        <div>
            <h1><?= e($t['page_title']) ?></h1>
            <p><?= e($t['subtitle']) ?></p>
        </div>

        <a href="dashboard.php?lang=<?= e($lang) ?>" class="back-button">
            <?= e($t['back_dashboard']) ?>
        </a>

    </section>

    <!-- ALERT MESSAGES -->
    <?php if ($message !== ''): ?>
        <div class="alert success">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert error">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- DONATION SUMMARY -->
    <section class="summary-cards">

        <div class="summary-card">
            <h3><?= e($t['total_donations']) ?></h3>

            <p>
                <?= number_format((float)$totalDonations, 2) ?>
                <?= e($t['birr']) ?>
            </p>
        </div>

        <div class="summary-card">
            <h3><?= e($t['donation_records']) ?></h3>

            <p><?= number_format($totalRecords) ?></p>
        </div>

    </section>

    <!-- ADD OR EDIT DONATION FORM -->
    <section class="donation-form-section">

        <h2>
            <?= e($editDonation ? $t['edit_donation'] : $t['new_donation']) ?>
        </h2>

        <form
            method="POST"
            action="donations.php?lang=<?= e($lang) ?>"
            class="donation-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>"
            >

            <input type="hidden" name="action" value="save">
            <input type="hidden" name="lang" value="<?= e($lang) ?>">

            <input
                type="hidden"
                name="id"
                value="<?= e($editDonation['id'] ?? '') ?>"
            >

            <div class="form-grid">

                <div class="form-group">
                    <label for="donor_name"><?= e($t['donor_name']) ?></label>

                    <input
                        type="text"
                        id="donor_name"
                        name="donor_name"
                        maxlength="150"
                        required
                        value="<?= e($editDonation['donor_name'] ?? '') ?>"
                        placeholder="<?= e($t['enter_donor']) ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="amount"><?= e($t['amount']) ?></label>

                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        min="0.01"
                        step="0.01"
                        required
                        value="<?= e($editDonation['amount'] ?? '') ?>"
                        placeholder="<?= e($t['enter_amount']) ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="payment_method">
                        <?= e($t['payment_method']) ?>
                    </label>

                    <select id="payment_method" name="payment_method" required>

                        <option value="">
                            <?= e($t['select_method']) ?>
                        </option>

                        <?php
                        $methods = [
                            'Cash' => $t['cash'],
                            'Bank Transfer' => $t['bank'],
                            'Telebirr' => $t['telebirr'],
                            'Other' => $t['other']
                        ];

                        foreach ($methods as $methodValue => $methodLabel):
                        ?>

                            <option
                                value="<?= e($methodValue) ?>"
                                <?= ($editDonation['payment_method'] ?? '') === $methodValue
                                    ? 'selected' : '' ?>
                            >
                                <?= e($methodLabel) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="donation_purpose">
                        <?= e($t['purpose']) ?>
                    </label>

                    <input
                        type="text"
                        id="donation_purpose"
                        name="donation_purpose"
                        maxlength="150"
                        required
                        value="<?= e($editDonation['donation_purpose'] ?? '') ?>"
                        placeholder="<?= e($t['purpose_placeholder']) ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="donation_date">
                        <?= e($t['donation_date']) ?>
                    </label>

                    <input
                        type="date"
                        id="donation_date"
                        name="donation_date"
                        required
                        value="<?= e($editDonation['donation_date'] ?? $today) ?>"
                    >
                </div>

                <div class="form-group full-width">
                    <label for="notes"><?= e($t['notes']) ?></label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        placeholder="<?= e($t['notes_placeholder']) ?>"
                    ><?= e($editDonation['notes'] ?? '') ?></textarea>
                </div>

            </div>

            <div class="form-actions">

                <button type="submit" class="save-button">
                    <?= e($editDonation ? $t['update'] : $t['save']) ?>
                </button>

                <?php if ($editDonation): ?>
                    <a
                        href="donations.php?lang=<?= e($lang) ?>"
                        class="cancel-button"
                    >
                        <?= e($t['cancel']) ?>
                    </a>
                <?php endif; ?>

            </div>

        </form>

    </section>

    <!-- DONATION RECORDS -->
    <section class="donation-list-section">

        <h2><?= e($t['donation_records']) ?></h2>

        <?php if (empty($donations)): ?>

            <p class="empty-message">
                <?= e($t['no_donations']) ?>
            </p>

        <?php else: ?>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th><?= e($t['number']) ?></th>
                            <th><?= e($t['donor']) ?></th>
                            <th><?= e($t['amount']) ?></th>
                            <th><?= e($t['payment_method']) ?></th>
                            <th><?= e($t['purpose']) ?></th>
                            <th><?= e($t['date']) ?></th>
                            <th><?= e($t['notes']) ?></th>
                            <th><?= e($t['actions']) ?></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($donations as $index => $donation): ?>

                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td><?= e($donation['donor_name']) ?></td>

                                <td>
                                    <?= number_format((float)$donation['amount'], 2) ?>
                                </td>

                                <td>
                                    <?= e($methods[$donation['payment_method']] ?? $donation['payment_method']) ?>
                                </td>

                                <td><?= e($donation['donation_purpose']) ?></td>

                                <td><?= e($donation['donation_date']) ?></td>

                                <td><?= e($donation['notes'] ?? '') ?></td>

                                <td>

                                    <div class="action-buttons">

                                        <a
                                            class="edit-button"
                                            href="donations.php?edit=<?= (int)$donation['id'] ?>&lang=<?= e($lang) ?>"
                                        >
                                            <?= e($t['edit']) ?>
                                        </a>

                                        <form
                                            method="POST"
                                            action="donations.php?lang=<?= e($lang) ?>"
                                            onsubmit="return confirm(<?= e(json_encode($t['confirm_delete'])) ?>);"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e($_SESSION['csrf_token']) ?>"
                                            >

                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="lang" value="<?= e($lang) ?>">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$donation['id'] ?>"
                                            >

                                            <button type="submit" class="delete-button">
                                                <?= e($t['delete']) ?>
                                            </button>

                                        </form>

                                    </div>

                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2"><?= e($t['total']) ?></th>

                            <th>
                                <?= number_format((float)$totalDonations, 2) ?>
                                <?= e($t['birr']) ?>
                            </th>

                            <th colspan="5"></th>
                        </tr>
                    </tfoot>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

<!-- ADMIN FOOTER -->
<footer class="admin-footer">

    <p>
        &copy; <?= date('Y') ?>
        Walayta Sodo Kalehiwot Church.
        <?= e($t['footer_rights']) ?>
    </p>

    <p><?= e($t['admin_panel']) ?></p>

</footer>

</body>
</html>
