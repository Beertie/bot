<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Basketball Controller
 *
 * @property \App\Model\Table\BasketballTable $Basketball
 */
class BasketballController extends AppController
{

    public function index(){

        $this->viewBuilder()->layout(false);
        $config = [
            'facebook_token' => 'EAAazwdCwp1IBAKEHZCIvjLTZCuMmMqdo1JJge5SAAT6bSsYbKoscLdaWlTGe6OWoHXFmJSHM3FfQ598GoQwdrcljAUZBrIje0ZBBZAf4TE24rZCGx1ZBmjjHBrWrY8TlVsZAbU527ql4S1ds1VBZCZBYxHSeJZBZBVAeVmZAdTQwMYTo2SwZDZD',
        ];

        // create an instance
        $botman = BotManFactory::create($config);
        $botman->verifyServices('fb_time_bot');


        // give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        // start listening
        $botman->listen();

    }
}
