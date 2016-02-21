<?php
class ZW_mail{

    var $mail = null;

    public function __construct() {
        require 'PHPMailer/PHPMailerAutoload.php';
        ini_set('date.timezone','Asia/Shanghai');
        $this->mail = new PHPMailer;
        $this->mail->CharSet = "UTF-8";
        //$this->mail->setLanguage('zh', 'PHPMailer/language/phpmailer.lang-zh.php');
        //$this->mail->SMTPDebug = 3;                      // Enable verbose debug output
        $this->mail->isSMTP();                           // Set mailer to use SMTP
        $this->mail->Host = 'smtp.qq.com';               // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                   // Enable SMTP authentication
        if (ONLINE_MODE){
            //线上使用醉晚官方账号
            $email = 'no-reply@sicun.org';
            $this->mail->Username = $email;    // SMTP username
            $this->mail->Password = 'Sicun520';       // SMTP password
            $this->mail->setFrom($email);
        } else {
            $email = '1464738780@qq.com';
            $this->mail->Username = $email;     // SMTP username
            $this->mail->Password = 'leecoder@lbt146';       // SMTP password
            $this->mail->setFrom($email);
        }
        $this->mail->SMTPSecure = 'tls';                 // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 587;                         // TCP port to connect to
    }

    public function get_mail(){
        return $this->mail;
    }
}
