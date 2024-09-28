<?php

class Index extends Controller
{
    // Other methods...

    public function api_get_data()
    {
        header('Content-Type: application/json');
        $query = new Child_Model();
        $data = $query->select('races');
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function api_add_race()
    {
        header('Content-Type: application/json');
        $query = new Child_Model();
        $input = json_decode(file_get_contents('php://input'), true);

        $sql = "INSERT INTO races (date, racetrack, horse_name, time, distance, jockey, odds, status, betfair_betID, bet_matched) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $input['date'], $input['racetrack'], $input['horse_name'], $input['time'], 
            $input['distance'], $input['jockey'], $input['odds'], $input['status'], 
            $input['betfair_betID'], $input['bet_matched']
        ];
        
        $result = $query->execute($sql, $params);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Race added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add race.']);
        }
    }
}
