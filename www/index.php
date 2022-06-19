<?php
mb_internal_encoding("UTF-8");


$to = 'mailcatcher@test.com';
$subject = 'これはmailcatcherのテストです。';
// $subject = 'nekoneko';
$message = 'mailcatcherのテスト';
$additional_headers = array(
  'From' => 'noreply@test.com',
  'Content-Type'=>'text/plain; charset=UTF-8', // mailhog対応
  'Content-Transfer-Encoding'=>'8bit' // mailhog対応
);


if (!mb_send_mail($to, $subject, $message, $additional_headers)) {
  print_r('メールの送信に失敗しました。');
} else {
  print_r('メールを送信しました');
}