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
$route['default_controller'] = 'Welcome';
$route['404_override'] = 'Welcome/not_found';
$route['translate_uri_dashes'] = FALSE;

$route['sign-in'] = 'SystemLogin/signin';
$route['back-office'] = 'AdminMain';
$route['logout'] = 'SystemLogin/logout';
$route['profile'] = 'Users/user_profile';
$route['UpdateProfile'] = 'Users/UpdateProfile';

$route['getAttr'] = 'Products/getAttributes';
$route['saveProducts'] = 'Products/save_products';
$route['add_img_page'] = 'Products/product_img_page';
$route['upload_pro_img'] = 'Products/upload_pro_img';
$route['getSpecProImg'] = 'Products/getSpecProImg';
$route['deleteProImg'] = 'Products/deleteProImg';
$route['changePhotoOrder'] = 'Products/changePhotoOrder';
$route['getProducts'] = 'Products/getProducts';
$route['deleteProduct'] = 'Products/deleteProduct';
$route['edit_product_page'] = 'Products/add_product';
$route['updateProStatus'] = 'Products/updateProStatus';
$route['edit_sub_products'] = 'Products/edit_sub_products';
$route['update_sub_products'] = 'Products/updateSubProducts';
$route['deleteSubProduct'] = 'Products/deleteSubProduct';
$route['convert-to-array'] = 'Products/convertToArray';
$route['getProAttr'] = 'Products/getProAttr';
$route['checkSubPro'] = 'Products/checkSubPro';
$route['updateProQtyPrice'] = 'Products/updateProQtyPrice';
$route['updateUserStatus'] = 'Users/updateUserStatus';
$route['addUser'] = 'Users/addUser';
$route['editUser'] = 'Users/addUser';
$route['checkfields'] = 'Users/checkDBfields';
$route['checkDBfieldOpt'] = 'Users/checkDBfieldOpt';
$route['getRegion'] = 'Users/getRegion';
$route['getCities'] = 'Users/getCities';
$route['saveUser'] = 'Users/saveUser';
$route['deleteUser'] = 'Users/deleteUser';
$route['addAccessGroups'] = 'GroupOptions/addAccessGroups';
$route['deleteGroups'] = 'GroupOptions/deleteAccessGroups';
$route['deleteCategories'] = 'Settings/deleteCategories';
$route['updateCateStatus'] = 'Settings/updateCateStatus';
$route['addCategory'] = 'Settings/addCategory';
$route['uploadSingleImage'] = 'Settings/upload_single_img';
$route['deleteAttribute'] = 'Settings/deleteAttribute';
$route['addAttributes'] = 'Settings/addAttributes';
$route['AttrValues'] = 'Settings/AttrValues';
$route['updateAttrValStatus'] = 'Settings/updateAttrValStatus';
$route['deleteAttributeVal'] = 'Settings/deleteAttributeVal';
$route['addAttributeVal'] = 'Settings/addAttributeVal';
$route['addAttributeVal_prod'] = 'Settings/addAttributeVal_prod';
$route['cateAssignAttr'] = 'Settings/cateAssignAttr';
$route['removeAssignAttr'] = 'Settings/removeAssignAttr';
$route['getAttributes'] = 'Settings/getAttributes';
$route['assignCateAttr'] = 'Settings/assignCateAttr';
$route['uploadBrandImage'] = 'Settings/upload_brand_img';
$route['addBrand'] = 'Settings/addBrand';
$route['deleteBrand'] = 'Settings/deleteBrand';
$route['cateAssignBrands'] = 'Settings/cateAssignBrands';
$route['getBrands'] = 'Settings/getBrands';
$route['assignCateBrand'] = 'Settings/assignCateBrand';
$route['removeAssignBrand'] = 'Settings/removeAssignBrand';
$route['addDelCharges'] = 'Settings/addDelCharges';
$route['getDelCharges'] = 'Settings/getDelCharges';
$route['deleteDelCharge'] = 'Settings/deleteDelCharge';
$route['add_page'] = 'Settings/add_page';
$route['savePage'] = 'Settings/save_page';
$route['page_img_page'] = 'Settings/page_img_page';
$route['getSpecPageImg'] = 'Settings/getSpecPageImg';
$route['upload_page_img'] = 'Settings/upload_page_img';
$route['deletePageImg'] = 'Settings/deletePageImg';

$route['getOrders'] = 'Orders/getOrders';
$route['view_order'] = 'Orders/view_order';
$route['delete_order'] = 'Orders/deleteOrder';
$route['getOrderStatus'] = 'Orders/getOrderStatus';
$route['updateOrderStatus'] = 'Orders/updateOrderStatus';
$route['updateOrderPayment'] = 'Orders/updateOrderPayment';
$route['updateOrderAddr'] = 'Orders/updateOrderAddr';


$route['updateCustomerStatus'] = 'Customers/updateCustomerStatus';
$route['addCustomer'] = 'Customers/addCustomer';
$route['editCustomer'] = 'Customers/addCustomer';
$route['saveCustomer'] = 'Customers/saveCustomer';
$route['deleteCustomer'] = 'Customers/deleteCustomer';
$route['sendMail'] = 'Customers/send_Mail';
$route['downloadData'] = 'Customers/downloadData';

$route['saveCurRate'] = 'OtherOptions/saveCurRate';
$route['deleteCurRate'] = 'OtherOptions/deleteCurRate';
$route['updateCurStatus'] = 'OtherOptions/updateCurStatus';
$route['updateRateType'] = 'OtherOptions/updateRateType';

$route['getCoupons'] = 'OtherOptions/getCoupons';
$route['addCoupon'] = 'OtherOptions/saveCoupon';
$route['updateCouponsStatus'] = 'OtherOptions/updateCouponsStatus';
$route['deleteCoupons'] = 'OtherOptions/deleteCoupons';