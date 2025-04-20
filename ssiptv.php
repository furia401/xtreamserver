<?php



header("Content-Type: text/plain");
$username = $_GET['username'];
$password = $_GET['password'];
if(isset($_GET['output'])){
    $type = "live/";
    $output = "&output=".$_GET['output'];
}else{
    $type = "";
    $output = "";
}

echo "#EXTM3U\n";
// Verifica se a category está presente na solicitação GET
if(isset($_GET['category'])) {
    if(isset($_GET['category']) && $_GET['category'] == "live") {

        $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_live_categories');
        $data = json_decode($jsonData, true);
        //echo $data;
        foreach($data as $live) {
            echo "#EXTINF:-1 type='playlist' tvg-logo='' size=\"Medium\", " . $live['category_name'] . " \n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&live=" . $live['category_id'] . "$output\n";
        }
    }

    if(isset($_GET['category']) && $_GET['category'] == "vod") {

        $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_vod_categories');
        $data = json_decode($jsonData, true);
        foreach($data as $vod) {
            echo "#EXTINF:-1 type='playlist' tvg-logo='', " . $vod['category_name'] . " \n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&vod=" . $vod['category_id'] . "\n";
        }
    }
    if(isset($_GET['category']) && $_GET['category'] == "series") {

        $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_series_categories');
        $data = json_decode($jsonData, true);
        foreach($data as $series) {
            echo "#EXTINF:-1 type='playlist' tvg-logo='' size=\"Big\", " . $series['category_name'] . " \n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&series=" . $series['category_id'] . "\n";
        }
    }
}





if(isset($_GET['live'])) {
    $category = $_GET['live'];
    if(isset($_GET['output'])){
    $type = "live/";
    $output = ".".$_GET['output'];
}else{
    $type = "";
    $output = "";
}

    $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_live_streams');
    $data = json_decode($jsonData, true);
    foreach($data as $live) {
        if($live['category_id'] == $category) {
            echo "#EXTINF:-1 tvg-logo=\"" . $live['stream_icon'] . "\" size=\"Medium\"," . $live['name'] . "\n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/".$type."" . $username . "/" . $password . "/" . $live['stream_id'] . "$output\n";
        }
    }
}

if(isset($_GET['vod'])) {
    $category = $_GET['vod'];

    $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_vod_streams');
    $data = json_decode($jsonData, true);

    foreach($data as $vod) {
        if($vod['category_id'] == $category) {
            echo "#EXTINF:-1 tvg-logo=\"" . $vod['stream_icon'] . "\" size=\"Medium\"," . $vod['name'] . "\n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/movie/" . $username . "/" . $password . "/" . $vod['stream_id'] . ".".$vod["container_extension"]."\n";
        }
    }
}

if(isset($_GET['series'])) {

    $category = $_GET['series'];

    $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_series');
    $data = json_decode($jsonData, true);

    foreach($data as $series) {
        if($series['category_id'] == $category) {
            echo "#EXTINF:-1 type=\"playlist\" tvg-logo=\"" . $series['cover'] . "\" size=\"Medium\"," . $series['name'] . "\n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&series_id=" . $series['series_id'] . "\n";
        }
    }
}

if(isset($_GET['series_id'])) {
    $series_id = $_GET['series_id'];

    $jsonData = file_get_contents('http://'. $_SERVER['HTTP_HOST'] .'/player_api.php?username='.$username.'&password='.$password.'&action=get_series_info&series_id='.$series_id.'');
    $data = json_decode($jsonData, true);

    foreach($data['seasons'] as $seasons) {
        $season_number = $seasons['season_number'];
        $name = $seasons['name'];
        foreach($data['episodes'][$season_number] as $episodes) {
            $episode_num = $episodes['episode_num'];
            $title = $episodes['title'];
            $id = $episodes['id'];
            

            echo "#EXTINF:-1 tvg-logo=\"".$episodes['info']['movie_image']."\" size=\"Medium\" group-title='".$name."', ".$title."\n";
            echo "#EXTBG:\n";
            echo "http://" . $_SERVER['HTTP_HOST'] . "/series/" . $username . "/" . $password . "/".$id."." . $episodes['container_extension'] . "\n";
        }
    }
}




if(isset($_GET['ssiptv'])) {
    if(isset($_GET['output'])) {
        $output = "&output=". $_GET['output'];
    }else{
        $output = "";
    }
echo "#EXTINF:-1 type=\"playlist\" tvg-logo=\"https://furia.x10.bz/Img/transparente.png\" size=\"Big\", \n";
echo "#EXTBG:http://" . $_SERVER['HTTP_HOST'] . "/img/live.png\n";
echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $_GET['password'] . "&category=live$output\n";
echo "#EXTINF:-1 type=\"playlist\" tvg-logo=\"https://furia.x10.bz/Img/transparente.png\" size=\"Big\", \n";
echo "#EXTBG:http://" . $_SERVER['HTTP_HOST'] . "/img/vod.png\n";
echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&category=vod\n";
echo "#EXTINF:-1 type=\"playlist\" tvg-logo=\"https://furia.x10.bz/Img/transparente.png\" size=\"Big\", \n";
echo "#EXTBG:http://" . $_SERVER['HTTP_HOST'] . "/img/series.png\n";
echo "http://" . $_SERVER['HTTP_HOST'] . "/ssiptv.php?username=" . $username . "&password=" . $password . "&category=series\n";
}
?>