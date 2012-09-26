
<?php

class Event{

	static function getevent($id)
	{
		return $r=DB::sql("select * from event where id= :id",array(':id' => $id));
	}

	static function createevent($id,$title,$sort,$label,$location,$starttime,$endtime,$introduction)
	{
		DB::sql("insert into event(title,sort,label,location,starttime,endtime,introduction) values(:title,:sort,:label,:location,:starttime,:endtime,:introduction)",array(':title' => $title,':sort' => $sort,':label' => $label,':location'=>$location,':starttime'=>$starttime,':endtime'=>$endtime,':introduction' => $introduction));

		$event_id=DB::get_insert_id();
		DB::sql("insert into user_createevent(user_id,event_id)values(:id,:event_id)",array(':id'=>$id,':event_id'=>$event_id));
		return $event_id;
	}
	static function joinevent($user_id,$event_id,$starttime,$endtime)
	{
		echo $starttime;
		echo $endtime;
		DB::sql("insert into user_joinevent(user_id,event_id,starttime,endtime)values(:user_id,:event_id,:starttime,:endtime)",array(':user_id'=>$user_id,':event_id'=>$event_id,':starttime'=>$starttime,':endtime'=>$endtime));
	}
	static function get_participant($event_id)
	{
		return DB::sql("select name,starttime,endtime from snsUsers,user_joinevent where snsUsers.id = user_id and event_id = :event_id",array(':event_id' => $event_id)); 
	}
	static function my_create_event($user_id)
	{
		return DB::sql("select event_id from user_createevent where user_id = :user_id",array(':user_id' => $user_id));
	}
	static function my_join_event($user_id)
	{

		return DB::sql("select event_id from user_joinevent where user_id = :user_id",array(':user_id' => $user_id));
	}


};

?>
