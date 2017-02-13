<?php

/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 6 $
 * $Author: PragmaMx $
 * $Date: 2015-07-08 09:07:06 +0200 (Mi, 08. Jul 2015) $
 */


/**
 * @see http://code.google.com/p/php-imap
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
 
 /*

    ALL - return all messages matching the rest of the criteria
    ANSWERED - die gesuchten Nachrichten wurden beantwortet
    BCC "text" - die gesuchten Nachrichten enthalten "text" im Bcc: Feld
    BEFORE "datum" - die gesuchten Nachrichten wurden vor "datum" gesendet
    BODY "text" - der Nachrichtenkrper enthlt "text"
    CC "text" - die gesuchten Nachrichten enthalten "text" im Cc: Feld
    DELETED - die gesuchten Nachrichten sind zur Lschung vorgemerkt
    FLAGGED - match messages with the \\FLAGGED (sometimes referred to as Important or Urgent) flag set
    FROM "text" - sucht Nachrichten mit "text" im Absender (From:)
    KEYWORD "text" - sucht Nachrichten mit Schlsselwort "text"
    NEW - die gesuchten Nachrichten sind neu
    OLD - die gesuchten Nachrichten sind nicht neu
    ON "datum" - die Nachrichten wurden am angegebenen "datum" gesendet
    RECENT - sucht Nachrichten deren \\RECENT Flag nocht gesetzt ist
    SEEN - sucht bereits gelesene Nachrichten (das \\SEEN Flag ist gesetzt)
    SINCE "datum" - sucht nach "datum" gesendete Nachrichten
    SUBJECT "text" - sucht Nachrichten mit "text" in der Betreffzeile
    TEXT "text" - sucht Nachrichten deren Text "text" enthlt
    TO "text" - sucht Nachrichten mit "text" im Empfnger (To:)
    UNANSWERED - sucht noch nicht beantwortete Nachrichten
    UNDELETED - sucht nicht zum Lschen vorgemerkte Nachrichten
    UNFLAGGED - sucht Nachrichten die nicht als wichtig markiert sind
    UNKEYWORD "text" - sucht Nachrichten in deren Schlsselwrtern "text" nicht enthalten ist
    UNSEEN - sucht ungelesene Nachrichten
 
 
 Liste mit Einstellungen fr Standard-Anbieter unter http://www.patshaping.de/hilfen_ta/pop3_smtp.htm
 
 */
 
class pmxMailParser {

//	private $imapPath;
//	private $login;
//	private $password;
//	private $mbox;
//	private $serverEncoding;
//	private $attachmentsDir;
//	private $inboxfolder;
//	private $mailerror;
//    protected $_configfile = '';
//    protected static $_config = array();
    private static $_defaults = array();
    private static $__set = array(); // Konfigurtion

	public function __construct($imapPath,$inbox, $login, $password, $attachmentsDir = false, $serverEncoding = 'iso-8859-1') {

		/* set defaults */
		self::$__set['server'] ="" ;
		self::$__set['login'] ="" ;
		self::$__set['password'] ="" ;
		self::$__set['port'] ="143" ;
		self::$__set['inboxfolder'] ="INBOX" ;
		self::$__set['timeout'] ="2" ;
		self::$__set['serverEncoding'] =$serverEncoding ;
		self::$__set['protocol'] ="imap" ;
		self::$__set['cert'] ="" ;
		self::$__set['ssl'] ="" ;
		self::$__set['attachmentsDir'] =$attachmentsDir ;

		$this->imapPath = $imapPath;
		$this->inboxfolder=$inbox;
		$this->login = $login;
		$this->password = $password;
		$this->serverEncoding = $serverEncoding;
		$this->timeout=2;
		$this->oldtimeout=ini_get('default_socket_timeout');
		ini_set('default_socket_timeout', $this->timeout);
		imap_timeout(IMAP_OPENTIMEOUT,$this->timeout);
		imap_timeout(IMAP_READTIMEOUT,$this->timeout);
		imap_timeout(IMAP_WRITETIMEOUT,$this->timeout);
		imap_timeout(IMAP_CLOSETIMEOUT,$this->timeout);
		if($attachmentsDir) {
			if(!is_dir($attachmentsDir)) {
				$this->writeError('Directory "' . $attachmentsDir . '" not found');
			}
			$this->attachmentsDir = realpath($attachmentsDir);
		}

		//$this->connect();
	}

