<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use PHPMailer\PHPMailer\Exception;

use Domain\User\UserInvitationsTable;
use Domain\User\UserInvitationTransformer;

class CreateInvitationAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $attributes = \JmesPath\search('data.attributes', $data);

        $invitationsTable = UserInvitationsTable::getTableFromRegistry();
        $invitation = $invitationsTable->newEntity();
        $invitation->email = $attributes['email'];
        $invitation->token = bin2hex(random_bytes(16));
        if ($attributes['expired_at']) {
            $invitation->expired_at = $attributes['expired_at'];
            $invitation->expired_at_timezone = $attributes['expired_at_timezone'] ?? date_default_timezone_get();
        } else {
            $invitation->expired_at = \Carbon\Carbon::now()->addWeek();
            $invitation->expired_at_timezone = date_default_timezone_get();
        }
        $invitation->remark = $attributes['remark'] ?? null;
        $invitationsTable->save($invitation);

        $mail = $this->container->mailer;
        try {
            $mail->addAddress($invitation->email, 'Joe User');
            $mail->addAddress('ellen@example.com');

            $mail->isHTML(true);
            $mail->Subject = $this->container->settings['mail']['subject'] . ' - User Invitation';
            $mail->Body = $this->container->template->render('User/invitation_html', [
                'url' => $this->container->settings['website']['url'] . '/#users/invite/' . $invitation->token,
                'email' => $this->container->settings['website']['email'],
                'invitation' => $invitation
            ]);
            $mail->AltBody = $this->container->template->render('User/invitation_txt', ['invitation' => $invitation]);

            $mail->send();
        } catch (Exception $e) {
        }

        return \Core\ResourceResponse::respond(
            UserInvitationTransformer::createForItem($invitation),
            $response
        )->withStatus(201);
    }
}
