<?php
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectfId;

class SearchLog {
    private $collection;
    
    public function __construct($mongodb) {
        $this->collection = $mongodb->search_logs;
    }
    
    /* Enregistrer une recherche utilisateur */
 
    public function logSearch($userId, $searchParams, $resultsCount) {
        try {
            $this->collection->insertOne([
                'user_id' => $userId ?? 0,
                'search_params' => [
                    'depart' => $searchParams['depart'] ?? '',
                    'arrivee' => $searchParams['arrivee'] ?? '',
                    'date' => $searchParams['date'] ?? '',
                    'filters' => $searchParams['filters'] ?? []
                ],
                'results_count' => (int)$resultsCount,
                'timestamp' => new MongoDB\BSON\UTCDateTime(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Erreur log recherche MongoDB : " . $e->getMessage());
            return false;
        }
    }
    
    /* Récupérer les recherches récentes */
    public function getRecentSearches($limit = 20) {
        try {
            return $this->collection->find([], [
                'sort' => ['timestamp' => -1],
                'limit' => $limit
            ])->toArray();
        } catch (Exception $e) {
            error_log("Erreur récupération logs : " . $e->getMessage());
            return [];
        }
    }
    
    /* Statistiques des recherches par ville */
    public function getTopSearchedRoutes($limit = 10) {
        try {
            $pipeline = [
                [
                    '$group' => [
                        '_id' => [
                            'depart' => '$search_params.depart',
                            'arrivee' => '$search_params.arrivee'
                        ],
                        'count' => ['$sum' => 1]
                    ]
                ],
                ['$sort' => ['count' => -1]],
                ['$limit' => $limit]
            ];
            
            return $this->collection->aggregate($pipeline)->toArray();
        } catch (Exception $e) {
            error_log("Erreur stats MongoDB : " . $e->getMessage());
            return [];
        }
    }
}