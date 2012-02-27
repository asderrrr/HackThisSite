<?php
class lectures extends baseModel {
    
    var $hasSearch = false;
    var $hasRevisions = false;
    
    public function get($id) {
        $record = $this->db->findOne(array(
            '_id' => new MongoId($id), 
            'type' => 'lecture', 
            'ghosted' => false
            ));
            
        if (empty($record)) return null;
        
        return $record;
    }
    
    public function getNew() {
        $records = $this->db->find(array(
            'time' => array('$gte' => time()), 
            'ghosted' => false
            ))
            ->sort(array('time' => -1));
        if ($records->count() == 0) return 'No upcoming lectures.';
        return $records;
    }
    
    public function validate($title, $lecturer, $description, $time, $duration, $creating = true) {
        $title = substr($this->clean($title), 0, 100);
        $lecturer = substr($this->clean($lecturer), 0, 80);
        $description = substr($this->clean($description), 0, 2000);
        $time = strtotime($time);
        $duration = strtotime($duration) - time();
        
        if (empty($title)) return 'Invalid title.';
        if (empty($lecturer)) return 'Invalid lecturer';
        if (empty($description)) return 'Invalid lecturer';
        if (empty($time))return 'I can\'t understand the date you chose.';
        if (empty($duration)) return 'I can\'t understand the duration you chose.';
        
        $entry = array(
            'type' => 'lecture', 
            'title' => $title, 
            'lecturer' => $lecturer, 
            'description' => $description,
            'time' => $time, 
            'duration' => $duration, 
            'ghosted' => false
            );
        
        return $entry;
    }
    
}