    /**
     * pmxAdminForm::__get()
     *
     * @param mixed $value_name
     * @return
     */
    public function __get($value_name)
    {
        if (isset(self::$__set[$value_name])) {
            return self::$__set[$value_name];
        }
        return false;
    }

    /**
     * pmxAdminForm::__set()
     *
     * @param mixed $name
     * @param mixed $val
     * @return
     */
    public function __set($name, $val)
    {
        self::$__set[$name] = $val ;
    }

	/*  IMAP Functionen */

	public function connect() {
		
		pmxDebug::pause();
		@$this->mbox = @imap_open($this->imapPath.$this->inboxfolder, $this->login, $this->password);
    	pmxDebug::restore();
		
		if($this->mbox) {
			return true;
		}
		
		return false;
	}
	
	
//	private function writeError($error)
//	{
//		$this->mailerror[]=$error;
//	}
	
	public function getError()
	{
		return imap_last_error();
	}

	public function checkConnection() {
		if($this->mbox && !imap_ping($this->mbox)) {
			return $this->reconnect();
		}
		return true;
	}

	public function reconnect() {
		if($this->mbox) $this->closeConnection();
		return $this->connect();
	}

	public function close() {
		pmxDebug::pause();

		if($this->mbox) $this->closeConnection();

		pmxDebug::start();

		$this->mbox=false;
		return true;
	}

	public function num_recent() {
		if($this->mbox) return imap_num_recent($this->mbox);
		return 0;
	}

	public function getCheck() {
		if (!$this->checkConnection()) return false;
		$result = get_object_vars (imap_check($this->mbox));

		return $result;
	}
	
	public function getMailboxList() {
		if (!$this->checkConnection()) return false;
		$maplist=array();
		$list = imap_list($this->mbox, $this->imapPath, "*");
		if (is_array($list)) {
			foreach ($list as $val) {
				$maplist[]= imap_utf7_decode($val) ;
			}
		} else {
			//echo "imap_list failed: " . imap_last_error() . "\n";
		}
		return $maplist;
	}	

	public function getMailboxes() {
		if (!$this->checkConnection()) return false;
		$maplist=array();
		$list = imap_listmailbox($this->mbox, $this->imapPath, "*");
		if (is_array($list)) {
			foreach ($list as $key=>$val) {
				$maplist[$key]= imap_utf7_decode($val);
			}
		} else {
			//echo "imap_list failed: " . imap_last_error() . "\n";
		}
		return $maplist;
	}	
	
	public function addInboxFolder($newname) {
		if (!$this->checkConnection()) return false;
		$folder=$this->getMailboxes();
		if (!in_array($this->imapPath.$newname,$folder)) {
			@imap_createmailbox($this->mbox, imap_utf7_encode($this->imapPath.$newname));
			return true;
		}
		return false;
	}	
	
	public function searchMails($imapCriteria = 'ALL') {
		if (!$this->checkConnection()) return false;
		$mailsIds = imap_search($this->mbox, $imapCriteria, SE_FREE, $this->serverEncoding);

		return $mailsIds ? $mailsIds : array();
	}

	public function searchMailsUID($imapCriteria = 'ALL') {
		if (!$this->checkConnection()) return false;
		$mailsIds = imap_search($this->mbox, $imapCriteria, SE_UID, $this->serverEncoding);

		return $mailsIds ? $mailsIds : array();
	}

