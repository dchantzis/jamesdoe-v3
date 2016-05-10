<?php
###################################################
############selectdbinc.php########################
###################################################
function loadCategoriesAdm()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$catVars = array();
	$albumVars = array();
	$comboBox = array();
	
	$query01 = "SELECT id, name, description FROM tags WHERE type='album'; ";
	$query02 = "SELECT albums.id, albums.name, albums.description, albums.tagid, albums.coverid, albums.lastupdatedtimestamp FROM albums ORDER BY albums.lastupdatedtimestamp DESC; ";
	$query03 = "SELECT albums.id, albums.name, albums.description, albums.tagid, albums.coverid, albums.lastupdatedtimestamp, count(images.albumid) AS imagescount FROM albums, images WHERE albums.id = images.albumid GROUP BY albums.id;";
	$query04 = "SELECT id, name FROM tags WHERE type='album'; ";
	$query05 = "SELECT images.id AS coverid, images.fileurl AS coverthumb FROM images";
	
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','category','false');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','category','false');
	$num02 = @mysql_num_rows($result02);
	$result03 = @mysql_query($query03) or dbErrorHandler(802,mysql_error(),$query03,'php','','','category','false');
	$num03 = @mysql_num_rows($result03);
	$result04 = @mysql_query($query04) or dbErrorHandler(802,mysql_error(),$query04,'php','','','category','false');
	$num04 = @mysql_num_rows($result04);
	$result05 = @mysql_query($query05) or dbErrorHandler(802,mysql_error(),$query05,'php','','','category','false');
	$num05 = @mysql_num_rows($result05);
	
	if(isset($num01)&&$num01!=0)
	{
		for($i=0; $i<$num01; $i++)
		{
			$catVars[$i]['id'] = mysql_result($result01,$i,'id');
			$catVars[$i]['name'] = strtolower(mysql_result($result01,$i,'name'));
			$catVars[$i]['description'] = mysql_result($result01,$i,'description');
			$catVars[$i]['albumno'] = 0;
			$catVars[$i]['albumimagescount'] = 0;
		}//for

		if(isset($num02)&&$num02!=0)
		{
			for($i=0; $i<$num02; $i++)
			{
				$tempalbum_id = mysql_result($result02,$i,'id');
				$albumVars[$tempalbum_id]['name'] = strtolower(mysql_result($result02,$i,'name'));
				$albumVars[$tempalbum_id]['description'] = mysql_result($result02,$i,'description');
				$albumVars[$tempalbum_id]['tagid'] = mysql_result($result02,$i,'tagid');
				$albumVars[$tempalbum_id]['coverid'] = mysql_result($result02,$i,'coverid');
				$albumVars[$tempalbum_id]['coverthumb'] = "defaultcoverlargethumb.png";
				$albumVars[$tempalbum_id]['lastupdated'] = convertTimeStamp(mysql_result($result02,$i,'lastupdatedtimestamp'),'short');
				$albumVars[$tempalbum_id]['imagescount']=0;
				$albumVars[$tempalbum_id]['albumcoverfullpath'] = './jdlayout/images/'.$albumVars[$tempalbum_id]['coverthumb'];
				
				if( $albumVars[$tempalbum_id]['name']=='' || strtolower($albumVars[$tempalbum_id]['name'])=='null' )
				{$albumVars[$tempalbum_id]['name']='(type a name)';}	
				if( $albumVars[$tempalbum_id]['description']=='' || strtolower($albumVars[$tempalbum_id]['description'])=='null' )
				{$albumVars[$tempalbum_id]['description']='(type a description)';}
				
				if($num05!=0){for($o=0; $o<$num05; $o++){if($albumVars[$tempalbum_id]['coverid']==@mysql_result($result05,$o,'coverid')){
					$albumVars[$tempalbum_id]['coverthumb']=@mysql_result($result05,$o,'coverthumb');
					$albumVars[$tempalbum_id]['albumcoverfullpath'] = './jdimages/largethumbnails/'.$albumVars[$tempalbum_id]['coverthumb'];}}}
				
				if(isset($num03)&&$num03!=0)
				{
					for($k=0; $k<$num03; $k++)
					{
						$albumid = mysql_result($result03,$k,'id');
						$imagescount = mysql_result($result03,$k,'imagescount');
						
						if($tempalbum_id == $albumid){$albumVars[$tempalbum_id]['imagescount'] = $imagescount;}
					}//for
				}//if
				else{}//do nothing
				
				for($j=0; $j<$num01; $j++)
				{
					if($catVars[$j]['id'] == $albumVars[$tempalbum_id]['tagid'])
					{
						//if($albumVars[$tempalbum_id]['name']=='invisiblealbum'){unset($albumVars[$tempalbum_id]);}
						//else{$catVars[$j]['albumno']++;}
						$catVars[$j]['albumno']++;
						$catVars[$j]['albumimagescount'] += $albumVars[$tempalbum_id]['imagescount'];
					}//if
				}//for
				
				//while we're here, create the combo box with all the category names and id
				$comboBox[$tempalbum_id] = "<span class='albumcatcomboboxes' id='albumcatcombobox_".$tempalbum_id."'><select class='text' name='album_tagid_".$tempalbum_id."' id='album_tagid_".$tempalbum_id."' >";
				$comboBox[$tempalbum_id] .= "<option value=''>[category name]</option>";
				for($k=0; $k<$num04; $k++)
				{
				if($albumVars[$tempalbum_id]['tagid'] != mysql_result($result04,$k,'id')){$comboBox[$tempalbum_id] .= "<option value='".mysql_result($result04,$k,'id')."'>".mysql_result($result04,$k,'name')."</option>";}
				}//for
				$comboBox[$tempalbum_id] .= "</select></span>";
				
			}//for
		}//if
		else{}//do nothing
	}//if
	else {}//no categories
	
	echo "<ul id='categories'>";
	echo "<span id='categoryanchor'></span>";
	if(isset($num01)&&$num01!=0)
	{
		reset($catVars);
		for($i=0; $i<$num01; $i++)
		{
			echo "<li class='cats' id='cat_".$catVars[$i]['id']."'>";			
			echo "<ul class='catbanners'>";
				echo "<li class='catnames'>";
					echo "<span class='catsps' id='catsp_".$catVars[$i]['id']."' title='Category Name.'>".$catVars[$i]['name']."</span>";
					echo "<span class='catfrms' id='catnamefrm_".$catVars[$i]['id']."'>";
						echo "<input class='text' type='text' id='cat_name_".$catVars[$i]['id']."' value='' maxlength='18' title='Category Name.' />";
						echo "<input class='button' type='button' name='catnamesubmitbutt_".$catVars[$i]['id']."' id='catnamesubmitbutt_".$catVars[$i]['id']."' value='save' />";
						echo "<input class='button' type='button' name='catnamecancelbutt_".$catVars[$i]['id']."' id='catnamecancelbutt_".$catVars[$i]['id']."' value='cancel' />";
						echo "<div id='cat_name_".$catVars[$i]['id']."failed' class='hidden'></div>";
					echo "</span>";
					echo "<span class='hidden' id='catnameloader_".$catVars[$i]['id']."'></span>";
					echo "<span class='hidden' id='ncat_".$catVars[$i]['id']."'>".$catVars[$i]['id']."</span>";
				echo "</li>";		
				
				echo "<li class='catinfos'>";
					echo "<span id='albumsnum_".$catVars[$i]['id']."'>".$catVars[$i]['albumno'];
					if($catVars[$i]['albumno'] == 1){echo " album";}else{echo " albums";}
					echo "</span>";
					echo ":: ";
					echo "<span id='albumsimagesnum_".$catVars[$i]['id']."'>".$catVars[$i]['albumimagescount'];
					if($catVars[$i]['albumimagescount'] == 1){echo " image";}else{echo " images";}
					echo "</span>";
					echo "<span class='togglers' id='togglecat_".$catVars[$i]['id']."'>"."show"."</span>";
				echo "</li>";
			echo "</ul>";

			echo "<div class='separator'></div>";
			echo "<div class='catsections' id='catsection_".$catVars[$i]['id']."'>";
			echo "<span class='hidden' id='categorytypedesc_".$catVars[$i]['id']."'>".$catVars[$i]['description']."</span>";
			
			echo "<ul class='catoptions' id='catoption_".$catVars[$i]['id']."'>";
				if( ($catVars[$i]['description']!='postsalbum')&&($catVars[$i]['description']!='welcomepageimagesalbum')){echo "<li id='addalbum_".$catVars[$i]['id']."'>Add Album</li>"." | ";}
				echo "<li id='deletecat_".$catVars[$i]['id']."'>Delete</li>";
			echo "</ul>";
			echo "<ul class='catdeletes' id='catdelete_".$catVars[$i]['id']."'>"
				."<li class='deletecatmsg'>delete this category and all its albums?</li>"
				."<li class='completedeletecategories'><span id='completedeletecategory_".$catVars[$i]['id']."'>yes</span></li>"
				."<li class='dontdeletecategories'><span id='dontdeletecategory_".$catVars[$i]['id']."'>no</span></li>"
				."</ul>";
			echo "<ul class='caterrmsguls' id='caterrmsgul_".$catVars[$i]['id']."'>"
				."<li class='caterrmsgs' id='caterrmsg_".$catVars[$i]['id']."'>Can't create new album. Please name your new category first.</li>"
				."<li class='caterrmsgokbutts'><span id='caterrmsgokbutt_".$catVars[$i]['id']."'>ok</span></li>"
				."</ul>";
			if($catVars[$i]['description']=='postsalbum')
				{echo "<div class='catnotes'>This category is automatically generated to store the images that are uploaded from the <span class='bold'>'Posts'</span> section. </div>";}
			elseif($catVars[$i]['description']=='welcomepageimagesalbum')
				{echo "<div class='catnotes'>This category is automatically generated to store the images that are uploaded from the <span class='bold'>'Welcome Page'</span> section. </div>";}
			echo "<div class='catalbumcontainers' id='catalbumcontainer_".$catVars[$i]['id']."' >";	
			if(isset($num02)&&$num02!=0)
			{	
				$temp=0;
				reset($albumVars);
				while (list($key, $val)=each ($albumVars))
				{		
					if($albumVars[$key]['tagid']==$catVars[$i]['id'])
					{
						if($temp==0)
						{
							echo "\n\t\t\t\t"."<div class='albumbanners' id='albumbanner_".$catVars[$i]['id']."'>Albums</div>";
							echo "\n\t\t\t\t"."<div class='catalbums' id='catalbums_".$catVars[$i]['id']."'>";
							echo "\n\t\t\t\t"."<span id='albumanchor_".$catVars[$i]['id']."'></span>";
						}
						$temp++;
						
						echo "\n\t\t\t\t"."<div class='albums' id='album_".$key."'>";
						echo "<span class='hidden' id='nalbum_".$key."'>".$key."</span>";
						echo "<span class='albumcovers' id='albumcover_".$key."'>"."<img src='".$albumVars[$key]['albumcoverfullpath']."' alt='album cover' />"."</span>";
						echo "<ul class='albumdatas' id='albumdata_".$key."'>";
							echo "<li class='albuminfos' id='albuminfo_".$key."'>";
								
								echo "<span class='editalbumnames' id='editalbumname_".$key."' title='Album Name.' >".$albumVars[$key]['name']."</span>";
								echo "\n\t\t\t\t"."<span class='albumfrms' id='albumnamefrm_".$key."'>";
								echo "<input class='text' type='text' name='album_name_".$key."' id='album_name_".$key."' value='' maxlength='24' title='Album Name.' />"
										."<input class='button' type='button' id='albumnamesubmitbutt_".$key."' value='save' />"
										."<input class='button' type='button' id='albumnamecancelbutt_".$key."' value='cancel' />"
										."<span id='album_name_".$key."failed' class='hidden'></span>";
								echo "</span>";						
								echo "<span id='albumnameloader_".$key."' class='hidden'></span>";
								
								echo "<span class='editalbumdescriptions' id='editalbumdescription_".$key."' title='Album Description.'>".$albumVars[$key]['description']."</span>";					
								echo "\n\t\t\t\t"."<span class='albumfrms' id='albumdescriptionfrm_".$key."'>";
								echo "<textarea class='text' name='album_description_".$key."' id='album_description_".$key."' cols='21' rows='5' wrap='soft' title='Album Description.'></textarea>"
									."<span class='editdescriptionbuttons'>"
										."<input class='button' type='button' id='albumdescriptionsubmitbutt_".$key."' value='save' />"
										."<input class='button' type='button' id='albumdescriptioncancelbutt_".$key."' value='cancel' />"
									."</span>"
									."<span class='charcounters'><span class='counters' id='acounter_".$key."'>".ALBUM_DESCRIPTION_MAX_LENGTH."</span> remaining characters</span>"
									."<span id='album_description_".$key."failed' class='hidden'></span>";
								echo "</span>";						
								echo "<span id='albumdescriptionloader_".$key."' class='hidden'></span>";
								
								echo "<span class='editalbumtagids' id='editalbumtagid_".$key."'>Move to category: </span>";
								echo "<span class='albumfrms' id='albumtagidfrm_".$key."'>";
									echo $comboBox[$key];
									echo "<input class='button' type='button' id='albumtagidsubmitbutt_".$key."' value='save' />";
									echo "<input class='button' type='button' id='albumtagidcancelbutt_".$key."' value='cancel' />";
									echo "<span id='album_tagid_".$key."failed' class='hidden'></span>";
								echo "</span>";
								echo "<span id='albumtagidloader_".$key."' class='hidden'></span>";	
								echo "<span class='editimagescounts' id='editimagescount_".$key."'>".$albumVars[$key]['imagescount'];
									if($albumVars[$key]['imagescount'] == 1){echo " image";}else{echo " images";}
								echo "</span>";					
								
								echo "<span class='editablumupdateds' id='editalbumupdated_".$key."'>"."Updated: ".$albumVars[$key]['lastupdated']."</span>";
							echo "</li>";
							echo "<li class='albumdeletes' id='albumdelete_".$key."'>";
								echo "<span class='deletealbummsg'>delete this album and all its images?</span>";
								echo "<div class='completedeletealbums'><span id='completedeletealbum_".$key."'>yes</span></div>";
								echo "<div class='dontdeletealbums'><span id='dontdeletealbum_".$key."'>no</span></div>";
							echo "</li>";
							
							echo "<span class='hidden' id='albumcategoryid_".$key."'>".$catVars[$i]['id']."</span>";					
						echo "</ul>";
						echo "<div class='albumoptions' id='albumoption_".$key."'>";
								echo "<div class='editalbumimages'><span id='editalbumimages_".$key."'>edit images</span></div>";
								echo "<div class='deletealbums'><span id='deletealbum_".$key."'>delete</span></div>";
						echo "</div>";
						echo "</div>";
						
						if($temp==$catVars[$i]['albumno']){echo "\n\t\t\t\t"."</div><!--catalbums-->";}
					}//if
				}//while
			}//if
			echo "</div><!--catalbumcontainers-->";
			echo "</div><!--catsection-->";
			echo "</li><!--cats-->";
		}//for
	}
	echo "</ul><!--categories-->";
	unset($dbobj);
	unset($catVars);
	unset($albumVars);
}//loadCategoriesAdm()

