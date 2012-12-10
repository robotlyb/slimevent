<?php

	/**
	 * 用户赞活动表
	 * @package Slimevent
	 */

class PraiseList{

	/**
	 * 检查活动是否能赞或者取消赞
	 * @param $eid
	 */
	private static function check($eid)
	{
		$e = Event::show($eid);

		if($e['status'] != F3::get('EVENT_PASSED_STATUS')) 
			Sys::error(F3::get('EVENT_NOT_PRAISE'),$eid);		//活动未审核通过去 无法赞
	}

	/**
	 * 新增加一条用户赞活动的记录
	 * @param $uid 
	 * @param $eid
	 */
	static function add($uid, $eid)
	{
		self::check($eid);

		$sql = "SELECT * FROM `praise` WHERE `uid` = :uid AND `eid` = :eid";
		$r = DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));
		if(count($r) > 0)
			Sys::error(F3::get('HAVE_PRAISED'),$eid);	//已经赞过

		$sql = 'INSERT INTO `praise` (`uid`, `eid`, `time`) VALUES (:uid, :eid, :time)';
		DB::sql($sql, array(':uid' => $uid, ':eid' => $eid, ':time' => time()));
	}

	/**
	 * 删除一条用户赞活动的记录
	 * @param $uid 
	 * @param $eid
	 */
	static function remove($uid, $eid)
	{
		self::check($eid);

		$sql = "DELETE FROM `praise` WHERE `uid` = :uid AND `eid` = :eid";
		DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));
	}

	/**
	 * 返回赞$eid活动的用户信息(uid, time)
	 * @param $eid
	 * @return array 用户赞活动记录关联数组
	 */
	static function get_praise_user($eid)
	{
		$sql = "SELECT `uid`, `time` FROM `praise` WHERE `eid` = :eid";
		return DB::sql($sql, array(':eid' => $eid));
	}

	/*
	 * 返回$uid用户赞过的所有活动信息(eid, time)
	 * @return array 用户赞活动记录关联数组
	 */
	 */
	static function get_praise_event($uid)
	{
		$sql = "SELECT `eid`, `time` FROM `praise` WHERE `uid` = :uid";
		return DB::sql($sql, array(':uid' => $uid));
	}
};

?>