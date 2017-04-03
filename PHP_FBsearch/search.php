<!-- submit3.8-->
<!-- author: lei jin 2/2017 -->
<!-- required to put all the files in one php file  -->
<?php error_reporting(E_ALL || ~E_NOTICE);?>
<!DOCTYPE html>

<html>
<head>
	<title>Facebook Search</title>
	<meta charset="utf-8">
	<script language="javascript">
	function clears(){
			// document.getElementById("fill1").innerHTML="";
			// document.getElementById("fill2").innerHTML="</br>";
			// document.getElementById("plain").innerHTML="";
			// document.getElementById("keyword").innerHTML="";
			location.replace("http://cs-server.usc.edu:27177/search.php")   


	}
	function changelable(){
		// document.write("d")
		var choice=document.getElementById("type").value;
		// alert(choice);
		if (choice=="places"){
			document.getElementById("fill1").innerHTML="Location:";
			document.getElementById("fill2").innerHTML="<input type=text name=\"location\" id=\"location\" value=\"<?php if(isset($_GET['location'])) echo $_GET['location']; ?>\" required> </input><label for=\"distance\" >Distance(meters): </label><input type=text name=\"distance\" id=\"distance\" value=\"<?php if(isset($_GET['distance'])) echo $_GET['distance']; ?>\" required> </input>"
	}
		else{
			document.getElementById("fill1").innerHTML="";
			document.getElementById("fill2").innerHTML="</br>"

		}
}
	function forplaces(){
			if (document.getElementById("type").value=="places"){
			document.getElementById("fill1").innerHTML="Location:";
			document.getElementById("fill2").innerHTML="<input type=text name=\"location\" id=\"location\" value=\"<?php if(isset($_GET['location'])) echo $_GET['location']; ?>\" required> </input><label for=\"distance\" >Distance(meters): </label><input type=text name=\"distance\" id=\"distance\" value=\"<?php if(isset($_GET['distance'])) echo $_GET['distance']; ?>\" required> </input>"
		}
	}


	</script>
</head>
<body>


<div width=900>
<div style="border-style:solid;border-width: 1px; width:800px;padding:5px;height:200px;margin-left:auto;margin-right:auto;" >
	<div style="width:500px;margin-left:auto;margin-right:auto;text-align:center;font-size:30px" >Facebook Search</div>
<form METHOD="GET" action="" style="backgroundColor:red" >
	<div style="float:left;width:100px;position:relative" id="lables">
		<p><lable for="Keyword" >Keyword:</lable></p>
		<p><label for="type" >Type: </label></p>
		<p id="fill1"></p>
	</div>
	<div style="float:left;width:600px;position:relative" id="lables">		
		<p><input type=text name="keyword" id="keyword"  value="<?php if(isset($_GET["keyword"])) echo $_GET["keyword"]; ?>" required oninvalid="this.setCustomValidity('This can\'t be left empty')"
    oninput="setCustomValidity('')"> </input>
		<p><select name="type" id="type"  onchange="changelable()">
			<option value="users" <?php if(isset($_GET["type"])&&$_GET["type"]=="users") echo "selected";?>>Users</option><option value="pages"<?php if(isset($_GET["type"])&&$_GET["type"]=="pages") echo "selected";?>>Pages</option> 
			<option value="events" <?php if(isset($_GET["type"])&&$_GET["type"]=="events") echo "selected";?>>Events</option><option value="places" <?php if(isset($_GET["type"])&&$_GET["type"]=="places") echo "selected";?>>Places</option><option value="groups" <?php if(isset($_GET["type"])&&$_GET["type"]=="groups") echo "selected";?>>Groups</option>
		</select></p>
		<p id="fill2"><br/> </p>
		<p><input type=submit name="submit" value="search" ></input>
		<input type=reset name="reset" value="clear" onclick="clears()"></p>
	</div>
<script language="javascript"> forplaces()</script>
</form>