function loadAlbumImagesAdm($albumID,$categoryID)
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$catVars = array();
	$albumVars = array();
	$imageVars = array();
	$explodeTagsArr = array();
	$explodeImagesOrder = array();
	$imageVarsTempSorted = array();
	$imageVarsTempUnsorted = array();
	
	$query = "SELECT id, name, tags, description, filename, fileurl, submitiontimestamp"
			." FROM images WHERE albumid='".$albumID."' AND albumtagid='".$categoryID."' ORDER BY submitiontimestamp DESC; ";
	$query01 = "SELECT albums.id, albums.name, tags.name AS category, albums.imagesorder FROM albums, tags WHERE albums.tagid = tags.id ORDER BY albums.name ASC; ";
	$query02 = "SELECT id, name FROM tags WHERE type='image'; ";
	
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','album','false');
	$num = @mysql_num_rows($result);
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','album','false');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','album','false');
	$num02 = @mysql_num_rows($result02);	

	if(isset($num)&&$num!=0)
	{
		for($i=0; $i<$num; $i++)
		{
			//$imageVars[$i]['id'] = mysql_result($result,$i,'id');
			$tempImageID = @mysql_result($result,$i,'id');
			$imageVars[$tempImageID]['name'] = @mysql_result($result,$i,'name');
			$imageVars[$tempImageID]['tags'] = @mysql_result($result,$i,'tags');
			$imageVars[$tempImageID]['filename'] = @mysql_result($result,$i,'filename');
			$imageVars[$tempImageID]['description'] = @mysql_result($result,$i,'description');
			$imageVars[$tempImageID]['fileurl'] = @mysql_result($result,$i,'fileurl');
			$imageVars[$tempImageID]['submitiontimestamp'] = convertTimeStamp(@mysql_result($result,$i,'submitiontimestamp'),'short');
			$imageVars[$tempImageID]['imagefullpath'] = './jdimages/thumbnails/'.$imageVars[$tempImageID]['fileurl'];
			
			//find the tag names
			if($num02!=0)
			{
				$explodeTagsArr = explode('.::.',$imageVars[$tempImageID]['tags']);
				$imageVars[$tempImageID]['tags']='';
				for($k=0; $k<$num02; $k++)
				{
					$tempTagID = @mysql_result($result02,$k,'id');
					$tempTagName = @mysql_result($result02,$k,'name');
					for($z=0; $z<count($explodeTagsArr); $z++)
					{
						if($explodeTagsArr[$z] == $tempTagID){ $imageVars[$tempImageID]['tags'].=$tempTagName.", ";}
					}//for
				}//for
			}//
			elseif($num02==0){$imageVars[$tempImageID]['tags']='';}
			
			if( $imageVars[$tempImageID]['name']=='' || strtolower($imageVars[$tempImageID]['name'])=="null" )
			{$imageVars[$tempImageID]['name']='(type a name)';}	
			if( $imageVars[$tempImageID]['description']=='' || strtolower($imageVars[$tempImageID]['description'])=="null" )
			{$imageVars[$tempImageID]['description']='(type a description)';}	
			if( $imageVars[$tempImageID]['tags']=='' || strtolower($imageVars[$tempImageID]['tags'])=="null" )
			{$imageVars[$tempImageID]['tags']='(type some tags)';}
			
			//while we're here, create the combo box with all the album names and id
			$comboBox[$tempImageID] = "<select class='text' name='image_album_".$tempImageID."' id='image_album_".$tempImageID."' >";
			$comboBox[$tempImageID] .= "<option value=''>[album name]</option>";
			for($k=0; $k<$num01; $k++)
			{
				if($albumID != @mysql_result($result01,$k,'id')){$comboBox[$tempImageID] .= "<option value='".@mysql_result($result01,$k,'id')."'>".@mysql_result($result01,$k,'name')." (".mysql_result($result01,$k,'category').")"."</option>";}
				else if($albumID == @mysql_result($result01,$k,'id')){$albumImagesOrder = @mysql_result($result01,$k,'imagesorder');}
			}//for
			$comboBox[$tempImageID] .= "</select>";
		}//for
	}//if
	else{}//do nothing

	if($albumImagesOrder!=0 && $albumImagesOrder!=NULL)
	{
		$explodeImagesOrder = explode('.::.',$albumImagesOrder);
		for($i=0; $i<count($explodeImagesOrder); $i++){reset ($imageVars);while (list($key, $val) = each ($imageVars)){if($explodeImagesOrder[$i]==$key){$imageVarsTempSorted[$key] = $imageVars[$key]; unset($imageVars[$key]);}}}
		reset($imageVars);while (list($key, $val) = each ($imageVars)){$imageVarsTempSorted[$key] = $imageVars[$key];}
		$imageVars = $imageVarsTempSorted;
	}//if

	echo "<div id='albumimages'>";
		echo "<div class='imagesbanners'>
				<span id='banner'>album images:</span>
				<span class='tips' id='organizealbumimages' title='Tip.::.Drag your images to rearange their order.'>
					<img id='organizealbumimagescheckbox' src='./jdlayout/images/unchecked.gif' />Organize images
				</span>
				<span class='hidden' id='album_imagesorder_".$albumID."'></span>
			</div>";
	echo "<div id='albumcontents' class='albumcontents'>";
	if(isset($num)&&$num!=0)
	{
		reset ($imageVars);
		while (list($key, $val) = each ($imageVars))
		{
			if($imageVars[$key]['name']!='(type a name)'){$tempImageHTMLtitle = strtoupper($imageVars[$key]['name']);}
			else{$tempImageHTMLtitle = $imageVars[$key]['filename'];}
			echo "<ul class='imagesections' id='imagesection_".$key."' rel='".$key."'>";
				echo "<li class='imagethumbs'>";
				echo "<img id='imagethumb_".$key."' src='".$imageVars[$key]['imagefullpath']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."' />";
				echo "</li>";
				echo "<li class='imageinfos' id='imageinfo_".$key."'>
						<span class='editimagenames' id='editimagename_".$key."' title='Image Name.'>".$imageVars[$key]['name']."</span>
						<span class='imagefrms' id='imagenamefrm_".$key."'>
							<input class='text' type='text' name='image_name_".$key."' id='image_name_".$key."' value='' maxlength='24' title='Image Name.' />
							<input class='button' type='button' id='imagenamesubmitbutt_".$key."' value='save' />
							<input class='button' type='button' id='imagenamecancelbutt_".$key."' value='cancel' />
							<span id='image_name_".$key."failed' class='hidden'></span>
						</span>
						<span id='imagenameloader_".$key."' class='hidden'></span>
							
						<span class='editimagedescriptions' id='editimagedescription_".$key."' title='Image Description.'>".$imageVars[$key]['description']."</span>
						<span class='imagefrms' id='imagedescriptionfrm_".$key."'>
							<textarea class='text' name='image_description_".$key."' id='image_description_".$key."' cols='41' rows='5' wrap='soft' title='Image Description.'></textarea>
							<span class='editdescriptionbuttons'>
								<input class='button' type='button' id='imagedescriptionsubmitbutt_".$key."' value='save' />
								<input class='button' type='button' id='imagedescriptioncancelbutt_".$key."' value='cancel' />
							</span>
							<span class='charcounters'><span class='counters' id='icounter_".$key."'>".IMAGE_DESCRIPTION_MAX_LENGTH."</span> remaining characters</span>
							<span id='image_description_".$key."failed' class='hidden'></span>
						</span>
						<span id='imagedescriptionloader_".$key."' class='hidden'></span>
						
						<span class='editimagetags' id='editimagetags_".$key."' title='Image Tags.'>".$imageVars[$key]['tags']."</span>
						<span class='imagefrms' id='imagetagsfrm_".$key."'>
							<input class='text' type='text' name='image_tags_".$key."' id='image_tags_".$key."' value='' maxlength='70' title='Image Tags.'/>
							<input class='button' type='button' id='imagetagssubmitbutt_".$key."' value='save' />
							<input class='button' type='button' id='imagetagscancelbutt_".$key."' value='cancel' />
							<span class='tips' title='Tip.::.Click a suggested tag to add.'><ul class='hidden' id='imagetagsuggestions_".$key."'></ul></span>
							<span id='image_tags_".$key."failed' class='hidden'></span>
						</span>
						<span id='imagetagsloader_".$key."' class='hidden'></span>
							
						<span class='editimagealbums' id='editimagealbum_".$key."'>Move to album: </span>
						<span class='imagefrms' id='imagealbumfrm_".$key."'>";
							echo $comboBox[$key];
						echo "<input class='button' type='button' id='imagealbumsubmitbutt_".$key."' value='save' />
							<input class='button' type='button' id='imagealbumcancelbutt_".$key."' value='cancel' />
							<span id='image_album_".$key."failed' class='hidden'></span>
						</span>
						<span id='imagealbumloader_".$key."' class='hidden'></span>
						
						<span class='editimagecovers' id='editimagecover_".$key."'>
							<img class='imagecovers' id='image_cover_".$key."' src='./jdlayout/images/off.gif' alt='radio button' />Set as album cover
						</span>
						
						<span class='imagesubmitiontimestamp'>"."Submitted: ".$imageVars[$key]['submitiontimestamp']."</span>
					</li>
					<li class='imageoptions' id='imageoption_".$key."'>
						<div class='closeimagesections'><span id='closeimagesection_".$key."'>close</span></div>
						<div class='deleteimages'><span id='deleteimage_".$key."'>delete</span></div>
					</li>
					<li class='imagedeletes' id='imagedelete_".$key."'>
						<span class='deleteimagemsg'>delete this image?</span>
						<div class='completedeleteimages'><span id='completedeleteimage_".$key."'>yes</span></div>
						<div class='dontdeleteimages'><span id='dontdeleteimage_".$key."'>no</span></div>
					</li>
			</ul>";
		}//while
	}
	else{}//do nothing
	echo "</div><!--albumcontents-->";
	echo "</div><!--albumimages-->";
			
	unset($dbobj);
	unset($catVars);
	unset($albumVars);
}//loadAlbumImagesAdm($albumID,$categoryID)