	public function deleteMail($mId) {
		if (!$this->checkConnection()) return false;
		imap_delete($this->mbox, $mId, FT_UID | CL_EXPUNGE);
		imap_expunge($this->mbox);
		return true;
	}

	public function moveMail($mId,$tomailbox) {
		if (!$this->checkConnection()) return false;
		$this->addInboxFolder($tomailbox);
		$result= imap_mail_move($this->mbox, imap_msgno($this->mbox,$mId), $tomailbox);
		if ($result) imap_expunge($this->mbox);
	}

	public function copyMail($mId,$tomailbox) {
		if (!$this->checkConnection()) return false;
		$this->addInboxFolder($tomailbox);
		$result= imap_mail_copy($this->mbox, imap_msgno($this->mbox,$mId), $tomailbox);
		return true;
	}
	
	public function setMailAsSeen($mId) {
		if (!$this->checkConnection()) return false;
		$this->setMailImapFlag($mId, '\\Seen');
	}

	/* Die mglichen Flags, die gesetzt werden knnen, sind \Seen, \Answered, \Flagged, \Deleted und \Draft, */
	public function setMailImapFlag($mId, $flag) {
		imap_setflag_full($this->mbox, $mId, $flag, ST_UID);
	}

	/* Die mglichen Flags, die zurckgesetzt werden knnen, sind \Seen, \Answered, \Flagged, \Deleted und \Draft, */
	public function resetMailImapFlag($mId, $flag) {
		imap_clearflag_full($this->mbox, $mId, $flag, ST_UID);
	}
	
	public function getUid($mId) {
		if (!$this->checkConnection()) return false;
		return imap_uid ($this->mbox, $mId);
	}
	private function getMailHeaders($mId) {
		if (!$this->checkConnection()) return false;
		$headers = imap_fetchheader($this->mbox, $mId, FT_UID);
		//preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)\r\n/m', $headers, $matches);
		if(!$headers) {
			throw new ImapMailboxException('Message with UID "' . $mId . '" not found');
		}
		return $headers;
	}
	
	public function getMailOverview($mId) {
		if (!$this->checkConnection()) return false;
		/*
		
			subject - die Betreffzeile der Nachricht
			from - der Absender
			to - der Empfnger
			date - Sendezeitpunkt der Nachricht
			message_id - die Message-ID der Nachricht
			references - die Nachricht bezieht sich auf eine andere Nachricht mit dieser Message-ID
			in_reply_to - die Nachricht ist eine Antwort auf eine andere Nachricht mit dieser Message-ID
			size - Gre der Nachricht in Bytes
			uid - die UID der Nachricht innerhalb des Postfachs
			msgno - die aktuelle Nachrichtennummer innerhalb des Postfachs
			recent - diese Nachricht ist als krzlich eingetroffen markiert
			flagged - diese Nachricht ist als wichtig markiert
			answered - diese Nachricht ist als beantwortet markiert
			deleted - diese Nachricht ist zur Lschung vorgemerkt
			seen - diese Nachricht ist als bereits gelesen markiert
			draft - diese Nachricht ist als Entwurf markiert
		 */		
		 
		$headers = imap_fetch_overview($this->mbox, $mId, FT_UID);

		if(!$headers) {
			throw new ImapMailboxException('Message with UID "' . $mId . '" not found');
		}
		return $headers;
	}
	public function getMailOverviewMid($mId) {
		if (!$this->checkConnection()) return false;
		/*
		
			subject - die Betreffzeile der Nachricht
			from - der Absender
			to - der Empfnger
			date - Sendezeitpunkt der Nachricht
			message_id - die Message-ID der Nachricht
			references - die Nachricht bezieht sich auf eine andere Nachricht mit dieser Message-ID
			in_reply_to - die Nachricht ist eine Antwort auf eine andere Nachricht mit dieser Message-ID
			size - Gre der Nachricht in Bytes
			uid - die UID der Nachricht innerhalb des Postfachs
			msgno - die aktuelle Nachrichtennummer innerhalb des Postfachs
			recent - diese Nachricht ist als krzlich eingetroffen markiert
			flagged - diese Nachricht ist als wichtig markiert
			answered - diese Nachricht ist als beantwortet markiert
			deleted - diese Nachricht ist zur Lschung vorgemerkt
			seen - diese Nachricht ist als bereits gelesen markiert
			draft - diese Nachricht ist als Entwurf markiert
		 */		
		 
		$headers = imap_fetch_overview($this->mbox, "$mId", FT_UID);

		if(!$headers) {
			//throw new ImapMailboxException('Message with MessageID "' . $mId . '" not found');
		}
		return $headers;
	}

