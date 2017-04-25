<?php
// use database class
require_once '../class/db.class.php';

class Caches
{
	var $db = null;

	public function __construct($database = NULL)
	{
		if (isset($database))
		{
			$this->db = $database;		
		}
		else 
		{
			// create a new database class instace
			$this->connectDatabase ();
		}
	}
	
	/**
	 * Create a new DB connection.
	 */
	private function connectDatabase() {
		$this->db = new Database ();
	}
	
	/**
	 * Get number of total active caches
	 * @return number of all activ caches
	 */
	public function getActiveCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM cache WHERE Status <> 'Expired'");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}
	
	/**
	 * Get number of all actions
	 */
	public function getActionTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity");
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
	}

	/**
	 * Get number of 'sown' actions
	 */
	public function getSownTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'sower'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get number of 'tend' actions
	 */
	public function getTendTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'tender'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get number of 'rescue' actions
	 */
	public function getRescueTotalCount()
	{
		$this->db->query("SELECT COUNT(*) as cnt FROM activity WHERE EntryType = 'adjunct'");
		$result = $this->db->single();
	
		$this->db->closeQuery();
	
		return $result['cnt'];
	}
	
	/**
	 * Get values of a systems cache
	 */
// 	public function getCacheInfo(string $system)
	public function getCacheInfo($system)
	{
		// check if a system is supplied
		if (!isset($system))
		{
			return;
		}
		
		$this->db->query("SELECT * FROM cache WHERE System = :system AND Status <> 'Expired'");
		$this->db->bind(':system', $system);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result;
	}
	
	/**
	 * Check if a cache is allowed to be tender
	 * @param unknown $system the system to check
	 * @return void|mixed 0 - cache is not allowed to be tendet; 1- cache can be tended
	 */
	public function isTendingAllowed($system)
	{
		// check if a system is supplied
		if (!isset($system))
		{
			return;
		}

		// select 0/1 from cache if time diff is >= 24 hours
		$this->db->query("SELECT count(1) as cnt FROM cache WHERE System = :system AND Status <> 'Expired' and (time_to_sec(timediff(CURRENT_TIMESTAMP(), LastUpdated)) / 3600) >= 24");
		$this->db->bind(':system', $system);
		
		$result = $this->db->single();
		
		$this->db->closeQuery();
		
		return $result['cnt'];
		}
}
?>