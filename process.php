 <?php
// Mulai session
session_start();

// Include file PHPMailer manual (tanpa Composer)
require __DIR__.'/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__.'/../vendor/PHPMailer/src/SMTP.php';
require __DIR__.'/../vendor/PHPMailer/src/Exception.php';

// Inisialisasi PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    // Konfigurasi SMTP Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sipalingtopup04@gmail.com'; // Ganti dengan email Gmail Anda
    $mail->Password   = 'passwordappkhusus';   // Password aplikasi khusus
    $mail->SMTPSecure = PHPMailer\PHPMailer\ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Pengirim dan Penerima
    $mail->setFrom('emailanda@gmail.com', 'SiPalingTopUp');
    $mail->addAddress('sipalingtopup04@gmail.com');
    $mail->addReplyTo($_POST['player_email'], $_POST['player_id']);

    // Format Email
    $mail->isHTML(true);
    $mail->Subject = 'Pesanan Baru - ' . $_POST['diamond_amount'] . ' Diamond';
    
    // Isi Email
    $message = "
    <h2>Detail Pesanan Top Up Free Fire</h2>
    <p><strong>ID Transaksi:</strong> " . htmlspecialchars($_POST['transaction_id']) . "</p>
    <p><strong>ID Free Fire:</strong> " . htmlspecialchars($_POST['player_id']) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($_POST['player_email']) . "</p>
    <p><strong>Jumlah Diamond:</strong> " . htmlspecialchars($_POST['diamond_amount']) . "</p>
    <p><strong>Harga:</strong> Rp " . number_format($_POST['diamond_price'], 0, ',', '.') . "</p>
    <p><strong>Metode Pembayaran:</strong> " . htmlspecialchars($_POST['payment_method']) . "</p>
    <p><strong>Waktu:</strong> " . htmlspecialchars($_POST['timestamp']) . "</p>
    <p><strong>Status:</strong> Pending</p>
    ";
    
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);

    // Kirim Email
    $mail->send();

    // Simpan data transaksi di session
    $_SESSION['lastTransaction'] = [
        'transaction_id' => $_POST['transaction_id'],
        'player_id' => $_POST['player_id'],
        'player_email' => $_POST['player_email'],
        'diamond_amount' => $_POST['diamond_amount'],
        'diamond_price' => $_POST['diamond_price'],
        'payment_method' => $_POST['payment_method'],
        'timestamp' => $_POST['timestamp'],
        'status' => 'Pending'
    ];

    // Redirect ke halaman pembayaran
    header('Location: payment_redirect.php?method=' . urlencode($_POST['payment_method']) . 
                     '&amount=' . urlencode($_POST['diamond_price']));
    exit();

} catch (Exception $e) {
    // Tangani error
    die("Gagal mengirim email: " . $e->getMessage());
}