	public function getMailHeaderInfo($mId) {
		if (!$this->checkConnection()) return false;
		/*
		 Die Ergebnisse werden in einem Objekt mit folgenden Eigenschaften zurckgegeben:
		
			toaddress - Inhalt des "To:" Felds (max. 1024 Zeichen)
			to - ein Array mit aus den einzelnen Empfngern aus dem "To:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			fromaddress - Inhalt des "From:" Felds (max. 1024 Zeichen)
			from - ein Array mit aus den einzelnen Empfngern aus dem "From:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			ccaddress - Inhalt des "Cc:" Felds (max. 1024 Zeichen)
			cc - ein Array mit aus den einzelnen Empfngern aus dem "Cc:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			bccaddress - Inhalt des "Bcc:" Felds (max. 1024 Zeichen)
			bcc - ein Array mit aus den einzelnen Empfngern aus dem "Bcc:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			reply_toaddress - Inhalt des "Reply-To:" Felds (max. 1024 Zeichen)
			reply_to - ein Array mit aus den einzelnen Empfngern aus dem "Reply-To:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			senderaddress - Inhalt des "Sender:" Felds (max. 1024 Zeichen)
			sender - ein Array mit aus den einzelnen Empfngern aus dem "Sender:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			return_pathaddress - Inhalt des "Return-Path:" Felds (max. 1024 Zeichen)
			return_path - ein Array mit aus den einzelnen Empfngern aus dem "Return-Path:" Feld. Jedes Array-Element hat die Eigenschaften personal, adl, mailbox und host
			remail -
			date - Sendedatum der Nachricht laut Kopfdaten
			Date - enthlt die gleichen Daten wie 'date'
			subject - die Betreffzeile der Nachricht
			Subject - enthlt die gleichen Daten wie 'subject'
			in_reply_to -
			message_id -
			newsgroups -
			followup_to -
			references -
			Recent - R wenn krzlich eingetroffen und gelesen, N wenn krzlich eingetroffen und ungelesen, ' ' wenn nicht krzlich eingetroffen
			Unseen - U wenn nicht krzlich eingetroffen UND ungelesen, ' ' wenn gelesen ODER ungelesen und krzlich eingetroffen.
			Flagged - F wenn als wichtig markiert, sonst ' '
			Answered - A wenn beantwortet, sonst' '
			Deleted - D wenn zum Lschen vorgemerkt, sonst ' '
			Draft - X wenn als Entwurf markiert, sonst ' '
			Msgno - die Nachrichtennummer
			MailDate -
			Size - Gre der Nachricht in Bytes
			udate - Sendedatum als Unix-Timestamp
			fetchfrom - "From:" Zeile limitiert auf fromlength Zeichen characters
			fetchsubject - "Subject:" Zeile limitiert auf subjectlength Zeichen
		 */		
		 
		$headers = imap_headerinfo($this->mbox, $mId);
		//$headers=decodeMimeStr($headers);
		
		if(!$headers) {
			throw new ImapMailboxException('Message with UID "' . $mId . '" not found');
		}
		
		$headers=$this->object_to_array($headers);
		$toarray=array();
		foreach ($headers['to'] as $tos) {
			$toarray[]=$this->object_to_array($tos);
		}
		$headers['to']=$toarray;
		
		$toarray=array();
		foreach ($headers['from'] as $tos) {
			$toarray[]=$this->object_to_array($tos);
		}
		$headers['from']=$toarray;
		
		$toarray=array();
		if (array_key_exists('cc',$headers) && is_array($headers['cc'])) {
			foreach ($headers['cc'] as $tos) {
				$toarray[]=$this->object_to_array($tos);
			}
			$headers['cc']=$toarray;
		}
		$toarray=array();
		if (array_key_exists('bcc',$headers) &&is_array($headers['bcc'])) {
			foreach ($headers['bcc'] as $tos) {
				$toarray[]=$this->object_to_array($tos);
			}
			$headers['bcc']=$toarray;
		}
		
		$toarray=array();
		if (array_key_exists('sender',$headers) &&is_array($headers['sender'])) {
			foreach ($headers['sender'] as $tos) {
				$toarray[]=$this->object_to_array($tos);
			}
			$headers['sender']=$toarray;
		}
		$toarray=array();
		if (array_key_exists('reply_to',$headers) &&is_array($headers['reply_to'])) {
			foreach ($headers['reply_to'] as $tos) {
				$toarray[]=$this->object_to_array($tos);
			}
			$headers['reply_to']=$toarray;
		}
		$toarray=array();
		if (array_key_exists('return_path',$headers) &&is_array($headers['return_path'])) {
			foreach ($headers['return_path'] as $tos) {
				$toarray[]=$this->object_to_array($tos);
			}
			$headers['return_path']=$toarray;
		}
		$headers['uid']=imap_uid ($this->mbox, $mId);
		return $headers;
	}
	