function loadPostsAdm()
{
	require_once('jddbase.class.php');
		
	$dbobj = new JDDBase();
	$postVars = array();
	$imageVars = array();
	$csrf_password_generator = hash('sha256', "mmeditposts").CSRF_PASS_GEN;

	$dbPostsLimit=$_SESSION['postslimit'];
	if($dbPostsLimit==''||!isset($dbPostsLimit)||$dbPostsLimit==0){$dbPostsLimit=0;}

	$query00 = "SELECT * FROM posts ORDER BY creationtimestamp DESC; ";
	$query01 = "SELECT * FROM posts ORDER BY creationtimestamp DESC LIMIT ".$dbPostsLimit.",10;";
	$query02 = "SELECT id, name, description, filename, fileurl, submitiontimestamp FROM images ORDER BY id ASC;";
	$query03 = "SELECT COUNT(*) AS newspostcount FROM posts WHERE type='newspost'; ";
	$query04 = "SELECT tags.id AS categoryid, albums.id AS albumid FROM tags, albums WHERE tags.id = albums.tagid AND tags.description='postsalbum'; ";
	$query05 = "SELECT count(*) AS commentsnumber, postid FROM comments GROUP BY postid; ";
	
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','posts','false');
	$num00 = @mysql_num_rows($result00);
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','posts','false');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','posts','false');
	$num02 = @mysql_num_rows($result02);
	$result03 = @mysql_query($query03) or dbErrorHandler(802,mysql_error(),$query03,'php','','','posts','false');
	$num03 = @mysql_num_rows($result03);
	$result04 = @mysql_query($query04) or dbErrorHandler(802,mysql_error(),$query04,'php','','','posts','false');
	$num04 = @mysql_num_rows($result04);
	$result05 = @mysql_query($query05) or dbErrorHandler(802,mysql_error(),$query05,'php','','','posts','false');
	$num05 = @mysql_num_rows($result05);
	
	$generalPostsCount = $num00;
	$newsPostCount = @mysql_result($result03,0,'newspostcount');
	//$imagesUpdatesPostCount = $num01-$newsPostCount;
	$imagesUpdatesPostCount = $generalPostsCount-$newsPostCount;

	if($num00!=0)
	{for($a=0;$a<$num00;$a++)
		{$tempPostID = @mysql_result($result00,$a,'id');$tempPostType = @mysql_result($result00,$a,'type');
		if($tempPostType=='imagesupdate'){$postNumbers['imagesupdate'][$tempPostID]=$imagesUpdatesPostCount--;}
		else{$postNumbers['newspost'][$tempPostID]=$newsPostCount--;}}}

	if($num04!=0)
		{$albumID = mysql_result($result04,0,'albumid');
		$categoryID = mysql_result($result04,0,'categoryid');}

	if($num01!=0)
	{
		//get all the posts from DB
		for($j=0; $j<$num01; $j++)
		{
			$tempPostID = mysql_result($result01,$j,'id');
			$postVars[$tempPostID]['headline'] = mysql_result($result01,$j,'headline');
			$postVars[$tempPostID]['body'] = mysql_result($result01,$j,'body');
			$postVars[$tempPostID]['type'] = mysql_result($result01,$j,'type');
			$postVars[$tempPostID]['tags'] = mysql_result($result01,$j,'tags');
			$postVars[$tempPostID]['images'] = mysql_result($result01,$j,'images');
			$postVars[$tempPostID]['submitiontimestamp'] = convertTimeStamp(mysql_result($result01,$j,'submitiontimestamp'),'short');
			$postVars[$tempPostID]['creationtimestamp'] = convertTimeStamp(mysql_result($result01,$j,'creationtimestamp'),'short');
			$postVars[$tempPostID]['commentsnumber'] = 0;
			
			if($postVars[$tempPostID]['type']=='imagesupdate'){$postVars[$tempPostID]['postnumber']=$postNumbers['imagesupdate'][$tempPostID];}
			else{$postVars[$tempPostID]['postnumber']=$postNumbers['newspost'][$tempPostID];}
			
			if( $postVars[$tempPostID]['headline']=='' || strtolower($postVars[$tempPostID]['headline'])=="null" )
			{$postVars[$tempPostID]['headline']='(type post headline)';}
			if( $postVars[$tempPostID]['body']=='' || strtolower($postVars[$tempPostID]['body'])=="null" )
			{$postVars[$tempPostID]['body']='(type your text here)';}
		}//for
		
		if($num02!=0)
		{
			//get all the images from DB
			for($z=0; $z<$num02; $z++)
			{
				$tempImageID = mysql_result($result02,$z,'id');
				if($tempImageID==0){continue;}
				$imageVars[$tempImageID]['id'] = $tempImageID; //seems redundant but do not delete this
				$imageVars[$tempImageID]['name'] = mysql_result($result02,$z,'name');
				$imageVars[$tempImageID]['description'] = mysql_result($result02,$z,'description');
				$imageVars[$tempImageID]['filename'] = mysql_result($result02,$z,'filename');
				$imageVars[$tempImageID]['fileurl'] = mysql_result($result02,$z,'fileurl');
				$imageVars[$tempImageID]['submitiontimestamp'] = mysql_result($result02,$z,'submitiontimestamp');
				$imageVars[$tempImageID]['imagefullpath'] = './jdimages/thumbnails/'.$imageVars[$tempImageID]['fileurl'];
			}//for
		}//if
		
		if($num05!=0)
		{
			for($z=0; $z<$num05; $z++)
			{
				$tempPostID = @mysql_result($result05,$z,'postid');
				if(isset($postVars[$tempPostID]['commentsnumber']))
					{$postVars[$tempPostID]['commentsnumber'] = @mysql_result($result05,$z,'commentsnumber');}
			}
		}
	}//
	
	//get the posts images
	if($num01!=0)
	{
		reset($imageVars);
		reset($postVars);
		while (list($key, $val) = each ($postVars))
			{$tempExplodeImageIDsArr = explode('.::.',$postVars[$key]['images']);
			for($k=0; $k<count($tempExplodeImageIDsArr); $k++){if(isset($imageVars[intval($tempExplodeImageIDsArr[$k])]['id']))
				{$imageVars[intval($tempExplodeImageIDsArr[$k])]['postid'] = $key;}}}
	}
	unset($tempExplodeImageIDsArr);

	//PRINT THE EDIT POSTS NAVIGATION
	if($generalPostsCount!=0)
	{
		$showEditPostsNavi = TRUE;
		if((($generalPostsCount-($dbPostsLimit))<=10)){$showEditPostsNaviPrevious = FALSE;}
		else{$showEditPostsNaviPrevious = TRUE;}
		if($generalPostsCount<10){$showEditPostsNaviPrevious = FALSE;}	
		
		if($dbPostsLimit==0){$showEditPostsNaviNext = TRUE;}
		else{$showEditPostsNaviNext = FALSE;}
		
		if($showEditPostsNaviPrevious==TRUE||$showEditPostsNaviNext==FALSE){$showEditPostsNavi = TRUE;}else{$showEditPostsNavi = FALSE;}
	}
	else{ $showEditPostsNavi = FALSE; $showEditPostsNaviNext = FALSE; $showEditPostsNaviPrevious = FALSE;}
	
	#############
	#############
	#############
			
	echo "<ul id='posts'>";
		echo "<span id='postanchor'></span>";	
		if($num01!=0)
		{
			reset ($postVars);
			while (list($key, $val) = each ($postVars))
			{
			echo "<li class='postelements' id='postelement_".$key."'>";
				echo "<ul class='postelementsbanners'>";
					echo "<li class='postheadlines'>";
						echo "<span class='postsps' id='postsp_".$key."' title='Post Headline'>".$postVars[$key]['headline']."</span>";
						echo "<span class='postfrms' id='postheadlinefrm_".$key."'>";
							echo "<input class='text' type='text' id='post_headline_".$key."' value='' maxlength='38' title='Post Headlines.' />";
							echo "<input class='button' type='button' name='postheadlinesubmitbutt_".$key."' id='postheadlinesubmitbutt_".$key."' value='save' />";
							echo "<input class='button' type='button' name='postheadlinescancelbutt_".$key."' id='postheadlinescancelbutt_".$key."' value='cancel' />";
							echo "<div id='post_headline_".$key."failed' class='hidden'></div>";
						echo "</span><!--postfrms-->";
						echo "<span class='hidden' id='postheadlineloader_".$key."'></span>";
						echo "<span class='hidden' id='npost_".$key."'>".$key."</span>";
					echo "</li><!--postheadlines-->";
					
					echo "<li class='postinfos'>";
						echo "<span id='postcreationtimestamp_".$key."'>".$postVars[$key]['creationtimestamp']."</span>";
						echo "<span class='togglers' id='togglepost_".$key."'>"."show"."</span>";
					echo "</li>";
				echo "</ul><!--postelementsbanners-->";
		
				echo "<div class='separator'></div>";
				echo "<div class='postsections' id='postsection_".$key."'>";
						echo "<ul class='posterrmsguls' id='posterrmsgul_".$key."'>"
							."<li class='posterrmsgs' id='posterrmsg_".$key."'>Please enter a headline for this post first.</li>"
							."<li class='posterrmsgokbutts'><span id='posterrmsgokbutt_".$key."'>ok</span></li>"
							."</ul>";
						echo "<div class='postnotes'>";
							if($postVars[$key]['type']=='imagesupdate'){echo "<span class='postcounts'>"."Images Update #".$postVars[$key]['postnumber']."</span>";}
							else{echo "<span class='postcounts'>"."Post #".$postVars[$key]['postnumber']."</span>";}
							if($postVars[$key]['type']=='imagesupdate')
							{echo "<div class='imagesupdatesnotes'><span class='highlight'>Images Update (Automatically generated post).</span></div>";}
						echo "</div>";
						echo "<div class='clearboth'></div>";
						echo "<div class='postcontainers' id='postcontainer_".$key."'>";
							echo "<div class='editpostbodycontainers'>";
								echo "<span class='editpostbodies' id='editpostbody_".$key."' title='Post Body.'>".$postVars[$key]['body']."</span>"
							."<span class='postfrms' id='postbodyfrm_".$key."'>"
							."<textarea class='text' type='text' name='post_body_".$key."' id='post_body_".$key."' cols='41' rows='5' wrap='soft' title='Post Body.'/></textarea>"				."<span class='editbodybuttons'>"
									."<input class='button' type='button' id='postbodysubmitbutt_".$key."' value='save' />"
									."<input class='button' type='button' id='postbodycancelbutt_".$key."' value='cancel' />"
								."</span>"
								."<span class='charcounters'><span class='counters' id='pcounter_".$key."'>".POST_BODY_MAX_LENGTH."</span> remaining characters</span>"
								."<span id='post_body_".$key."failed' class='hidden'></span>"
							."</span>"
							."<span id='postbodyloader_".$key."' class='hidden'></span>";
							echo "</div>";
						echo "<div class='clearboth'></div>";
						##################
						if($postVars[$key]['type']!='imagesupdate')
						{
							echo "<ul class='uploadimagesections' id='uploadimagesections_".$key."'>
								<li class='uploadimages' id='uploadimage_0_".$key."'>
									<form id='uploadimagesfrm_".$key."' name='uploadimagesfrm_".$key."' method='post' action='./jdincfunctions/functionsinc.php?type=8' enctype='multipart/form-data'>
									Select images to upload: <span id='inputplaceholder_".$key."'><input id='imagefile_".$key."_0' name='imagefile_".$key."_0' class='text' type='file' size='41' title='Browse image.' /></span>
									<input type='hidden' name='postid' class='hidden' value='".$key."' />
									<input type='hidden' name='csrf' class='hidden' value='".$csrf_password_generator.'uploadimage'."' />
									<input type='hidden' name='pageid' class='hidden' value='mmeditposts' />
									<span id='submituploadfrm_".$key."'></span>
									<input id='submituploadfrmbutt_".$key."' type='submit' class='button' value='upload' />
									</form>
									<ul class='imagestoupload' id='imagestoupload_".$key."'></ul>
								</li>
								<li class='supportedimagefiletypes'>Upload up to 5 images.<br />Supported image filetypes: jpg, gif, png</li>
							</ul>";
						}
						#################
						echo "<span class='hidden' id='posttype_".$key."'>".$postVars[$key]['type']."</span>";
						echo "<div class='postimagesbanners' id='postimagesbanners_".$key."'>";
							echo "<span class='banners'>Post images:</span>";
							if($postVars[$key]['type']=='imagesupdate')
								{echo "<span class='postimagesnotes'>To manage these images, go to the 'images' section.</span>";}
							else{echo "<span class='postimagesnotes'>To edit the information of these images, go to the 'images' section.</span>";}
						echo "</div>";
						echo "<div id='postimagecontents_".$key."' class='postimagecontents'>";					
						reset($imageVars);
						while (list($imagekey, $imageval) = each ($imageVars))
						{
							if($imageVars[$imagekey]['postid']==$key)
							{
								if($imageVars[$imagekey]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$imagekey]['name']);}
								else{$tempImageHTMLtitle = $imageVars[$imagekey]['filename'];}
								if($postVars[$key]['type']=='imagesupdate')
								{
									echo "<img id='imagethumb_".$imagekey."' class='imagesupdateimagethumbs' src='".$imageVars[$imagekey]['imagefullpath']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>";
								}//if
								else
								{
									echo "<ul class='imagesections' id='imagesection_".$imagekey."' rel='".$imagekey."'>"
										."<li class='imagethumbs'>"
										."<img id='imagethumb_".$imagekey."' src='".$imageVars[$imagekey]['imagefullpath']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>"
										."</li>"
										."<li class='imagedeletes' id='imagedelete_".$imagekey."'>"
										."<span class='deleteimagemsg'>delete?</span>"
										."<div class='completedeleteimages'><span id='completedeleteimage_".$imagekey."'>yes</span></div>"
										."<div class='dontdeleteimages'><span id='dontdeleteimage_".$imagekey."'>no</span></div>"
										."</li>"
										."</ul>";
								}//else
							}//if
						}//while
						echo "</div>";
						#################
						echo "<div class='clearboth'></div>";
						echo "<ul class='postdeletes' id='postdelete_".$key."'>"
							."<li class='deletepostmsg'>delete this post and all its contents?</li>"
							."<li class='completedeleteposts'><span id='completedeletepost_".$key."'>yes</span></li>"
							."<li class='dontdeleteposts'><span id='dontdeletepost_".$key."'>no</span></li>"
							."</ul>";
						echo "<ul class='postoptions' id='postoption_".$key."'>"
							."<li class='postcommentbutts' id='postcommentbutt_".$key."'>".$postVars[$key]['commentsnumber'];
							if($postVars[$key]['commentsnumber']!=1){echo " comments";}else{echo " comment";}
							echo "</li>";
						//if($postVars[$key]['type']!='imagesupdate'){echo "<li class='postdeletebutts' id='postdeletebutt_".$key."'>delete</li>";}
						echo "<li class='postdeletebutts' id='postdeletebutt_".$key."'>delete</li>";
						echo "</ul>";
						echo "<div class='clearboth'></div>";
						#################
						echo "<div class='postslineseperator'></div>";
						echo "</div><!--postcontainers-->";
				echo "</div><!--postsections-->";
			echo "</li><!--postelements-->";
			}
		}
		
	echo "</ul><!--posts-->";
	
	if($showEditPostsNavi)
	{
		echo "<ul class='editpostlinks'>";
			echo "<li id='prev'>";
				if($showEditPostsNaviPrevious){echo "<a href='./jdincfunctions/functionsinc.php?type=23' title='Previous 10 Posts'>&lt;previous 10</a>";}
				//else{echo "<span class='strikethrough'>&lt;previous 10</span>";}
				echo "</li>";
				echo "<li id='next'>";
				if(!$showEditPostsNaviNext){echo "<a href='./jdincfunctions/functionsinc.php?type=24' title='Next 10 Post'>next 10&gt;</a>";}
					//else{echo "<span class='strikethrough'>next 10&gt;</span>";}
				echo "</li>";
			echo "</ul>";
			echo "<span class='clearboth'></span>";
	}//
	
	echo "<span class='hidden' id='aid'>".$albumID."</span><span class='hidden' id='ncat_".$albumID."'>".$categoryID."</span>";
	unset($dbobj);
}//loadPostsAdm()

function uploadedImagesResults()
{
	$flag = 0;
	if(isset($_SESSION['uploadImagesResults']))
	{
		$uploadImagesResults = $_SESSION['uploadImagesResults']; unset($_SESSION['uploadImagesResults']);
		reset($uploadImagesResults);
		echo "<ul id='uploadimageresults'>";
		echo "<li id='uploadimageresultsclose'><span onclick='$(\"uploadimageresults\").dispose();'>close</span></li>";
		echo "<li id='uploadimageresultsbanner'>upload results:</li>";
		echo "<li class='clearboth'></li>";
		while (list($key, $val) = each ($uploadImagesResults))
		{ 
		  $explodedArr = explode('.',($uploadImagesResults[$key]['size']/1024),2);
		  $imageSize = $explodedArr[0].'.'.substr($explodedArr[1], 0, 2);
		  
          echo "<li><b>Image</b> ".$uploadImagesResults[$key]['name']." (".$imageSize." KB) : ";
		  if(isset($uploadImagesResults[$key]['error'])){echo '<b>'.variousMessages($uploadImagesResults[$key]['error']).'</b>'; }
		  else{ echo "<b>Uploaded successfully.</b>"; }
		  echo "</li>";
		  $flag = 1;//there's at least on image
		}//
		if($flag==0){echo "<li><b>Select (at least) one image to upload.</b></li>";}
		echo "</ul>";
	}
	else{}//do nothing
}//uploadedImagesResults()

function searchNsuggest()
{
	require_once('jddbase.class.php');
	require_once('validate.class.php');
	
	$dbobj = new JDDBase();
	$validator = new Validate();
	$suggestionsArr = array();
	
	$errorCode = NULL;
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'searchnsuggestimagetags', $_POST['pageid'])){errorHandler(702,'ajax'); exit;}
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok

	$arVals = array("imagetag"=>"","imageid"=>"","albumid"=>"","categoryid"=>"","csrf"=>"","pageid"=>"");

	reset ($_POST);
	while (list($key, $val) = each ($_POST))
	{	
		if (trim($val) == "") { $val = "";}
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");;
		$arVals[$key] = trim($arVals[$key]);
		unset($_POST[$key]);
	}//while

	$query = "SELECT name FROM tags WHERE type='image' AND name LIKE '".$arVals['imagetag']."%'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','searchNsuggest','false');
	$num = @mysql_num_rows($result);
	
	if($num!=0){for($i=0; $i<$num; $i++){ $suggestionsArr[$i] = mysql_result($result,$i,'name'); }}//if
	
	$suggestionsArr = array_unique($suggestionsArr);
	unset($dbobj);
	unset($validator);
	searchNsuggestXMLresponse(0,$arVals['imageid'],$suggestionsArr);
}//searchNsuggest()

#####################
/*
COMMON USERS FUNCTION
*/
#####################

function loadImagesOrder()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$imagesOrder = array();
	$tempImagesOrder = array();
	$albumCovers = array();
	$tempExplodedArr = array();
	$tempCovers = array();
	$covers = array();
	
	$query00 = "SELECT albums.id, albums.tagid, albums.imagesorder, albums.coverid, count(images.id) AS albumimagescount"
			." FROM albums, images" 
			." WHERE albums.id=images.albumid GROUP BY albums.id; ";
	$query01 = "SELECT id, name, description, tags, submitiontimestamp, albumid, albumtagid, fileurl FROM images; ";
	
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','loadImagesOrder','true');
	$num00 = @mysql_num_rows($result00);
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','loadImagesOrder','true');
	$num01 = @mysql_num_rows($result01);
	
	if($num01==0){}//no images
	{
		for($j=0; $j<$num01; $j++)
		{
			$tempID = mysql_result($result01,$j,'id');
			$tempCategoryID = mysql_result($result01,$j,'albumtagid');
			$tempAlbumID = mysql_result($result01,$j,'albumid');
			$tempImagesOrder[$tempCategoryID][$tempAlbumID] .= mysql_result($result01,$j,'id').'.::.';
			
			$tempCovers[$tempID]['name']=mysql_result($result01,$j,'name');
			if($tempCovers[$tempID]['name']==''){$tempCovers[$tempID]['name']='unnamed';}
			$tempCovers[$tempID]['description']=mysql_result($result01,$j,'description');
			$tempCovers[$tempID]['tags']=mysql_result($result01,$j,'tags');
			$tempCovers[$tempID]['submitiontimestamp']=convertTimeStamp(mysql_result($result01,$j,'submitiontimestamp'),'full');
			$tempCovers[$tempID]['fileurl']=mysql_result($result01,$j,'fileurl');
		}//for
	}//else
	
	if($num00==0){redirects(0,'');}//no albums
	else
	{
		for($i=0; $i<$num00; $i++)
		{
			$tempCategoryID = mysql_result($result00,$i,'tagid');
			$tempAlbumID = mysql_result($result00,$i,'id');
			$tempOrder = mysql_result($result00,$i,'imagesorder');
			$tempCover = mysql_result($result00,$i,'coverid');
			$tempAlbumImagesCount = mysql_result($result00,$i,'albumimagescount');
			####################################
			//SET THE IMAGES ORDER//
			####################################
			if(($tempOrder==0)||($tempOrder=='')){$tempImagesOrderDisplayType = 'SubmitionTimestamp';}//if
			else
			{
				$explodedArr = explode('.::.',$tempOrder); unset($explodedArr[count($explodedArr)-1]);//the last element is empty
				if(count($explodedArr)!=$tempAlbumImagesCount){$tempImagesOrderDisplayType = 'SubmitionTimestamp';} //then the order doesn't count
				else{$tempImagesOrderDisplayType = 'SelectedOrder';}
			}//else

				
			if($tempImagesOrderDisplayType=='SubmitionTimestamp'){$imagesOrder[$tempCategoryID][$tempAlbumID] = $tempImagesOrder[$tempCategoryID][$tempAlbumID];}//if
			elseif($tempImagesOrderDisplayType=='SelectedOrder')
			{
				$explodedArr = explode('.::.',$tempOrder);
					unset($explodedArr[count($explodedArr)-1]);//the last element is empty
				$explodedArrDBOrder = explode('.::.',$tempImagesOrder[$tempCategoryID][$tempAlbumID]); 
					unset($explodedArrDBOrder[count($explodedArrDBOrder)-1]);//the last element is empty
				for($z=0; $z<count($explodedArrDBOrder); $z++){$newExplodedArrDBOrder[$explodedArrDBOrder[$z]]='ThisValueDoesNotMatter';}
				unset($explodedArrDBOrder); $explodedArrDBOrder=$newExplodedArrDBOrder; unset($newExplodedArrDBOrder);
				
				//print first the images in the order of the $explodedArr
				//notice that the number of iterations has to do with how many images are in the $explodedArrDBOrder and not in the $explodedArr
				
				reset($explodedArrDBOrder);
				$tempCounter = count($explodedArrDBOrder);
				for($j=0; $j<$tempCounter; $j++)
				{
					if(isset($explodedArr[$j])){$tempImageID = $explodedArr[$j];}
					if(!isset($explodedArrDBOrder[$tempImageID])){continue;}
					$imagesOrder[$tempCategoryID][$tempAlbumID] .= $tempImageID.'.::.';
					unset($explodedArrDBOrder[$tempImageID]);
				}//
				//now print the rest of the images that are not in order and are in the $imageVars array
				reset($explodedArrDBOrder);
				while (list($imagekey, $imageval) = each ($explodedArrDBOrder)){$imagesOrder[$tempCategoryID][$tempAlbumID] .= $imageKey.'.::.';}//
			}//
			#########################################
			#########################################
			
			
			if($tempCover==0)
				{$tempExplodedArr = explode('.::.',$tempImagesOrder[$tempCategoryID][$tempAlbumID]);
				$albumCovers[$tempCategoryID][$tempAlbumID] = $tempExplodedArr[0];
				$tempCovers[$tempExplodedArr[0]]['albumimagescount'] = $tempAlbumImagesCount;
				$covers[$tempExplodedArr[0]] = $tempCovers[$tempExplodedArr[0]]; //get the first image as the cover
				}
			else
				{$albumCovers[$tempCategoryID][$tempAlbumID] = $tempCover;
				$tempCovers[$tempCover]['albumimagescount'] = $tempAlbumImagesCount;
				$covers[$tempCover] = $tempCovers[$tempCover];}
		}//for
	}//else

	$_SESSION['IMAGESORDER'] = $imagesOrder;
	$_SESSION['ALBUMCOVERS'] = $albumCovers;
	$_SESSION['COVERS'] = $covers;
	unset($dbobj);
}//loadImagesOrder()

