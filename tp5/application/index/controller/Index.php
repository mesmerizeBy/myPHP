<?php
namespace app\index\controller;
use think\Controller;
use think\Request;


class Index extends Controller
{
	private function deldir($directory){//自定义函数递归的函数整个目录
	  if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
	    if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功
	      while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹
	        if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
	          $subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
	          if(is_dir($subFile)){//如果是目录条件则成了
	            $this->deldir($subFile);//递归调用自己删除子目录
	          }
	          if(is_file($subFile)){//如果是文件条件则成立
	            unlink($subFile);//直接删除这个文件
	          }
	        }
	      }
	      closedir($dir_handle);//关闭目录资源
	      rmdir($directory);//删除空目录
	    }
	  }
	}
	
    public function index()
    {
    	return  $this->fetch();
        
    }
    

	public function login(){
		session(["expire"=>3600,"use_cookies"=>true]);

		if(session("?user")){
			return json(array(
				"id"=>session_id(),
				"status"=>1,
				"data"=>"已登录"
			));
		}

		$user=model("Login")->where('user', request()->param('user'))->find();
		if(empty($user)){
			$data= json(array(
				'status'=>0,
				'data'=>"用户名不存在"
			));
		}
		else if($user->password==request()->param('pwd')){
			$data= json(array(
				'status'=>1,
				'data'=>"登录成功"
			));
			session("user",$user->user);
			$dir=ROOT_PATH . 'public' . DS ."uploads".DS.session("user");
			if(is_dir($dir)){
				$this->deldir($dir);
			}
		}
		else
		$data= json(array(
				'status'=>2,
				'data'=>"密码错误"
			));


		return $data;
	}
	

}