	public function getMail($mId, $filesave=false) {
		if (!$this->checkConnection()) return false;
		$head = imap_rfc822_parse_headers($this->getMailHeaders($mId));

		$mail = new IncomingMail();
		$mail->mId = $mId;
		$mail->date = date('Y-m-d H:i:s', isset($head->date) ? strtotime($head->date) : time());
		$mail->time = (isset($head->date)) ? strtotime($head->date) : time();
		$mail->subject = $this->decodeMimeStr($head->subject);
		$mail->fromName = isset($head->from[0]->personal) ? $this->decodeMimeStr($head->from[0]->personal) : null;
		$mail->fromAddress = strtolower($head->from[0]->mailbox . '@' . $head->from[0]->host);

		$toStrings = array();
		foreach($head->to as $to) {
			$toEmail = strtolower($to->mailbox . '@' . $to->host);
			$toName = isset($to->personal) ? $this->decodeMimeStr($to->personal) : null;
			$toStrings[] = $toName ? "$toName <$toEmail>" : $toEmail;
			$mail->to[$toEmail] = $toName;
		}
		$mail->toString = implode(', ', $toStrings);

		if(isset($head->cc)) {
			foreach($head->cc as $cc) {
				$mail->cc[strtolower($cc->mailbox . '@' . $cc->host)] = isset($cc->personal) ? $this->decodeMimeStr($cc->personal) : null;
			}
		}

		if(isset($head->reply_to)) {
			foreach($head->reply_to as $replyTo) {
				$mail->replyTo[strtolower($replyTo->mailbox . '@' . $replyTo->host)] = isset($replyTo->personal) ? $this->decodeMimeStr($replyTo->personal) : null;
			}
		}

		$struct = imap_fetchstructure($this->mbox, $mId, FT_UID);

		if(empty($struct->parts)) {
			$this->initMailPart($mail, $struct, 0,$filesave);
		}
		else {
			foreach($struct->parts as $partNum => $partStruct) {
				$this->initMailPart($mail, $partStruct, $partNum + 1,$filesave);
			}
		}

		$mail->textHtmlOriginal = $mail->textHtml;

		return $mail;
	}

	private function quoteAttachmentFilename($filename) {
		$replace = array('/\s/' => '_', '/[^0-9a-zA-Z_\.]/' => '', '/_+/' => '_', '/(^_)|(_$)/' => '');

		return preg_replace(array_keys($replace), $replace, $filename);
	}

