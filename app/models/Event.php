
<?php

class Event{

	static function get_all_events()
	{
		$sql = "SELECT * FROM event";
		$r = DB::sql($sql);
		return $r;
	}

	static function getevent($id)
	{
		return $r=DB::sql("select * from event where id= :id",array(':id' => $id));
	}

	static function createevent($id,$title,$sort,$label,$location,$starttime,$endtime,$introduction)
	{
		DB::sql("insert into event(title,sort,label,location,starttime,endtime,introduction,creator) values(:title,:sort,:label,:location,:starttime,:endtime,:introduction,:uid)",
			array(
				':title' => $title,
				':sort' => $sort,
				':label' => $label,
				':location'=>$location,
				':starttime'=>$starttime,
				':endtime'=>$endtime,
				':introduction' => $introduction,
				':uid' => $id
				)
			);
		$event_id=DB::get_insert_id();
		return $event_id;
	}

	static function joinevent($user_id,$event_id,$starttime,$endtime)
	{
		DB::sql("insert into user_joinevent(user_id,event_id,starttime,endtime)values(:user_id,:event_id,:starttime,:endtime)",array(':user_id'=>$user_id,':event_id'=>$event_id,':starttime'=>$starttime,':endtime'=>$endtime));
	}

	static function get_participant($event_id)
	{
		return DB::sql("select distinct * from snsUsers,user_joinevent where snsUsers.id = user_id and event_id = :event_id",array(':event_id' => $event_id)); 
	}

	static function my_create_event($user_id)
	{
		return DB::sql("select * from event where creator = :user_id",array(':user_id' => $user_id));
	}

	static function my_join_event($user_id)
	{
		$event_ids = DB::sql("select distinct(event_id) from user_joinevent where user_id = :user_id",array(':user_id' => $user_id));
		return self::get_events($event_ids);
	}

	static function get_events($event_ids)
	{
		$events = array();
		foreach($event_ids as $values)
			foreach($values as $id)
			{
				$event = self::getevent($id);
				$events[] = $event[0];
			}
		return $events;
	}

};

?>
