<?php
header('Content-Type: application/json; charset=utf-8');

// 許可されたメソッドのみ受付
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "不正なリクエストです。"]);
    exit;
}

// フォームデータの受け取りとエスケープ
$referrer = isset($_POST['referrer']) ? htmlspecialchars(trim($_POST['referrer']), ENT_QUOTES, 'UTF-8') : '';
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8') : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8') : '';
$tel = isset($_POST['tel']) ? htmlspecialchars(trim($_POST['tel']), ENT_QUOTES, 'UTF-8') : '';
$concern = isset($_POST['concern']) ? htmlspecialchars(trim($_POST['concern']), ENT_QUOTES, 'UTF-8') : '';
$recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

// バリデーション
if (empty($referrer) || empty($name) || empty($email) || empty($tel) || empty($concern)) {
    echo json_encode(["success" => false, "message" => "必須項目が入力されていません。"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "有効なメールアドレスを入力してください。"]);
    exit;
}

// Google reCAPTCHA v2 の検証
// 【重要】 ご自身の「シークレットキー」に置き換えてください
$recaptchaSecret = '6LelJtwsAAAAAP6O3ymrIBWBVzqewzJyvaz-0QbT';
if (!empty($recaptchaSecret) && $recaptchaSecret !== 'YOUR_RECAPTCHA_SECRET_KEY') {
    if (empty($recaptchaResponse)) {
        echo json_encode(["success" => false, "message" => "スパムチェック（reCAPTCHA）にチェックを入れてください。"]);
        exit;
    }
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseData = json_decode($verifyResponse);
    if (!$responseData->success) {
        echo json_encode(["success" => false, "message" => "スパムチェックに失敗しました。再度お試しください。"]);
        exit;
    }
}

// 送信先
$to = "sugawaraclinic16@gmail.com";
$subject = "【菅原接骨院HP】ご予約・お問い合わせを受け付けました";

// メーラーのヘッダー設定
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// メール本文の組み立て
$mail_body = "ホームページよりご予約・お問い合わせがありました。\n\n";
$mail_body .= "【ご紹介者様名】\n$referrer\n\n";
$mail_body .= "【お名前】\n$name\n\n";
$mail_body .= "【メールアドレス】\n$email\n\n";
$mail_body .= "【電話番号】\n$tel\n\n";
$mail_body .= "【気になる箇所・症状】\n$concern\n";

// 送信実行
$send_success = @mail($to, $subject, $mail_body, $headers);

if ($send_success) {
    echo json_encode(["success" => true, "message" => "送信が完了しました。"]);
} else {
    echo json_encode(["success" => false, "message" => "メール送信に失敗しました。サーバーの設定をご確認ください。"]);
}
?>
