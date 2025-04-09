<?php
// Database connection
require_once 'config/database.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$scheduleId = (int)$_GET['id'];

// Get schedule
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
$stmt->execute([$scheduleId]);
$schedule = $stmt->fetch();

if (!$schedule) {
    header('Location: index.php');
    exit;
}

// Get schedule details
$stmt = $pdo->prepare("SELECT * FROM schedule_details WHERE schedule_id = ? ORDER BY date ASC");
$stmt->execute([$scheduleId]);
$details = $stmt->fetchAll();

// Month names
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

// Pasaran badge classes
function getPasaranBadgeClass($pasaran)
{
    $badgeClasses = [
        'Legi' => 'inline-block px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800',
        'Pahing' => 'inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800',
        'Pon' => 'inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800',
        'Wage' => 'inline-block px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800',
        'Kliwon' => 'inline-block px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800'
    ];

    return $badgeClasses[$pasaran] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Shalat Jumat - <?php echo $monthNames[$schedule['month']]; ?> <?php echo $schedule['year']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="container max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-green-800 mb-8">Jadwal Shalat Jumat Masjid An Nur Tong Tji</h1>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                Jadwal Bulan <?php echo $monthNames[$schedule['month']]; ?> <?php echo $schedule['year']; ?>
            </h2>
            <div class="flex space-x-2">
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Kembali
                </a>
                <a href="generator.php?id=<?php echo $scheduleId; ?>" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Edit
                </a>
                <a href="export.php?id=<?php echo $scheduleId; ?>" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Export
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pasaran
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penceramah
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Muadzin
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($details as $index => $detail): ?>
                        <tr class="<?php echo $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $index + 1; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('j F Y', strtotime($detail['date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="<?php echo getPasaranBadgeClass($detail['pasaran']); ?>">
                                    <?php echo $detail['pasaran']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="font-medium <?php echo $detail['preacher'] === 'Ustadz Wahidin' ? 'text-pink-700' : 'text-blue-700'; ?>">
                                    <?php echo $detail['preacher']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $detail['muadzin']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mt-6 border-t-4 border-green-600">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Keterangan:</h3>
            <ul class="list-disc pl-5 space-y-1">
                <li>Ustadz Wahidin bertugas sebagai penceramah pada hari pasaran
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">Legi</span>
                    dan
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Kliwon</span>
                </li>
                <li>Ustadz Nawawi bertugas sebagai penceramah pada hari pasaran
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Pahing</span>,
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Pon</span>,
                    dan
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800">Wage</span>
                </li>
            </ul>
        </div>
    </div>
</body>

</html>