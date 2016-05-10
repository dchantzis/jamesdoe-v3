<?php
/*####################################################
	uploadToFileserver($field_name,$upload_dir_path,$partial_file_name,$page_id),
	deleteFromFileserver($old_file_url,$page_id,$upload_dir_path),
	upload_to_database($field_name,$page_id)
####################################################*/

//function that uploads image with name $field_name, to directory $uploaddir
//$page_id --> page to redirect if something fails
function uploadToFileserver($field_name,$upload_dir_path,$partial_file_name,$page_id,$file_kind)
{
	if($_FILES[$field_name]['size'] > 0)
	{
		if($_FILES[$field_name]['size'] > IMAGES_MAX_FILESIZE){ $file['error'] = 192; } //{redirects($page_id,'?flg=107'); }
		else
		{
			if(strtolower(PRESERVE_ORIGINAL_IMAGE_FILETYPE) == 'false')
				{$_FILES[$field_name]['name'] = removeExtension($_FILES[$field_name]['name']) . '.' . UPLOADED_IMAGES_FILETYPE;}
			
			$file['filename'] = strtolower(str_replace(' ','',$_FILES[$field_name]['name']));
			$file['tmpname'] = $_FILES[$field_name]['tmp_name'];
			$file['filesize'] = $_FILES[$field_name]['size'];
			$file['mimetype'] = $_FILES[$field_name]['type'];

			//if we want the name of the file to be: 'timestamp'+'filename', use the following
			$timestamp = date("Ymd") . date("His");
			$file['fileurl'] = $timestamp . $partial_file_name . strtolower($file['filename']);
			
			if(strtolower($file_kind) == 'image')
			{
				$uploadfilefullresolution = createDirectory($upload_dir_path,IMAGES_FOLDER_FULL_RESOLUTION) . '/' . $file['fileurl'];
				$uploadfilethumbnail = createDirectory($upload_dir_path,IMAGES_FOLDER_THUMBNAILS) . '/' . $file['fileurl'];
				$uploadfilelargethumbnail = createDirectory($upload_dir_path,IMAGES_FOLDER_LARGE_THUMBNAILS) . '/' . $file['fileurl'];
				
				$image = openImage($file['tmpname']);
				//create the full resolution version of the image
				$imagefullresolution = imageResize($image,'fullresolution');
				$uploadfilefullresolution = removeExtension($uploadfilefullresolution);
				//create the thumbnail version of the image
				$imagethumbnail = imageResize($image,'thumbnail');
				$uploadfilethumbnail = removeExtension($uploadfilethumbnail);
				//create the large thumbnail version of the image
				$imagelargethumbnail = imageResize($image,'largethumbnail');
				$uploadfilelargethumbnail = removeExtension($uploadfilelargethumbnail);
				
				if(strtolower(PRESERVE_ORIGINAL_IMAGE_FILETYPE) == 'true'){$tmp = findFileExtension($file['filename']);}//do nothing upload image as normal file
				elseif(strtolower(PRESERVE_ORIGINAL_IMAGE_FILETYPE) == 'false'){$tmp = ''; $file['fileurl'] = removeExtension($file['fileurl']) . '.' . UPLOADED_IMAGES_FILETYPE; }//elseif
				
				if(uploadImage($imagefullresolution,$uploadfilefullresolution,$tmp)){}//ALL OK //do nothing
				else{$file['error']=108;}//{redirects($page_id,'?flg=108');}					
				if(uploadImage($imagethumbnail,$uploadfilethumbnail,$tmp)){}//ALL OK//do nothing
				else{$file['error']=108;}//{redirects($page_id,'?flg=108');}	
				if(uploadImage($imagelargethumbnail,$uploadfilelargethumbnail,$tmp)){}//ALL OK//do nothing
				else{$file['error']=108;}//{redirects($page_id,'?flg=108');}
								
				imagedestroy($imagefullresolution);
				imagedestroy($imagethumbnail);
				imagedestroy($imagelargethumbnail);
				return $file;
			}//if
			else {
				//this is used to upload filetypes that are not images.
				$uploadfile = $upload_dir_path . '/' . $file['fileurl'];
				if (move_uploaded_file($file['tmpname'], $uploadfile)) {} //ok! file uploaded successfully.
				else { redirects($page_id,'?flg=108'); }//else //error. file not uploaded.
				return $file; //array
			}//else
		}//else		
	}//if
	else{ $file['error']=103; return $file; }
	//{redirects($page_id,'?flg=103');}//echo "no file was selected";
}//uploadToFileserver($field_name,$upload_dir_path,$partial_file_name,$page_id,$file_kind)

