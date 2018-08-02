<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\File as File;
use app\index\model\Login as Login;

class Article extends Controller{
	
	public function upload(){
    	session(["expire"=>3600,"use_cookies"=>true]);
	    $file = request()->file("article");
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file&&session("?user")){
	    	
	        $info = $file->rule('date')->move(ROOT_PATH . 'public' . DS ."uploads".DS.session("user"));
	        if($info){
	            // 成功上传后 获取上传信息
	            $result=array(
	            	"id"=>session("user"),
	            	'errno'=> 0,
	            	'data'=>array("/uploads/".session("user")."/" . str_replace("\\","/",$info->getSaveName()))
	            );
	            return json($result);
	        }else{
	            // 上传失败获取错误信息
	            return  json(array(
	            	"errno"=>1,
	            	"data"=>$file->getError()
	            ));
	        }
	    }
	    else{
	    	return  json(array(
	            	"errno"=>1,
	            	"data"=>"请检查是否登录"
	            ));
	    }
	}
	public function publish(){
		session(["expire"=>3600,"use_cookies"=>true]);
		if(!session("?user")){
			return json(array(
				"id"=>session_id(),
				"status"=>0,
				"data"=>"请检查是否登录"
			));
		}

		$title=request()->param('title');
		$tags= request()->post('tags[]/a');
		$types=implode(",",request()->post('types/a'));
		$content=request()->param('content');
		$article=model("Article");
		preg_match_all("/<img[^>]*src=\"[^>]*>/",$content,$array);
		$array0=array_map(function($str){
			preg_match_all("/(?:\")(.*)(?:\"\s.*>)/",$str,$arr);
			return $arr[1][0];
		},$array[0]);
		foreach ($array0 as $value) {
			$path=ROOT_PATH . 'public' . DS .$value;
			$name=substr($value,strripos($value,"/")+1);
			$to=ROOT_PATH . 'public' . DS .'article';
			if(!is_dir($to)){
				mkdir ($to,0777,true);
			}
			if(is_file($path)){
				copy($path,$to.DS.$name);
			}
			$content=str_replace($value,"/article/".$name,$content);
		};
		$article->data([
			"title"=>$title,
			"tags"=>$tags,
			"types"=>$types,
			"content"=>base64_encode($content),
			"user_id"=>model("Login")->where("user",session("user"))->find()->id,
			"data"=>date("Y-m-d")
		]);
		
		$article->save();
		return json([
			"id"=>$article->article_id,
			"other"=>$tags
		]);
	}
	public function getArticle(){
		$article=model("Article")->where('article_id', request()->param('id'))->find();
		$article->content=base64_decode($article->content);
		return $article->toJson();
	}
}

?>