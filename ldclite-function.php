<?php
global $ldc_like_text, $ldc_dislike_text;
$ldc_options = get_option('ldclite_options');
$ldc_like_text = $ldc_options['ldc_like_text'];
$ldc_dislike_text = $ldc_options['ldc_dislike_text'];
$ldc_deactivate = $ldc_options['ldc_deactivate'];

function ldc_like_counter_p($text="Likes: ",$post_id=NULL)
{
	global $post;
	if(empty($post_id))
	{
	$post_id=$post->ID;
	}
	$ldc_return = "<span class='ldc-ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','like')\" >".$text."<img src=\"".plugins_url( 'images/up.png' , __FILE__ )."\" />(<span>".get_post_ul_meta($post_id,"like")."</span>)</span>";
	return $ldc_return;
}

function ldc_dislike_counter_p($text="dislikes: ",$post_id=NULL)
{
	global $post;
	if(empty($post_id))
	{
	$post_id=$post->ID;
	}
	$ldc_return = "<span class='ldc-ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','dislike')\" >".$text."<img src=\"".plugins_url( 'images/down.png' , __FILE__ )."\" />(<span>".get_post_ul_meta($post_id,"dislike")."</span>)</span>";
	return $ldc_return;
}

function ldc_like_counter_c($text="Likes: ",$post_id=NULL)
{
	global $comment;
	if(empty($post_id))
	{
	$post_id=get_comment_ID();
	}
	$ldc_return = "<span class='ldc-ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','c_like')\" >".$text."<img src=\"".plugins_url( 'images/up.png' , __FILE__ )."\" />(<span>".get_post_ul_meta($post_id,"c_like")."</span>)</span>";
	return $ldc_return;
}

function ldc_dislike_counter_c($text="dislikes: ",$post_id=NULL)
{
	global $comment;
	if(empty($post_id))
	{
	$post_id=get_comment_ID();
	}
	$ldc_return = "<span class='ldc-ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','c_dislike')\" >".$text."<img src=\"".plugins_url( 'images/down.png' , __FILE__ )."\" />(<span>".get_post_ul_meta($post_id,"c_dislike")."</span>)</span>";
	return $ldc_return ;
}
function get_post_ul_meta($post_id,$up_type)
{
	global $wpdb;
	$table_name = $wpdb->prefix."like_dislike_counters"; 
	$sql = $wpdb->prepare( "select ul_value from $table_name where post_id = %d and ul_key = %s",$post_id, $up_type );
	
	$to_ret=$wpdb->get_var($sql);
	if(empty($to_ret))
	{
	$to_ret=0;
	}
	return $to_ret;
}



if(isset($ldc_options['ldc_deactivate']) && $ldc_options['ldc_deactivate']===0){
	//if ( ! is_admin() ) {
	add_filter( 'comment_text', 'ldclite_addCommentLike' );
	//}
	// add_action( 'the_content', 'ldclite_addPostLike' );
}

