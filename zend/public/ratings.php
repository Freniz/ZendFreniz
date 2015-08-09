<?php


session_start();

    $rating = new ratings($_REQUEST['widget_id']);
    
    
    isset($_REQUEST['fetch']) ? $rating->get_ratings() : $rating->vote();
    
    
    



class ratings {
    
    private $ratings;
    private $pageid;
    private $data = array();
    
    
function __construct($pid) {
    $this->pageid=$pid;
    mysql_connect('localhost','nizam','ajith786') or die("cannot connected");
    mysql_select_db("fztest1") or die ("coudnt find database");    
    $result=mysql_query("select ratings from pages_info where pageid='$pid'");
    while($row=mysql_fetch_assoc($result)){
        $this->ratings=unserialize($row['ratings']);
        if(!isset ($this->ratings))
            $this->ratings=array();
    }
    mysql_close();
}
public function get_ratings() {
        $data['widget_id'] = $this->pageid;
        $data['number_votes'] = count($this->ratings); 
        $data['total_points'] = array_sum($this->ratings);
        if($data['number_votes']!=0 && isset ($data['total_points']))
        $data['dec_avg'] =round($data['total_points']/$data['number_votes'],1);
        else
            $data['dec_avg']=0;
        $data['whole_avg'] = round($data['dec_avg']);
        echo json_encode($data); 
}
public function vote() {
    if(!array_key_exists($_SESSION['userid'], $this->ratings)){
    # Get the value of the vote
    preg_match('/star_([1-5]{1})/', $_REQUEST['clicked_on'], $match);
    $vote = $match[1];
    $this->ratings[$_SESSION['userid']]=$vote;
    
    mysql_connect('localhost','nizam','ajith786') or die("cannot connected");
    mysql_select_db("fztest1") or die ("coudnt find database");    
    mysql_query("update pages_info set ratings='".  serialize($this->ratings)."' where pageid='$this->pageid'");
    mysql_close();
    }
    $this->get_ratings();

}
# ---
# end class
}














//function return_rating($raw_id) {
//    
//    $widget_data = fetch_rating($raw_id);
//    echo json_encode($widget_data);
//}
//
//# Data is stored as:
//#     widget_id:number_of_voters:total_points:dec_avg:whole_avg
//function fetch_rating($raw_id) {
//    
//    $all  = file('./ratings.data.txt');
//    
//    foreach($all as $k => $record) {
//        if(preg_match("/$raw_id:/", $record)) {
//            $selected = $all[$k];
//            break;
//        }
//    }
//
//    if($selected) {
//        $data = split(':', $selected);
//        $data[] = round( $data[2] / $data[1], 1 );
//        $data[] = round( $data[3] );
//    }
//    else {
//        $data[0] = $raw_id;
//        $data[1] = 0;
//        $data[2] = 0;
//        $data[3] = 0;
//        $data[4] = 0;
//    }
//    
//    return $data;
//}
//
//
//
//
//function register_vote() {
//    
//    preg_match('/star_([1-5]{1})/', $_REQUEST['clicked_on'], $match);
//    $vote = $match[1];
//    
//    $current_data = fetch_rating($_REQUEST['widget']);
//    
//    $new_data[] = $current_data['stars'] + $vote;
//    $new_data[] = $current_data['cast'] + 1;
//    
//
//    # --> This needs to be fixed, since a widget ID is ALWAYS passed in
//    # it should be a class property
//    file_put_contents($_REQUEST['widget'] . '.txt', "{$new_data[0]}\n{$new_data[1]}");
//    
//    return_rating($_REQUEST['widget']);
//}

    //foreach($all as $k => $record) {
    //    if(preg_match("/$raw_id:/", $record)) {
    //        $selected = $all[$k];
    //        break;
    //    }
    //}
    //
    //if($selected) {
    //    $this->data = split(':', $selected);
    //    $this->data[] = round( $this->data[2] / $this->data[1], 1 );
    //    $this->data[] = round( $this->data[3] );
    //}
    //else {
    //    $this->data[0] = $this->widget_id;
    //    $this->data[1] = 0;
    //    $this->data[2] = 0;
    //    $this->data[3] = 0;
    //    $this->data[4] = 0;
    //}
?>