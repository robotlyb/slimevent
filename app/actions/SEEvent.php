<?php

class SEEvent{

	function __construct()
	{
		if(Account::is_login() === FALSE)
			F3::reroute('/');
	}

	function sorts()
	{
		$str = F3::get('PARAMS.str');  //这个是用户要参加的活动id
		$events = Event::sorts($str);	
		F3::set('events',$events);
		echo Template::serve('hello.html');
	}

	function all_events()
	{
		$events = Event::get_all_events();
		F3::set('events',$events);
		echo Template::serve('/event/.html');

	}

	function create(){
		$id = Account::the_user_id(); //这个是当前登录用户的id
		//你需要修改下面的代码和models里Event.php里的createevent()函数,使得创建活动用户的id也存到event表里

		$a =Event::createevent(
				$id,
				$_POST["title"],
				$_POST["sort"],
				$_POST["label"],
				$_POST["location"],
				$_POST["starttime"],
				$_POST["endtime"],
				$_POST["introduction"]
			);

		if(isset($_FILES['avatar']))  //上传图像
			if($_FILES['avatar']['tmp_name'])
				if(is_uploaded_file($_FILES['avatar']['tmp_name']))
				{
					$path = "avatar/";
					copy($_FILES['avatar']['tmp_name'],$path.$a.".jpg");
				}


		F3::reroute("/event/{$a}");
	}
	function del()
	{
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id
		Event::del_event($eid);
		F3::reroute("/event/my");
	}
	function show_join()
	{
		echo Template::serve('join.html');
	}
	function joins() 
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id
		Event::joinevent($uid,$eid,$_POST["starttime"],$_POST["endtime"]);

		F3::reroute("/event/{$eid}");
	}

	function participants()
	{
		F3::set('route', array('discover', 'participants'));
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id
		//	echo "参加活动的id: ".$eid;

		$event = Event::getevent($eid);
		F3::set('event',$event[0]);

		$result = Event::get_participant($eid);
		$values = array();
		foreach($result as $row)
		{
		
			$values[]=array(
					'name' => $row["name"],
					'starttime' => $row["starttime"],
					'endtime' => $row["endtime"]);
		}
		$time = Event::get_public_time($eid);
		F3::set('values', $values);
		F3::set('time',$time);
		echo Template::serve('participants.html');

		//显示id为eid活动的所有参与者信息(名字,起始空闲时间)
	}

	function my()
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id
		//	echo "当前登录用户的id: ".$uid;

		$my_create = Event::my_create_event($uid);
		$my_join = Event::my_join_event($uid);
	

        F3::set('my_create',$my_create);
        F3::set('my_join',$my_join);

		echo Template::serve('my_event.html');

	}

	function show_create(){
		echo Template::serve('create.html');
	}

	function show(){
		F3::set('route', array('discover', 'intro'));
		$event = Event::getevent(F3::get('PARAMS.eventID'));
		$event[0]['introduction'] = Sys::convert_br_space($event[0]['introduction']);

		$now = strtotime(date("Y-m-d"));
		$start = strtotime($event[0]['starttime']);
		$end = strtotime($event[0]['endtime']);
		if($start > $now)
		{
			$event[0]['status'] = "还有". ($start-$now)/86400 . "天开始";
			$event[0]['status_num'] = -1;
		}
		else if($end < $now)
		{
			$event[0]['status'] = "已结束";
			$event[0]['status_num'] = 1;
		}
		else
		{
		 	$event[0]['status'] = "进行中";
			$event[0]['status_num'] = 0;
		}
		F3::set('event',$event[0]);
		F3::set('uid',Account::the_user_id());

		echo Template::serve('event/event.html');
	}

	function photos(){
		F3::set('route', array('discover', 'photo'));

		echo Template::serve('event/photo.html');
	}
	function discussion(){
		F3::set('route', array('discover', 'discussion'));

		echo Template::serve('event/discussion.html');
	}
};

?>
