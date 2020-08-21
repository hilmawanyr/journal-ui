<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('curl');
		$this->load->driver('cache', ['adapter' => 'memcached']);
	}

	public function detail(string $orcid) : void
	{
		if (!$this->cache->memcached->get($orcid)) {
			$fetch_data = $this->_get_summary($orcid);
			$this->cache->memcached->save($orcid, $fetch_data, 300);
		}
		$data['summary'] = $this->cache->memcached->get($orcid);
		$data['page'] = "profile_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Get author summary
	 * 
	 * @param string $orcid
	 * @return array
	 */	
	protected function _get_summary(string $orcid) : array
	{
		$summary = $this->_hit_host($orcid);

		$response['uri_orcid'] = $summary->{'orcid-identifier'}->uri;
		$response['orcid_id'] = $summary->{'orcid-identifier'}->path;
		$response['given_name'] = $summary->person->name->{'given-names'}->value;
		$response['fam_name'] = $summary->person->name->{'family-name'}->value;
		$response['biography'] = is_null($summary->person->biography) ? $summary->person->biography : '-';
		$response['ext_identifier'] = count($summary->person->{'external-identifiers'}->{'external-identifier'}) != 0 
										? $this->_resolve_external_id($summary
																		->person
																		->{'external-identifiers'}
																		->{'external-identifier'}
																	)
										: '';
		$response['education'] = count($summary->{'activities-summary'}->educations->{'affiliation-group'}) != 0
									? $this->_resolve_education($summary
																	->{'activities-summary'}
																	->educations
																	->{'affiliation-group'}
																)
									: '';
		$response['employment'] = count($summary->{'activities-summary'}->employments->{'affiliation-group'}) != 0
									? $this->_resolve_employment($summary
																	->{'activities-summary'}
																	->employments
																	->{'affiliation-group'}
																)
									: '';
		$response['works'] = count($summary->{'activities-summary'}->works->group) != 0
								? $this->_resolve_works($summary->{'activities-summary'}->works->group)
								: '';
		$response['number_of_works'] = empty($response['works']) ? 0 : count($response['works']);
		$response['emails'] = count($summary->person->emails->email) != 0 ? $summary->person->emails->email : '';
		return $response;
	}

	/**
	 * Hit endpoint to get summary
	 * 
	 * @param string $orcid
	 * @return array
	 */
	protected function _hit_host(string $orcid) : object
	{
		$host = ORCID_HOST.$orcid;
		$header = [
			'Accept: application/json',
			ORCID_BEARER_TOKEN
		];
		$exec = $this->curl->exec($host, $header);
		$result = json_decode($exec);
		return $result;
	}

	/**
	 * Resolve for External Identifier response
	 * 
	 * @param array $eid
	 * @return array
	 */
	protected function _resolve_external_id(array $eid) : array
	{
		foreach ($eid as $val) {
			$res[] = [
				'exteral_id_type' => $val->{'external-id-type'},
				'exteral_id_value' => $val->{'external-id-value'}
			];
		}
		return $res;
	}

	/**
	 * Resolve for Education response
	 * 
	 * @param array $aff_group
	 * @return array
	 */
	protected function _resolve_education(array $aff_group) : array
	{
		foreach ($aff_group as $key => $val) {
			if (count($val->summaries) != 0 ) {
				foreach ($val->summaries as $value) {
					$response[] = [
						'dept_name' => $value->{'education-summary'}->{'department-name'},
						'org_name' => $value->{'education-summary'}->organization->name
					];
				}
			} else {
				$response = [];
			}
		}
		return $response;
		
	}

	/**
	 * Resolve for Employment response
	 * 
	 * @param array $aff_group
	 * @return array
	 */
	protected function _resolve_employment(array $aff_group) : array
	{
		foreach ($aff_group as $key => $val) {
			if (count($val->summaries) != 0 ) {
				foreach ($val->summaries as $value) {
					$response[] = [
						'dept_name' => $value->{'employment-summary'}->{'department-name'},
						'org_name' => $value->{'employment-summary'}->organization->name
					];
				}
			} else {
				$response = [];
			}
		}
		return $response;
	}

	/**
	 * Resolve foe Works response
	 * 
	 * @param array $works
	 * @return array
	 */
	protected function _resolve_works(array $works) : array
	{
		foreach ($works as $key => $value) {
			
			foreach ($value->{'work-summary'} as $_key => $val) {

				foreach ($val->{'external-ids'}->{'external-id'} as $__key => $vals) {
					$ids[] = [
						'external_id_type' => $vals->{'external-id-type'},
						'external_id_value' => $vals->{'external-id-value'}
					];
				}

				$response[] = [
					'url' => isset($val->url->value) ? $val->url->value : '-',
					'type' => $val->type,
					'pub_date' => [
						!is_null($val->{'publication-date'}->year) 
							? $val->{'publication-date'}->year->value 
							: '',
						!is_null($val->{'publication-date'}->month) 
							? $val->{'publication-date'}->month->value 
							: '',
						!is_null($val->{'publication-date'}->day) 
							? $val->{'publication-date'}->day->value 
							: ''
					],
					'title' => isset($val->{'journal-title'}->value) ? $val->{'journal-title'}->value : '(untitled)',
					'ids' => $ids
				];
			}
		}
		return $response;
	}

}

/* End of file Profile.php */
/* Location: ./application/modules/profile/controllers/Profile.php */