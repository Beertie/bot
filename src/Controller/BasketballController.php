<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\Nbb\Nbb;

use Cake\Cache\Cache;
use Mpociot\BotMan\BotManFactory;
use Mpociot\BotMan\BotMan;
use Mpociot\BotMan\Facebook\ButtonTemplate;
use Mpociot\BotMan\Facebook\ElementButton;


/**
 * Basketball Controller
 *
 */
class BasketballController extends AppController
{

    public $name_name = "Test";

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


            Cache::write("log_1", ["id_hello" => $bot->getUser()->getId()]);
            //$bot->reply($bot->getChannel()->getId());

            //Ask question
            $bot->reply('Voor welk team speel je?');


            //Set up a array
            $cacheData = ["club" => "select", "team"=> "select", "bot"=> "talked"];

            //Cache the array
            Cache::write($bot->getUser()->getId(), $cacheData);
            return;


        });


        //If heard
        $botman->hears("{team}", function ( BotMan $bot, $team){
            $cacheData = Cache::read($bot->getUser()->getId());
            if($cacheData['bot'] == 'talked'){
                $cacheData["bot"] = false;
                //Cache the array
                Cache::write($bot->getUser()->getId(), $cacheData);
                return;
            }


            if($cacheData['club'] == 'select'){
                $teams = $this->selectTeam($team);
                $team = array_values($teams)[0];

                $cacheData = ["club" => ["club_id"=> $team['nbb_id'], "club_name" =>$team['team']]];
                Cache::write($bot->getUser()->getId(), $cacheData);

                $bot->reply("Je hebt gekozen voor ".$team['team']);

                $teams_array = $this->teams($team['nbb_id']);

                Cache::write("teams", $teams_array);

                $replay = ButtonTemplate::create('In welk team speel je ?');

                foreach ($teams_array as $team){
                    $buttons->addButton(ElementButton::create($team['naam']))->type('postback');
                }

                $bot->reply($replay);
            }

        });



        $botman->hears("info", function (BotMan $bot) {

            $bot->reply("Start");

            $user = $bot->getUser();
            $info = Cache::read($user->getId());


            $bot->reply($info['team']);

            $bot->reply("End");
        });


        $botman->hears("reset", function (BotMan $bot) {
            // Delete all stored information.
            $bot->userStorage()->delete();
        });

        $botman->hears("call me {name}", function (BotMan $bot, $name) {

            $bot->userStorage()->save([
                'name' => $name
            ]);

            $bot->reply('I will call you '.$name);
        });

        $botman->hears("who am I", function (BotMan $bot) {

            $user = $bot->userStorage()->get();

            if ($user->has('name')) {
                $bot->reply('You are '.$user->get('name'));
            } else {
                $bot->reply('I do not know you yet.');
            }
        });


        // start listening
        $botman->listen();

    }

    public function listTeams(){

        $nbb = new Nbb();

        $teams = $nbb->getAllClubs();

        $return_teams=[];
        foreach ($teams as $team){
            //debug($team);
            if(is_array($team)){
                foreach ($team as $t){
                    $return_teams[$t->id] =  $t->naam;
                }
            }
        }
        return $return_teams;
    }

    public function selectTeam($team_name){

        $find_teams = [];

        $teams = $this->listTeams();

        foreach ($teams as $nbb_id => $team){
            similar_text($team, $team_name, $percent);
            if($percent > 75){
                $find_teams[$percent] = ["nbb_id" =>  $nbb_id, "team" => $team];
            }

        }

        krsort($find_teams);
        return $find_teams;
    }

    public function teams(){

        $nbb = new Nbb();

        $teams = $nbb->getAllTeams(81);
        return $teams->teams;


    }
}
