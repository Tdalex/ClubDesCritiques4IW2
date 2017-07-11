<?php
/**
 * Club Des Critiques Extension
 *
 * @package clubdescritiques
 */

namespace ClubDesCritiques;

use ClubDesCritiques\Utilisateur as Utilisateur;

class ChatRoom{
	function __construct() {		
		// add_action( 'wp_ajax_check_updates', array( $this, 'ajax_check_updates_handler' ) );
		add_action( 'wp_ajax_join_room', array( $this, 'ajax_join_room_handler' ) );
	}
	
    public static function activate()
    {
        global $wpdb;
    }

    public static function getNextChat(){
        global $wpdb;

        $query = "SELECT ". $wpdb->posts .".ID, ".$wpdb->posts .".post_title  FROM ". $wpdb->posts ." INNER JOIN ". $wpdb->postmeta ." as metaA ON (". $wpdb->posts .".ID = metaA.post_id) WHERE cast(metaA.meta_value as datetime) >= now() AND metaA.meta_key = 'end_date' order by cast(metaA.meta_value as date) ASC";

		$result = $wpdb->get_results($query);
        if(!empty($result))
            return $result[0];

        return false;
    }

	public static function joinChatRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();
		
		$currentUser = get_field('current_user', $roomId); 
		$done = false;
		if(!empty($currentUser)){
			foreach($currentUser as $key => $cu){		
				$currentUser[$key]['user'] = $cu['user']['ID'];
				if($cu['user']['ID'] == $userId){
					$currentUser[$key]['user_timeout'] = self::timeOut(5);
					$done = true;
				}		
			}
		}
		if(!$done){
			$key++;
			$currentUser[$key]['user'] = $userId;
			$currentUser[$key]['user_timeout'] = self::timeOut(5);
		}
		update_field('field_5960de0ff4bab', $currentUser, $roomId); 
		return true;
	}

	public static function cleanCurrentUsers($roomId){
		$currentUser = get_field('current_user', $roomId); 
		$currentUserTmp = array();
		if($currentUser){
			$i = 0;
			foreach($currentUser as $cu){
				if(isset($cu['user']['ID']) && $cu['user_timeout'] > self::now()){
					$currentUserTmp[$i]['user'] = $cu['user']['ID'];
					$currentUserTmp[$i]['user_timeout'] = $cu['user_timeout'];
					$i++;
				}		
			}
		}
		update_field('field_5960de0ff4bab', $currentUserTmp, $roomId);
		return true;
	}
	
	public static function kickUser($roomId, $kickUserId){
		$currentUser = get_field('current_user', $roomId); 
		$currentUserTmp = array();
		if($currentUser){
			$i = 0;
			foreach($currentUser as $cu){
				if(isset($cu['user']['ID']) && $cu['user']['ID'] != $kickUserId){
					$currentUserTmp[$i]['user'] = $cu['user']['ID'];
					$currentUserTmp[$i]['user_timeout'] = $cu['user_timeout'];
					$i++;
				}		
			}
		}
		update_field('field_5960de0ff4bab', $currentUserTmp, $roomId);
		
		$parentId = $roomId;		
		if($room->post_parent != 0){
			$parentId = $room->post_parent;
		}
		
		$kickedFrom = get_field('kicked_from', 'user_'.$kickUserId);
		$kickedFromTmp = array();
		$done = false;
		if($kickedFrom){
			$i = 0;
			foreach($kickedFrom as $kf){
				$kickedFromTmp[$i] = $kf->ID;
				if($kf->ID == $parentId){
					$done = true;
				}	
				$i++;	
			}
		}
		if(!$done){
			$kickedFromTmp[] = $parentId;
		}
		update_field('field_59634a06080f6',$kickedFromTmp, 'user_'.$kickUserId);
		return true;
	}
	
	public static function isUserInRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();
		
		$currentUser = get_field('current_user', $roomId); 
		if($currentUser){
			foreach($currentUser as $cu){
				if(isset($cu['user']['ID']) && $cu['user']['ID'] == $userId){
					return true;
				}		
			}
		}
		return false;
	}
	
	public static function isUserKicked($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();
		
		$parentId = $roomId;		
		if($room->post_parent != 0){
			$parentId = $room->post_parent;
		}
		
		$kickedFrom = get_field('kicked_from', 'user_'.$userId);
		if($kickedFrom){
			foreach($kickedFrom as $kf){
				if($kf->ID == $parentId){
					return true;
				}	
			}
		}
		return false;
	}
	public static function roomAllNotes($roomId){
		$notes    = array();
		$chatroom = get_field('current_user', $roomId);
		if($chatroom){
			$product  = get_field('product', $roomId)[0];
			foreach($chatroom as $c){
				$notes[] = Utilisateur::getNotation($product->ID, $c['user']['ID']);
			}
		}
		
		return $notes;
	}

	public static function createNewRoom($roomId){
		$room 	    = get_post($roomId);
		$roomNumber = count(get_children(array('post_parent' => $roomId))) + 1;
		$args = array(
	        'post_author' => $room->post_author,
	        'post_title'  => $room->post_title.'_'.$roomNumber,
	        'post_status' => 'publish',
	        'post_type'   => 'chat-room',
	        'post_parent' => $roomId,
	    );
		$id = wp_insert_post($args);
		$token = wp_generate_password( 8, false );
		update_field('field_5960d25530cec',get_field('max_user',$roomId), $id);
		update_field('field_5960d14eb8a9c',get_field('start_date',$roomId), $id);
		update_field('field_5960d1e3b8a9d',get_field('end_date',$roomId), $id);
		update_field('field_5960d1f1b8a9e',get_field('product',$roomId), $id);
		update_field('field_5963361fc991f',$token, $id);
		update_field('field_5961f6bb438aa',$roomNumber++, $id);
		return $id;
	}
	
	public static function getUserRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();
		$room     	 = get_post($roomId);
		$allRooms 	 = array();
		$parentId    = $roomId;
		
		if($room->post_parent != 0){
			$parentId = $room->post_parent;
			$allRooms[] = get_post($room->post_parent);
		}else{			
			$allRooms[] = $room;
		}		
		
		foreach(get_children(array('post_parent' => $parentId)) as $child){
			$allRooms[] = $child;
		}
		
		foreach ($allRooms as $room){
			$currentUser = get_field('current_user', $room->ID); 
			self::cleanCurrentUsers($room->ID);	
			if(self::isUserInRoom($room->ID)){
				return $room->ID;
			}
		}
		return false;
	}

	public static function selectBestRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$room     	 = get_post($roomId);
		$allRooms 	 = array();
		$setRoom     = 0;
		$bestAvgNote = 0;
		$userNote    = Utilisateur::getNotation($product->ID, $userId);
		$parentId    = $roomId;
		
		if($room->post_parent != 0){
			$parentId = $room->post_parent;
			$allRooms[] = get_post($room->post_parent);
		}else{			
			$allRooms[] = $room;
		}		
		
		foreach(get_children(array('post_parent' => $parentId)) as $child){
			$allRooms[] = $child;
		}
		
		foreach ($allRooms as $room){
			self::cleanCurrentUsers($room->ID);	
			if(get_field('max_user', $room) > count($allNotes = self::roomAllNotes($room->ID))){
				$avgNote = (array_sum($allNotes) + $userNote)/(count($allNotes) + 1);
				if(count($allNotes) == 0){
					$setRoom 	 = $room->ID;
					break;
				}elseif(count($allNotes) < floor(get_field('max_user', $room)/4) && abs($avgNote - 2.5) < 2){
					$setRoom 	 = $room->ID;
					break;
				}elseif(abs($bestAvgNote - 2.5) > abs($avgNote - 2.5) && abs($avgNote - 2.5) < 1.5) {
					$bestAvgNote = array_sum($allNotes)/count($allNotes);
					$setRoom 	 = $room->ID;
				}
			}
		}
		
		if($setRoom == 0){
			$setRoom = self::createNewRoom($parentId);
		}
		self::joinChatRoom($setRoom);
		
		if($setRoom != $roomId){
			return Utilisateur::redirect(get_permalink($setRoom));
			
		}else{		
			return true;
		}
	}
	
	public static function changeRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		$currentUser = get_field('current_user', $roomId); 
		$currentUserTmp = array();
		if($currentUser){
			$i = 0;
			foreach($currentUser as $cu){
				if($cu['user']['ID'] !== $userId){
					$currentUserTmp[$i]['user'] = $cu['user']['ID'];
					$currentUserTmp[$i]['user_timeout'] = $cu['user_timeout'];
					$i++;
				}		
			}
		}
		update_field('field_5960de0ff4bab', $currentUserTmp, $roomId);
		self::selectBestRoom($roomId);
		return true;
	}
	
	public static function now(){
		$tz = 'Europe/Paris';
		$timestamp = time();
		$dt = new \DateTime("now", new \DateTimeZone($tz)); 
		$dt->setTimestamp($timestamp);
		return $dt->format('Y-m-d H:i:s');
	}
	
	public static function timeOut($timeout = 5){
		$tz = 'Europe/Paris';
		$timestamp = time();
		$dt = new \DateTime("now", new \DateTimeZone($tz)); 
		$dt->modify('+'.$timeout.' minutes');
		return $dt->format('Y-m-d H:i:s');
	}
}