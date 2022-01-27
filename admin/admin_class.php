<?php
session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';

    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}
	//fungsi login admin & staff
	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_type'] == 1)
				return 1;
			else
				return 2;
		}else{
			return 3;
		}
	}
	//fungsi logout
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	//fungsi untuk menambahkan user admin dan staff ataupun perubahan pada user admin dan staff
	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	//fungsi untuk menyimpan perubahan settings ataupun penamambahan settings
	function save_settings(){
		extract($_POST);
		$data = " hotel_name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data." where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}
	//fungsi untuk menyimpan perubahan category ataupun penamambahan category
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", price = '$price' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
						$data .= ", cover_img = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO room_categories set ".$data);
		}else{
			$save = $this->db->query("UPDATE room_categories set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	//fungsi untuk menghapus category
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM room_categories where id = ".$id);
		if($delete)
			return 1;
	}
	//fungsi untuk menyimpan perubahan room ataupun penamambahan room
	function save_room(){
		extract($_POST);
		$data = " room = '$room' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", status = '$status' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO rooms set ".$data);
		}else{
			$save = $this->db->query("UPDATE rooms set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	//fungsi untuk menghapus category
	function delete_room(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM rooms where id = ".$id);
		if($delete)
			return 1;
	}
	//fungsi untuk menyimpan perubahan data check_in ataupun penamambahan check_in
	function save_check_in(){
		extract($_POST);
		$data = " room_id = '$rid' ";
		$data .= ", name = '$name' ";
		$data .= ", contact_no = '$contact' ";
		$data .= ", status = 1 ";

		$data .= ", date_in = '".$date_in.' '.$date_in_time."' ";
		$out= date("Y-m-d H:i",strtotime($date_in.' '.$date_in_time.' +'.$days.' days'));
		$data .= ", date_out = '$out' ";
		$i = 1;
		while($i== 1){
			$ref  = sprintf("%'.04d\n",mt_rand(1,9999999999));
			if($this->db->query("SELECT * FROM checked where ref_no ='$ref'")->num_rows <= 0)
				$i=0;
		}
		$data .= ", ref_no = '$ref' ";

		if(empty($id)){
			$save = $this->db->query("INSERT INTO checked set ".$data);
			$id=$this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE checked set ".$data." where id=".$id);
		}
		if($save){

			$this->db->query("UPDATE rooms set status = 1 where id=".$rid);
					return $id;
		}
	}
	//fungsi untuk mengubah status menjadi checkout
	function save_checkout(){
		extract($_POST);
			$save = $this->db->query("UPDATE checked set status = 2 where id=".$id);
			if($save){

				$this->db->query("UPDATE rooms set status = 0 where id=".$rid);
						return 1;
			}

	}
	//fungsi untuk menyimpan data booking
	function save_book(){
		extract($_POST);
		$data = " booked_cid = '$cid' ";
		$data .= ", name = '$name' ";
		$data .= ", contact_no = '$contact' ";
		$data .= ", status = 0 ";
		$data .= ", date_in = '".$date_in.' '.$date_in_time."' ";
		$out= date("Y-m-d H:i",strtotime($date_in.' '.$date_in_time.' +'.$days.' days'));
		$data .= ", date_out = '$out' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
						$data .= ", cover_img = '$fname' ";
		}
		$i = 1;
		while($i== 1){
			$ref  = sprintf("%'.04d\n",mt_rand(1,9999999999));
			if($this->db->query("SELECT * FROM checked where ref_no ='$ref'")->num_rows <= 0)
				$i=0;
		}
		$data .= ", ref_no = '$ref' ";

			$save = $this->db->query("INSERT INTO checked set ".$data);
			$id=$this->db->insert_id;

		if($save){
					return $id;
		}
	}
	function payment(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", price = '$price' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
						$data .= ", cover_img = '$fname' ";

		}
		if(empty($id)){
			echo "Data Tidak Ditemukan";
		}else{
			$save = $this->db->query("UPDATE checked set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}

}
