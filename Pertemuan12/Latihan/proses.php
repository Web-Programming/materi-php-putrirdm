<?php
// -------------------------
// Contoh Proses Form POST
// -------------------------

$nama = '';
$email = '';
$pesan = '';
$postErrors = [];
$postSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    // Validasi sederhana
    if ($nama === '') {
        $postErrors[] = 'Nama wajib diisi.';
    }

    if ($email === '') {
        $postErrors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $postErrors[] = 'Format email tidak valid.';
    }

    if ($pesan === '') {
        $postErrors[] = 'Pesan wajib diisi.';
    }

    if (empty($postErrors)) {
        $postSuccess = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Dasar PHP Form - POST</title>
</head>
<body>
    <h2>Contoh Proses Form POST</h2>

    <?php if (!empty($postErrors)): ?>
        <div class="error">
            <strong>Validasi gagal:</strong>
            <ul>
                <?php foreach ($postErrors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($postSuccess): ?>
        <div class="success">Data berhasil dikirim dengan method POST.</div>
        <div class="result">
            <strong>Hasil POST:</strong><br>
            Nama: <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?><br>
            Email: <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?><br>
            Pesan: <?= nl2br(htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8')) ?>
        </div>
    <?php endif; ?>
    <a href="index2.php">Kembali ke Form</a>
</body>
</html>