function getImage($type)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$errorCode = NULL;
	$getImageID = NULL;
	$getImagePosition = NULL;
	$tempExplodedArr = NULL;
	$imageVars = array();
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'getimage', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	$categoryID = $_POST["categoryid"];
	$albumID = $_POST["albumid"];
	$imageID = $_POST["imageid"];
	unset($_POST["categoryid"]); unset($_POST["albumid"]); unset($_POST["imageid"]);
	
	if(!isset($_SESSION['IMAGESORDER'])){loadImagesOrder();}
	$tempExplodedArr = explode('.::.',$_SESSION['IMAGESORDER'][$categoryID][$albumID]);
	for($i=0; $i<count($tempExplodedArr); $i++)
	{
		$tempPosition = $i;
		if($imageID==$tempExplodedArr[$i])
		{
			if($type=='previous'){$getImagePosition = --$tempPosition;}
			elseif($type=='next'){$getImagePosition = ++$tempPosition;}
		}
	}//for
	if($getImagePosition >= $tempPosition){$getImagePosition = 0;}
	if($getImagePosition < 0){$getImagePosition = --$tempPosition;}

	$getImageID = (int)$tempExplodedArr[$getImagePosition];
	
	$query = "SELECT images.id, images.name, images.description, images.tags, images.submitiontimestamp, images.albumid, images.albumtagid, images.fileurl, albums.name AS albumname, tags.name AS categoryname "
			." FROM images, albums, tags "
			." WHERE images.albumid = albums.id AND images.albumtagid = tags.id AND "
			." images.id='".$getImageID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','getImage','true');
	$num = @mysql_num_rows($result);

	if($num==0){}//no image found
	else
	{
		$taggedImageVars['id'] = @mysql_result($result,0,'id');
		$taggedImageVars['name'] = @mysql_result($result,0,'name');
		if(($taggedImageVars['name']=='')||(strtolower($taggedImageVars['name'])=='null')){$taggedImageVars['name']='';}
		$taggedImageVars['description'] = @mysql_result($result,0,'description');
		if(($taggedImageVars['description']=='')||(strtolower($taggedImageVars['description'])=='null')){$taggedImageVars['description']='';}
		$taggedImageVars['tags'] = @mysql_result($result,0,'tags');
		$taggedImageVars['fileurl'] = @mysql_result($result,0,'fileurl');
		$taggedImageVars['submitiontimestamp'] = convertTimeStamp(@mysql_result($result,0,'submitiontimestamp'),'full');	
		$taggedImageVars['albumid'] = @mysql_result($result,0,'albumid');
		$taggedImageVars['albumtagid'] = @mysql_result($result,0,'albumtagid');
		$taggedImageVars['albumname'] = @mysql_result($result,0,'albumname');
		$taggedImageVars['categoryname'] = @mysql_result($result,0,'categoryname');	
		$taggedImageVars['imageposition'] = ($getImagePosition+1);
		$taggedImageVars['albumimagescount'] = ((count($tempExplodedArr))-1);
	
		if($taggedImageVars['tags']==''){$taggedImageVars['tags'] = '-';}
		else{$taggedImageVars['tags'] = getImageTagNames($taggedImageVars['tags'],'ajax');}
	}//else
	unset($dbobj);
	unset($validator);
	getImageXMLresponse(0,$taggedImageVars);
}//getImage($categoryID,$albumID,$imageID,$type)

function getTaggedImage($type)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$errorCode = NULL;
	$getImageID = NULL;
	$getImagePosition = NULL;
	$tempExplodedArr = NULL;
	$taggedImageVars = array();
	$imageVars = array();
	$tempImageVars = array();
	$tempExplodedTagsArr = array();
	$taggedImagesOrder = '';
	
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	
	//check for CSRF (Cross Site Request Forgery)
	//echo CSRF_PASS_GEN.'gettaggedimage' . ' '.$_POST['pageid']; exit;
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'gettaggedimage', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	$categoryID = $_POST["categoryid"];
	$albumID = $_POST["albumid"];
	$imageID = $_POST["imageid"];
	$tagID = $_POST["tagid"];
	unset($_POST["categoryid"]); unset($_POST["albumid"]); unset($_POST["imageid"]); unset($_POST["tagid"]);

	$query00 = "SELECT images.id, images.name, images.tags FROM images, albums"
			. " WHERE images.albumid=albums.id AND albums.visibility='true' ORDER BY submitiontimestamp DESC; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','getTaggedImage','true');
	$num00 = @mysql_num_rows($result00);
	
	if($num00==0){}//no images in the album to display!
	else
	{
		for($i=0; $i<$num00; $i++)
		{
			$tempImageID = @mysql_result($result00,$i,'id');
			$imageVars[$tempImageID]['name'] = @mysql_result($result00,$i,'name');
			$imageVars[$tempImageID]['tags'] = @mysql_result($result00,$i,'tags');
		}
	}//else
	
	reset($imageVars);
	while (list($imagekey, $imageval) = each ($imageVars))
	{
		$tempExplodedTagsArr = explode('.::.',$imageVars[$imagekey]['tags']);
		for($i=0; $i<count($tempExplodedTagsArr); $i++){
			if($tempExplodedTagsArr[$i]==$tagID)
				{ $tempImageVars[$imagekey]=$imageVars[$imagekey]; $taggedImagesOrder.=$imagekey.'.::.'; unset($imageVars[$imagekey]);}
		}
	}//
	unset($imageVars); $imageVars = $tempImageVars; unset($tempImageVars);

	$tempExplodedArr = explode('.::.',$taggedImagesOrder);
	for($i=0; $i<count($tempExplodedArr); $i++)
	{
		$tempPosition = $i;
		if($imageID==$tempExplodedArr[$i])
		{
			if($type=='previous'){$getImagePosition = --$tempPosition;}
			elseif($type=='next'){$getImagePosition = ++$tempPosition;}
		}
	}//for
	if($getImagePosition >= $tempPosition){$getImagePosition = 0;}
	if($getImagePosition < 0){$getImagePosition = --$tempPosition;}

	$getImageID = (int)$tempExplodedArr[$getImagePosition];

	//$query = "SELECT * FROM images WHERE albumid='".$albumID."' AND albumtagid='".$categoryID."' AND images.id='".$getImageID."'; ";
	
	$query = "SELECT images.id, images.name, images.description, images.tags, images.submitiontimestamp, images.albumid, images.albumtagid, images.fileurl, albums.name AS albumname, tags.name AS categoryname "
			." FROM images, albums, tags "
			." WHERE images.albumid = albums.id AND images.albumtagid = tags.id AND"
			." images.id='".$getImageID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','getTaggedImage','true');
	$num = @mysql_num_rows($result);

	if($num==0){}//no image found
	else
	{
		$taggedImageVars['id'] = @mysql_result($result,0,'id');
		$taggedImageVars['name'] = @mysql_result($result,0,'name');
		if(($taggedImageVars['name']=='')||(strtolower($taggedImageVars['name'])=='null')){$taggedImageVars['name']='';}
		$taggedImageVars['description'] = @mysql_result($result,0,'description');
		if(($taggedImageVars['description']=='')||(strtolower($taggedImageVars['description'])=='null')){$taggedImageVars['description']='';}
		$taggedImageVars['tags'] = @mysql_result($result,0,'tags');
		$taggedImageVars['fileurl'] = @mysql_result($result,0,'fileurl');
		$taggedImageVars['submitiontimestamp'] = convertTimeStamp(@mysql_result($result,0,'submitiontimestamp'),'full');		
		$taggedImageVars['albumid'] = @mysql_result($result,0,'albumid');
		$taggedImageVars['albumtagid'] = @mysql_result($result,0,'albumtagid');
		$taggedImageVars['albumname'] = @mysql_result($result,0,'albumname');
		$taggedImageVars['categoryname'] = @mysql_result($result,0,'categoryname');		
		$taggedImageVars['imageposition'] = ($getImagePosition+1);
		$taggedImageVars['albumimagescount'] = ((count($tempExplodedArr))-1);
	
		if($taggedImageVars['tags']==''){$taggedImageVars['tags'] = '-';}
		else{$taggedImageVars['tags'] = getImageTagNames($taggedImageVars['tags'],'ajax');}
	}//else
	unset($dbobj);
	unset($validator);
	getImageXMLresponse(0,$taggedImageVars);
}//getTaggedImage($categoryID,$albumID,$imageID,$type)

function loadImageThumbnails($categoryID, $albumID, $imagesOrder)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();

	$imageVars = array();
	$explodedArr = array();

	$query = "SELECT id, name, tags, description, submitiontimestamp, fileurl, filename FROM images WHERE images.albumid='".$albumID."' AND images.albumtagid='".$categoryID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadImageThumbnails','false');
	$num = @mysql_num_rows($result);
	
	if($num==0){}//no images in the album to display!
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempImageID = mysql_result($result,$i,'id');
			$imageVars[$tempImageID]['name'] = mysql_result($result,$i,'name');
			$imageVars[$tempImageID]['tags'] = mysql_result($result,$i,'tags');
			$imageVars[$tempImageID]['description'] = mysql_result($result,$i,'description');
			$imageVars[$tempImageID]['submitiontimestamp'] = mysql_result($result,$i,'submitiontimestamp');
			$imageVars[$tempImageID]['fileurl'] = mysql_result($result,$i,'fileurl');
			$imageVars[$tempImageID]['filename'] = mysql_result($result,$i,'filename');
		}
	}//else
	
	if(($imagesOrder==0)||($imagesOrder=='')){$imagesOrderDisplayType = 'SubmitionTimestamp';}//if
	else
	{
		$explodedArr = explode('.::.',$imagesOrder); unset($explodedArr[count($explodedArr)-1]);//the last element is empty
		if(count($explodedArr)!=count($imageVars)){$imagesOrderDisplayType = 'SubmitionTimestamp';} //then the order doesn't count
		else{$imagesOrderDisplayType = 'SelectedOrder';}
	}//else
	
	
	if($imagesOrderDisplayType=='SubmitionTimestamp')
	{
		reset($imageVars);
		echo "<div class='albumimagesthumbnails' id='albumimagesthumbnails'>";
		while (list($imagekey, $imageval) = each ($imageVars))
		{
			if($imageVars[$imagekey]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$imagekey]['name']);}
			else{$tempImageHTMLtitle = $imageVars[$imagekey]['filename'];}
			echo "<img class='imagethumbs' id='imagethumb_".$imagekey."' src='./jdimages/thumbnails/".$imageVars[$imagekey]['fileurl']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>";
		}//
		echo "</div>";
	}//if
	elseif($imagesOrderDisplayType=='SelectedOrder')
	{
		reset($imageVars);
		$explodedArr = explode('.::.',$imagesOrder); unset($explodedArr[count($explodedArr)-1]);//the last element is empty
	
		echo "<div class='albumimagesthumbnails' id='albumimagesthumbnails'>";
		//print first the images in the order of the $explodedArr
		//notice that the number of iterations has to do with how many images are in the $imageVars and not in the $explodedArr
		$tempCounter = count($imageVars);
		for($j=0; $j<$tempCounter; $j++)
		{
			if(isset($explodedArr[$j])){$tempImageID = $explodedArr[$j];}
			if(!isset($imageVars[$tempImageID]['name'])){continue;}
			if($imageVars[$tempImageID]['name']!=''&&$imageVars[$tempImageID]['name']!='null'){$tempImageHTMLtitle = strtoupper($imageVars[$tempImageID]['name']);}
			else{$tempImageHTMLtitle = $imageVars[$tempImageID]['filename'];}
			echo "<img class='imagethumbs' id='imagethumb_".$tempImageID."' src='./jdimages/thumbnails/".$imageVars[$tempImageID]['fileurl']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>";
			unset($imageVars[$tempImageID]);
		}//
		//now print the rest of the images that are not in order and are in the $imageVars array
		reset($imageVars);
		while (list($imagekey, $imageval) = each ($imageVars))
		{
			if($imageVars[$imagekey]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$imagekey]['name']);}
			else{$tempImageHTMLtitle = $imageVars[$imagekey]['filename'];}
			echo "<img class='imagethumbs' id='imagethumb_".$imagekey."' src='./jdimages/thumbnails/".$imageVars[$imagekey]['fileurl']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>";
		}//
		echo "</div>";
	}//
	
	unset($dbobj);
}//loadImageThumbnails($categoryID, $albumID)

function validateImageID($file)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	if($file=='albumsinc.js')
	{
		######################
		//check if the $_GET table has only the value we want, 
		//and the value is of the type we want
		//returns the value we want trimmed
		if(!isset($_GET['albumid']) || !isset($_GET['categoryid']) || !isset($_GET['imageid'])){redirects(0,'');}
		$getVarType['categoryid'] = "([^0-9]+)";
		$getVarType['albumid'] = "([^0-9]+)";
		$getVarType['imageid'] = "([^0-9]+)";
		$validatedVars = $validator->checkGetVariable(3,0,$getVarType);
		$categoryID = $validatedVars["categoryid"];
		$albumID = $validatedVars['albumid'];
		$imageID = $validatedVars['imageid'];
		######################
		unset($validator);
		$_SESSION['IMGID']=$imageID;
		redirects(13,"?categoryid=".$categoryID."&albumid=".$albumID."");
	}
	elseif($file=='postsinc.js')
	{
		######################
		//check if the $_GET table has only the value we want, 
		//and the value is of the type we want
		//returns the value we want trimmed
		if(!isset($_GET['imageid'])){redirects(0,'');}
		$getVarType['imageid'] = "([^0-9]+)";
		$validatedVars = $validator->checkGetVariable(1,0,$getVarType);
		$imageID = $validatedVars['imageid'];
		######################
		$query = "SELECT id, albumid, albumtagid "
				." FROM images WHERE id='".$imageID."'; ";
		$result = @mysql_query($query) or die("error executing query "+$query);
		$num = @mysql_num_rows($result);
		
		if($num==0){redirects(0,'');}//image not found
		else{$categoryID = @mysql_result($result,0,'albumtagid'); $albumID = @mysql_result($result,0,'albumid');}
		unset($dbobj);
		unset($validator);
		$_SESSION['IMGID']=$imageID;
		redirects(13,"?categoryid=".$categoryID."&albumid=".$albumID."");
	}
	elseif($file=='tags.php')
	{
		######################
		//check if the $_GET table has only the value we want, 
		//and the value is of the type we want
		//returns the value we want trimmed
		if(!isset($_GET['albumid']) || !isset($_GET['categoryid']) || !isset($_GET['imageid']) || !isset($_GET['tagid'])){redirects(0,'');}
		$getVarType['categoryid'] = "([^0-9]+)";
		$getVarType['albumid'] = "([^0-9]+)";
		$getVarType['imageid'] = "([^0-9]+)";
		$getVarType['tagid'] = "([^0-9]+)";
		$validatedVars = $validator->checkGetVariable(4,0,$getVarType);
		$categoryID = $validatedVars["categoryid"];
		$albumID = $validatedVars['albumid'];
		$imageID = $validatedVars['imageid'];
		$tagID = $validatedVars['tagid'];
		######################
		unset($validator);
		$_SESSION['IMGID']=$imageID;
		redirects(17,"?categoryid=".$categoryID."&albumid=".$albumID."&tagid=".$tagID);
	}
}//validateImageID($file)

function findImage($categoryID,$albumID,$imageID)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$query = "SELECT images.id, images.name, images.description, images.tags, images.submitiontimestamp, images.albumid, images.albumtagid, images.fileurl, albums.name AS albumname, tags.name AS categoryname "
			." FROM images, albums, tags "
			." WHERE images.albumid = albums.id AND images.albumtagid = tags.id AND "
			." images.albumid='".$albumID."' AND images.albumtagid='".$categoryID."' AND images.id='".$imageID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','findImage','true');
	$num = @mysql_num_rows($result);
	
	if($num==0){redirects(0,'');}//image not found
	else
	{
		$imageVars['id']=@mysql_result($result,0,'id');
		$imageVars['name']=@mysql_result($result,0,'name');
		if($imageVars['name']==''||$imageVars['name']=='null'){$imageVars['name']='unnamed';}
		$imageVars['description']=@mysql_result($result,0,'description');
		if(($imageVars['description']=='')||(strtolower($imageVars['description'])=='null')){$imageVars['description']='';}
		$imageVars['tags']=@mysql_result($result,0,'tags');
		$imageVars['submitiontimestamp']=convertTimeStamp(@mysql_result($result,0,'submitiontimestamp'),'full');
		$imageVars['fileurl']=@mysql_result($result,0,'fileurl');
		$imageVars['imagealbumname']=@mysql_result($result,0,'albumname');
		$imageVars['imagecategoryname']=@mysql_result($result,0,'categoryname');
	}

	unset($_SESSION['IMGID']);
	unset($dbobj);
	unset($validator);
	return $imageVars;
}//findImage(imageID)

