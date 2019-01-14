<?php
class Helper_model extends CI_Model {

/*
*	info:	This Method is meant to fetch global configuration data from database
*	to use this method
*	Create table 'config'
*	id increment and primary key
*	name string
*	value text
*	Where name will be settings name like 'sitename'
*	Value will store sitename value like 'Example Site'
*	Parameter name : string
*/

	public function get_settings($name){
	/*
	Fetching data from config table of database	
	*/
	
		$query			=	$this->db->get_where('config', array('name' => $name),1);
		$result_row		=	$query->row();
		if($result_row){
			return $details->value;
		}
	
	}

	

/*
*	Uploading file was not this simple enough.
*	All you need is name of input type file which is $_FILES's name
*	And Upload path for the file where you want to upload file.
*	Make Sure Path where file is to be uploaded, exist and is writable
*	All Failure message will be logged to log directory
*	$key is the key index in case of multiple file upload
* call the following function in foreach loop and the index will be $key 
* in case you are uploading multiple files
*/
	public function upload_file($path,$name,$key=NULL){
		$this->load->helper('string');
		/*
		Upload file helper function	
		*/
		if(!isset($path)){
			/*
			Need to set path like /directory/image/	
			*/
			log_message('error','Image upload path not defined');
			return FALSE;
		}
		if(!isset($name)){
			/*
			name of input type file is missing
			*/
			
			log_message('error','File name not defined');
			return false;
		}
		$target_dir =	$path;
		$time			=	md5(base64_encode(time().random_string('alnum',5)));
		if($key===NULL){
			$target_file	=	$target_dir.$time."_".rand(0,10).".".strtolower(get_file_ext($_FILES[$name]["name"]));
		}else{
			$target_file	=	$target_dir.$time."_".rand(0,10).".".strtolower(get_file_ext($_FILES[$name]["name"][$key]));
		}
		$uploadOk = 1;

		if($_FILES[$name]){
			if($key===NULL){
				$origin_path 	=	$_FILES[$name]["tmp_name"];
			}else{
				$origin_path 	=	$_FILES[$name]["tmp_name"][$key];
			}
			

			if (move_uploaded_file($origin_path, $target_file)) {
				
				if(ENV == 'development'){
					$target_file = img_to_jpg($target_file);
				}
				return $target_file;
			}else {
				
				log_message('error','Directory does not exist, please create directory or defined valid path in first argument');
				return FALSE;
			}
				
		}else{
			log_message('error','File not found, please check if form has attribute enctype=multipart/form-data');
			return FALSE;
		}
	}




/*
*	This function can be used anywhere to display alert message in well formatted manner
*	Any warning or success or info message will be displayed
*	Require bootstrap for well formatting
*	Parameters : msg : Message to be displayed
*				type : Type of messages 'info','danger','success','warning'
*				'icon': This will be fontawesome icon class which icon should be displayed
*				example : for warning, instead of 'fa fa-warning', 'fa-warning' should be passed here
*
*/
	public function display_alert($msg,$type='info',$icon='info-circle'){
		if(!$msg){
			return FALSE;
			exit();
		}
		?>
		<div class="alert alert-<?php echo $type;?>" align="center">
			<i class="fa fa-<?php echo $icon;?>"></i> <?php echo $msg;?>
		</div>
		<?php
	}



/*
*	This method can be used to redirect current page to next page and displayed message
*	Example, if login fails, Next page can be shown incorrect username password.
*	Example and parameter explaination
*	msg :	message to be displayed, e.g. Record deleted successfully
*	type:	Type/context of message example, for success or green color, 'success' should be passed
*		:	Available options are: success|warning|danger|info
*	url :	url should be the redirecting page url, without base_url, example 'home/login'
*
*/
	public function redirect_msg($msg,$type='info',$url){
	if(!$msg OR !$url){
	return false;	
	}
	
	$this->session->set_flashdata(array('msg'=>$msg,'type'=>$type));
	redirect(base_url($url));
	exit();
	}



/*
*	Works with Bootstap classes
*	This function needs to call on views where certain alert messages needs to be displayed.
*	This function works once the 'redirect_msg' is been called and data will be passed to session
*	And will be catch by this function
*	This function will be evoked when there is variable named 'msg'
*
*/
	public function alert_message(){
		if($this->session->userdata('msg')){
			?>
			<div id="fading_div" class="alert alert-<?php echo $this->session->userdata('type')?>">
				<i class="fa fa-info-circle fa-lg"></i> <?php echo $this->session->userdata('msg');?>
			</div>
			<?php
		}
	}



/*
*	Simple function for mailing using codeigniter
*	Required parameter is an array which should have 4 values in it
*	string name: by which name email should be sent
*	string : email, by which email should be sent, (from)
*	string : subject of the email
*	message: it can be simple text, html or complete view of html
*	it will return true when email function runs successfully, else false
*	more details will be visible in error log 
* if $attachment null only text mail sent
*	if $attachment is file path that file willbe sent
* if $attachment is array of multiple files multiple files will be sent 
* as an attachment
*/
	public function send_formatted_mail($data,$attachment=NULL){
		$this->load->library('email');
		$this->email->clear();
		
		//	Checking any blank entry
		if(empty($data['name'])
			OR empty($data['subject'])
			OR empty($data['to'])
			OR empty($data['message']) ){
			log_message('error','Empty paraters for email');
			return FALSE;
		}
		
		
		/*
			Initializing codeigniter email configuration
		*/
		$config['useragent']		=	'Codeigniter';
		$config['mailpath']			=	'/usr/sbin/sendmail'; // or "/usr/sbin/sendmail"
		$config['protocol']			=	'smtp';
		$config['smtp_host']		=	'localhost';
		$config['smtp_port']		=	'25';
		
		
		$config['mailtype']			=	'html';
		$config['charset']			=	'utf-8';
		
		$config['wordwrap']			=	TRUE;
		$config['newline']			=	"\r\n";
		$config['crlf'] 			= 	"\r\n";
			
			
		ini_set('sendmail_from', $data['from']);
		$this->email->reply_to($data['from'], $data['name']);
		$this->email->initialize($config);
		$this->email->to($data['to']);
		$this->email->from($data['from'], $data['name']);
		$this->email->subject($data['subject']);
		$this->email->message($data['message']);

		if($attachment!=NULL){
			if(!is_array($attachment)){
				$this->email->attach(FCPATH.$attachment);
			}else{
				foreach($attachment as $key => $attach){
					$this->email->attach(FCPATH.$attach);
				}
			}
		}
		
		if($mail = $this->email->send()){
			log_message('error','Message sent to :'.$data['to']);
			return TRUE;
		}else{
			log_message('error','message not sent to :'.$data['to']);
			return FALSE;
			exit();
		}

}




/*
*	Image resizing using this function
*	@Param strong upload path : image path
*	with and height optional any one parameter to be passed
*
*/
	public function image_resize($upload_path='',$width=NULL,$height=NULL){
			// Just need path to grab image and resize followed by overwriting
			if($upload_path==''	OR 	!file_exists($upload_path)){
				log_message('error','Helper_model : Empty file path or image does not exists for image resizing');
				return false;
			}

			if($width==NULL && $height==NULL){
				return false;
			}

			$this->load->library('image_lib');
			
			ini_set('memory_limit', '-1');	// this will prevent memory overload by PHP, 
			
			$config['source_image']		=	$upload_path;
			$config['new_image']		=	$upload_path;
			
			$this->image_lib->initialize($config); 

			$confirm=$this->image_lib->resize();
			
			if($confirm){
			
				return TRUE;
			
			}else{
			
				return FALSE;
			
			}
				
				
			/*
			Reference
			http://stackoverflow.com/questions/11193346/image-resizing-codeigniter
			*/
	}
	
	
	
	
	
