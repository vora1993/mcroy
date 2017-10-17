<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface; 

use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mime\Mime;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class SendEmail extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    
    public function __invoke($subject, $html, $text, $attachments = null)
    {
        $sm = $this->serviceLocator->getServiceLocator();
        $viewHelperManager = $sm->get('ViewHelperManager');
        $config = $viewHelperManager->get("setting");
        $setting = $config();
        
        $email_type = $setting->email_type;
        $email_name = $setting->email_name;
        $email_host = $setting->email_host;
        $username   = $setting->email_username;
        $password   = $setting->email_password;
        $from       = $setting->email_from;
        $to         = $setting->email_to;
        $email_cc   = $setting->email_cc;
        
        if(!$to) $to = $setting->company_email;
        
        $message = new Mail\Message();
        $message->addTo($to);
        $message->addFrom($from, $setting->company_name);
        $message->setSubject($subject);
    
        // HTML part
        $htmlPart           = new MimePart($html);
        $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $htmlPart->type     = "text/html; charset=UTF-8";
    
        // Plain text part
        $textPart           = new MimePart($text);
        $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        $textPart->type     = "text/plain; charset=UTF-8";
    
        $body = new MimeMessage();
        if ($attachments) {
            // With attachments, we need a multipart/related email. First part
            // is itself a multipart/alternative message        
            $content = new MimeMessage();
            $content->addPart($textPart);
            $content->addPart($htmlPart);
    
            $contentPart = new MimePart($content->generateMessage());
            $contentPart->type = "multipart/alternative;\n boundary=\"" .
                $content->getMime()->boundary() . '"';
    
            $body->addPart($contentPart);
            $messageType = 'multipart/related';
    
            // Add each attachment
            if(count($attachments) > 0) {
                foreach ($attachments as $thisAttachment) {
                    $attachment = new MimePart(fopen($thisAttachment, 'r'));
                    $attachment->filename    = basename($thisAttachment);
                    $attachment->type        = Mime::TYPE_OCTETSTREAM;
                    $attachment->encoding    = Mime::ENCODING_BASE64;
                    $attachment->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $body->addPart($attachment);
                }
            }
        } else {
            // No attachments, just add the two textual parts to the body
            $body->setParts(array($textPart, $htmlPart));
            $messageType = 'multipart/alternative';
        }
    
        // attach the body to the message and set the content-type
        $message->setBody($body);
        $message->getHeaders()->get('content-type')->setType($messageType);
        $message->setEncoding('UTF-8');
        if($email_cc) $message->addCc($email_cc);
        
        if($email_type === 'smtp') {
            // Setup SMTP transport using LOGIN authentication
            $transport = new SmtpTransport();
            $options   = new SmtpOptions(array(
                'name'              => $email_name,
                'host'              => $email_host,
                'port'              => 465, // Notice port change for TLS is 587
                'connection_class'  => 'smtp',
                'connection_config' => array(
                    'username' => $username,
                    'password' => $password,
                    'ssl'      => 'ssl',
                ),
            ));
            $transport->setOptions($options);
        } else {
            $transport = new SendmailTransport();
        }
        $transport->send($message);
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
}