//function that deletes files from fileserver
//$page_id --> page to redirect if something fails
function deleteFromFileserver($fileURL)
{
	$dirHandle = @opendir(IMAGES_UPLOAD_DIR);
	if(!$dirHandle){return 0;}
	
	if($fileURL != "" )
	{
		if(file_exists(IMAGES_UPLOAD_DIR.IMAGES_FOLDER_FULL_RESOLUTION."/".$fileURL))
		{
			unlink (IMAGES_UPLOAD_DIR.IMAGES_FOLDER_FULL_RESOLUTION."/".$fileURL);
			unlink (IMAGES_UPLOAD_DIR.IMAGES_FOLDER_THUMBNAILS."/".$fileURL);
			unlink (IMAGES_UPLOAD_DIR.IMAGES_FOLDER_LARGE_THUMBNAILS."/".$fileURL);
			return 1;
		}//if
		else{return 0;}
	}//if
	else{return 0;}
}//deleteFromFileserver

//Upload a file to database
//$page_id --> page to redirect if something fails
function upload_to_database($field_name,$page_id)
{
	global $paper_upload_max_filesize; //set in sessioninitinc.php

	if($_FILES[$field_name]['size'] > 0)
	{
		if($_FILES[$field_name]['size'] > IMAGES_MAX_FILESIZE){ redirects($page_id,'?flg=107'); }//echo "error. file too large.";
		else
		{
			//OK!
			$file['filename'] = $_FILES[$field_name]['name'];
			$file['tmpname'] = $_FILES[$field_name]['tmp_name'];
			$file['filesize'] = $_FILES[$field_name]['size'];
			$file['filetype'] = $_FILES[$field_name]['type'];

			$fp = fopen($file['tmpname'], 'r');
			$file['filecontent'] = fread($fp, $file['filesize']);
			$file['filecontent'] = addslashes($file['filecontent']);
			fclose($fp);

			if(!get_magic_quotes_gpc()){ $file['filename'] = addslashes($file['filename']);}//if
			
			return $file; //array
		}//else		
	}//if
	else
	{
		//echo "no file was selected";
		Redirects($page_id,"?flg=103","");
	}//else
	
}//upload_to_database($field_name,$page_id)

//$parent_dir_path --> the path of the directory inside of which the new directory will be created.
//$child_dir_name --> the name of the new directory.
function createDirectory($parent_dir_path,$child_dir_name)
{
	$directory_mask = 0700;
	//check if a directory with this name already exists in the $parent_dir_path
	if(is_dir($parent_dir_path . $child_dir_name))
	{
		$full_path = $parent_dir_path . $child_dir_name . "/";	
	}//
	else
	{
		$full_path = $parent_dir_path . $child_dir_name . "/";	
		mkdir($full_path, $directory_mask);
		//Create directory with name $child_dir_name
	}//
	return ($full_path);
}//createDirectory()

