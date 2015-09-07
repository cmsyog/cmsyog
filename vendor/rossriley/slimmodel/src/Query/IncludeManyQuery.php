<?php

namespace SlimModel\Query;

/**
 * Supplied with a set of options and a DBAL connection this class builds a query to include data.
 * from a many to many join.
 *
 */
class IncludeManyQuery
{

    public $db;
    public $options;
    public $key_filter;
    public $query;


    public function __construct($db = false, $include_options = [], $key_filter = [])
    {
        $this->db = $db;
        $this->options = $include_options;
        $this->key_filter = $key_filter;
    }


    public function build()
    {
        $this->query = $this->db->createQueryBuilder();
        $this->query->select("l.id as lkey, r.*")
            ->from($this->options["origin"],"l")
            ->leftjoin("l", $this->options["join"], "j", "j.{$this->options['join_left_key']} = l.{$this->options['key']}")
            ->leftjoin("l", $this->options["table"],"r", "r.{$this->options['join_key']} = j.{$this->options['join_right_key']}")
            ->where("r.{$this->options['join_key']} IS NOT NULL");

        if (count($this->key_filter)) {
            $this->query->andwhere($this->query->expr()->in("l.{$this->options['key']}", $this->key_filter));
        }

    }

    public function execute()
    {
        $this->build();
        return $this->query->execute()->fetchAll();
    }



}