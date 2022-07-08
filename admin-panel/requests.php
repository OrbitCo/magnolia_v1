<?php
require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php');
require_once('function.php');
global $db,$conn;
SessionStart();
use Aws\S3\S3Client;
$f = '';
$s = '';
$p = '';
if (isset($_GET['f'])) {
    $f = Secure($_GET['f'], 0);
}
if (isset($_GET['s'])) {
    $s = Secure($_GET['s'], 0);
}
if (isset($_GET['p'])) {
    $p = Secure($_GET['p'], 0);
}
$hash_id = '';
if (!empty($_POST['hash_id'])) {
    $hash_id = $_POST['hash_id'];
    unset($_POST['hash_id']);
} else if (!empty($_GET['hash_id'])) {
    $hash_id = $_GET['hash_id'];
    unset($_GET['hash_id']);
} else if (!empty($_GET['hash'])) {
    $hash_id = $_GET['hash'];
    unset($_GET['hash']);
} else if (!empty($_POST['hash'])) {
    $hash_id = $_POST['hash'];
    unset($_POST['hash']);
}
$data = array();
header("Content-type: application/json");
if ($f == 'session_status') {
    if (isset( $_SESSION['JWT'])) {
        $data = array(
            'status' => 200
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!isset( $_SESSION['JWT'])) {
    exit("Please login or signup to continue.");
}
if ($s == 'auto_user_like') {
    if (!empty($_GET['users'])) {
        $save = Wo_SaveConfig('auto_user_like', Secure($_GET['users']));
        if ($save) {
            $data['status'] = 200;
        }
    }
    else{
        $save = Wo_SaveConfig('auto_user_like', '');
        if ($save) {
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'delete-app') {
    $data = array(
        'status' => 500
    );
    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
        if (DeleteApp($_GET['id'])) {
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'remove_multi_app') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value) && $value > 0) {
                DeleteApp($value);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($s == 'insert-invitation') {
    $data             = array(
        'status' => 200,
        'html' => ''
    );
    $wo['invitation'] = InsertAdminInvitation();
    if ($wo['invitation'] && is_array($wo['invitation'])) {
        $data['html']   = Wo_LoadAdminPage('manage-invitation-keys/list');
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'rm-invitation' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $data = array(
        'status' => 304
    );
    if (DeleteAdminInvitation('id', $_GET['id'])) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'rm-user-invitation' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $data = array(
        'status' => 304
    );
    if (DeleteUserInvitation('id', $_GET['id'])) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($s == 'remove_multi_invitation') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value) && $value > 0) {
                DeleteUserInvitation('id', $value);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($f == 'update-ads' && (auth()->admin == '1' || CheckUserPermission(auth()->id, $p))) {
    $updated = false;
    foreach ($_POST as $key => $ads) {
        if ($key != 'hash_id') {
            $ad_data = array(
                'code' => htmlspecialchars(base64_decode($ads)),
                'active' => (empty($ads)) ? 0 : 1
            );
            $update = $db->where('placement', Secure($key))->update('site_ads', $ad_data);
            if ($update) {
                $updated = true;
            }
        }
    }
    if ($updated == true) {
        $data = array(
            'status' => 200
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($f == 'get_lang_key' && (auth()->admin == '1' || CheckUserPermission(auth()->id, $p))) {
    $html  = '';
    $langs = Wo_GetLangDetails($_GET['id']);
    if (count($langs) > 0) {
        foreach ($langs as $key => $wo['langs']) {
            foreach ($wo['langs'] as $wo['key_'] => $wo['lang_vlaue']) {
                $wo['is_editale'] = 0;
                if ($_GET['lang_name'] == $wo['key_']) {
                    $wo['is_editale'] = 1;
                }
                $html .= Wo_LoadAdminPage('edit-lang/form-list', false);
            }
        }
    } else {
        $html = "<h4>Keyword not found</h4>";
    }
    $data['status'] = 200;
    $data['html']   = $html;
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($f == 'get_country_lang_key' && (auth()->admin == '1' || CheckUserPermission(auth()->id, $p))) {
    $html  = '';
    $langs = Wo_GetLangDetailsByid($_GET['id'],true);
    if (count($langs) > 0) {
        foreach ($langs as $key => $wo['langs']) {
            foreach ($wo['langs'] as $wo['key_'] => $wo['lang_vlaue']) {
                $wo['is_editale'] = 0;
                if ($_GET['lang_name'] == $wo['key_']) {
                    $wo['is_editale'] = 1;
                }
                if($wo['key_'] === 'options') {
                    $html .= '<div class="form-group" style="margin-bottom: 0px;"><div class="form-lins"><label class="form-lasbel">Country Area Code</label><textarea style="resize: none;" name="options" id="options" class="form-control" cols="20" rows="2" >' . $wo['langs']['options'] . '</textarea></div></div>';
                }else if($wo['key_'] === 'lang_key'){
                    $html  .= '<div class="form-group" style="margin-bottom: 0px;"><div class="form-lins"><label class="form-lasbel">Country Code</label><textarea style="resize: none;" name="lang_key" id="lang_key" class="form-control" cols="20" rows="2" >'.$wo['langs']['lang_key'].'</textarea></div></div>';
                }else {
                    $html .= Wo_LoadAdminPage('edit-countries/form-list', false);
                }
            }
        }
    } else {
        $html = "<h4>Keyword not found</h4>";
    }
    $data['status'] = 200;
    $data['html']   = $html;
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($f == "admin_setting" && (auth()->admin == '1' || CheckUserPermission(auth()->id, $p))) {
    if ($s == 'ReadNotify') {
        $db->where('recipient_id',0)->where('admin',1)->where('seen',0)->update('notifications',array('seen' => time()));
    }
    if ($s == 'search_in_pages') {
        $keyword = Secure($_POST['keyword']);
        $html = '';

        $files = scandir('pages');
        $not_allowed_files = array('edit-custom-page','edit-lang','edit-movie','edit-profile-field','edit-terms-pages'); 
        foreach ($files as $key => $file) {
            if (file_exists('pages/'.$file.'/content.phtml') && !in_array($file, $not_allowed_files)) {
                
                $string = file_get_contents('pages/'.$file.'/content.phtml');
                preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches1);

                if (!empty($matches1) && !empty($matches1[2])) {
                    foreach ($matches1[2] as $key => $title) {
                        if (strpos(strtolower($title), strtolower($keyword)) !== false) {
                            $page_title = '';
                            preg_match_all("@(?s)<h6([^<]*)>([^<]*)<\/h6>@", $string, $matches3);
                            if (!empty($matches3) && !empty($matches3[2])) {
                                foreach ($matches3[2] as $key => $title2) {
                                    $page_title = $title2;
                                    break;
                                }
                            }
                            $html .= '<a href="'.Wo_LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$title.'</small></div></a>';
                            break;
                        }
                    }
                }

                preg_match_all("@(?s)<label([^<]*)>([^<]*)<\/label>@", $string, $matches2);
                if (!empty($matches2) && !empty($matches2[2])) {
                    foreach ($matches2[2] as $key => $lable) {
                        if (strpos(strtolower($lable), strtolower($keyword)) !== false) {
                            $page_title = '';
                            preg_match_all("@(?s)<h6([^<]*)>([^<]*)<\/h6>@", $string, $matches3);
                            if (!empty($matches3) && !empty($matches3[2])) {
                                foreach ($matches3[2] as $key => $title2) {
                                    $page_title = $title2;
                                    break;
                                }
                            }

                            $html .= '<a href="'.Wo_LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$lable.'</small></div></a>';
                            break;
                        }
                    }
                }
            }
        }
        $data = array(
                    'status' => 200,
                    'html'   => $html
                );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();

    }
    if ($s == 'remove_multi_lang') {
        if (!empty($_POST['ids'])) {
            $langs = Wo_LangsNamesFromDB();
            foreach ($_POST['ids'] as $key => $value) {
                if (in_array($value, $langs)) {
                    $lang_name = Secure($value);
                    $query     = mysqli_query($conn, "ALTER TABLE `langs` DROP COLUMN `$lang_name`");
                    if ($query) {
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_page') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value) && $value > 0) {
                     Wo_DeleteCustomPage($value);
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_ban') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                Wo_DeleteBanned(Secure($value));
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_gift') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                Wo_DeleteGift(Secure($value));
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_multi_article') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                $article = $db->where('id',Secure($value))->objectbuilder()->getOne('blog');
                Wo_DeleteArticle($article->id, $article->thumbnail);
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_category') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && in_array($value, array_keys(Dataset::blog_categories()))) {
                    $db->where('lang_key',Secure($value))->delete('langs');
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_sticker') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value)) {
                    Wo_DeleteSticker(Secure($value));
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_field') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value)) {
                    DeleteField($value);
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_country') {
        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $key => $value) {
                if (in_array($value, array_keys(Dataset::countries('id')))) {
                    $db->where('id',Secure($value))->delete('langs');
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_multi_report') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('safe','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    $report = $db->where('id',Secure($value))->getOne('reports');
                    if ($_POST['type'] == 'delete') {
                        Wo_DeleteReport($report['id']);
                    }
                    elseif ($_POST['type'] == 'safe') {
                        Wo_DeleteReport($report['id']);
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_request') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('paid','decline'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value)) {
                    if ($_POST['type'] == 'decline') {
                        $get_payment_info = Wo_GetPaymentHistory(Secure($value));
                        $get_payment_info = ToArray($get_payment_info);
                        if (!empty($get_payment_info)) {
                            $id     = $get_payment_info['id'];
                            $update = mysqli_query($conn, "UPDATE `affiliates_requests` SET status = '2' WHERE id = {$id}");
                            if ($update) {
                                $message_body = Emails::parse('emails/payment-declined', array(
                                    'name' => ($get_payment_info['user'][ 'first_name' ] !== '' ? $get_payment_info['user'][ 'first_name' ] : $get_payment_info['user'][ 'username' ]),
                                    'amount' => $get_payment_info['amount'],
                                    'site_name' => $wo['config']['siteName']
                                ));
                                $send_message_data = array(
                                    'from_email' => $wo['config']['siteEmail'],
                                    'from_name' => $wo['config']['siteName'],
                                    'to_email' => $get_payment_info['user']['email'],
                                    'subject' => 'Payment Declined | ' . $wo['config']['siteName'],
                                    'charSet' => 'utf-8',
                                    'message_body' => $message_body,
                                    'is_html' => true
                                );
                                $send_message      = SendEmail($send_message_data['to_email'], $send_message_data['subject'], $send_message_data['message_body'], false);
                                $data['status'] = 200;

                            }
                        }
                    }
                    elseif ($_POST['type'] == 'paid') {
                        $get_payment_info = Wo_GetPaymentHistory(Secure($value));
                        $get_payment_info = ToArray($get_payment_info);
                        if (!empty($get_payment_info)) {
                            $id     = $get_payment_info['id'];
                            $update = mysqli_query($conn, "UPDATE `affiliates_requests` SET status = '1' WHERE id = {$id}");
                            if ($update) {
                                $message_body = Emails::parse('emails/payment-sent', array(
                                    'name' => ($get_payment_info['user'][ 'first_name' ] !== '' ? $get_payment_info['user'][ 'first_name' ] : $get_payment_info['user'][ 'username' ]),
                                    'amount' => $get_payment_info['amount'],
                                    'site_name' => $wo['config']['siteName']
                                ));
                                $send_message_data = array(
                                    'from_email' => $wo['config']['siteEmail'],
                                    'from_name' => $wo['config']['siteName'],
                                    'to_email' => $get_payment_info['user']['email'],
                                    'to_name' => $get_payment_info['user']['first_name'],
                                    'subject' => 'Payment Declined | ' . $wo['config']['siteName'],
                                    'charSet' => 'utf-8',
                                    'message_body' => $message_body,
                                    'is_html' => true
                                );
                                $send_message      = SendEmail($send_message_data['to_email'], $send_message_data['subject'], $send_message_data['message_body'], false);
                                if ($send_message) {
                                    $data['status'] = 200;
                                }
                            }
                        }
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'remove_multi_verification') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('verify','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value)) {
                    if ($_POST['type'] == 'delete') {
                        $db->where('id',Secure($value))->delete('verification_requests');
                    }
                    elseif ($_POST['type'] == 'verify') {
                        $verify = $db->where('id',Secure($value))->getOne('verification_requests');
                        Wo_VerifyUser(Secure($value), $verify['user_id']);
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_multi_story') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (!empty($value) && is_numeric($value)) {
                    if ($_POST['type'] == 'delete') {
                        Wo_Deletesuccess_stories(Secure($value));
                    }
                    elseif ($_POST['type'] == 'activate') {
                        Wo_Approvesuccess_stories(Secure($value));
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        Wo_DisApprovesuccess_stories(Secure($value));
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_multi_gender') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('enable','disable','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (in_array($value, array_keys(Dataset::gender()))) {
                    if ($_POST['type'] == 'delete') {
                        if((int)$value == 4526 || (int)$value == 4525 ){
                            $data['status'] = 300;
                        }else {
                            $db->where('lang_key',Secure($value))->delete('langs');
                            $data['status'] = 200;
                        }
                    }
                    elseif ($_POST['type'] == 'enable') {
                        $db->where('lang_key',Secure($value))->update('langs', array('options' => 1));
                    }
                    elseif ($_POST['type'] == 'disable') {
                        $db->where('lang_key',Secure($value))->update('langs', array('options' => NULL));
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'delete_multi_users') {
        if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('activate','deactivate','delete'))) {
            foreach ($_POST['ids'] as $key => $value) {
                if (is_numeric($value) && $value > 0) {
                    if ($_POST['type'] == 'delete') {
                        $d_user = LoadEndPointResource('users');
                        if($d_user) {
                            $deleted = $d_user->delete_user(Secure($value));
                        }
                    }
                    elseif ($_POST['type'] == 'activate') {
                        $db->where('id', Secure($value));

                        $update_data = array('active' => '1','email_code' => '');
                        $update = $db->update('users', $update_data);
                    }
                    elseif ($_POST['type'] == 'deactivate') {
                        $db->where('id', Secure($value));

                        $update_data = array('active' => '0','email_code' => '');
                        $update = $db->update('users', $update_data);
                    }
                }
            }
            $data = ['status' => 200];
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
    }
    if ($s == 'update_user_permission'){
        if(!empty($_GET['user_id'])){
            $_id = (int)Secure($_GET['user_id']);
            $_user = $db->where('id',$_id)->getOne('users',array('*'));

            if($_user) {
                $_new_permission = array();
                $_permission = $_user['permission'];
                if( $_permission == '' ){
                    $_new_permission[Secure($_GET['permission'])] = Secure($_GET['permission_val']);
                }else{
                    $_permission = unserialize($_user['permission']);
                    $_permission[Secure($_GET['permission'])] = Secure($_GET['permission_val']);
                    $_new_permission = $_permission;
                }
                $db->where('id',$_id)->update('users', array( 'permission' => serialize($_new_permission)));

                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'update_user_permission_normal'){
        if(!empty($_POST['user_id'])){
            $_id = (int)Secure($_POST['user_id']);
            $_user = $db->where('id',$_id)->getOne('users',array('*'));

            if($_user) {
               $db->where('id',$_id)->update('users', array( 'permission' => serialize($_POST['permission'])));
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_gift') {
        if (!empty($_GET['gift_id'])) {
            if (Wo_DeleteGift($_GET['gift_id']) === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_sticker') {
        if (!empty($_GET['sticker_id'])) {
            if (Wo_DeleteSticker($_GET['sticker_id']) === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_photo') {
        if (!empty($_GET['photo_id'])) {
            $photo_id = Secure($_GET['photo_id']);
            $photo_file = Secure($_GET['photo_file']);
            $avater_file = str_replace('_full.','_avater.', $photo_file);
            $db->where('avater',$avater_file)->update('users',array( 'avater' => $wo['config']['userDefaultAvatar'] ));
            $deleted = false;
            Wo_DeletePhoto($photo_id);
            if (DeleteFromToS3( $photo_file ) === true) {
                $deleted = true;
            }
            if (DeleteFromToS3( $avater_file ) === true) {
                $deleted = true;
            }
            if ($deleted === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_photo') {
        if (!empty($_GET['photo_id'])) {
            $photo_id = (int)Secure($_GET['photo_id']);
            Wo_ApprovePhoto($photo_id);
            $data = array(
                'status' => 200
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'disapprove_photo') {
        if (!empty($_GET['photo_id'])) {
            $photo_id = (int)Secure($_GET['photo_id']);
            Wo_DisApprovePhoto($photo_id);
            $data = array(
                'status' => 200
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_all_photo') {
        Wo_ApproveAllPhoto();
        $data = array(
            'status' => 200
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'disapprove_all_photo') {
        Wo_DisApproveAllPhoto();
        $data = array(
            'status' => 200
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_receipt') {
        if (!empty($_GET['receipt_id'])) {
            $photo_id = Secure($_GET['receipt_id']);
            $receipt = $db->where('id',$photo_id)->getOne('bank_receipts',array('*'));

            if($receipt){

                $membershipType = 0;
                $amount         = 0;
                $realprice      = (int)$receipt['price'];

                if ($receipt['mode'] == 'credits') {
                    if ($realprice == (int)$wo['config']['bag_of_credits_price']) {
                        $amount = (int)$wo['config']['bag_of_credits_amount'];
                    } else if ($realprice == (int)$wo['config']['box_of_credits_price']) {
                        $amount = (int)$wo['config']['box_of_credits_amount'];
                    } else if ($realprice == (int)$wo['config']['chest_of_credits_price']) {
                        $amount = (int)$wo['config']['chest_of_credits_amount'];
                    }
                } else if ($receipt['mode'] == 'membership') {
                    if ($realprice == (int)$wo['config']['weekly_pro_plan']) {
                        $membershipType = 1;
                    } else if ($realprice == (int)$wo['config']['monthly_pro_plan']) {
                        $membershipType = 2;
                    } else if ($realprice == (int)$wo['config']['yearly_pro_plan']) {
                        $membershipType = 3;
                    } else if ($realprice == (int)$wo['config']['lifetime_pro_plan']) {
                        $membershipType = 4;
                    }
                } else if ($receipt['mode'] == 'unlock_photo_private') {

                }


                $updated = $db->where('id',$photo_id)->update('bank_receipts',array('approved'=>1,'approved_at'=>time()));
                if ($updated === true) {

                    $Notification = LoadEndPointResource('Notifications');
                    if($Notification) {
                        $Notification->createNotification(auth()->web_device_id, auth()->id, $receipt['user_id'], 'approve_receipt', $wo['config']['currency_symbol'] . $realprice, '/#');
                    }

                    if($receipt['mode'] == 'credits'){
                        $query_one = mysqli_query($conn, "UPDATE `users` SET `balance` = `balance` + {$amount} WHERE `id` = {$receipt['user_id']}");
                    }
                    if($receipt['mode'] == 'membership'){
                        $query_one = mysqli_query($conn, "UPDATE `users` SET `pro_time` = '".time()."', `is_pro` = '1', `pro_type` = '".$membershipType."' WHERE `id` = ".$receipt['user_id']);
                    }

                    if($receipt['mode'] == 'unlock_photo_private'){
                        $query_one = mysqli_query($conn, "UPDATE `users` SET `lock_private_photo` = 0 WHERE `id` = {$receipt['user_id']}");
                    }

                    $query_one = mysqli_query($conn, "INSERT `payments`(`user_id`,`amount`,`type`,`pro_plan`,`credit_amount`,`via`) VALUES ('{$receipt['user_id']}','{$receipt['price']}','{$receipt['mode']}','{$membershipType}','{$amount}','Bank transfer');");

                    $data = array(
                        'status' => 200
                    );
                }
            }
            $data = array(
                'status' => 200,
                'data' => $receipt
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_receipt') {
        if (!empty($_GET['receipt_id'])) {
            $user_id = Secure($_GET['user_id']);
            $photo_id = Secure($_GET['receipt_id']);
            $photo_file = Secure($_GET['receipt_file']);

            $Notification = LoadEndPointResource('Notifications');
            if($Notification) {
                $Notification->createNotification(auth()->web_device_id, auth()->id, $user_id, 'disapprove_receipt', '', '/contact');
            }

            $deleted = false;
            $db->where('id',$photo_id)->delete('bank_receipts');
            if (DeleteFromToS3( $photo_file ) === true) {
                $deleted = true;
            }
            if ($deleted === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_reported_content') {
        if (!empty($_GET['id']) && !empty($_GET['type']) && !empty($_GET['report_id'])) {
            $type   = Secure($_GET['type']);
            $id     = Secure($_GET['id']);
            $report = Secure($_GET['report_id']);
            if ($type == 'user') {
                $deleteReport = Wo_DeleteReport($report);
                if ($deleteReport === true) {
                    $data = array(
                        'status' => 200,
                        'html' => Wo_CountUnseenReports()
                    );
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'mark_as_safe') {
        if (!empty($_GET['report_id'])) {
            $deleteReport = Wo_DeleteReport($_GET['report_id']);
            if ($deleteReport === true) {
                $data = array(
                    'status' => 200,
                    'html' => Wo_CountUnseenReports()
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'update_general_setting' && Wo_CheckSession($hash_id) === true) {
        $saveSetting = false;
        if (isset($_POST['skey'])) {
            unset($_POST['skey']);
        }
        if (isset($_POST['hash_id'])) {
            unset($_POST['hash_id']);
        }
        foreach ($_POST as $key => $value) {
            if ($key == 'smtp_password') {
                $value = openssl_encrypt($value, "AES-128-ECB", 'mysecretkey1234');
            }
            if ($key == 'twilio_chat_call' && $value == 1) {
                if ($wo['config']['agora_chat_call'] == 1) {
                    Wo_SaveConfig('agora_chat_call', 0);
                }
            }
            if ($key == 'agora_chat_call' && $value == 1) {
                if ($wo['config']['twilio_chat_call'] == 1) {
                    Wo_SaveConfig('twilio_chat_call', 0);
                }
            }
            if ($key == 'bulksms_provider' && $value == 1) {
                Wo_SaveConfig('twilio_provider', 0);
                Wo_SaveConfig('messagebird_provider', 0);
                Wo_SaveConfig('infobip_provider', 0);
                Wo_SaveConfig('msg91_provider', 0);
            }
            if ($key == 'twilio_provider' && $value == 1) {
                Wo_SaveConfig('bulksms_provider', 0);
                Wo_SaveConfig('messagebird_provider', 0);
                Wo_SaveConfig('infobip_provider', 0);
                Wo_SaveConfig('msg91_provider', 0);
            }
            if ($key == 'messagebird_provider' && $value == 1) {
                Wo_SaveConfig('bulksms_provider', 0);
                Wo_SaveConfig('twilio_provider', 0);
                Wo_SaveConfig('infobip_provider', 0);
                Wo_SaveConfig('msg91_provider', 0);
            }
            if ($key == 'infobip_provider' && $value == 1) {
                Wo_SaveConfig('bulksms_provider', 0);
                Wo_SaveConfig('twilio_provider', 0);
                Wo_SaveConfig('messagebird_provider', 0);
                Wo_SaveConfig('msg91_provider', 0);
            }
            if ($key == 'msg91_provider' && $value == 1) {
                Wo_SaveConfig('bulksms_provider', 0);
                Wo_SaveConfig('twilio_provider', 0);
                Wo_SaveConfig('messagebird_provider', 0);
                Wo_SaveConfig('infobip_provider', 0);
            }
            $saveSetting = Wo_SaveConfig($key, $value);
            if( $key == 'image_verification' && $value == "1"){
                Wo_SaveConfig('image_verification_start', time());
            }elseif( $key == 'image_verification' && $value == "0"){
                Wo_SaveConfig('image_verification_start', 0);
            }
        }
        if ($saveSetting === true) {
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'update_pages_seo' && Wo_CheckSession($hash_id) === true) {
        $config_seo = $wo['config']['seo'];

        $arr_seo = unserialize($config_seo);
        $arr_seo[$_POST['page_name']] = array(
            'title' => $_POST['default_title'],
            'meta_description' => $_POST['meta_description'],
            'meta_keywords' => $_POST['meta_keywords'],
        );
        $saveSetting = Wo_SaveConfig('seo', serialize($arr_seo));
        if ($saveSetting === true) {
            $data['status'] = 200;
            $data['page'] = $_POST['page_name'];
            $data['config_seo'] = $wo['config']['seo'];
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'save-design' && Wo_CheckSession($hash_id) === true) {
        $saveSetting = false;
        if (isset($_FILES['logo']['name'])) {
            $fileInfo = array(
                'file' => $_FILES["logo"]["tmp_name"],
                'name' => $_FILES['logo']['name'],
                'size' => $_FILES["logo"]["size"]
            );
            $media    = UploadLogo($fileInfo);
        }
        if (isset($_FILES['light-logo']['name'])) {
            $fileInfo = array(
                'file' => $_FILES["light-logo"]["tmp_name"],
                'name' => $_FILES['light-logo']['name'],
                'size' => $_FILES["light-logo"]["size"],
                'light-logo' => true
            );
            $media    = UploadLogo($fileInfo);
        }
        if (isset($_FILES['favicon']['name'])) {
            $fileInfo = array(
                'file' => $_FILES["favicon"]["tmp_name"],
                'name' => $_FILES['favicon']['name'],
                'size' => $_FILES["favicon"]["size"],
                'favicon' => true
            );
            $media    = UploadLogo($fileInfo);
        }

        $saveSetting = false;
        foreach ($_POST as $key => $value) {
            $saveSetting = Wo_SaveConfig($key, $value);
        }
        if ($saveSetting === true) {
            $data['status'] = 200;
        }

        $data['status'] = 200;
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_user' && isset($_GET['user_id']) && Wo_CheckSession($hash_id) === true) {
        $deleted = false;
        $d_user = LoadEndPointResource('users');
        if($d_user) {
            $deleted = $d_user->delete_user(Secure($_GET['user_id']));
        }
        if ($deleted['is_delete'] === true) {
            $data['status'] = 200;
            $data['message'] = 'Deleted';
        }else{
            $data['status'] = 200;
            $data['message'] = 'Not Deleted';
        }
        $data['deleted'] = $deleted;
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'update_terms_setting' && Wo_CheckSession($hash_id) === true) {
        $saveSetting = false;
        foreach ($_POST as $key => $value) {
            $saveSetting = Wo_SaveConfig($key, base64_decode($value));
        }
        if ($saveSetting === true) {
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_lang') {
        if (Wo_CheckSession($hash_id) === true) {
            $mysqli = Wo_LangsNamesFromDB();
            if (in_array($_POST['lang'], $mysqli)) {
                $data['status']  = 400;
                $data['message'] = 'This lang is already used.';
            } else if( !ctype_alpha($_POST['lang']) ) {
                $data['status']  = 400;
                $data['message'] = 'you can use only letters in language name.';
            } else {
                $lang_o_name = Secure($_POST['lang']);
                $lang_name = Secure($_POST['lang']);
                $lang_name = strtolower($lang_name);
                $lang_o_for_insert_name = $lang_name;
                $query     = mysqli_query($conn, "ALTER TABLE `langs` ADD `$lang_name` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
                if ($query) {
                    //$content = file_get_contents('assets/languages/extra/english.php');
                    //$fp      = fopen("assets/languages/extra/$lang_name.php", "wb");
                    //fwrite($fp, $content);
                    //fclose($fp);
                    $english = Wo_LangsFromDB('english');
                    foreach ($english as $key => $lang) {
                        $lang  = Secure($lang);
                        $query = mysqli_query($conn, "UPDATE `langs` SET `{$lang_name}` = '$lang' WHERE `lang_key` = '{$key}'");
                    }
                    $data_langs = [];
                    $query = mysqli_query($conn, "SHOW COLUMNS FROM `langs`");
                    while ($fetched_data = mysqli_fetch_assoc($query)) {
                        if ($fetched_data['Field'] != "ref" && $fetched_data['Field'] != "lang_key" && $fetched_data['Field'] != "id") {
                            $data_langs[] = $fetched_data['Field'];
                        }
                    }
                    $final_query = "";
                    $implode = implode(', ', $data_langs);
                    for ($i=0; $i < count($data_langs); $i++) {
                        $text = "'$lang_name',"; 
                        if (($i + 1) == count($data_langs)) {
                            $text = "'$lang_name'"; 
                        }
                        $final_query .= $text;
                    }
                    $insert = mysqli_query($conn, "INSERT INTO `langs` (`id`, `lang_key`, $implode) VALUES (NULL, '$lang_o_for_insert_name', $final_query)");
                    $data['status'] = 200;
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_lang_key') {
        if (Wo_CheckSession($hash_id) === true) {
            if (!empty($_POST['lang_key'])) {
                $lang_key  = Secure($_POST['lang_key']);
                $mysqli    = mysqli_query($conn, "SELECT COUNT(id) as count FROM `langs` WHERE `lang_key` = '$lang_key'");
                $sql_fetch = mysqli_fetch_assoc($mysqli);
                if ($sql_fetch['count'] == 0) {
                    $mysqli = mysqli_query($conn, "INSERT INTO `langs` (`lang_key`) VALUE ('$lang_key')");
                    if ($mysqli) {
                        $_SESSION['language_changed'] = true;
                        $data['status'] = 200;
                        $data['url']    = Wo_LoadAdminLinkSettings('manage-languages');
                    }
                } else {
                    $data['status']  = 400;
                    $data['message'] = 'This key is already used, please use other one.';
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_lang') {
        $mysqli = Wo_LangsNamesFromDB();
        if (in_array($_GET['id'], $mysqli)) {
            $lang_name = Secure($_GET['id']);
            $query     = mysqli_query($conn, "ALTER TABLE `langs` DROP COLUMN `$lang_name`");
            if ($query) {
                //unlink("assets/languages/extra/$lang_name.php");
                $data['status'] = 200;
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'update_lang_key') {
        if (Wo_CheckSession($hash_id) === true) {
            $array_langs = array();
            $lang_key    = Secure($_POST['id_of_key']);
            $langs       = Wo_LangsNamesFromDB('english',true);
            foreach ($_POST as $key => $value) {
                if (in_array($key, $langs)) {
                    $key   = Secure($key);
                    $value = Secure($value);
                    $query = mysqli_query($conn, "UPDATE `langs` SET `{$key}` = '{$value}' WHERE `id` = '{$lang_key}'");
                    if ($query) {
                        $data['status'] = 200;
                        $_SESSION['language_changed'] = true;
                    }
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_message') {
        $send_message      = SendEmail(auth()->email,'Test Message From ' . $wo['config']['siteName'],'If you can see this message, then your SMTP configuration is working fine.');
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = 'Error while sending email.';
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_sms_message') {
        $message      = 'This is a test message from ' . $wo['config']['siteName'];
        $send_message = SendSMS($wo['config']['sms_phone_number'], $message);
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = $send_message;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_bulksms_message') {
        $message      = 'This is a test message from ' . $wo['config']['siteName'];
        $send_message = SendSMS($wo['config']['sms_phone_number'], $message);
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = $send_message;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_msg91_message') {
        $message      = 'This is a test message from ' . $wo['config']['siteName'];
        $send_message = SendSMS($wo['config']['sms_phone_number'], $message);
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = $send_message;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_infobip_message') {
        $message      = 'This is a test message from ' . $wo['config']['siteName'];
        $send_message = SendSMS($wo['config']['sms_phone_number'], $message);
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = $send_message;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_messagebird_message') {
        $message      = 'This is a test message from ' . $wo['config']['siteName'];
        $send_message = SendSMS($wo['config']['sms_phone_number'], $message);
        if ($send_message === true) {
            $data['status'] = 200;
        } else {
            $data['status'] = 400;
            $data['error']  = $send_message;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_s3') {
        try {
            $s3Client = S3Client::factory(array(
                'version' => 'latest',
                'region' => $wo['config']['region'],
                'credentials' => array(
                    'key' => $wo['config']['amazone_s3_key'],
                    'secret' => $wo['config']['amazone_s3_s_key']
                )
            ));
            $buckets  = $s3Client->listBuckets();
            $result   = $s3Client->putBucketCors(array(
                'Bucket' => $wo['config']['bucket_name'], // REQUIRED
                'CORSConfiguration' => array( // REQUIRED
                    'CORSRules' => array( // REQUIRED
                        array(
                            'AllowedHeaders' => array(
                                'Authorization'
                            ),
                            'AllowedMethods' => array(
                                'POST',
                                'GET',
                                'PUT'
                            ), // REQUIRED
                            'AllowedOrigins' => array(
                                '*'
                            ), // REQUIRED
                            'ExposeHeaders' => array(),
                            'MaxAgeSeconds' => 3000
                        )
                    )
                )
            ));
            if (!empty($buckets)) {
                if ($s3Client->doesBucketExist($wo['config']['bucket_name'])) {
                    $data['status'] = 200;
                    $array          = array(
                        'upload/photos/d-avatar.jpg'
                    );
                    foreach ($array as $key => $value) {
                        $upload = Wo_UploadToS3($value, array(
                            'delete' => 'no'
                        ));
                    }
                } else {
                    $data['status'] = 300;
                }
            } else {
                $data['status'] = 500;
            }
        }
        catch (Exception $e) {
            $data['status']  = 400;
            $data['message'] = $e->getMessage();
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'test_s3_2') {
        try {
            $s3Client = S3Client::factory(array(
                'version' => 'latest',
                'region' => $wo['config']['region_2'],
                'credentials' => array(
                    'key' => $wo['config']['amazone_s3_key_2'],
                    'secret' => $wo['config']['amazone_s3_s_key_2']
                )
            ));
            $buckets  = $s3Client->listBuckets();
            $result   = $s3Client->putBucketCors(array(
                'Bucket' => $wo['config']['bucket_name_2'], // REQUIRED
                'CORSConfiguration' => array( // REQUIRED
                    'CORSRules' => array( // REQUIRED
                        array(
                            'AllowedHeaders' => array(
                                'Authorization'
                            ),
                            'AllowedMethods' => array(
                                'POST',
                                'GET',
                                'PUT'
                            ), // REQUIRED
                            'AllowedOrigins' => array(
                                '*'
                            ), // REQUIRED
                            'ExposeHeaders' => array(),
                            'MaxAgeSeconds' => 3000
                        )
                    )
                )
            ));
            if (!empty($buckets)) {
                if ($s3Client->doesBucketExist($wo['config']['bucket_name_2'])) {
                    $data['status'] = 200;
                    $array          = array(
                        'upload/photos/d-avatar.jpg'
                    );
                    foreach ($array as $key => $value) {
                        $upload = Wo_UploadToS3($value, array(
                            'delete' => 'no'
                        ));
                    }
                } else {
                    $data['status'] = 300;
                }
            } else {
                $data['status'] = 500;
            }
        }
        catch (Exception $e) {
            $data['status']  = 400;
            $data['message'] = $e->getMessage();
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    // if ($s == 'fake-users') {

    //     $countries = array('AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
    //     $countries_key = array_keys($countries);

    //     require $_BASEPATH.'lib'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'fake-users'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    //     $faker = Faker\Factory::create();
    //     if (empty($_POST['password'])) {
    //         $_POST['password'] = '123456789';
    //     }
    //     $count_users = $_POST['count_users'];
    //     $password = $_POST['password'];
    //     $avatar = $_POST['avatar'];

    //     Wo_RunInBackground(array('status' => 200));

    //     $Date1 = date('Y-m-d');
    //     $Date2 = date('Y-m-d', strtotime($Date1 . " - 19 year"));
    //     $users      = LoadEndPointResource('users');
    //     if ($users) {
    //         for ($i=0; $i < $count_users; $i++) {
    //             $genders = array("4525", "4526");
    //             $random_keys = array_rand($genders, 1);
    //             $gender = array_rand(array("male", "female"), 1);
    //             $gender = $genders[$random_keys];
    //             $re_data  = array(
    //                 'email' => Secure(str_replace(".", "_", $faker->userName) . '_' . rand(111, 999) . "@yahoo.com", 0),
    //                 'username' => Secure($faker->userName . '_' . rand(111, 999), 0),
    //                 'password' => Secure($password, 0),
    //                 'email_code' => Secure(md5($faker->userName . '_' . rand(111, 999)), 0),
    //                 'src' => 'Fake',
    //                 'gender' => Secure($gender),
    //                 'lastseen' => time(),
    //                 'verified' => 1,
    //                 'active' => 1,
    //                 'first_name' => $faker->name,
    //                 'last_name' => $faker->lastName,
    //                 'lat' => auth()->lat,
    //                 'lng' => auth()->lng,
    //                 'birthday' => $Date2,
    //                 'country_id' => $countries_key[array_rand($countries_key)],
    //                 'about' => 'Ut ab voluptas sed a nam. Sint autem inventore aut officia aut aut blanditiis. Ducimus eos odit amet et est ut eum.'
    //             );

    //             if ($avatar == 1) {
    //                 $re_data['avater'] = 'upload/photos/users/'.rand(1,20).'.jpg'; //$users->ImportImageFromLogin($faker->imageUrl($wo['config']['profile_picture_width_crop'], $wo['config']['profile_picture_height_crop'],'people'), 1);
    //             }

    //             $re_data['address']         = $faker->address;
    //             $re_data['facebook']        = $faker->company;
    //             $re_data['google']          = $faker->company;
    //             $re_data['twitter']         = $faker->company;
    //             $re_data['linkedin']        = $faker->company;
    //             $re_data['website']         = $faker->company;
    //             $re_data['instagram']       = $faker->company;
    //             $re_data['language']        = 'english';
    //             $re_data['type']            = 'user';
    //             $re_data['phone_number']    = $faker->phoneNumber;
    //             $re_data['timezone']        = 'UTC';
    //             $re_data['start_up']        = '3';
    //             $re_data['height']          = '152';
    //             $re_data['hair_color']      = '1';
    //             $re_data['interest']        = 'Sint autem inventore aut officia';
    //             $re_data['location']        = 'Ducimus';
    //             $re_data['relationship']    = '1';
    //             $re_data['work_status']     = '2';
    //             $re_data['education']       = '3';
    //             $re_data['ethnicity']       = '3';
    //             $re_data['body']            = '3';
    //             $re_data['character']       = '13';
    //             $re_data['children']        = '2';
    //             $re_data['friends']         = '3';
    //             $re_data['pets']            = '0';
    //             $re_data['live_with']       = '3';
    //             $re_data['car']             = '2';
    //             $re_data['religion']        = '1';
    //             $re_data['smoke']           = '2';
    //             $re_data['drink']           = '2';
    //             $re_data['travel']          = '2';
    //             $re_data['music']           = 'pop';
    //             $re_data['dish']            = 'meat';
    //             $re_data['song']            = 'song';
    //             $re_data['hobby']           = 'hobby';
    //             $re_data['city']            = 'city';
    //             $re_data['sport']           = 'sport';
    //             $re_data['book']            = 'book';
    //             $re_data['movie']           = 'movie';
    //             $re_data['colour']          = 'red';
    //             $re_data['tv']              = 'tv';
    //             $re_data['privacy_show_profile_on_google']      = 1;
    //             $re_data['privacy_show_profile_random_users']   = 1;
    //             $re_data['privacy_show_profile_match_profiles'] = 1;
    //             $re_data['phone_verified']                      = 1;
    //             $re_data['online']                              = 1;

    //             $regestered_user = $users->register($re_data);
    //         }
    //     }
    //     header("Content-type: application/json");
    //     echo json_encode($data);
    //     exit();
    // }
    
    
    
    
     if ($s == 'fake-users') {

       
        $countries = array('AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
        $countries_key = array_keys($countries);

        require $_BASEPATH.'lib'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'fake-users'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        $faker = Faker\Factory::create();
       
        if (empty($_POST['password'])) {
            $_POST['password'] = '123456789';
        }
        $count_users = $_POST['count_users'];
        $password = $_POST['password'];
        $avatar = $_POST['avatar'];
        $genderchoice= $_POST['gender_choice'];

        Wo_RunInBackground(array('status' => 200));

        // $Date1 = date('Y-m-d');
        // $Date2 = date('Y-m-d', strtotime($Date1 . " - 25 year"));
        $users      = LoadEndPointResource('users');
        if ($users) {
            for ($i=0; $i < $count_users; $i++) {
                //$genders = array("4525","4526");
                //$random_keys = array_rand($genders, 1);
               // $gender = array_rand(array("male", "female"), 1);
                if ($genderchoice == "F")
                {
                    $gender = '4526';
                    $fgender= 'female';
                }
                else
                {
                    $gender = '4525';
                    $fgender= 'male';
                }

                $timestamp = rand( strtotime("Jan 01 1985"), strtotime("Nov 01 2000") );
                $random_Date = date("Y-m-d", $timestamp );

                
                $aboutit =array("I am well-balanced and stable, but willing to let you knock me off my feet.","I want to be everything you didn't know you were looking for.","I believe happiness is wanting what you get.","I am someone who will kiss you in the rain.","I don't want a perfect relationship: I want someone to act silly with, who treats me well, and who loves being with me more than anything.","I believe the best time for new beginnings is now.","What I am is good enough.","I want to be the reason you look down at your phone and smile.","I believe in true love.","I am old fashioned sometimes. I still believe in romance, in roses, in holding hands.","I want someone I can love who will love me back.","As long as you're happy, I don't care about anything else.","I don't smoke, drink or party every weekend. I don't play around or start drama to get attention. Yes, we do still exist!","I want someone who will watch movies with me on lazy days.","I believe in sticking around through the good and the bad times.","Nice guys finish last? Let's prove that wrong.","I want someone who will keep surprising me.","I believe the right person is out there looking for me.","I'm going to make the rest of my life the best of my life. Care to share it with me?","I want someone who can make me smile for no reason.","I could be the person you've been dreaming of.","I am strong, kind, smart, hilarious, sweet, lovable, and amazing. Isn't that what you've been looking for?","I want to be the reason your dreams will come true.","Together we could make our dreams come true.","I'm neither especially clever nor especially gifted, except for when it comes to being your perfect other half.","I want someone loving, who can cook, and if you look good in a pair of jeans, that would be a bonus!","I believe nothing is more romantic than someone who wants you as much as you want them.","I won't run away in the storms.","I want to build a lifetime of dreams with someone special.","I believe an honest relationship is more important than a perfect relationship.","I want to inspire and be inspired.","I want a lasting relationship.","I believe life is short, and I want to waste it wisely.","I am here to find love and give love in return.","I want to fall madly in love.","I'm not going to say it's going to be easy, but I can promise it will be worth it!","I can guarantee you won't find anybody else like me.","I want a happily ever after.","I believe I am too good a catch to be single.","WiFi, food, my bed, snuggles. Perfection.","I want to meet someone who is afraid to lose me.","I want to meet someone who is afraid to lose me.","I believe I have a lot of love to give.","I am strong enough to protect you and soft enough to melt your heart.","I want to build a future with the right person.","I am here because I believe life begins at the end of your comfort zone.","If I could rate my personality, I'd say good looking!","I want to meet someone who will text me good morning and goodnight.","I believe how you make others feel about themselves says a lot about you.","I find that having a dirty mind makes ordinary conversations much more interesting.","I want to meet someone who gives me compliments.","I believe something beautiful is on the horizon.","I live my life without stress and worries.","I want to meet someone who makes me laugh.","I believe some people cross your path and change your whole direction.","I am good looking (In certain lighting).","I want to meet someone who likes to cuddle.","I believe good looks fade, but a good heart keeps you beautiful forever.","As long as you think I'm awesome, we will get along just fine.","I want to meet someone who won't rush things.","","I am too positive to be doubtful, too optimistic to be fearful, and too determined to be defeated.","I want someone who I can be completely myself around.","","","I want someone I can play Xbox with.","Forget what hurt you in the past. It wasn't me. I'm like the opposite of that person!","I want someone who I can kiss in the pouring rain.","I'm not beautiful like you, but I'm beautiful like me!","I am just one small person in this big world trying to find real love.","I want someone who enjoys holding hands.","I want someone who will be my best friend.","I'm responsible, hard-working, faithful and a really, really good kisser.","Once I've found my special someone, my life will be complete.","I just want someone to throw cookie dough around in the kitchen with.","I want someone who will remember the little things.","Being both strong and soft is a combination I have mastered.","I'm not here to be an average partner, I'm here to be an awesome partner.","I want someone who can shut me up with kisses.","I want you—So be brave and want me too!","Don't let idiots ruin your day, date me instead!","I'm a tidy person, with a few messy habits.","I want someone to love me without restriction, trust me without fear, and want me without demand!","I want to be the best at loving you.","I've learned to stop rushing things that need time to grow.","I'm trusting, and I'll never try to tell you what you can and can't do.","I am 100% ready to invest in a long term relationship.","I am looking for my last love.","I'm loving, and I'll always look forward to seeing you at the end of each day.","I appreciate the little things.","","I'm willing to work hard to make you happy in life.","I am not the one your mother warned you about.","I want to meet someone wants to surprise me and hug me from behind.","I like it when she has really good taste in music, even if it’s different than mine. I just like a girl who’s passionate about music."," Good artistic abilities. Like she’s got some kind of interesting talent – painting, drawing, singing, writing, playing an instrument, whatever. It makes me want to learn more about her.","A woman who always smiles. There’s nothing more attractive than a woman who’s smiling all the time.","I love it when she’s always really happy to see me. Like when she gives me a big huge or smiles a lot when we see each other. It just makes me even more excited to be around her.","Somebody who you can talk to really easily. Where you don’t have to try very hard and the conversation just flows really easily and really naturally. It’s nice when a girl’s attractive but if you can’t talk to her you can only go so far."," Boldness. When she’s really confident and fearless when it comes to doing anything.","Independence and confidence: she’s not afraid to speak up and share her thoughts. She’ll talk to anyone about anything and she’s very sure of herself in front of other people."," Someone who is open-minded and won’t judge you. I need to be with someone who makes me feel comfortable bringing stuff up to her and doesn’t make me worry about what she’s going to think of me.","She has to be comfortable enough to let me have my freedom and she’s got to have her own independent life too. I have to know that she trusts me and I don’t have to worry about her ever freaking out about me having my own friends and my own social life.","Honesty. There’s nothing I admire more in a woman than honesty and the ability to be truthful and open with me.","Being good with kids. I want to have a family and I want to know that she’s nurturing and would be a good mother.","She can easily befriend anyone and literally have a conversation with anybody. If I’m going to introduce her to my family or my friends I don’t want to have to worry about her sitting in the corner feeling awkward. I just love a girl who can navigate her way through any social situation."," A girl who doesn’t get jealous easily. We’re both gonna have plenty of friends of the opposite sex and it’s just so much easier to be with someone who isn’t constantly getting jealous and feels confident about our relationship."," When she’s not obsessed with social media and taking pictures every five seconds. There’s nothing more annoying than a girl trying to update her Snapchat story constantly. It’s so much more attractive when a girl can just be in the moment.","Someone who shares my values.","A girl with an awesome sense of humor and a love of goofing around and being silly. She doesn’t take life too seriously.","A girl who doesn’t worry constantly about what everybody thinks of her. That’s exhausting.","We’ve got to have chemistry. That doesn’t have to be only physical. Just chemistry in the sense that we can joke around together and have interesting conversations and talk easily together. Something where you just “click” with her.","Integrity.","A happy attitude. Someone who can be optimistic.","A girl who takes care of herself, like she exercises and treats her body well. I want to be with someone who’s going to be healthy for the long haul.","A girl who’s comfortable with herself. She likes herself and she’s not trying to be anyone else. Then we can spend less time on me trying to make her feel like she’s good enough and more time enjoying each other.","A girl who doesn’t need to go on fancy dates all the time. Someone who’s comfortable hanging out on the couch and drinking beer.","A girl who has ambition and doesn’t feel weird about it.","A girl who surprises me, by liking things or doing things that I wouldn’t expect from her. And when she does something sweet to surprise me, like showing up with my favorite beer when she comes over.","When she has a really cute and infectious laugh. It just makes me laugh too.","When she has a really sweet scent that’s not overwhelming. Not too much perfume or anything. She just has a really fresh, unique scent.");
                $aboutit_random_keys = array_rand($aboutit);
                $aboutit_random_keys_1 = $aboutit[$aboutit_random_keys];
                
                $re_data  = array(
                    'email' => Secure(str_replace(".", "_", $faker->userName) . '_' . rand(111, 999) . "@yahoo.com", 0),
                    'username' => Secure($faker->userName . '_' . rand(111, 999), 0),
                    'password' => Secure($password, 0),
                    'email_code' => Secure(md5($faker->userName . '_    ' . rand(111, 999)), 0),
                    'src' => 'Fake',
                    'gender' => Secure($gender),
                    'lastseen' => time(),
                    'verified' => 1,
                    'active' => 1,
                    'first_name' => $faker->name($fgender),
                    'last_name' => $faker->lastName,
                    'lat' => auth()->lat,
                    'lng' => auth()->lng,
                    'birthday' => $random_Date,
                    'country_id' => $countries_key[array_rand($countries_key)],
                    'about' => $aboutit_random_keys_1
                );
         
              

                if ($avatar == 1) {
                   
                    $re_data['avater'] = 'upload/photos/users/'.rand(1,20).'.jpg'; //$users->ImportImageFromLogin($faker->imageUrl($wo['config']['profile_picture_width_crop'], $wo['config']['profile_picture_height_crop'],'people'), 1);
                }
              
                //    hair color array
                $hair_color = array('1' => __('Brown'),'2' => __('Black'),'3' => __('White'),'4' => __('Sandy'),'5' => __('Gray or Partially Gray'),'6' => __('Red/Auburn'),'7' => __('Blond/Strawberry'),'8' => __('Blue'),'9' => __('Green'),'10' => __('Orange'),'11' => __('Pink'),'12' => __('Purple'),'13' => __('Partly or Completely Bald'),'14' => __('Other'));
                $hair_color_key = array_keys($hair_color);
                $hair_random_keys = array_rand($hair_color_key);
                
                // relationship array
                $relationship =array('1' => __('Single'),'2' => __('Married'));
                $relationship_color_key = array_keys($relationship);
                $relationship_random_keys = array_rand($relationship_color_key);

                // work_status

                $work_status= array('1' => __('I\'m studying'),'2' => __('I\'m working'),'3' => __('I\'m looking for work'),'4' => __('I\'m retired'),'5' => __('Self-Employed'),'6' => __('Other'));
                $work_status_key= array_keys($work_status);
                $work_status_random_keys= array_rand($work_status_key);

                // education

                $education=  array('1' => __('Secondary school'),'2' => __('ITI'),'3' => __('College'),'4' => __('University'),'5' => __('Advanced degree'),'6' => __('Other'));
                $education_key= array_keys($education);
                $education_random_keys= array_rand($education_key);

                //  ethnicity 
                $ethnicity = array('1' => __('White'),'2' => __('Black'),'3' => __('Middle Eastern'),'4' => __('North African'),'5' => __('Latin American'),'6' => __('Mixed'),'7' => __('Asian'),'8' => __('Other'));
                $ethnicity_key = array_keys($ethnicity);
                $ethnicity_random_keys = array_rand($ethnicity_key);

                // body
                $body = array('1' => __('Slim'),'2' => __('Sporty'),'3' => __('Curvy'),'4' => __('Round'),'5' => __('Supermodel'),'6' => __('Average'),'7' => __('Other'));
                $body_key = array_keys($body);
                $body_random_keys = array_rand($body_key);
                
                //character
                $character = array('1' => __('Accommodating'),'2' => __('Adventurous'),'3' => __('Calm'),'4' => __('Careless'),'5' => __('Cheerful'),'6' => __('Demanding'),'7' => __('Extroverted'),'8' => __('Honest'),'9' => __('Generous'),'10' => __('Humorous'),'11' => __('Introverted'),'12' => __('Liberal'),'13' => __('Lively'),'14' => __('Loner'),'15' => __('Nervous'),'16' => __('Possessive'),'17' => __('Quiet'),'18' => __('Reserved'),'19' => __('Sensitive'),'20' => __('Shy'),'21' => __('Social'),'22' => __('Spontaneous'),'23' => __('Stubborn'),'24' => __('Suspicious'),'25' => __('Thoughtful'),'26' => __('Proud'),'27' => __('Considerate'),'28' => __('Friendly'),'29' => __('Polite'),'30' => __('Reliable'),'31' => __('Careful'),'32' => __('Helpful'),'33' => __('Patient'),'34' => __('Optimistic'));
                $character_key = array_keys($character);
                $character_random_keys = array_rand($character_key);

                // children
                $children = array('1' => __('No, never'),'2' => __('Someday, maybe'),'3' => __('Expecting'),'4' => __('I already have kids'),'5' => __('I have kids and don\'t want more'));
                $children_key = array_keys($children);
                $children_random_keys = array_rand($children_key);
                
                
                // friends

                $friends = array('1' => __('No friends'),'2' => __('Some friends'),'3' => __('Many friends'),'4' => __('Only good friends'));
                $friends_key = array_keys($friends);
                $friends_random_keys = array_rand($friends_key);

                // pets
                $pets = array('1' => __('None'),'2' => __('Have pets'));
                $pets_key= array_keys($pets);
                $pets_random_keys = array_rand($pets_key);

                // live_with
                $live_with= array('1' => __('Alone'),'2' => __('Parents'),'3' => __('Friends'),'4' => __('Partner'),'5' => __('Children'),'6' => __('Other'));
                $live_with_key= array_keys($live_with);
                $live_with_random_keys = array_rand($live_with_key);

                // religion
                $religion= array('1' => __('muslim'),'2' => __('Atheist'),'3' => __('Buddhist'),'4' => __('Catholic'),'5' => __('Christian'),'6' => __('Hindu'),'7' => __('Jewish'),'8' => __('Agnostic'),'9' => __('Sikh'),'10' => __('Other'));
                $religion_key =array_keys($religion);
                $religion_random_keys= array_rand($religion_key);

                // smoke
                $smoke = array('1' => __('Never'),'2' => __('I smoke sometimes'),'3' => __('Chain Smoker'));
                $smoke_key= array_keys($smoke);
                $smoke_random_keys= array_rand($smoke_key);

                // drink

                $drink = array('1' => __('Never'),'2' => __('I drink sometimes'));
                $drink_key = array_keys($drink);
                $drink_random_keys= array_rand($drink_key);


                // travel
                $travel = array('1' => __('Yes, all the time'),'2' => __('Yes, sometimes'),'3' => __('Not very much'),'4' => __('No'));
                $travel_key= array_keys($travel);
                $travel_random_keys = array_rand($travel_key);

//                 // music

                $music= array("Blues","Classic Rock","Country","Dance","Disco","Funk","Grunge","Hip-Hop","Jazz","Metal","New Age","Oldies","Other","Pop","R&B","Rap","Reggae","Rock","Techno","Industrial","Alternative","Ska","Death Metal","Pranks","Soundtrack","Euro-Techno","Ambient","Trip-Hop","Vocal","Jazz+Funk","Fusion","Trance","Classical","Instrumental","Acid","House","Game","Sound Clip","Gospel","Noise","AlternRock","Bass","Soul","Punk","Space","Meditative","Instrumental Pop","Instrumental Rock","Ethnic","Gothic","Darkwave","Techno-Industrial","Electronic","Pop-Folk","Eurodance","Dream","Southern Rock","Comedy","Cult","Gangsta","Top 40","Christian Rap","Jungle","Native American","Cabaret","New Wave","Psychadelic","Rave","Showtunes","Trailer","Lo-Fi","Tribal","Acid Punk","Acid Jazz","Polka","Retro","Musical","Rock & Roll","Hard Rock","Folk","Folk-Rock","National Folk","Swing","Fast Fusion","Bebob","Latin","Revival","Celtic","Bluegrass","Avantgarde","Gothic Rock","Progressive Rock","Psychedelic Rock","Symphonic Rock","Slow Rock","Big Band","Chorus","Easy Listening","Acoustic","Humour","Speech","Chanson","Opera","Chamber Music","Sonata","Symphony","Booty Bass","Primus","Porn Groove","Satire","Slow Jam","Club","Tango","Samba","Folklore","Ballad","Power Ballad","Rhythmic Soul","Freestyle","Duet","Punk Rock","Drum Solo","Acapella","Euro-House","Dance Hall");
                $music_random_keys=array_rand($music);
                $music_random_keys_1= $music[$music_random_keys];

//                 // dishes

                $dishes= array("Mexican","Malaysian","Lebanese","Thai","Singapore","Italian");
                $dishes_random_keys = array_rand($dishes);
                $dishes_random_keys_1= $dishes[$dishes_random_keys];

                   // songs
                $song= array("GOOD 4 U","BAD HABITS","SAVE YOUR TEARS","MONTERO","LEVITATING","BODY","WELLERMAN","FRIDAY","BLINDING LIGHTS","THE BUSINESS","BED","WITHOUT YOU","KISS ME MORE","HEAD and HEART","LITTLE BIT OF LOVE","PEACHES","HEAT WAVES","STAY","DON'T PLAY","LATEST TRENDS","LETS GO HOME TOGETHER","YOUR LOVE ","HEARTBREAK ANTHEM","GOOSEBUMPS","BLACK MAGIC","PARADISE","GET OUT MY HEAD","DEJA VU","ASTRONAUT IN THE OCEAN","SOMEONE YOU LOVED","SWEET MELODY","GOOD WITHOUT","CALLING MY PHONE","I WANNA BE YOUR SLAVE","MOOD","DANCE MONKEY","WATERMELON SUGAR","REMEMBER","STREETS");
                $song_random_keys = array_rand($song);
                $song_random_keys_1= $song[$song_random_keys];


                // Hobby
                $hobby=array(
                    '3D printing' => __('3D printing'),
                    'Acroyoga' => __('Acroyoga'),
                    'Acting' => __('Acting'),
                    'Aerial silk' => __('Aerial silk'),
                    'Airbrushing' => __('Airbrushing'),
                    'Amateur radio' => __('Amateur radio'),
                    'Animation' => __('Animation'),
                    'Anime' => __('Anime'),
                    'Aquascaping' => __('Aquascaping'),
                    'Art' => __('Art'),
                    'Astrology' => __('Astrology'),
                    'Baking' => __('Baking'),
                    'Barbershop Music' => __('Barbershop Music'),
                    'Baton twirling' => __('Baton twirling'),
                    'Beatboxing' => __('Beatboxing'),
                    'Beer tasting' => __('Beer tasting'),
                    'Bell ringing' => __('Bell ringing'),
                    'Binge-watching' => __('Binge-watching'),
                    'Blogging' => __('Blogging'),
                    'Board/tabletop games' => __('Board/tabletop games'),
                    'Book discussion clubs' => __('Book discussion clubs'),
                    'Book restoration' => __('Book restoration'),
                    'Bowling' => __('Bowling'),
                    'Brazilian jiu-jitsu' => __('Brazilian jiu-jitsu'),
                    'Breadmaking' => __('Breadmaking'),
                    'Bullet journaling' => __('Bullet journaling'),
                    'Calligraphy' => __('Calligraphy'),
                    'Candle making' => __('Candle making'),
                    'Candy making' => __('Candy making'),
                    'Car fixing building' => __('Car fixing building'),
                    'Card games' => __('Card games'),
                    'Cardistry' => __('Cardistry'),
                    'Ceramics' => __('Ceramics'),
                    'Chatting' => __('Chatting'),
                    'Cheesemaking' => __('Cheesemaking'),
                    'Chess' => __('Chess'),
                    'Cleaning' => __('Cleaning'),
                    'Clothesmaking' => __('Clothesmaking'),
                    'Coffee roasting' => __('Coffee roasting'),
                    'Collecting' => __('Collecting'),
                    'Coloring' => __('Coloring'),
                    'Flag football' => __('Flag football'),
                    'Flower growing' => __('Flower growing'),
                    'Flying' => __('Flying'),
                    'Flying disc' => __('Flying disc'),
                    'Flying model planes' => __('Flying model planes'),
                    'Foraging' => __('Foraging'),
                    'Fossicking' => __('Fossicking'),
                    'Freestyle football' => __('Freestyle football'),
                    'Fruit picking' => __('Fruit picking'),
                    'Gardening' => __('Gardening'),
                    'Geocaching' => __('Geocaching'),
                    'Ghost hunting' => __('Ghost hunting'),
                    'Gold prospecting' => __('Gold prospecting'),
                    'Graffiti' => __('Graffiti'),
                    'Groundhopping' => __('Groundhopping'),
                    'Guerrilla gardening' => __('Guerrilla gardening'),
                    'Gymnastics' => __('Gymnastics'),
                    'Handball' => __('Handball'),
                    'Herbalism' => __('Herbalism'),
                    'Herping' => __('Herping'),
    
                );
                $hobby_random_keys = array_rand($hobby);
                $hobby_random_keys_1 = $hobby[$hobby_random_keys];
          

                // sport
                $sports= array("soccer","basketball","tennis","baseball","golf","running","volleyball","badminton","swimming","boxing","table tennis","skiing","ice skating","roller skating","cricket","rugby","pool","darts","football","bowling","ice hockey","surfing","karate","horse racing","snowboarding","skateboarding","cycling","archery","fishing","gymnastics","figure skating","rock climbing","sumo wrestling","taekwondo","fencing","water skiing","jet skiing","weight lifting","scuba diving","judo","wind surfing","kickboxing","sky diving","hang gliding","bungee jumping");
                $sports_random_keys = array_rand($sports);
                $sports_random_keys_1 = $sports[$sports_random_keys];

                // city

                $city= array("LONDON","St Petersburg","BERLIN","MADRID","ROMA","KIEV","PARIS","BUCURESTI (Bucharest)","BUDAPEST","Hamburg","MINSK","WARSZAWA (Warsaw)","BEOGRAD (Belgrade)","WIEN (Vienna)","Kharkov","Barcelona","Novosibirsk","Nizhny Novgorod","Milano (Milan)","Ekaterinoburg","München (Munich)","PRAHA (Prague)","Samara","Omsk","SOFIA","Dnepropetrovsk","Kazan","Ufa","Chelyabinsk","Donetsk","Napoli (Naples)","Birmingham","Perm","Rostov-na-Donu","Odessa","Volgograd","Köln (Cologne)","Torino (Turin)","Voronezh","Krasnoyarsk","Saratov","ZAGREB","Zaporozhye","Lódz","Marseille","RIGA","Lvov","ATHINAI (Athens)","Salonika","STOCKHOLM","Kraków","Valencia","AMSTERDAM","Leeds","Tolyatti","Kryvy Rig","Sevilla","Palermo","Ulyanovsk","KISHINEV","Genova","Izhevsk","Frankfurt am Main","Krasnodar","Wroclaw (Breslau)","Glasgow","Yaroslave","Khabarovsk","Vladivostok","Zaragoza","Essen","Rotterdam","Irkutsk","Dortmund","Stuttgart","Barnaul","VILNIUS","Poznan","Düsseldorf","Novokuznetsk","LISBOA (Lisbon)","HELSINKI","Málaga","Bremen","Sheffield","SARAJEVO","Penza","Ryazan","Orenburg","Naberezhnye Tchelny","Duisburg","Lipetsk","Hannover","Mykolaiv","Tula","OSLO","Tyumen","KOBENHAVN (Copenhagen)","Kemerovo");
                $city_random_keys = array_rand($city);
                $city_random_keys_1 = $city[$city_random_keys];

                // books

                $books= array("Fighting the Flying Circus","Some Still Live","The Last Enemy","West with the Night","Enemy Coast Ahead","So Away I Went!","The Big Show (Le Grand Cirque)","The First and the Last","I Flew for the Führer","Fighter Over Finland (Hävittäjälentäjänä Kahdessa Sodassa)","Wing Leader","Samurai!","Baa Baa Black Sheep","Nine Lives","Wings on My Sleeve","Sky Fever: The Autobiography of Sir Geoffrey de Havilland","Night Fighter","War in a Stringbag","A Thousand Shall Fall","Sole Survivor","Herman the German: Enemy Alien U.S. Army Master Sergeant","Not Much of an Engineer","The Al Mooney Story: They All Fly Through the Same Air","Yeager: An Autobiography","Jackie Cochran: An Autobiography","From the Ground Up","Kelly: More Than My Share of It All","I Could Never Be So Lucky Again","Eagle's Wings","Skunk Works: A Personal Memoir of My Years at Lockheed","Spitfire: A Test Pilot's Story","Failure Is Not an Option: Mission Control from Mercury to Apollo 13 and Beyond","Rock This","Confessions of a Pretty Lady","Random Acts of Badness","Cancer Schmancer","How Men Have Babies: a New Father's Survival Guide","It's Not Easy Being Me: a Lifetime of No Respect But Plenty of Sex and Drugs (published posthumously)","Hollywood Causes Cancer","Bigger Than Hitler & Better Than Christ","The I Chong: Meditations From the Joint","How to Raise Kids Who Won't Hate You","Born Standing Up","Why We Suck","Ernie: The Autobiography","My Shit Life So Far","American on Purpose","Killing Willis, AKA Thatswhatimtalkinbout","Tough Sh*t: Life Advice from a Fat, Lazy Slob Who Still Made Good","Dyn-o-mite!","The Filthy Truth","I Know Nothing (But Here's What I've Learned)","So, Anyway...","It Sure Beats Working","Bring On The Empty Horses","Joyce Grenfell Requests the Pleasure","Tall, Dark & Gruesome","Dear Me","An Actor's Life","When the Smoke Hit the Fan","Happy Trails","Little Girl Lost","It Would Be So Nice If You Weren't Here: My Journey Through Show Business","Blind in One Ear","Once Before I Go","Which Reminds Me","Wildelife (published posthumously)","The Days of My Life","Accidentally on Purpose");
                $books_random_keys = array_rand($books);
                $books_random_keys_1 = $books[$books_random_keys];

                // movie 
                $movielist = array("Hide in the Light","After Effect","The Detained","#SquadGoals","Await Further Instructions","Haunting on Fraternity Row","Lasso","Trauma","The Toybox","Inoperable","Killer Kate!","Sick for Toys","Ouija House","Petrified","The Jurassic Games","Triassic World","Realms","Mega Shark vs. Mecha Shark","Ozark Sharks","Atomic Shark","Selfie from Hell","Santa Jaws","Deep Blue Sea 2","Tremors: A Cold Day in Hell","Cucuy: The Boogeyman","Broken Ghost","President Evil","Dear God No!","Dead Kansas","Malevolence 3: Killer","Fake Blood","Fox Trap","Mother Krampus","Unhinged","Party Hard Die Young","Black Hollow Cage","Perfect","Sleepless Nights","Hi-Death","Spirits","Stray","Bonehill Road","Don't Speak","Wolfman's Got Nards","Out of the Shadows","Inner Ghosts","Sleep Has Her House","Creep","In a Stranger's House","The Cleaning Lady","Framed","Aterrados","Nightmare Cinema","Cutterhead","Hell Is Where the Home Is","Endzeit","Dachra","Endzeit","Patient 001","Betsy","The Dark Tapes","Destruction Los Angeles","Curvature","Inside","Die in One Day","Demon Tongue","Solis","The Crucifixion","Cynthia","Skin Creepers","Boarding School","Writers Retreat","Dead Sea","Wrecker","The Rake","The Field Guide to Evil","Primal Rage","The Heretics","Nevesta");
                $movie_random_keys = array_rand($movielist);
                $movie_random_keys_1 = $movielist[$movie_random_keys];

                // Tv show 
                $tvshow =array("Lake Placid: Legacy","Megalodon","6-Headed Shark Attack","5 Headed Shark Attack","Sharknado 5: Global Swarming","The Last Sharknado: It's About Time","Sharknado 3: Oh Hell No!","Sharknado 4: The 4th Awakens","Ice Sharks","Dam Sharks","Toxic Shark","Planet of the Sharks","Trailer Park Shark","Nightmare Shark","Saltwater","Mississippi River Sharks","Frenzy","Karma","No Escape Room","House of the Witch","Empire of the Sharks","Strange Events","Lake Placid vs. Anaconda","Night of the Wild","Woensdag Gehaktdag","Embrión","Megan","Everything & Everything & Everything","Velvet Road","Polaroid");
                $tvshow_random_keys = array_rand($tvshow);
                $tvshow_random_keys_1 = $tvshow[$tvshow_random_keys];


                
                $re_data['address']         = $faker->address;
                $re_data['facebook']        = $faker->company;
                $re_data['google']          = $faker->company;
                $re_data['twitter']         = $faker->company;
                $re_data['linkedin']        = $faker->company;
                $re_data['website']         = $faker->company;
                $re_data['instagram']       = $faker->company;
                $re_data['language']        = 'english';
                $re_data['type']            = 'user';
                $re_data['phone_number']    = $faker->phoneNumber;
                $re_data['timezone']        = 'UTC';
                $re_data['start_up']        = '3';
                $re_data['height']          = '152';
                $re_data['hair_color']      = $hair_random_keys;
                $re_data['interest']        = 'Sint autem inventore aut officia';
                $re_data['location']        = 'Ducimus';
                $re_data['relationship']    = $relationship_random_keys;
                $re_data['work_status']     = $work_status_random_keys;
                $re_data['education']       = $education_random_keys;
                $re_data['ethnicity']       = $ethnicity_random_keys;
                $re_data['body']            = $body_random_keys;
                $re_data['character']       = $character_random_keys;
                $re_data['children']        = $children_random_keys;
                $re_data['friends']         = $friends_random_keys;
                $re_data['pets']            = $pets_random_keys;
                $re_data['live_with']       = $live_with_random_keys;
                $re_data['car']             = '2';
                $re_data['religion']        = $religion_random_keys;
                $re_data['smoke']           = $smoke_random_keys;
                $re_data['drink']           = $drink_random_keys;
                $re_data['travel']          = '2';
                $re_data['music']           = $music_random_keys_1;
                $re_data['dish']            = $dishes_random_keys_1;
                $re_data['song']            = strtolower($song_random_keys_1);
                $re_data['hobby']           = $hobby_random_keys_1;
                $re_data['city']            = $city_random_keys_1;
                $re_data['sport']           = $sports_random_keys_1;
                $re_data['book']            = $books_random_keys_1;
                $re_data['movie']           = $movie_random_keys_1;
                $re_data['colour']          = $faker->colorName;
                $re_data['tv']              = $tvshow_random_keys_1;
                $re_data['privacy_show_profile_on_google']      = 1;
                $re_data['privacy_show_profile_random_users']   = 1;
                $re_data['privacy_show_profile_match_profiles'] = 1;
                $re_data['phone_verified']                      = 1;
                $re_data['online']                              = 1;

                $regestered_user = $users->register($re_data);
            }

        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    
    
    
    if ($s == 'add_new_announcement') {
        if (!empty($_POST['announcement_text'])) {
            $html = '';
            $id   = Wo_AddNewAnnouncement(base64_decode($_POST['announcement_text']));
            if ($id > 0) {
                $wo['activeAnnouncement'] = Wo_GetAnnouncement($id);
                $html .= Wo_LoadAdminPage('manage-announcements/active-list', false);
                $data = array(
                    'status' => 200,
                    'text' => $html
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_announcement') {
        if (!empty($_GET['id'])) {
            $DeleteAnnouncement = Wo_DeleteAnnouncement($_GET['id']);
            if ($DeleteAnnouncement === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'disable_announcement') {
        if (!empty($_GET['id'])) {
            $html                = '';
            $DisableAnnouncement = Wo_DisableAnnouncement(Secure($_GET['id']));
            if ($DisableAnnouncement === true) {
                $wo['inactiveAnnouncement'] = Wo_GetAnnouncement(Secure($_GET['id']));
                $html .= Wo_LoadAdminPage('manage-announcements/inactive-list', false);
                $data = array(
                    'status' => 200,
                    'html' => $html
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'activate_announcement') {
        if (!empty($_GET['id'])) {
            $html                 = '';
            $ActivateAnnouncement = Wo_ActivateAnnouncement(Secure($_GET['id']));
            if ($ActivateAnnouncement === true) {
                $wo['activeAnnouncement'] = Wo_GetAnnouncement($_GET['id']);
                $html .= Wo_LoadAdminPage('manage-announcements/active-list', false);
                $data = array(
                    'status' => 200,
                    'html' => $html
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_country') {
        if (Wo_CheckSession($hash_id) === true) {
            $insert_data = array();
            $insert_data['ref'] = 'country';
            $add = false;
            foreach (Wo_LangsNamesFromDB() as $wo['key_']) {
                if (!empty($_POST[$wo['key_']])) {
                    $insert_data[$wo['key_']] = Secure($_POST[$wo['key_']]);
                    $add = true;
                }
            }
            if ($add == true) {
                $insert_data['options'] = Secure($_POST['options']);
                $id = $db->insert('langs', $insert_data);
                if (!empty($_POST['lang_key'])) {
                    $db->where('id', $id)->update('langs', array('lang_key' => Secure($_POST['lang_key'])));
                }else{
                    $db->where('id', $id)->update('langs', array('lang_key' => $id));
                }

                $data['status'] = 200;
            } else {
                $data['status'] = 400;
                $data['message'] = 'please check details';
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_country') {
        header("Content-type: application/json");
        if (!empty($_GET['key']) && in_array($_GET['key'], array_keys(Dataset::countries('id')))) {
                $db->where('id',Secure($_GET['key']))->delete('langs');
                $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_verification') {
        header("Content-type: application/json");
        if (!empty($_GET['id']) && $_GET['id'] > 0) {
            $db->where('id',Secure($_GET['id']))->delete('verification_requests');
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'verify_user' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $type = '';
            if (Wo_VerifyUser($_GET['id'], $_GET['verification_id']) === true) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_gender') {
        if (Wo_CheckSession($hash_id) === true) {
            $insert_data = array();
            $insert_data['ref'] = 'gender';
            $add = false;
            foreach (Wo_LangsNamesFromDB() as $wo['key_']) {
                if (!empty($_POST[$wo['key_']])) {
                    $insert_data[$wo['key_']] = Secure($_POST[$wo['key_']]);
                    $add = true;
                }
            }
            if ($add == true) {
                $id = $db->insert('langs', $insert_data);
                $db->where('id', $id)->update('langs', array('lang_key' => $id));
                $data['status'] = 200;
            } else {
                $data['status'] = 400;
                $data['message'] = 'please check details';
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_gender') {
        header("Content-type: application/json");
        if (!empty($_GET['key']) && in_array($_GET['key'], array_keys(Dataset::gender()))) {
            if((int)$_GET['key'] == 4526 || (int)$_GET['key'] == 4525 ){
                $data['status'] = 300;
            }else {
                $db->where('lang_key',Secure($_GET['key']))->delete('langs');
                $data['status'] = 200;
            }
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_page' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
            $page_name    = Secure($_POST['page_name']);
            $page_content = Secure($_POST['page_content']);
            $page_title   = Secure($_POST['page_title']);
            $page_type    = 0;
            if (!empty($_POST['page_type'])) {
                $page_type = 1;
            }
            if (!preg_match('/^[\w]+$/', $page_name)) {
                $data = array(
                    'status' => 400,
                    'message' => 'Invalid page name characters'
                );
                header("Content-type: application/json");
                echo json_encode($data);
                exit();
            }
            $data_ = array(
                'page_name' => $page_name,
                'page_content' => $page_content,
                'page_title' => $page_title,
                'page_type' => $page_type
            );
            $add   = Wo_RegisterNewPage($data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'edit_page' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_POST['page_id']) && !empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
            $page_name    = $_POST['page_name'];
            $page_content = $_POST['page_content'];
            $page_title   = $_POST['page_title'];
            $page_type    = 0;
            if (!empty($_POST['page_type'])) {
                $page_type = 1;
            }
            if (!preg_match('/^[\w]+$/', $page_name)) {
                $data = array(
                    'status' => 400,
                    'message' => 'Invalid page name characters'
                );
                header("Content-type: application/json");
                echo json_encode($data);
                exit();
            }
            $data_ = array(
                'page_name' => $page_name,
                'page_content' => $page_content,
                'page_title' => $page_title,
                'page_type' => $page_type
            );
            $add   = Wo_UpdateCustomPageData($_POST['page_id'], $data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_page' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $delete = Wo_DeleteCustomPage($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_field') {
        if (Wo_CheckSession($hash_id) === true && !empty($_POST['name']) && !empty($_POST['type']) && !empty($_POST['description'])) {
            $type              = Secure($_POST['type']);
            $name              = Secure($_POST['name']);
            $description       = Secure($_POST['description']);
            $registration_page = 0;
            if (!empty($_POST['registration_page'])) {
                $registration_page = 1;
            }
            $profile_page = 0;
            if (!empty($_POST['profile_page'])) {
                $profile_page = 1;
            }
            $length = 32;
            if (!empty($_POST['length'])) {
                if (is_numeric($_POST['length']) && $_POST['length'] < 1001) {
                    $length = Secure($_POST['length']);
                }
            }
            $placement_array = array(
                'profile',
                'general',
                'social',
                'none'
            );
            $placement       = 'profile';
            if (!empty($_POST['placement'])) {
                if (in_array($_POST['placement'], $placement_array)) {
                    $placement = Secure($_POST['placement']);
                }
            }
            $data_ = array(
                'name' => $name,
                'description' => $description,
                'length' => $length,
                'placement' => $placement,
                'registration_page' => $registration_page,
                'profile_page' => $profile_page,
                'active' => 1
            );
            if (!empty($_POST['options'])) {
                $options              = @explode("\n", $_POST['options']);
                $type                 = Secure(implode($options, ','));
                $data_['select_type'] = 'yes';
            }
            $data_['type'] = $type;
            $add           = RegisterNewField($data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'edit_field' && Wo_CheckSession($hash_id) === true ) {
        if (!empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['id'])) {
            $name              = Secure($_POST['name']);
            $description       = Secure($_POST['description']);
            $registration_page = 0;
            if (!empty($_POST['registration_page'])) {
                $registration_page = 1;
            }
            $profile_page = 0;
            if (!empty($_POST['profile_page'])) {
                $profile_page = 1;
            }
            $active = 0;
            if (!empty($_POST['active'])) {
                $active = 1;
            }
            $length = 32;
            if (!empty($_POST['length'])) {
                if (is_numeric($_POST['length'])) {
                    $length = Secure($_POST['length']);
                }
            }
            $placement_array = array(
                'profile',
                'general',
                'social',
                'none'
            );
            $placement       = 'profile';
            if (!empty($_POST['placement'])) {
                if (in_array($_POST['placement'], $placement_array)) {
                    $placement = Secure($_POST['placement']);
                }
            }
            $data_ = array(
                'name' => $name,
                'description' => $description,
                'length' => $length,
                'placement' => $placement,
                'registration_page' => $registration_page,
                'profile_page' => $profile_page,
                'active' => $active
            );
            if (!empty($_POST['options'])) {
                $options              = @explode("\n", $_POST['options']);
                $data_['type']        = implode($options, ',');
                $data_['select_type'] = 'yes';
            }
            $add = UpdateField($_POST['id'], $data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_field') {
        if (Wo_CheckSession($hash_id) === true && !empty($_GET['id'])) {
            $delete = DeleteField($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'free_gender_enable') {
        header("Content-type: application/json");
        if (!empty($_GET['key']) && in_array($_GET['key'], array_keys(Dataset::gender()))) {
            $db->where('lang_key',Secure($_GET['key']))->update('langs', array('options' => 1));
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'free_gender_disable') {
        header("Content-type: application/json");
        if (!empty($_GET['key']) && in_array($_GET['key'], array_keys(Dataset::gender()))) {
            $db->where('lang_key',Secure($_GET['key']))->update('langs', array('options' => NULL));
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'edit_new_success_story' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_POST['quote']) && !empty($_POST['content']) && !empty($_POST['id'])) {

            $id             = Secure($_POST['id']);
            $quote          = Secure($_POST['quote']);
            $story          = Secure(base64_decode($_POST['content']));

            $data_ = array(
                'quote' => $quote,
                'description' => $story
            );
            $add   = $db->where('id',$id)->update('success_stories', $data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_success_stories' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $delete = Wo_Deletesuccess_stories($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_success_stories' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $delete = Wo_Approvesuccess_stories($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'disapprove_success_stories' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $delete = Wo_DisApprovesuccess_stories($_GET['id']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_category') {
        if (Wo_CheckSession($hash_id) === true) {
            $insert_data = array();
            $insert_data['ref'] = 'blog_categories';
            $add = false;
            foreach (Wo_LangsNamesFromDB() as $wo['key_']) {
                if (!empty($_POST[$wo['key_']])) {
                    $insert_data[$wo['key_']] = Secure($_POST[$wo['key_']]);
                    $add = true;
                }
            }
            if ($add == true) {
                $id = $db->insert('langs', $insert_data);
                $db->where('id', $id)->update('langs', array('lang_key' => $id));
                $data['status'] = 200;
            } else {
                $data['status'] = 400;
                $data['message'] = 'please check details';
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_category') {
        header("Content-type: application/json");
        if (!empty($_GET['key']) && in_array($_GET['key'], array_keys(Dataset::blog_categories()))) {
            $db->where('lang_key',Secure($_GET['key']))->delete('langs');
            $data['status'] = 200;
        }
        echo json_encode($data);
        exit();
    }
    if ($s == 'add_new_blog_article' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_POST['category']) && !empty($_POST['title']) && !empty($_POST['description'])) {
            $category           = Secure($_POST['category']);
            $title              = Secure($_POST['title']);
            $description        = Secure($_POST['description']);
            $tags               = Secure($_POST['tags']);
            $content            = Secure(base64_decode($_POST['content']));

            $media_file = 'upload/photos/d-blog.jpg';
            if (isset($_FILES['thumbnail'])) {
                if (!empty($_FILES['thumbnail']["tmp_name"])) {
                    $filename = "";
                    $fileInfo = array(
                        'file' => $_FILES["thumbnail"]["tmp_name"],
                        'name' => $_FILES['thumbnail']['name'],
                        'size' => $_FILES["thumbnail"]["size"],
                        'type' => $_FILES["thumbnail"]["type"],
                        'types' => 'jpg,png,gif,jpeg'
                    );
                    $media = ShareFile($fileInfo, 0, false, 'blogs');
                    if (!empty($media)) {
                        $filename = $media['filename'];
                    }
                    $media_file = Secure($filename);
                }
            }
            $data_ = array(
                'title'         => $title,
                'content'       => $content,
                'description'   => $description,
                'category'      => $category,
                'tags'          => $tags,
                'thumbnail'     => $media_file,
                'created_at'    => time()
            );
            $add   = Wo_RegisterNewBlogPost($data_);
            if ($add) {
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'edit_blog_article' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_POST['id']) && !empty($_POST['category']) && !empty($_POST['title']) && !empty($_POST['description'])) {

            $id                 = Secure($_POST['id']);
            $category           = Secure($_POST['category']);
            $title              = Secure($_POST['title']);
            $description        = Secure($_POST['description']);
            $tags               = Secure($_POST['tags']);
            $content            = base64_decode($_POST['content']);

            $article            = Wo_GetArticle($id);
            $remove_prev_img    = false;
            $old_thumb          = $article['thumbnail'];
            if (isset($_FILES['thumbnail'])) {
                if (!empty($_FILES['thumbnail']["tmp_name"])) {
                    $filename = "";
                    $fileInfo = array(
                        'file' => $_FILES["thumbnail"]["tmp_name"],
                        'name' => $_FILES['thumbnail']['name'],
                        'size' => $_FILES["thumbnail"]["size"],
                        'type' => $_FILES["thumbnail"]["type"],
                        'types' => 'jpg,png,gif,jpeg'
                    );
                    $media = ShareFile($fileInfo, 0, false, 'blogs');
                    if (!empty($media)) {
                        $filename = $media['filename'];
                        $remove_prev_img    = true;
                    }
                    $media_file = Secure($filename);
                }
            }else{
                $media_file = $article['thumbnail'];
            }

            $data_ = array(
                'title'         => $title,
                'content'       => $content,
                'description'   => $description,
                'category'      => $category,
                'tags'          => $tags,
                'thumbnail'     => $media_file
            );
            $add   = $db->where('id',$id)->update('blog', $data_);
            if ($add) {
                if( $old_thumb !== '' && $remove_prev_img == true ) {
                    DeleteFromToS3($old_thumb);
                }
                $data['status'] = 200;
            }
        } else {
            $data = array(
                'status' => 400,
                'message' => 'Please fill all the required fields'
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'delete_blog_article' && Wo_CheckSession($hash_id) === true) {
        if (!empty($_GET['id'])) {
            $delete = Wo_DeleteArticle($_GET['id'], $_GET['thumbnail']);
            if ($delete) {
                $data = array(
                    'status' => 200
                );
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'approve_user') {
        if (!empty($_GET['user_id'])) {
            $_id = Secure($_GET['user_id']);
            $receipt = $db->where('id',$_id)->getOne('users',array('*'));

            if($receipt){
                $updated = $db->where('id',$_id)->update('users',array('verified'=>"1",'status'=>"1",'approved_at'=>time()));
                if ($updated === true) {
                    $data = array(
                        'status' => 200
                    );
                }
            }
            $data = array(
                'status' => 200,
                'data' => $receipt
            );
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'decline_payment') {
        if (!empty($_GET['id']) && Wo_CheckSession($hash_id)) {
            $get_payment_info = Wo_GetPaymentHistory($_GET['id']);
            if (!empty($get_payment_info)) {
                $id     = $get_payment_info['id'];
                $update = mysqli_query($conn, "UPDATE `affiliates_requests` SET status = '2' WHERE id = {$id}");
                if ($update) {
                    $message_body = Emails::parse('emails/payment-declined', array(
                        'name' => ($user[ 'first_name' ] !== '' ? $get_payment_info['user']->first_name : $get_payment_info['user']->username),
                        'amount' => $get_payment_info['amount'],
                        'site_name' => $wo['config']['siteName']
                    ));
                    $send_message_data = array(
                        'from_email' => $wo['config']['siteEmail'],
                        'from_name' => $wo['config']['siteName'],
                        'to_email' => $get_payment_info['user']->email,
                        'subject' => 'Payment Declined | ' . $wo['config']['siteName'],
                        'charSet' => 'utf-8',
                        'message_body' => $message_body,
                        'is_html' => true
                    );
                    $send_message      = SendEmail($send_message_data['to_email'], $send_message_data['subject'], $send_message_data['message_body'], false);
                    $data['status'] = 200;

                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
    if ($s == 'mark_as_paid') {
        if (!empty($_GET['id']) && Wo_CheckSession($hash_id)) {
            $get_payment_info = Wo_GetPaymentHistory($_GET['id']);
            if (!empty($get_payment_info)) {
                $id     = $get_payment_info['id'];
                $update = mysqli_query($conn, "UPDATE `affiliates_requests` SET status = '1' WHERE id = {$id}");
                if ($update) {
                    $message_body = Emails::parse('emails/payment-sent', array(
                        'name' => ($user[ 'first_name' ] !== '' ? $get_payment_info['user'][ 'first_name' ] : $get_payment_info['user'][ 'username' ]),
                        'amount' => $get_payment_info['amount'],
                        'site_name' => $config['siteName']
                    ));
                    $send_message_data = array(
                        'from_email' => $wo['config']['siteEmail'],
                        'from_name' => $wo['config']['siteName'],
                        'to_email' => $get_payment_info['user']['email'],
                        'to_name' => $get_payment_info['user']['name'],
                        'subject' => 'Payment Declined | ' . $wo['config']['siteName'],
                        'charSet' => 'utf-8',
                        'message_body' => $message_body,
                        'is_html' => true
                    );
                    $send_message      = SendEmail($send_message_data['to_email'], $send_message_data['subject'], $send_message_data['message_body'], false);
                    if ($send_message) {
                        $data['status'] = 200;
                    }
                }
            }
        }
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
mysqli_close($conn);
unset($wo);