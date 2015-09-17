<?php

namespace KinetiseSkeleton\Provider;

use KinetiseSkeleton\Controller\Api\CommentsController;
use KinetiseSkeleton\Controller\Tutorial\IndexController;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['controllers.api.comments'] = $app->share(function() use ($app) {
            return new CommentsController($app);
        });

        $app['controllers.tutorial.index'] = $app->share(function() use ($app) {
            return new IndexController($app);
        });
    }

    public function boot(Application $app)
    {
        /** @var ControllerCollection $api */
        $api = $app['controllers_factory'];
        $app->mount('/api', $api);

        $api->before(function(Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);

                $request->request->set(
                    '_json',
                    $data ? $data : array()
                );
            }
        });

        $api->get('/comments', 'controllers.api.comments:getAction')->bind('api_comments');
        $api->post('/comments', 'controllers.api.comments:addAction')->bind('api_comment_add');

        $api
            ->get('/comments/{id}', 'controllers.api.comments:commentAction')
            ->assert('id', '\d+')
            ->bind('api_comment');

        $api
            ->post('/comments/{id}/delete', 'controllers.api.comments:deleteAction')
            ->assert('id', '\d+')
            ->bind('api_comment_delete');

        /** @var ControllerCollection $tutorial */
        $tutorial = $app['controllers_factory'];
        $app->mount('/tutorial', $tutorial);

        $tutorial
            ->get('/{section}', 'controllers.tutorial.index:indexAction')
            ->value('section', 'index')
            ->bind('tutorial_index');
    }

}