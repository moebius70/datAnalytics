<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Index extends Controller
{
    public function __construct() {
        // Initialize model if needed
        // $model = new Index_Model();
    }

    public function index(): void
    {
        // TODO: Implement the logic for the homepage
        $v = new View();
        $v->render('default');
    }

    public function calculation(float $p = 4.16, float $r = 0.2, int $t = 5): void
    {
        $r = $r * 0.95;
        $c = new Calculations($p, $r, $t);

        // Attempt to calculate compound interest
        try {
            $compoundInterest = $c->compoundroi();
            echo "\nCompound return: $compoundInterest<br>";
        } catch (Exception $e) {
            echo "\nError calculating compound return: " . $e->getMessage() . "\n";
        }

        // Attempt to calculate average interest
        try {
            $averageInterest = round($c->roi(), 2);
            echo "\nAverage Interest: $averageInterest%\n";
        } catch (DivisionByZeroError $e) {
            echo "\nError calculating average interest: Division by zero - " . $e->getMessage() . "<br>";
        } catch (Exception $e) {
            echo "\nError calculating average interest: " . $e->getMessage() . "\n";
        }
    }

    public function get_data(){
        $query = new Child_Model();
        $data = $query->select("SELECT * FROM `races`");

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header('Content-Type: application/json');
        echo json_encode($data);

        echo "Data fetched successfully";
    } 
    
    public function add_race($date, $racetrack, $horse_name, $time, $distance, $jockey, $odds, $status, $betfair_betID, $bet_matched) {
        $query = new Child_Model();
        $sql = "INSERT INTO races (date, racetrack, horse_name, time, distance, jockey, odds, status, betfair_betID, bet_matched) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [$date, $racetrack, $horse_name, $time, $distance, $jockey, $odds, $status, $betfair_betID, $bet_matched];
        $result = $query->execute($sql, $params);

        if ($result) {
            echo "Race added successfully.";
        } else {
            echo "Failed to add race.";
        }
    }
}



?>