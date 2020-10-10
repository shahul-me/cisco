 <?php  
 
	//Count		
	$intCnt 	=  $argv[1];
	$connection = '';
	
	//Insert Query
	$router = new clsRouter();
	$router->insertQuery($intCnt);
	
	
    class clsRouter { 
    	private $connection; 
		private $HostName = "localhost"; 
		private $UserName = "root"; 
		private $Password = ""; 
		private $dbname   = "cisco";
         	
				
		public function insertQuery($intCnt) {
			
			// Create connection 
			$connection = new mysqli($this->HostName, $this->UserName, $this->Password, $this->dbname); 
			  
			// Check connection 
			if ($connection->connect_error) {
				die("Connection failed: " . $connection->connect_error);
			} 			
			
			$query = 'INSERT INTO routers (`sapid`, `hostname` , `loopback`, `mac`) VALUES ';
				$query_parts = array();
				for($x=0; $x< $intCnt; $x++){
					$query_parts[] = "('" . substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 18) . "', '" . $this->RandomDomain() . "', '" . long2ip((mt_rand()*mt_rand(1,2))+mt_rand(0,1)) . "', '" . implode(':', str_split(substr(md5(mt_rand()), 0, 12), 2)) . "')";
				}
			$query .= implode(',', $query_parts);

			if ($connection->multi_query($query) === TRUE){
				echo "$intCnt  New records created successfully!!";
			}else{
				echo "Error: " . $query . "<br>" . $connection->error;
			}
			$connection->close();
		}	
		
		
		public function RandomDomain() { 				
			$generated_string = ""; 		  
			$domain = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
			for ($i = 0; $i < 12; $i++) { 
				$index = rand(0, strlen($domain) - 1);
				$generated_string = $generated_string . $domain[$index]; 
			}
			return $generated_string; 
		} 
    } 
	
        ?>