</div>
<p> </p>
<p> </p>
<p> </p>
<?php
require_once __DIR__ . '/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
	if(isset($_GET["submit"]))
	{

		echo "<div id=\"plain\" style='width:800px;margin-left:auto;margin-right:auto;' >";
		$keyword=$_GET["keyword"];
		$type=$_GET["type"];
		$location="";
		$distance="";
		$fields='id,name,picture.width(700).height(700),albums,posts';
		if (isset($_GET["location"])) {
			$location=$_GET["location"];
			$distance=$_GET["distance"];
		}

		$googleAPI="AIzaSyCm9AMkzgUPh7kfxRIk5KQooZKN-b3lcBY";
		$accessToken="your fb token";
		$fb = new Facebook\Facebook([
  		'app_id' => '1841405776135087',
  		'app_secret' => 'app_secret',
  		'default_graph_version' => 'v2.8',
	]);

		$fb->setDefaultAccessToken($accessToken);
		switch ($type) {
			case 'users':
				$request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'user','fields'=>$fields]);
				break;
			case 'pages':
				$request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'page','fields'=>$fields]);
				break;
			case 'events':
				$request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'event','fields'=>$fields.',place']);
				break;
			case 'groups':
				$request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'group','fields'=>$fields]);
				break;
			case 'places':
				// $request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'event','fields'=>$fields]);
				$content=file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$location."&key=AIzaSyCm9AMkzgUPh7kfxRIk5KQooZKN-b3lcBY");
				$content=json_decode($content,true);
				$lat=$content["results"][0]["geometry"]["location"]["lat"];
				$lng=$content["results"][0]["geometry"]["location"]["lng"];
				// echo $lat.$lng;
				//***TODO: consider not exists
				$request = $fb->request('GET','/search',['q'=>$keyword,'type'=>'place','center'=>$lat.','.$lng,'distance'=>$distance,'fields'=>$fields]);
				break;
		}

  		$response=$fb->getClient()->sendRequest($request);
  		$res=$response->getDecodedBody();
  		$data=$res["data"];
  		function display_user($data,$type,$keyword){
  			$id=array();
  			$name=array();
  			$profile_photo=array();
  			$detail=array();
  			$i=0;
  			foreach ($data as $user) {
  				$id[$i]=$user["id"];
  				$name[$i]=$user["name"];
  				$profile_photo[$i]=$user["picture"]["data"]["url"];	
  				$i++;	
  			}
  			$htmls='<table border=1><tr><td style="width:250px">Profile Photo</td><td style="width:350px">Name</td>
  			<td style="width:200px">Details</td></tr>';			
  			for ($i=0;$i<count($id);$i++)
  				$htmls=$htmls.'<tr><td><a href="'.$profile_photo[$i].'" target=_blank><img style="width:40px;height:30px;" src="'.$profile_photo[$i].'"></a></td><td>'.$name[$i].'</td>
  			<td><a href="search.php?keyword='.$keyword.'&type='.$type.'&id='.$id[$i].'  "">Details</a></td></tr>';
  			$htmls=$htmls.'</table>';
  			echo $htmls;
  		}
  		function display_place($data,$type,$keyword,$location,$distance){
  			$id=array();
  			$name=array();
  			$profile_photo=array();
  			$detail=array();
  			$i=0;
  			foreach ($data as $user) {
  				$id[$i]=$user["id"];
  				$name[$i]=$user["name"];
  				$profile_photo[$i]=$user["picture"]["data"]["url"];	
  				$i++;	
  			}
  			$htmls='<table border=1><tr><td style="width:250px">Profile Photo</td><td style="width:350px">Name</td>
  			<td style="width:200px">Details</td></tr>';			
  			for ($i=0;$i<count($id);$i++)
  				$htmls=$htmls.'<tr><td><a href="'.$profile_photo[$i].'" target=_blank><img style="width:40px;height:30px;" src="'.$profile_photo[$i].'"></a></td><td>'.$name[$i].'</td>
  			<td><a href="search.php?distance='.$distance.'&location='.$location.'&keyword='.$keyword.'&type='.$type.'&id='.$id[$i].'  "">Details</a></td></tr>';
  			$htmls=$htmls.'</table>';
  			echo $htmls;
  		}
  		function display_event($data){
  			$id=array();
  			$name=array();
  			$profile_photo=array();
  			$place=array();
  			$i=0;
  			foreach ($data as $user) {
  				$id[$i]=$user["id"];
  				$name[$i]=$user["name"];
  				if (array_key_exists('place', $user))
  				$place[$i]=$user["place"]["name"];
  				$profile_photo[$i]=$user	["picture"]["data"]["url"];
  				$i++;	
  			}
  			$htmls='<table border=1><tr><td style="width:250px">Profile Photo</td><td style="width:350px">Name</td>
  			<td style="width:200px">Place</td></tr>';			
  			for ($i=0;$i<count($id);$i++)
  				$htmls=$htmls.'<tr><td><a href="'.$profile_photo[$i].'" target=_blank><img style="width:40px;height:30px;" src="'.$profile_photo[$i].'"></a></td><td>'.$name[$i].'</td><td>'.$place[$i].'</td></tr>';
  			$htmls=$htmls.'</table>';
  			echo $htmls;
  		}
  		if(count($res["data"])==0)
  			echo "<table border=1 width=800><th>No record has been found</th></div>" ;
  		elseif ($type=='events')
  			display_event($data);
  		elseif ($type=='places')
  			display_place($data,$type,$keyword,$location,$distance);
  		else
  			display_user($data,$type,$keyword);
  		echo "</div> ";
 	 	// echo var_dump($res);
 	 	// echo $res["data"][0]["id"];//访问方式
 	 	// print_r(json_encode($res)); 
	}
