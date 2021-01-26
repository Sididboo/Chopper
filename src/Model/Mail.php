<?php

namespace App\Model;


use Mailjet\Client;
use Mailjet\Resources;

class Mail{
    private $api_Key = "5acdca0264cc09d9677d27049b243c90";
    private $api_Key_secret = "c57dd382521ac1880148833c7843fe31";


    public function Send($to_email, $to_name, $subject, $content){
        $mj = new Client($this->api_Key, $this->api_Key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "theovacant@outlook.fr",
                        'Name' => "Mailjet Text"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2290087,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variable' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $mj->post(Resources::$Email, ['body' => $body]);
    }
}