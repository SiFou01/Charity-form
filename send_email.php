<?php
// filepath: c:\xampp\htdocs\Charity-form\send_email.php

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Extract form data
    $associationName = $data['associationName'] ?? '';
    $address = $data['address'] ?? '';
    $phone = $data['phone'] ?? '';
    $fax = $data['fax'] ?? '';
    $email = $data['email'] ?? '';
    $facebook = $data['facebook'] ?? '';
    $fieldsOfActivity = $data['fieldsOfActivity'] ?? '';
    $widowsOrphans = $data['widowsOrphans'] ?? '';
    $declarantName = $data['declarantName'] ?? '';
    $boardMembers = $data['boardMembers'] ?? [];

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

    // Prepare the email payload
    $payload = [
        'sender' => [
            'name' => 'Salsabil Charity',
            'email' => 'seifeddinebaghdad3127@gmail.com', // Replace with your sender email
        ],
        'to' => [
            [
                'email' => 'seifbaghdad01@gmail.com', // Replace with your recipient email
                'name' => 'Recipient Name', // Replace with recipient name
            ],
        ],
        'subject' => $subject,
        'htmlContent' => $message,
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
    echo json_encode(['message' => 'Invalid data']);
}
?>