<?php
namespace app\index\controller;
use think\Controller;
use app\index\model\JxnuStu as JxnuStu;

class Master extends Controller{
	public function getStu(){
		session(["expire"=>3600,"use_cookies"=>true]);
		if(!session("?user")){
			return json(array(
				"id"=>session_id(),
				"status"=>0,
				"data"=>"请检查是否登录"
			));
		}

		$stus=model("JxnuStu")->column(["id",'stu_no','stu_name','stu_iden','stu_sex','stu_birth','isrural']);
		$man=model("JxnuStu")->where("stu_sex","男")->count();
		$woman=model("JxnuStu")->where("stu_sex","女")->count();
		return Json([
			"status"=>1,
			"stu"=>$stus,
			"man"=>$man,
			"woman"=>$woman
		]);
	}
	public function getStuGrade(){
		session(["expire"=>3600,"use_cookies"=>true]);
		if(!session("?user")){
			return json(array(
				"id"=>session_id(),
				"status"=>0,
				"data"=>"请检查是否登录"
			));
		}

		$stu=request()->param("stu_no");
		$stu_pwd=request()->param("stu_iden");
		$url="http://jwc.jxnu.edu.cn/Default_Login.aspx?preurl=";
		$data=array(
		    "rblUserType" => "Student",
		    "ddlCollege" => "180     ",
		    "StuNum"=>$stu,
		    "Password"=>$stu_pwd,
		    "login"=>"登录",
		    "__VIEWSTATE"=>"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",
		    "__EVENTVALIDATION"=>"/wEWSgLwtPqZDgLr6+/kCQK3yfbSBAKDspbeCQL21fViApC695MMAsjQmpEOAsjQpo4OAv3S2u0DAv3S9t4DAqPW8tMDAqPW3ugDArWVmJEHAr/R2u0DAqrwhf4KAsjQtoIOAuHY1soHAsjQooMOAv3S3ugDArfW7mMC/dL+0AMCvJDK9wsC/dLy0wMCr9GugA4C8pHSiQwC6dGugA4C+dHq0QMChLCSig0C3NH61QMCjtCenA4CntDm2gMCxrDmjQ0CyNCqhQ4Co9b+0AMCvJDaiwwC3NHa7QMCv9Hi3wMC/dLu3AMC3NHm2gMCjtCyhw4CpbHqgA0CyNCugA4C/dLm2gMC3NHq0QMCjtCigw4C/dLi3wMCjtC+hA4CqvCJ9QoC3NHu3AMC3NHi3wMC6dGenA4C3NHy0wMCjtC6mQ4CjtCugA4C3NH+0AMCntDa7QMC/dL61QMCw5bP/gICv9He6AMC/dLq0QMC8pHaiwwCr9Gyhw4CyNC+hA4CyNCenA4C3NH23gMCr9GqhQ4C3NHe6AMCo9bm2gMCjtC2gg4C+euUqg4C2tqumwgC0sXgkQ8CuLeX+QECj8jxgApF/+i5q0uHVrBvAB7amIg23y7jCc1hDr4AP324ybMZtg=="
	    );
		$ch = curl_init($url);
		$cachepath=APP_PATH . 'runtime/cache/';
		$options = [
		     // 缓存类型为File
		    'type'   => 'File', 
		     // 缓存有效期为永久有效
		    'expire' => 0,
		     // 指定缓存目录
		    'path'   => $cachepath, 
		];
		cache($options);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
	    curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    $output = curl_exec($ch);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		// 根据头大小去获取头信息内容
		$header = substr($output, 0, $headerSize);
		preg_match_all("/set\-cookie:([^(\r\n|;)]*)/i", $header, $matches); 
		$cookie=$matches[1];
		if(sizeof($cookie)>=2){
			cache($stu, $cookie, 3600);
			$get = curl_init("http://jwc.jxnu.edu.cn/MyControl/All_Display.aspx?UserControl=xfz_bysh.ascx&Action=Personal");
			curl_setopt($get, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		    curl_setopt($get, CURLOPT_CONNECTTIMEOUT, 60); 
		    curl_setopt($get, CURLOPT_RETURNTRANSFER, 1);  
		    curl_setopt($get, CURLOPT_COOKIE, $cookie[0].";".$cookie[1]);
		    $output = curl_exec($get);
		    curl_close($ch);  
		    curl_close($get); 
		    preg_match_all("/加权平均标准分：([^<]*)/i",$output,$result);
		}
		else{
			$result=[0,[0]];
		}
	    return json($result[1]);


	}
}


?>