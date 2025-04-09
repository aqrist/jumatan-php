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

// Prepare CSV data
$csvData = [
    ['JADWAL SHOLAT JUMAT'],
    ['PT. TONG TJI TEA INDONESIA'],
    [''],
    ['NO', 'TANGGAL', 'NAMA PENGISI', 'NOMINAL', 'KETERANGAN']
];

foreach ($details as $index => $detail) {
    $date = new DateTime($detail['date']);
    $dayOfMonth = $date->format('j');

    // Preacher row
    $csvData[] = [
        $index + 1,
        $dayOfMonth . ' ' . $monthNames[$schedule['month']] . ' ' . $schedule['year'] . "\n" . $detail['pasaran'],
        'Penceramah: ' . $detail['preacher'],
        'Rp. 300.000',
        ''
    ];

    // Muadzin row
    $csvData[] = [
        '',
        '',
        'Muadzin: ' . $detail['muadzin'],
        'Rp. 100.000',
        ''
    ];
}

// Add footer
$csvData[] = [''];
$csvData[] = ['Tanda tangan tersebut sebagai bukti serah terima kepada yang bersangkutan'];

// Create CSV content
$output = fopen('php://output', 'w');
foreach ($csvData as $row) {
    fputcsv($output, $row);
}
fclose($output);

// Set headers for download
$filename = 'Jadwal_Shalat_Jumat_' . $monthNames[$schedule['month']] . '_' . $schedule['year'] . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

exit;