	private function initMailPart(IncomingMail $mail, $partStruct, $partNum,$filesave=false) {
		$data = $partNum ? imap_fetchbody($this->mbox, $mail->mId, $partNum, FT_UID) : imap_body($this->mbox, $mail->mId, FT_UID);

		if($partStruct->encoding == 1) {
			$data = imap_utf8($data);
		}
		elseif($partStruct->encoding == 2) {
			$data = imap_binary($data);
		}
		elseif($partStruct->encoding == 3) {
			$data = imap_base64($data);
		}
		elseif($partStruct->encoding == 4) {
			$data = imap_qprint($data);
		}
		$data = trim($data);
		$part_len=strlen($data);
		$params = array();
		if(!empty($partStruct->parameters)) {
			foreach($partStruct->parameters as $param) {
				$params[strtolower($param->attribute)] = $param->value;
			}
		}
		if(!empty($partStruct->dparametersx)) {
			foreach($partStruct->dparameters as $param) {
				$params[strtolower($param->attribute)] = $param->value;
			}
		}
		if(!empty($params['charset'])) {
			$data1 = @iconv($params['charset'], $this->serverEncoding."//TRANSLIT", $data);
			$data= (!$data1) ? $data:$data1;
		}

		// attachments
		if($this->attachmentsDir) {
			$filename = false;
			$attachmentId = $partStruct->ifid ? trim($partStruct->id, " <>") : null;
			if(empty($params['filename']) && empty($params['name']) && $attachmentId) {
				$filename = $attachmentId . '.' . strtolower($partStruct->subtype);
			}
			elseif(!empty($params['filename']) || !empty($params['name'])) {
				$filename = !empty($params['filename']) ? $params['filename'] : $params['name'];
				$filename = $this->decodeMimeStr($filename);
				$filename = $this->quoteAttachmentFilename($filename);
			}
			
			
			$tempfile=$mail->mId."-".$partNum."-".$filename; /* mod */
			$mimetype = $this->decodeMimeStr($filename);
			
			if($filename) {
				if($this->attachmentsDir) {
					
					/* original 
					$filepath = rtrim($this->attachmentsDir, '/\\') . DIRECTORY_SEPARATOR . $filename;
					file_put_contents($filepath, $data);
					$mail->attachments[$filename] = $filepath;
					*/
					
					/* mod for pmx by terraproject */
					$filepath = rtrim($this->attachmentsDir, '/\\') . DIRECTORY_SEPARATOR . $tempfile;
					
					if ($filesave) {
							file_put_contents($filepath, $data);
							list($width, $height, $mimetype, $attr) = getimagesize($filepath);
							$degrees=270;
					}	
						//$mimetype=image_type_to_mime_type ( $mimetype );
						
						$mail->attachments[$tempfile]['filepath'] = $filepath;
						$mail->attachments[$tempfile]['filename'] = $tempfile;
						$mail->attachments[$tempfile]['filetitle'] = $filename;
						$mail->attachments[$tempfile]['mimetyp'] = $mimetype;
						$mail->attachments[$tempfile]['filelen'] = $part_len;
				} else {
					$mail->attachments[$tempfile]['filename'] = $filename;
					$mail->attachments[$tempfile]['filetitle'] = $filename;
					$mail->attachments[$tempfile]['filepath'] = false;
					$mail->attachments[$tempfile]['mimetyp'] = $mimetype;
					$mail->attachments[$tempfile]['filelen'] = $part_len;
				}
				if($attachmentId) {
					$mail->attachmentsIds[$tempfile]['filename'] = $attachmentId;
					$mail->attachments[$tempfile]['filelen'] = $part_len;
				}
			}
		}
		if($partStruct->type == 0 && $data) {
			if(strtolower($partStruct->subtype) == 'plain') {
				$mail->textPlain .= $data;
			}
			else {
				$mail->textHtml .= $data;
			}
		}
		elseif($partStruct->type == 2 && $data) {
			$mail->textPlain .= trim($data);
		}
		if(!empty($partStruct->parts)) {
			foreach($partStruct->parts as $subpartNum => $subpartStruct) {
				$this->initMailPart($mail, $subpartStruct, $partNum . '.' . ($subpartNum + 1));
			}
		}
	}