// function addAlbums(){
// 	document.getElementById("t1").innerHTML="s";

// }
	else 
		if(isset($_GET["id"])){
			echo "<div id=\"plain\" style='width:800px;margin-left:auto;margin-right:auto;'>";
			$id=$_GET["id"];
			$fb = new Facebook\Facebook([
  			'app_id' => '1841405776135087',
  			'app_secret' => 'app_secret',
  			'default_graph_version' => 'v2.8',
			]);
			$fb->setDefaultAccessToken("your fb token");
			$request = $fb->request('GET',$id,['fields'=>'id,name,albums.limit(5){name,photos.limit(2){name, picture.width(700).height(700)}},posts.limit(5)']);
 			$response=$fb->getClient()->sendRequest($request);
  			$res=$response->getDecodedBody();  			
  			// print_r($res);
  			if (!array_key_exists('albums', $res)){
  				echo '<table border=1px style="width:800px" id="table1"><th>No albums has been found</th></table><p></p><p></p><p></p>';
  			}
  			else{
				$albumsName=[];
				$albumsURL1=[];
				$albumsURL2=[];
				$albumsid=[];
				$i=0;
					echo '<script language="javascript">';
					echo "var aName=new Array();var aUrl=new Array();var aid=new Array();" ;	
					echo "</script>";
				foreach ($res["albums"]["data"] as $albumsArray) {
					$albumsName[$i]=filter_var($albumsArray["name"], FILTER_SANITIZE_SPECIAL_CHARS);
					echo '<script language="javascript">';
					echo "aName[$i]=\"$albumsName[$i]\";aUrl[$i]=new Array();aid[$i]=new Array();" ;	
					echo "</script>";
					for ($j=0;$j<count($albumsArray["photos"]["data"]);$j++){
						$albumsURL1[$j]=$albumsArray["photos"]["data"][$j]["picture"];
						$albumsid[$j]=$albumsArray["photos"]["data"][$j]["id"];
						echo '<script language="javascript"> ';
						echo "aUrl[$i][$j]=\"$albumsURL1[$j]\";" ;	
						echo "aid[$i][$j]=\"$albumsid[$j]\";" ;	
						echo "</script>";
					}
					$i++;
				};
				echo "<script language='javascript'> 
						function addAlbums(){	

						for(var i=0;i<aName.length;i++){
							var index='t'+(i+1);
							var index2='m'+(i+1)
						if (document.getElementById(index).innerHTML==''){
							document.getElementById('tb1').border='1px';
							document.getElementById(index).innerHTML='<a href=# onclick=\"addPics('+i+')\">'+aName[i]+'</a>';
						}
						else{
							document.getElementById('tb1').border='0px';
							document.getElementById(index).innerHTML='';
							document.getElementById(index2).innerHTML='';
							
						}

					}
				}

					function addPics(var1){			
								var str=''
								var index1='m'+(var1+1);
								for(var j=0;j<aUrl[var1].length;j++){	
									str+='<a href=\"https://graph.facebook.com/v2.8/'+aid[var1][j]+'/picture?access_token=your fb token\" target=_blank><img width=130 height=130 src='+aUrl[var1][j]+'></img><a> '
									
							}
									if (document.getElementById(index1).innerHTML=='')
										document.getElementById(index1).innerHTML=str;
									else
										document.getElementById(index1).innerHTML='';
						}
				";
				echo "</script>";
				echo '<br/><table border=1px style="width:800px" class="table1"><th><a href=# onclick="addAlbums()">Albums</a></th></table><br/>';
				echo '<table border=0 style="width:800px" class="table1" id ="tb1"><td id="t1" border=0></td></tr><tr id="m1"></tr> <tr><td id="t2" border=0></td></tr><tr id="m2"><tr><td id="t3" border=0></td></tr><tr id="m3"></tr> <tr ><td id="t4" border=0></td></tr><tr id="m4"></tr><tr ><td id="t5" border=0></td></tr><tr id="m5"></tr></table>';
  				echo '<br/><br/>';
  				}



  			if (!array_key_exists('posts', $res)){
  				echo '<table border=1px style="width:800px" id="table1"><th>No posts has been found </th></table>';
  			}
  			else{

				$mess=[];
				$i=0;
					echo '<script language="javascript">';
					echo "var messages=new Array()" ;	
					echo "</script>";
				$i=0;
				foreach ($res["posts"]["data"] as $postsArray) {
					// echo "$i";
					if (array_key_exists('message', $postsArray)){
						$mess[$i]=filter_var($postsArray["message"], FILTER_SANITIZE_SPECIAL_CHARS);
					// echo "$mess[$i]<br/><br/>";
					echo '<script language="javascript">';
					echo "messages[$i]=\"$mess[$i]\";"; 	
					echo "</script>";
					$i++;}					
				};

				// print_r(json_encode($res));
				echo "<script language='javascript'> 
					function addPosts(){	
						document.getElementById('tb2').border='1px'
						for(var i=0;i<messages.length;i++){
							var index='p'+(i+1);
						if (document.getElementById(index).innerHTML==''){
							document.getElementById(index).innerHTML=messages[i];
							document.getElementById('info2').innerHTML='<strong>message</strong>';}

						else{
							document.getElementById('info2').innerHTML=''
							document.getElementById('tb2').border='0px'
							document.getElementById(index).innerHTML='';
						}

					}
				}

				";
				echo "</script>";
				// print_r($res);
			echo '<table border=1px style="width:800px" class="table2"><th><a href=# onclick="addPosts()">Posts</a></th> </table><br/>';
			echo '<table border=0px style="width:800px" class="table2" id="tb2"><tr id="info2"><tr/><tr><td id="p1"></td></tr>
			<tr ><td id="p2"></td></tr><tr><td id="p3"></td></tr><tr><td id="p4"></td></tr><tr><td id="p5"></td></tr></table>';
  			echo "</div> ";
  			}
	}



?>
</div>


</body>
</html>