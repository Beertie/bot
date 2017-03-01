<?php

namespace App\Lib\Nbb;

class Nbb{

    //Club id fo use for one club
    public $club_id;

    //TODO remove option of url
    //Set global api url
    public $url = "http://db.basketball.nl/db/json/wedstrijd.pl";

    /**
     * Nbb constructor.
     *
     * @param int $club_id update club id
     */
    function __construct($club_id = 81)
    {
        //Add id to global url
        $this->url .= "?clb_ID=$club_id";

        //Update global url
        $this->club_id = $club_id;
    }

    /**
     * Get all games of one seseon of teh select club sort bij date
     *
     * TODO
     * Add filters for teams
     * Add sort options for date
     * Add filters for played games
     *
     * @return mixed|string
     */
    public function getAllGames(){

        $games = file_get_contents($this->url);
        $games = json_decode($games);

        return $games;

    }

    /**
     *
     * Get all the games for the week for club
     *
     *
     * @param bool $home_game_only
     * @param null $week_number
     * @return array
     */
    public function getThisWeek($home_game_only = false, $week_number = null){

       //Get all games json
        $games = $this->getAllGames();

        //If no week number set get current number
        if($week_number == null ){
            $week_number = $this->getWeekNumber();
        }

        //Get dates of the weeekend for the week number
        $date = $this->getStartAndEndDate(($week_number -1), 2017);

        //Define week array
        $week = [];

        //For al games of the json file
        foreach ($games->wedstrijden as $game){

            //If no date for game skip it
            if($game->datum == null){
                continue;
            }

            //Set time of game to unix time
            $unix_time = strtotime($game->datum);

            //Check is time is the range
            if($unix_time > strtotime($date[0]) AND $unix_time < strtotime($date[1])){

                //If only want home games skip all others
                if($home_game_only AND $game->thuis_club_id != $this->club_id){
                    continue;
                }
                $week[] = $game;
            }

        }

        return $week;

    }

    /**
     * Get game for competitons
     *
     * TODO
     * Add filter for played games
     * Add sortoptions for date
     *
     * @param $comp_id
     * @return mixed|string
     */
    public function getCompetition($comp_id){

        $comp = file_get_contents("http://db.basketball.nl/db/json/wedstrijd.pl?cmp_ID=$comp_id");
        $comp = json_decode($comp);
        return $comp;

    }


    public function getScore($comp_id, $year = false){

        $url = "http://db.basketball.nl/db/json/stand.pl?cmp_ID=$comp_id";

        if($year != false){
            $url .= "&&seizoen=".$year."-".($year + 1);
        }
        //debug($url);


        $comp = file_get_contents($url);
        $comp = json_decode($comp);

        //debug($comp);

        return $comp;
    }

    public function getLastWeek(){

    }

    public function getStatsForTeam(){

    }

    public function getStatsForComp(){



    }

    public function getResultsByTeam($team_id){
        //plg_ID
        ////http://db.basketball.nl/db/wedstrijd/uitslag.pl?&szn_Naam=2016-2017&cmp_ID=1045&plg_ID=767&org_ID=2&LVactie=Wedstrijdgegevens+tonen&Sorteer=wed_Datum
        $url = "http://db.basketball.nl/db/json/wedstrijd.pl?plg_ID=$team_id";
        $score = file_get_contents($url);
        $score = json_decode($score);

        //debug($score);

        return $score;


    }