function loadAlbums($categoryID)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$albumVars = array();
	
	$query01 = "SELECT albums.id, albums.name, albums.description, albums.tagid, albums.coverid, albums.lastupdatedtimestamp, COUNT(images.id) as imagecount "
			." FROM albums, images"
			." WHERE albums.visibility='true' AND albums.id=images.albumid AND albums.tagid='".$categoryID."' GROUP BY(albums.id) ORDER BY albums.lastupdatedtimestamp DESC; ";
	$query02 = "SELECT images.id, images.name, images.fileurl, images.albumid FROM images WHERE images.albumtagid='".$categoryID."'; ";
	
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','loadAlbums','false');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','loadAlbums','false');
	$num02 = @mysql_num_rows($result02);	
	
	if($num01==0){redirects(0,'');}//no albums in the category
	else
	{
		for($i=0; $i<$num01; $i++)
		{
			$tempAlbumID = mysql_result($result01,$i,'id');
			$albumVars[$tempAlbumID]['name'] = mysql_result($result01,$i,'name');
			$albumVars[$tempAlbumID]['description'] = nl2br(mysql_result($result01,$i,'description'));
			$albumVars[$tempAlbumID]['categoryid'] = mysql_result($result01,$i,'tagid');
			$albumVars[$tempAlbumID]['coverid'] = mysql_result($result01,$i,'coverid');
			$albumVars[$tempAlbumID]['coverthumb'] = "defaultcover.png";
			$albumVars[$tempAlbumID]['albumcoverfullpath'] = './jdimages/largethumbnails/'.$albumVars[$tempAlbumID]['coverthumb'];
			$albumVars[$tempAlbumID]['imagecount'] = mysql_result($result01,$i,'imagecount');
			$albumVars[$tempAlbumID]['lastupdatedtimestamp'] = convertTimeStamp(mysql_result($result01,$i,'lastupdatedtimestamp'),'full');		
		
			if( $albumVars[$tempAlbumID]['description']=='' || strtolower($albumVars[$tempAlbumID]['description'])=='null' )
			{$albumVars[$tempAlbumID]['description']='';}
		}//for
	}//else
	
	if($num02==0){}//no images in the albums
	{
		for($j=0; $j<$num02; $j++)
		{
			$tempImageID = mysql_result($result02,$j,'id');
			$tempImageName = mysql_result($result02,$j,'name');
			$tempImageFileURL = mysql_result($result02,$j,'fileurl');
			$tempImageAlbumID = mysql_result($result02,$j,'albumid');
			if($tempImageID == $albumVars[$tempImageAlbumID]['coverid'])
			{
				$albumVars[$tempImageAlbumID]['coverthumb'] = $tempImageFileURL;
				$albumVars[$tempImageAlbumID]['albumcoverfullpath'] = './jdimages/largethumbnails/'.$tempImageFileURL.'';
			}//IF
		}//for
	}//if
	
	reset($albumVars);
	while (list($albumkey, $albumval) = each ($albumVars))
	{
		echo "<ul class='categoryalbums'>";
		echo "<li class='albumnames' id='albumname_".$albumkey."'>".$albumVars[$albumkey]['name']."</li>";
		echo "<li class='albumcovers'>"."<img src='".$albumVars[$albumkey]['albumcoverfullpath']."' alt='Album Cover' title='Album Cover' />"."</li>";
		echo "<li class='albumdescriptions'>".$albumVars[$albumkey]['description']."</li>";
		echo "<li class='albumimagecounts'>".$albumVars[$albumkey]['imagecount']." images"."</li>";
		echo "<li class='albumupdatedtimestamps'>"."updated: ".$albumVars[$albumkey]['lastupdatedtimestamp']."</li>";
		echo "</ul>";
	}//
	unset($dbobj);
	unset($validator);
}//loadAlbums($categoryID)

function getXHTMLPostsImages($postImages,$thumbstype)
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();

	$tempExplodeImageIDsArr = array();
	$returnString = '';

	if($postImages==''){}//do nothing
	else
	{
		$query="SELECT images.id, images.name, images.description, images.tags, images.submitiontimestamp, images.albumid, images.filename, images.albumtagid, images.fileurl, albums.visibility FROM images, albums WHERE images.albumid=albums.id; ";
		$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','getXHTMLPostsImages','true');
		$num = @mysql_num_rows($result);
		
		//get all the images from DB
		for($z=0; $z<$num; $z++)
		{
			$tempImageID = @mysql_result($result,$z,'id');
			if($tempImageID==0){continue;}
			$imageVars[$tempImageID]['id'] = $tempImageID; //seems redundant but do not delete this
			$imageVars[$tempImageID]['name'] = @mysql_result($result,$z,'name');
			$imageVars[$tempImageID]['description'] = @mysql_result($result,$z,'description');
			$imageVars[$tempImageID]['filename'] = @mysql_result($result,$z,'filename');
			$imageVars[$tempImageID]['fileurl'] = @mysql_result($result,$z,'fileurl');
			$imageVars[$tempImageID]['submitiontimestamp'] = @mysql_result($result,$z,'submitiontimestamp');
			$imageVars[$tempImageID]['albumvisibility'] = @mysql_result($result,$z,'visibility');
			
			if($thumbstype=='largethumbnails')
				{$imageVars[$tempImageID]['imagefullpath'] = './jdimages/largethumbnails/'.$imageVars[$tempImageID]['fileurl'];}
			elseif($thumbstype=='thumbnails')
				{$imageVars[$tempImageID]['imagefullpath'] = './jdimages/thumbnails/'.$imageVars[$tempImageID]['fileurl'];}
		}//for
		if($num!=0)
		{
			reset($imageVars);
			$tempExplodeImageIDsArr = explode('.::.',$postImages);
			$returnString = "<ul class='postimageuls'>";
			$returnString .= "<li class='postimagebanners'>Post Images: </li>";
			for($k=0; $k<count($tempExplodeImageIDsArr); $k++)
			{
				$tempImageID = $tempExplodeImageIDsArr[$k];
				if(isset($imageVars[intval($tempImageID)]['id']))
				{
					if($imageVars[$tempImageID]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$tempImageID]['name']);}
					else{$tempImageHTMLtitle = $imageVars[$tempImageID]['filename'];}
					
					if($imageVars[$tempImageID]['albumvisibility']=='true')
						{$returnString .= "<li class='postimagethumbs'><img src='".$imageVars[$tempImageID]['imagefullpath'].
							"' id='postimagethumb_".$tempImageID."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."' /></li>";}
					else if($imageVars[$tempImageID]['albumvisibility']=='false')
						{$returnString .= "<li class='postimagethumbs'><a href='"."./jdimages/fullresolution/".$imageVars[$tempImageID]['fileurl']."'><img src='".$imageVars[$tempImageID]['imagefullpath'].
							"' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."' /></a></li>";}
				}//if
			}//for
			$returnString .= "</ul>";
		}//if
		unset($tempExplodeImageIDsArr);
	}//else
	
	unset($dbobj);
	unset($validator);
	return $returnString;
}//getXHTMLPostsImages($postImages)

function getAllPostTimestamps()
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$displayImageUpdatePosts='visible';
	$settingsArr = loadSettings(); $displayImageUpdatePosts = $settingsArr['postsimagesupdates'];
	if($displayImageUpdatePosts=='visible'){$sqlFragment = "";}
	elseif($displayImageUpdatePosts=='invisible'){$sqlFragment = " WHERE type='newspost' ";}//else
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$postTimestamp = array();
	$monthNames = array( '1'=>'January','01'=>'January','2'=>'February','02'=>'February',
		'3'=>'March','03'=>'March','4'=>'April','04'=>'April','5'=>'May','05'=>'May',
		'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'August','08'=>'August',
		'9'=>'September','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	
	$query = "SELECT * FROM posts ".$sqlFragment." ORDER BY creationtimestamp DESC;";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','getAllPostTimestamps','false');
	$num = @mysql_num_rows($result);
	
	if($num==0){}//no results, do nothing
	else
	{
		for($i=0; $i<$num; $i++)
		{
			if($i==0)// the query gets the most recent post FIRST, and we need to store it!
			{
				
				$mostRecentPostTimestamp = explode(' ',mysql_result($result,$i,'creationtimestamp'));
				$mostRecentPostTimestamp = explode('-',$mostRecentPostTimestamp[0]);
				$mostRecentPostTimestampYear = $mostRecentPostTimestamp[0]; //0 is for year
				$mostRecentPostTimestampMonth = $mostRecentPostTimestamp[1]; //1 is for month
				$mostRecentPostTimestampDay = $mostRecentPostTimestamp[2]; //2 is for day
				
				$postTimestampDays[$i]['day'] = $mostRecentPostTimestampDay;
				$postTimestampDays[$i]['id'] = mysql_result($result,$i,'id');
				$postTimestampDays[$i]['headline'] = mysql_result($result,$i,'headline');
				
				$postTimestampMonths[$mostRecentPostTimestampMonth] = $monthNames[$mostRecentPostTimestampMonth];	
				$postTimestampYears[$mostRecentPostTimestampYear] = $mostRecentPostTimestampYear;
			}
			else
			{
				$tempCreationTimestamp = explode(' ',mysql_result($result,$i,'creationtimestamp'));
				$tempCreationTimestamp = explode('-',$tempCreationTimestamp[0]);
				$tempCreationTimestampYear = $tempCreationTimestamp[0]; //0 is for year
				$tempCreationTimestampMonth = $tempCreationTimestamp[1]; //1 is for month
				$tempCreationTimestampDay = $tempCreationTimestamp[2]; //2 is for day
				
				if($tempCreationTimestampYear == $mostRecentPostTimestampYear)
				{
					if($tempCreationTimestampMonth == $mostRecentPostTimestampMonth)
					{
						$postTimestampDays[$i]['day'] = $tempCreationTimestampDay;
						$postTimestampDays[$i]['headline'] = mysql_result($result,$i,'headline');
						$postTimestampDays[$i]['id'] = mysql_result($result,$i,'id');
					}
					$postTimestampMonths[$tempCreationTimestampMonth] = $monthNames[$tempCreationTimestampMonth];
				}		
				$postTimestampYears[$tempCreationTimestampYear] = $tempCreationTimestampYear;
			}
		}//for
		
		echo "<div class='statictips'>Use the navigation below to browse through the posts archive.</div>";
		
		reset($postTimestampYears);
		echo "<ul class='postsarchiveyears' id='postsarchiveyear'>";
			while (list($key, $val) = each ($postTimestampYears))
			{
				echo "<li id='postyear_".$key."'>".$postTimestampYears[$key]."</li>";
			}//while
		echo "</ul>";

		echo "<div class='biglineverticals01'></div>";
		reset($postTimestampMonths);
		echo "<span id='postsarchivemonthsplaceholder'>";
		echo "<ul class='postsarchivemonths' id='postsarchivemonth'>";
			while (list($key, $val) = each ($postTimestampMonths))
			{
				echo "<li id='postmonth_".$key."'>".$postTimestampMonths[$key]." ".$mostRecentPostTimestampYear."</li>";
			}//while
		echo "</ul>";
		echo "</span>";
		
		echo "<div class='biglineverticals02'></div>";
		reset($postTimestampDays);
		echo "<span id='postsarchivedaysplaceholder'>";
		echo "<ul class='postsarchivedays' id='postsarchiveday'>";
			while (list($key, $val) = each ($postTimestampDays))
			{
				echo "<li id='".$key."'>"
					."<span class='days'>".$postTimestampDays[$key]['day']."</span>"
					." <span class='postheadlines'>"
						."<a href='./posts.php?postid=".$postTimestampDays[$key]['id']."' id='postday_".$key."' title='Click to read post'>"
						.$postTimestampDays[$key]['headline']
						."</a>"
					."</span>"
					."</li>";
			}//while
		echo "</ul>";
		echo "</span>";
		echo "<span id='syearid' class='hidden'>".$mostRecentPostTimestampYear."</span>";
	}//else

	unset($dbobj);
	unset($validator);
}//getAllPostTimestamps()

function getPostElements()
{
	require_once('validate.class.php');
	require_once('jddbase.class.php');
	
	$displayImageUpdatePosts = 'visible';
	$settingsArr = loadSettings(); $displayImageUpdatePosts = $settingsArr['postsimagesupdates'];
	if($displayImageUpdatePosts=='visible'){$sqlFragment = "";}
	elseif($displayImageUpdatePosts=='invisible'){$sqlFragment = " WHERE type='newspost' ";}//else
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	
	$postTimestamp = array();
	$monthNames = array( '1'=>'January','01'=>'January','2'=>'February','02'=>'February',
		'3'=>'March','03'=>'March','4'=>'April','04'=>'April','5'=>'May','05'=>'May',
		'6'=>'June','06'=>'June','7'=>'July','07'=>'July','8'=>'August','08'=>'August',
		'9'=>'September','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
	
	$errorCode = NULL;
	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!$validator->checkPost()){errorHandler(701,'ajax'); exit;}
	else {} //all ok
	//check for CSRF (Cross Site Request Forgery)
	if(!$validator->checkCSRF($_POST["csrf"], CSRF_PASS_GEN.'getpostelements', $_POST['pageid'])){errorHandler(702,'ajax'); exit;} //error Redirect()
	else{ unset($_POST["csrf"]); unset($_POST["pageid"]); }//all ok
	
	
	if(isset($_POST['yearid']))
	{
		$postYear = $_POST['yearid']; unset($_POST['yearid']);
		if(isset($_POST['monthid'])){$postMonth=$_POST['monthid']; unset($_POST['monthid']);}else{$postMonth='';}
		
		$query = "SELECT * FROM posts ".$sqlFragment." ORDER BY creationtimestamp DESC;";
		$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'ajax','','','getPostElements','true');
		$num = @mysql_num_rows($result);
		if($num==0){}//no results, do nothing
		else
		{
			for($i=0; $i<$num; $i++)
			{
				$tempCreationTimestamp = explode(' ',mysql_result($result,$i,'creationtimestamp'));
				$tempCreationTimestamp = explode('-',$tempCreationTimestamp[0]);
				$tempCreationTimestampYear = $tempCreationTimestamp[0]; //0 is for year
				$tempCreationTimestampMonth = $tempCreationTimestamp[1]; //1 is for month
				$tempCreationTimestampDay = $tempCreationTimestamp[2]; //2 is for day
				
				if($tempCreationTimestampYear == $postYear)
				{
					if($tempCreationTimestampMonth == $postMonth)
					{
						$postTimestampDays[$i]['day'] = $tempCreationTimestampDay;
						$postTimestampDays[$i]['headline'] = mysql_result($result,$i,'headline');
						$postTimestampDays[$i]['id'] = mysql_result($result,$i,'id');
					}//if
					$postTimestampMonths[$tempCreationTimestampMonth] = $monthNames[$tempCreationTimestampMonth];
				}//if
			}//for
		}//else
		
		unset($dbobj);
		unset($validator);
		if($postMonth==''){getPostElementsXMLresponse(0,$postTimestampMonths,'months');}//if
		else{getPostElementsXMLresponse(0,$postTimestampDays,'headlines');}//else
	}//if(isset($_GET['yearid']))
}//getPostElements()

function getImageTagNames($imageTagString,$type)
{
	require_once('jddbase.class.php');
	
	$validator = new Validate();
	$dbobj = new JDDBase();
	$explodedImagesTagString = array();
	$tags = array();
	
	$query = "SELECT * FROM tags WHERE type='image' ORDER BY id ASC;";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','getImageTagNames','false');
	$num = @mysql_num_rows($result);
	
	
	$explodedImagesTagString = explode('.::.', $imageTagString);
	for($i=0; $i<$num; $i++)
	{
		$dbTagID = @mysql_result($result,$i,'id');
		$dbTagName = @mysql_result($result,$i,'name');
		for($j=0; $j<count($explodedImagesTagString); $j++)
			{if($dbTagID == $explodedImagesTagString[$j]){$tags[$dbTagID] = $dbTagName;}}//for
	}//for
	
	reset($tags);
	$tagsStringAJAX='';
	$tagsStringPHP='<ul class="tags">';
	while (list($tagID, $tagName)=each ($tags))
	{
		$tagsStringAJAX.='<tag>'.$tagID.'.::.'.$tagName.'</tag>';
		$tagsStringPHP.='<li>'.'<a href="./tags.php?tagid='.$tagID.'" title="Tag Name">'.$tagName.'</a>,'.'</li> ';
	}//for
	$tagsStringPHP.='</ul>';
	
	unset($dbobj);
	unset($validator);
	if($type=='ajax'){return $tagsStringAJAX;}
	elseif($type='php'){return $tagsStringPHP;}
}//getImageTagNames($imageTagString)

