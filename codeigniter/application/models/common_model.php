<?php
class common_model extends CI_Model {

	private $table_name;
	public function __construct($table_name = '')
	{
		parent::__construct();
		$this->table_name = $table_name;
	}

	function Count($data)
	{
		foreach ($data as $column => $value) {
			$this->db->where($column, $value);
		}
		$this->db->from($this->table_name);
		return $this->db->count_all_results();
	}

	function Select()
	{
		$query = $this->db->get($this->table_name);
		return $query->result_array();
	}

	function Find($data, $limit = 'All', $orderby = '', $result_type=0)
	{
		foreach ($data as $column => $value) {
			$this->db->where($column, $value);
		}

		if ($limit != 'All') {
			$this->db->limit( $limit );
		}

		if ($orderby != '') {
			$this->db->order_by($orderby['column'], $orderby['order']); 
		}

		$query = $this->db->get($this->table_name);
		if ($query->num_rows()) {
			return ($limit == 1 && $result_type==0) ? $query->row_array() : $query->result_array();
		}
		return array();
	}

	function FindById($id)
	{
		return $this->Find(array('id' => $id), 1);
	}

	function FindBySlug($column, $slug){
		return $this->Find(array($column => $slug), 1);
	}

	function FindAll($data = array())
	{
		return $this->Find($data);
	}

	function Save($mode, $data, $where = array())
	{
		 
		if ($mode == 'add') {
			$data['created_at'] = date('Y-m-d H:i:s');//gmt_zone();
                        $data['updated_at'] = date('Y-m-d H:i:s');//gmt_zone();
			$this->db->insert($this->table_name, $data);

			return ($this->db->affected_rows()) ? $this->FindById($this->db->insert_id()) : NULL;
		} else {
			$update = false;
			$this->db->set($data);
			if (array_key_exists('id', $data)) {
				$update = true;
				$this->db->where(array('id' => $data['id']));
			}
			if (is_array($where) && count($where)) {
				$update = true;
				foreach ($where as $column => $value) {
					$this->db->where($column, $value);
				}
			}
			if ($update) {
                                $data['updated_at'] = date('Y-m-d H:i:s');//gmt_zone();
				$this->db->update($this->table_name);
			}

			return $this->db->affected_rows();
		}
	}

	function Delete($data)
	{
		foreach ($data as $column => $value) {
			$this->db->where($column, $value);
		}
		$this->db->delete($this->table_name);

		return $this->db->affected_rows();
	}

	function get_slug($params)
	{
		$check_slug = str_replace('&', 'and', $params['slug']);
		if (array_key_exists('lower_case', $params) && $params['lower_case'] == 'no') {

		} else {
			$check_slug = strtolower(trim($check_slug));
		}

		$check_slug = preg_replace('/[^\p{L}\p{N}]/u', ' ', trim($check_slug));
		$check_slug = preg_replace('/\s\s+/', ' ', $check_slug);
		$check_slug = str_replace(' ', '-', $check_slug);

		$Slug = $this->find_unique_slug($check_slug, $params);
		if (count($Slug) > 0) {
			$slug = $Slug[$params['field']];
			if (strlen($slug) > strlen($check_slug)) {
				$newNumber = substr($slug, strlen($check_slug) + 1) + 1;
				$slug = $check_slug . '-' . $newNumber;
			} else {
				$slug .= '-1';
			}
		} else {
			$slug = $check_slug;
		}
		return $slug;
	}
	
	function find_unique_slug($slug, $params)
	{
		$this->db->select($params['field']);
		$this->db->where($params['field'] . " RLIKE'(^".$slug."(-[0-9]+)?$)'");

		if (array_key_exists('additional_field_name', $params) && array_key_exists('additional_field_value', $params)) {
			$this->db->where(array($params['additional_field_name'] => $params['additional_field_value']));
		}

		$this->db->order_by($params['unique_field'], 'DESC');
		$this->db->limit(1);
		$query = $this->db->get($params['table']);
		return ($query->num_rows() > 0) ?  $query->row_array() : NULL;
	}

