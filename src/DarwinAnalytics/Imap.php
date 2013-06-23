<?php

namespace DarwinAnalytics;

class Imap {

    private $imapStream;
    private static $count = 0;

    public function __construct() {
    }

    public function open($provider, $username, $password) {
        $this->imapStream = @imap_open($provider.'INBOX', $username, $password);
        if(!$this->imapStream) {
            throw new \Exception(preg_replace(array('/\[\D*\]\s*/','/\s*\(\D*\)\s*/'), '', imap_errors()[0]));
        }
    }

    public function close() {
        imap_close($this->imapStream);
    }

    public function isOpen() {
        return $this->isOpen;
    }

    public function getMailHeaders($limit = null) {
        $imapStream = $this->imapStream;
        if(!isset($imapStream)) {
            throw new \Exception('Imap stream is not open');
        }
        $mails = array();

        $numberOfMails = imap_num_msg($imapStream);
        for ($mailNumber=1; $mailNumber <= $numberOfMails; $mailNumber++) { 
            $header = imap_header($imapStream, $mailNumber);

            $uid = imap_uid($imapStream, $mailNumber);

            $mails[$uid] = array(
                'subject' => $header->subject,
                'date' => date('G:i:s, j F Y', $header->udate),
                'from' => $header->from,
                'uid' => $uid
                ); 
        }
        return $mails;
    }

    public function getMail($uid) {
        $imapStream = $this->imapStream;
        if(!isset($imapStream)) {
            throw new \Exception('Imap stream is not open');
        }
        return imap_fetchstructure($this->imapStream, $uid, FT_UID);
    }

    public function getImapStream() {
        return $this->imapStream;
    }
}