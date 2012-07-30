<?php
/*
Plugin Name: Like Dislike counter
Plugin URI: http://www.wpfruits.com
Description: Like dislike counter for posts and comments
Author: WPFruits
Version: 1.0
Author URI: http://www.wpfruits.com
*/
function like_dislike_counter_install() 
	{
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$table_name = $wpdb->prefix."like_dislike_counters";     
     	if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
     	{
     	$sql= "CREATE TABLE ".$table_name."(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,post_id VARCHAR( 200 ) NOT NULL,ul_key VARCHAR( 30 ) NOT NULL,ul_value VARCHAR( 30 ) NOT NULL);"; 
     	dbDelta($sql);
	 	}
      	
   }
register_activation_hook(__FILE__,'like_dislike_counter_install');

function like_dislike_couter_init_method() {
    wp_enqueue_script( 'jquery' );
}    
 
add_action('init', 'like_dislike_couter_init_method');


function like_counter_p($text="Likes: ",$post_id=NULL)
{
	global $post;
	if(empty($post_id))
	{
	$post_id=$post->ID;
	}
	echo "<span class='ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','like')\" >".$text."<img src=\"".WP_PLUGIN_URL."/like-dislike-counter/images/up.png\" />(<span>".get_post_ul_meta($post_id,"like")."</span>)</span>";
}

function dislike_counter_p($text="dislikes: ",$post_id=NULL)
{
	global $post;
	if(empty($post_id))
	{
	$post_id=$post->ID;
	}
	echo "<span class='ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','dislike')\" >".$text."<img src=\"".WP_PLUGIN_URL."/like-dislike-counter/images/down.png\" />(<span>".get_post_ul_meta($post_id,"dislike")."</span>)</span>";
}

function like_counter_c($text="Likes: ",$post_id=NULL)
{
	global $comment;
	if(empty($post_id))
	{
	$post_id=get_comment_ID();
	}
	echo "<span class='ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','c_like')\" >".$text."<img src=\"".WP_PLUGIN_URL."/like-dislike-counter/images/up.png\" />(<span>".get_post_ul_meta($post_id,"c_like")."</span>)</span>";
}
function dislike_counter_c($text="dislikes: ",$post_id=NULL)
{
	global $comment;
	if(empty($post_id))
	{
	$post_id=get_comment_ID();
	}
	echo "<span class='ul_cont' onclick=\"alter_ul_post_values(this,'$post_id','c_dislike')\" >".$text."<img src=\"".WP_PLUGIN_URL."/like-dislike-counter/images/up.png\" />(<span>".get_post_ul_meta($post_id,"c_dislike")."</span>)</span>";
}
function get_post_ul_meta($post_id,$up_type)
{
	global $wpdb;
	$table_name = $wpdb->prefix."like_dislike_counters"; 
	$sql="select ul_value from $table_name where post_id=$post_id and ul_key='$up_type' ;";
	$to_ret=$wpdb->get_var($sql);
	if(empty($to_ret))
	{
	$to_ret=0;
	}
	return $to_ret;
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
	$sql="update $table_name set ul_value=".($lnumber+1)." where post_id='$post_id' and ul_key='$up_type';";
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
		$sql="insert into $table_name(post_id,ul_key,ul_value) values('$post_id','$up_type',".($lnumber+1).");";
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

function wp_dislike_like_footer_script() {
	if(!is_admin())
	{
	?>
    <script type="text/javascript">
    function alter_ul_post_values(obj,post_id,ul_type)
	{
		jQuery(obj).find("span").html("..");
    	jQuery.ajax({
   		type: "POST",
   		url: "<?php echo WP_PLUGIN_URL;?>/like-dislike-counter/ajax_counter.php",
   		data: "post_id="+post_id+"&up_type="+ul_type,
   		success: function(msg){
     		jQuery(obj).find("span").html(msg);
   			}
 		});
	}
	</script>
    
    <?php
    }
}

add_action('wp_footer', 'wp_dislike_like_footer_script');
