<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PasswordResetService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer
    ) {}

    public function createResetToken(string $email): ?string
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return null;
        }
        $plain = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $hash = hash('sha256', $plain);
        $user->setPasswordResetTokenHash($hash);
        $user->setPasswordResetExpiresAt(new \DateTimeImmutable('+1 hour'));
        $this->em->flush();

        $link = $_ENV['FRONT_BASE_URL'].'?token='.$plain;

        $emailObj = (new Email())
            ->from('no-reply@mgp.test')
            ->to($user->getEmail())
            ->subject('Password reset request')
            ->text("Use this link to reset your password: $link (valid 1h)")
            ->html('<p>Pour réinitialiser votre mot de passe cliquez sur ce lien :</p><p><a href="'.$link.'">Réinitialiser</a></p><p>Valide 1h.</p>');
        $this->mailer->send($emailObj);

        return $plain;
    }

    public function validateAndConsume(string $plainToken): ?User
    {
        $hash = hash('sha256', $plainToken);
        $user = $this->userRepository->findOneBy(['passwordResetTokenHash' => $hash]);
        if (!$user) {
            return null;
        }
        if (!$user->getPasswordResetExpiresAt() || $user->getPasswordResetExpiresAt() < new \DateTimeImmutable()) {
            return null;
        }
        
        $user->setPasswordResetTokenHash(null);
        $user->setPasswordResetExpiresAt(null);
        $this->em->flush();
        return $user;
    }
}
