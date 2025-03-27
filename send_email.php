<?php
require 'vendor/autoload.php'; // Include Brevo SDK via Composer

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log all POST data for debugging
    error_log('POST Data: ' . print_r($_POST, true));

    // Collect form data
    $associationName = $_POST['associationName'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $fax = $_POST['fax'] ?? '';
    $website = $_POST['website'] ?? '';
    $email = $_POST['email'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $fieldsOfActivity = $_POST['fieldsOfActivity'] ?? '';
    $widowsOrphans = $_POST['widowsOrphans'] ?? '';
    $declarantName = $_POST['declarantName'] ?? '';

    // Collect and format board members' data
    $boardMembers = [];
    if (isset($_POST['boardMembers']) && is_array($_POST['boardMembers'])) {
        foreach ($_POST['boardMembers'] as $index => $member) {
            $name = $member['name'] ?? 'N/A';
            $job = $member['job'] ?? 'N/A';
            $role = $member['role'] ?? 'N/A';
            $boardMembers[] = "Member " . ($index + 1) . ": $name, $job, $role";
        }
    }

    // Format board members as a plain list
    $boardMembersList = implode("\n", $boardMembers);

    // Log the declarant name and board members for debugging
    error_log("Declarant Name: $declarantName");
    error_log("Board Members: " . print_r($boardMembers, true));

    // Handle file upload
    $encodedFile = '';
    if (isset($_FILES['approvalDoc']) && $_FILES['approvalDoc']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['approvalDoc']['tmp_name'];
        $fileName = $_FILES['approvalDoc']['name'];
        $fileContent = file_get_contents($fileTmpPath);
        $encodedFile = base64_encode($fileContent);
    } else {
        error_log('Error uploading the PDF file.');
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
        <p><strong>Declarant Name:</strong> $declarantName</p>
        <h2>Board Members:</h2>
        <pre>$boardMembersList</pre>
    ";

    // Configure Brevo API
    $apiKey = '';
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
        echo 'success';
    } catch (Exception $e) {
        error_log('Error sending email: ' . $e->getMessage());
        echo 'error';
    }
} else {
    echo 'Invalid request method.';
}
?>
