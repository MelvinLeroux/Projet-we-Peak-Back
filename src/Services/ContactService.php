<?php 

namespace App\Services;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetSmtpTransport;
use Symfony\Component\Mailer\Mailer;
class ContactService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($data): void
    {
        
        $name = $data['name'];
        $email = $data['email'];
        $telephone = $data['phone'];
        $company = $data['company'];
        $message = $data['message'];


        // Configuration du transport SMTP Mailjet
        $transport = new MailjetSmtpTransport('abcd0e0d0db3912bc17352fc0330302b', 'f60bf1913100b882ac0181826de8f6cd');

        // Création de l'objet Mailer
        $mailer = new Mailer($transport);

        // Création de l'objet Email
        $email = (new Email())
            ->from('wepeakfrance@gmail.com')
            ->to('wepeakfrance@gmail.com')
            ->subject('Nouvelle demande de contact')
            ->text('Nom: ' . $name . '\nE-mail: ' . $email . '\nMessage: ' . $message . '\nTéléphone: ' . $telephone . '\nSociété: ' . $company);
        // Envoi de l'e-mail
        $mailer->send($email);
    }
}     