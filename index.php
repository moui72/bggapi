<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
libxml_use_internal_errors(true);
class cacheException extends Exception {

}
class CollectionItem
{
  var $gameId;
  var $name;
  var $image;
  var $thumbnail;
  var $minPlayers;
  var $maxPlayers;
  var $playingTime;
  var $isExpansion;
  var $yearPublished;
  var $averageRating;
  var $rank;
  var $numPlays;
  var $rating;
  var $owned;
  var $previousOwned;
  var $preOrdered;
  var $forTrade;
  var $want;
  var $wantToPlay;
  var $wantToBuy;
  var $wishList;

  // deep details
  var $description;
  var $artists;
  var $categories;
  var $designers;
  var $expansions;
  var $mechanics;
  var $publishers;

  public function __construct($xmldata)
  {
    $this->isExpansion = false;

    $this->gameId = $xmldata['objectid'] * 1;
    $this->name = $xmldata->name.'';
    $this->image = $xmldata->image.'';
    $this->thumbnail = $xmldata->thumbnail.'';
    $this->owned = $this->boolOf($xmldata->status['own']);
    $this->previousOwned = $this->boolOf($xmldata->status['prevown']);
    $this->forTrade = $this->boolOf($xmldata->status['fortrade']);
    $this->want = $this->boolOf($xmldata->status['want']);
    $this->wantToPlay = $this->boolOf($xmldata->status['wanttoplay']);
    $this->wantToBuy = $this->boolOf($xmldata->status['wanttobuy']);
    $this->wishList = $this->boolOf($xmldata->status['wishlist']);
    $this->preOrdered = $this->boolOf($xmldata->status['preordered']);
    $this->numPlays = $xmldata->numplays * 1;
    $this->yearPublished = $xmldata->yearpublished * 1;

    // stats
    if($xmldata->stats->rating['value'] == 'N/A'){
      $this->rating = -1;
    }else{
      $this->rating = $xmldata->stats->rating['value'] * 1;
    }
    $this->averageRating = $xmldata->stats->rating->bayesaverage['value'] * 1;
    $this->rank = $xmldata->stats->rating->ranks->rank[0]['value'] * 1;
    $this->minPlayers = $xmldata->stats['minplayers'] * 1;
    $this->maxPlayers = $xmldata->stats['maxplayers'] * 1;
    $this->playingTime = $xmldata->stats['playingtime'] * 1;
  }

  public function notGhost(){
    return $this->owned || $this->want || $this->wantToPlay || $this->wantToBuy
      || $this->wishList || $this->preOrdered || $this->previousOwned;
  }

  /*
  public function getDetails($id){

    if(!isset($id)){
      $id = $this->gameId;
    }

    $fileContents = file_get_contents("http://boardgamegeek.com/xmlapi2/thing?stats=1&id=".$id);
    $fileContents = trim(str_replace('"', "'", $fileContents));
    $xml = simplexml_load_string($fileContents);
    $item = $xml->item;

    if($xml->item['type'].'' == 'boardgame'){
      $this->isExpansion = false;
    }

    $this->description = $item->description.'';
    $this->minPlayers = $item->minplayers['value'] * 1;
    $this->maxPlayers = $item->maxplayers['value'] * 1;
    $this->playingTime = $item->playingtime['value'] * 1;
    $this->averageRating = $item->statistics->ratings->bayesaverage['value'] * 1;
    $this->rank = $item->statistics->ratings->ranks->rank[0]['value'] * 1;

    $this->designers = [];
    $this->publishers = [];
    $this->artists = [];
    $this->expansions = [];
    $this->mechanics = [];

    foreach($item->link as $index => $obj){
      if($obj['type'] == 'boardgamecategory'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';
        $tempObj->id = $obj['id'] * 1;
        $this->categories[] = $tempObj;
      }
      if($obj['type'] == 'boardgamemechanic'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->mechanics[] = $tempObj;
      }
      if($obj['type'] == 'boardgameexpansion'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->expansions[] = $tempObj;
      }
      if($obj['type'] == 'boardgamedesigner'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->designers[] = $tempObj;
      }
      if($obj['type'] == 'boardgameartist'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->artists[] = $tempObj;
      }
      if($obj['type'] == 'boardgamepublisher'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->publishers[] = $tempObj;
      }
    }
  }
  */

