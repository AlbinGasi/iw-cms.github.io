<?php 
class Users extends Entity
{
	public static $table_name;
	public static $key_column = "user_id";
	private static $_db;
	
	public static function Init(){
		self::$_db = Connection::get_instance();
		self::$table_name = Config::get('table_prefix')."users";
	}
	
	public static function setUserActivity($username,$activity,$post_id=null,$type=null,$username2=null){
		$date = date('d.m.Y');// . " | " . date('H:m:s A');
		if($post_id != null){
			if($type != null){
				if($username2 != null){
					$stmt = self::$_db->prepare("INSERT INTO ".Config::get('table_prefix')."user_activity (username,activity,post_id,type,date,username2) VALUES (?,?,?,?,?,?)");
					$stmt->bindValue(1, $username);
					$stmt->bindValue(2, $activity);
					$stmt->bindValue(3, $post_id);
					$stmt->bindValue(4, $type);
					$stmt->bindValue(5, $date);
					$stmt->bindValue(6, $username2);
					$stmt->execute();
				}else{
				$stmt = self::$_db->prepare("INSERT INTO ".Config::get('table_prefix')."user_activity (username,activity,post_id,type,date) VALUES (?,?,?,?,?)");
				$stmt->bindValue(1, $username);
				$stmt->bindValue(2, $activity);
				$stmt->bindValue(3, $post_id);
				$stmt->bindValue(4, $type);
				$stmt->bindValue(5, $date);
				$stmt->execute();
				}
			}else{
				$stmt = self::$_db->prepare("INSERT INTO ".Config::get('table_prefix')."user_activity (username,activity,post_id,date) VALUES (?,?,?,?)");
				$stmt->bindValue(1, $username);
				$stmt->bindValue(2, $activity);
				$stmt->bindValue(3, $post_id);
				$stmt->bindValue(4, $date);
				$stmt->execute();
			}
		}else{
			$stmt = self::$_db->prepare("INSERT INTO ".Config::get('table_prefix')."user_activity (username,activity,date) VALUES (?,?,?)");
			$stmt->bindValue(1, $username);
			$stmt->bindValue(2, $activity);
			$stmt->bindValue(3, $date);
			$stmt->execute();
		}
		
	}
	
	public static function user_login($username,$password){
				$stmt = self::$_db->prepare("SELECT username, password, user_id, user_status FROM ".Config::get('table_prefix')."users WHERE username = :name AND password = :password LIMIT 1");
				$stmt->bindParam(':name', $username);
				$stmt->bindParam(':password', $password);
				$stmt->execute();
				$user = $stmt->fetch(PDO::FETCH_ASSOC);
				
				if($stmt->rowCount() > 1){
					Alerts::get_alert("danger","Ther's error, contact the administrator to resolve.");
					return false;
				}else if($stmt->rowCount() == 1){
					if($user['user_status'] == 1){
						Alerts::get_alert("danger","Activation!","You must activate your account.");
					}else if($user['user_status'] == 0){
						Alerts::get_alert("danger","Sorry!","Your account is deactivated, contact the site administrator for new activation.");
					}else{
						if($user['username'] == $username && $user['password'] == $password){
							$_SESSION['user_ag'] = array();
							if($user['user_status'] == 351){
								$_SESSION['user_ag']['adminuser'] = "admin351";
							}
							$_SESSION['user_ag']['logedin'] = "successUserLogged_871";
							$_SESSION['user_ag']['user_id'] = $user['user_id'];
							Alerts::get_alert("info","Successful", "You will be redirect to index page.");
						}else{
							Alerts::get_alert("danger","Error","Check your username or password.");
						}
					}
					
					
					
				}else{
					Alerts::get_alert("danger","Error","Check your username or password.");
				}
	}

	public static function is_admin(){
		if(isset($_SESSION['user_ag']['adminuser'])){
			if($_SESSION['user_ag']['adminuser'] == "admin351"){
			return true;
			}else{
			return false;
			}
		}else{
			return false;
		}
	}
	
	public static function is_admin2($user_id){
		$stmt = self::$_db->prepare("SELECT user_status FROM ".Config::get('table_prefix')."users WHERE user_id = :id LIMIT 1");
		$stmt->bindParam(':id', $user_id);
		$stmt->execute();
		
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if($res['user_status'] == 351 || $res['user_status'] == 350){
			return true;
		}else{
			return false;
		}
	}
	
	public static function is_moderator($user_id){
		$stmt = self::$_db->prepare("SELECT user_status FROM ".Config::get('table_prefix')."users WHERE user_id = :id LIMIT 1");
		$stmt->bindParam(':id', $user_id);
		$stmt->execute();
		
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if($res['user_status'] == 250){
			return true;
		}else{
			return false;
		}
	}
	
	public static function is_writer($user_id){
		$stmt = self::$_db->prepare("SELECT user_status FROM ".Config::get('table_prefix')."users WHERE user_id = :id LIMIT 1");
		$stmt->bindParam(':id', $user_id);
		$stmt->execute();
		
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if($res['user_status'] == 3){
			return true;
		}else{
			return false;
		}
	}
	
	public static function is_loggedin(){
		if(isset($_SESSION['user_ag']['logedin'])){
			if($_SESSION['user_ag']['logedin'] == "successUserLogged_871"){
				if(isset($_SESSION['user_ag']['user_id'])){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	

}
Users::Init();





?>