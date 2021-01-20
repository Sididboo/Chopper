<?php

namespace App\Controller;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        $mj = new Client('5acdca0264cc09d9677d27049b243c90','c57dd382521ac1880148833c7843fe31',true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "theovacant@outlook.fr",
                        'Name' => "Théo"
                    ],
                    'To' => [
                        [
                            'Email' => "theovacant@outlook.fr",
                            'Name' => "Théo"
                        ]
                    ],
                    'Subject' => "Greetings from Mailjet.",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());

        return $this->render('home/index.html.twig');
    }
}
