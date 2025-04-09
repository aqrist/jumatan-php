<?php
// Database connection
require_once 'config/database.php';

// Get all saved schedules
$stmt = $pdo->prepare("SELECT id, year, month, created_at FROM schedules ORDER BY year DESC, month ASC");
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format month names
$monthNames = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Shalat Jumat Masjid An Nur Tong Tji</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="container max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-green-800 mb-8">Jadwal Shalat Jumat Masjid An Nur Tong Tji</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-t-4 border-green-600">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Buat Jadwal Baru</h2>
                <a href="generator.php" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Buat Jadwal
                </a>
            </div>

            <h2 class="text-xl font-semibold text-gray-800 mb-4">Jadwal Tersimpan</h2>

            <?php if (count($schedules) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $monthNames[$schedule['month']]; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $schedule['year']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d M Y H:i', strtotime($schedule['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="view.php?id=<?php echo $schedule['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                                        <a href="generator.php?id=<?php echo $schedule['id']; ?>" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <a href="export.php?id=<?php echo $schedule['id']; ?>" class="text-yellow-600 hover:text-yellow-900">Export</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-gray-500">
                    Belum ada jadwal tersimpan. Silakan buat jadwal baru.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>