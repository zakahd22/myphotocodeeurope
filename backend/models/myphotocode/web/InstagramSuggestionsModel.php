<?php
require_once G_PATH . "models/baseModel.php";

class InstagramSuggestionsModel extends baseModel{
//    public $event;
//    public $events_array;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get the event from the id
     */
    public function getInstagramSuggestions(){
        return $this->select('InstagramSuggestions');
    }
    
    public function getInstagramSuggestionsByWordTypePais($word, $type, $pais){
        
        $this->setFilter('word', '=', $word, "AND");
        $this->setFilter('type', '=', $type, "AND");
        $this->setFilter('pais', '=', $pais, "AND");
       
        
        
        $query = $this->select('InstagramSuggestions');
        
        return $query;
    }
    public function insertInstagramSuggestions(){
        $query = $this->insert('InstagramSuggestions');
        
        return $query;
    }
    
    public function updateInstagramSuggestions($word, $type, $pais, $updates){
        $this->setFilter('word', '=', $word, "AND");
        $this->setFilter('type', '=', $type, "AND");
        $this->setFilter('pais', '=', $pais, "AND");
        
        $query = $this->update('InstagramSuggestions', $updates);
        
        return $query;
    }
    
}
