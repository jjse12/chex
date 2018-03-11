CREATE TABLE IF NOT EXISTS 'usercreator'.'users' (
  'user_number_id' int(10) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user number id of each user, unique identification number',
  'user_id' varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s ID which matches the format AAAXY where AAA are incresing letters from AAA to ZZZ and XY are the initials of user''s first and last name',
  'user_fname' varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s first name',
  'user_lname' varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s last name',
  'user_email' varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  'user_mobile' int(8) UNSIGNED NOT NULL COMMENT 'user''s mobile phone number will go here 8 digits',
  'user_phone' int(8) UNSIGNED NOT NULL COMMENT 'user''s home phone number will go here 8 digits',
  'user_address' varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s address with a size limit of 200 characters',
  'user_gender' varchar(1) COLLATE utf8_unicode_ci NOT NULL COMMENT 'M for Male, F for Female',
  'user_bday' date() NOT NULL COMMENT 'user''s birthday is stored in this variable',
  PRIMARY KEY ('user_number_id'),
  UNIQUE KEY 'user_id' ('user_id'),
  UNIQUE KEY 'user_email' ('user_email')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';