function uploadImage($image,$uploadfile,$uploadtype)
{
	if($uploadtype == ''){$tmp = UPLOADED_IMAGES_FILETYPE;}
	else{$tmp = $uploadtype;}
	
	switch(strtolower($tmp))
	{
		case 'jpeg':
		case 'jpg':
			if(imagejpeg($image,$uploadfile . '.jpg',100)){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		case 'gif':
			if(imagegif($image,$uploadfile . '.gif')){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		case 'png':
			if(imagepng($image,$uploadfile . '.png')){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		case 'gd':
			if(imagegd($image,$uploadfile . '.gd')){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		case 'gd2':
			if(imagegd2($image,$uploadfile . '.gd2')){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		case 'wbmp':
			if(imagewbmp($image,$uploadfile . '.wbmp')){return 1;}//image uploaded successfully
			else{return 0;}//error
			break;
		default:
			return 0;
			break;
	}//switch
}//uploadImage()

function openImage($file) {
	# JPEG:
	$image = @imagecreatefromjpeg($file);
	if ($image !== false) { return $image; }
	# GIF:
	$image = @imagecreatefromgif($file);
	if ($image !== false) { return $image; }
	# PNG:
	$image = @imagecreatefrompng($file);
	if ($image !== false) { return $image; }
	# GD File:
	$image = @imagecreatefromgd($file);
	if ($image !== false) { return $image; }
	# GD2 File:
	$image = @imagecreatefromgd2($file);
	if ($image !== false) { return $image; }
	# WBMP:
	$image = @imagecreatefromwbmp($file);
	if ($image !== false) { return $image; }
	# Try and load from string:
	$image = @imagecreatefromstring(file_get_contents($file));
	if ($image !== false) { return $image; }
	return false;
}//openImage()

//findFileExtension()
function findFileExtension($filename)
{
	$filename = strtolower($filename) ;
	$file_extension = split("[/\\.]", $filename) ;
	$n = count($file_extension)-1;
	$file_extension = $file_extension[$n];
	return $file_extension;
}//findFileExtension()

function removeExtension($str)
{ 
     $temp = strrchr($str, '.'); 
     if($temp != false) { $str = substr($str, 0, -strlen($temp));}
     return $str;
}//removeExtension()

function imageResize($image,$resize_type)
{

	$width = imagesx($image);
	$height = imagesy($image);
	
	if($resize_type == 'fullresolution')
	{
		if(IMAGE_FULL_RESOLUTION_RESIZE == 'false')
		{
			if($width > $height){$val = imagesx($image);}
			else{$val = imagesy($image);}
		}
		else{$val=floatval(IMAGES_FULL_RESOLUTION_PIXELS);}
	}
	elseif($resize_type == 'thumbnail'){$val=floatval(IMAGES_FOLDER_THUMBNAILS_PIXELS);}
	elseif($resize_type == 'largethumbnail'){$val=floatval(IMAGES_FOLDER_LARGE_THUMBNAILS_PIXELS);}
	
	if(THUMBNAILS_TYPE == 'normal')
	{
		if($width > $height) { $new_width = $val; $new_height = $height * ($new_width/$width); }//horizontal image
		else { $new_height = $val; $new_width = $width * ($new_height/$height); }//vertical image
		// Resample
		$image_resized = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	}//if
	elseif(THUMBNAILS_TYPE == 'square')
	{
		if($resize_type == 'fullresolution')
		{
			if(IMAGE_FULL_RESOLUTION_RESIZE == 'false')
			{
				if($width > $height) { $new_width = $val; $new_height = $height * ($new_width/$width); }//horizontal image
				else { $new_height = $val; $new_width = $width * ($new_height/$height); }//vertical image
			}
			else
			{
				//if($width>=$val){$new_width = $val; $new_height = $height * ($new_width/$width);}
				if($width>=$val)
				{
					//USE THIS---> //$new_width = $val; $new_height = $height * ($new_width/$width);
					//OR THIS:
					if($width > $height) { $new_height = $val; $new_width = $width * ($new_height/$height); }//horizontal image
					else {$new_width = $val; $new_height = $height * ($new_width/$width); }//vertical image
				}
				else{$new_width = $width; $new_height = $height * ($new_width/$width);}
				//if the original image is smaller, then don't resize it upwards
			}
			// Resample
			$image_resized = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		}//if
		else
		{
			if($width>$height){ $new_width = ceil(($width-$height)/2); $width=$height; }
			else{ $new_height = ceil(($height-$width)/2); $height=$width;}
			// Resample
			$image_resized = imagecreatetruecolor($val, $val);
			imagecopyresampled($image_resized, $image, 0, 0, $new_width, $new_height, $val, $val, $width, $height);
		}//else
	}//elseif()
	
	return $image_resized;
}//imageResize()
?>