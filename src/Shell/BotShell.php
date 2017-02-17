<?php
namespace App\Shell;

use Cake\Console\Shell;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;

/**
 * Bot shell command.
 */
class BotShell extends Shell
{


    public function start()
    {
        $config = [
            'facebook_token' => 'EAACrv7aoid8BAMI0JTU3DZCn0oR7VrpP9jekoMxztA3Kucj1CpodwOkLZA6XZA6urYy8psjZC18FaIZCP3Bh6kBR8BAfsn2yKqQFosRIlZBxXCdcZBvyXs7ESHjMcmZAxQQ6YhvReCZCZACg2M5V4rplBUO52pbRhA0rmk6ZA7RFNmByAZDZD',
        ];

        // create an instance
        $botman = BotManFactory::create($config);
        $botman->verifyServices('fb_time_bot');

        //$botman->say("Test");
            // give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

            // start listening
        $botman->listen();



/*
        // create an instance
        $botman = BotManFactory::create($config);

        debug($botman);



        $botman->reply("HALLOO");

        // give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        // start listening
        $botman->listen();

        //echo "End \n";*/
    }


    public function facebook(){
        //EAACrv7aoid8BAMI0JTU3DZCn0oR7VrpP9jekoMxztA3Kucj1CpodwOkLZA6XZA6urYy8psjZC18FaIZCP3Bh6kBR8BAfsn2yKqQFosRIlZBxXCdcZBvyXs7ESHjMcmZAxQQ6YhvReCZCZACg2M5V4rplBUO52pbRhA0rmk6ZA7RFNmByAZDZD

        $access_token = 'EAACrv7aoid8BAMI0JTU3DZCn0oR7VrpP9jekoMxztA3Kucj1CpodwOkLZA6XZA6urYy8psjZC18FaIZCP3Bh6kBR8BAfsn2yKqQFosRIlZBxXCdcZBvyXs7ESHjMcmZAxQQ6YhvReCZCZACg2M5V4rplBUO52pbRhA0rmk6ZA7RFNmByAZDZD';
        $verify_token = 'fb_time_bot';
        $hub_verify_token = null;
        if(isset($_REQUEST['hub_challenge'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $hub_verify_token = $_REQUEST['hub_verify_token'];
        }
        if ($hub_verify_token === $verify_token) {
            echo $challenge;
        }


        $config = [
            'facebook_token' => 'xxx',
        ];

        // create an instance
        $botman = BotManFactory::create($config);

        debug($botman);

        $botman->verifyServices('fb_time_bot');

        $botman->reply("HALLOO");

        // give the bot something to listen for.
        $botman->hears('hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        // start listening
        $botman->listen();

    }
}