	public function del($table=NULL,$col=NULL,$val=NULL){
	/*
	These are database CRUD helper,
	in case of error, please refer error log or use native codeigniter function
	*/
	
		if(($table==NULL) OR ($col==NULL) OR ($val==NULL)){
			log_message('error','Helper_model : Missing table, col, or value');
			return FALSE;
		}
		
		$query		=	$this->db->where($col, $val);
		$query		=	$this->db->delete($table);

		if(!$query){
			log_message('error','Helper_model! Unable to delete from '.$table." given attribute ".$col." with value ".$val);
			return FALSE;
		}

		return TRUE;
	}
	
	public function update($table=NULL,$data=NULL,$col=NULL,$val=NULL){

		if(($data==NULL) OR ($col==NULL) OR ($val==NULL) OR ($table==NULL)){
			log_message('error','Helper_model! Update > Missing table, col, or value');
			return FALSE;
			exit();
		}
		
		$query	=	$this->db->where($col, $val);
		$query	=	$this->db->update($table, $data);
		if(!$query){
			return FALSE;
		}

		return TRUE;
	}
	
	public function get_by_id($table=NULL,$col=NULL,$value=NULL,$limit=null,$order=NULL){

		if($table==NULL OR $col==NULL OR $value==NULL){

			log_message('error','Helper_model! get_by_id > Missing table, col, or value');
			return FALSE;

		}

		if(!empty($order)){
			
			foreach ($order as $key => $value){

				$query=$this->db->order_by($key, $value);	
			
			}

		}

		$query =	$this->db->get_where($table, array($col => $value),$limit);
		
		if($query->num_rows()>0){
				
			return $query->result();
			
		}else{
			
			return FALSE;
			
		}
	}
	
