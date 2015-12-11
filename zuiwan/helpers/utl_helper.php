<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!defined('UTL_HELPER_LOADED'))
{
	define('UTL_HELPER_LOADED', true);

	/**
	 * 将时间转为多少 秒/天/月 前
	 */
	function it_time_before($time){
		$t= time() - $time;
		$f=array(
				'31536000'=>'年',
				'2592000'=>'个月',
				'604800'=>'星期',
				'86400'=>'天',
				'3600'=>'小时',
				'60'=>'分钟',
				'1'=>'秒'
		);
		foreach ($f as $k=>$v) {
			if (0 != $c=floor($t/(int)$k)) {
				return $c.$v.'前';
			}
		}
	}

	/**
	 * 天时分秒
	 * @param $sec
	 */
	function it_time_intval($sec){
		$ret = '';
		$arr = array('天'=>86400,'小时'=>3600,'分钟'=>60,'秒'=>1);
		foreach ($arr as $k=>$v){
			$t = floor($sec / $v);
			if($t){
				$ret .= $t.$k;
			}
			$sec = $sec % $v;
			if($sec == 0) break;
		}
		if(!$ret) $ret = '0秒';
		return $ret;
	}

	/**
	 * 抽取数据对像的成员到一维数据里
	 * @param array $arr_obj 对像数组
	 * @param string $member 要提取的对像的成员名称
	 * @return string
	 */
	function it_objarr_member2arr($arr_obj,$member){
		$ret = array();
		foreach($arr_obj as $obj){
			$ret[] = $obj->$member;
		}
		return $ret;
	}

	/**
	 * 遍历数组元素，对元素执行Func(v)
	 * @param array $arr
	 * @param string $func 函数名，原型func($val)
	 * @param bool $store 是否将函数反回结果保存
	 */
	function it_arr_foreach(&$arr,$func,$store){
		foreach($arr as &$v){
			if($store){
				$v = $func($v);
			}else{
				$func($v);
			}
		}
	}

	/**
	 * 遍历数组元素，对元素执行Func(k,v)
	 * @param array $arr
	 * @param string $func 函数名，原型func($k,$val)
	 * @param bool $store 是否将函数反回结果保存
	 */
	function it_arr_foreach2($arr,$func,$store){
		foreach($arr as $k=>&$a){
			if($store){
				$a = $func($k, $a);
			}else{
				$func($k, $a);
			}
		}
	}

	/**
	 * 多行
	 * @param string $sql
	 */
	function it_db_rows($db,$sql,$binds=false,$key=null){
		$query = $db->query($sql,$binds);
		$data = $query->result();
		$query->free_result();

		if($data && $key){
			$ret = array();
			foreach($data as $d){
				 $ret[$d->$key] = $d;
			}
			return $ret;
		}
		return $data;
	}

	/**
	 * 返回整行，By $id 这个条件
	 * @param unknown $db
	 * @param String $table
	 * @param String|Array $id 如果是String的话 'id'=>$id
	 * @return unknown
	 */
	function it_db_byid($db, $table, $id){
		if(!is_array($id)){
			$id = array('id'=>$id);
		}
		$db->where($id);
		$query = $db->get($table);
		$data = $query->result();
		$query->free_result();
		return $data[0];
	}

	/**
	 * 获取单行
	 * @param string $sql
	 */
	function it_db_row($db,$sql,$binds=false){
		$data = it_db_rows($db, $sql,$binds);
		if($data){
			return $data[0];
		}
		return null;
	}

	/**
	 *
	 * @param db $db
	 * @param string $count_sql SELECT COUNT(*) AS record_count FROM ...
	 * @param string $data_sql without LIMIT
	 * @param int $page_no 从1开始
	 * @param int $pagesize
	 * @return array('count'=>int,'data'=>array);
	 */
	function it_db_pager($db,$count_sql,$data_sql,$page_no,$pagesize){

		$page_no = intval($page_no);
		$pagesize = intval($pagesize);

		if($page_no < 1 || $pagesize < 1){
			throw new Info_Exception('pager arg wrong', 1);
		}

		$r = it_db_row($db,$count_sql);

		$from = ($page_no - 1) * $pagesize;
		$data_sql .= " LIMIT $from,$pagesize";
		$rows = it_db_rows($db, $data_sql);
		$data = new stdClass();
		$data->count = $r->record_count;
		$data->data = $rows;

		return $data;
	}

	function it_pager($current_pager, $pager_size, $record_count, $script){

		$pager_count = ceil($record_count / $pager_size);

		$html = "<div id=\"pager1\" class=\"gri_pg\"><div class=\"pg\">";
		$html = '<nav><ul class="pagination">';

		//第一页，上一页是否可用
		if($current_pager > 1){
			# $html .= "<a class=\"first\" href=\"javascript:{$script}(1);\"><i class=\"i_pg_f\"></i></a><a class=\"prev\" href=\"javascript:{$script}({$current_pager}-1);\"><i class=\"i_pg_l\"></i></a>";//first pre
            $html .= '<li><a href="javascript:' . $script . '(1);"><span>&laquo;</span></a>';
		}else{
			# $html .= "<a class=\"first\" href=\"javascript:void(0);\"><i class=\"i_pg_f\"></i></a><a class=\"prev\" href=\"javascript:void(0);\"><i class=\"i_pg_l\"></i></a>";//disable
            $html .= '<li class="disabled"><a href="javascript:void(0);"><span>&laquo;</span></a>';
		}

		//显示几个页面
		if($pager_count > 6){ //小于6直接显示全部就OK了
			$pager_start = $current_pager - 2;
			$pager_end = $current_pager + 2;
			if($pager_start < 1){
				$pager_end += 1 - $pager_start;
			}
			if($pager_end > $pager_count){
				$pager_start -= $pager_end - $pager_count;
			}

			if($pager_start < 1){
				$pager_start = 1;
			}
			if($pager_end > $pager_count){
				$pager_end = $pager_count;
			}
		}else{
			$pager_start = 1;
			$pager_end = $pager_count;
		}

		for($i=$pager_start;$i<=$pager_end;$i++){
			if($i == $current_pager){
				# $html .= "<strong class=\"current\" href=\"javascript:void(0);\">{$i}</strong>"; //显示当前页样式
                $html .= '<li class="active"><a>' . $i . '</a></li>';
			}else{
				# $html .= "<a href=\"javascript:{$script}({$i});\">{$i}</a>";
                $html .= '<li><a href="javascript:' . $script . '(' . $i . ')">' . $i . '</a></li>';
			}
		}

		//显示最后一页的页码
		if($pager_end < $pager_count){
			#if($pager_end + 1 < $pager_count){
			#	$html .= "<span class=\"dot\">...</span>";
			#}
			#$html .= "<a href=\"javascript:{$script}({$pager_count});\">{$pager_count}</a>";
            $html .= '<li><a href="javascript:' . $script . '(' . $pager_count . ')">' . $pager_count . '</a></li>';
		}

		//下一页，最后一页
		if($current_pager < $pager_count){
			# $html .= "<a class=\"next\" href=\"javascript:{$script}({$current_pager}+1);\"><i class=\"i_pg_n\"></i></a><a href=\"javascript:{$script}({$pager_count});\"><i class=\"i_pg_e\"></i></a>";
            $html .= '<li><a href="javascript:' . $script . '(' . $current_pager . '+1)">&raquo;</a></li>';
		}else{
			# $html .= "<a class=\"next\" href=\"javascript:void(0);\"><i class=\"i_pg_n\"></i></a><a href=\"javascript:void(0);\"><i class=\"i_pg_e\"></i></a>";
            $html .= '<li class="disabled"><a href="javascript:void(0);"><span>&raquo;</span></a>';
		}

		$html .= "</ul></nav><div class=\"show\">共<em>{$record_count}</em>条记录，每页显示<span class=\"gri_datatable_pg_rowcount\"><select style=\"width: 50px\"id=\"selPagesize\" class=\"gri_datatable_rownum ipt_show\" onchange=\"$script(1)\">";

		$arr_size = array(5,10,15,20,30,50,80,100,200);
		foreach ($arr_size as $s){
			if($s == $pager_size){
				$html .= "<option value=\"{$s}\" selected=\"selected\">{$s}</option>";
			}else{
				$html .= "<option value=\"{$s}\">{$s}</option>";
			}
		}
		$html .= "</select> 条</span></div></div>";

		return $html;
	}

	function it_arr_select_tag($arr, $el_id, $default_val, $propertys, $on_change="",$showall=false){
		$on_change = $on_change!="" ? " onchange=\"{$on_change}()\"" : '';
		$html = "<select id=\"$el_id\" name=\"$el_id\" $propertys $on_change >";
		if($showall){
		    $html .= "<option value=\"\" >全部</option>";
		}
		foreach ($arr as $k=>$v){
			if($k == $default_val ){
				$html .= "<option value=\"{$k}\" selected=\"selected\">{$v}</option>";
			}else{
				$html .= "<option value=\"{$k}\">{$v}</option>";
			}
		}
		$html .= '</select>';
		return $html;
	}

	/**
	 *
	 * @param array(class) $list
	 * @param string $property_val member_field_name
	 * @param string $property_name member_field_name
	 * @param string $default_val string
	 * @param string $select_el_id
	 * @param string $on_change 触发Change的JS函数名称不用带括号
	 */
	function it_setlect_tag($list,$property_val,$property_name,$default_val,$select_el_id,$on_change='',$propertys='',$showall=false){
		$on_change = $on_change ? " onchange=\"{$on_change}()\"" : '';
		$html = "<select id=\"$select_el_id\" name=\"$select_el_id\" {$on_change} $propertys >";
		if($showall){
			$html .= "<option value=\"\" >全部</option>";
		}
		foreach ($list as $r){
			if($r->$property_val == $default_val ){
				$html .= "<option value=\"{$r->$property_val}\" selected=\"selected\">{$r->$property_name}</option>";
			}else{
				$html .= "<option value=\"{$r->$property_val}\">{$r->$property_name}</option>";
			}
		}
		$html .= '</select>';
		return $html;
	}


	/**
	 *
	 * @param spline|pie $type
	 * @param string $title
	 * @param array(string) $arr_x
	 * @param array(numeric) $arr_data
	 * @param int $width
	 * @param string $filepath path end with /
	 * @return string
	 */
	function it_highcharts2png_spline($title,$arr_x,$arr_data,$width,$height,$img_width,$filepath){
		$x = json_encode($arr_x);
		$data = json_encode($arr_data);
		$opt = "{
chart:{
    type: 'spline',
    zoomType:'x',
    width:$width,
    height:$height
},

plotOptions:{
	spline:{
		lineWidth:1.5,
		marker:{
			radius:1.5
		}
	}
},

credits:{
    enabled : false
},

title:{
    text: '$title'
},

xAxis:{
    categories: $x
},

yAxis:{
    title:{
        text: ''
    }
},

series:[{
    name: '',
    data: $data
}]

}";
		return it_highcharts2png($opt,$width,$filepath);
	}

	/**
	 *
	 * @param string $highcharts
	 * @param int $width
	 * @param unknown $filepath with / path only, file name gen by method
	 * @return string file full path
	 */
	function it_highcharts2png($hc_json,$width,$filepath){
		$filename = md5($hc_json . $width);
		$imgname = $filename.'.png';

		$full_img = $filepath . $imgname;
		if(file_exists($full_img)){
			return $imgname;
		}
		$full_json = "{$filepath}{$filename}.json";
		file_put_contents($full_json, $hc_json);
		$ret = 0;
		$output = array();
		exec("/usr/local/phantomjs-2.0.0/bin/phantomjs /usr/local/phantomjs-2.0.0/highcharts/highcharts-convert.js -infile {$full_json} -outfile {$full_img} -scale 2.5 -width $width -constr Chart", $output, $ret);
		if($ret){ //不等于0表示出错了
			throw new Info_Exception('gen png failed', 1);
		}
		return $imgname;
	}

	function it_is_empty($str){
		if(!isset($str)) return true;
		$str = trim($str);
		if($str === '') return true;

		return false;
	}

	/**
	 * 将字符串中的用户去重
	 * @param string ... $users 用分号分隔用户字符串
	 */
	function it_unique_users($users){
		$args = func_get_args();
		$users = join(';',$args);
		$users = explode(';',$users);
		$users = array_unique($users);
		$us = array();
		foreach($users as $u){
			if($u){
				$us[] = $u;
			}
		}
		$us = join(';',$us);
		if($us) $us .= ";";
		return trim($us,';');
	}

	/**
	 * 获取时间的日期部分 字符串
	 * @param int|string $datetime
	 * @return 'yyyy-mm-dd'
	 */
	function it_date($datetime){
        if(!is_numeric($datetime)){
            $datetime = strtotime($datetime);
        }
        return date('Y-m-d',$datetime);
	}

	/**
	 * 将时分秒 h:i:s 转成 总秒数
	 * @param string $h_i_s
	 * @return seconds
	 */
	function it_to_second($h_i_s){
        $arr = explode(':', $h_i_s);
        return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
	}

	/**
	 * 求ta,tb时间交集 的 总秒数
	 * ta1<ta2,tb1<tb2
	 * @param unixtime $ta1
	 * @param unixtime $ta2
	 * @param unixtime $tb1
	 * @param unixtime $tb2
	 */
    function it_time_intersection($ta1, $ta2, $tb1, $tb2){
    	if($ta1 <= $tb1 && $tb1 <= $ta2){
    		if($ta2 < $tb2){
    			// ta1 ... tb1 *** $ta2 ... $tb2
    			return $ta2 - $tb1;
    		}
    		// ta1 ... tb1 *** $tb2 ... $ta2
    		return $tb2 - $tb1;
    	}

    	if($tb1 <= $ta1 && $ta1 <= $tb2){
    		if($ta2 < $tb2){
    			// tb1 ... ta1 *** $ta2 ... $tb2
    			return $ta2 - $ta1;
    		}
    		// tb1 ... ta1 *** $tb2 ... $ta2
    		return $tb2 - $ta1;
    	}
    	return 0;
    }
	/**
	 * get schedual finish time
	 * @author junacheng
	 * @param datetime $date
	 * @param int $days
	 * @return datetime
	 */
	function it_get_plan_finish_time($date,$days)
	{

	}

    // do not create an API doing same thing with another

	function it_encode_html($str)
	{
		$farr = array(
				"/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/isU",
				"/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
				"[<]",
				"[>]",
		);

		$tarr = array(
				"",
				"",
				"《",
				"》",
		);
		$str = addslashes($str);
		$str = preg_replace( $farr,$tarr,$str);
		return $str;
	}

	function it_decode_html($str)
	{
		$str = stripcslashes($str);
		return $str;
	}

	/**
	 * 显示工作量的辅助函数
	 * @param it_version $data
	 * @param string $prefix
	 * @return string
	 */
	function it_plan_str($days,$star,$end)
	{
	    $star = date('Y-m-d',strtotime($star));
	    $end = date('Y-m-d',strtotime($end));
	    return  "{$star}&nbsp;至&nbsp;{$end}&nbsp共 &nbsp$days&nbsp;工作日";
	}

	function HS($html){
        echo htmlspecialchars($html);
	}
}
