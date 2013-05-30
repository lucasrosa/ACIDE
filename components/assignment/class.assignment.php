<?php

	/*
	 *  Copyright (c) UPEI sbateman@upei.ca lrosa@upei.ca
	 */

require_once('../../common.php');

class Assignment extends Common {

    //////////////////////////////////////////////////////////////////
    // PROPERTIES
    //////////////////////////////////////////////////////////////////
	
    public $id         						= ''; // "id" => "Assignment 1",
	public $due_date						= ''; // "due_date" => new MongoDate(strtotime("2010-01-15 00:00:00")),
	public $submitted_date					= ''; // "submitted_date" => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
	public $allow_late_submission			= ''; // "allow_late_submission" => 2,
	public $description_url					= ''; // "description_url" => "http://www.google.ca/",
	public $maximum_number_group_members	= ''; // "maximum_number_group_members" => 2
	
    //////////////////////////////////////////////////////////////////
    // METHODS
    //////////////////////////////////////////////////////////////////

    // -----------------------------||----------------------------- //

    //////////////////////////////////////////////////////////////////
    // 
    //////////////////////////////////////////////////////////////////

    public function asasasd (){
    	
    }
}

?>