  public function isExp(){
    $this->isExpansion = true;
  }
  public function readDetails($item){

    if($item['type'].'' != 'boardgame'){
      $this->isExpansion = true;
    }

    $this->description = $item->description.'';

    $this->designers = [];
    $this->publishers = [];
    $this->artists = [];
    $this->expansions = [];
    $this->mechanics = [];

    foreach($item->link as $index => $obj){
      if($obj['type'] == 'boardgamecategory'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';
        $tempObj->id = $obj['id'] * 1;
        $this->categories[] = $tempObj;
      }
      if($obj['type'] == 'boardgamemechanic'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->mechanics[] = $tempObj;
      }
      if($obj['type'] == 'boardgameexpansion'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->expansions[] = $tempObj;
      }
      if($obj['type'] == 'boardgamedesigner'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->designers[] = $tempObj;
      }
      if($obj['type'] == 'boardgameartist'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->artists[] = $tempObj;
      }
      if($obj['type'] == 'boardgamepublisher'){
        $tempObj = new stdClass();
        $tempObj->name = $obj['value'].'';

        $tempObj->id = $obj['id'] * 1;
        $this->publishers[] = $tempObj;
      }
    }
  }

  private function boolOf($v){
    if($v.'' == '0'){
      return false;
    }
    return true;
  }

  function json(){
    return json_encode($this, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  }
};

$attempts = 0;
$maxattempts = 10;

try{
  if(isset($_GET['username']) && $_GET['username']){
    // fetch collection with stats
    while(true){
      $fileContents =
        file_get_contents("http://boardgamegeek.com/xmlapi2/collection?username=".
        urlencode($_GET['username'])."&stats=1");
      $fileContents = trim(str_replace('"', "'", $fileContents));
      $xml = simplexml_load_string($fileContents);
      if(count($xml->item) < 1){
        if(strpos($xml,'request for this collection has been accepted') !== false){
          throw new cacheException("Request queued");
        }elseif(isset($xml->error->message)){
          throw new Exception($xml->error->message.'');
        }else{
          throw new Exception("No data");
        }
      }else{
        break;
      }
    }
    $collection = [];
    $ids = [];
    $hash = [];
    // build collection
    foreach($xml->item as $index => $game){
      $cgame = new CollectionItem($game);
      if($cgame->notGhost()){
        $collection[] = $cgame;
        $ids[] = $game['objectid'] * 1;
        $hash[$game['objectid'] * 1] = count($collection) - 1;
      }
    }
    // set isExpansion values with second call
    $expOnly =
      file_get_contents("http://boardgamegeek.com/xmlapi2/collection?username=".
      urlencode($_GET['username'])."&subtype=boardgameexpansion");
    $expXML = simplexml_load_string($expOnly);
    foreach($expXML->item as $index => $exp){
      $game = $collection[$hash[$exp['objectid'] * 1]];
      $game->isExp();
    }

    /* get deep details
    if($_GET['getDetails'] == 1){
      $detailsFile = file_get_contents("http://boardgamegeek.com/xmlapi2/thing?id=".implode(',',$ids));
      $xmlDetails = simplexml_load_string($detailsFile);
      if(count($xmlDetails->item) < 1){
        if(strpos($xml,'request for this collection has been accepted') === false){
          $json =  "{\"message\": \"Error getting game details.\", \"status\": 500}";
          die();
        }else{
          $json = "{\"message\": \"".trim($xml)."\", \"status\": 202}";
          die();
        }
      }
      foreach($xmlDetails->item as $index => $game){
        $cgame = $collection[$hash[$game['id'] * 1]];
        $cgame->readDetails($game);
      }
    }
  */

    $json = json_encode($collection, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  }else{
    throw new Exception("Valid username is required");
  }
  print $json;
}catch(cacheException $m){
  print "{\n\t\"message\": \"".$m->getMessage().".\",\n\t\"status\": 202\n}";
}catch(Exception $e){
  print "{\n\t\"error\": {\n\t\t\"message\": \"".$e->getMessage().".\"\n\t\t}\n\t}";
}
?>
