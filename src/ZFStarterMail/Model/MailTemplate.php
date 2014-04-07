<?php

namespace ZFStarterMail\Model;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use DomainModel\Object\DomainModelMagic;
use Zend\ServiceManager\ServiceManager;

/**
 * Class MailTemplate
 * @property string id
 * @property string description
 * @property string subject
 * @property string bodyHtml
 * @property string bodyText
 * @property string alias
 * @property string fromEmail
 * @property string fromName
 * @property int    signature
 * @property string created
 * @property string updated
 * @property int    creator
 * @property int    updater
 * @property string status
 * @package Mail\Model
 */
class MailTemplate extends DomainModelMagic
{
    /** @var array */
    protected $primaryColumns = array(
        'id'
    );

    /**
     * Assign data to template
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function assign($name, $value)
    {
        $this->bodyHtml = str_replace("%" . $name . "%", $value, $this->bodyHtml);
        $this->bodyText = str_replace("%" . $name . "%", $value, $this->bodyText);
        $this->subject = str_replace("%" . $name . "%", $value, $this->subject);
        return $this;
    }

    /**
     * Send email
     *
     * @param ServiceManager $serviceManager
     */
    public function send(ServiceManager $serviceManager)
    {
        $config = $serviceManager->get('config');
        $transport = new SmtpTransport();
        $options = new SmtpOptions($config['mail']['transport']);
        $transport->setOptions($options);
        $message = new Message();
        $message->setEncoding("UTF-8");
        if (isset($config['mail']['headers'])) {
            foreach ($config['mail']['headers'] as $key => $value) {
                $message->getHeaders()->addHeaderLine($key, $value);
            }
        }

        //Set default email (from config)
        if (!$this->fromEmail && isset($config['mail']['defaultFrom']['email'])
            && $config['mail']['defaultFrom']['email']
        ) {
            $this->fromEmail = $config['mail']['defaultFrom']['email'];
        }
        //Set default name (from config)
        if (!$this->fromName && isset($config['mail']['defaultFrom']['name'])
            && $config['mail']['defaultFrom']['name']
        ) {
            $this->fromName = $config['mail']['defaultFrom']['name'];
        }

        $message = $this->populate($message);
        $transport->send($message);
    }

    /**
     * Populate mail message
     *
     * @param Message $message
     * @return Message
     */
    public function populate($message)
    {
        if ($this->fromEmail) {
            $message->setFrom($this->fromEmail, $this->fromName);
        }
        if ($this->toEmail) {
            $message->addTo($this->toEmail, $this->toName);
        }
        if ($this->subject) {
            $message->setSubject($this->subject);
        }

        $body = new MimeMessage();
        if ($this->bodyHtml) {
            $html = new MimePart($this->bodyHtml);
            $html->type = Mime::TYPE_HTML;
            $html->disposition = Mime::DISPOSITION_INLINE;
            $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $html->charset = 'utf8';
            $body->addPart($html);
        }

        if ($this->bodyText) {
            $text = new MimePart($this->bodyText);
            $text->type = Mime::TYPE_TEXT;
            $text->disposition = Mime::DISPOSITION_INLINE;
            $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $text->charset = 'utf8';
            $body->addPart($text);
        }
        $message->setBody($body);
        return $message;
    }
}
