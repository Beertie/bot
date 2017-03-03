<?php
namespace App\Shell;

use App\Lib\Nbb\Nbb;
use Cake\Cache\Cache;
use Cake\Console\Shell;

/**
 * Foo shell command.
 */
class FooShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
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

    public function selectTeam(){

        $team_name = "Rivertrotters";

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

    public function test(){

        $team = ["team" => "select"];

        Cache::write(1062814013823687, $team);
    }

    public function add(){
        $team = ["team" => "select"];

        Cache::write(123456789, $team);
    }
}
