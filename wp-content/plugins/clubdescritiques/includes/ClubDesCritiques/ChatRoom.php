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

        $query = "SELECT DISTINCT ". $wpdb->posts .".ID, ".$wpdb->posts .".post_title  FROM ". $wpdb->posts ." INNER JOIN ". $wpdb->postmeta ." as metaA ON (". $wpdb->posts .".ID = metaA.post_id) WHERE cast(metaA.meta_value as date) >= now() AND metaA.meta_key = 'start_date'";
        $result = $wpdb->get_results($qurey);
        if(!empty($result))
            return $result;

        return false;
    }

	public static function joinChatRoom($roomId, $userId = null){
		if($userId === null)
			$userId = get_current_user_id();

		setcookie("last_room", $roomId, time()+3600);
		foreach($GLOBALS['chatroom'] as $chat => $value){
			if($chat != $roomId && ){
				unset($GLOBALS['chatroom'][$chat][$userId]);
			}
		}
		$GLOBALS['chatroom'][$roomId][$userId] = time() + 300;
		return true;
	}

	public static function cleanCurrentUsers(){
		foreach($GLOBALS['chatroom'] as $chat => $value){
			foreach($GLOBALS['chatroom'][$chat] as $user => $timeout){
				if($timeout < time()){
					unset($GLOBALS['chatroom'][$chat][$user]);
				}
			}
		}
		return true;
	}

	public static function roomAllNotes($roomId){
		$notes    = array();
		$chatroom = $GLOBALS['chatroom'];
		$product  = get_field('product', $roomId);
		if(isset($chatroom[$roomId])){
			foreach($chatroom[$roomId] as $user => $value){
				$notes[] = Utilisateur::getNotation($product->ID, $user);
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
		self::cleanCurrentUsers();
		if($userId === null)
			$userId = get_current_user_id();

		$room     	 = get_post($roomId);
		$allRooms 	 = array();
		$setRoom     = 0;
		$bestAvgNote = 0;
		$userNote    = Utilisateur::getNotation($product->ID, $userId);

		if($room->post_parent != 0){
			$allRooms[] = get_post($room->post_parent);
			$roomId = $room->post_parent;
		}

		$product 	 = get_field('product', $roomId);
		foreach(get_children(array('post_parent' => $roomId)) as $child){
			$allRooms[] = $child;
		}

		foreach ($allRooms as $room){
			if(get_field('max_user', $room) < count($allNotes = self::roomAllNotes($room->ID))){
				$avgNote = (array_sum($allNotes) + $userNote)/(count($allNotes) + 1);
				if(count($allNotes) < floor(get_field('max_user', $room)/4) && abs($avgNote - 2.5) < 2){
					$bestAvgNote = array_sum($allNotes)/count($allNotes);
					$setRoom 	 = $room->ID;
					break;
				}elseif(abs($bestAvgNote - 2.5) > abs($avgNote - 2.5) && abs($avgNote - 2.5) < 1.5) {
					$bestAvgNote = array_sum($allNotes)/count($allNotes);
					$setRoom 	 = $room->ID;
				}
			}
		}

		if($setRoom == 0){
			$setRoom = self::createNewRoom($roomId);
		}

		self::joinChatRoom($setRoom);
		return true;
	}
}
