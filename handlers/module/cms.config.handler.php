<?php
/*
Flaschcoms integration configuration file.
*/
error_reporting(0);

//define("ENCRYPTION_STRING",     "");

//settings
define("DB_HOST",     			"localhost");
define("DB_USER",     			"flashcoms");
define("DB_PASSWORD", 			"8sr66GR7dUba5BuZ");
define("DB_NAME",     			"phpfox");

define("DB_CHARSET",     		"utf8");

define('DOCUMENT_ROOT',			 $_SERVER['DOCUMENT_ROOT'].'/');
define('SITE',					'http://'.$_SERVER['HTTP_HOST'].'/');

define('FLASHCOMS_ROOT',        DOCUMENT_ROOT."chat71/");
define('FLASHCOMS_HTTP_ROOT', 	SITE.'chat71/');
define('FLASHCOMS_JS', 			FLASHCOMS_HTTP_ROOT.'js/flashcoms.js');

define('HANDLER_PATH',			FLASHCOMS_HTTP_ROOT.'handlers/common.handler.php');



define("LOG_DIR",   			SITE.'');
//settings end

//Authorization Constants ($queryType == 'AUTHORIZATION')
define("SQL_AUTHORIZATION", "
							SELECT 
								user.user_id as id,
								user.user_name as login,
								user.password as password,
                                user.password_salt as salt
							FROM 
								phpfox_user AS user
							WHERE 
                                [condition]
                                ");
                            
define("SQL_AUTOLOGIN_CASE", "user.user_id = '[uid]'");
define("SQL_LOGIN_CASE", "user.user_name = '[login]' AND user.password = '[password]'");
define("SQL_GUEST_CASE", "user.user_name = '[login]'");


//Define table where "core.secret" is stored (for SocialEngine only)
define("CMS_SOCIALENGINE_CORE_SECRET_TABLE", "engine4_core_settings"); 

//Password encryption algorithm
define("CMS", "PHPFox"); 
//Allowed values: VBulletin    - field 'salt' is needed,
//                Joomla       - field 'password' is needed,
//                SKADate      - no requirements,
//                WordPress    - field 'password' is needed,
//                PGDatingPro  - no requirements,  
//                SocialEngine - field 'salt' is needed,
//				  XenForo      - field 'password' is needed (xf_user_authenticate.data as password),
//                PHPBB        - field 'password' is needed, 
//                Drupal7      - field 'password' is needed, 
//                Drupal6      - no reqirements,
//                ABKSoft      - field 'password' is needed, 
//                Interactive Arts Ltd - no requirements, 
//                Invision Power Services - field 'salt' is needed,
//                ChatZone     - no reqirements,
//                Concrete5    - CONCRETE_PASSWORD_SALT should be set  
//                PHPFox       - field 'salt' is needed, 
//                osDate       - no reqirements,    
//                or leave empty.
define('SALT', '27704e28468e2904109d879ab2e3cc19');
define('CONCRETE_PASSWORD_SALT', 'bMg33SRZrRIjZ8KAClxh5WtghmOrtgiM7HfAqz76Qs4kW6K5XJOBGFBESYyeYq18'); // only for Concrete5 (can be found in config/site.php) 
define('DRUPAL_HASH_LENGTH', 55); //only for Drupal7 (can be found in drupal_root/includes/password.inc)

define("NO_PASSWORD_ENCRYPTION", FALSE); //TRUE - no encryption


//Profile constants ( $queryType == 'PROFILE' )
/** SQL query should contain field names:
 * 	required	"id"               //numeric value
 * 	required	"login"            //string
 *          	"password"         //string, required for PPV
 * 	optional	"gender"               //values should be checked
 * 	optional	"country"          //string
 * 	optional	"state"            //string
 *  optional	"city"             //string
 * 	optional	"birthday"             //format yyyy-mm-dd
 *  optional    "age"              //int
 * 	optional	"photo"            //filename
 * 	optional	"thumbnail"        //small photo
 * 	optional	"description"      //string
 * 	optional	"lvl"                  //"level" values should be checked
 * 	optional    "usergroupid"          //values should be checked 
 * 
 *  optional    "usergroupname"          //values should be checked, has been added for customer on WordPress
 *              "credits"          //string, required for PPV 
 *              "priceperminute"   //string, optional for PPV 
 * 
 *  optional    "val_id"           //If sql query return more than one row. Id of every value.  
 *  optional    "val"              //If sql query return more than one row 
 * 	
 *  			
 */
//YEAR(FROM_DAYS(TO_DAYS('" . date('Y-m-d H:i:s') . "')-TO_DAYS(birth)))
                
define("SQL_PROFILE", "
            					SELECT 
									user.user_id as id,
								    user.user_name as login,
									user.gender as gender, 
                                    pc.name as country,
                                    puf.city_location as city,
									CONCAT(SUBSTRING(user.birthday, 1, 2),'-',SUBSTRING(user.birthday, 3, 2),'-',SUBSTRING(user.birthday, 5, 4))  as birthday, 
									user.user_image as photo, 
									puc.cf_about_me as description,
                                    pug.title as lvl,
                                    pug.title as usergroupid
								FROM 
									phpfox_user AS user
                                LEFT JOIN 
                                    phpfox_country AS pc ON pc.country_iso = user.country_iso
                                LEFT JOIN
                                    phpfox_user_field AS puf ON puf.user_id = user.user_id
                                LEFT JOIN 
                                    phpfox_user_custom AS puc ON puc.user_id = user.user_id
                                LEFT JOIN
                                    phpfox_user_group AS pug ON pug.user_group_id = user.user_group_id 
								WHERE 
									user.user_id = '[uid]'");
                                
define("SQL_PROFILE_ROWS", NULL); //NULL if previous query return one row, "id:name, 3:birthday, 10:city, 11:country, 23:description, ..."                              
define("DEFAULT_EMPTY_VALUE", "Not specified");

//define("GENDERS", "1:1, 2:0, 4:Couple, 8:Group");//example "1:Female, 2:Male, 4:Couple, 8:Group" (important: "male" and "female" for messenger7)
define("GENDERS", "1:0, 2:1");//example "1:Female, 2:Male, 4:Couple, 8:Group" (important: "male" and "female" for messenger7)
define("SEPARATE_AGES_FOR_GENDERS", FALSE);//TRUE if there are separate ages filled in couple (age field required). Example output: 34/37

#define("ADMINSTRATORS_ARRAY", "Administrator"); //List of administrators separated by ", " (linked to "usergroupid")
define("ADMINSTRATORS_ARRAY", ""); //List of administrators separated by ", " (linked to "usergroupid")
define("MODERATORS_ARRAY", "Administrator"); //List of moderators separated by ", " (linked to "usergroupid")
define("OPEARTORS_ARRAY", ""); //List of moderators separated by ", " (linked to "usergroupid") for PPV
define("VISITORS_ARRAY", ""); //List of moderators separated by ", " (linked to "usergroupid") for PPV
define("APPLICATION_GUEST_LEVEL", "guest");
define("APPLICATION_REGULAR_LEVEL", "regular");  

//Settings for autologin
define("WORDPRESS_COOKIE_NAME", "wordpress_logged_in_e2ca8aaed423679957d7de46000b9a14");//

//Photo
define("PHOTO_IS_FILE",         TRUE);//TRUE - file, FALSE - URL
define('USE_PHOTOCONVERTER',	FALSE);//photoconverter.php?pic=...&r=4x3
define('PHOTO_PATH',			SITE.'');
define('ROOT_PHOTO_PATH',		DOCUMENT_ROOT.'');
define('PHOTO_COMPLEX',		    FALSE); //TRUE if photo paths use [id] and/or [login]

define('NO_PHOTO_MALE',			FLASHCOMS_HTTP_ROOT.'photos/no_photo_big.png');
define('NO_PHOTO_FEMALE',		FLASHCOMS_HTTP_ROOT.'photos/no_photo_big.png');
define('NO_PHOTO', 				FLASHCOMS_HTTP_ROOT.'photos/no_photo_big.png');
define('NO_PHOTO_6', 			FLASHCOMS_HTTP_ROOT.'common/images/no_photo.swf');

//For PPV
define("PPV_DEFAULT_PRICE_PER_MINUTE", 2); //credits per minute of private chat

define("SQL_PROFILE_URL", SITE."[login]/");// placeholders: [id] and/or [login]

//SKAdate only (optional)
define("SQL_SELECT_ADMINS", "
							SELECT * 
							FROM skadate_admin 
							WHERE admin_username = '[login]'
                            ");



//If there are no such tables
//DB dump friendlist
/**
                               CREATE TABLE `chat7_friendlist` (
                                 `id_user` int(10) unsigned NOT NULL,
                                 `id_friend` int(10) unsigned NOT NULL
                               ); 
*/
//DB dump blocklist
/**
                               CREATE TABLE `chat7_blocklist` (
                                 `id_user` int(10) unsigned NOT NULL,
                                 `id_blocked` int(10) unsigned NOT NULL
                               ); 
*/
define("FB_SAME_TABLE", FALSE); //TRUE - Friends and Blocks in the same table                             
//Friends constants ( $queryType == 'FRIENDS' )
define("SQL_FRIENDS", "	
							SELECT 
								user_id as user,
								friend_user_id as partner
							FROM 
								phpfox_friend
							WHERE
								user_id = '[uid]' LIMIT 50
                                ");
//Add/Remove friends constants
define("SQL_SELECT_FRIENDS", "
							SELECT * 
							FROM 
								phpfox_friend
							WHERE 
								user_id = '[user]' AND friend_user_id = '[partner]'  
                            ");
                                            
define("SQL_INSERT_FRIENDS", "
                            INSERT INTO 
                                phpfox_friend_request 
                            SET 
                                user_id = '[partner]',
                                friend_user_id = '[user]',
                                message  = 'chat friend request',
                                time_stamp = ".mktime()."
                            ");//array('[user]','[partner]', '[accepted]', '[pending]', '[created]', '[created Y-m-d]')

//SQL_UPDATE_FRIENDS is used only if FB_SAME_TABLE is defined
define("SQL_UPDATE_FRIENDS", "
                            UPDATE 
                                preferito
                            SET 
                                BiancoNero = '1'
                            WHERE 
                                RIDUtente_Mittente = '[user]' AND RIDUtente_Destinatario = '[partner]'
                            ");
							
                     
define("SQL_DELETE_FRIENDS", "
                            DELETE
                            FROM
                                phpfox_friend 
                            WHERE 
                                user_id = '[user]' AND friend_user_id = '[partner]' 
                            ");
								                 
                                
//Block constants ( $queryType == 'BLOCKS' )
define("SQL_BLOCKS", "
            				
							SELECT 
								user_id as user,
								block_user_id as partner
							FROM 
								phpfox_user_blocked
							WHERE
								user_id = '[uid]' LIMIT 50
                                ");

//Add/Remove blocks constants
define("SQL_SELECT_BLOCKS", "
                            SELECT * 
                            FROM 
                                phpfox_user_blocked 
                            WHERE 
                                user_id = '[user]' AND block_user_id = '[partner]'
                            ");

define("SQL_INSERT_BLOCKS", "
                            INSERT INTO 
                                phpfox_user_blocked 
                            SET 
                                user_id = '[user]',
                                block_user_id = '[partner]',
                                time_stamp = '".mktime()."'
                            ");

//SQL_UPDATE_BLOCKS is used only if FB_SAME_TABLE is defined                            
define("SQL_UPDATE_BLOCKS", "
                            UPDATE 
                                preferito
                            SET 
                                BiancoNero = '0'
                            WHERE 
                                RIDUtente_Mittente = '[user]' AND RIDUtente_Destinatario = '[partner]'
                            ");

define("SQL_DELETE_BLOCKS", "
                            DELETE
                            FROM
                                phpfox_user_blocked
                            WHERE 
                                user_id = '[user]' AND block_user_id = '[partner]'
                            ");



                                
//Common functions
//define("SQL_GET_ID_BY_LOGIN", "
//            				SELECT 
//            					user.id as id,
//            					user.username as login
//            				FROM 
//            					".DB_PRX_SITE."user as user
//            				WHERE username = '[uid]'
//                            ");
//
//define("SQL_GET_LOGIN_BY_ID", "
//            				SELECT 
//            					user.id as id,
//            					user.username as login
//            				FROM 
//            					".DB_PRX_SITE."user as user
//            				WHERE id = '[uid]'
//                            ");



                            

//SocialEngine ONLY

define("SOCIAL_ENGINE_FRIEND_REQUEST_ADD", "
                            INSERT INTO 
                                engine4_activity_notifications 
                            SET 
                                subject_id = '[user]',
                                user_id = '[partner]',
                                subject_type = 'user',
                                object_type = 'user',
                                object_id = '[partner]',
                                type = 'friend_request',
                                date = '[created]'
                            ");

define("PHPFOX_FRIEND_REQUEST_ADD", "
                            INSERT INTO 
                                phpfox_notification 
                            SET 
                                type_id = 'friend_request',
                                item_id = (SELECT
                                                request_id
                                            FROM
                                                phpfox2_friend_request
                                            WHERE
                                                user_id = '[partner]' AND
                                                friend_user_id = '[user]'),
                                user_id  = '[partner]',
                                owner_user_id = '[user]',
                                time_stamp = ".mktime()."
                            ");                            
                                                        
//------------------
      






//Comment the following if it's not needed                            
define("SQL_UPDATE_FRIENDS_COUNT", "
                            UPDATE 
                                `engine4_activity_notifications`
                            SET 
                                `read` = '1',
                                `mitigated` = '1'
                            WHERE 
                                `subject_id` = '[user]'
                                AND `user_id` = '[partner]'
                                AND `type` = 'friend_request'
                            ");// '[count]', '[user]', '[partner]'




                            
                    

?>
