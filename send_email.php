<?php
require 'vendor/autoload.php'; // Include Brevo SDK via Composer

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Brevo API key
    $apiKey = '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';

    // Collect form data
    $associationName = $_POST['associationName'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $fax = $_POST['fax'];
    $website = $_POST['website'] ?? 'N/A';
    $email = $_POST['email'];
    $facebook = $_POST['facebook'];
    $fieldsOfActivity = $_POST['fieldsOfActivity'];
    $widowsOrphans = $_POST['widowsOrphans'];

    // Handle file upload
    if (isset($_FILES['approvalDoc']) && $_FILES['approvalDoc']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['approvalDoc']['tmp_name'];
        $fileName = $_FILES['approvalDoc']['name'];
        $fileType = $_FILES['approvalDoc']['type'];
        $fileContent = file_get_contents($fileTmpPath);
        $encodedFile = base64_encode($fileContent);
    } else {
        die('Error uploading the PDF file.');
    }

    // Prepare email content
    $emailContent = "
        <h1>New Participation Form Submission</h1>
        <p><strong>Association Name:</strong> $associationName</p>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Fax:</strong> $fax</p>
        <p><strong>Website:</strong> $website</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Facebook:</strong> $facebook</p>
        <p><strong>Fields of Activity:</strong> $fieldsOfActivity</p>
        <p><strong>Widows and Orphans Section:</strong> $widowsOrphans</p>
    ";

    // Configure Brevo API
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
    $apiInstance = new TransactionalEmailsApi(new Client(), $config);

    // Prepare email data
    $emailData = [
        'sender' => ['name' => 'Salsabil Charity', 'email' => 'seifeddinebaghdad3127@gmail.com'],
        'to' => [['email' => 'seifbaghdad01@gmail.com', 'name' => 'Recipient Name']],
        'subject' => 'New Participation Form Submission',
        'htmlContent' => $emailContent,
        'attachment' => [
            [
                'content' => $encodedFile,
                'name' => $fileName
            ]
        ]
    ];

    try {
        $apiInstance->sendTransacEmail($emailData);
        echo 'Form submitted successfully and email sent.';
    } catch (Exception $e) {
        echo 'Error sending email: ', $e->getMessage();
    }
} else {
    echo 'Invalid request method.';
}
?>
