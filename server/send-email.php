<?php

require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $childBirthYear = isset($_POST['childBirthYear']) ? trim($_POST['childBirthYear']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $privacyConsent = isset($_POST['privacyConsent']) ? $_POST['privacyConsent'] : 'false';

    if (empty($name) || empty($phone) || $privacyConsent !== 'true') {
        http_response_code(400);
        echo json_encode(['error' => 'Имя родителя, телефон и согласие с политикой обработки персональных данных обязательны']);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL);

        $mail->Subject = 'Новая заявка с сайта "Планета футбола"';
        $mail->Body = "Новая заявка на бронирование места в футбольном лагере\n\n" .
                      "Имя родителя: " . $name . "\n" .
                      "Год рождения ребенка: " . $childBirthYear . "\n" .
                      "Телефон: " . $phone . "\n" .
                      "Согласие с политикой обработки персональных данных: " . ($privacyConsent === 'true' ? 'Да' : 'Нет') . "\n" .
                      "Дата заявки: " . date('d.m.Y H:i:s') . "\n";

        $mail->send();
        $logMessage = date('[Y-m-d H:i:s]') . ' - Телефон: ' . $phone . ' - Статус: Успешно отправлено\n';
        file_put_contents('send-mail.log', $logMessage, FILE_APPEND);
        http_response_code(200);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $logMessage = date('[Y-m-d H:i:s]') . ' - Телефон: ' . $phone . ' - Статус: Ошибка отправки - ' . $mail->ErrorInfo . '\n';
        file_put_contents('send-mail.log', $logMessage, FILE_APPEND);
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка отправки: ' . $mail->ErrorInfo]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается']);
}
