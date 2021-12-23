<?php
/**
 * @package Applications
 * @subpackage Facebook
 */
namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Presentation\Router;

/**
 * Class FacebookApplication
 *
 * Application for the facebook crawler and the entry for all links that come from Facebook.
 */
class FacebookApplication extends Application
{
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'facebook.news',
                '/news/{id}',
                FacebookStoryAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            );
    }
}
