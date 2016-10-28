<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Request;
use Hash;

class User extends Model
{
    /*注册api*/
    public function signUp()
    {
    	/*验证用户名密码是否都不为空*/
    	$check = $this->has_username_and_password();
    	if(!$check) {
    		return ['status'=> 0, 'msg'=> 'username and password are required'];
    	}
    	$username = $check['username'];
    	$password = $check['password'];

    	/*验证用户名是否存在*/
    	$user_exist = $this
    			->where('username', $username)
    			->exists();
    	if($user_exist) {
    		return ['status'=> 0, 'msg'=> 'username has exist'];
    	}

    	/*保存进数据库*/
    	$this->username = $username;
    	$this->password = Hash::make($password);

    	$email = Request::get('email');
    	$mobile = Request::get('mobile');
    	$intro = Request::get('intro');
    	$avatar_url = Request::get('avatar_url');

    	if($email)
    		$this->email = $email;
    	if($mobile)
    		$this->mobile = $mobile;
    	if($intro)
    		$this->intro = $intro;
    	if($avatar_url)
    		$this->avatar_url = $avatar_url;

    	if(!$this->save())
    		return ['status'=> 0, 'msg'=> 'db insert failed'];
    	return ['status'=> 1, 'id'=> $this->id];
    }

    /*登陆api*/
    public function login()
    {
    	/*验证用户名密码是否都不为空*/
    	$check = $this->has_username_and_password();
    	if(!$check) {
    		return ['status'=> 0, 'msg'=> 'username and password are required'];
    	}
    	$username = $check['username'];
    	$password = $check['password'];

    	/*验证用户名是否存在*/
    	$user = $this
    			->where('username', $username)
    			->first();
    	if(!$user) {
    		return ['status'=> 0, 'msg'=> 'username does not exist'];
    	}

    	/*验证密码是否正确*/
    	if(!Hash::check($password, $user->password)) {
    		return ['status'=> 0, 'msg'=>'username or password is wrong'];
    	}

    	session()->put('username', $username);
    	session()->put('user_id', $user->id);
    	return ['status'=>1, 'id'=> $user->id];
    }

    /*退出api*/
    public function logout()
    {
    	session()->forget('username');
    	session()->forget('user_id');
    	return ['status'=>1];
    }

    /*检测用户名密码是否都不为空*/
    protected function has_username_and_password()
    {
    	$username = Request::get('username');
    	$password = Request::get('password');

    	if(!($username && $password)) {
    		return false;
    	}
    	return ['username'=>$username, 'password'=>$password];
    }

    /*检测是否有登陆*/
    public function is_logged_in()
    {
        return session('user_id')?:false;
    }
}
