# CI : Codeigniter helper model
 Codeigfniter version 3.x
 PHP version 5.5+

 Author Dheeraj Thedijje
 Thedijje.com
 File is free to use and download
 this includes codeigniter universal helper files

 ## What is CI helper for?
 
 Codeigniter is no doubt really great framework to get your work done with less and less mess. All you have to do is focus on your App and don't re-invent wheel.
 However 
 While working with CI, i felt lots of thing while writing model, i need to repeat again and again
 For example
 ```
 public function some_model(){
		$this->db->from('table_name');
		$this->db->where(array('col'=>'desire_value'));
		$this->db->limit(10);
		$query	=	$this->db->get();
		return $query->result();
 }
 
 ```

 In code written above, all we needed to change was
 - 'table_name'
 - 'where' condition array
 - 'limit' count

 but i had to write code again and again. so what i did here is this.
 ```
 public function get_multiple($table,$condition=NULL,$limit=10){
		$this->db->from($table);
		$this->db->where($condition);
		$this->db->limit($limit);
		$query	=	$this->db->get();
		return $query->result();
 }
 ```

 Now all i have to do is for any table any query, i never had to write this code again and again. just use that as follows

 ```
 $users_list	=	$this->helper_model->get_multiple('users',array('status'=>1),10);

 ```

 I am done, yes its great!!
 ## Getting started

 ### Cloning or Downloading package
 You can start fresh project via just cloning our git repo into your development environment
 Just do 
 ```
 $ git clone https://github.com/Thedijje/CI_helper.git
 ```

 ### Configuring
 You really don't have to digg inside to configure anything, most of settings are pre-configured. You can configure _database_ and _base url_ values right in index.php file, Yeah.. I know its Awesome.
Although, to make your life easier, just add this little piece of code in your `autoload.php`
`$autoload['model'] = array( array( 'Helper_model'=>'lib' ) );`
Or you can also hit this line of code in your constructor of any Controller
`$this->load->model('Helper_model','lib');`
All the following examples are using this reference only. Yea.. it is Awesome.


### Functions list
Following are the functions inside **helper_model** of this package

#### General functions 

#### get_settings
**Call**  
`$this->lib->get_settings($key_name)`

**Arguments**
`(string) $keyname` required which will fetch values from table.

**Requirement**  
Database connection : Yes  
Database table name : `config`  
Database structure : `id(INT)`,`type(VARCHAR)`,`name(VARCHAR)`,`value(VARCHAR)`  
#### upload_file

**Call**  
`$this->lib->upload_file($path,$name,$key=NULL);`  

**Arguments**  
`(string) $path`: Path where files to be saved/moved example : *static/files/uploaded/*  
`(string) $name`: Name of the $_FILE variable from where file to be picked.  
`(index) 	$key` : Index in case of multiple file.  

**Usage**  
```
$this->lib->upload_file('static/upload','logo_image');
```


#### display_alert
**Call**
`$this->lib->display_alert($msg,$type='info',$icon='fa-info-circle');`  

**Arguments**
`(string) $msg`: Message to be displayed.  
`(string) $type` : Bootstrap 3+ Alert class. Options : `danger`,`success`,`info`,`warning`  
`(string) $icon`: FontAwesome icon class. Default `fa-info-circle  `

**Usage**  
```
$this->lib->display_alert('Something went wrong','danger','times-circle');
```

#### redirect_msg
**Call**  
`$this->lib->redirect_msg($msg,$type,$url);`  

**Arguments**  
`(string) $msg` : Message to display on page  
`(string) $type`: Bootstrap contenxt of message. Options : `success`,`danger`,`warning`,`info`  
`(string) $url` : Local project's URL, URL must not be external, and must contain only inner `controller/methods`. Internally   `base_url($url)` is being called, so `url` Helper is required to run this function.  

**Usage**  
```
$this->lib->redirect_msg('Incorrect username or password','danger','user/login');
```

#### alert_message
**Call**  
`$this->lib->alert_message();`  

**Arguments**  
No argument needed  

**Usage**  
Paste the following code anywhere in `view` where result of `redirect_msg` function has to be displayed  

```
$this->lib->alert_message();
```

#### send_formatted_mail
**Call**  
`$this->lib->send_formatted_mail($data,$attachment=NULL);`  

**Arguments**  
`(array) data`: Consist of following values  

- `(string) from` : Valid email address to send email from  
- `(string) to` 	: Valid email address to send mail to  
- `(string) subject` : Valid string for email subject  
- `(string) message` : Text/HTML content to be send in email 

`(string) $attachment`: Upload signle file
`(array) $attachment`	: Upload multiple file

**Usage**  
```
$data['from']	=	'no-reply@example.com';
$data['to']	=	'awesomeuser@example.com';
$data['subject']=	'Welcome to CI HELPER Model';
$data['message']=	$this->load->view('email/hello_teplate',$content,TRUE);

$this->lib->send_formatted_mail($data,$attachment=NULL);
```


#### image_resize
**Call**  
`$this->lib->image_resize($path,500,600)`

**Arguments**  
`(string) $path`	:	Valid path where image exists
`(int) $width`	:	width of new image in pixel
`(int) $height`	:	Height of new image in pixel

*If one of height/width is provided, other will be adjusted keeping ratio same*

**Usage**  
```
$this->lib->image_resize('static/upload/profile_image/user.jpg',250,250);
```





### Database functions

|	Sr	|	Function Name	|	Use syntax	|	Comment	|
|----|---------------|------------|----------|
|	1	|	del 	|	$this->lib->del($table,$col,$val)	|	Delete a record in table using table name, col and value 	|
|	2	|	update 	|	$this->lib->update($table,$data,$col,$val)	|	Update table set dataset where $col is havin $value |
|	3	|	get_by_id	|	$this->lib->get_by_id($table,$col,$val,$limit,$order)	|	Get multiple record by providing single where condition, optionally can sort and linmit the output desire_value|
|	4	|	get_multi_where	|	$this->lib->get_multi_where($table,$conditions,$limit,$count,$order,$group)	|	Get Multiple values by providing multiple condition, optionally can sort, order,get only count of result and group data accordingly	|
|	5	|	get_row_array	|	$this->lib->get_row_array($table,$where,$order,$limit)	|	get single record using multiple condition, order and linmit 	|
|	6	|	get_row 	|	$this->lib->get_row($table,$col,$val,$col_get)	| Get single row in array form if specified col, get single value based on conditions 	|
|	7	|	get_table	|	$this->lib->get_table($table,$order,$group,$limit)	|	Get table, additionally apply multiple sorting, grouping, and limit 	|
|	8	|	count_data	|	$this->lib->count_data($table,$col,$val)	|	Quickly Get num row value based on condition on table 	|
|	9	|	 count_multiple	|	$this->lib->count_multiple($table,$condition)	|	Get num row of table or condition on it 	|
| 10	|	search	|	$this->lib->search($table,$query_text,$attributes)	|	Search from table using query and list of col to search for 	|
|	11	|	upload_file 	|	$this->lib->upload_file($path,$name,$key=NULL)	|	Upload multiple files in the $path specified |
|	12	|	send_formatted_mail 	|	$this->lib->send_formatted_mail($data,$attachment=NULL)	|	Send mail with attachment/attachments |

