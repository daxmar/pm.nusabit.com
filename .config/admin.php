<?php
// Gunakan __DIR__ agar path file config aman
require_once __DIR__ . '/config.php'; 

$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- LOGIKA PAGINASI ---
$limit = 10; 
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

$countQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi");
$countData = mysqli_fetch_assoc($countQuery);
$totalRecords = $countData['total'];
$totalPages = ceil($totalRecords / $limit);

// --- QUERY AMBIL SEMUA KOLOM ---
$sql = "SELECT id, nama, jenjang, nomer_hp, alasan, 
               DATE_FORMAT(timestamp, '%d %b %Y, %H:%i') as waktu 
        FROM absensi 
        ORDER BY id DESC 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Populasi Absensi</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pagination { margin-top: 10px; }
        .pagination a { padding: 8px 12px; border: 1px solid #ccc; text-decoration: none; color: black; }
        .pagination a.active { background-color: #007bff; color: white; border-color: #007bff; }
    </style>
</head>
<body>

<h2>Daftar Seluruh Data Absensi</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Jenjang</th>
            <th>No. HP</th>
            <th>Alasan</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['jenjang']; ?></td>
                    <td><?php echo $row['nomer_hp']; ?></td>
                    <td><?php echo $row['alasan']; ?></td>
                    <td><?php echo $row['waktu']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <span>Halaman: </span>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?halaman=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>