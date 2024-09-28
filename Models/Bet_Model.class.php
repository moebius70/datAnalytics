<?php

class Bet_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function insertRace($data) {
        $sql = "INSERT INTO races (`date`, racetrack, horse_name, `time`, distance, jockey, odds, `status`, betfair_betID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['date'], $data['racetrack'], $data['horse_name'], $data['time'], 
            $data['distance'], $data['jockey'], $data['odds'], $data['status'], 
            $data['betfair_betID']
        ];
        return $this->execute($sql, $params);
    }

    public function insertBetData($data) {
        $sql = "INSERT INTO bet_data (id, `status`, betfair_betID, bet_matched, points) 
                VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['betID'], $data['status'], $data['betfair_betID'], $data['bet_matched'], $data['points']
        ];
        return $this->execute($sql, $params);
    }

    
    public function updateBet($data) {
        try {
            print_r($data);
            $id = $data['betID'];
    
            // Update the bet_data table
            $sql = "UPDATE bet_data SET `status` = ?, betfair_betID = ?, bet_matched = ?, points = ? WHERE id = ?";
            $params = [
                $data['status'],     // integer
                $data['betfair_betID'], // integer or string depending on column type
                $data['bet_matched'], // integer (1 or 0)
                $data['points'],     // integer
                $id                  // integer
            ];
    
            $result = $this->execute($sql, $params);
            if ($result === false) {
                throw new Exception("Failed to update bet_data table.");
            }
    
            // Update the races table using the 'betID'
            $sql = "UPDATE races SET `date` = ?, racetrack = ?, horse_name = ?, `time` = ?, distance = ?, jockey = ?, odds = ?, `status` = ? WHERE betfair_betID = ?";
            $params = [
                $data['date'],       // string (date format)
                $data['racetrack'],  // string
                $data['horse_name'], // string
                $data['time'],       // string (time format)
                $data['distance'],   // integer
                $data['jockey'],     // string
                $data['odds'],       // string or integer depending on column type
                $data['status'],     // integer
                $data['betfair_betID']//$id                  // integer
            ];
    
            $result = $this->execute($sql, $params);
            if ($result === false) {
                throw new Exception("Failed to update races table.");
            }
            
            return true;
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            $this->mysqli->rollback();
            error_log("Error in updateBet: " . $e->getMessage());
            return false;
        }
    }
    
    
    

    public function getAllBetsWithRaces() {
        $sql = "SELECT races.*, bet_data.status AS bet_status, bet_data.betfair_betID, bet_data.bet_matched, bet_data.points, races.betfair_betID 
                FROM races 
                INNER JOIN bet_data ON bet_data.betfair_betID = races.betfair_betID";
        return $this->select($sql);
    }

    public function getAllRaces() {
        $sql = "SELECT * FROM races";
        return $this->select($sql);
    }

    public function getFilteredBets($searchParams) {
        $sql = "SELECT races.date, races.racetrack, races.horse_name, races.time, races.distance, 
        races.jockey, races.odds, bet_data.status, bet_data.betfair_betID, bet_data.bet_matched, 
        bet_data.points, races.betID 
                FROM races 
                JOIN bet_data ON bet_data.betfair_betID = races.betfair_betID
                WHERE 1=1";
        
        $params = [];
    
        if (!empty($searchParams['date'])) {
            $sql .= " AND races.date = ?";
            $params[] = $searchParams['date'];
        }
        if (!empty($searchParams['racetrack'])) {
            $sql .= " AND races.racetrack LIKE ?";
            $params[] = '%' . $searchParams['racetrack'] . '%';
        }
        if (!empty($searchParams['horse_name'])) {
            $sql .= " AND races.horse_name LIKE ?";
            $params[] = '%' . $searchParams['horse_name'] . '%';
        }
        if (!empty($searchParams['time'])) {
            $sql .= " AND races.time = ?";
            $params[] = $searchParams['time'];
        }
        if (!empty($searchParams['distance'])) {
            $sql .= " AND races.distance = ?";
            $params[] = $searchParams['distance'];
        }
        if (!empty($searchParams['jockey'])) {
            $sql .= " AND races.jockey LIKE ?";
            $params[] = '%' . $searchParams['jockey'] . '%';
        }
        if (!empty($searchParams['odds'])) {
            $sql .= " AND races.odds LIKE ?";
            $params[] = '%' . $searchParams['odds'] . '%';
        }
        if (!empty($searchParams['status'])) {
            $sql .= " AND bet_data.status LIKE ?";
            $params[] = '%' . $searchParams['status'] . '%';
        }
        if (!empty($searchParams['betfair_betID'])) {
            $sql .= " AND bet_data.betfair_betID LIKE ?";
            $params[] = '%' . $searchParams['betfair_betID'] . '%';
        }
        if (isset($searchParams['bet_matched']) && $searchParams['bet_matched'] !== '') {
            $sql .= " AND bet_data.bet_matched = ?";
            $params[] = $searchParams['bet_matched'];
        }
        if (!empty($searchParams['points'])) {
            $sql .= " AND bet_data.points = ?";
            $params[] = $searchParams['points'];
        }
    
        return $this->select($sql, $params);
    }

    public function getAllBets() {
        $sql = "SELECT bet_data.id, races.date AS race_date, races.racetrack, races.horse_name, DATE_FORMAT(`time`, '%H:%i') AS race_time, races.distance, races.jockey, 
        races.odds, bet_data.status, bet_data.betfair_betID, bet_data.bet_matched, bet_data.points, races.betfair_betID 
                FROM races 
                JOIN bet_data ON bet_data.betfair_betID = races.betfair_betID";
    
        return $this->select($sql);
    }    

    public function deleteBet($betfair_betID) {
        $this->beginTransaction();
        try {
            // Delete from bet_data table first
            $sql = "DELETE FROM bet_data WHERE betfair_betID = ?";
            $this->execute($sql, [$betfair_betID]);
    
            // Then delete from races table
            $sql = "DELETE FROM races WHERE betfair_betID = ?";
            $this->execute($sql, [$betfair_betID]);
    
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            error_log("Error in deleteBet: " . $e->getMessage());
            return false;
        }
    }
    
}

?>
