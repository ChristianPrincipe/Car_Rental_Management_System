<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../vendor/autoload.php'; // mPDF
    require_once __DIR__ . '/../includes/db.php';     // Database connection

    $owner_id = $_SESSION['owner_id'] ?? null;
    if (!$owner_id) exit('Unauthorized access');

    // Fetch branch info for the logged-in owner
    $stmt = $pdo->prepare("SELECT branch_id, branch_name, branch_image FROM branches WHERE owner_id = ?");
    $stmt->execute([$owner_id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$branch) exit('Branch not found.');

    $branch_id = $branch['branch_id'];
    $branch_name = $branch['branch_name'];
    $branch_image = $branch['branch_image'];

    // Resolve image path for branch image (fallback empty string if file missing)
    $imagePath = realpath(__DIR__ . '/../uploads/' . $branch_image);
    if (!$imagePath || !file_exists($imagePath)) {
        $imagePath = '';  // Avoid broken image links
    }

    // Query to get all bookings with bookingStatus_id = 5 (booked)
    $stmt = $pdo->prepare("
        SELECT 
            rentals.rental_id, 
            cars.car_model, 
            rentaltype.rentalType_name, 
            customers.customer_name, 
            rentals.booking_date, 
            rentals.booking_time, 
            rentals.estimated_total, 
            bookingstatus.bookingStatus_name
        FROM rentals 
        JOIN cars ON rentals.car_id = cars.car_id
        JOIN rentaltype ON rentals.rentalType_id = rentaltype.rentalType_id
        JOIN customers ON rentals.customer_id = customers.customer_id
        JOIN bookingstatus ON rentals.bookingStatus_id = bookingstatus.bookingStatus_id
        WHERE cars.branch_id = ? AND rentals.bookingStatus_id = 5
    ");
    $stmt->execute([$branch_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize mPDF
    $mpdf = new \Mpdf\Mpdf();

    // Prepare HTML content for PDF
    $html = '
    <html>
    <head>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 13px;
                color: #000000;
                background-color: #ffffff;
                margin: 0;
                padding: 30px 40px;
            }
            p.branch-name {
                font-size: 30px;
                color: #333333;
                margin-top: 0;
                margin-bottom: 25px;
                font-weight: 600;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
            }
            th, td {
                border: 1px solid #000000;
                padding: 12px 15px;
                text-align: left;
                font-weight: 600;
                font-size: 13px;
            }
            th {
                // background-color: #D4AF37;
                color: #000000;
                letter-spacing: 0.1em;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tbody tr:hover {
                background-color: #ffe599;
            }
            .total-row {
                background-color: #D4AF37;
                color: #000000;
                font-weight: 700;
                font-size: 14px;
            }
            .signature-section {
                margin-top: 70px;
                text-align: center;
                font-size: 14px;
                color: #555555;
            }
            .signature-line {
                margin: 30px auto 10px;
                border-top: 2px solid #333333;
                width: 250px;
            }
            .footer {
                font-size: 11px;
                color: #777777;
                text-align: right;
                margin-top: 40px;
                border-top: 1px solid #000000;
                padding-top: 8px;
                font-style: italic;
            }
            .table-header-row {
                background-color: #D4AF37;
                color: #000000;
                font-weight: 700;
                font-size: 18px;
                text-align: center;
                letter-spacing: 2px;
            }
            .table-header-row th {
                background-color: #D4AF37;
                color: #000000;
                font-weight: 700;
                font-size: 20px;
                text-align: center;
                letter-spacing: 2px;
                padding: 15px;
            }
        </style>
    </head>
    <body>';

    // Logos and title row
    $logoLeft = realpath(__DIR__ . '/../onwer-landing/image/nav-bar-icon/car-rental-logo.png');
    if (!$logoLeft || !file_exists($logoLeft)) {
        $logoLeft = '';
    }

    $html .= '
    <table style="width: 100%; border-bottom: 2px #D4AF37; margin-bottom: 80px; border-collapse: collapse;">
        <tr>
            <td style="width: 80px; text-align: left; border: none;">' .
                ($logoLeft ? '<img src="' . $logoLeft . '" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;" />' : '') .
            '</td>
            <td style="text-align: center; border: none;">
                <p class="branch-name"><strong>' . htmlspecialchars($branch_name) . ' Report</strong></p>
            </td>
            <td style="width: 80px; text-align: right; border: none;">' .
                ($imagePath ? '<img src="' . $imagePath . '" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;" />' : '') .
            '</td>
        </tr>
    </table>';

    // Booked details table with heading row inside table
    $html .= '
    <table>
        <thead>
            <tr class="table-header-row">
                <th colspan="8"><h4>BOOKED DETAILS REPORT</h4></th>
            </tr>
            <tr>
                <th><strong>#</strong></th>
                <th><strong>Car Model</strong></th>
                <th><strong>Rental Type</strong></th>
                <th><strong>Customer</strong></th>
                <th><strong>Booking Date</strong></th>
                <th><strong>Booking Time</strong></th>
                <th><strong>Total</strong></th>
                <th><strong>Booking Status</strong></th>
            </tr>
        </thead>
        <tbody>';

    $count = 1;
    $totalSales = 0;

    foreach ($bookings as $booking) {
        $totalSales += $booking['estimated_total'];
        $html .= '
            <tr>
                <td>' . $count++ . '</td>
                <td>' . htmlspecialchars($booking['car_model']) . '</td>
                <td>' . htmlspecialchars($booking['rentalType_name']) . '</td>
                <td>' . htmlspecialchars($booking['customer_name']) . '</td>
                <td>' . htmlspecialchars($booking['booking_date']) . '</td>
                <td>' . htmlspecialchars($booking['booking_time']) . '</td>
                <td>₱' . number_format($booking['estimated_total'], 2) . '</td>
                <td>' . htmlspecialchars($booking['bookingStatus_name']) . '</td>
            </tr>';
    }

    $html .= '
            <tr class="total-row">
                <td colspan="6" style="text-align: right;">Total Sales:</td>
                <td colspan="2">₱' . number_format($totalSales, 2) . '</td>
            </tr>
        </tbody>
    </table>';

    // Signature section
    $html .= '
    <div class="signature-section">
        <div class="signature-line"></div>
        <p><strong>Owner / Manager Signature</strong></p>
    </div>

    </body>
    </html>';

    $mpdf->SetHTMLFooter('
    <div style="text-align: right; font-size: 11px; color: #777; font-style: italic; padding-top: 10px; border-top: 1px solid #000;">
        Page {PAGENO} of {nbpg}
    </div>
    ');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Booked_Report.pdf', 'D');
    exit;
}
?>
