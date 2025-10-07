<?php
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

class PendingReview {
    private $collection;
    
    public function __construct($mongodb) {
        $this->collection = $mongodb->pending_reviews;
    }
    
    /* Créer un avis en attente */
    public function createReview($covoiturageId, $userId, $note, $commentaire) {
        try {
            $result = $this->collection->insertOne([
                'covoiturage_id' => (int)$covoiturageId,
                'user_id' => (int)$userId,
                'note' => (int)$note,
                'commentaire' => $commentaire,
                'status' => 'pending',
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'validated_at' => null,
                'validated_by' => null
            ]);
            
            return (string)$result->getInsertedId();
        } catch (Exception $e) {
            error_log("Erreur création avis MongoDB : " . $e->getMessage());
            return false;
        }
    }
    
    /* Récupérer les avis en attente */
    public function getPendingReviews() {
        try {
            return $this->collection->find(
                ['status' => 'pending'],
                ['sort' => ['created_at' => -1]]
            )->toArray();
        } catch (Exception $e) {
            error_log("Erreur récupération avis : " . $e->getMessage());
            return [];
        }
    }
    
    /* Valider un avis */
    public function approveReview($reviewId, $employeId) {
        try {
            $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($reviewId)],
                [
                    '$set' => [
                        'status' => 'approved',
                        'validated_at' => new MongoDB\BSON\UTCDateTime(),
                        'validated_by' => (int)$employeId
                    ]
                ]
            );
            return true;
        } catch (Exception $e) {
            error_log("Erreur validation avis : " . $e->getMessage());
            return false;
        }
    }
    
    /* Refuser un avis */
    public function rejectReview($reviewId, $employeId, $reason = '') {
        try {
            $this->collection->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($reviewId)],
                [
                    '$set' => [
                        'status' => 'rejected',
                        'validated_at' => new MongoDB\BSON\UTCDateTime(),
                        'validated_by' => (int)$employeId,
                        'rejection_reason' => $reason
                    ]
                ]
            );
            return true;
        } catch (Exception $e) {
            error_log("Erreur rejet avis : " . $e->getMessage());
            return false;
        }
    }
}