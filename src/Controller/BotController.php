<?php
namespace App\Controller;

use App\Controller\AppController;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use Goutte\Client;


/**
 * Bot Controller
 *
 */
class BotController extends AppController
{

    public function index(){

        $this->viewBuilder()->layout(false);

        //Config token
        //EAACrv7aoid8BAE9KWa8R5l0rwAKeOmw61EAzSKRE42CJZC2G5ibBGpMUyQjj6latpAjI8DnKz0M1u9UKZCh2m4x0XxHMw47XV6mgV1FU9UePqi2fZCfGU2GDZADix9PXk4eXzSqclcwCiHyMHfnhFXsn5PrE0ojXFb1baQQoaQZDZD
        $config = [
            'facebook_token' => 'EAACrv7aoid8BAE9KWa8R5l0rwAKeOmw61EAzSKRE42CJZC2G5ibBGpMUyQjj6latpAjI8DnKz0M1u9UKZCh2m4x0XxHMw47XV6mgV1FU9UePqi2fZCfGU2GDZADix9PXk4eXzSqclcwCiHyMHfnhFXsn5PrE0ojXFb1baQQoaQZDZD',
        ];

        // create an instance
        $botman = BotManFactory::create($config);
        $botman->verifyServices('fb_time_bot');

        $botman->hears('dumpert', function (BotMan $bot) {
            $url = $this->randomDumpert();
            $bot->reply($url);
        });

        // give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        // start listening
        $botman->listen();

    }

    public function randomDumpert(){
        $client = new Client();
        $random_page = rand(1,8725);

        $url = "http://www.dumpert.nl/toppers/$random_page/";

        $crawler = $client->request('GET', $url);

        $data = $crawler->filter('.dumpthumb')->each(function ($node) {

            return $node->attr('href');;
        });
        $random_video = rand(0,14);

        $random = $client->request('GET', $data[$random_video]);

        return $random->getUri();

    }

    public function test(){

    }
}
