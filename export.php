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

// Format for export
$format = isset($_GET['format']) ? $_GET['format'] : 'html';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Jadwal - <?php echo $monthNames[$schedule['month']]; ?> <?php echo $schedule['year']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="container max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-green-800 mb-8">Export Jadwal Shalat Jumat</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-t-4 border-green-600">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    Export Jadwal Bulan <?php echo $monthNames[$schedule['month']]; ?> <?php echo $schedule['year']; ?>
                </h2>
                <a href="view.php?id=<?php echo $scheduleId; ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Kembali
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="export_excel.php?id=<?php echo $scheduleId; ?>" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-md transition duration-200 flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 01-1-1V8a1 1 0 011-1h4a1 1 0 110 2H4v7h12v-7h-3a1 1 0 110-2h4a1 1 0 011 1v8a1 1 0 01-1 1H3z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M4.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 5.414V11a1 1 0 11-2 0V5.414L5.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Export Excel
                </a>

                <a href="export_pdf.php?id=<?php echo $scheduleId; ?>" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-4 px-6 rounded-md transition duration-200 flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 01-1-1V8a1 1 0 011-1h4a1 1 0 110 2H4v7h12v-7h-3a1 1 0 110-2h4a1 1 0 011 1v8a1 1 0 01-1 1H3z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M4.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 5.414V11a1 1 0 11-2 0V5.414L5.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Export PDF
                </a>

                <a href="export_csv.php?id=<?php echo $scheduleId; ?>" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-4 px-6 rounded-md transition duration-200 flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 01-1-1V8a1 1 0 011-1h4a1 1 0 110 2H4v7h12v-7h-3a1 1 0 110-2h4a1 1 0 011 1v8a1 1 0 01-1 1H3z" clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M4.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 5.414V11a1 1 0 11-2 0V5.414L5.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </div>
</body>

</html>