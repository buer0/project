<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /*新增加一个问题*/
    public function add()
    {
    	/*检测用户是否登陆*/
    	$user = user_ins()->is_logged_in();
    	if(!$user)
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('title')) {
    		return ['status'=> 0, 'msg'=> 'title is required'];
    	}
    	$this->title = rq('title');
    	$this->user_id = session('user_id');

    	if(rq('desc')) {
    		$this->desc = rq('desc');
    	}

    	return $this->save() ?
    		['status'=> 1, 'id'=> $this->id] :
    		['status'=> 0, 'msg'=> 'db insert failed'];
    }

    public function change()
    {
    	$user = user_ins()->is_logged_in();
    	if(!$user)
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('id'))
    		return ['status'=> 0, 'msg'=> 'id is required'];

    	$question = $this->find(rq('id'));

    	if(!$question)
    		return ['status'=> 0, 'msg'=> 'question not exist'];

    	if($question->user_id != session('user_id'))
    		return ['status'=> 0, 'msg'=> 'permission denied'];

    	if(rq('title'))
    		$question->title = rq('title');

    	if(rq('desc'))
    		$question->desc = rq('desc');

    	return $question->save() ?
    		['status'=> 1] :
    		['status'=> 0, 'msg'=> 'db update failed'];
    }

    public function read()
    {
    	if(rq('id'))
    		return ['status'=> 1, 'data' => $this->find(rq('id'))];

    	$limit = rq('limit') ?: 15;
    	$page = rq('page') ?: 1;
    	$skip =  $limit * ($page - 1);

    	$res = $this->orderBy('created_at')
    		->limit($limit)
    		->skip($skip)
    		->get(['id', 'title', 'desc', 'user_id', 'created_at', 'updated_at'])
    		->keyBy('id');

    	return ['status'=> 1, 'data' => $res];
    }

    public function remove()
    {
    	if(!user_ins()->is_logged_in())
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('id'))
    		return ['status'=> 0, 'msg'=> 'id is required'];

    	$question = $this->find(rq('id'));

    	if(!$question)
    		return ['status'=> 0, 'msg'=> 'question not exist'];

    	if($question->user_id != session('user_id'))
    		return ['status'=> 0, 'msg'=> 'permission denied'];

    	/*
    	*删除问题下所有的回答和评论
    	*/

    	return $question->delete() ?
    		['status'=> 1] :
    		['status'=> 0, 'msg'=> 'db delete failed'];
    }
}
