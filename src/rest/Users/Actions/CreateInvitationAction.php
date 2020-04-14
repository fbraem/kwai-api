<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use PHPMailer\PHPMailer\Exception;

use Domain\User\UserInvitationsTable;
use Domain\User\UserInvitationTransformer;
use Domain\User\UsersTable;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\InputValidator;
use Kwai\Core\Infrastructure\Validators\ValidationException;


use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;

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

        try {
            (new InputValidator([
                'data.attributes.email' => v::allOf(
                    v::email(),
                    v::callback(function ($value) {
                        // Check if the email address isn't used yet ...
                        $user = UsersTable::getTableFromRegistry()
                            ->find()
                            ->where(['email' => $value])
                            ->first()
                        ;
                        return $user == null;
                    })->setTemplate('{{name}} already in use')
                )
            ]))->validate($data);

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
            $invitation->user = $request->getAttribute('clubman.user');

            $invitationsTable->save($invitation);

            $mail = $this->container->mailer;
            try {
                $mail->addAddress($invitation->email);

                $mail->isHTML(true);
                $mail->Subject = $this->container->settings['mail']['subject'] . ' - User Invitation';
                $mail->Body = $this->container->template->render('User/invitation_html', [
                    'url' => $this->container->settings['website']['url'] . '/#users/invite/' . $invitation->token,
                    'email' => $this->container->settings['website']['email'],
                    'invitation' => $invitation,
                    'user' => $invitation->user
                ]);
                $mail->AltBody = $this->container->template->render('User/invitation_txt', [
                    'url' => $this->container->settings['website']['url'] . '/#users/invite/' . $invitation->token,
                    'email' => $this->container->settings['website']['email'],
                    'invitation' => $invitation,
                    'user' => $invitation->user
                ]);

                if (!$mail->send()) {
                    return (new SimpleResponse(500, $mail->ErrorInfo))($response);
                };
            } catch (Exception $e) {
                return (new SimpleResponse(500, $e->getMessage()))($response);
            }

            $response = (new ResourceResponse(
                UserInvitationTransformer::createForItem($invitation)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
