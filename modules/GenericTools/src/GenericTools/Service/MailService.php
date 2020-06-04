<?php
/**
 * Created by PhpStorm.
 * User: amiel
 * Date: 10/12/18
 * Time: 14:36
 */

namespace GenericTools\Service;

use Laminas\View\Model\ViewModel;
use Interop\Container\ContainerInterface;


class MailService
{
    private $mailer;
    private $fromEmail;
    private $fromName;

    public function __construct(ContainerInterface $container)
    {
        $this->continer = $container;
        //Extend of PHPMiler for Openemr
        $this->mailer = new \MyMailer();
        $this->mailer->isHTML(true);

        /* Disable some SSL checks*/
        $this->mailer->SMTPOptions=array(
            'ssl'=>array(
                'verify_peer'=>false,
                'verify_peer_name'=>false,
                'allow_self_signed'=>true
            )
        );
        /* end ssl config        */

    }

    /**
     * OPTIONAL METHOD
     * default:
     * name - $GLOBALS["practice_return_email_path"]
     * email - $GLOBALS["patient_reminder_sender_email"]
     * @param $email
     * @param $name
     */
    public function from($email, $name)
    {
        $this->fromEmail = $email;
        $this->fromName = $name;
    }

    /**
     * Subject text
     * @param $subject
     */
    public function subject($subject)
    {
        $this->mailer->Subject = $subject;
    }

    /**
     * buils html body using zend template
     * @param $viewTemplate
     * @param $viewParams
     */
    public function body($viewTemplate, $viewParams)
    {
        $renderer = $this->continer->get('Laminas\View\Renderer\PhpRenderer');
        $body = new ViewModel($viewParams);
        $body->setTemplate($viewTemplate);
        $body = $renderer->render($body);
        $this->mailer->Body = $body;
    }

    /**
     *
     * @param        $file - binary string / path to file
     * @param        $attachType - 'file'/'binary'
     * @param        $fileName - name of attach file
     * @param string $mimeType - OPTIONAL
     */
    public function attach($file, $attachType, $fileName, $mimeType = '')
    {
        switch ($attachType){
            case 'file':
                $this->mailer->addAttachment($file, $fileName, 'base64', $mimeType);
                break;
            case 'binary':
                $this->mailer->addStringAttachment($file, $fileName, 'base64', $mimeType);
                break;
        }
    }

    /**
     * OPTIONAL METHOD
     * Mail priority
     * @param $number - only 1/2/3
     */
    public function priority($number)
    {
        switch ($number){
            case 1:
                $this->mailer->Priority = 1;
                //https://stackoverflow.com/questions/10766793/set-urgent-option-in-phpmailer
                // May set to "Urgent" or "Highest" rather than "High"
                $this->mailer->AddCustomHeader("X-MSMail-Priority: High");
                // Not sure if Priority will also set the Importance header:
                $this->mailer->AddCustomHeader("Importance: High");
                break;
            case 2:
                $this->mailer->Priority = 2;
                break;
            case 3:
                $this->mailer->Priority = 3;
                break;
        }
    }

    /**
     * Send email
     * @param array $addresses
     *@param bool $returnLog
     * @return bool
     */
    public function send(array $addresses,$returnLog = false )
    {
        /* Enable SMTP debug output. */
        //$mail->SMTPDebug = 4;
        foreach ($addresses as $address){
            $this->mailer->addAddress($address);
        }

        $fromEmail = is_null($this->fromEmail) ?  $GLOBALS["patient_reminder_sender_email"] : $this->fromEmail;
        $fromName = is_null($this->fromName) ?  $GLOBALS["patient_reminder_sender_name"] : $this->fromName;
        $this->mailer->SetFrom($fromEmail, $fromName);

        if($this->mailer->send()) {
            if($returnLog){
                return array('wasSend'=>'true','error'=> 'success');
            }else{
                return true;
            }
        } else {
            error_log('Send mail was failed:' . $this->mailer->ErrorInfo);
            if($returnLog){
                return array('wasSend'=>'false','error'=> $this->mailer->ErrorInfo);
            }else{
                return false;
            }


        }

    }

    public function clearAttachments()
    {
        $this->mailer->clearAttachments();
        return 1;
    }

    public function clearAllCustom()
    {
        $this->mailer->clearAttachments();
        $this->mailer->clearAddresses();
        $this->mailer->clearAllRecipients();
        $this->mailer->clearBCCs();
        $this->mailer->clearCCs();
        return 1;
    }

    public function clearAll()
    {
        $this->mailer->clearAttachments();
        $this->mailer->clearAddresses();
        $this->mailer->clearAllRecipients();
        $this->mailer->clearBCCs();
        $this->mailer->clearCCs();
        $this->mailer->clearReplyTos();
        $this->mailer->clearCustomHeaders();
        return 1;
    }
}