function loadTaggedImageThumbnails($tagID)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();

	$imageVars = array();
	$tempImageVars = array();
	$tempExplodedTagsArr = array();

	$query = "SELECT images.id, images.name, images.tags, images.description, images.submitiontimestamp, images.fileurl, images.filename, images.albumid, images.albumtagid"
		." FROM images, albums"
		." WHERE images.albumid=albums.id AND albums.visibility='true'"
		." ORDER BY images.submitiontimestamp DESC; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadTaggedImageThumbnails','false');
	$num = @mysql_num_rows($result);
	
	if($num==0){redirects(0,'');}//no images in the album to display!
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempImageID = @mysql_result($result,$i,'id');
			$imageVars[$tempImageID]['name'] = @mysql_result($result,$i,'name');
			$imageVars[$tempImageID]['tags'] = @mysql_result($result,$i,'tags');
			$imageVars[$tempImageID]['description'] = @mysql_result($result,$i,'description');
			$imageVars[$tempImageID]['submitiontimestamp'] = @mysql_result($result,$i,'submitiontimestamp');
			$imageVars[$tempImageID]['fileurl'] = @mysql_result($result,$i,'fileurl');
			$imageVars[$tempImageID]['filename'] = @mysql_result($result,$i,'filename');
			$imageVars[$tempImageID]['albumid'] = @mysql_result($result,$i,'albumid');
			$imageVars[$tempImageID]['categoryid'] = @mysql_result($result,$i,'albumtagid');
		}
	}//else
	
	reset($imageVars);
	while (list($imagekey, $imageval) = each ($imageVars))
	{
		$tempExplodedTagsArr = explode('.::.',$imageVars[$imagekey]['tags']);
		for($i=0; $i<count($tempExplodedTagsArr); $i++)
			{if($tempExplodedTagsArr[$i]==$tagID){ $tempImageVars[$imagekey]=$imageVars[$imagekey]; unset($imageVars[$imagekey]); }}
	}//
	unset($imageVars); $imageVars = $tempImageVars; unset($tempImageVars);
	
	reset($imageVars);
	echo "<div class='taggedimagesthumbnails' id='taggedimagesthumbnails'>";
	while (list($imagekey, $imageval) = each ($imageVars))
	{
		if($imageVars[$imagekey]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$imagekey]['name']);}
		else{$tempImageHTMLtitle = $imageVars[$imagekey]['filename'];}
		echo "<a href='./jdincfunctions/functionsinc.php?categoryid=".$imageVars[$imagekey]['categoryid']
			."&albumid=".$imageVars[$imagekey]['albumid']
			."&imageid=".$imagekey
			."&tagid=".$tagID
			."&type=34' title='View Image'>";
		echo "<img class='imagethumbs' id='imagethumb_".$imagekey."' src='./jdimages/thumbnails/".$imageVars[$imagekey]['fileurl']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>";
		echo "</a>";
	}//
	echo "</div>";
	
	unset($dbobj);
}//loadTaggedImageThumbnails($tagID)

function displayImageTags()
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();
	$tagsArr = array();
	$imagesArr = array();
	$tempExplodeArr = array();
	$orphanTagsArr = array();
	$minValue=0;
	$maxValue=0;
	
	$query01 = "SELECT * FROM tags WHERE type='image'; ";
	$query02 = "SELECT images.id, images.name, images.tags FROM images, albums WHERE images.albumid=albums.id AND albums.visibility='true'; ";
	
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','displayImageTags','true');
	$num01 = @mysql_num_rows($result01);
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','displayImageTags','true');
	$num02 = @mysql_num_rows($result02);	
	
	if($num01!=0)
	{
		for($i=0; $i<$num01; $i++)
		{
			$tagID = @mysql_result($result01,$i,'id');
			$tagsArr[$tagID]['name'] = @mysql_result($result01,$i,'name');
			$tagsArr[$tagID]['imagesno'] = 0;
		}//for
		for($j=0; $j<$num02; $j++)
		{
			$imageID = @mysql_result($result02,$j,'id');
			$imagesArr[$imageID]['name'] = @mysql_result($result02,$j,'name');
			$imagesArr[$imageID]['tags'] = @mysql_result($result02,$j,'tags');
		}//for
		
		reset($imagesArr);
		while (list($imagekey, $imageval) = each ($imagesArr))
		{
			$tempExplodeArr = explode('.::.',$imagesArr[$imagekey]['tags']);
			for($i=0; $i<count($tempExplodeArr); $i++)
			{
				if($tempExplodeArr[$i]==''){continue;}
				$tagsArr[$tempExplodeArr[$i]]['imagesno']++;
				
				$maxValue = $tagsArr[$tempExplodeArr[$i]]['imagesno'];
				$minValue = $tagsArr[$tempExplodeArr[$i]]['imagesno'];
			}//for
		}//while
		
		//remove from array all tags that have no images attached to them
		reset($tagsArr);
		while (list($key, $val) = each ($tagsArr))
			{if($tagsArr[$key]['imagesno']==0||!isset($tagsArr[$key]['name'])){$orphanTagsArr[$key]=$tagsArr[$key]; unset($tagsArr[$key]);}}//while
		
		//find the min and max values of tag occurances in images
		reset($tagsArr);
		while (list($key, $val) = each ($tagsArr))
			{if($tagsArr[$key]['imagesno']>$maxValue){$maxValue=$tagsArr[$key]['imagesno'];}
			if($tagsArr[$key]['imagesno']<$minValue){$minValue=$tagsArr[$key]['imagesno'];}}//while
		
		//difference between max and min
		$difference = $maxValue-$minValue;
		$distribution = $difference/3;
		
		unset($dbobj);
		
		if(count($orphanTagsArr)==0){cleanOrphanImageTags($orphanTagsArr);}
		
		//display tags
		$tagsUL = '<ul>';
		reset($tagsArr);
		while (list($key, $val) = each ($tagsArr))
		{
			$liClass='smallTag';
			if($tagsArr[$key]['imagesno']==$minValue){$liClass='smallestTag';}
			else if($tagsArr[$key]['imagesno']==$maxValue){$liClass='largestTag';}
			else if($tagsArr[$key]['imagesno']>($minValue+($distribution*2))){$liClass='largeTag';}
			else if($tagsArr[$key]['imagesno']>($minValue+$distribution)){$liClass='mediumTag';}
	
			$tagsUL.='<li class="'.$liClass.'">'.'<a href="./tags.php?tagid='.$key.'" class="'.$aClass.'">'.$tagsArr[$key]['name'].'</a>'.' </li>';
		}//while
		$tagsUL.='</ul>';
		return $tagsUL;
	}
	else{return '';}
}//displayImageTags()

function displayPostComments($postID)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();
	$commentVars = array();
	$tempCommentID = NULL;
	
	$query = "SELECT * FROM comments WHERE postid='".$postID."' ORDER BY submitiontimestamp ASC; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','displayPostComments','false');
	$num = @mysql_num_rows($result);
	
	if($num==0){}//no images in the album to display!
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempCommentID = @mysql_result($result,$i,'id');
			$commentVars[$tempCommentID]['name'] = @mysql_result($result,$i,'name');
			$commentVars[$tempCommentID]['email'] = @mysql_result($result,$i,'email');
			$commentVars[$tempCommentID]['website'] = @mysql_result($result,$i,'website');
			$commentVars[$tempCommentID]['reply'] = @mysql_result($result,$i,'reply');
			$commentVars[$tempCommentID]['submitiontimestamp'] = @mysql_result($result,$i,'submitiontimestamp');
		
			$commentVars[$tempCommentID]['reply'] = nl2br($commentVars[$tempCommentID]['reply']);
		}
	}//else
	
	if($num!=0)
	{
		reset($commentVars);
		echo "<ul class='comments' id='comments'>";
		while (list($commentkey, $commentval) = each ($commentVars))
		{
			echo "<li id='comment_".$commentkey."'>";
			echo "<div id='commentelement_".$commentkey."'>";
			if((!isset($_SESSION['ADMIN_LOGIN'])) || ($_SESSION['ADMIN_LOGIN']!=TRUE) ||
				(!isset($_SESSION['ADMIN_USERNAME'])) || ($_SESSION['ADMIN_USERNAME']=='') ||
				(!isset($_SESSION['ADMIN_PASSWORD'])) || ($_SESSION['ADMIN_PASSWORD']==''))
				{echo "<a href='mailto:".$commentVars[$commentkey]['email']."'>".$commentVars[$commentkey]['name']."</a>"." said,";}
			else{echo "".$commentVars[$commentkey]['name'].""." said,";}
			echo "<div class='biglines'></div>";
			echo "<div class='timestamp'>".convertTimeStamp($commentVars[$commentkey]['submitiontimestamp'],'reallylong')."</div>";
			echo "<div class='reply'>".$commentVars[$commentkey]['reply']."</div>";
			echo "</div>";//commentelements
				
			if((isset($_SESSION['ADMIN_LOGIN'])) && ($_SESSION['ADMIN_LOGIN']==TRUE) &&
				(isset($_SESSION['ADMIN_USERNAME'])) && ($_SESSION['ADMIN_USERNAME']!='') &&
				(isset($_SESSION['ADMIN_PASSWORD'])) && ($_SESSION['ADMIN_PASSWORD']!=''))
				{echo '<div class="deletecommentbuttons" id="deletecommentbutton_'.$commentkey.'">delete</div>';
				echo '<ul id="commentdelete_'.$commentkey.'" class="commentdeletes">'
					.'<li class="deletecommentmsg">delete this comment?</li>'
					.'<li class="completedeletecomments"><span id="completedeletecomment_'.$commentkey.'">yes</span></li>'
					.'<li class="dontdeletecomments"><span id="dontdeletecomments_'.$commentkey.'">no</span></li>'
					.'</ul>';}
			echo "</li>";
		}//
		echo "<span id='commentanchor'></span>";
		echo "</ul>";
	}//
	else{echo "<ul class='comments' id='comments'>"."<span id='commentanchor'></span>"."</ul>";}
	
	echo "<div class='newcommentbanner'>Leave a reply </div>"
	. "<div class='biglines'></div>"
	."<div class='newcommentmessages'><span id='newcommentmessagesfailed' class='hidden'></span></div>";
	
	echo "\n\n<ul class='newcommentfrms' id='newcommentfrms'>";
   	
	if((!isset($_SESSION['ADMIN_LOGIN'])) || ($_SESSION['ADMIN_LOGIN']!=TRUE) ||
		(!isset($_SESSION['ADMIN_USERNAME'])) || ($_SESSION['ADMIN_USERNAME']=='') ||
		(!isset($_SESSION['ADMIN_PASSWORD'])) || ($_SESSION['ADMIN_PASSWORD']==''))
	{echo "<li class='name'>"
   		."<input type='text' maxlength='30'  class='text' id='name' name='name' value='your name (required)' />"
    ."</li>"
    ."<li class='email'>"
    	."<input type='text' maxlength='90'  class='text' id='email' name='email' value='your email (will not be published) (required)' />"
    ."</li>"
    ."<li class='website'>"
    	."<input type='text' maxlength='90'  class='text' id='website' name='website' value='your website (not required)' />"
    ."</li>";}
	else
		{echo "<li class='hidden'>"
			."<input type='text' maxlength='80'  class='text' id='name' name='name' value='James' />"
		."</li>"
		."<li class='hidden'>"
			."<input type='text' maxlength='90'  class='text' id='email' name='email' value='".PROFILE_EMAIL."' />"
		."</li>"
		."<li class='hidden'>"
			."<input type='text' maxlength='90'  class='text' id='website' name='website' value='http://www.jamesdoe.com' />"
		."</li>";}
	
	echo "<li class='comment'>"
    	."<textarea class='text' name='reply' id='reply' cols='21' rows='7' wrap='soft' title=''>your reply</textarea>"
		."<div class='charcounters'><span class='counters' id='ccounter'>".POST_COMMENT_MAX_LENGTH."</span> remaining characters</div>"
    ."</li>"
    ."<li class='commentssubmit'>"
    	."<input type='button' class='button' id='addreply_".$postID."' name='addreply_".$postID."' value='send'/>"
    ."</li>"
    ."</ul>";
	echo "<div class='newcommentloader'><span id='newcommentloader' class='hidden'/></span></div>";
	unset($_SESSION['displayPosts']);
	
	unset($dbobj);
}//displayPostComments($postID)


function loadAlbumsVisibilityAdm($profileID)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();
	$albumArr = array();
	$visibleAlbumArr = array();
	$invisibleAlbumArr = array();
	
	$query = "SELECT albums.id, albums.name, albums.tagid, albums.visibility, albums.creationtimestamp, albums.lastupdatedtimestamp, tags.name AS categoryname FROM albums, tags WHERE albums.tagid = tags.id ORDER BY tags.name ASC;";

	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadAlbumsVisibilityAdm','false');
	$num = @mysql_num_rows($result);

	if(isset($num)&&$num!=0)
	{
		for($i=0; $i<$num; $i++)
		{
			$tempAlbumID = @mysql_result($result,$i,'id');
			$albumArr[$tempAlbumID]['id'] = $tempAlbumID;
			$albumArr[$tempAlbumID]['name'] = strtolower(mysql_result($result,$i,'name'));
			$albumArr[$tempAlbumID]['tagid'] = @mysql_result($result,$i,'tagid');
			$albumArr[$tempAlbumID]['visibility'] = @mysql_result($result,$i,'visibility');
			$albumArr[$tempAlbumID]['creationtimestamp'] = @mysql_result($result,$i,'creationtimestamp');
			$albumArr[$tempAlbumID]['lastupdatedtimestamp'] = @mysql_result($result,$i,'lastupdatedtimestamp');
			$albumArr[$tempAlbumID]['categoryname'] = @mysql_result($result,$i,'categoryname');
		}//for
		reset($albumArr);
		while (list($albumkey, $albumval) = each ($albumArr))
		{
			if($albumArr[$albumkey]['visibility']=='true'){$visibleAlbumArr[$albumkey]=$albumArr[$albumkey];}
			else if($albumArr[$albumkey]['visibility']=='false'){$invisibleAlbumArr[$albumkey]=$albumArr[$albumkey];}
		}
	}
	
	reset($visibleAlbumArr);
	reset($invisibleAlbumArr);

	if(isset($num)&&$num!=0)
	{
    	echo "<li class='albums'>";
        	echo "<div class='statictips'>Select which albums are visible to visitors:</div>";
            echo "<div class='biglinehorizontal'></div>";
			echo "<span class='hidden' id='album_visibility'></span>";
            echo "<span class='tips' id='invisiblealbumstip' title='Tip.::.Click one to make visible'>";
            
			echo "<ul id='invisiblealbums'>";
			if(isset($invisibleAlbumArr))
			{
				while (list($albumkey, $albumval) = each ($invisibleAlbumArr))
				{
					echo "<li id='".$invisibleAlbumArr[$albumkey]['tagid']."::".$invisibleAlbumArr[$albumkey]['id']."'>";
						echo $invisibleAlbumArr[$albumkey]['categoryname']." &gt; ";
						echo "<span class='bold'>".$invisibleAlbumArr[$albumkey]['name']."</span>";
					echo "</li>";
               	}
			}
			echo "</ul>";
            echo "</span>";
            echo "<div class='biglineverticals'></div>";
            echo "<span class='tips' id='visiblealbumstip' title='Tip.::.Click one to make invisible'>";
            echo "<ul id='visiblealbums'>";
			if(isset($visibleAlbumArr))
			{
				while (list($albumkey, $albumval) = each ($visibleAlbumArr))
				{
					echo "<li id='".$visibleAlbumArr[$albumkey]['tagid']."::".$visibleAlbumArr[$albumkey]['id']."'>";
						echo $visibleAlbumArr[$albumkey]['categoryname']." &gt; ";
						echo "<span class='bold'>".$visibleAlbumArr[$albumkey]['name']."</span>";
					echo "</li>";
                }
			}
            echo "</ul>";
       		echo "</span>";
       echo "</li>";
	}
	else
	{
		echo "<li class='albums'>";
			echo "<div class='statictips'>Select which albums are visible to visitors:</div>";
           	echo "<div class='biglinehorizontal'></div>";
			echo "<span class='hidden' id='album_visibility'></span>";
            echo "<span class='tips' id='invisiblealbumstip' title='Tip.::.No Albums'>"."<ul id='invisiblealbums'>"."</ul>"."</span>";
            echo "<div class='biglineverticals'></div>";
            echo "<span class='tips' id='visiblealbumstip' title='Tip.::.No Albums'>"."<ul id='visiblealbums'>"."</ul>"."</span>";
		echo "</li>";
	}
	unset($dbobj);
}// loadAlbumsVisibilityAdm()

