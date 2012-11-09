
<?php

class Event{

	static function sorts($str)
	{
		$sql = "SELECT * FROM event WHERE `sort` = :str";
		$r = DB::sql($sql, array(':str' => $str));
		return $r;
	}

	static function status($status)
	{
		$now = date("Y-m-d");
		if($status == -1)
			$sql = "SELECT * FROM `event` WHERE `starttime` > '$now'";
		if($status == 0)
			$sql = "SELECT * FROM `event` WHERE `starttime` <= '$now' AND `endtime` >= '$now'";
		if($status == 1)
			$sql = "SELECT * FROM `event` WHERE `endtime` < '$now'";
		$r = DB::sql($sql);
		return $r;
	}

	static function del_event($id)
	{
		$sql = "DELETE  FROM event WHERE `id` = :eid";
		$r = DB::sql($sql, array(':eid' => $id));
	}
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

	static function editevent($eid,$id,$title,$sort,$label,$location,$starttime,$endtime,$introduction)
	{
		self::del_event($eid);
		DB::sql("insert into event(id,title,sort,label,location,starttime,endtime,introduction,creator) values(:eid,:title,:sort,:label,:location,:starttime,:endtime,:introduction,:uid)",
				array(
					':eid' => $eid,
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
		return $eid;
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
	static function get_public_time($event_id)
	{
		$time = array();
		$starttime = DB::sql("select starttime from user_joinevent where event_id = :event_id group by starttime order by count(starttime) desc limit 1",array(':event_id' =>$event_id));
		$time[0] =$starttime[0]["starttime"];

		$endtime = DB::sql("select endtime from user_joinevent where starttime = :time and event_id = :event_id group by starttime order by count(starttime) desc limit 1",array(':time'=>$time[0],':event_id'=>$event_id));
		$time[1] = $endtime[0]["endtime"];
		return $time;

	}
	static function sendmessage($uid,$eid)
	{
		$user_id = DB::sql("select distinct(user_id) from user_joinevent where event_id = :event_id",array(':event_id' =>$eid));
		$event_name = DB::sql("select title from event where id = :eid",array(':eid'=>$eid))[0]['title'];
		$public_time =  Event::get_public_time($eid);
		$content = '下面的活动即将开始，请您注意参加！<br />活动名称:'.$event_name.'<br />活动公共时间:'.$public_time[0].' - '.$public_time[1].'<br />';
//		echo $content;

//		Code::dump($event_name);
//		Code::dump($sql);
//		echo $event_name;
		foreach($user_id as $user_id)
		{
//			echo $user_id['user_id'];
			DB::sql("insert into message(to_id,from_id,content,is_read) values(:uid,:user_id,:content,0)",array(':uid'=>$uid,':user_id'=>$user_id['user_id'],':content' => $content));
		}	
	}

};

?>
