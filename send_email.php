<?php
// filepath: c:\xampp\htdocs\Charity-form\send_email.php

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    $associationName = $_POST['associationName'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $fax = $_POST['fax'] ?? '';
    $email = $_POST['email'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $fieldsOfActivity = $_POST['fieldsOfActivity'] ?? '';
    $widowsOrphans = $_POST['widowsOrphans'] ?? '';
    $declarantName = $_POST['declarantName'] ?? '';
    $boardMembers = isset($_POST['boardMembers']) ? json_decode($_POST['boardMembers'], true) : [];

    // Check if a file was uploaded
    if (isset($_FILES['approvalDoc']) && $_FILES['approvalDoc']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['approvalDoc']['tmp_name'];
        $fileName = $_FILES['approvalDoc']['name'];
        $fileType = mime_content_type($fileTmpPath);
        $fileContent = base64_encode(file_get_contents($fileTmpPath)); // Encode file content in base64
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'No file uploaded or file upload error.']);
        exit;
    }

    // Prepare the email content
    $subject = "استمارة مشاركة جديدة من $associationName";
    $message = "
        <h1>استمارة مشاركة</h1>
        <p><strong>اسم الجمعية:</strong> $associationName</p>
        <p><strong>العنوان:</strong> $address</p>
        <p><strong>الهاتف:</strong> $phone</p>
        <p><strong>الفاكس:</strong> $fax</p>
        <p><strong>البريد الإلكتروني:</strong> $email</p>
        <p><strong>صفحة الفايسبوك:</strong> $facebook</p>
        <p><strong>ميادين النشاط:</strong> $fieldsOfActivity</p>
        <p><strong>موجز تعريفي:</strong> $widowsOrphans</p>
        <p><strong>اسم المصرح:</strong> $declarantName</p>
        <h2>الهيئة المديرة:</h2>
        <ul>
    ";

    foreach ($boardMembers as $member) {
        $message .= "<li>" . implode(' - ', $member) . "</li>";
    }

    $message .= "</ul>";

    // Brevo API endpoint and API key
    $url = 'https://api.brevo.com/v3/smtp/email';
    $apiKey = ''; // Replace with your Brevo API key

    // Prepare the email payload with multiple recipients
    $payload = [
        'sender' => [
            'name' => 'Salsabil Charity',
            'email' => 'seif@salsabilcharity.com', // Replace with your sender email
        ],
        'to' => [
            [
                'email' => 'seifbaghdad01@gmail.com', // First recipient email
                'name' => 'Recipient One', // First recipient name
            ],
            [
                'email' => 'seifeddinebaghdad3127@gmail.com', // Second recipient email
                'name' => 'Recipient Two', // Second recipient name
            ],
        ],
        'subject' => $subject,
        'htmlContent' => $message,
        'attachment' => [
            [
                'content' => $fileContent, // Base64-encoded file content
                'name' => $fileName, // File name
                'type' => $fileType, // MIME type
            ],
        ],
    ];

    // Send the email using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle the response
    if ($httpCode === 201) {
        http_response_code(200);
        echo json_encode(['message' => 'Email sent successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to send email', 'response' => $response]);
    }
} else {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid request method']);
}
?>