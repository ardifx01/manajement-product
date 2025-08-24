<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'shodiqsolution@gmail.com';
    public string $fromName   = 'Manajemen Product';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp'; // Diubah dari 'mail' ke 'smtp'

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = 'smtp.gmail.com';

    /**
     * SMTP Username
     */
    public string $SMTPUser = 'shodiqsolution@gmail.com';

    /**
     * SMTP Password
     */
    public string $SMTPPass = 'mlkgvycnkdfhtldh'; // Pastikan ini adalah App Password

    /**
     * SMTP Port
     */
    public int $SMTPPort = 587; // Port untuk TLS

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 60; // Ditambah dari 5 ke 60 untuk stabilitas

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * SMTP Encryption.
     */
    public string $SMTPCrypto = 'tls'; // TLS untuk port 587

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html'; // Disarankan 'html' untuk email modern

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = true; // Diubah ke true untuk validasi email

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $CRLF = "\r\n";

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $newline = "\r\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}