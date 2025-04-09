<?php
// Database connection
require_once 'config/database.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID jadwal tidak ditemukan');
}

$scheduleId = $_GET['id'];

// Get schedule data
$stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
$stmt->execute([$scheduleId]);
$schedule = $stmt->fetch();

if (!$schedule) {
    die('Data jadwal tidak ditemukan');
}

// Get schedule details
$stmt = $pdo->prepare("SELECT * FROM schedule_details WHERE schedule_id = ? ORDER BY date ASC");
$stmt->execute([$scheduleId]);
$details = $stmt->fetchAll();

// Month names in Indonesian
$monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Require FPDF library
require_once 'vendor/autoload.php';

// Create PDF
class PDF extends FPDF
{
    function Header()
    {
        // Arial bold 14
        $this->SetFont('Arial', 'B', 14);
        
        // Title
        $this->Cell(0, 10, 'JADWAL SHOLAT JUMAT', 0, 1, 'C');
        $this->Cell(0, 10, 'PT. TONG TJI TEA INDONESIA', 0, 1, 'C');
        
        // Line break
        $this->Ln(10);
        
        // Table header
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(0, 104, 56); // Tong Tji green (006838)
        $this->SetTextColor(255, 255, 255); // White text
        $this->Cell(10, 10, 'NO', 1, 0, 'C', true);
        $this->Cell(50, 10, 'TANGGAL', 1, 0, 'C', true);
        $this->Cell(70, 10, 'NAMA PENGISI', 1, 0, 'C', true);
        $this->Cell(30, 10, 'NOMINAL', 1, 0, 'C', true);
        $this->Cell(30, 10, 'KETERANGAN', 1, 1, 'C', true);
        
        // Reset text color
        $this->SetTextColor(0, 0, 0);
    }

    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        
        // Page number
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Initialize PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Table Content
$rowNumber = 1;
foreach ($details as $detail) {
    $date = new DateTime($detail['date']);
    $dayOfMonth = $date->format('j');
    $formattedDate = $dayOfMonth . ' ' . $monthNames[$schedule['month']-1] . ' ' . $schedule['year'] . "\n" . $detail['pasaran'];
    
    // Preacher row
    $pdf->Cell(10, 10, $rowNumber, 1, 0, 'C');
    $pdf->Cell(50, 10, $formattedDate, 1, 0, 'C');
    $pdf->Cell(70, 10, 'Penceramah: ' . $detail['preacher'], 1, 0, 'L');
    $pdf->Cell(30, 10, 'Rp. 300.000', 1, 0, 'R');
    $pdf->Cell(30, 10, '', 1, 1, 'L');
    
    // Muadzin row
    $pdf->Cell(10, 10, '', 1, 0, 'C');
    $pdf->Cell(50, 10, '', 1, 0, 'C');
    $pdf->Cell(70, 10, 'Muadzin: ' . $detail['muadzin'], 1, 0, 'L');
    $pdf->Cell(30, 10, 'Rp. 100.000', 1, 0, 'R');
    $pdf->Cell(30, 10, '', 1, 1, 'L');
    
    $rowNumber++;
}

// Add footer text
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Tanda tangan tersebut sebagai bukti serah terima kepada yang bersangkutan', 0, 1, 'L');

// Output PDF
$filename = "Jadwal_Shalat_Jumat_" . $monthNames[$schedule['month']-1] . "_" . $schedule['year'] . ".pdf";
$pdf->Output('D', $filename);