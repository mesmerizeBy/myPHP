<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\File as File;
use app\index\model\Login as Login;
use think\Db;

class Article extends Controller{
	private function count($types, $set){
		foreach ($types as $key ) {
			if(array_key_exists($key,$set)){
				$set[$key]++;
			}
			else{
				$set[$key]=1;
			}
		}
		return $set;
	}
	
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
		$tags= implode(",",request()->post('tags/a'));
		$types=implode(",",request()->post('types/a'));
		$content=request()->param('content');
		$isPublic=request()->param('isPublic')=="true"?1:0;
		$summary=request()->param('summary');
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
			"date"=>date("Y-m-d H:i:s"),
			"isPublic"=>$isPublic,
			"summary"=>$summary
		]);
		
		$article->save();
		return json([
			"id"=>$article->article_id,
			"other"=>$isPublic
		]);
	}
	public function getArticle(){
		session(["expire"=>3600,"use_cookies"=>true]);
		$article=model("Article")->where('article_id', request()->param('id'))->find();
		if($article==null){
			return json(array(
				"status"=>0,
				"data"=>"别做坏事",
				"content"=>"空空如也"
			));
		}
		if(!session("?user")&&!$article->isPublic){
			return json(array(
				"status"=>0,
				"data"=>"请检查是否登录",
				"content"=>"不许偷看"
			));
		}
		$article->content=base64_decode($article->content);
		$article->tags=explode(",",$article->tags);
		$article->types=explode(",",$article->types);
		return $article->toJson();
	}
	public function getArticleList(){
		session(["expire"=>3600,"use_cookies"=>true]);
		$count=[];
		if(session("?user")){
			$article=model("Article")->select();

			foreach($article as $user){
			    $user->content=base64_decode($user->content);
				$user->tags=explode(",",$user->tags);
				$user->types=explode(",",$user->types);

				$count=$this->count($user->types,$count);
			}
			$date=Db::query("select count(*) as count,DATE_FORMAT(date, '%Y-%m-%d') as date from Article group by DATE_FORMAT(date, '%Y-%m-%d')");
			
			return json([
				"count"=>$count,
				"list"=>$article,
				"date"=>$date
			]);  
		}
		else{
			$article=model("Article")->all(["isPublic"=>1]);
			foreach($article as $user){
			    $user->content=base64_decode($user->content);
				$user->tags=explode(",",$user->tags);
				$user->types=explode(",",$user->types);

				$count=$this->count($user->types,$count);
			}
			
			$date=Db::query("select count(*) as count,DATE_FORMAT(date, '%Y-%m-%d') as date from Article where isPublic = 1 group by DATE_FORMAT(date, '%Y-%m-%d')");
			
			return json([
				"count"=>$count,
				"list"=>$article,
				"date"=>$date
			]);  
		}
	}
	public function removeArticle(){
		session(["expire"=>3600,"use_cookies"=>true]);
		if(!session("?user")){
			return json(array(
				"id"=>session_id(),
				"status"=>0,
				"data"=>"请检查是否登录"
			));
		}

		$article_id=request()->param('article_id');
		$article=model("Article")->get($article_id);
		if($article->delete()>0){
			return json([
				"status"=>1,
				"data"=>"删除成功"
			]);
		}else{
			return json([
				"status"=>0,
				"data"=>"删除失败"
			]);
		}

	}

	
	
}

?>