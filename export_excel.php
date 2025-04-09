<?php
// Database connection
require_once 'config/database.php';
require 'vendor/autoload.php'; // Include PhpSpreadsheet library

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('Jadwal Shalat Jumat Generator')
    ->setLastModifiedBy('Jadwal Shalat Jumat Generator')
    ->setTitle('Jadwal Shalat Jumat')
    ->setSubject('Jadwal Shalat Jumat')
    ->setDescription('Jadwal Shalat Jumat Masjid An Nur Tong Tji');

// Add header
$sheet->setCellValue('A1', 'JADWAL SHOLAT JUMAT');
$sheet->setCellValue('A2', 'PT. TONG TJI TEA INDONESIA');
$sheet->setCellValue('A4', 'NO');
$sheet->setCellValue('B4', 'TANGGAL');
$sheet->setCellValue('C4', 'NAMA PENGISI');
$sheet->setCellValue('D4', 'NOMINAL');
$sheet->setCellValue('E4', 'KETERANGAN');

// Merge cells for title
$sheet->mergeCells('A1:E1');
$sheet->mergeCells('A2:E2');

// Style the header
$sheet->getStyle('A1:E2')->getFont()->setBold(true);
$sheet->getStyle('A1:E2')->getFont()->setSize(14);
$sheet->getStyle('A1:E2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Style the column headers
$sheet->getStyle('A4:E4')->getFont()->setBold(true);
$sheet->getStyle('A4:E4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A4:E4')->getFill()->getStartColor()->setRGB('006838'); // Tong Tji green
$sheet->getStyle('A4:E4')->getFont()->getColor()->setRGB('FFFFFF');

// Add data
$row = 5;
foreach ($details as $index => $detail) {
    $date = new DateTime($detail['date']);
    $dayOfMonth = $date->format('j');

    // Preacher row
    $sheet->setCellValue('A' . $row, $index + 1);
    $sheet->setCellValue('B' . $row, $dayOfMonth . ' ' . $monthNames[$schedule['month']] . ' ' . $schedule['year'] . "\n" . $detail['pasaran']);
    $sheet->setCellValue('C' . $row, 'Penceramah: ' . $detail['preacher']);
    $sheet->setCellValue('D' . $row, 'Rp. 300.000');
    $sheet->setCellValue('E' . $row, '');

    // Muadzin row
    $row++;
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('C' . $row, 'Muadzin: ' . $detail['muadzin']);
    $sheet->setCellValue('D' . $row, 'Rp. 100.000');
    $sheet->setCellValue('E' . $row, '');

    $row++;
}

// Add footer
$sheet->setCellValue('A' . $row, 'Tanda tangan tersebut sebagai bukti serah terima kepada yang bersangkutan');
$sheet->mergeCells('A' . $row . ':E' . $row);

// Auto-size columns
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set row height for the date cells to accommodate the pasaran
foreach ($sheet->getRowDimensions() as $rd) {
    $rd->setRowHeight(30);
}

// Create writer and output file
$writer = new Xlsx($spreadsheet);
$filename = 'Jadwal_Shalat_Jumat_' . $monthNames[$schedule['month']] . '_' . $schedule['year'] . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