    public function getStats($comp_id, $year = false){

        $url = "http://db.basketball.nl/db/json/wedstrijd.pl?cmp_ID=$comp_id";

        if($year != false){
            $url .= "&&seizoen=".$year."-".($year + 1);
        }
        //debug($url);


        $score = file_get_contents($url);
        $score = json_decode($score);

        $stats = [];
        foreach ($score->wedstrijden as $game){


            //If no score skip
            if($game->score_thuis == 0 AND $game->score_uit == 0){
                continue;
            }

            $stats[$game->thuis_ploeg_id]['name'] = $game->thuis_ploeg;
            $stats[$game->uit_ploeg_id]['name'] = $game->uit_ploeg;


            //Set defauls
            if(!isset($stats[$game->thuis_ploeg_id]['home']['win'])){
                $stats[$game->thuis_ploeg_id]['home']['win'] = 0;
            }

            if(!isset($stats[$game->thuis_ploeg_id]['home']['lose'])){
                $stats[$game->thuis_ploeg_id]['home']['lose'] = 0;
            }

            if(!isset($stats[$game->uit_ploeg_id]['away']['win'])){
                $stats[$game->uit_ploeg_id]['away']['win'] = 0;
            }

            if(!isset($stats[$game->uit_ploeg_id]['away']['lose'])){
                $stats[$game->uit_ploeg_id]['away']['lose'] = 0;
            }

            if(!isset($stats[$game->thuis_ploeg_id]['streak'])){
                $stats[$game->thuis_ploeg_id]['streak'] = 0;
            }

            if(!isset($stats[$game->uit_ploeg_id]['streak'])){
                $stats[$game->uit_ploeg_id]['streak'] = 0;
            }

            $home_win = true;
            if($game->score_thuis < $game->score_uit){
                $home_win = false;
            }


            if($home_win){

                //Count the wins and lose
                $stats[$game->thuis_ploeg_id]['home']['win'] = $stats[$game->thuis_ploeg_id]['home']['win'] + 1;
                $stats[$game->uit_ploeg_id]['away']['lose'] = $stats[$game->uit_ploeg_id]['away']['lose'] + 1;

                //Set last 5 record
                $stats[$game->thuis_ploeg_id]["last"][] = "W";
                $stats[$game->uit_ploeg_id]["last"][] = "L";

                //Set streak;
                //Winning team
                if($stats[$game->thuis_ploeg_id]['streak'] <= 0){
                    $stats[$game->thuis_ploeg_id]['streak'] = 1;
                }else{
                    $stats[$game->thuis_ploeg_id]['streak'] = $stats[$game->thuis_ploeg_id]['streak'] + 1;
                }

                //Losing team
                if($stats[$game->uit_ploeg_id]['streak'] > 0){
                    $stats[$game->uit_ploeg_id]['streak'] = -1;
                }else{
                    $stats[$game->uit_ploeg_id]['streak'] = $stats[$game->uit_ploeg_id]['streak'] - 1;
                }


            }else{

                //Count the wins and lose
                $stats[$game->thuis_ploeg_id]['home']['lose'] = $stats[$game->thuis_ploeg_id]['home']['lose'] + 1;
                $stats[$game->uit_ploeg_id]['away']['win'] = $stats[$game->uit_ploeg_id]['away']['win'] + 1;

                //Set last 5 record
                $stats[$game->thuis_ploeg_id]["last"][] = "L";
                $stats[$game->uit_ploeg_id]["last"][] = "W";

                //Losing team
                if($stats[$game->uit_ploeg_id]['streak'] > 0){
                    $stats[$game->uit_ploeg_id]['streak'] = -1;

                }else{
                    $stats[$game->uit_ploeg_id]['streak'] = $stats[$game->uit_ploeg_id]['streak'] - 1;
                }


                //Winning team
                if($stats[$game->thuis_ploeg_id]['streak'] <= 0){
                    $stats[$game->thuis_ploeg_id]['streak'] = 1;
                }else{
                    $stats[$game->thuis_ploeg_id]['streak'] = $stats[$game->thuis_ploeg_id]['streak'] + 1;
                }
            }


        }

        foreach ($stats as $team_id => $st){

            $reversed = array_reverse($st['last']);

            //debug($reversed);
            $w = 0;
            $l = 0;

            for($i = 0; $i <= 4; $i++){
                //debug($reversed[$i]);
                if($reversed[$i] == "W"){
                    $w = $w + 1;
                }else{
                    $l = $l +1;
                }
            }

            $stats[$team_id]["home"] = $stats[$team_id]["home"]['win']."-".$stats[$team_id]["home"]['lose'];
            $stats[$team_id]["away"] = $stats[$team_id]["away"]['win']."-".$stats[$team_id]["away"]['lose'];

            if($stats[$team_id]['streak'] > 0){
                $stats[$team_id]['streak'] = "W ".$stats[$team_id]['streak'];
            }

            if($stats[$team_id]['streak'] < 0){
                $stats[$team_id]['streak'] = "L ". abs($stats[$team_id]['streak']);
            }

            $stats[$team_id]["L5"] = $w."-".$l;

        }

        return $stats;

    }

    public function getSchedule($comp_id, $team_id){

        $comp = file_get_contents("http://db.basketball.nl/db/json/wedstrijd.pl?cmp_ID=$comp_id");
        $comp = json_decode($comp);

        $schedule = [];
        foreach ($comp->wedstrijden as $key => $game){

            if($game->thuis_ploeg_id == $team_id OR $game->uit_ploeg_id == $team_id){

                if($game->score_thuis == 0 AND $game->score_uit == 0){
                    $schedule[] = $game;
                }

            }

        }

        return $schedule;

    }

    /**
     *
     *
     * @param $comp_id
     */
    public function getStatsComp($comp_id){
        $url = "http://db.basketball.nl/db/json/stand.pl?cmp_ID=$comp_id";

        $stats = file_get_contents($url);
        $stats = json_decode($stats);

        /**
         * 	(int) 0 => object(stdClass) {
        afko => 'BV Voorne HS 1'
        ID => '235'
        status => 'Actief'
        rang => '1'
        gespeeld => '10'
        percentage => '90.0'
        tegenscore => '480'
        punten => '18'
        eigenscore => '714'
        datum => '2016-12-21'
        team => 'BV Voorne Heren 1'
        saldo => (int) 234
        positie => '1'
        },
         */


       // debug($stats);


    }

    public function getStartAndEndDate($week, $year)
    {

        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7*$week)+1-$day)*24*3600;
        $return[0] = date('Y-n-j', $time);
        $time += 6*24*3600;
        $return[1] = date('Y-n-j', $time);
        return $return;
    }

    public function getWeekNumber(){
        $time = date("Y-m-d",time());
        $date = new \DateTime($time);
        $week = $date->format("W");

        //debug($week);
        return $week;
    }

}