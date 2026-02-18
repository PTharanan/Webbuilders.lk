<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - WEBbuilders.lk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            max-width: 450px;
            width: 90%;
            padding: 32px;
            animation: fadeInScale 0.4s ease-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .success-icon-wrapper {
            width: 70px;
            height: 70px;
            background-color: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon-inner {
            width: 40px;
            height: 40px;
            background-color: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .receipt-summary {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
        }

        .summary-label {
            color: #6b7280;
            font-size: 13px;
        }

        .summary-value {
            color: #111827;
            font-size: 14px;
            font-weight: 500;
        }

        .amount-value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .txn-badge {
            background-color: #f3f4f6;
            padding: 4px 10px;
            border-radius: 100px;
            font-size: 11px;
            color: #374151;
            font-weight: 500;
        }

        .info-badge {
            background-color: #f0f9ff;
            color: #0369a1;
            padding: 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .btn-black {
            background-color: #0f172a;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
            transition: all 0.2s;
        }

        .btn-black:hover {
            background-color: #1e293b;
        }

        .btn-white {
            background-color: white;
            color: #0f172a;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        @media (max-height: 600px) {
            body {
                overflow-y: auto;
                align-items: flex-start;
            }

            .success-card {
                margin: 20px 0;
            }
        }

        @media print {

            .btn-black,
            .btn-white {
                display: none !important;
            }

            body {
                background-color: white;
                align-items: flex-start;
            }

            .success-card {
                box-shadow: none;
                width: 100%;
                max-width: none;
                border: 1px solid #eee;
            }
        }
    </style>
</head>

<body>
    <div class="success-card">
        <div class="text-center">
            <div class="success-icon-wrapper">
                <div class="success-icon-inner">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h1>
            <p class="text-gray-500 text-xs px-4 leading-relaxed">Your payment has been processed successfully. You'll
                receive a confirmation email shortly.</p>
        </div>

        <div class="receipt-summary">
            <div class="summary-row border-b border-gray-100 pb-3 mb-3">
                <span class="summary-label">Amount</span>
                <span class="amount-value" id="displayAmount">LKR 0.00</span>
            </div>

            <div class="summary-row">
                <span class="summary-label">Transaction ID</span>
                <span class="txn-badge" id="displayTxn">TXN-000000000</span>
            </div>

            <div class="summary-row">
                <span class="summary-label">Payment Method</span>
                <span class="summary-value">**** 4242</span>
            </div>

            <div class="summary-row">
                <span class="summary-label">Date</span>
                <span class="summary-value">
                    <?php echo date('M d, Y'); ?>
                </span>
            </div>

            <div class="summary-row">
                <span class="summary-label">Merchant</span>
                <span class="summary-value">WEBbuilders.lk</span>
            </div>
        </div>

        <div class="info-badge">
            <i class="far fa-envelope text-blue-500"></i>
            <span id="displayEmail">Receipt sent to your email</span>
        </div>

        <div class="flex flex-col gap-2">
            <button class="btn-black" onclick="window.print()">
                <i class="fas fa-download"></i>
                Download Receipt
            </button>
            <a href="home.php" class="btn-white">
                <i class="fas fa-arrow-left"></i>
                Return to Store
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Try to load any stored checkout data
            const checkoutData = JSON.parse(localStorage.getItem('checkoutData'));
            if (checkoutData) {
                if (checkoutData.total) {
                    document.getElementById('displayAmount').textContent = 'LKR ' + parseFloat(checkoutData.total).toLocaleString(undefined, { minimumFractionDigits: 2 });
                }

                // Randomly generate a transaction ID for the demo if not present
                const txnId = 'TXN-' + Math.floor(Math.random() * 1000000000);
                document.getElementById('displayTxn').textContent = txnId;

                // Clear checkout data after showing success
                // localStorage.removeItem('checkoutData');
            }
        });
    </script>

</body>

</html>