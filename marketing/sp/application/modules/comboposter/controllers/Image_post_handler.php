<?php 

/**
 * 		
 */
class Image_post_handler
{
	private $comboposter;
	
	function __construct($comboposter_handler)
	{
		$this->comboposter = $comboposter_handler;
	}


	public function create()
	{
		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		$data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		$data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'image';
		$data['post_action'] = 'add';
		$data["time_zone"] = $this->comboposter->_time_zone_list();

		$data['page_title'] = $this->comboposter->lang->line('Image post');
		$data['title'] = $this->comboposter->lang->line('Image post');
		$data['body'] = 'posts/image';

		$this->comboposter->_viewcontroller($data);
	}


	public function edit($table_id)
	{	
		/* get campaign info */
		$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id)));

		if (count($campaign_info) == 0) {
			redirect(base_url('404'),'refresh');
		}

		$data['campaigns_social_media'] = json_decode($campaign_info[0]['posting_medium'], true);
		unset($campaign_info[0]['posting_medium']);
		$data['campaign_form_info'] = $campaign_info[0];



		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		$data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		$data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'image';
		$data['post_action'] = 'edit';
		$data["time_zone"] = $this->comboposter->_time_zone_list();

		$data['page_title'] = $this->comboposter->lang->line('Image post');
		$data['title'] = $this->comboposter->lang->line('Image post');
		$data['body'] = 'posts/image';

		$this->comboposter->_viewcontroller($data);
	}


	public function clone_campaign($table_id)
	{	
		/* get campaign info */
		$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id)));

		if (count($campaign_info) == 0) {
			redirect(base_url('404'),'refresh');
		}

		$data['campaigns_social_media'] = json_decode($campaign_info[0]['posting_medium'], true);
		unset($campaign_info[0]['posting_medium']);
		$data['campaign_form_info'] = $campaign_info[0];



		/* get each social accounts list */
		$data['facebook_account_list'] = $this->comboposter->getUserAccountsList('facebook', $this->comboposter->user_id);
		$data['twitter_account_list'] = $this->comboposter->getUserAccountsList('twitter', $this->comboposter->user_id);
		$data['pinterest_account_list'] = $this->comboposter->getUserAccountsList('pinterest', $this->comboposter->user_id);
		$data['wordpress_account_list'] = $this->comboposter->getUserAccountsList('wordpress', $this->comboposter->user_id);
		$data['blogger_account_list'] = $this->comboposter->getUserAccountsList('blogger', $this->comboposter->user_id);
		

		// echo "<pre>";print_r($data['blogger_account_list']);exit;

		$data['post_type'] = 'image';
		$data['post_action'] = 'clone';
		$data["time_zone"] = $this->comboposter->_time_zone_list();

		$data['page_title'] = $this->comboposter->lang->line('Image post');
		$data['title'] = $this->comboposter->lang->line('Image post');
		$data['body'] = 'posts/image';

		$this->comboposter->_viewcontroller($data);
	}


	public function add()
	{

		$processed_input_data = $this->prepare_input_data();

		if (!is_array($processed_input_data)) {
			echo $processed_input_data;
		} else {

			$response = array();

			$posting_mediums_count = $processed_input_data['posting_mediums_count'];
			unset($processed_input_data['posting_mediums_count']);

			// ************************************************//
			$status = $this->comboposter->_check_usage($module_id = 111, $request = $posting_mediums_count);
			if ($status == "2") {

			    $response['status'] = 'error';
			    $response['message'] = $this->comboposter->lang->line("Sorry, your posting bulk limit has exceed.");
			    echo json_encode($response);
			    exit();
			} else if ($status == "3") {

			    $response['status'] = 'error';
			    $response['message'] = $this->comboposter->lang->line("Sorry, your monthly posting limit has exceed.");
			    echo json_encode($response);
			    exit();
			}
			// ************************************************//
			
			$this->comboposter->basic->insert_data('comboposter_campaigns', $processed_input_data);
			$this->comboposter->_insert_usage_log($module_id = 111, $request = $posting_mediums_count);

			if ($this->comboposter->db->affected_rows() > 0) {

				$response['status'] = 'success';

				if ($processed_input_data['schedule_type'] == 'now') {

					$table_id = $this->comboposter->db->insert_id();
					$this->comboposter->single_campaign_post_to_all_media($table_id);
					$response['message'] = $this->comboposter->lang->line("Campaign created & posted successfully.");
				}

				$response['message'] = $this->comboposter->lang->line("Campaign created successfully.");

				echo json_encode($response);
			} else {

				$response['status'] = 'error';
				$response['message'] = $this->comboposter->lang->line("Something went wrong.");

				echo json_encode($response);
			}
		}
	}


	public function edit_action()
	{
		$processed_input_data = $this->prepare_input_data();

		$table_id = $this->comboposter->input->post('table_id', true);

		if (!is_array($processed_input_data)) {
			echo $processed_input_data;
		} else {

			$response = array();

			$campaign_info = $this->comboposter->basic->get_data('comboposter_campaigns', array('where' => array('user_id' => $this->comboposter->user_id, 'id' => $table_id, 'posting_status' => 'pending')), array('posting_medium'));

			if (count($campaign_info) > 0) {

				/* remove usage log  */
				$posting_mediums_count = json_decode($campaign_info[0]['posting_medium'], true);
				$posting_mediums_count = count($posting_mediums_count);
				$this->comboposter->_delete_usage_log($module_id = 111, $request = $posting_mediums_count);


				/* add usage log & update */
				$posting_mediums_count = $processed_input_data['posting_mediums_count'];
				unset($processed_input_data['posting_mediums_count']);

				$this->comboposter->basic->update_data('comboposter_campaigns', array('user_id' => $this->comboposter->user_id, 'id' => $table_id), $processed_input_data);
				$this->comboposter->_insert_usage_log($module_id = 111, $request = $posting_mediums_count);


				$response['status'] = 'success';
				$response['message'] = $this->comboposter->lang->line("Campaign edited successfully.");
			} else {

				$response['status'] = 'error';
				$response['message'] = $this->comboposter->lang->line("Something went wrong.");
			}

			echo json_encode($response);
			
		}
	}


	public function prepare_input_data()
	{
		$response = array();

		/* get form inputs */
		$data['user_id'] = $this->comboposter->user_id;

		$data['campaign_type'] = 'image';
		$data['campaign_name'] = $this->comboposter->input->post('campaign_name', true);
		$data['image_url'] = $this->comboposter->input->post('image_url', true);
		if ($data['campaign_name'] == '' || $data['image_url'] == '') {

			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Campaign name / Image url cann't be empty.");
			return json_encode($response);
		}

		$data['message'] = $this->comboposter->input->post('message', true);
		$data['title'] = $this->comboposter->input->post('title', true);
		$data['rich_content'] = htmlspecialchars($this->comboposter->input->post('rich_content'));

		$data['schedule_type'] = $this->comboposter->input->post('schedule_type', true);
		if ($data['schedule_type'] == '') {
			$data['schedule_type'] = 'later';
		} 

		$data['schedule_timezone'] = $this->comboposter->input->post('time_zone', true);
		$data['schedule_time'] = $this->comboposter->input->post('schedule_time', true);
		if ($data['schedule_type'] == 'now') {
			$data['schedule_time'] = date("Y-m-d h:i:s");
		}


		/* get social media info */
		$facebook_pages = $this->comboposter->input->post('facebook_pages', true);
		$twitter_accounts = $this->comboposter->input->post('twitter_accounts', true);
		$pinterest_boards = $this->comboposter->input->post('pinterest_boards', true);
		$wordpress_accounts = $this->comboposter->input->post('wordpress_accounts', true);
		$blogger_blogs = $this->comboposter->input->post('blogger_blogs', true);


		/* check if is empty */
		if (count($facebook_pages) == 0 
			&& count($twitter_accounts) == 0 
			&& count($pinterest_boards) == 0 
			&& count($wordpress_accounts) == 0 
			&& count($blogger_blogs) == 0) {

			$response['status'] = 'error';
			$response['message'] = $this->comboposter->lang->line("Please make sure that at least one social media is selected.");

			return json_encode($response);
		}


		/* get all social media in an array and process it */
		$posting_mediums = array();

		array_push($posting_mediums, $facebook_pages);
		array_push($posting_mediums, $twitter_accounts);
		array_push($posting_mediums, $pinterest_boards);
		array_push($posting_mediums, $wordpress_accounts);
		array_push($posting_mediums, $blogger_blogs);

		$posting_mediums = array_filter($posting_mediums, function ($element) {
			return count($element) > 0 ? true : false;
		});

		$posting_mediums = $this->comboposter->mutiArrToSingleArr($posting_mediums);
		$data['posting_mediums_count'] = count($posting_mediums);
		$data['posting_medium'] = json_encode($posting_mediums);

		return $data;
	}
}