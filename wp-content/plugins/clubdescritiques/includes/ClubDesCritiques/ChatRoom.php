<?php
/**
 * Club Des Critiques Extension
 *
 * @package clubdescritiques
 */

namespace ClubDesCritiques;

use ClubDesCritiques\Utilisateur as Utilisateur;

class ChatRoom{
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
				if($cu['user_timeout'] > self::now()){
					$currentUserTmp[$i]['user'] = $cu['user']['ID'];
					$currentUserTmp[$i]['user_timeout'] = $cu['user_timeout'];
					$i++;
				}		
			}
		}
		update_field('field_5960de0ff4bab', $currentUserTmp, $roomId);
		return true;
	}
	
	public static function isUserInRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();
		
		$currentUser = get_field('current_user', $roomId); 
		if($currentUser){
			foreach($currentUser as $cu){
				if($cu['user']['ID'] == $userId){
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
	        'post_type'   => 'chat_room',
	        'post_parent' => $roomId,
	    );
		$id = wp_insert_post($args);

		update_field('start_date',get_field('start_date',$roomId), $id);
		update_field('end_date',get_field('end_date',$roomId), $id);
		return $id;
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
		
		self::cleanCurrentUsers($roomId);	
		if(self::isUserInRoom($roomId)){
			$setRoom = $room->ID;
		}else{
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
		}
		
		self::joinChatRoom($setRoom);
		
		if($setRoom != $roomID){
		}else{		
			return true;
		}
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
