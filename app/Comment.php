<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function add()
    {
    	if(!user_ins()->is_logged_in())
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('content'))
    		return ['status'=> 0, 'msg' => 'content is required'];

    	if(
    		(!rq('answer_id') && !rq('reply_to')) ||
    		(rq('answer_id') && rq('reply_to'))
    		)
    		return ['status'=> 0, 'msg' => 'reply_to or answer_id is required'];

    	if(rq('answer_id'))
    	{
    		$answer = answer_ins()->find(rq('answer_id'));
    		if(!$answer)
    			return ['status'=> 0, 'msg' => 'answer not exist'];
    		$this->answer_id = rq('answer_id');
    	} else
    	{
    		$comment = $this->find(rq('reply_to'));
    		if(!$comment)
    			return ['status'=> 0, 'msg' => 'comment not exist'];
    		if($comment->user_id == session('user_id'))
    			return ['status'=> 0, 'msg' => 'can not reply to yourself'];
    		$this->reply_to = rq('reply_to');
    	}

    	$this->content = rq('content');
    	$this->user_id = session('user_id');

    	return $this->save() ?
    		['status'=> 1, 'id'=>$this->id] :
    		['status'=> 0, 'msg'=> 'db insert failed'];
    }

    public function read()
    {
    	if(
    		(!rq('answer_id') && !rq('reply_to')) ||
    		(rq('answer_id') && rq('reply_to'))
    		)
    		return ['status'=> 0, 'msg' => 'reply_to or answer_id is required'];

    	if(rq('answer_id'))
    	{
    		$answer = answer_ins()->find(rq('answer_id'));
    		if(!$answer)
    			return ['status'=> 0, 'msg' => 'answer not exist'];
    		$data = $this->where('answer_id', rq('answer_id'))->get();
    	} else
    	{
    		$comment = $this->find(rq('reply_to'));
    		if(!$comment)
    			return ['status'=> 0, 'msg' => 'comment not exist'];
    		$data = $this->where('reply_to', rq('reply_to'))->get();
    	}

    	return ['status'=> 1, 'data'=>$data->keyBy('id')];
    }

    public function remove()
    {
    	if(!user_ins()->is_logged_in())
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('id'))
    		return ['status'=> 0, 'msg'=> 'id is required'];

    	$comment = $this->find(rq('id'));

    	if(!$comment)
    		return ['status'=> 0, 'msg'=> 'comment not exist'];

    	if($comment->user_id != session('user_id'))
    		return ['status'=> 0, 'msg'=> 'permission denied'];

    	$this->where('reply_to', rq('id'))->delete();
    	return $comment->delete() ?
    		['status'=> 1] :
    		['status'=> 0, 'msg'=> 'db delete failed'];
    }
}
