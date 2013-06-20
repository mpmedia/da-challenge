<?php

namespace DarwinAnalytics;

class Mailr {
    private $imap;

    public function __construct($provider, $mailbox, $username, $password) {
        $this->imap = imap_open($provider.$mailbox, $username, $password);
    }

    public function getMailHeaders($limit = null) {
        $imap = $this->imap;
        $mails = array();

        $numberOfMails = imap_num_msg($imap);
        for ($mailNumber=1; $mailNumber <= $numberOfMails; $mailNumber++) { 
            $header = imap_header($imap, $mailNumber);

            $uid = imap_uid($imap, $mailNumber);

            $mails[$uid] = array(
                'subject' => $header->subject
                ); 
        }
        return $mails;
    }
}