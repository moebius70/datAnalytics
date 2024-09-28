<?php
class Dashboard extends Controller {
    
    private $betModel;

    public function __construct() {
        parent::__construct();
        $this->betModel = new Bet_Model();
    }

    // public function index() {
    //     // Display the dashboard view
    //     $this->render('add_bet');
    // }

    public function get_bet_data() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $betData = [
                'date' => $_POST['race_date'] ?? null,
                'racetrack' => $_POST['racetrack'] ?? null,
                'horse_name' => $_POST['horse_name'] ?? null,
                'time' => $_POST['race_time'] ?? null,
                'distance' => $_POST['distance'] ?? null,
                'jockey' => $_POST['jockey'] ?? null,
                'odds' => $_POST['odds'] ?? null,
                'status' => $_POST['status'] ?? null,
                'betfair_betID' => $_POST['betfair_betID'] ?? null,
                'bet_matched' => $_POST['bet_matched'] ?? null,
                'points' => $_POST['points'] ?? null
            ];
    
            $message = $this->add_bet($betData);
            
            // Render the add_bet view and pass the message to it
            $this->render('add_bet', ['message' => $message]);
        } else {
            // Render the add_bet view with no message if no POST request is made
            $this->render('add_bet');
        }
    }
    
    public function add_bet($betData) {
        if (!$this->validateBetData($betData)) {
            return 'Invalid data provided.';
        }
    
        $this->betModel->beginTransaction();
    
        try {
            // Insert race data first to get the auto-increment ID for betData
            $this->betModel->insertRace($betData);
            $raceId = $this->betModel->getLastInsertId();
    
            // Update bet data with the race ID
            $betData['betID'] = $raceId;
            $this->betModel->insertBetData($betData);
    
            $this->betModel->commit();
    
            // Return the success message
            return 'Bet successfully added!';
        } catch (Exception $e) {
            $this->betModel->rollback();
            return 'Failed to add bet: ' . $e->getMessage();
        }
    }
    

    public function display_bets() {
        $searchParams = $this->buildSearchParams();
        $bets = !empty($searchParams) ? $this->betModel->getFilteredBets($searchParams) : $this->betModel->getAllBets();
        $this->render('display_bets', ['bets' => $bets]);
        // print_r($bets);
    }

    private function buildSearchParams() {
        $searchParams = [];
    
        if (!empty($_GET['race_date'])) {
            $searchParams['date'] = $_GET['race_date'];
        }
        if (!empty($_GET['racetrack'])) {
            $searchParams['racetrack'] = $_GET['racetrack'];
        }
        if (!empty($_GET['horse_name'])) {
            $searchParams['horse_name'] = $_GET['horse_name'];
        }
        if (!empty($_GET['race_time'])) {
            $searchParams['time'] = $_GET['race_time'];
        }
        if (!empty($_GET['distance'])) {
            $searchParams['distance'] = $_GET['distance'];
        }
        if (!empty($_GET['jockey'])) {
            $searchParams['jockey'] = $_GET['jockey'];
        }
        if (!empty($_GET['odds'])) {
            $searchParams['odds'] = $_GET['odds'];
        }
        if (!empty($_GET['status'])) {
            $searchParams['status'] = $_GET['status'];
        }
        if (!empty($_GET['betfair_betID'])) {
            $searchParams['betfair_betID'] = $_GET['betfair_betID'];
        }
        if (isset($_GET['bet_matched']) && $_GET['bet_matched'] !== '') {
            $searchParams['bet_matched'] = $_GET['bet_matched'];
        }
        if (!empty($_GET['points'])) {
            $searchParams['points'] = $_GET['points'];
        }

        return $searchParams;
    }

    public function update_bet() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bet_id = $_POST['betID'];
            $betData = [
                'date' => $_POST['race_date'],
                'racetrack' => $_POST['racetrack'],
                'horse_name' => $_POST['horse_name'],
                'time' => $_POST['race_time'],
                'distance' => $_POST['distance'],
                'jockey' => $_POST['jockey'],
                'odds' => $_POST['odds'],
                'status' => $_POST['status'],
                'betfair_betID' => $_POST['betfair_betID'],
                'bet_matched' => $_POST['bet_matched'],
                'points' => $_POST['points'],
                'betID' => $bet_id // This will be used to match with the bet_data table
            ];

            $this->betModel->updateBet($betData);
            // header('Location: /Dashboard/display_bets');
        }
    }

    public function display_races() {
        // Fetch all races
        $races = $this->betModel->getAllRaces();
        return $this->render('display_races', ['races' => $races]);
    }

    private function validateBetData($data) {
        return isset(
            $data['date'], $data['racetrack'], $data['horse_name'], $data['time'], 
            $data['distance'], $data['jockey'], $data['odds'], $data['status'], 
            $data['betfair_betID'], $data['bet_matched'], $data['points']
        ) && is_numeric($data['points']) && $data['points'] >= 1 && $data['points'] <= 1000;
    }

    public function delete_bet() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $betfair_betID = $_POST['betfair_betID'];
    
            if ($this->betModel->deleteBet($betfair_betID)) {
                header('Location: /Dashboard/display_bets');
                exit();
            } else {
                return $this->renderError('Failed to delete bet.');
            }
        }
    }
    
}

?>