	public function decodeMimeStr($string) {
		$newString = '';
		$charset=$this->serverEncoding;
		$elements = imap_mime_header_decode($string);
		for($i = 0; $i < count($elements); $i++) {
			if($elements[$i]->charset == 'default') {
				$elements[$i]->charset = 'iso-8859-1';
			}
			$newString .=($elements[$i]->charset !=$charset)? @iconv($elements[$i]->charset, $charset."//TRANSLIT", $elements[$i]->text):$elements[$i]->text;
		}
		
		return $newString;
	}

	private function closeConnection() {
		if($this->mbox) {
		pmxDebug::pause();

			$errors = imap_errors();
			if($errors) {
				foreach($errors as $error) {
					trigger_error($error);
				}
			}
			@imap_close($this->mbox);
		pmxDebug::start();
			return true;
		}
		return false;
	}

	private function object_to_array($Class){
				# Typecast to (array) automatically converts stdClass -> array.
				$Class = (array)$Class;
			   
				# Iterate through the former properties looking for any stdClass properties.
				# Recursively apply (array).
				foreach($Class as $key => $value){
					if(is_object($value) && get_class($value)==='stdClass'){
						$Class[$key] = $this->object_to_array($value);
					}					
				}
				return $Class;
	}

	public function __destruct() {
		$this->closeConnection();
		
	}
	
}

class IncomingMail {

	public $mId;
	public $date;
	public $time;
	public $subject;

	public $fromName;
	public $fromAddress;

	public $to = array();
	public $toString;
	public $cc = array();
	public $replyTo = array();

	public $textPlain;
	public $textHtml;
	public $textHtmlOriginal;
	public $attachments = array();
	public $attachmentsIds = array();

	public function fetchMessageInternalLinks($baseUrl) {
		if($this->textHtml) {
			foreach($this->attachments as $filepath) {
				$filename = basename($filepath['filepath']);
				if(isset($this->attachmentsIds[$filename])) {
					$this->textHtml = preg_replace('/(<img[^>]*?)src=["\']?ci?d:' . preg_quote($this->attachmentsIds[$filename]) . '["\']?/is', '\\1 src="' . $baseUrl . $filename . '"', $this->textHtml);
				}
			}
		}
	}

	public function fetchMessageHtmlTags($stripTags = array('html', 'body', 'head', 'meta')) {
		if($this->textHtml) {
			foreach($stripTags as $tag) {
				$this->textHtml = preg_replace('/<\/?' . $tag . '.*?>/is', '', $this->textHtml);
			}
			$this->textHtml = trim($this->textHtml, " \r\n");
		}
	}
}

class ImapMailboxException extends Exception {

/* todo */

}



/*
require_once('../Imap.php');

// IMAP must be enabled in Google Mail Settings
define('GMAIL_EMAIL', 'some@gmail.com');
define('GMAIL_PASSWORD', 'somepassword');
define('ATTACHMENTS_DIR', dirname(__FILE__) . '/attachments');

$mailbox = new ImapMailbox('{imap.gmail.com:993/imap/novalidate-cert/ssl}INBOX', GMAIL_EMAIL, GMAIL_PASSWORD, ATTACHMENTS_DIR, 'utf-8');
$mails = array();

foreach($mailbox->searchMails('ALL') as $mailId) {
	$mail = $mailbox->getMail($mailId);
	// $mailbox->setMailAsSeen($mail->mId);
	// $mailbox->deleteMail($mail->mId);
	$mails[] = $mail;
}

var_dump($mails);
*/ 
?>