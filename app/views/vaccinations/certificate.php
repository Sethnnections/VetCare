<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Certificate - <?php echo $vaccination['animal_name']; ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 20px solid #28a745;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 2.5em;
        }
        .header h2 {
            color: #6c757d;
            margin: 10px 0 0 0;
            font-size: 1.2em;
        }
        .content {
            margin: 30px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            color: #28a745;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .signature-area {
            margin-top: 50px;
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
        }
        .signature {
            text-align: center;
            margin-top: 60px;
        }
        .stamp {
            position: absolute;
            bottom: 40px;
            right: 40px;
            width: 150px;
            height: 150px;
            border: 3px solid #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            opacity: 0.8;
        }
        .stamp-text {
            color: #dc3545;
            font-weight: bold;
            text-align: center;
            font-size: 14px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-height: 80px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .certificate {
                border: 15px solid #28a745;
                box-shadow: none;
                margin: 0;
                padding: 30px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Print Button -->
        <div class="no-print" style="text-align: center; margin-bottom: 20px;">
            <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Print Certificate
            </button>
            <button onclick="window.close()" class="btn btn-secondary" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                Close
            </button>
        </div>

        <!-- Logo -->
        <div class="logo">
            <h1 style="color: #28a745; margin: 0;">Veterinary Clinic</h1>
            <p style="color: #6c757d; margin: 5px 0;">Professional Animal Healthcare</p>
        </div>

        <!-- Header -->
        <div class="header">
            <h1>VACCINATION CERTIFICATE</h1>
            <h2>Official Record of Animal Vaccination</h2>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Animal Information -->
            <div class="section">
                <h3 class="section-title">Animal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Animal Name:</span>
                        <span class="info-value"><?php echo $vaccination['animal_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Species:</span>
                        <span class="info-value"><?php echo $vaccination['species']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Breed:</span>
                        <span class="info-value"><?php echo $vaccination['breed'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value"><?php echo $vaccination['birth_date'] ? formatDate($vaccination['birth_date']) : 'Unknown'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Color/Markings:</span>
                        <span class="info-value"><?php echo $vaccination['color'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Microchip ID:</span>
                        <span class="info-value"><?php echo $vaccination['microchip_id'] ?? 'Not specified'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Vaccination Details -->
            <div class="section">
                <h3 class="section-title">Vaccination Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Vaccine Name:</span>
                        <span class="info-value"><?php echo $vaccination['vaccine_name']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Vaccine Type:</span>
                        <span class="info-value"><?php echo $vaccination['vaccine_type'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Manufacturer:</span>
                        <span class="info-value"><?php echo $vaccination['manufacturer'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Batch Number:</span>
                        <span class="info-value"><?php echo $vaccination['batch_number'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Vaccination Date:</span>
                        <span class="info-value"><?php echo formatDate($vaccination['vaccine_date']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Next Due Date:</span>
                        <span class="info-value"><?php echo formatDate($vaccination['next_due_date']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dosage:</span>
                        <span class="info-value"><?php echo $vaccination['dosage'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Route:</span>
                        <span class="info-value"><?php echo $vaccination['route'] ?? 'Not specified'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Medical Professional -->
            <div class="section">
                <h3 class="section-title">Administration Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Administered By:</span>
                        <span class="info-value"><?php echo $vaccination['administered_by_name'] ?? 'Not specified'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">License Number:</span>
                        <span class="info-value"><?php echo $vaccination['license_number'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Clinic Name:</span>
                        <span class="info-value">Veterinary Clinic</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Clinic Address:</span>
                        <span class="info-value">123 Veterinary Street, Animal City, AC 12345</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <?php if (!empty($vaccination['notes'])): ?>
            <div class="section">
                <h3 class="section-title">Additional Notes</h3>
                <p style="color: #6c757d; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($vaccination['notes'])); ?></p>
            </div>
            <?php endif; ?>

            <!-- Reaction Notes -->
            <?php if (!empty($vaccination['reaction_notes'])): ?>
            <div class="section">
                <h3 class="section-title" style="color: #dc3545;">Reaction Notes</h3>
                <p style="color: #dc3545; line-height: 1.6; font-style: italic;"><?php echo nl2br(htmlspecialchars($vaccination['reaction_notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Signature -->
        <div class="signature-area">
            <div class="signature">
                <div style="border-bottom: 1px solid #495057; width: 300px; margin: 0 auto 10px;"></div>
                <p style="margin: 0; color: #495057;">Signature of Licensed Veterinarian</p>
                <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 0.9em;">
                    <?php echo $vaccination['administered_by_name'] ?? 'Veterinary Professional'; ?>
                </p>
            </div>
        </div>

        <!-- Official Stamp -->
        <div class="stamp no-print">
            <div class="stamp-text">
                OFFICIAL<br>
                VACCINATION<br>
                RECORD<br>
                <?php echo date('Y'); ?>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 40px; color: #6c757d; font-size: 0.8em;">
            <p>This certificate verifies that the above-named animal has been vaccinated in accordance with standard veterinary practices.</p>
            <p>Certificate generated on: <?php echo date('F j, Y \a\t g:i A'); ?></p>
            <p>Certificate ID: VC-<?php echo strtoupper(substr(md5($vaccination['vaccine_id'] . $vaccination['animal_id']), 0, 8)); ?></p>
        </div>
    </div>

    <script>
        // Auto-print if requested
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === 'true') {
            window.print();
        }

        // Close window after print
        window.onafterprint = function() {
            // window.close();
        };
    </script>
<div class="qr-code no-print" style="position: absolute; bottom: 40px; left: 40px;">
    <?php
    $certificateData = [
        'certificate_id' => 'VC-' . strtoupper(substr(md5($vaccination['vaccine_id'] . $vaccination['animal_id']), 0, 8)),
        'animal_name' => $vaccination['animal_name'],
        'vaccine_name' => $vaccination['vaccine_name'],
        'vaccine_date' => $vaccination['vaccine_date'],
        'next_due_date' => $vaccination['next_due_date'],
        'veterinary_clinic' => 'Veterinary Clinic'
    ];
    $qrContent = urlencode(json_encode($certificateData));
    ?>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $qrContent; ?>" 
         alt="Vaccination Certificate QR Code" style="border: 1px solid #ddd;">
</div>
</body>
</html>