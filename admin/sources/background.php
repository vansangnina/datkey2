<?php	if(!defined('_source')) die("Error");
$act = (isset($_REQUEST['act'])) ? addslashes($_REQUEST['act']) : "";
switch($act){
	case "capnhat":
		get_gioithieu();
		$template = "background/item_add";
		break;
	case "save":
		save_gioithieu();
		break;
		
	default:
		$template = "index";
}
function fns_Rand_digit($min,$max,$num)
	{
		$result='';
		for($i=0;$i<$num;$i++){
			$result.=rand($min,$max);
		}
		return $result;	
	}

function get_gioithieu(){
	global $d, $item;
	$type = htmlspecialchars($_REQUEST['type']);
	$sql = "select * from #_background where type='".$type."' limit 0,1";
	$d->query($sql);
	if($d->num_rows()==0)
	{
		$data['hienthi'] = '1';
		$data['ngaytao'] = time();
		$data['type'] = $type;
		
		$d->setTable('background');
		if($d->insert($data))
			transfer("Dữ liệu được khởi tạo","index.php?com=background&act=capnhat&type=".$type);
		else
			transfer("Khởi tạo dữ liệu lỗi","index.php?com=background&act=capnhat&type=".$type);
	}
	$item = $d->fetch_array();
}
function save_gioithieu(){
	global $d,$config;
	$type = htmlspecialchars($_REQUEST['type']);
	if(empty($_POST)) transfer("Không nhận được dữ liệu", "index.php?com=background&act=capnhat&type=".$type);
	
	foreach ($config['lang'] as $key => $value) {
		$file_name = $_FILES['file'.$key]['name'];		
		if($photo = upload_image("file".$key, _format_duoihinh,_upload_hinhanh,$file_name)){
			$data['photo'.$key] = $photo;
			//$data['thumb'] = create_thumb($data['photo'], 170, 130, _upload_hinhanh,$file_name,2);
			$d->setTable('background');			
			$d->select();
			if($d->num_rows()>0){
				$row = $d->fetch_array();
				delete_file(_upload_hinhanh.$row['photo'.$key]);
				//delete_file(_upload_hinhanh.$row['thumb']);
			}
		}		
	}
	$data['link'] = $_POST['link'];
	$data['tenkhongdau'] = changeTitle($_POST['ten']);
	$data['title'] = $_POST['title'];
	$data['keywords'] = $_POST['keywords'];
	$data['description'] = $_POST['description'];
	$data['hienthi'] = isset($_POST['hienthi']) ? 1 : 0;
	$data['ngaysua'] = time();
	
	foreach ($config['lang'] as $key => $value) {
		$data['ten'.$key] = $_POST['ten'.$key];
		$data['mota'.$key] = magic_quote($_POST['mota'.$key]);
		$data['noidung'.$key] = magic_quote($_POST['noidung'.$key]);			
	}

	$d->reset();
	$d->setTable('background');
	$d->setWhere('type', $type);
	if($d->update($data))
		transfer("Dữ liệu được cập nhật", "index.php?com=background&act=capnhat&type=".$type);
	else
		transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=background&act=capnhat&type=".$type);
}

?>