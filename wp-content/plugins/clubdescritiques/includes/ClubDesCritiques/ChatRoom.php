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
		update_field('current_room', array($roomId), $userId);
		return true;
	}

	public static function roomAllNotes($roomId){
		$notes = array();
		$users = reset(
		get_users(
			array(
				'meta_key' => 'current_room',
				'meta_value' => array($roomId),
				)
			)
		);
		$product = get_field('product', $roomId);
		foreach($users as $user){
			$notes[] = Utilisateur::getNotation($product->ID, $user->ID);
		}

		return $notes;
	}

	public static function createNewRoom($roomId){
		$room = get_post($roomId);
		$args = array(
	        'post_author' => $room->post_author,
	        'post_title' => $room->post_title.'_2',
	        'post_status' => 'publish',
	        'post_type' => 'chat_room',
	        'post_parent' => $roomId,
	    );
		$id = wp_insert_post($args);

		//populate ACF

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

		if($room->post_parent != 0){
			$allRooms[] = get_post($room->post_parent);
			$roomId = $room->post_parent;
		}

		$product 	 = get_field('product', $roomId);
		$allRooms[]  = get_children(array('post_parent' => $roomId));

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
