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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['users']['GET'] = 'UserController/index';
$route['users/(:num)']['GET'] = 'UserController/show/$1';
$route['users']['POST'] = 'UserController/store';
$route['users/(:num)']['POST'] = 'UserController/update/$1';
$route['users/(:num)']['DELETE'] = 'UserController/delete/$1';
$route['users/login']['POST'] = 'UserController/login';
$route['users/logout']['POST'] = 'UserController/logout';

$route['access/check']['POST'] = 'AccessController/check';

$route['roles']['GET'] = 'RoleController/index';
$route['email/send-notification-email']['POST'] = 'EmailController/SendNotificationEmail';

$route['id-cards']['GET'] = 'IDCardController/index';
$route['id-cards/get-paginated']['GET'] = 'IDCardController/getPaginated';
$route['id-cards/(:num)']['GET'] = 'IDCardController/show/$1';
$route['id-cards']['POST'] = 'IDCardController/store';
$route['id-cards/(:num)']['POST'] = 'IDCardController/update/$1';
$route['id-cards/(:num)']['DELETE'] = 'IDCardController/delete/$1';
$route['id-cards/update-and-merge']['POST'] = 'IDCardController/updateAndMerge';
$route['id-cards/download-template']['POST'] = 'IDCardController/downloadTemplate';