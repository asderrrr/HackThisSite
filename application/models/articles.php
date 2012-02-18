<?php
class articles extends mongoBase {
    const KEY_SERVER = "mongo:server";
    const KEY_DB     = "mongo:db";

    var $db;
    var $mongo;
    
    public function __construct(Mongo $mongo) {
        $db       = Config::get(self::KEY_DB);
        $this->mongo = $mongo->$db;
        $this->db = $mongo->$db->content;
    }
    
    private function UserInfo($record, $single = false) {
        if (empty($record))
            return $record;
        
        if ($single) {
            if (is_string($record['user'])) return $record;
            $record['user'] = MongoDBRef::get($this->mongo, $this->clean($record['user']));
        } else {
            foreach ($record as $key => $entry) {
                if (is_string($entry['user'])) continue;
                $record[$key]['user'] = MongoDBRef::get($this->mongo, $this->clean($entry['user']));
            }
        }
        
        return $record;
    }

    public function getNewPosts($cache = true) {
        $news = $this->realGetNewPosts();
        return $news;
    }

    public function get($id, $cache = true, $idlib = true) {
        if ($cache && apc_exists('news_' . $id)) return apc_fetch('news_' . $id);

        $news = $this->realGet($id, $idlib);
        if ($cache && !empty($news)) apc_add('news_' . $id, $news, 10);
        return $news;
    }
    
    public function getNextUnapproved() {
        $record = $this->db->findOne(array('published' => false, 'ghosted' => false));
        $record = $this->UserInfo($record, true);
        return $record;
    }

    public function create($title, $text, $tags) {
        $ref = MongoDBRef::create('users', Session::getVar('_id'));
        
        $func = function($value) { return trim($value); };
        
        $entry = array(
            'type' => 'article', 
            'title' => substr($this->clean($title), 0, 100), 
            'body' => substr($this->clean($text), 0, 1000), 
            'tags' => array_map($func, explode(',', $this->clean($tags))),
            'user' => $ref, 
            'date' => time(), 
            'commentable' => true, 
            'published' => false, 
            'ghosted' => false, 
            'flaggable' => false
            );
        $this->db->insert($entry);
        
        $id = $entry['_id'];
        unset($entry['_id'], $entry['user'], $entry['date'], $entry['commentable'],
            $entry['published'], $entry['flaggable']);
        Search::index($id, $entry);
    }

    public function edit($id, $title, $text, $tags) {
        $func = function($value) { return trim($value); };
        
        $update = array(
            'type' => 'article',
            'title' => $this->clean($title), 
            'body' => $this->clean($text), 
            'tags' => array_map($func, explode(',', $this->clean($tags))),
            'ghosted' => false
            );
        
        $this->db->update(array('_id' => new MongoId($id)), array('$set' => $update));
        
        unset($update['_id'], $update['commentable']);
        Search::index($id, $update);
    }

    public function delete($id) {
        $this->db->update(array('_id' => $this->_toMongoId($id)), array('$set' => array('ghosted' => true)));
        Search::delete($id);
        return true;
    }

    public function realGetNewPosts() {
        $posts = $this->db->find(
            array(
                'type' => 'article',
                'ghosted' => false,
                'published' => true
            )
        )->sort(array('date' => -1))
         ->limit(10);
         $posts = iterator_to_array($posts);
         $posts = $this->UserInfo($posts);
         
         return $posts;
    }

    public function realGet($id, $idlib) {
        if ($idlib) {
            $idLib = new Id;

            $query = array('type' => 'article', 'ghosted' => false, 'published' => true);
            $keys = $idLib->dissectKeys($id, 'news');

            $query['date'] = array('$gte' => $keys['date'], '$lte' => $keys['date'] + $keys['ambiguity']);
        } else {
            $query = array('_id' => $this->_toMongoId($id), 'type' => 'article', 'published' => true, 'ghosted' => false);
        }

        $results = $this->db->find($query);

        if ($results->count() == 0) return 'Invalid id.';
        if (!$idlib)
            return iterator_to_array($results);
        
        $toReturn = array();

        foreach ($results as $result) {
            if (!$idLib->validateHash($id, array('ambiguity' => $keys['ambiguity'],
                'reportedDate' => $keys['date'], 'date' => $result['date'],
                'title' => $result['title']), 'news'))
                continue;

            $result = $this->UserInfo($result, true);
            array_push($toReturn, $result);
        }

        return $toReturn;
    }
    
    public function approve($id) {
        $this->db->update(array('_id' => $this->_toMongoId($id)), array('$set' => array('published' => true)));
        return true;
    }
}