function loadProfilesAdm($profileID)
{
	require_once('jddbase.class.php');
	$dbobj = new JDDBase();
	$profileArr = array();
	
	$query = "SELECT id, username, email, blog "
			. " FROM profiles WHERE id='".$profileID."'";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadProfilesAdm','false');
	$num = @mysql_num_rows($result);

	if(isset($num)&&$num!=0)
	{
		$profileArr['id'] = @mysql_result($result,0,'id');
		$profileArr['username'] = @mysql_result($result,0,'username');
		$profileArr['email'] = @mysql_result($result,0,'email');
		$profileArr['blog'] = @mysql_result($result,0,'blog');
		if((strtolower($profileArr['blog'])=='null')||($profileArr['blog']=='(type your blog address)')){$profileArr['blog']='(type your blog address)';}
	}
	
	if(isset($num)&&$num!=0)
	{
	echo "<li class='profile'>"
		."<div class='statictips'>Profile Options:</div>"
		."<div class='biglinehorizontal'></div>"
		."<ul class='profileoptions' id='profileoptions'>"
			."<li><span class='elements'>Username:</span>"
				."<span class='editprofileelements' id='editprofileusername_".$profileID."' title='Profile Username'>".$profileArr['username']."</span>"
				."<span class='profilefrms' id='profileusernamefrm_".$profileID."'>"
					."<input class='text' type='text' name='profile_username_".$profileID."' id='profile_username_".$profileID."' value='' maxlength='55' title='Profile Name' />"
					."<input class='button' type='button' name='profileusernamesubmitbutt_".$profileID."' id='profileusernamesubmitbutt_".$profileID."' value='save' />"
					."<input class='button' type='button' name='profileusernamecancelbutt_".$profileID."' id='profileusernamecancelbutt_".$profileID."' value='cancel' />"
					."<span id='profile_username_".$profileID."failed' class='hidden'>a</span>"
				."</span>"
				."<span id='profileusernameloader_".$profileID."' class='hidden'></span>"
			."</li>";
		echo "<li><span class='elements'>E-Mail Address:</span>"
				."<span class='editprofileelements' id='editprofileemail_".$profileID."' title='Profile E-Mail Address'>".$profileArr['email']."</span>"
				."<span class='profilefrms' id='profileemailfrm_".$profileID."'>"
				."<input class='text' type='text' name='profile_email_".$profileID."' id='profile_email_".$profileID."' value='' maxlength='55' title='Profile E-Mail Address' />"
				."<input class='button' type='button' name='profileemailsubmitbutt_".$profileID."' id='profileemailsubmitbutt_".$profileID."' value='save' />"
				."<input class='button' type='button' name='profileemailcancelbutt_".$profileID."' id='profileemailcancelbutt_".$profileID."' value='cancel' />"
				."<span id='profile_email_".$profileID."failed' class='hidden'></span>"
				."</span>"
				."<span id='profileemailloader_".$profileID."' class='hidden'></span>"
			."</li>";
		echo "<li><span class='elements'>Blog Address:</span>"
				."<span class='editprofileelements' id='editprofileblog_".$profileID."' title='Profile Blog Address'>".$profileArr['blog']."</span>"
				."<span class='profilefrms' id='profileblogfrm_".$profileID."'>"
				."<input class='text' type='text' name='profile_blog_".$profileID."' id='profile_blog_".$profileID."' value='' maxlength='55' title='Profile Blog Address' />"
				."<input class='button' type='button' name='profileblogsubmitbutt_".$profileID."' id='profileblogsubmitbutt_".$profileID."' value='save' />"
				."<input class='button' type='button' name='profileblogcancelbutt_".$profileID."' id='profileblogcancelbutt_".$profileID."' value='cancel' />"
				."<span id='profile_blog_".$profileID."failed' class='hidden'></span>"
				."</span>"
				."<span id='profileblogloader_".$profileID."' class='hidden'></span>"
			."</li>";
	echo "</ul>";
    echo "</li>";
	}
	else{}
}//loadProfilesAdm()

function loadSettingsAdm($profileID)
{
	require_once('jddbase.class.php');

	$dbobj = new JDDBase();
	$settingsArr = array();
	$commentsCheckbox = '';
	$homepageCheckbox = '';
	$imagesUpdatesCheckbox = '';
	$linksCheckbox = '';
	
	$query = "SELECT profileid, postscommentsstatus, sitehomepage, postsimagesupdates, linksstatus "
			. " FROM settings WHERE profileid='".$profileID."'";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadSettingsAdm','false');
	$num = @mysql_num_rows($result);

	if(isset($num)&&$num!=0)
	{
		$settingsArr['profileid'] = @mysql_result($result,0,'profileid');
		$settingsArr['postscommentsstatus'] = @mysql_result($result,0,'postscommentsstatus');
		$settingsArr['sitehomepage'] = @mysql_result($result,0,'sitehomepage');
		$settingsArr['postsimagesupdates'] = @mysql_result($result,0,'postsimagesupdates');
		$settingsArr['linksstatus'] = @mysql_result($result,0,'linksstatus');
	}
	
	if(isset($num)&&$num!=0)
	{	
		echo "<li class='comments' id='togglecomments'>";
		if($settingsArr['postscommentsstatus']=='active'){$commentsCheckbox = 'checked';}
		else if($settingsArr['postscommentsstatus']=='inactive'){$commentsCheckbox = 'unchecked';}
		echo "<img title='".$commentsCheckbox."' id='togglecommentscheckbox' src='./jdlayout/images/".$commentsCheckbox.".gif' />Comments are active for news posts.";
		echo "</li>";
		
		echo "<li class='homepage' id='togglehomepage'>";
		if($settingsArr['sitehomepage']=='welcomepage'){$homepageCheckbox = 'unchecked';}
		else if($settingsArr['sitehomepage']=='newssection'){$homepageCheckbox = 'checked';}
		echo "<img title='".$homepageCheckbox."' id='togglehomepagecheckbox' src='./jdlayout/images/".$homepageCheckbox.".gif' />Site home page is the news section.";
		echo "</li>";
		
		echo "<li class='imagesupdates' id='toggleimagesupdates'>";
		if($settingsArr['postsimagesupdates']=='visible'){$imagesUpdatesCheckbox = 'checked';}
		else if($settingsArr['postsimagesupdates']=='invisible'){$imagesUpdatesCheckbox = 'unchecked';}
		echo "<img title='".$imagesUpdatesCheckbox."' id='toggleimagesupdatescheckbox' src='./jdlayout/images/".$imagesUpdatesCheckbox.".gif' />Posts about images updates are visible.";
		echo "</li>";
		
		echo "<li class='links' id='togglelinks'>";
		if($settingsArr['linksstatus']=='visible'){$linksCheckbox = 'checked';}
		else if($settingsArr['linksstatus']=='invisible'){$linksCheckbox = 'unchecked';}
		echo "<img title='".$linksCheckbox."' id='togglelinkscheckbox' src='./jdlayout/images/".$linksCheckbox.".gif' />Links are visible.";
		echo "</li>";
	}
	else
	{
		echo "<li class='comments' id='togglecomments'>"
			."<img title='checked' id='togglecommentscheckbox' src='./jdlayout/images/checked.gif' />Comments are active for news posts."
			."</li>";
		echo "<li class='homepage' id='togglehomepage'>"
			."<img title='unchecked' id='togglehomepagecheckbox' src='./jdlayout/images/unchecked.gif' />Site home page is the news section."
			."</li>";
		echo "<li class='imagesupdates' id='toggleimagesupdates'>"
			."<img title='checked' id='toggleimagesupdatescheckbox' src='./jdlayout/images/checked.gif' />Posts about images updates are visible."
			."</li>";
		echo "<li class='links' id='togglelinks'>"
			."<img title='unchecked' id='togglelinkscheckbox' src='./jdlayout/images/unchecked.gif' />Links are visible."
			."</li>";
	}
	unset($dbobj);
}//loadSettingsAdm()

function loadSettings()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$profileID = 0;
	$settingsArr = array();
	
	$query="SELECT * FROM settings WHERE profileid='".$profileID."'; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadSettings','true');
	$num = @mysql_num_rows($result);
	if($num!=0){
		$settingsArr['postscommentsstatus'] = @mysql_result($result,0,'postscommentsstatus');
		$settingsArr['sitehomepage'] = @mysql_result($result,0,'sitehomepage');
		$settingsArr['postsimagesupdates'] = @mysql_result($result,0,'postsimagesupdates');
		$settingsArr['linksstatus'] = @mysql_result($result,0,'linksstatus');
	}
	unset($dbobj);
	return $settingsArr;
}//loadSettings()

function loadWelcomePageImages($profileID)
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$imageVars = array();
	
	$query = "SELECT images.id, images.name, images.albumid, images.filename, images.fileurl, images.albumtagid "
		."FROM images, albums, tags "
		."WHERE images.albumid = albums.id AND images.albumtagid = tags.id AND tags.description='welcomepageimagesalbum' "
		."ORDER BY images.submitiontimestamp DESC; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','loadWelcomePageImages','false');
	$num = @mysql_num_rows($result);
	
	if($num == 0){}
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempImageID = @mysql_result($result,$i,'id');
			$imageVars[$tempImageID]['name'] = @mysql_result($result,$i,'name');
			$imageVars[$tempImageID]['albumid'] = @mysql_result($result,$i,'albumid');
			$imageVars[$tempImageID]['filename'] = @mysql_result($result,$i,'filename');
			$imageVars[$tempImageID]['fileurl'] = @mysql_result($result,$i,'fileurl');
			$imageVars[$tempImageID]['categoryid'] = @mysql_result($result,$i,'albumtagid');
			$imageVars[$tempImageID]['imagefullpath'] = './jdimages/thumbnails/'.$imageVars[$tempImageID]['fileurl'];
			
			$albumID = $imageVars[$tempImageID]['albumid'];
			$categoryID = $imageVars[$tempImageID]['categoryid'];
		}//for
	}//else
	
	reset($imageVars);
	echo "<div id='welcomepageimagescontents' class='welcomepageimagescontents'>";
	echo "<span id='ncat_".$albumID."' class='hidden'>".$categoryID."</span>";
	echo "<span id='aid' class='hidden'>".$albumID."</span>";			
	
    reset($imageVars);
	while (list($imagekey, $imageval) = each ($imageVars))
	{
		if($imageVars[$imagekey]['name']!=''){$tempImageHTMLtitle = strtoupper($imageVars[$imagekey]['name']);}
		else{$tempImageHTMLtitle = $imageVars[$imagekey]['filename'];}
		
		echo "<ul class='imagesections' id='imagesection_".$imagekey."' rel='".$imagekey."'>"
			."<li class='imagethumbs'>"
			."<img id='imagethumb_".$imagekey."' src='".$imageVars[$imagekey]['imagefullpath']."' alt='".$tempImageHTMLtitle."' title='".$tempImageHTMLtitle."'/>"
			."</li>"
			."<li class='imagedeletes' id='imagedelete_".$imagekey."'>"
			."<span class='deleteimagemsg'>delete?</span>"
			."<div class='completedeleteimages'><span id='completedeleteimage_".$imagekey."'>yes</span></div>"
			."<div class='dontdeleteimages'><span id='dontdeleteimage_".$imagekey."'>no</span></div>"
			."</li>"
			."</ul>";
	}//while
	echo "</div>";

	unset($dbobj);
}//loadWelcomePageImages($profileID)

function displayWelcomePageElements($profileID)
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$imageVars = array();
	$welcomeImageHTML = '';
	
	$query = "SELECT images.id, images.name, images.albumid, images.filename, images.fileurl, images.albumtagid "
		."FROM images, albums, tags "
		."WHERE images.albumid = albums.id AND images.albumtagid = tags.id AND tags.description='welcomepageimagesalbum' "
		."ORDER BY images.submitiontimestamp DESC; ";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','displayWelcomePageElements','false');
	$num = @mysql_num_rows($result);

	if($num == 0){$welcomeImageHTML = 'empty';}
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$imageVars[$i]['id'] = @mysql_result($result,$i,'id');
			$imageVars[$i]['name'] = @mysql_result($result,$i,'name');
			$imageVars[$i]['albumid'] = @mysql_result($result,$i,'albumid');
			$imageVars[$i]['filename'] = @mysql_result($result,$i,'filename');
			$imageVars[$i]['fileurl'] = @mysql_result($result,$i,'fileurl');
			$imageVars[$i]['categoryid'] = @mysql_result($result,$i,'albumtagid');
			$imageVars[$i]['imagefullpath'] = './jdimages/fullresolution/'.$imageVars[$i]['fileurl'];
			
			if($imageVars[$i]['name']!=''){$imageVars[$i]['tempImageHTMLtitle'] = strtoupper($imageVars[$i]['name']);}
			else{$imageVars[$i]['tempImageHTMLtitle'] = $imageVars[$i]['filename'];}
		}//for
		$randomInteger = rand(0,($num-1));
		$welcomeImageHTML = "<img src='".$imageVars[$randomInteger]['imagefullpath']."' id='welcomeimage' title='".$imageVars[$i]['tempImageHTMLtitle']."' />";
	}//else
	
	unset($dbobj);
	return $welcomeImageHTML;
}//displayWelcomePageElements($profileID)

function displayUsersActionLogEntries($entryType)
{
	if($entryType=='comment'){displayUsersActionLogEntriesComments('ualdisplay'); return "";}
	elseif($entryType=='routine'){displayUsersActionLogEntriesRoutine(); return "";}
	elseif($entryType=='error'){displayUsersActionLogEntriesError(); return "";}
}//displayUsersActionLogEntries($entryType)

