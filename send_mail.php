<?php
header('Content-Type: application/json; charset=utf-8');

// 許可されたメソッドのみ受付
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "不正なリクエストです。"]);
    exit;
}

// フォームデータの受け取りとエスケープ
$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8') : '';
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8') : '';
$tel = isset($_POST['tel']) ? htmlspecialchars(trim($_POST['tel']), ENT_QUOTES, 'UTF-8') : '';
$type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type']), ENT_QUOTES, 'UTF-8') : '';
$message_body = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8') : '';

// バリデーション
if (empty($name) || empty($email) || empty($type) || empty($message_body)) {
    echo json_encode(["success" => false, "message" => "必須項目が入力されていません。"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "有効なメールアドレスを入力してください。"]);
    exit;
}

// 送信先 (要件指定アドレス)
$to = "medyogentleman@gmail.com";
$subject = "【菅原接骨院HP】お問い合わせを受け付けました（$type）";

// メーラーのヘッダー設定
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// メール本文の組み立て
$mail_body = "菅原接骨院のホームページからお問い合わせがありました。\n\n";
$mail_body .= "【お名前】\n$name\n\n";
$mail_body .= "【メールアドレス】\n$email\n\n";
$mail_body .= "【電話番号】\n$tel\n\n";
$mail_body .= "【お問い合わせ種別】\n$type\n\n";
$mail_body .= "【お問い合わせ内容】\n$message_body\n";

// 送信実行
$send_success = @mail($to, $subject, $mail_body, $headers);

// ※ローカル環境等でメールサーバーが立っていない場合、mail() は false を返します。
// テスト用に、mail設定がない場合でも成功を返すように擬似化も可能ですが、
// セキュリティ・仕様上はちゃんとmailの結果を返します。
// （動作確認時に強制的にtrueにする場合は下の行のコメントを外してください）
// $send_success = true; 

if ($send_success) {
    echo json_encode(["success" => true, "message" => "送信が完了しました。"]);
} else {
    // ローカルテスト用にエラーを握りつぶして成功にする場合↓
    // echo json_encode(["success" => true, "message" => "【テスト通知】メール送信成功（疑似）"]);
    echo json_encode(["success" => false, "message" => "メール送信に失敗しました。サーバーのメール設定をご確認ください。"]);
}
?>