	function remove_directory($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (filetype($dir . '/' . $object) == 'dir') {
						$this->remove_directory($dir . '/' . $object);
					} else {
						unlink($dir . '/' . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	function resizeImageHandler($handler, $data, $productData = array() )
	{
		require_once(APPPATH . 'third_party/SimpleImage.class.php');
		$error = '';

		if (isset($data['check_file_size']) && $data['check_file_size']) {
			if (! array_key_exists('file_size', $data)) {
				$data['file_size'] = strlen(file_get_contents($data['upload_data']['full_path']));//filesize($data['upload_data']['full_path']);
			}
			if (($data['file_size'] / 1024) > $data['max_file_size']) {
				$error = 'File Size should not exceed ' . $data['max_file_size'] / 1024 . ' mb. current size is ' . $data['file_size'] / 1024;
			}
		}

		if (! $error) {
			if (!array_key_exists('filename', $data)) {
				$data['filename'] = $data['upload_data']['raw_name'];
			}

			if ($handler == 'user_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . USER_ORIGINAL_IMAGE_SUFFIX . '.jpg', USER_ORIGINAL_IMAGE_WIDTH, USER_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . USER_SCREEN_IMAGE_SUFFIX . '.jpg', USER_SCREEN_IMAGE_WIDTH, USER_SCREEN_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . USER_THUMB_IMAGE_SUFFIX . '.jpg', USER_THUMB_IMAGE_WIDTH, USER_THUMB_IMAGE_HEIGHT, true);
			} elseif ($handler == 'article_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ARTICLE_ORIGINAL_IMAGE_SUFFIX . '.jpg', ARTICLE_ORIGINAL_IMAGE_WIDTH, ARTICLE_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ARTICLE_SCREEN_IMAGE_SUFFIX . '.jpg', ARTICLE_SCREEN_IMAGE_WIDTH, ARTICLE_SCREEN_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ARTICLE_THUMB_IMAGE_SUFFIX . '.jpg', ARTICLE_THUMB_IMAGE_WIDTH, ARTICLE_THUMB_IMAGE_HEIGHT, true);
			} elseif ($handler == 'contest_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . CONTEST_ORIGINAL_IMAGE_SUFFIX . '.jpg', CONTEST_ORIGINAL_IMAGE_WIDTH, CONTEST_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . CONTEST_SCREEN_IMAGE_SUFFIX . '.jpg', CONTEST_SCREEN_IMAGE_WIDTH, CONTEST_SCREEN_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . CONTEST_THUMB_IMAGE_SUFFIX . '.jpg', CONTEST_THUMB_IMAGE_WIDTH, CONTEST_THUMB_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . CONTEST_THUMB_IMAGE_O_SUFFIX . '.jpg', CONTEST_THUMB_IMAGE_O_WIDTH, CONTEST_THUMB_IMAGE_O_HEIGHT, true);
			} elseif ($handler == 'product_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . PRODUCT_ORIGINAL_IMAGE_SUFFIX . '.jpg', PRODUCT_ORIGINAL_IMAGE_WIDTH, PRODUCT_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . PRODUCT_SCREEN_IMAGE_SUFFIX . '.jpg', PRODUCT_SCREEN_IMAGE_WIDTH, PRODUCT_SCREEN_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . PRODUCT_THUMB_IMAGE_SUFFIX . '.jpg', PRODUCT_THUMB_IMAGE_WIDTH, PRODUCT_THUMB_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . PRODUCT_THUMB_IMAGE_O_SUFFIX . '.jpg', PRODUCT_THUMB_IMAGE_O_WIDTH, PRODUCT_THUMB_IMAGE_O_HEIGHT, true);
			} elseif ($handler == 'about_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ABOUT_ORIGINAL_IMAGE_SUFFIX . '.jpg', ABOUT_ORIGINAL_IMAGE_WIDTH, ABOUT_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ABOUT_SCREEN_IMAGE_SUFFIX . '.jpg', ABOUT_SCREEN_IMAGE_WIDTH, ABOUT_SCREEN_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . ABOUT_THUMB_IMAGE_SUFFIX . '.jpg', ABOUT_THUMB_IMAGE_WIDTH, ABOUT_THUMB_IMAGE_HEIGHT, true);
			} elseif ($handler == 'home_image') {
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . HOME_ORIGINAL_IMAGE_SUFFIX . $data['extension'], HOME_ORIGINAL_IMAGE_WIDTH, HOME_ORIGINAL_IMAGE_HEIGHT, true);
				$this->resizeImage($data['upload_data']['full_path'], $data['path']. $data['filename'] . HOME_THUMB_IMAGE_SUFFIX . $data['extension'], HOME_THUMB_IMAGE_WIDTH, HOME_THUMB_IMAGE_HEIGHT, true);
			}

			if (isset($data['delete_original_image']) && $data['delete_original_image']) {
				$this->deleteFile($data['upload_data']['full_path']);
			}
		}

		return array($data['filename'], $error);
	}
	
	function resizeImage($source_image, $target_image, $width = 0, $height = 0, $best_fit = false)
	{
		$SimpleImage = new SimpleImage($source_image);
		
		if ($best_fit) {
			$SimpleImage->best_fit($width, $height)->save($target_image, 100);
		} else {
			if (!$width && !$height) {
				$SimpleImage->save($target_image);
			} elseif ($SimpleImage->get_width() >= $width) {
				$SimpleImage->fit_to_width($width)->crop(0, 0, $width, (($SimpleImage->get_height() > $height) ? $height : $SimpleImage->get_height()))->save($target_image);
			} elseif ($SimpleImage->get_height() >= $height) {
				$SimpleImage->fit_to_width($height)->crop(0, 0, (($SimpleImage->get_width() > $width) ? $width : $SimpleImage->get_width()), $height)->save($target_image);
			} else {
				$SimpleImage->best_fit($width, $height)->save($target_image);
			}
		}
	}

	function deleteFileHandler($handler, $filename)
	{
		if ($handler == 'profile') {
			$this->deleteFile(ROOT_PATH . 'public/images/profiles_pictures/large/' . $filename);
			$this->deleteFile(ROOT_PATH . 'public/images/profiles_pictures/medium/' . $filename);
			$this->deleteFile(ROOT_PATH . 'public/images/profiles_pictures/small/' . $filename);
		}
	}

	function deleteFile($full_file)
	{
		if (file_exists($full_file)) {
			unlink($full_file);
		}
	}

	public function is_valid_url($url)
	{
		echo $url;
		$urlregex = "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$";
		return eregi($urlregex, $url); 
	}

	public function make_folders($path_prefix, $path)
	{
		foreach($path as $folder) {
			$path_prefix .= '/' . $folder;
			if (!file_exists($path_prefix) || !is_dir($path_prefix)) {
				mkdir($path_prefix);
			}
		}
	}

	public function SetCookie($name, $value)
	{
		$this->input->set_cookie(array(
			'name'   => COOKIE_PREFIX . $name,
			'value'  => $value,
			'expire' =>  + 24 * 3600,
			'path'   => '/'	));
	}

	public function DeleteCookie($name)
	{
		$this->input->set_cookie(array(
			'name'   => COOKIE_PREFIX . $name,
			'value'  => '',
			'expire' => time() - 31556926,
			'path'   => '/'	));
	}

	public function resize($filename, $width, $height) {
		require_once(APPPATH . 'third_party/image.php');
		require_once(APPPATH . 'helpers/utf8_helper.php');

		if (!file_exists(DIR_IMAGE_OC . $filename) || !is_file(DIR_IMAGE_OC . $filename)) {//echo 'in if' . DIR_IMAGE_OC . $filename;
			return;
		} 
		
		$info = pathinfo($filename);
		
		$extension = $info['extension'];
		
		$old_image = $filename;
		$new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		if (!file_exists(DIR_IMAGE_OC . $new_image) || (filemtime(DIR_IMAGE_OC . $old_image) > filemtime(DIR_IMAGE_OC . $new_image))) {
			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!file_exists(DIR_IMAGE_OC . $path)) {
					@mkdir(DIR_IMAGE_OC . $path, 0777);
				}		
			}
			
			$image = new Image(DIR_IMAGE_OC . $old_image);
			$image->resize($width, $height);
			$image->save(DIR_IMAGE_OC . $new_image);
		}
	
//		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
//			return HTTPS_CATALOG . 'image/' . $new_image;
//		} else {
			return SITE_URL . 'store/image/' . $new_image;
//		}
	}

	function getOrders()
	{
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, 
			(
				SELECT os.name FROM " . DB_PREFIX . "order_status os 
				WHERE os.order_status_id = o.order_status_id AND os.language_id = '1'
			) AS status, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified 
			FROM `" . DB_PREFIX . "order` o
			WHERE o.order_status_id > '0'
			ORDER BY o.date_added DESC 
			LIMIT 0, " .DASHBOARD_LISTING;

		$query = $this->db->query($sql);
		$Orders = array();
		if ($query->num_rows()) {
			foreach ($query->result_array() as $result) {
				$Orders[] = array(
					'order_id'   => $result['order_id'],
					'customer'   => $result['customer'],
					'status'     => $result['status'],
					'date_added' => get_date($result['date_added']),
					'total'      => '$' . number_format($result['total'], 2),
				);
			}
		}
		return $Orders;
	}
}
?>