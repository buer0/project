<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function add()
    {
    	if(!user_ins()->is_logged_in())
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('question_id') || !rq('content'))
    		return ['status'=> 0, 'msg'=> 'question_id and content are required'];

    	$question = question_ins()->find(rq('question_id'));

    	if(!$question)
    		return ['status'=> 0, 'msg'=> 'question not exist'];

    	$answer = $this->where([
    		'question_id'=>rq('question_id'),
    		'user_id'=>session('user_id')
    		])->count();
    	if($answer)
    		return ['status'=> 0, 'msg'=> 'ddeplicate answers'];

    	$this->content = rq('content');
    	$this->question_id = rq('question_id');
    	$this->question_id = rq('question_id');
    	$this->user_id = session('user_id');

    	return $this->save() ?
    		['status'=> 1, 'id'=> $this->id] :
    		['status'=> 0, 'msg'=> 'db insert failed'];
    }

    public function change()
    {
    	if(!user_ins()->is_logged_in())
    		return ['status'=> 0, 'msg'=> 'login required'];

    	if(!rq('id') || !rq('content'))
    		return ['status'=> 0, 'msg'=> 'id and content are required'];

    	$answer = $this->find(rq('id'));

    	if(!$answer)
    		return ['status'=> 0, 'msg'=> 'answer not exist'];

    	if($answer->user_id != session('user_id'))
    		return ['status'=> 0, 'msg'=> 'permission denied'];
    	$answer->content = rq('content');

    	return $answer->save() ?
    		['status'=> 1] :
    		['status'=> 0, 'msg'=> 'db update failed'];
    }

    public function read()
    {
    	if(!rq('id') && !rq('question_id'))
    		return ['status'=> 0, 'msg' => 'id or question_id is required'];

    	if(rq('id')) {
    		$answer = $this->find(rq('id'));
    		if(!$answer)
    			return ['status'=> 0, 'msg' => 'answer not exist'];
    		return ['status'=> 1, 'data' => $answer];
    	}

    	if(!question_ins()->find(rq('question_id')))
    		return ['status'=> 0, 'msg' => 'question not exist'];

    	$answers = $this->where('question_id', rq('question_id'))->get()->keyBy('id');
    	return ['status'=> 1, 'data' => $answers];
    }
}