function displayUsersActionLogEntriesRoutine()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$entryVars = array();
	
	$dbUalRoutineLimit=$_SESSION['ualroutinelimit'];
	if($dbUalRoutineLimit==''||!isset($dbUalRoutineLimit)||$dbUalRoutineLimit==0){$dbUalRoutineLimit=0;}

	$query00 = "SELECT * FROM usersactionlog WHERE entrytype='routine' ORDER BY actiondatetime DESC; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','displayUsersActionLogEntriesRoutine','false');
	$num00 = @mysql_num_rows($result00);
	
	$query = "SELECT * FROM usersactionlog WHERE entrytype='routine'"."ORDER BY actiondatetime DESC LIMIT ".$dbUalRoutineLimit.",30;";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','displayUsersActionLogEntriesRoutine','false');
	$num = @mysql_num_rows($result);
	if($num == 0)
	{
		echo "<div id='noresultsmessage'>"
			."<span id='howcome'>How come???</span>".""
			."<span id='reasonhowcome'>No actions in the system... yet.</span>"
		."</div>";
	}
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempID = @mysql_result($result,$i,'id');
			$entryVars[$tempID]['userid'] = @mysql_result($result,$i,'userid');
			$entryVars[$tempID]['function'] = @mysql_result($result,$i,'function');
			$entryVars[$tempID]['message'] = @mysql_result($result,$i,'message');
			$entryVars[$tempID]['entrytype'] = @mysql_result($result,$i,'entrytype');
			$entryVars[$tempID]['actiondatetime'] = @mysql_result($result,$i,'actiondatetime');
			$entryVars[$tempID]['userip'] = @mysql_result($result,$i,'userip');
			$entryVars[$tempID]['userprivileges'] = @mysql_result($result,$i,'userprivileges');
		}//for

		$generalCount = $num00;		
		$entryCounter = count($entryVars)-1;
		reset($entryVars);
		
		echo "<div id='entriesoptions'>";
			echo "<a id='deleteallroutineentries' href='#'>clear all entries</a>";
			echo "<div id='deleteallentriesline'></div>";
        echo "</div>";
		echo "<div class='entriesdeletes' id='entriesdelete'>";
			echo "<span class='deleteentriesmsg'>Really delete all routine actions log entries?</span>";
			echo "<div class='completedeleteentries'><span id='completedeleteentries'>yes</span></div>";
			echo "<div class='dontdeleteentries'><span id='dontdeleteentries'>no</span></div>";
		echo "</div>";
		
		echo "<ul class='usersactionlogentries' id='usersactionlogentries'>";
			echo "<li id='firstrow'>";
				echo "<span class='timestamp'>Timestamp</span>";
				if(DISPLAY_USERIP_IN_ACTIONLOG=='on'){echo "<span class='userip'>User IP</span>";}
				echo "<span class='action'>Action</span>";
				echo "<span class='biglines'></span>";
			echo "</li>";
		while (list($key, $val) = each ($entryVars))
		{
			if (($entryCounter%2) == 0){ $liClass = "odd"; } 
			else{ $liClass = "even";}
			
			
				echo "<li id='usersactionlogentry_".$key."' class='".$liClass."'>";
					echo "<span class='actiondatetime' title='Action Timestamp'>".convertTimeStamp($entryVars[$key]['actiondatetime'],'short')."</span>";
					if(DISPLAY_USERIP_IN_ACTIONLOG=='on'){echo "<span class='userip' title='User IP'>".$entryVars[$key]['userip']."</span>";}
					echo "<span class='message' title='Action'>".$entryVars[$key]['message']."</span>";
				echo "</li>";
			$entryCounter--;
		}//while
		echo "</ul>";

		if($generalCount!=0)
		{
			$showRoutineNavi = TRUE;
			if((($generalCount-($dbUalRoutineLimit))<=30)){$showRoutineNaviPrevious = FALSE;}
			else{$showRoutineNaviPrevious = TRUE;}
			if($generalCount<30){$showRoutineNaviPrevious = FALSE;}	
			
			if($dbUalRoutineLimit==0){$showRoutineNaviNext = TRUE;}
			else{$showRoutineNaviNext = FALSE;}
			
			if($showRoutineNaviPrevious==TRUE||$showRoutineNaviNext==FALSE){$showRoutineNavi = TRUE;}else{$showRoutineNavi = FALSE;}
		}
		else{ $showRoutineNavi = FALSE; $showRoutineNaviNext = FALSE; $showRoutineNaviPrevious = FALSE;}
		
		if($showRoutineNavi)
		{
			echo "<ul class='edituallinks' id='edituallinks'>";
				echo "<li id='prev'>";
					if($showRoutineNaviPrevious){echo "<a href='./jdincfunctions/functionsinc.php?type=39' title='Previous 30 Entries'>&lt;previous 30</a>";}
					echo "</li>";
					echo "<li id='next'>";
					if(!$showRoutineNaviNext){echo "<a href='./jdincfunctions/functionsinc.php?type=40' title='Next 30 Entries'>next 30&gt;</a>";}
					echo "</li>";
			echo "</ul>";
			echo "<span class='clearboth'></span>";
		}//if
	}//else
	unset($dbobj);
}//displayUsersActionLogEntriesRoutine()
function displayUsersActionLogEntriesError()
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$entryVars = array();
	
	$dbUalErrorLimit=$_SESSION['ualerrorlimit'];
	if($dbUalErrorLimit==''||!isset($dbUalErrorLimit)||$dbUalErrorLimit==0){$dbUalErrorLimit=0;}
	
	$query00 = "SELECT * FROM usersactionlog WHERE entrytype='error' ORDER BY actiondatetime DESC; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','displayUsersActionLogEntriesRoutine','false');
	$num00 = @mysql_num_rows($result00);
	
	$query = "SELECT * FROM usersactionlog WHERE entrytype='error'"."ORDER BY actiondatetime DESC LIMIT ".$dbUalErrorLimit.",20;";
	$result = @mysql_query($query) or dbErrorHandler(802,mysql_error(),$query,'php','','','displayUsersActionLogEntriesError','false');
	$num = @mysql_num_rows($result);

	if($num == 0)
	{
		echo "<div id='noresultsmessage'>"
			."<span id='luckyyou'>Lucky You!!!</span>".""
			."<span id='reasonluckyyou'>No errors occured in the system... yet.</span>"
		."</div>";
	}
	else
	{
		for($i=0; $i<$num; $i++)
		{
			$tempID = @mysql_result($result,$i,'id');
			$entryVars[$tempID]['userid'] = @mysql_result($result,$i,'userid');
			$entryVars[$tempID]['function'] = @mysql_result($result,$i,'function');
			$entryVars[$tempID]['message'] = @mysql_result($result,$i,'message');
			$entryVars[$tempID]['entrytype'] = @mysql_result($result,$i,'entrytype');
			$entryVars[$tempID]['actiondatetime'] = @mysql_result($result,$i,'actiondatetime');
			$entryVars[$tempID]['userip'] = @mysql_result($result,$i,'userip');
			$entryVars[$tempID]['userprivileges'] = @mysql_result($result,$i,'userprivileges');
		}//for

		$generalCount = $num00;		
		$entryCounter = count($entryVars)-1;
		reset($entryVars);

		echo "<div id='entriesoptions'>";
			echo "<a id='deleteallerrorentries' href='#'>clear all entries</a>";
			echo "<div id='deleteallentriesline'></div>";
        echo "</div>";
		echo "<div class='entriesdeletes' id='entriesdelete'>";
			echo "<span class='deleteentriesmsg'>Really delete all error log entries?</span>";
			echo "<div class='completedeleteentries'><span id='completedeleteentries'>yes</span></div>";
			echo "<div class='dontdeleteentries'><span id='dontdeleteentries'>no</span></div>";
		echo "</div>";
		
		echo "<ul class='usersactionlogentries' id='usersactionlogentries'>";
			echo "<li id='firstrow'>";
				echo "<span class='timestamp'>Timestamp</span>";
				if(DISPLAY_USERIP_IN_ACTIONLOG=='on'){echo "<span class='userip'>User IP</span>";}
				echo "<span class='privileges'>Privileges</span>";
				echo "<span class='error'>Error</span>";
				echo "<span class='biglines'></span>";
			echo "</li>";
		while (list($key, $val) = each ($entryVars))
		{
			if (($entryCounter%2) == 0){ $liClass = "odd"; } 
			else{ $liClass = "even";}
			
			echo "<li id='usersactionlogentry_".$key."' class='".$liClass."'>";
				echo "<span class='actiondatetime' title='Action Timestamp'>".convertTimeStamp($entryVars[$key]['actiondatetime'],'short')."</span>";
				if(DISPLAY_USERIP_IN_ACTIONLOG=='on'){echo "<span class='userip' title='User IP'>".$entryVars[$key]['userip']."</span>";}
				echo "<span class='userprivileges' title='User Privileges'>".$entryVars[$key]['userprivileges']."</span>";
				echo "<span class='message' title='Action'>".$entryVars[$key]['message']."</span>";
			echo "</li>";
			$entryCounter--;
		}//while
		echo "</ul>";
		
		if($generalCount!=0)
		{
			$showErrorNavi = TRUE;
			if((($generalCount-($dbUalErrorLimit))<=20)){$showErrorNaviPrevious = FALSE;}
			else{$showErrorNaviPrevious = TRUE;}
			//$showErrorNaviPrevious = TRUE;
			if($generalCount<20){$showErrorNaviPrevious = FALSE;}	
				
			if($dbUalErrorLimit==0){$showErrorNaviNext = TRUE;}
			else{$showErrorNaviNext = FALSE;}
				
			if($showErrorNaviPrevious==TRUE||$showErrorNaviNext==FALSE){$showErrorNavi = TRUE;}else{$showErrorNavi = FALSE;}
		}
		else{ $showErrorNavi = FALSE; $showErrorNaviNext = FALSE; $showErrorNaviPrevious = FALSE;}
	
		if($showErrorNavi)
		{
			echo "<ul class='edituallinks' id='edituallinks'>";
				echo "<li id='prev'>";
					if($showErrorNaviPrevious){echo "<a href='./jdincfunctions/functionsinc.php?type=41' title='Previous 20 Entries'>&lt;previous 20</a>";}
					echo "</li>";
					echo "<li id='next'>";
					if(!$showErrorNaviNext){echo "<a href='./jdincfunctions/functionsinc.php?type=42' title='Next 20 Entries'>next 20&gt;</a>";}
				echo "</li>";
			echo "</ul>";
			echo "<span class='clearboth'></span>";
		}//
	}//else
	unset($dbobj);
}//displayUsersActionLogEntriesError()
function displayUsersActionLogEntriesComments($type)
{
	require_once('jddbase.class.php');
	
	$dbobj = new JDDBase();
	$entryVars = array();
	
	$dbUalCommentsLimit=$_SESSION['ualcommentslimit'];
	if($dbUalCommentsLimit==''||!isset($dbUalCommentsLimit)||$dbUalCommentsLimit==0){$dbUalCommentsLimit=0;}
	
	$query00 = "SELECT posts.headline, comments.id, comments.postid, comments.name, comments.email, comments.website, comments.reply, comments.submitiontimestamp" 
			. " FROM comments, posts "
			. " WHERE comments.postid=posts.id AND comments.email<>'".PROFILE_EMAIL."' AND comments.website<>'".'http://www.jamesdoe.com'."'"
			. " ORDER BY submitiontimestamp DESC; ";
	$result00 = @mysql_query($query00) or dbErrorHandler(802,mysql_error(),$query00,'php','','','displayUsersActionLogEntriesRoutine','false');
	$num00 = @mysql_num_rows($result00);
	
	$query01 = "SELECT posts.headline, comments.id, comments.postid, comments.name, comments.email, comments.website, comments.reply, comments.submitiontimestamp" 
			. " FROM comments, posts "
			. " WHERE comments.postid=posts.id AND comments.email<>'".PROFILE_EMAIL."' AND comments.website<>'".'http://www.jamesdoe.com'."'"
			. " ORDER BY submitiontimestamp DESC LIMIT ".$dbUalCommentsLimit.",20;";
	$result01 = @mysql_query($query01) or dbErrorHandler(802,mysql_error(),$query01,'php','','','displayUsersActionLogEntriesComments','false');
	$num01 = @mysql_num_rows($result01);

	$query02 = "SELECT `actiondatetime`, `function` FROM usersactionlog ORDER BY actiondatetime DESC; ";
	$result02 = @mysql_query($query02) or dbErrorHandler(802,mysql_error(),$query02,'php','','','displayUsersActionLogEntriesComments','false');
	$num02 = @mysql_num_rows($result02);

	if($num02 == 0){$lastAdminLogin = date("Y-m-d") . " " . date("H:i:s");}//impossible to happen
	else
	{
		for($i=0; $i<$num02; $i++)
		{
			$tempFunction = @mysql_result($result02,$i,'function');
			if($tempFunction=='adminLogin'){$lastAdminLogin = @mysql_result($result02,$i,'actiondatetime');}
			if($tempFunction=='adminLogout'){$lastAdminLogout = @mysql_result($result02,$i,'actiondatetime');}

			if(isset($lastAdminLogin)&&isset($lastAdminLogout)){break;}
		}
	}
	
	if($type=='adminnavi')
	{
		$counter=0;
		if($num01 == 0){return $counter;}
		else
		{	
			for($i=0; $i<$num01; $i++)
			{
				$tempID = @mysql_result($result01,$i,'id');
				$entryVars[$tempID]['postid'] = @mysql_result($result01,$i,'postid');
				$entryVars[$tempID]['submitiontimestamp'] = @mysql_result($result01,$i,'submitiontimestamp');
				
				if( (strtotime($entryVars[$tempID]['submitiontimestamp']) > strtotime($lastAdminLogout)) )
				{
					if(isset($_SESSION['mostRecentDisplayUALEComments'])&&(strtotime($_SESSION['mostRecentDisplayUALEComments'])>strtotime($entryVars[$tempID]['submitiontimestamp']))){$entryVars[$tempID]['new'] = 'no';}
					else{$entryVars[$tempID]['new'] = 'yes'; $counter++;}
				}
				else{$entryVars[$tempID]['new'] = 'no';}
			}
			unset($dbobj);
			return $counter;
		}
	}//if
	elseif($type=='ualdisplay')
	{
		$generalCommentsCount = $num00;
		if($num01 == 0)
		{
			echo "<div id='noresultsmessage'>"
				."<span id='sorry'>We're sorry </span>"."<span id='notreally'>(but not really)</span> "
				."<span id='reasonnotreallysorry'>Nobody cared enough to comment on your posts :-( .</span>"
			."</div>";
		}
		else
		{
			$newCommentsCounter=0;
			for($i=0; $i<$num01; $i++)
			{
				$tempID = @mysql_result($result01,$i,'id');
				$entryVars[$tempID]['postid'] = @mysql_result($result01,$i,'postid');
				$entryVars[$tempID]['postheadline'] = @mysql_result($result01,$i,'headline');
				$entryVars[$tempID]['name'] = @mysql_result($result01,$i,'name');
				$entryVars[$tempID]['email'] = @mysql_result($result01,$i,'email');
				$entryVars[$tempID]['website'] = @mysql_result($result01,$i,'website');
				$entryVars[$tempID]['reply'] = @mysql_result($result01,$i,'reply');
				$entryVars[$tempID]['submitiontimestamp'] = @mysql_result($result01,$i,'submitiontimestamp');
				
				if( (strtotime($entryVars[$tempID]['submitiontimestamp']) > strtotime($lastAdminLogout)) )
				{
					if(isset($_SESSION['mostRecentDisplayUALEComments'])&&(strtotime($_SESSION['mostRecentDisplayUALEComments'])>strtotime($entryVars[$tempID]['submitiontimestamp']))){$entryVars[$tempID]['new'] = 'no';}
					else{$entryVars[$tempID]['new'] = 'yes'; $newCommentsCounter++;}
				}
				else{$entryVars[$tempID]['new'] = 'no';}
			}
			if($newCommentsCounter!=0){$_SESSION['mostRecentDisplayUALEComments'] = date("Y-m-d") . " " . date("H:i:s"); unset($_SESSION['ADMINSIDEBAR']);}
			$entryCounter = count($entryVars)-1;
			reset($entryVars);

			echo "<ul class='usersactionlogentries'>";
				echo "<li id='firstrow'>";
					echo "<span class='when'>When?</span>";
					echo "<span class='where'>Where?</span>";
					echo "<span class='who'>Who?</span>";
					echo "<span class='what'>What?</span>";
					echo "<span class='biglines'></span>";
				echo "</li>";
			while (list($key, $val) = each ($entryVars))
			{
			
				if (($entryCounter%2) == 0){ $liClass = "odd"; } else{ $liClass = "even";}
				echo "<li id='usersactionlogentry_".$key."' class='".$liClass."'>";
					echo "<span class='submitiontimestamp' title='Submition Timestamp'>";
						if($entryVars[$key]['new']=='yes'){echo "<div class='red'>".convertTimeStamp($entryVars[$key]['submitiontimestamp'],'short')."</div>";}
						else{echo convertTimeStamp($entryVars[$key]['submitiontimestamp'],'short');}
					echo "</span>";
					echo "<span class='forpost'>"."<a href='./mmeditcomments.php?postid=".$entryVars[$key]['postid']."' title='Post: \"".strtoupper($entryVars[$key]['postheadline'])."\"'>"."go to post"."</a>"."</span>";
					echo "<span class='fromuser'>"."<a href='".$entryVars[$key]['website']."'>".$entryVars[$key]['name']."</a>"." said: </span>";
					echo "<span class='reply' title='What ".$entryVars[$key]['name']." said.'>"."\"".$entryVars[$key]['reply']."\""."</span>";
				echo "</li>";
				$entryCounter--;
			}//while
			echo "</ul>";
			
		//PRINT THE EDIT POSTS NAVIGATION
		if($generalCommentsCount!=0)
		{
			$showCommentsNavi = TRUE;
			if((($generalCommentsCount-($dbUalCommentsLimit))<=20)){$showCommentsNaviPrevious = FALSE;}
			else{$showCommentsNaviPrevious = TRUE;}
			if($generalCommentsCount<20){$showCommentsNaviPrevious = FALSE;}	
			
			if($dbUalCommentsLimit==0){$showCommentsNaviNext = TRUE;}
			else{$showCommentsNaviNext = FALSE;}
			
			if($showCommentsNaviPrevious==TRUE||$showCommentsNaviNext==FALSE){$showCommentsNavi = TRUE;}else{$showCommentsNavi = FALSE;}
		}
		else{ $showCommentsNavi = FALSE; $showCommentsNaviNext = FALSE; $showCommentsNaviPrevious = FALSE;}
	
		if($showCommentsNavi)
		{
			echo "<ul class='edituallinks' id='edituallinks'>";
				echo "<li id='prev'>";
					if($showCommentsNaviPrevious){echo "<a href='./jdincfunctions/functionsinc.php?type=37' title='Previous 20 Comments'>&lt;previous 20</a>";}
					echo "</li>";
					echo "<li id='next'>";
					if(!$showCommentsNaviNext){echo "<a href='./jdincfunctions/functionsinc.php?type=38' title='Next 20 Comments'>next 20&gt;</a>";}
					echo "</li>";
			echo "</ul>";
			echo "<span class='clearboth'></span>";
		}//
			
		}//else
	}//else 
	unset($dbobj);
}//displayUsersActionLogEntriesComments()
?>