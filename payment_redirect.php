<?php
// Fungsi untuk mengarahkan ke aplikasi pembayaran
function redirectToPayment($paymentMethod, $amount) {
    $paymentNumber = '881026244417'; // Nomor tanpa 0 di depan
    $note = 'TopUp FF ' . $amount . ' Diamond';
    $isMobile = preg_match('/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i', $_SERVER['HTTP_USER_AGENT']);
    
    // Format nomor untuk DANA (tanpa +62 atau 0)
    $danaNumber = '881026244417';
    // Format nomor untuk GoPay (dengan 62)
    $gopayNumber = '62881026244417';

    switch($paymentMethod) {
        case 'dana':
            $appDeepLink = 'dana://v1/transfer?phoneNumber='.$danaNumber.'&amount='.$amount.'&notes='.urlencode($note);
            $webFallback = 'https://m.dana.id/m/transfer?phoneNumber='.$danaNumber.'&amount='.$amount;
            break;
        case 'ovo':
            $appDeepLink = 'ovo://transfer?phone='.$paymentNumber.'&amount='.$amount.'&message='.urlencode($note);
            $webFallback = 'https://ovo.id/transfer?phone='.$paymentNumber.'&amount='.$amount;
            break;
        case 'gopay':
            $appDeepLink = 'gopay://payment?phone='.$gopayNumber.'&amount='.$amount.'&notes='.urlencode($note);
            $webFallback = 'https://gopay.com/pay/'.$gopayNumber.'?amount='.$amount;
            break;
        case 'qris':
            header('Location: https://qris.id/pay?amount='.$amount);
            exit;
    }

    // Untuk mobile device
    if ($isMobile) {
        // Coba buka aplikasi
        header('Location: '.$appDeepLink);
        
        // Fallback ke web setelah timeout
        echo '<script>
            setTimeout(function() {
                window.location.href = "'.$webFallback.'";
            }, 1000);
        </script>';
    } 
    // Untuk desktop
    else {
        header('Location: '.$webFallback);
    }
}

// Ambil parameter dari URL
$method = $_GET['method'] ?? '';
$amount = $_GET['amount'] ?? '';

if ($method && $amount) {
    redirectToPayment($method, $amount);
} else {
    header('Location: success.html');
}