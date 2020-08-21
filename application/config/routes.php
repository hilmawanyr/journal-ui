<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'landing';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['search'] = 'landing/search/query';
$route['article/(:any)/detail'] = 'landing/search/modal_detail/$1';
$route['filter_search'] = 'landing/search/fiter_query';

$route['mail/(:any)'] = 'mail/$1';
$route['send_mail'] = 'mail/send';
$route['save_template'] = 'mail/save_template';
$route['template_list'] = 'mail/template_list';
$route['get_template/(:any)'] = 'mail/get_detail_template/$1';

$route['xml_export/(:any)'] = 'landing/export/export_xml/$1';
$route['csv_export/(:any)'] = 'landing/export/export_csv/$1';
$route['export_all_xml/(:any)'] = 'landing/export/export_all_xml/$1';
$route['export_all_csv/(:any)'] = 'landing/export/export_all_csv/$1';

$route['auth'] = 'auth';
$route['attemp_login'] = 'auth/attemp_login';
$route['logout'] = 'auth/logout';
$route['redirect_auth/(:any)'] = 'auth/redirect_auth/$1';

$route['invite'] = 'mail/invite';
$route['invitation'] = 'invite';

$route['profile/(:any)'] = 'profile/detail/$1';