<?php
// Database connection
require_once 'config/database.php';

// Javanese calendar cycle
$pasaranCycle = ['Legi', 'Pahing', 'Pon', 'Wage', 'Kliwon'];

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

// Check if editing existing schedule
$editMode = false;
$scheduleId = null;
$existingMuadzins = [];
$year = date('Y');
$month = date('n') - 1; // 0-based month for JavaScript

if (isset($_GET['id'])) {
    $scheduleId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = ?");
    $stmt->execute([$scheduleId]);
    $schedule = $stmt->fetch();

    if ($schedule) {
        $editMode = true;
        $year = $schedule['year'];
        $month = $schedule['month'];

        // Get existing muadzins
        $stmt = $pdo->prepare("SELECT * FROM schedule_details WHERE schedule_id = ?");
        $stmt->execute([$scheduleId]);
        $details = $stmt->fetchAll();

        foreach ($details as $detail) {
            $day = date('j', strtotime($detail['date']));
            $existingMuadzins[$day] = $detail['muadzin'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = (int)$_POST['year'];
    $month = (int)$_POST['month'];
    $muadzins = $_POST['muadzins'] ?? [];

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Check if schedule exists for this month/year
        $stmt = $pdo->prepare("SELECT id FROM schedules WHERE year = ? AND month = ?");
        $stmt->execute([$year, $month]);
        $existingSchedule = $stmt->fetch();

        if ($existingSchedule) {
            // Update existing schedule
            $scheduleId = $existingSchedule['id'];
            $stmt = $pdo->prepare("UPDATE schedules SET updated_at = NOW() WHERE id = ?");
            $stmt->execute([$scheduleId]);

            // Delete existing details
            $stmt = $pdo->prepare("DELETE FROM schedule_details WHERE schedule_id = ?");
            $stmt->execute([$scheduleId]);
        } else {
            // Create new schedule
            $stmt = $pdo->prepare("INSERT INTO schedules (year, month) VALUES (?, ?)");
            $stmt->execute([$year, $month]);
            $scheduleId = $pdo->lastInsertId();
        }

        // Insert schedule details
        $stmt = $pdo->prepare("INSERT INTO schedule_details (schedule_id, date, pasaran, preacher, muadzin) VALUES (?, ?, ?, ?, ?)");

        foreach ($muadzins as $day => $muadzin) {
            $date = sprintf('%04d-%02d-%02d', $year, $month + 1, $day);
            $pasaran = calculatePasaran(strtotime($date));
            $preacher = determinePreacher($pasaran);

            $stmt->execute([$scheduleId, $date, $pasaran, $preacher, $muadzin]);
        }

        $pdo->commit();
        header("Location: view.php?id=$scheduleId");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Helper functions
function calculatePasaran($timestamp)
{
    global $pasaranCycle;

    // Reference date known to be "Legi": January 1, 2000
    $referenceDate = strtotime('2000-01-01');
    $referencePasaran = 0; // 0 = Legi

    // Calculate days since reference date
    $dayDiff = floor(($timestamp - $referenceDate) / (60 * 60 * 24));

    // Pasaran repeats every 5 days
    $pasaranIndex = ($referencePasaran + $dayDiff) % 5;
    return $pasaranCycle[$pasaranIndex];
}

function determinePreacher($pasaran)
{
    if ($pasaran === 'Legi' || $pasaran === 'Kliwon') {
        return 'Ustadz Wahidin';
    } else {
        return 'Ustadz Nawawi';
    }
}

function getFridaysInMonth($year, $month)
{
    $fridays = [];

    // Create date for first day of month (month is 0-based in JS but 1-based in PHP)
    $date = new DateTime("$year-" . ($month + 1) . "-01");

    // Find all Fridays (5 = Friday in date('w') format)
    while ($date->format('n') == $month + 1) {
        if ($date->format('w') == 5) {
            $fridays[] = [
                'date' => $date->format('Y-m-d'),
                'day' => (int)$date->format('j'),
                'pasaran' => calculatePasaran($date->getTimestamp())
            ];
        }
        $date->modify('+1 day');
    }

    return $fridays;
}

// Get Fridays for the selected month
$fridays = getFridaysInMonth($year, $month);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Jadwal Shalat Jumat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="container max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-green-800 mb-8">Generator Jadwal Shalat Jumat</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8 border-t-4 border-green-600">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">
                    <?php echo $editMode ? 'Edit Jadwal' : 'Buat Jadwal Baru'; ?>
                </h2>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                    Kembali
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Tahun:</label>
                        <input type="number" id="year" name="year" min="1900" max="2100" value="<?php echo $year; ?>"
                            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Bulan:</label>
                        <select id="month" name="month"
                            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php for ($i = 0; $i < 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $month ? 'selected' : ''; ?>>
                                    <?php echo $monthNames[$i]; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Input Muadzin untuk Jumat di Bulan
                        <span id="selectedMonth"><?php echo $monthNames[$month]; ?></span>
                        <span id="selectedYear"><?php echo $year; ?></span>
                    </h2>

                    <?php if (count($fridays) > 0): ?>
                        <div class="space-y-3 mb-6">
                            <?php foreach ($fridays as $friday): ?>
                                <?php
                                $badgeClass = '';
                                switch ($friday['pasaran']) {
                                    case 'Legi':
                                        $badgeClass = 'bg-pink-100 text-pink-800';
                                        break;
                                    case 'Pahing':
                                        $badgeClass = 'bg-purple-100 text-purple-800';
                                        break;
                                    case 'Pon':
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'Wage':
                                        $badgeClass = 'bg-teal-100 text-teal-800';
                                        break;
                                    case 'Kliwon':
                                        $badgeClass = 'bg-orange-100 text-orange-800';
                                        break;
                                }

                                $existingValue = isset($existingMuadzins[$friday['day']]) ? $existingMuadzins[$friday['day']] : '';
                                ?>
                                <div class="flex flex-col md:flex-row md:items-center gap-2">
                                    <div class="min-w-44">
                                        <?php echo $friday['day']; ?> <?php echo $monthNames[$month]; ?> <?php echo $year; ?>
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full <?php echo $badgeClass; ?>">
                                            <?php echo $friday['pasaran']; ?>
                                        </span>
                                    </div>
                                    <input type="text" name="muadzins[<?php echo $friday['day']; ?>]" placeholder="Nama Muadzin"
                                        value="<?php echo htmlspecialchars($existingValue); ?>"
                                        class="flex-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-gray-500">
                            Tidak ada hari Jumat di bulan ini.
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-md transition duration-200">
                    <?php echo $editMode ? 'Update Jadwal' : 'Simpan Jadwal'; ?>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Update selected month and year display when inputs change
        document.getElementById('year').addEventListener('change', updateSelectedDate);
        document.getElementById('month').addEventListener('change', updateSelectedDate);

        function updateSelectedDate() {
            const year = document.getElementById('year').value;
            const monthSelect = document.getElementById('month');
            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April',
                'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'
            ];

            document.getElementById('selectedMonth').textContent = monthNames[monthSelect.value];
            document.getElementById('selectedYear').textContent = year;

            // Reload page with new year/month to update Fridays
            window.location.href = `generator.php?year=${year}&month=${monthSelect.value}${<?php echo $editMode ? "+'&id=$scheduleId'" : "''"; ?>}`;
        }
    </script>
</body>

</html>