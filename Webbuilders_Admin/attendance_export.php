<?php
require_once 'auth_check.php';
require_once 'config.php';
require_once 'dbConnect.php';

// Get format and month/year
$format = isset($_GET['format']) ? $_GET['format'] : 'excel';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Validate month/year
if ($month < 1 || $month > 12) {
    $month = date('m');
    $year = date('Y');
}

// Get days in month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Get first and last day of the month
$monthStartDate = sprintf("%04d-%02d-01", $year, $month);
$monthEndDate = sprintf("%04d-%02d-%02d", $year, $month, $daysInMonth);

// Fetch employees who:
// 1. Joined on or before the end of the month (joining_date <= month_end)
// 2. AND (left_date is NULL OR left_date > month_start)
// This includes current employees and those who left after the month started
$stmt = $conn->prepare("
    SELECT DISTINCT e.id, e.name 
    FROM employees e
    WHERE e.joining_date <= :month_end 
    AND (e.left_date IS NULL OR e.left_date > :month_start)
    ORDER BY e.name ASC
");
$stmt->execute([
    ':month_start' => $monthStartDate,
    ':month_end' => $monthEndDate
]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to fetch attendance record
function getAttendanceRecord($conn, $employeeId, $year, $month, $day) {
    $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = :employee_id AND attendance_date = :attendance_date");
    $stmt->execute([
        ':employee_id' => $employeeId,
        ':attendance_date' => $dateStr
    ]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Build data array
$data = [];
foreach ($employees as $employee) {
    $employeeData = [
        'name' => $employee['name'],
        'days' => []
    ];
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $record = getAttendanceRecord($conn, $employee['id'], $year, $month, $day);
        $employeeData['days'][$day] = $record ? [
            'attendance' => $record['attendance'] ? 'Yes' : 'No',
            'tasklog' => $record['tasklog_submitted'] ? 'Yes' : 'No',
            'in_time' => $record['in_time'] ? date('g:i A', strtotime($record['in_time'])) : '-',
            'out_time' => $record['out_time'] ? date('g:i A', strtotime($record['out_time'])) : '-',
            'hours' => $record['working_hours'] ? round($record['working_hours'], 2) : '-'
        ] : [
            'attendance' => 'No',
            'tasklog' => 'No',
            'in_time' => '-',
            'out_time' => '-',
            'hours' => '-'
        ];
    }
    
    $data[] = $employeeData;
}

// Month display
$monthDisplay = date('F Y', mktime(0, 0, 0, $month, 1, $year));

if ($format === 'excel') {
    // Export to Excel
    require_once 'vendor/autoload.php';
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Attendance');
    
    // Set header with title
    $sheet->setCellValue('A1', 'Attendance Report - ' . $monthDisplay);
    
    // Calculate last column (A + daysInMonth)
    $lastCol = chr(64 + 1 + $daysInMonth); // A=65, so 64+1+days
    if ($daysInMonth > 26) {
        // For months with more than 26 days, use double letters
        $lastCol = 'A' . chr(64 + ($daysInMonth - 26));
    }
    
    $sheet->mergeCells('A1:' . $lastCol . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    
    // Column headers
    $col = 'A';
    $sheet->setCellValue($col . '2', 'Employee Name');
    $sheet->getStyle($col . '2')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
    $sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('374151');
    $sheet->getColumnDimension($col)->setWidth(25);
    
    // Add day headers
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $dayName = date('D', strtotime($dateStr));
        $dayOfWeek = date('w', strtotime($dateStr));
        $col++;
        $sheet->setCellValue($col . '2', "$day-$dayName");
        $sheet->getStyle($col . '2')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle($col . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getColumnDimension($col)->setWidth(15);
        
        // Color code header: red for Sunday, light colors for others
        if ($dayOfWeek == 0) { // Sunday
            $sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('EF4444'); // Red
        } else {
            $sheet->getStyle($col . '2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('3B82F6'); // Blue
        }
    }
    
    // Add data rows
    $row = 3;
    $today = new DateTime();
    $todayStr = $today->format('Y-m-d');
    
    foreach ($data as $employeeData) {
        $sheet->setCellValue('A' . $row, $employeeData['name']);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        $col = 'A';
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $col++;
            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $dayOfWeek = date('w', strtotime($dateStr));
            $dayData = $employeeData['days'][$day];
            
            // Determine status text
            $cellValue = '';
            $isToday = ($dateStr === $todayStr);
            $isFutureDate = (strtotime($dateStr) > strtotime($todayStr));
            
            // If date is today or future, don't show anything
            if ($isFutureDate) {
                $cellValue = '-';
            } else {
                // If attended
                if ($dayData['attendance'] === 'Yes') {
                    $cellValue = 'Present';
                    if ($dayData['tasklog'] === 'Yes') {
                        $cellValue .= "\nTasklog: Yes";
                    } else {
                        $cellValue .= "\nTasklog: No";
                    }
                    // Add in and out times
                    if ($dayData['in_time'] !== '-') {
                        $cellValue .= "\nIn: " . $dayData['in_time'];
                    }
                    if ($dayData['out_time'] !== '-') {
                        $cellValue .= "\nOut: " . $dayData['out_time'];
                    }
                } else {
                    // Not attended - show Absent
                    $cellValue = 'Absent';
                }
            }
            
            $sheet->setCellValue($col . $row, $cellValue);
            $sheet->getStyle($col . $row)->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(60);
            
            // Color code cells
            if ($dayOfWeek == 0) { // Sunday
                $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DC2626'); // Dark red
                $sheet->getStyle($col . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF')); // White text
            } else if ($dayData['attendance'] === 'Yes') {
                $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7'); // Light green
                $sheet->getStyle($col . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('166534')); // Dark green text
            } else {
                $sheet->getStyle($col . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F9FF'); // Light blue
            }
            
            // Add border
            $sheet->getStyle($col . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }
        
        $row++;
    }
    
    // Set file name and download
    $fileName = 'Attendance_Report_' . str_replace(' ', '_', $monthDisplay) . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} elseif ($format === 'pdf') {
    // Export to PDF
    require_once 'vendor/autoload.php';
    
    $pdf = new \TCPDF();
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(8, 8, 8);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage('L'); // Landscape
    $pdf->SetFont('helvetica', 'B', 14);
    
    // Title
    $pdf->Cell(0, 10, 'Attendance Report - ' . $monthDisplay, 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Ln(5);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(52, 73, 94);
    
    $pdf->Cell(30, 8, 'Employee', 1, 0, 'C', true);
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
        $dayName = date('D', strtotime($dateStr));
        $dayOfWeek = date('w', strtotime($dateStr));
        
        // Highlight Sunday headers in red
        if ($dayOfWeek == 0) {
            $pdf->SetFillColor(239, 68, 68); // Red for Sunday
        } else {
            $pdf->SetFillColor(59, 130, 246); // Blue for weekdays
        }
        $pdf->Cell(6.5, 8, "$day", 1, 0, 'C', true);
    }
    
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 6.5);
    $pdf->SetTextColor(0, 0, 0);
    
    // Table data
    $today = new DateTime();
    $todayStr = $today->format('Y-m-d');
    
    foreach ($data as $employeeData) {
        $pdf->Cell(30, 6, substr($employeeData['name'], 0, 25), 1, 0, 'L');
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $dayOfWeek = date('w', strtotime($dateStr));
            $dayData = $employeeData['days'][$day];
            
            // Determine what to show
            $cellValue = '';
            $isFutureDate = (strtotime($dateStr) > strtotime($todayStr));
            
            if ($isFutureDate) {
                $cellValue = '-';
            } else if ($dayData['attendance'] === 'Yes') {
                $cellValue = 'P'; // P for Present
            } else {
                $cellValue = 'A'; // A for Absent
            }
            
            // Set fill color based on Sunday or attendance
            if ($dayOfWeek == 0) { // Sunday
                $pdf->SetFillColor(220, 38, 38); // Dark red
                $pdf->SetTextColor(255, 255, 255); // White text
            } else {
                // Dark green for attended (P)
                if ($dayData['attendance'] === 'Yes') {
                    $pdf->SetFillColor(34, 197, 94); // Dark green
                    $pdf->SetTextColor(255, 255, 255); // White text
                } else {
                    // Light gray/blue for absent (A)
                    $pdf->SetFillColor(240, 249, 255); // Light blue
                    $pdf->SetTextColor(0, 0, 0);
                }
            }
            
            $pdf->Cell(6.5, 6, $cellValue, 1, 0, 'C', true);
        }
        
        $pdf->Ln();
    }
    
    // Set file name and download
    $fileName = 'Attendance_Report_' . str_replace(' ', '_', $monthDisplay) . '.pdf';
    $pdf->Output($fileName, 'D');
    exit;
}
?>
