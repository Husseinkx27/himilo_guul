<?php
require_once __DIR__ . '/../config.php';

function ensure_upload_dir($path){
    if (!is_dir($path)) mkdir($path,0755,true);
}

function create_unique_filename($prefix,$original){
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $name = $prefix . '_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    return $name;
}

function is_allowed_image($type){
    $allow = ['image/jpeg','image/png','image/gif','image/webp'];
    return in_array($type, $allow);
}

function create_thumbnail($srcPath,$destPath,$maxW=800,$maxH=600){
    // If GD is not available, avoid fatal error and fallback to copying the original image
    if (!function_exists('imagecreatetruecolor')){
        error_log('[uploads] GD functions not available; skipping thumbnail generation.');
        // try a best-effort copy so downstream code still has an image file
        if (@copy($srcPath, $destPath)) return true;
        return false;
    }

    $info = getimagesize($srcPath);
    if (!$info) return false;
    list($w,$h) = $info;
    $ratio = min($maxW/$w, $maxH/$h, 1);
    $nw = (int)($w * $ratio);
    $nh = (int)($h * $ratio);
    $dst = imagecreatetruecolor($nw,$nh);
    $mime = $info['mime'];
    switch ($mime){
        case 'image/jpeg': $src = imagecreatefromjpeg($srcPath); break;
        case 'image/png': $src = imagecreatefrompng($srcPath); break;
        case 'image/gif': $src = imagecreatefromgif($srcPath); break;
        case 'image/webp': $src = imagecreatefromwebp($srcPath); break;
        default: return false;
    }
    imagecopyresampled($dst,$src,0,0,0,0,$nw,$nh,$w,$h);
    // Save as JPEG to dest path
    imagejpeg($dst,$destPath,85);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}

function store_business_images($files,$business_id){
    global $pdo;
    $saved = [];
    $dir = UPLOAD_DIR . '/business_' . intval($business_id);
    ensure_upload_dir($dir);
    // normalize files array for multiple uploads
    $fileCount = is_array($files['name']) ? count($files['name']) : 0;
    for ($i=0;$i<$fileCount;$i++){
        $error = $files['error'][$i];
        if ($error !== UPLOAD_ERR_OK) continue;
        $tmp = $files['tmp_name'][$i];
        $type = mime_content_type($tmp);
        if (!is_allowed_image($type)) continue;
        if (filesize($tmp) > MAX_UPLOAD_SIZE) continue;
        $origName = $files['name'][$i];
        $fn = create_unique_filename('biz'.$business_id, $origName);
        $dest = $dir . '/' . $fn;
        if (move_uploaded_file($tmp, $dest)){
            // create thumbnail path
            $thumb = $dir . '/thumb_' . $fn;
            create_thumbnail($dest, $thumb);
            // store relative path for DB
            $relative = 'uploads/business_' . intval($business_id) . '/' . $fn;
            $ins = $pdo->prepare('INSERT INTO business_images (business_id,file_path,alt_text) VALUES (:b,:f,:a)');
            $ins->execute([':b'=>$business_id,':f'=>$relative,':a'=>'']);
            $saved[] = $relative;
        }
    }
    return $saved;
}

function store_profile_image($file,$user_id){
    global $pdo;
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $tmp = $file['tmp_name'];
    $type = mime_content_type($tmp);
    if (!is_allowed_image($type)) return null;
    if (filesize($tmp) > MAX_UPLOAD_SIZE) return null;
    $dir = UPLOAD_DIR . '/profiles_' . intval($user_id);
    ensure_upload_dir($dir);
    $origName = $file['name'];
    $fn = create_unique_filename('user'.$user_id, $origName);
    $dest = $dir . '/' . $fn;
    if (move_uploaded_file($tmp, $dest)){
        $thumb = $dir . '/thumb_' . $fn;
        create_thumbnail($dest, $thumb, 300,300);
        return 'uploads/profiles_' . intval($user_id) . '/' . $fn;
    }
    return null;
}
?>