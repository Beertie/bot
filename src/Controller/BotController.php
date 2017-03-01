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

    /**
     * Fb token of bot page
     *
     * @var string
     */
    private $token = "EAACrv7aoid8BAH9j7ZCNprZBZAHKvs0r9qD87I1gD6i8BZCbkZBvWv8d5JPnFNasFi9JUhQbnC6Ufs6wkG39ZCErOnKYOPLzYsWdAOZBdtigfraoHrFqAaMqKUhuk7AWTFZCmE5zBZCfy9hnzZCYPm5XUaUiX7KogCt2rQ6ZCoG52DfTwZDZD";

    private $verify_token = "fb_time_bot";

    public function index(){

        $this->viewBuilder()->layout(false);

        //Config token
        $config = [
            'facebook_token' => $this->token,
        ];

        // create an instance
        $botman = BotManFactory::create($config);
        $botman->verifyServices('fb_time_bot');

        $botman->hears("help", function (Botman $bot){

            $help = "Hi, this is the help options of me \n
cats: for cats facts \n
-ask me 2 \n
-ask me 3 \n
-ask me 4 \n
-ask me 5 \n
            ";
            $bot->reply($help);
        });

      /*  $botman->hears('dumpert', function (BotMan $bot) {
            $url = $this->randomDumpert();
            $bot->reply($url);
        });*/

        // give the bot something to listen for.
        $botman->hears('Hey|Hi|Hello', function (BotMan $bot) {

            if($bot->isBot()){
                return;
            }

            $response_array = [
                "Heey how are you doing?",
                "Heey men how are you ?",
                "Hi how’s it going?"
            ];

            $bot->types(2);
            $bot->reply($response_array[rand(0, (count($response_array) - 1))]);
        });

        //Give u a random cat facts.
        $botman->hears('Cats', function (BotMan $bot) {

            if($bot->isBot()){
                return;
            }

            $bot->types(2);
            $bot->reply($this->catFact());
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

    public function catFact(){

        $jsonObj = json_decode(file_get_contents("http://catfacts-api.appspot.com/api/facts"));

        if($jsonObj->success == "true"){

            return $jsonObj->facts[0];
        }

    }

    public function verifyToken(){

        $this->viewBuilder()->layout(false);

        $access_token = $this->token;
        $verify_token = $this->verify_token;
        $hub_verify_token = null;
        if(isset($_REQUEST["hub_challenge"])) {
                    $challenge = $_REQUEST["hub_challenge"];
         $hub_verify_token = $_REQUEST["hub_verify_token"];
        }
        if ($hub_verify_token === $verify_token) {
            echo $challenge;
        }

    }

    public function test(){
        $this->viewBuilder()->layout(false);

        $access_token = $this->token;
        $verify_token = $this->verify_token;
        $hub_verify_token = null;
        if(isset($_REQUEST["hub_challenge"])) {
            $challenge = $_REQUEST["hub_challenge"];
            $hub_verify_token = $_REQUEST["hub_verify_token"];
        }
        if ($hub_verify_token === $verify_token) {
            echo $challenge;
        }
    }
}
