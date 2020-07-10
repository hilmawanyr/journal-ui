<?php 

	function PopulateForm()
	{
		$CI = &get_instance();
		$post = array();
		foreach (array_keys($_POST) as $key) {
			$post[$key] = $CI->input->post($key);
		}
		return $post;
	}

	function dd($data, $die=0)
	{
		echo "<pre>";
		var_dump ($data);
		echo "</pre>";
		$die === 0 ? exit() : '';
	}