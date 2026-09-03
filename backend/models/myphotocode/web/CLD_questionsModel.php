<?php
require_once G_PATH . "models/baseModel.php";

class CLD_questionsModel extends baseModel{

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get Questions the event_id
     */
    public function getQuestions($event){
        $this->setFilter('event', '=', $event);
        return $this->select('CLD_questions');
    }
    
        
    public function getEventsByQuestionNumber($event_id, $numQuestion = 1){
        $this->setFilter('event', '=', $event_id);
        $this->setFilter('question_number', '=', $numQuestion, "AND");
        return $this->select('CLD_questions');
    }
}