	public function get_multi_where($table=NULL,$where=NULL,$limit=null,$count=FALSE,$order=NULL,$group=NULL){
		
		if($table==NULL OR	$where==NULL){
			log_message('error','Helper_model! > get_multi_where : Missing table or where argument')
		}
		if(!empty($group)){
			$this->db->group_by($group);
		}
		
		
		if(!empty($order)){
			
			foreach ($order as $key => $value){
			$query=$this->db->order_by($key, $value);	
			}
		
		}else{
		//$query	=	$this->db->order_by('id','desc');
		}
		
		
		
		$query =	$this->db->get_where($table	, $where	,	$limit);
		
		if($query->num_rows()>0){
				//	If just count is needed
				if($count){

					
					return $query->num_rows();
				
				}else{
				//	Or return full result
					return $query->result();
				
				}
				
			}else{
				
				return FALSE;
			}
	}
	
	
	public function get_row_array($table,$where,$order=NULL,$limit=1){
		
			$query =	$this->db->get_where($table, $where,$limit);

			if($query->num_rows()>0){
			
				$row = $query->row();
			
				if(isset($row)){
			
					return $row;	
			
				}
			}else{
			
				return FALSE;
			
			}
			
	}
	
	
	public function get_row($table,$col,$value,$col_get=NULL){
			if(!$table OR !$col OR !$value){
			return FALSE;	
			}
			
			
			$query =	$this->db->get_where($table, array($col => $value),1);
			
			if($query->num_rows()>0){
					$data=$query->row_array();
					if($col_get){
						return $data[$col_get];
						}else{
						return $data;
						}
					
				}else{
					return FALSE;
				}
	}
	
	public function get_table($table,$order=NULL,$group=NULL,$limit=NULL){
	/*
			$this->helper_model->get_table('user',array('id'=>'asc'));
			will produce
			SELECT  * FROM `USER` ORDER BY `ID` ASC;
	*/
		if(!empty($order)){
			
			foreach ($order as $key => $value){
			$query=$this->db->order_by($key, $value);	
			}
		
		}
		
		if(!empty($group)){
			$this->db->group_by($group);
		}
		
		if(!empty($limit)){
			$this->db->limit($limit);
		}
		
		$query = $this->db->get($table);
		if($query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}	
	}
	
	public function count_data($table,$condition=NULL,$value=NULL){
		if($condition!=NULL && $value!=NULL){
				$query = $this->db->get_where($table, array($condition => $value));
				if($query){
						echo $query->num_rows();
				}else{
						return FALSE;
				}	
		}else{
			$query = $this->db->get($table);
			if($query){
				echo $query->num_rows();
				}
		}
	
			
	}
	
	public function count_multiple($table,$condition=NULL){
	if($condition!=NULL){
				$query = $this->db->get_where($table,$condition);
				if($query){
						return $query->num_rows();
				}else{
						return FALSE;
				}	
		}else{
			$query = $this->db->get($table);
			if($query){
				return $query->num_rows();
				}
		}	
	
	}
	
	public function search($table,$query,$attribute,$condition=NULL){
		$this->db->from($table);
		foreach($attribute as $key=>$values){
			$this->db->or_like($values,$query);
		}
		if($condition!=NULL){
			/*
			Suggest something here
			*/
		}
		$query	=	$this->db->get();
		if($query){
			return $query->result();
		}
	}
	
	

	
}