function ldclite_addPostLike ( $content ) 
{
	global $ldc_like_text, $ldc_dislike_text;
	$ldc_return = '';
	if(is_page()){
		$ldc_return .= ldc_like_counter_p($ldc_like_text);
		$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		//$ldc_return = '<div class="clearfix">'.$ldc_return.'</div>';
		//return $content.$ldc_return;
	}
	else{

		$ldc_return = '';
		if(is_home()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else if( is_category()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else if( is_tag()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else if(is_tax()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else if(is_author()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else if(is_date()){
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		else{
			$ldc_return .= ldc_like_counter_p($ldc_like_text);
			$ldc_return .= ldc_dislike_counter_p($ldc_dislike_text);
		}
		//return 'Tikendra maitry';
	}
	$ldc_return = '<div class="clearfix">'.$ldc_return.'</div>';
	return $content.$ldc_return;
}

function ldclite_addCommentLike( $mytext ) {
	global $comment, $ldc_like_text, $ldc_dislike_text;
	$mytext = get_comment_text( $comment );
	$mytext .= "\n";
	$mytext .= '<div class="ldc-cmt-box clearfix">';
	$mytext .= ldc_like_counter_c($ldc_like_text);
	$mytext .= ldc_dislike_counter_c($ldc_dislike_text);
	$mytext .= '</div>';
	$mytext .= '<div style="clear:both;"></div>';
	return $mytext;
}

function update_post_ul_meta($post_id,$up_type)
{
	global $wpdb;
	$table_name = $wpdb->prefix."like_dislike_counters";
	$lnumber=get_post_ul_meta($post_id,$up_type);
	if($up_type=='c_like'||$up_type=='c_dislike')
	{
	$for_com='c_';
	}
	else
	{
	$for_com='';
	}
	if($lnumber)
	{ 
		$sql = $wpdb->prepare( "update $table_name set ul_value = %d where post_id = %d and ul_key = %s",$lnumber+1, $post_id, $up_type );
	
		if(isset($_COOKIE['ul_post_cnt']))
		{
			$posts=$_COOKIE['ul_post_cnt'];
			array_push($posts,$for_com.$post_id);
			foreach($posts as $key=>$value)
			{
			setcookie("ul_post_cnt[$key]",$value, time()+1314000);
			}
		}
		else
		{
		setcookie("ul_post_cnt[0]",$for_com.$post_id, time()+1314000);
		}
		$wpdb->query($sql);
	}
	else
	{
		$sql = $wpdb->prepare( "insert into $table_name(post_id,ul_key,ul_value) values(%d,%s,%d)",$post_id, $up_type,$lnumber+1 );

		if(isset($_COOKIE['ul_post_cnt']))
		{
			$posts=$_COOKIE['ul_post_cnt'];
			array_push($posts,$post_id);
			foreach($posts as $key=>$value)
			{
			setcookie("ul_post_cnt[$key]",$for_com.$value, time()+1314000);
			}
		}
		else
		{
		setcookie("ul_post_cnt[0]",$for_com.$post_id, time()+1314000);
		}
	$wpdb->query($sql);
	}
}

/* Admin Like Editing Function - Begin */
add_action( 'add_meta_boxes_comment', 'pmg_comment_tut_add_meta_box' );
function pmg_comment_tut_add_meta_box()
{
    add_meta_box( 'pmg-comment-title', __( 'Comment Likes' ), 'pmg_comment_tut_meta_box_cb', 'comment', 'normal', 'high' );
}

function pmg_comment_tut_meta_box_cb( $comment )
{
    $likes = get_post_ul_meta( $comment->comment_ID, 'c_like' );
    $dislikes = get_post_ul_meta( $comment->comment_ID, 'c_dislike' );
    wp_nonce_field( 'pmg_comment_update', 'pmg_comment_update', false );
    ?>
    <p>
        <label for="pmg_comment_likes"><?php _e( 'Likes' ); ?></label>;
        <input type="text" name="pmg_comment_likes" value="<?php echo esc_attr( $likes ); ?>" class="widefat" />
                <label for="pmg_comment_dislikes"><?php _e( 'Dislikes' ); ?></label>;
        <input type="text" name="pmg_comment_dislikes" value="<?php echo esc_attr( $dislikes ); ?>" class="widefat" />
        
    </p>
    <?php
}


function pmg_comment_tut_edit_comment( $comment_id )
{ 
    if( ! isset( $_POST['pmg_comment_update'] ) || ! wp_verify_nonce( $_POST['pmg_comment_update'], 'pmg_comment_update' ) )
        return;
    if( isset( $_POST['pmg_comment_likes'] ) )
          admin_update_likes($comment_id, "c_like", (int)$_POST['pmg_comment_likes'] );
    if( isset( $_POST['pmg_comment_dislikes'] ) )
          admin_update_likes($comment_id, "c_dislike", (int)$_POST['pmg_comment_dislikes'] );
}
add_action( 'edit_comment', 'pmg_comment_tut_edit_comment' );

function admin_update_likes ($post_id, $up_type, $lnumber) {
	global $wpdb;
	$table_name = $wpdb->prefix."like_dislike_counters";
	$olnumber = get_post_ul_meta($post_id, $up_type);
	if($olnumber) {
		$sql = $wpdb->prepare( "update $table_name set ul_value = %d where post_id = %d and ul_key = %s", $lnumber, $post_id, $up_type );
	} else {
		$sql = $wpdb->prepare( "insert into $table_name(post_id,ul_key,ul_value) values(%d,%s,%d)",$post_id, $up_type,$lnumber );
	}
	error_log($sql);
	$wpdb->query($sql);

}
/*Admin Like Editing Functions - End*/

function like_counter_p($text="Likes: ",$post_id=NULL){
	global $ldc;
	global $post_id;
	echo ldc_like_counter_p($text);
}
function dislike_counter_p($text="dislikes: ",$post_id=NULL){
	global $ldc;
	global $post_id;
	echo ldc_dislike_counter_p($text);
}
function like_counter_c($text="Likes: ",$post_id=NULL){
	global $ldc;
	global $post_id;
	echo ldc_like_counter_c($text);
}
function dislike_counter_c($text="dislikes: ",$post_id=NULL){
	global $ldc;
	global $post_id;
	echo ldc_dislike_counter_c($text);
}
